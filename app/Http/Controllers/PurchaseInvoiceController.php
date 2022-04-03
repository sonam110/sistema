<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PurchaseOrder;
use App\PurchaseOrderProduct;
use Illuminate\Support\Str;
use App\Mail\PurchaseOrder as PurchaseOrderMail;
use DB;
//use PDF;
//use Mail;

class PurchaseInvoiceController extends Controller
{
	function __construct()
    {
        $this->middleware('permission:purchase-invoice-list', ['only' => ['purchaseOrderList','purchaseOrderDatatable','productsOrderedButNotReceived','productsOrderedButNotReceivedList']]);
        $this->middleware('permission:purchase-order-create', ['only' => ['purchaseOrderCreate','purchaseOrderSave']]);
        $this->middleware('permission:purchase-order-view', ['only' => ['purchaseOrderView']]);
        $this->middleware('permission:purchase-order-delete', ['only' => ['purchaseOrderDelete']]);
        $this->middleware('permission:purchase-order-download', ['only' => ['purchaseOrderDownload']]);
        $this->middleware('permission:purchase-order-action', ['only' => ['purchaseOrderAction']]);
    }

    public function purchaseInvoiceList()
    {
      	return view('purchases.invoice-order');
    }

    public function purchaseInvoiceDatatable(Request $request)
    {
        $query = PurchaseOrder::select('*')->whereIn('type',array(2,3))->orderBy('id','DESC')->with('supplier')->get();
        return datatables($query)
	        ->editColumn('supplier', function ($query)
	        {
	            return $query->supplier->name;
	        })
	        ->editColumn('type', function ($query)
	        {
	            if ($query->type==2) {return 'FAC';} else {return 'NC';}
	        })
            ->editColumn('invoice_amount', function ($query)
            {
                return '<strong>$'.$query->gross_amount.'</strong>';
            })
	        ->editColumn('concept', function ($query)
	        {
	            return $query->Concept->description;
	        })
	        ->editColumn('payment', function ($query)
	        {
	            if ($query->payment==0) {return 'No';} else {return 'Si';}
	        })
	        ->addColumn('action', function ($query)
	        {
	        	//$download = auth()->user()->can('purchase-order-download') ? '<a class="btn btn-sm btn-default" target="_blank" href="'.route('purchase-order-download',base64_encode($query->id)).'" data-toggle="tooltip" data-placement="top" title="Download / Print" data-original-title="Descargar / Imrimir"><i class="fa fa-download"></i></a>' : '';

	        	$view = auth()->user()->can('purchase-invoice-view') ? '<a class="btn btn-sm btn-info" href="'.route('purchase-invoice-view',base64_encode($query->id)).'" data-toggle="tooltip" data-placement="top" title="Ver Factura" data-original-title="Ver factura"><i class="fa fa-eye"></i></a>' : '';

                $delete = auth()->user()->can('purchase-invoice-delete') ? '<a class="btn btn-sm btn-danger" href="'.route('purchase-invoice-delete',base64_encode($query->id)).'" onClick="return confirm(\'Está seguro que desea eliminarlo?\');" data-toggle="tooltip" data-placement="top" title="Eliminar Factura" data-original-title="Eliminar Factura"><i class="fa fa-trash"></i></a>' : '';
                
                $pagar='';
                if ($query->payment==0)
                  {
                  $pagar = auth()->user()->can('purchase-invoice-edit') ? '<a class="btn btn-sm btn-info" href="'.route('purchase-invoice-pay',base64_encode($query->id)).'" onClick="return confirm(\'Está seguro que desea Pagar esta Factura?\');" data-toggle="tooltip" data-placement="top" title="Pagar Factura" data-original-title="Pagar Factura"><i class="fa fa-dollar"></i></a>' : '';
                  }
                return '<div class="btn-group btn-group-xs">'.$view.$pagar.$delete.'</div>';
	        })
        ->escapeColumns(['action'])
        ->addIndexColumn()
        ->make(true);
    }

    public function purchaseInvoiceCreate()
    {
        return View('purchases.invoice-order');
    }

    public function purchaseInvoiceSave(Request $request)
    {
        $messages = array(
         'po_date.required' => 'La fecha es requerida',
         'po_no.required' => 'El numero es requerido',
         'concept_id.exists'  => 'El concepto es requerido',
         'supplier_id.exists'  => 'El Proveedor es requerido'
        );
        $this->validate($request, [
            'supplier_id' 	=> 'required|integer|exists:suppliers,id',
            'concept_id' 	=> 'required|integer|exists:purchase_concepts,id',
            'po_date'     	=> 'required',
            'po_no'     	=> 'required'
        ],$messages);

        DB::beginTransaction();
        try {
        	$purchaseOrder = new PurchaseOrder;
	        $purchaseOrder->supplier_id     = $request->supplier_id;
            $purchaseOrder->concept_id      = $request->concept_id;
	        $purchaseOrder->po_no 			= $request->po_no;
	        $purchaseOrder->po_date    		= $request->po_date;
	        $purchaseOrder->total_amount    = $request->total_amount;
	        $purchaseOrder->tax_percentage  = $request->tax_percentage;
	        $purchaseOrder->tax_amount    	= $request->tax_amount;
	        $purchaseOrder->gross_amount    = $request->gross_amount;
	        $purchaseOrder->remark 			= $request->remark;
            $purchaseOrder->perc_iibb 		= $request->perc_iibb;
            $purchaseOrder->perc_gan 		= $request->perc_gan;
            $purchaseOrder->perc_iva 		= $request->perc_iva;
            $purchaseOrder->po_status		= 'Completed';
            $purchaseOrder->type 			= $request->type;
	        $purchaseOrder->is_read_token   = Str::random(40);
	        $purchaseOrder->save();

	        DB::commit();
	        notify()->success('Hecho, La factura creada correctamente.');
            return redirect()->route('purchase-invoice-list');
        } catch (\Exception $exception) {
            DB::rollback();
            notify()->error('Error, Oops!!!, algo salió mal, pruebe de nuevo.'.$exception->getMessage());
            return redirect()->back()->withInput();
        } catch (\Throwable $exception) {
            DB::rollback();
            notify()->error('Error, Oops!!!, algo va mal, pruebe de nuevo.');
            return redirect()->back()->withInput();
        }
    }

    public function purchaseInvoiceView($id)
    {
        if(PurchaseOrder::find(base64_decode($id)))
        {
            $poInfo = PurchaseOrder::find(base64_decode($id));
            return View('purchases.invoice-order', compact('poInfo'));
        }
        notify()->error('Oops!!!, salgo va mal, pruebe de nuevo.');
        return redirect()->back();
    }

    public function purchaseInvoicePay($id)
    {
        $p = PurchaseOrder::find(base64_decode($id)); 
        if ($p)
        {
            $p->payment=1;
            $p->save();
            notify()->success('Hecho, Factura Pagada.');
            return redirect()->back();
        }
        notify()->error('Oops!!!, algo va mal, pruebe de nuevo.');
        return redirect()->back();
    }

    public function purchaseInvoiceDelete($id)
    {
        if(PurchaseOrder::find(base64_decode($id)))
        {
            PurchaseOrder::find(base64_decode($id))->delete();
            PurchaseOrderProduct::where('purchase_order_id', base64_decode($id))->delete();
            notify()->success('Hecho, Factura Eliminada.');
            return redirect()->back();
        }
        notify()->error('Oops!!!, algo va mal, pruebe de nuevo.');
        return redirect()->back();
    }

    public function productsOrderedButNotReceived()
    {
        return view('purchases.products-ordered-but-not-received');
    }

    public function productsOrderedButNotReceivedList(Request $request)
    {
        $query = PurchaseOrderProduct::select('purchase_order_products.*', 'purchase_orders.id as poId')->orderBy('id','DESC')->with('purchaseOrder', 'purchaseOrder.supplier', 'producto')
        ->join('purchase_orders', function ($join) {
            $join->on('purchase_orders.id', '=', 'purchase_order_products.purchase_order_id');
        })
        ->where('receiving_status', '!=', 'Completed')
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
            ->editColumn('required_qty', function ($query)
                {
                    return '<strong>'.$query->required_qty.'</strong>';
                })
            ->editColumn('accept_qty', function ($query)
                {
                    return '<strong>'.$query->accept_qty.'</strong>';
                })
            ->addColumn('action', function ($query)
            {
                $receiving = auth()->user()->can('purchase-order-receiving') ? '<a class="btn btn-sm btn-success" href="'.route('purchase-order-receiving',base64_encode($query->poId)).'" data-toggle="tooltip" data-placement="top" title="Receiving" data-original-title="Recepción"><i class="fa fa-plus"></i></a>' : '';

                return '<div class="btn-group btn-group-xs">'.$receiving.'</div>';
            })
        ->escapeColumns(['action'])
        ->addIndexColumn()
        ->make(true);
    }
}
