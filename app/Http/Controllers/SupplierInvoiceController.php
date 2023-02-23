<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SupplierInvoice;
use Illuminate\Support\Str;
use App\Mail\PurchaseOrder as PurchaseOrderMail;
use DB;
class SupplierInvoiceController extends Controller
{
   function __construct()
    {
        $this->middleware('permission:purchase-invoice-list', ['only' => ['supplierInvoiceList','supplierInvoiceDatatable']]);
        $this->middleware('permission:purchase-order-create', ['only' => ['supplierInvoiceCreate','supplierInvoiceSave']]);
        $this->middleware('permission:purchase-order-view', ['only' => ['supplierInvoiceView']]);
        $this->middleware('permission:purchase-order-delete', ['only' => ['supplierInvoiceDelete']]);

    }

    public function supplierInvoiceList()
    {
        return view('supplier.supplier-invoice-manage');
    }

    public function supplierInvoiceDatatable(Request $request)
    {
        $query = SupplierInvoice::select('*')->orderBy('id','DESC')->with('supplier');
        return datatables($query)
            ->addColumn('supplier', function ($query)
            {
                return $query->supplier->name;
            })
            ->editColumn('type', function ($query)
            {
                if ($query->type==2) {return 'FAC';} else {return 'NC';}
            })
            ->addColumn('concept', function ($query)
            {
                return @$query->Concept->description;
            })

            ->editColumn('gross_amount', function ($query)
            {
                return '<strong>$'.$query->gross_amount.'</strong>';
            })
            ->editColumn('convention', function ($query)
            {
                return '<strong>$'.$query->convention.'</strong>';
            })
            ->editColumn('profit_advance', function ($query)
            {
                return '<strong>$'.$query->profit_advance.'</strong>';
            })
            ->editColumn('payment', function ($query)
            {
                if ($query->status==0) {return 'No';} else {return 'Si';}
            })
            ->addColumn('action', function ($query)
            {

                $view = auth()->user()->can('supplier-invoice-view') ? '<a class="btn btn-sm btn-info" href="'.route('supplier-invoice-view',base64_encode($query->id)).'" data-toggle="tooltip" data-placement="top" title="Ver Factura" data-original-title="Ver factura"><i class="fa fa-eye"></i></a>' : '';

                $delete = auth()->user()->can('supplier-invoice-delete') ? '<a class="btn btn-sm btn-danger" href="'.route('supplier-invoice-delete',base64_encode($query->id)).'" onClick="return confirm(\'Está seguro que desea eliminarlo?\');" data-toggle="tooltip" data-placement="top" title="Eliminar Factura" data-original-title="Eliminar Factura"><i class="fa fa-trash"></i></a>' : '';

                $pagar='';
                if ($query->status==0)
                  {
                  $pagar = auth()->user()->can('supplier-invoice-edit') ? '<a class="btn btn-sm btn-info" href="'.route('supplier-invoice-pay',base64_encode($query->id)).'" onClick="return confirm(\'Está seguro que desea Pagar esta Factura?\');" data-toggle="tooltip" data-placement="top" title="Pagar Factura" data-original-title="Pagar Factura"><i class="fa fa-dollar"></i></a>' : '';
                  }
                return '<div class="btn-group btn-group-xs">'.$view.$pagar.$delete.'</div>';
            })
        ->escapeColumns(['action'])
        ->addIndexColumn()
        ->make(true);
    }

    public function supplierInvoiceCreate()
    {
        return View('supplier.supplier-invoice-manage');
    }

    public function supplierInvoiceSave(Request $request)
    {
        $messages = array(
         'invoice_date.required' => 'La fecha es requerida',
         'invoice_no.required' => 'El numero es requerido',
         'supplier_id.exists'  => 'El Proveedor es requerido',
          'concept_id.exists'  => 'El concepto es requerido',
         'invoice_no.unique'  => 'El número de factura ya estaba tomado.',
        );
        $this->validate($request, [
            'supplier_id'   => 'required|integer|exists:suppliers,id',
            'concept_id'    => 'required|integer|exists:purchase_concepts,id',
            'invoice_date'       => 'required',
            'invoice_no'         => 'required|unique:supplier_invoices,invoice_no',
        ],$messages);

        DB::beginTransaction();
        try {
            $supplierInvoice = new SupplierInvoice;
            $supplierInvoice->supplier_id     = $request->supplier_id;
            $supplierInvoice->concept_id      = $request->concept_id;
            $supplierInvoice->invoice_no      = $request->invoice_no;
            $supplierInvoice->invoice_date    = $request->invoice_date;
            $supplierInvoice->total_amount    = $request->total_amount;
            $supplierInvoice->tax_percentage  = $request->tax_percentage;
            $supplierInvoice->tax_amount      = $request->tax_amount;
            $supplierInvoice->gross_amount    = $request->gross_amount;
            $supplierInvoice->remark          = $request->remark;
            $supplierInvoice->perc_iibb       = $request->perc_iibb;
            $supplierInvoice->perc_gan        = $request->perc_gan;
            $supplierInvoice->perc_iva        = $request->perc_iva;
            $supplierInvoice->convention      = $request->convention;
            $supplierInvoice->profit_advance  = $request->profit_advance;
            $supplierInvoice->status       = '1';
            $supplierInvoice->type            = $request->type;
            $supplierInvoice->save();

            DB::commit();
            notify()->success('Hecho, La factura creada correctamente.');
            return redirect()->route('supplier-invoice-list');
        } catch (\Exception $exception) {
            DB::rollback();
            notify()->error('Error, Oops!!!, algo salió mal, pruebe de nuevo.'.$exception->getMessage());
            return redirect()->back()->withInput();
        } catch (\Throwable $exception) {
            DB::rollback();
            //dd($exception->getMessage());
            notify()->error('Error, Oops!!!, algo va mal, pruebe de nuevo.');
            return redirect()->back()->withInput();
        }
    }

    public function supplierInvoiceView($id)
    {
        if(SupplierInvoice::find(base64_decode($id)))
        {
            $poInfo = SupplierInvoice::find(base64_decode($id));
            return View('supplier.supplier-invoice-manage', compact('poInfo'));
        }
        notify()->error('Oops!!!, salgo va mal, pruebe de nuevo.');
        return redirect()->back();
    }

    public function supplierInvoicePay($id)
    {
        $p = SupplierInvoice::find(base64_decode($id));
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

    public function supplierInvoiceDelete($id)
    {
        if(SupplierInvoice::find(base64_decode($id)))
        {
            SupplierInvoice::find(base64_decode($id))->delete();
            notify()->success('Hecho, Factura Eliminada.');
            return redirect()->back();
        }
        notify()->error('Oops!!!, algo va mal, pruebe de nuevo.');
        return redirect()->back();
    }

}
