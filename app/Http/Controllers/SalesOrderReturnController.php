<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Notifications\SaleOrderNotification;
use App\booking;
use App\bookeditem;
use App\SalesOrderReturn;
use App\Producto;
use DB;
use Notification;
use App\User;

class SalesOrderReturnController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:sales-order-return', ['except' => ['salesOrderReturnSave']]);
    }

    public function salesOrderReturnList()
    {
	    return view('sales.sales-order-return');
    }

    public function salesReturnProductDatatable(Request $request)
    {
        $query = SalesOrderReturn::select('*')->orderBy('id','DESC')->with('booking', 'producto')->get();
        return datatables($query)
	        ->editColumn('tranjectionid', function ($query)
		        {
		            return '<strong>'.$query->booking->tranjectionid.'</strong>';
		        })
	       	->editColumn('order_date', function ($query)
		        {
		            return $query->created_at->format('Y-m-d');
		        })
	       	->editColumn('customer', function ($query)
		        {
		            return $query->booking->firstname.' '.$query->booking->lastname;
		        })
	        ->editColumn('product_name', function ($query)
		        {
		            return $query->producto->nombre;
		        })
	        ->editColumn('returned_qty', function ($query)
		        {
		            return '<strong>'.$query->return_qty.'</strong>';
		        })
	        ->editColumn('returned_amount', function ($query)
		        {
		            return '<strong>$'.$query->return_amount.'</strong>';
		        })
	        ->editColumn('returned_date', function ($query)
		        {
		        	return $query->created_at->format('Y-m-d');
		        })
        ->escapeColumns(['action'])
        ->addIndexColumn()
        ->make(true);
    }

    public function salesOrderReturn($id)
    {
    	if(booking::where('deliveryStatus', 'Delivered')->find(base64_decode($id)))
        {
            $saleInfo = booking::find(base64_decode($id));
	        return view('sales.sales-order-return', compact('saleInfo'));
        }
        notify()->error('Oops!!!, algo salió mal, intente de nuevo.');
        return redirect()->back();
    }

    public function salesOrderReturnSave(Request $request)
    {
        DB::beginTransaction();
        try {
        	$return_token = Str::random(15);
        	$getTax = booking::select('tranjectionid','tax_percentage')->find($request->booking_id);
        	foreach ($request->return_qty as $key => $returnQty) {
	    		if(!empty($returnQty))
	  			{
	  				$calTax = (($returnQty * $request->itemPrice[$key]) * $getTax->tax_percentage)/100;
	  				$salesOrderReturn = new SalesOrderReturn;
			        $salesOrderReturn->booking_id 		= $request->booking_id;
			        $salesOrderReturn->bookeditem_id	= $request->bookeditem_id[$key];
			        $salesOrderReturn->producto_id    	= $request->producto_id[$key];
			        $salesOrderReturn->return_token  	= $return_token;
			        $salesOrderReturn->return_qty  		= $returnQty;
			        $salesOrderReturn->return_amount  	= (($returnQty * $request->itemPrice[$key]) + $calTax);
			        $salesOrderReturn->return_note  	= $request->return_note;
			        $salesOrderReturn->save();

			        //Stock In Start
		        	$getStock = Producto::select('id','stock')->find($request->producto_id[$key]);
		        	$getStock->stock = $getStock->stock + $returnQty;
		        	$getStock->save();
		        	//Stock In End

		        	//update record order item
		        	$updateOrderQty = bookeditem::select('id','return_qty')->find($request->bookeditem_id[$key]);
		        	$updateOrderQty->return_qty = $updateOrderQty->return_qty + $returnQty;
		        	$updateOrderQty->save();
			    }
			}
			$changeStatus = true;
			$checkSaleStatus = bookeditem::where('bookingId', $request->booking_id)->get();
			foreach ($checkSaleStatus as $key => $checkbothQty) {
				if($checkbothQty->itemqty!=$checkbothQty->return_qty)
				{
					$changeStatus = false;
					break;
				}
			}
			if($changeStatus)
			{
				$updateStatus = booking::find($request->booking_id);
				$updateStatus->deliveryStatus = 'Return';
				$updateStatus->save();
			}
			$totalReverseAmount = SalesOrderReturn::where('return_token', $return_token)->sum('return_amount');
			//Send Notification
            $details = [
                'body'      => 'Order Number #'.$getTax->tranjectionid. ' product has been returned by '.auth()->user()->name.'. Retutned order amount is $'.$totalReverseAmount.'. Return note is: '.$request->return_note,
                'actionText'=> 'Ver Pedido',
                'actionURL' => route('sales-return-by-token', [base64_encode($request->booking_id),$return_token]),
                'order_id'  => $request->booking_id
            ];
            Notification::send(User::first(), new SaleOrderNotification($details));
	        DB::commit();
	        notify()->success('Success, Sale order quantity returned successfully.');
            return redirect()->back();
        } catch (\Exception $exception) {
            DB::rollback();
            notify()->error('Error, Oops!!!, algo salió mal, intente de nuevo.'. $exception->getMessage());
            return redirect()->back()->withInput();
        } catch (\Throwable $exception) {
            DB::rollback();
            notify()->error('Error, Oops!!!, algo salió mal, intente de nuevo.'. $exception->getMessage());
            return redirect()->back()->withInput();
        }
    }
}
