<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\booking;
use App\PurchaseOrder;
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
            $getRec = booking::select('id', 'created_by', 'firstname', 'lastname', 'tranjectionid', 'payableAmount', 'paymentThrough', 'deliveryStatus', 'created_at')->where('created_by', '!=', null)
                ->orderBy('id', 'DESC')
                ->with('createdBy')
                ->whereRaw($whereRaw);
        }
        else
        {
            $getRec = booking::select('id', 'created_by', 'firstname', 'lastname', 'tranjectionid', 'payableAmount', 'paymentThrough', 'deliveryStatus', 'created_at')->where('created_by', '!=', null)
                ->orderBy('id', 'DESC')
                ->with('createdBy')
                ;

        }
        if(auth()->user()->hasRole('admin'))
        {
            $query = $getRec->get();
        }
        else
        {
            $query = $getRec->where('created_by', auth()->id())->get();
        }
        return datatables($query)->editColumn('placed_by', function ($query)
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
        })->editColumn('customer_name', function ($query)
        {
            return '<strong>' . $query->firstname . ' ' . $query->lastname . '</strong>';
        })->editColumn('order_date', function ($query)
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

            $query = PurchaseOrder::orderBy('id', 'DESC')->with('supplier')
                ->whereRaw($whereRaw)->get();
        }
        else
        {
            $query = PurchaseOrder::orderBy('id', 'DESC')->with('supplier')
                ->get();

        }
        return datatables($query)->editColumn('supplier', function ($query)
        {
            return $query
                ->supplier->name;
        })->editColumn('invoice_amount', function ($query)
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
            ->where('stock', '<', env('MIN_STOCK', '100'))
            ->get();
        return datatables($query)
            ->editColumn('stock', function ($query)
            {
                return '<span class="badge badge-success">'.$query->stock.'</span>';
            })
            ->editColumn('precio', function ($query)
            {
                return '<strong>$'.$query->precio.'</strong>';
            })
            ->editColumn('item', function ($query)
            {
                return $query->item->nombre;
            })
            ->editColumn('categoria', function ($query)
            {
                return $query->categoria->nombre;
            })
            ->editColumn('marca', function ($query)
            {
                return $query->marca->nombre;
            })
            ->editColumn('modelo', function ($query)
            {
                return $query->modelo->nombre;
            })
            ->editColumn('medida', function ($query)
            {
                return $query->medida->nombre;
            })
            ->editColumn('altura', function ($query)
            {
                return $query->altura->nombre;
            })
            ->editColumn('tecnologia', function ($query)
            {
                return $query->tecnologia->nombre;
            })
            ->editColumn('garantia', function ($query)
            {
                return $query->garantia->nombre;
            })
            ->editColumn('postura', function ($query)
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
}
