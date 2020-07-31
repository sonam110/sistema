<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PurchaseOrder;
use App\PurchaseOrderProduct;
use App\PurchaseOrderReceiving;
use App\Producto;
use DB;

class PurchaseOrderReceivingController extends Controller
{
    function __construct()
    {
        $this->middleware(['role:admin','permission:purchase-order-receiving']);
    }

    public function purchaseOrderReceivedList()
    {
	    return view('purchases.purchase-order-receiving');
    }

    public function poReceivedProductDatatable(Request $request)
    {
        $query = PurchaseOrderReceiving::select('*')->orderBy('id','DESC')->with('purchaseOrder', 'purchaseOrder.supplier', 'producto')->get();
        return datatables($query)
	        ->editColumn('po_no', function ($query)
		        {
		            return $query->purchaseOrder->po_no;
		        })
	       	->editColumn('po_date', function ($query)
		        {
		            return $query->purchaseOrder->po_date;
		        })
	       	->editColumn('supplier', function ($query)
		        {
		            return $query->purchaseOrder->supplier->name;
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
	        /*->addColumn('action', function ($query)
	        {
	        	$return = auth()->user()->can('purchase-order-return') ? '<a class="btn btn-sm btn-danger" href="'.route('purchase-order-return',base64_encode($query->id)).'" data-toggle="tooltip" data-placement="top" title="Return This product?" data-original-title="Return This product?"><i class="fa fa-mail-reply"></i></a>' : '';
                return '<div class="btn-group btn-group-xs">'.$return.'</div>';
	        })*/
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
        notify()->error('Oops!!!, something went wrong, please try again.');
        return redirect()->back();
    }

    public function purchaseOrderReceivingSave(Request $request)
    {
        DB::beginTransaction();
        try {
        	foreach ($request->received_qty as $key => $recQty) {
	    		if(!empty($recQty))
	  			{
		        	$purchaseOrderRec = new PurchaseOrderReceiving;
			        $purchaseOrderRec->purchase_order_id 		= $request->purchase_order_id;
			        $purchaseOrderRec->purchase_order_product_id= $request->purchase_order_product_id[$key];
			        $purchaseOrderRec->producto_id    	= $request->producto_id[$key];
			        $purchaseOrderRec->received_qty  	= $recQty;
			        $purchaseOrderRec->save();

			        
			        //Stock In Start
		        	$getStock = Producto::select('id','stock')->find($request->producto_id[$key]);
		        	$getStock->stock = $getStock->stock + $recQty;
		        	$getStock->save();
		        	//Stock In End

		        	//Accepted Qty Start
		        	$getAcceptedQty = PurchaseOrderProduct::select('id','accept_qty','required_qty')->find($request->purchase_order_product_id[$key]);
		        	$totalAcceptedQty = $getAcceptedQty->accept_qty + $recQty;
		        	$getAcceptedQty->accept_qty = $totalAcceptedQty;
		        	$getAcceptedQty->receiving_status = ($totalAcceptedQty >= $getAcceptedQty->required_qty) ? 'Completed' : 'Process';
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
	        notify()->success('Success, Purchase order created successfully.');
            return redirect()->route('purchase-order-list'); 
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
