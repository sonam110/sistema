<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\PurchaseOrder;
use App\PurchaseOrderProduct;
use App\PurchaseOrderReturn;
use App\Producto;
use DB;

class PurchaseOrderReturnController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:purchase-order-return', ['except' => ['purchaseOrderReturnSave']]);
    }

    public function purchaseOrderReturnList()
    {
	    return view('purchases.purchase-order-return');
    }

    public function poReturnProductDatatable(Request $request)
    {
        $query = PurchaseOrderReturn::select('*')->orderBy('id','DESC')->with('purchaseOrder', 'purchaseOrder.supplier', 'producto')->get();
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
	        ->editColumn('returned_qty', function ($query)
		        {
		            return '<strong>'.$query->return_qty.'</strong>';
		        })
	        ->editColumn('returned_amount', function ($query)
		        {
		            return '<strong>$'.$query->return_price.'</strong>';
		        })
	        ->editColumn('returned_date', function ($query)
		        {
		        	return $query->created_at->format('Y-m-d');
		        })
        ->escapeColumns(['action'])
        ->addIndexColumn()
        ->make(true);
    }

    public function purchaseOrderReturn($id)
    {
    	if(PurchaseOrder::whereNotIn('po_status', ['Pending', 'Sent'])->find(base64_decode($id)))
        {
            $poInfo = PurchaseOrder::find(base64_decode($id));
	        return view('purchases.purchase-order-return', compact('poInfo'));
        }
        notify()->error('Oops!!!, algo salió mal, intente de nuevo.');
        return redirect()->back();
    }

    public function purchaseOrderReturnSave(Request $request)
    {
        DB::beginTransaction();
        try {
        	$return_token = Str::random(15);
        	$getTax = PurchaseOrder::select('tax_percentage')->find($request->purchase_order_id);

        	foreach ($request->return_qty as $key => $returnQty) {
	    		if(!empty($returnQty))
	  			{
	  				$calTax = (($returnQty * $request->return_price[$key]) * $getTax->tax_percentage)/100;

		        	$purchaseOrderReturn = new PurchaseOrderReturn;
			        $purchaseOrderReturn->purchase_order_id 		= $request->purchase_order_id;
			        $purchaseOrderReturn->purchase_order_product_id= $request->purchase_order_product_id[$key];
			        $purchaseOrderReturn->producto_id    	= $request->producto_id[$key];
			        $purchaseOrderReturn->return_token  	= $return_token;
			        $purchaseOrderReturn->return_qty  		= $returnQty;
			        $purchaseOrderReturn->return_price  	= (($returnQty * $request->return_price[$key]) + $calTax);
			        $purchaseOrderReturn->return_note  		= $request->return_note;
			        $purchaseOrderReturn->save();


			        //Stock Out Start
		        	$getStock = Producto::select('id','stock')->find($request->producto_id[$key]);
		        	$getStock->stock = $getStock->stock - $returnQty;
		        	$getStock->save();
		        	//Stock Out End

		        	//Return Qty Start
		        	$getReturnQty = PurchaseOrderProduct::select('id','required_qty','accept_qty','return_qty')->find($request->purchase_order_product_id[$key]);
		        	$totalReturnQty = $getReturnQty->return_qty + $returnQty;
		        	$totalReceivedQty = $getReturnQty->accept_qty + $getReturnQty->return_qty + $returnQty;
		        	$getReturnQty->return_qty = $totalReturnQty;
		        	$getReturnQty->receiving_status = ($totalReceivedQty >= $getReturnQty->required_qty) ? 'Completed' : 'Process';
		        	$getReturnQty->save();
		        	//Return Qty End
			    }
			}

			$checkPOStatus = PurchaseOrderProduct::whereIn('receiving_status',['Pending','Process'])->where('purchase_order_id', $request->purchase_order_id)->count();
			$updateStatus = PurchaseOrder::find($request->purchase_order_id);
			$updateStatus->po_status = ($checkPOStatus<1) ? 'Completed' : 'Receiving';
			$updateStatus->po_completed_date = ($checkPOStatus<1) ? date('Y-m-d') : null;
			$updateStatus->save();

	        DB::commit();
	        notify()->success('Realizada, Cantidad devuelta aceptada en la O/C..');
            return redirect()->back();
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
}
