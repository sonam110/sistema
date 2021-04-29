<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\PurchaseOrder;
use App\PurchaseOrderProduct;
use App\PurchaseOrderReceiving;
use App\Producto;
use DB;
use Braghetto\Hokoml\Hokoml;

class PurchaseOrderReceivingController extends Controller
{
    function __construct()
    {
        $this->middleware(['permission:purchase-order-receiving']);
    }

    public function purchaseOrderReceivedList()
    {
	    return view('purchases.purchase-order-receiving');
    }

    public function poReceivedProductDatatable(Request $request)
    {
        $query = PurchaseOrderReceiving::select('purchase_order_receivings.*')->orderBy('id','DESC')->with('purchaseOrder', 'purchaseOrder.supplier', 'producto')
        ->join('purchase_orders', function ($join) {
            $join->on('purchase_orders.id', '=', 'purchase_order_receivings.purchase_order_id');
        })
        ->get();
        return datatables($query)
	        ->editColumn('po_no', function ($query)
		        {
		            return @$query->purchaseOrder->po_no;
		        })
	       	->editColumn('po_date', function ($query)
		        {
		            return @$query->purchaseOrder->po_date;
		        })
	       	->editColumn('supplier', function ($query)
		        {
		            return @$query->purchaseOrder->supplier->name;
		        })
	        ->editColumn('product_name', function ($query)
		        {
		            return $query->producto->nombre;
		        })
	        ->editColumn('received_qty', function ($query)
		        {
		            return '<strong>'.$query->received_qty.'</strong>';
		        })
	        ->editColumn('received_date', function ($query)
		        {
		        	return $query->created_at->format('Y-m-d');
		        })
        ->escapeColumns(['action'])
        ->addIndexColumn()
        ->make(true);
    }

    public function purchaseOrderReceiving($id)
    {
    	if(PurchaseOrder::where('po_status','!=', 'Completed')->find(base64_decode($id)))
        {
            $poInfo = PurchaseOrder::find(base64_decode($id));
	        return view('purchases.purchase-order-receiving', compact('poInfo'));
        }
        notify()->error('Oops!!!, algo salió mal, intente de nuevo.');
        return redirect()->back();
    }

    public function purchaseOrderReceivingSave(Request $request)
    {
        DB::beginTransaction();
        try {
        	$receiving_token = Str::random(15);
        	foreach ($request->received_qty as $key => $recQty) {
	    		if(!empty($recQty))
	  			{
		        	$purchaseOrderRec = new PurchaseOrderReceiving;
			        $purchaseOrderRec->purchase_order_id 		= $request->purchase_order_id;
			        $purchaseOrderRec->purchase_order_product_id= $request->purchase_order_product_id[$key];
			        $purchaseOrderRec->producto_id    	= $request->producto_id[$key];
			        $purchaseOrderRec->receiving_token  = $receiving_token;
			        $purchaseOrderRec->received_qty  	= $recQty;
			        $purchaseOrderRec->save();


			        //Stock In Start
		        	$getStock = Producto::select('id','stock')->find($request->producto_id[$key]);
              // save Start
              $oldStock = $getStock->stock;
		        	$getStock->stock = $getStock->stock + $recQty;
		        	$getStock->save();
		        	//Stock In End

		        	//Start ***Available Quantity update in ML ONLY if previos stock not < 0
              if ($oldStock >= 0) {
                $response = $this->addStockMl($request->producto_id[$key], $recQty);
              }
              //End ***Available Quantity update in ML

		        	//Accepted Qty Start
		        	$getAcceptedQty = PurchaseOrderProduct::select('id','required_qty','accept_qty','return_qty')->find($request->purchase_order_product_id[$key]);
		        	$totalAcceptedQty = $getAcceptedQty->accept_qty + $recQty;
		        	$totalReceivedQty = $getAcceptedQty->accept_qty + $getAcceptedQty->return_qty + $recQty;
		        	$getAcceptedQty->accept_qty = $totalAcceptedQty;
		        	$getAcceptedQty->receiving_status = ($totalReceivedQty >= $getAcceptedQty->required_qty) ? 'Completed' : 'Process';
		        	$getAcceptedQty->save();
		        	//Accepted Qty End
			    }
			}

			$checkPOStatus = PurchaseOrderProduct::whereIn('receiving_status',['Pending','Process'])->where('purchase_order_id', $request->purchase_order_id)->count();
			$updateStatus = PurchaseOrder::find($request->purchase_order_id);
			$updateStatus->po_status = ($checkPOStatus<1) ? 'Completed' : 'Receiving';
			$updateStatus->po_completed_date = ($checkPOStatus<1) ? date('Y-m-d') : null;
			$updateStatus->save();

	        DB::commit();
	        notify()->success('Realizado, Cantidad aceptada en la O/C.');
            return redirect()->route('purchase-order-list');
        } catch (\Exception $exception) {
            DB::rollback();
            notify()->error('Error, Oops!!!, algo fué mal, intente de nuevo.'. $exception->getMessage());
            return redirect()->back()->withInput();
        } catch (\Throwable $exception) {
            DB::rollback();
            notify()->error('Error, Oops!!!, algo fué mal, intente de nuevo.'. $exception->getMessage());
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
