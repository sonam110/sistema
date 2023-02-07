<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PurchaseOrder;
use App\PurchaseOrderProduct;
use Illuminate\Support\Str;
use App\Mail\PurchaseOrder as PurchaseOrderMail;
use DB;
use PDF;
use Mail;

class PurchaseOrderController extends Controller
{
	function __construct()
    {
        $this->middleware('permission:purchase-order-list', ['only' => ['purchaseOrderList','purchaseOrderDatatable','productsOrderedButNotReceived','productsOrderedButNotReceivedList']]);
        $this->middleware('permission:purchase-order-create', ['only' => ['purchaseOrderCreate','purchaseOrderSave']]);
        $this->middleware('permission:purchase-order-view', ['only' => ['purchaseOrderView']]);
        $this->middleware('permission:purchase-order-delete', ['only' => ['purchaseOrderDelete']]);
        $this->middleware('permission:purchase-order-download', ['only' => ['purchaseOrderDownload']]);
        $this->middleware('permission:purchase-order-action', ['only' => ['purchaseOrderAction']]);
    }

    public function purchaseOrderList()
    {
      	return view('purchases.purchase-order');
    }

    public function purchaseOrderDatatable(Request $request)
    {
        $query = PurchaseOrder::select('*')->where('purchase_orders.type',1)->orderBy('purchase_orders.id','DESC')->with('supplier');
        return datatables($query)
	        ->addColumn('checkbox', function ($query)
	        {
	            return '<label class="custom-control custom-checkbox">
	                   <input type="checkbox"  name="boxchecked[]" value="' . $query->id . '"  class ="colorinput-input custom-control-input allChecked" id="boxchecked">
	                     <span class="custom-control-label"></span>
	                    </label>';
	        })
	        ->addColumn('supplier', function ($query)
	        {
	            return $query->supplier->name;
	        })
            ->editColumn('gross_amount', function ($query)
            {
                return '<strong>$'.$query->gross_amount.'</strong>';
            })
	        ->editColumn('po_status', function ($query)
	        {
	            if ($query->po_status == 'Sent')
	            {
	                $status = '<span class="badge badge-info">'.$query->po_status.'</span>';
	            }
	            elseif ($query->po_status == 'Receiving')
	            {
	                $status = '<span class="badge badge-warning">'.$query->po_status.'</span>';
	            }
	            elseif ($query->po_status == 'Completed')
	            {
	                $status = '<span class="badge badge-success">'.$query->po_status.'</span>';
	            }
	            else
	            {
	                $status = '<span class="badge badge-default">'.$query->po_status.'</span>';
	            }
	            return $status;
	        })
	        ->addColumn('action', function ($query)
	        {
	        	$download = auth()->user()->can('purchase-order-download') ? '<a class="btn btn-sm btn-default" target="_blank" href="'.route('purchase-order-download',base64_encode($query->id)).'" data-toggle="tooltip" data-placement="top" title="Download / Print" data-original-title="Descargar / Imrimir"><i class="fa fa-download"></i></a>' : '';
	        	$receiving = '';
	        	if($query->po_status!='Completed')
	        	{
	        		$receiving = auth()->user()->can('purchase-order-receiving') ? '<a class="btn btn-sm btn-success" href="'.route('purchase-order-receiving',base64_encode($query->id)).'" data-toggle="tooltip" data-placement="top" title="Receiving" data-original-title="Recepción"><i class="fa fa-plus"></i></a>' : '';
	        	}

                $return = '';
                if($query->po_status=='Receiving' || $query->po_status=='Completed')
                {
                    $return = auth()->user()->can('purchase-order-return') ? '<a class="btn btn-sm btn-warning" href="'.route('purchase-order-return',base64_encode($query->id)).'" data-toggle="tooltip" data-placement="top" title="Return Product" data-original-title="Devolver Producto"><i class="fa fa-mail-reply"></i></a>' : '';
                }

	        	$view = auth()->user()->can('purchase-order-view') ? '<a class="btn btn-sm btn-info" href="'.route('purchase-order-view',base64_encode($query->id)).'" data-toggle="tooltip" data-placement="top" title="View PO" data-original-title="Ver Orden"><i class="fa fa-eye"></i></a>' : '';

                $delete = auth()->user()->can('purchase-order-delete') ? '<a class="btn btn-sm btn-danger" href="'.route('purchase-order-delete',base64_encode($query->id)).'" onClick="return confirm(\'Está seguro que desea eliminarlo?\');" data-toggle="tooltip" data-placement="top" title="Delete PO" data-original-title="Eliminar Orden"><i class="fa fa-trash"></i></a>' : '';


                return '<div class="btn-group btn-group-xs">'.$download.$receiving.$return.$view.$delete.'</div>';
	        })
        ->escapeColumns(['action'])
        ->addIndexColumn()
        ->make(true);
    }

    public function purchaseOrderCreate()
    {
        return View('purchases.purchase-order');
    }

    public function purchaseOrderSave(Request $request)
    {
        $this->validate($request, [
            'supplier_id' 	=> 'required|integer|exists:suppliers,id',
            'po_date'     	=> 'required',
            "product_id"    => "required|array|min:1",
            "product_id.*"  => "required|string|distinct|min:1",
        ]);

        DB::beginTransaction();
        try {
        	$getLastNumber = PurchaseOrder::where('type','=','1')->orderBy('id', 'DESC')->first();
        	if($getLastNumber)
        	{
        		preg_match_all('!\d+!', $getLastNumber->po_no, $newNumber);
	            $nextPONumber = sprintf("%0".PurchaseOrder::PO_NUMBER_DIGIT."d", ($newNumber[0][0]+1));
	            $po_no =  PurchaseOrder::PO_NUMBER_PREFIX.$nextPONumber;
        	}
        	else
        	{
        		$nextPONumber = sprintf("%0".PurchaseOrder::PO_NUMBER_DIGIT."d", 1);
        		$po_no =  PurchaseOrder::PO_NUMBER_PREFIX.$nextPONumber;
        	}

        	$purchaseOrder = new PurchaseOrder;
	        $purchaseOrder->supplier_id     = $request->supplier_id;
	        $purchaseOrder->po_no 			= $po_no;
	        $purchaseOrder->po_date    		= $request->po_date;
	        $purchaseOrder->total_amount    = $request->total_amount;
	        $purchaseOrder->tax_percentage  = $request->tax_percentage;
	        $purchaseOrder->tax_amount    	= $request->tax_amount;
	        $purchaseOrder->gross_amount    = $request->gross_amount;
	        $purchaseOrder->remark 			= $request->remark;
	        $purchaseOrder->is_read_token   = Str::random(40);
	        $purchaseOrder->save();
	        if($purchaseOrder)
	        {
	        	foreach ($request->product_id as $key => $info) {
	        		if(!empty($info))
          			{
		        		$poProduct = new PurchaseOrderProduct;
		        		$poProduct->purchase_order_id 	= $purchaseOrder->id;
		        		$poProduct->producto_id 	= $info;
		        		$poProduct->required_qty	= $request->required_qty[$key];
		        		$poProduct->price 			= $request->price[$key];
		        		$poProduct->save();
		        	}
	        	}
	        }
	        DB::commit();
	        notify()->success('Hecho, Orden de Compra creada correctamente.');
            return redirect()->route('purchase-order-list');
        } catch (\Exception $exception) {
            DB::rollback();
            //die($exception->getMessage());
            notify()->error('Error, Oops!!!, algo salió mal, pruebe de nuevo.');
            return redirect()->back()->withInput();
        } catch (\Throwable $exception) {
            DB::rollback();
            notify()->error('Error, Oops!!!, algo va mal, pruebe de nuevo.');
            return redirect()->back()->withInput();
        }
    }

    public function purchaseOrderView($id)
    {
        if(PurchaseOrder::find(base64_decode($id)))
        {
            $poInfo = PurchaseOrder::find(base64_decode($id));
            return View('purchases.purchase-order', compact('poInfo'));
        }
        notify()->error('Oops!!!, salgo va mal, pruebe de nuevo.');
        return redirect()->back();
    }

    public function purchaseOrderAction(Request $request)
    {
      	$data  = $request->all();
      	foreach($request->input('boxchecked') as $action)
      	{
          	if($request->input('cmbaction')=='Delete') {
                PurchaseOrder::find($action)->delete();
                PurchaseOrderProduct::where('purchase_order_id', $action)->delete();
            }
            elseif($request->input('cmbaction')=='Sent')
            {
                $poInfo = PurchaseOrder::find($action);
                Mail::to($poInfo->supplier->email)->send(new PurchaseOrderMail($poInfo));
                $poInfo->po_status = ($poInfo->po_status=='Pending') ? 'Sent' : $poInfo->po_status;
                $poInfo->save();
            }
      	}
        if($request->input('cmbaction')=='Delete')
        {
            notify()->success('Hecho, Orden de Compra Eliminada.');
        }
        if($request->input('cmbaction')=='Sent')
        {
            notify()->success('Hecho, Mail de cancelacion enviado exitosamente al Proveedor.');
        }

      	return redirect()->back();
  	}

    public function purchaseOrderDelete($id)
    {
        if(PurchaseOrder::find(base64_decode($id)))
        {
            PurchaseOrder::find(base64_decode($id))->delete();
            PurchaseOrderProduct::where('purchase_order_id', base64_decode($id))->delete();
            notify()->success('Hecho, Orden de Compra Eliminada.');
            return redirect()->back();
        }
        notify()->error('Oops!!!, algo va mal, pruebe de nuevo.');
        return redirect()->back();
    }

    public function purchaseOrderDownload($id)
    {
        if(PurchaseOrder::find(base64_decode($id)))
        {
            $poInfo = PurchaseOrder::find(base64_decode($id));
	        $data = [
	            'poInfo' => $poInfo
	        ];
	        $pdf = PDF::loadView('purchases.purchase-order-download', $data);
	        return $pdf->stream($poInfo->po_no.'.pdf');
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
            ->addColumn('po_no', function ($query)
                {
                    return @$query->purchaseOrder->po_no;
                })
            ->addColumn('po_date', function ($query)
                {
                    return @$query->purchaseOrder->po_date;
                })
            ->addColumn('supplier', function ($query)
                {
                    return @$query->purchaseOrder->supplier->name;
                })
            ->addColumn('product_name', function ($query)
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
