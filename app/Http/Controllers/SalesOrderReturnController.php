<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\booking;
use App\bookeditem;
use App\SalesOrderReturn;
use App\Producto;
use DB;

class SalesOrderReturnController extends Controller
{
    function __construct()
    {
        $this->middleware(['role:admin','permission:sales-order-return']);
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
        notify()->error('Oops!!!, something went wrong, please try again.');
        return redirect()->back();
    }

    public function salesOrderReturnSave(Request $request)
    {
        DB::beginTransaction();
        try {
        	$return_token = Str::random(15);
        	foreach ($request->return_qty as $key => $returnQty) {
	    		if(!empty($returnQty))
	  			{
		        	$salesOrderReturn = new SalesOrderReturn;
			        $salesOrderReturn->booking_id 		= $request->booking_id;
			        $salesOrderReturn->bookeditem_id	= $request->bookeditem_id[$key];
			        $salesOrderReturn->producto_id    	= $request->producto_id[$key];
			        $salesOrderReturn->return_token  	= $return_token;
			        $salesOrderReturn->return_qty  		= $returnQty;
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

	        DB::commit();
	        notify()->success('Success, Sale order quantity returned successfully.');
            return redirect()->route('sales-order-list'); 
        } catch (\Exception $exception) {
            DB::rollback();
            notify()->error('Error, Oops!!!, something went wrong, please try again.'. $exception->getMessage());
            return redirect()->back()->withInput(); 
        } catch (\Throwable $exception) {
            DB::rollback();
            notify()->error('Error, Oops!!!, something went wrong, please try again.'. $exception->getMessage());
            return redirect()->back()->withInput();
        }
    }
}
