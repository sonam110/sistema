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
use Braghetto\Hokoml\Hokoml;

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

		        	//Start ***Available Quantity update in ML
                    $response = $this->addStockMl($request->producto_id[$key], $returnQty);
                    //End ***Available Quantity update in ML
			    }
			}
			$changeStatus = true;
			$checkSaleStatus = bookeditem::where('bookingId', $request->booking_id)->get();
			foreach ($checkSaleStatus as $key => $checkbothQty) {
				if($checkbothQty->itemqty!= (int) $checkbothQty->return_qty)
				{
					$changeStatus = false;
					break;
				}
			}

			if($changeStatus)
			{
				$updateStatus = booking::select('id', 'deliveryStatus')->find($request->booking_id);
				$updateStatus->deliveryStatus = 'Return';
				$updateStatus->save();
			}
			$totalReverseAmount = SalesOrderReturn::where('return_token', $return_token)->sum('return_amount');
			//Send Notification
            $details = [
                'body'      => 'Orden Numero #'.$getTax->tranjectionid. ' producto devuelto por '.auth()->user()->name.'. Monto Devuelto $'.$totalReverseAmount.'. Nota de Devolución #: '.$request->return_note,
                'actionText'=> 'Ver Pedido',
                'actionURL' => route('sales-return-by-token', [base64_encode($request->booking_id),$return_token]),
                'order_id'  => $request->booking_id
            ];
            Notification::send(User::first(), new SaleOrderNotification($details));
	        DB::commit();
	        notify()->success('Hecha , Cantidad devuelta exitosamente en la Orden de venta.');
            return redirect()->route('sales-order-list');
        } catch (\Exception $exception) {
            DB::rollback();
            dd($exception->getMessage());
            notify()->error('Error, Oops!!!, algo salió mal, intente de nuevo.'. $exception->getMessage());
            return redirect()->back()->withInput();
        } catch (\Throwable $exception) {
            DB::rollback();
            dd($exception->getMessage());
            notify()->error('Error, Oops!!!, algo salió mal, intente de nuevo.'. $exception->getMessage());
            return redirect()->back()->withInput();
        }
    }

    private function addStockMl($productoId, $purchaseQty)
    {
        $is_stock_updated_in_ml = '0';
        $records = Producto::select('id','nombre','stock','precio','mla_id')
                ->where('id', $productoId)
                ->where('disponible', '1')
                ->where('mla_id', '!=', null)
                ->orderBy('mla_id')
                ->first();
        if($records && !empty($records->mla_id))
        {
            $mlas = new Hokoml(\Config::get('mercadolibre'), env('ML_ACCESS_TOKEN',''), env('ML_USER_ID',''));
            $response = $mlas->product()->find($records->mla_id);
            if($response['http_code']==200)
            {
                //if product found
                $variationsArr  = array();
                $variations     = $response['body']['variations'];
                foreach ($variations as $key => $variation) {
                    $variationsArr[] = [
                        'id'    => $variation['id'],
                        'available_quantity' => $variation['available_quantity'] + $purchaseQty
                    ];
                }

                if(is_array($variationsArr) && sizeof($variationsArr)>0)
                {
                    //if variation found then update variation available quantity
                    $response = $mlas->product()->update($records->mla_id, [
                        'variations' => $variationsArr
                    ]);
                }
                else
                {
                    //if variation not found then update main available quantity
                    $mainList     = $response['body'];
                    $response = $mlas->product()->update($records->mla_id, [
                        'available_quantity'  => $mainList['available_quantity'] + $purchaseQty
                    ]);
                }
                if($response['http_code']==200)
                {
                    $is_stock_updated_in_ml = '1';
                }
            }
        }
        return $is_stock_updated_in_ml;
    }
}
