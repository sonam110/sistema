<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\booking;
use App\PurchaseOrder;
use App\SupplierInvoice;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\salesReport;
use App\Exports\purchaseReport;
use App\Producto;

class ReportController extends Controller
{
	function __construct()
    {
    	$this->middleware('permission:sales-report', ['only' => ['salesReport','salesReportList','getWhereRawFromRequest']]);
    	$this->middleware('permission:export-sales-report', ['only' => ['downloadsalesReport','getWhereRawFromRequest']]);
        $this->middleware('permission:purchase-report', ['only' => ['purchaseReport','purchaseReportList','getWhereRawFromRequest']]);
        $this->middleware('permission:export-purchase-report', ['only' => ['downloadpurchaseReport','getWhereRawFromRequest']]);
    	$this->middleware('permission:short-stock-item-report', ['only' => ['shortStockItemReport','shortStockItemsDatatable']]);
    }

    public function salesReport()
    {
        return view('reports.sales-report');
    }

    public function salesReportList(Request $request)
    {
        $whereRaw = $this->getWhereRawFromRequest($request);
        if ($whereRaw != '')
        {
            $getRec = booking::select('bookings.id', 'bookings.created_by', 'bookings.firstname', 'bookings.lastname', 'bookings.tranjectionid', 'bookings.payableAmount', 'bookings.paymentThrough', 'bookings.installments', 'bookings.deliveryStatus', 'bookings.cae_fac', 'bookings.created_at')
                ->whereNotIn('bookings.deliveryStatus',['Cancel','Return'])
                ->orderBy('bookings.id', 'DESC')
                ->with('createdBy')
                ->whereRaw($whereRaw);

        }
        else
        {
            $getRec = booking::select('bookings.id', 'bookings.created_by', 'bookings.firstname', 'bookings.lastname', 'bookings.tranjectionid', 'bookings.payableAmount', 'bookings.paymentThrough', 'bookings.installments', 'bookings.deliveryStatus', 'bookings.cae_fac', 'bookings.created_at')
                ->whereNotIn('bookings.deliveryStatus',['Cancel','Return'])
                ->orderBy('bookings.id', 'DESC')
                ->orderBy('bookings.id', 'DESC')
                ->with('createdBy');

        }
        if(auth()->user()->hasRole('admin'))
        {
            $query = $getRec;
        }
        else
        {
            $query = $getRec->where('created_by', auth()->id());
        }
        return datatables($query)->addColumn('placed_by', function ($query)
        {
            if ($query->createdBy)
            {
                return '<strong>' . $query
                    ->createdBy->name . ' ' . $query
                    ->createdBy->lastname . '</strong>';
            }
            return '-';
        })->editColumn('tranjectionid', function ($query)
        {
            return '<strong>' . $query->tranjectionid . '</strong>';
        })->addColumn('customer_name', function ($query)
        {
            return '<strong>' . $query->firstname . ' ' . $query->lastname . '</strong>';
        })->addColumn('order_date', function ($query)
        {
            return $query
                ->created_at
                ->format('Y-m-d');
        })->editColumn('payableAmount', function ($query)
        {
            return '<strong>$ ' . number_format($query->payableAmount,2,',','.') . '</strong>';
        })->editColumn('deliveryStatus', function ($query)
        {
            if ($query->deliveryStatus == 'Process')
            {
                $status = '<span class="badge badge-info">' . $query->deliveryStatus . '</span>';
            }
            elseif ($query->deliveryStatus == 'Cancel')
            {
                $status = '<span class="badge badge-danger">' . $query->deliveryStatus . '</span>';
            }
            elseif ($query->deliveryStatus == 'Delivered')
            {
                $status = '<span class="badge badge-success">' . $query->deliveryStatus . '</span>';
            }
            elseif ($query->deliveryStatus == 'Return')
            {
                $status = '<span class="badge badge-danger">' . $query->deliveryStatus . '</span>';
            }
            else
            {
                $status = '<span class="badge badge-default">' . $query->deliveryStatus . '</span>';
            }
            return $status;
        })->addColumn('action', function ($query)
        {

            $view = auth()->user()
                ->can('sales-order-view') ? '<a class="btn btn-sm btn-info" href="' . route('sales-order-view', base64_encode($query->id)) . '" data-toggle="tooltip" data-placement="top" title="Ver Orden" data-original-title="Ver Pedido"><i class="fa fa-eye"></i></a>' : '';

            return '<div class="btn-group btn-group-xs">' . $view . '</div>';
        })->escapeColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }

    public function downloadsalesReport(Request $request)
    {
        $fileName = 'sales_' . time() . '.csv';
        $data = Excel::store(new salesReport($request) , $fileName, 'customer_uploads');
        $url = url('assets/uploads/reports/' . $fileName);
        if ($data)
        {
            return json_encode(['status' => 200, "message" => "Archivo descargado ", 'url' => $url, 'fileName' => $fileName]);
        }
        else
        {
            return json_encode(['status' => 403, "message" => "Opss! Algo salió mal ", 'url' => '', 'fileName' => '']);
        }

    }
    /*---------------Purchase Report--------------------*/

    public function purchaseReport()
    {
        return view('reports.purchase-report');
    }

    public function purchaseReportList(Request $request)
    {
        $whereRaw = $this->getWhereRawFromRequest($request);
        if ($whereRaw != '')
        {

            $query = PurchaseOrder::orderBy('purchase_orders.id', 'DESC')->with('supplier')
                ->whereRaw($whereRaw);
        }
        else
        {
            $query = PurchaseOrder::orderBy('purchase_orders.id', 'DESC')->with('supplier');

        }
        return datatables($query)->addColumn('supplier', function ($query)
        {
            return $query
                ->supplier->name;
        })->editColumn('gross_amount', function ($query)
        {
            return '<strong>$ ' . number_format($query->gross_amount,2,',','.') . '</strong>';
        })->editColumn('po_status', function ($query)
        {
            if ($query->po_status == 'Sent')
            {
                $status = '<span class="badge badge-info">' . $query->po_status . '</span>';
            }
            elseif ($query->po_status == 'Receiving')
            {
                $status = '<span class="badge badge-warning">' . $query->po_status . '</span>';
            }
            elseif ($query->po_status == 'Completed')
            {
                $status = '<span class="badge badge-success">' . $query->po_status . '</span>';
            }
            else
            {
                $status = '<span class="badge badge-default">' . $query->po_status . '</span>';
            }
            return $status;
        })->addColumn('action', function ($query)
        {

            $view = auth()->user()
                ->can('purchase-order-view') ? '<a class="btn btn-sm btn-info" href="' . route('purchase-order-view', base64_encode($query->id)) . '" data-toggle="tooltip" data-placement="top" title="View PO" data-original-title="Ver Orden"><i class="fa fa-eye"></i></a>' : '';

            return '<div class="btn-group btn-group-xs">' . $view . '</div>';
        })->escapeColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }


    public function purchaseConceptReport(Request $request)
    {
      	$from_date 	= null;
        $to_date    = null;

    	$totalConcepts = PurchaseOrder::join('purchase_concepts', function ($join) {
                $join->on('purchase_orders.concept_id', '=', 'purchase_concepts.id');
            })
          ->whereIn('type',array(2,3))
          ->selectRaw('sum(case when type=2 then 1 else -1 end * purchase_orders.total_amount) as total, purchase_concepts.description as concepto')
          ->groupBy('purchase_concepts.description');

    	if($request->from_date)
    	{
    		$from_date = $request->from_date;
    		$totalConcepts->whereDate('purchase_orders.created_at', '>=', $request->from_date);
    	}
    	if($request->to_date)
    	{
    		$to_date = $request->to_date;
    		$totalConcepts->whereDate('purchase_orders.created_at', '<=', $request->to_date);
    	}
        $totalConceptsData = $totalConcepts->get();


    	$totalProvee = PurchaseOrder::join('suppliers', function ($join) {
                $join->on('purchase_orders.supplier_id', '=', 'suppliers.id');
            })
          ->where('type',2)
          ->selectRaw('sum(case when type=2 then 1 else -1 end * purchase_orders.total_amount) as total, suppliers.name')
          ->groupBy('suppliers.name');

    	if($request->from_date)
    	{
    		$from_date = $request->from_date;
    		$totalProvee->whereDate('purchase_orders.created_at', '>=', $request->from_date);
    	}
    	if($request->to_date)
    	{
    		$to_date = $request->to_date;
    		$totalProvee->whereDate('purchase_orders.created_at', '<=', $request->to_date);
    	}
        $totalProveeData = $totalProvee->get();

        $dateList = $this->dateList($from_date, $to_date);

        return view('reports.purchase-concept-report',compact('totalConceptsData','from_date','to_date','totalProveeData'));
    }

    public function downloadpurchaseReport(Request $request)
    {
        $fileName = 'purchase_' . time() . '.csv';
        $data = Excel::store(new purchaseReport($request) , $fileName, 'customer_uploads');
        $url = url('assets/uploads/reports/' . $fileName);
        if ($data)
        {
            return json_encode(['status' => 200, "message" => "Archivo descargado", 'url' => $url, 'fileName' => $fileName]);
        }
        else
        {
            return json_encode(['status' => 403, "message" => "Opss! Algo Salió mal ", 'url' => '', 'fileName' => '']);
        }

    }
		/*---------------Facturas Report--------------------*/
		public function facturasConceptReport(Request $request)
		{
		    $from_date 	= null;
		    $to_date    = null;

		  $totalConcepts = SupplierInvoice::join('purchase_concepts', function ($join) {
		            $join->on('supplier_invoices.concept_id', '=', 'purchase_concepts.id');
		        })
		      ->whereIn('type',array(2,3))
		      ->selectRaw('sum(case when type=2 then 1 else -1 end * supplier_invoices.total_amount) as total, purchase_concepts.description as concepto')
		      ->groupBy('purchase_concepts.description');

		  if($request->from_date)
		  {
		    $from_date = $request->from_date;
		    $totalConcepts->whereDate('supplier_invoices.created_at', '>=', $request->from_date);
		  }
		  if($request->to_date)
		  {
		    $to_date = $request->to_date;
		    $totalConcepts->whereDate('supplier_invoices.created_at', '<=', $request->to_date);
		  }
		    $totalConceptsData = $totalConcepts->get();


		  $totalProvee = SupplierInvoice::join('suppliers', function ($join) {
		            $join->on('supplier_invoices.supplier_id', '=', 'suppliers.id');
		        })
		      ->where('type',2)
		      ->selectRaw('sum(case when type=2 then 1 else -1 end * supplier_invoices.total_amount) as total, suppliers.name')
		      ->groupBy('suppliers.name');

		  if($request->from_date)
		  {
		    $from_date = $request->from_date;
		    $totalProvee->whereDate('supplier_invoices.created_at', '>=', $request->from_date);
		  }
		  if($request->to_date)
		  {
		    $to_date = $request->to_date;
		    $totalProvee->whereDate('supplier_invoices.created_at', '<=', $request->to_date);
		  }
		    $totalProveeData = $totalProvee->get();

		    $dateList = $this->dateList($from_date, $to_date);

		    return view('reports.facturas-concept-report',compact('totalConceptsData','from_date','to_date','totalProveeData'));
		}


    private function getWhereRawFromRequest(Request $request)
    {
        $w = '';
        if (is_null($request->dateRange) == false)
        {

            if ($request->dateRange == 'day')
            {
                if ($w != '')
                {
                    $w = $w . " AND ";
                }
                $w = $w . "(" . "DATE(created_at) = '" . date('Y-m-d') . "')";

            }
            else if ($request->dateRange == 'week')
            {
                $end = date('Y-m-d');
                $start = date('Y-m-d', strtotime('-7 days'));
                if ($w != '')
                {
                    $w = $w . " AND ";
                }
                if ($start != '')
                {
                    $w = $w . "(" . "DATE(created_at) >= '" . $start . "')";
                }
                if (is_null($start) == false && is_null($end) == false)
                {
                    $w = $w . " AND ";
                }
                if ($end != '')
                {
                    $w = $w . "(" . "DATE(created_at) <= '" . $end . "')";
                }
            }
            else if ($request->dateRange == 'month')
            {
                if ($w != '')
                {
                    $w = $w . " AND ";
                }
                $w = $w . "(" . "MONTH(created_at) = '" . date('m') . "')";
            }
        }
        return ($w);

    }

    public function shortStockItemReport()
    {
        return view('reports.short-stock-item-report');
    }

    public function shortStockItemsDatatable(Request $request)
    {
        $query = Producto::select('*')->with('categoria','marca','modelo','item','altura','garantia','medida','postura','tecnologia')
            ->where('stock', '<', env('MIN_STOCK', '100'));
        return datatables($query)
            ->editColumn('stock', function ($query)
            {
                return '<span class="badge badge-success">'.$query->stock.'</span>';
            })
            ->editColumn('precio', function ($query)
            {
                return '<strong>$'.$query->precio.'</strong>';
            })
            ->addColumn('item', function ($query)
            {
                return $query->item->nombre;
            })
            ->addColumn('categoria', function ($query)
            {
                return $query->categoria->nombre;
            })
            ->addColumn('marca', function ($query)
            {
                return $query->marca->nombre;
            })
            ->addColumn('modelo', function ($query)
            {
                return $query->modelo->nombre;
            })
            ->addColumn('medida', function ($query)
            {
                return $query->medida->nombre;
            })
            ->addColumn('altura', function ($query)
            {
                return $query->altura->nombre;
            })
            ->addColumn('tecnologia', function ($query)
            {
                return $query->tecnologia->nombre;
            })
            ->addColumn('garantia', function ($query)
            {
                return $query->garantia->nombre;
            })
            ->addColumn('postura', function ($query)
            {
                return $query->postura->nombre;
            })
            ->editColumn('activo', function ($query)
            {
                if ($query->activo == 0)
                {
                    $status = '<span class="badge badge-danger">Inactive</span>';
                }
                else
                {
                    $status = '<span class="badge badge-success">Active</span>';
                }
                return $status;
            })
        ->escapeColumns([''])
        ->addIndexColumn()
        ->make(true);
    }
    private function dateList($from_date=null, $to_date=null, $withList=null)
    {
        //Date wise list
        $dateList = array();
        $diff = 6;
        $today      = new \DateTime();
        $earlier    = $today->sub(new \DateInterval('P'.$diff.'D'));
        $later      = new \DateTime(date('Y-m-d'));
        if($withList=='yes' || (empty($from_date) && empty($to_date)))
        {
            //List Date Wise
            if(!empty($from_date) && !empty($to_date))
            {
                $earlier    = new \DateTime($from_date);
                $later      = new \DateTime($to_date);
            } elseif(!empty($from_date) && empty($to_date)) {
                $earlier    = new \DateTime($from_date);
                $later      = new \DateTime(date('Y-m-d'));
            }

            $end       = $later->modify('+1 day');
            $interval  = new \DateInterval('P1D');
            $period = new \DatePeriod($earlier, $interval, $end);
            $dateList = array();
            foreach ($period as $key => $value) {
                $dateList[] = $value->format("Y-m-d");
            }
        }
        return $dateList;
    }
}
