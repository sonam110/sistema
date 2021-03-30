<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\booking;
use App\bookeditem;
use App\PurchaseOrder;
use App\BookingPaymentThrough;
use App\BookingInstallmentPaid;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\salesReport;
use App\Exports\purchaseReport;
use App\Producto;
use App\Marca;
use App\Modelo;
use App\Item;

class ReportNewController extends Controller
{
    function __construct()
    {
    	$this->middleware('permission:sales-report');
    }

    public function salesReportNew(Request $request)
    {
    	$from_date 	= null;
        $to_date    = null;
        $withList   = $request->withList;


        //Total Web Sale
        $totalWebSale = booking::where('created_by', '3');
        if($request->from_date)
        {
          $from_date = $request->from_date;
          $totalWebSale->whereDate('created_at', '>=', $request->from_date);
        }
        if($request->to_date)
        {
          $to_date = $request->to_date;
          $totalWebSale->whereDate('created_at', '<=', $request->to_date);
        }
        if(auth()->user()->hasRole('admin'))
        {
          $totalWEBSaleAmount = $totalWebSale->where('orderstatus', 'approved')->sum('amount');
        }
        else
        {
          $totalWEBSaleAmount = $totalWebSale->where('orderstatus', 'approved')->where('bookings.created_by', auth()->id())->sum('amount');
        }
    	//Total POS Sale
    	$totalPOSSale = BookingPaymentThrough::join('bookings', function ($join) {
            $join->on('booking_payment_throughs.booking_id', '=', 'bookings.id');
        });
    	if($request->from_date)
    	{
    		$from_date = $request->from_date;
    		$totalPOSSale->whereDate('booking_payment_throughs.created_at', '>=', $request->from_date);
    	}
    	if($request->to_date)
    	{
    		$to_date = $request->to_date;
    		$totalPOSSale->whereDate('booking_payment_throughs.created_at', '<=', $request->to_date);
    	}
        if(auth()->user()->hasRole('admin'))
        {
            $totalPOSSaleAmount = $totalPOSSale->sum('booking_payment_throughs.amount');
        }
        else
        {
            $totalPOSSaleAmount = $totalPOSSale->where('bookings.created_by', auth()->id())->sum('booking_payment_throughs.amount');
        }




    	//Total POS Sale by payment method
    	$totalPOSSalePaymentMethod = BookingPaymentThrough::join('bookings', function ($join) {
                $join->on('booking_payment_throughs.booking_id', '=', 'bookings.id');
            })
            ->whereNotIn('booking_payment_throughs.payment_mode', ['Cash','Installment','Cheque']);
    	if($request->from_date)
    	{
    		$from_date = $request->from_date;
    		$totalPOSSalePaymentMethod->whereDate('booking_payment_throughs.created_at', '>=', $request->from_date);
    	}
    	if($request->to_date)
    	{
    		$to_date = $request->to_date;
    		$totalPOSSalePaymentMethod->whereDate('booking_payment_throughs.created_at', '<=', $request->to_date);
    	}
        if(auth()->user()->hasRole('admin'))
        {
            $totalPOSSalePaymentMethodAmount = $totalPOSSalePaymentMethod->sum('booking_payment_throughs.amount');
        }
        else
        {
            $totalPOSSalePaymentMethodAmount = $totalPOSSalePaymentMethod->where('bookings.created_by', auth()->id())->sum('booking_payment_throughs.amount');
        }



    	//Total POS Sale Through Cash
    	$totalPOSSaleCash = BookingPaymentThrough::join('bookings', function ($join) {
                $join->on('booking_payment_throughs.booking_id', '=', 'bookings.id');
            })
            ->where('booking_payment_throughs.payment_mode', 'Cash');
    	if($request->from_date)
    	{
    		$from_date = $request->from_date;
    		$totalPOSSaleCash->whereDate('booking_payment_throughs.created_at', '>=', $request->from_date);
    	}
    	if($request->to_date)
    	{
    		$to_date = $request->to_date;
    		$totalPOSSaleCash->whereDate('booking_payment_throughs.created_at', '<=', $request->to_date);
    	}
        if(auth()->user()->hasRole('admin'))
        {
            $totalPOSSaleCashAmount = $totalPOSSaleCash->sum('booking_payment_throughs.amount');
        }
        else
        {
            $totalPOSSaleCashAmount = $totalPOSSaleCash->where('bookings.created_by', auth()->id())->sum('booking_payment_throughs.amount');
        }

        //Total installment recibe
      	$totalInstallmentRecibe = BookingInstallmentPaid::join('booking_payment_throughs', function ($join) {
              $join->on('booking_installment_paids.booking_payment_through_id', '=', 'booking_payment_throughs.id');
            });
//              $totalINSSaleAmount = 1000 ;
      	if($request->from_date)
      	{
      		$from_date = $request->from_date;
      		$totalInstallmentRecibe->whereDate('booking_installment_paids.created_at', '>=', $request->from_date);
      	}
      	if($request->to_date)
      	{
      		$to_date = $request->to_date;
      		$totalInstallmentRecibe->whereDate('booking_installment_paids.created_at', '<=', $request->to_date);
      	}
          if(auth()->user()->hasRole('admin'))
          {
               $totalINSSaleAmount = $totalInstallmentRecibe->sum('booking_installment_paids.amount');
          }
          else
          {
              $totalINSSaleAmount = $totalInstallmentRecibe->where('booking_installment_paids.created_by', auth()->id())->sum('booking_installment_paids.amount');
          }
        //Date wise list
        $dateList = $this->dateList($from_date, $to_date, $withList);

        return view('reports.sales-report-new', compact('from_date','to_date','totalPOSSaleAmount', 'totalWEBSaleAmount', 'totalPOSSalePaymentMethodAmount', 'totalPOSSaleCashAmount', 'totalINSSaleAmount','dateList','withList'));
    }

    public function typeListAll(Request $request)
    {
        if($request->type=='Modelo') {
            $data = Modelo::select('id', 'nombre as text')->where('activo', '1')->orderBy('nombre');
        } elseif($request->type=='Marca') {
            $data = Marca::select('id', 'nombre as text')->where('activo', '1')->orderBy('nombre');
        } elseif($request->type=='Productos') {
            $data = Producto::select('id', 'nombre as text')->where('disponible', '1')->orderBy('nombre');
        } else {
            $data = Item::select('id', 'nombre as text')->where('activo', '1')->orderBy('nombre');
        }

        if($request->searchTerm!='')
        {
            $records = $data->where('nombre', 'like', '%' . $request->searchTerm. '%');
        }

        $records = $data->get()->toArray();
        echo json_encode($records);
    }

    public function productSalesReport(Request $request)
    {
        $from_date  = null;
        $to_date    = null;
        $withList   = $request->withList;
        $productList        = $request->productList;
        $choose_type        = $request->choose_type;
        $selected_b_or_m    = $request->selected_b_or_m;
        $nombre     = null;

        //Total POS Sale
        $totalPOSSale = bookeditem::select('bookeditems.id','bookeditems.itemqty','bookeditems.return_qty','bookeditems.itemPrice')
            ->join('bookings', function ($join) {
                $join->on('bookeditems.bookingId', '=', 'bookings.id');
            })
            ->join('productos', function ($join) {
                $join->on('bookeditems.itemid', '=', 'productos.id');
            })
            ->where('bookings.created_by', '!=', 3)
            ->whereNotIn('bookings.orderstatus',['Cancel','Return']);

        if($request->from_date)
        {
            $from_date = $request->from_date;
            $totalPOSSale->whereDate('bookeditems.created_at', '>=', $request->from_date);
        }
        if($request->to_date)
        {
            $to_date = $request->to_date;
            $totalPOSSale->whereDate('bookeditems.created_at', '<=', $request->to_date);
        }

        if(!empty($selected_b_or_m))
        {
            if($request->choose_type=='Modelo') {
                $totalPOSSale->join('modelos', function ($join) {
                    $join->on('productos.modelo_id', '=', 'modelos.id');
                })->where('productos.modelo_id', $selected_b_or_m);
                $data = Modelo::select('id', 'nombre')->find($selected_b_or_m);
                $nombre = $data->nombre;
            } elseif($request->choose_type=='Marca') {
                $totalPOSSale->join('marcas', function ($join) {
                    $join->on('productos.marca_id', '=', 'marcas.id');
                })->where('productos.marca_id', $selected_b_or_m);

                $data = Marca::select('id', 'nombre')->find($selected_b_or_m);
                $nombre = $data->nombre;
            } elseif($request->choose_type=='Productos') {
                $totalPOSSale->where('productos.id', $selected_b_or_m);

                $data = Producto::select('id', 'nombre')->find($selected_b_or_m);
                $nombre = $data->nombre;
            } elseif($request->choose_type=='Item') {
                $totalPOSSale->join('items', function ($join) {
                    $join->on('productos.item_id', '=', 'items.id');
                })->where('productos.item_id', $selected_b_or_m);

                $data = Producto::select('id', 'nombre')->where('item_id', $selected_b_or_m)->first();
                $nombre = $data->nombre;
            }
        }

        if(auth()->user()->hasRole('admin'))
        {
            $getPOSRecord = $totalPOSSale->get();
        }
        else
        {
            $getPOSRecord = $totalPOSSale->where('bookings.created_by', auth()->id())->get();
        }
        $totalPOSAmount = 0;
        foreach ($getPOSRecord as $key => $items) {
            $totalPOSAmount = $totalPOSAmount + (($items->itemqty - $items->return_qty) * $items->itemPrice);
        }

        //Total Web Sale
        $totalWEBSale = bookeditem::select('bookeditems.id','bookeditems.itemqty','bookeditems.return_qty','bookeditems.itemPrice')
            ->join('bookings', function ($join) {
                $join->on('bookeditems.bookingId', '=', 'bookings.id');
            })
            ->join('productos', function ($join) {
                $join->on('bookeditems.itemid', '=', 'productos.id');
            })
            ->where('bookings.created_by', 3)
            ->where('bookings.orderstatus','approved');

        if($request->from_date)
        {
            $totalWEBSale->whereDate('bookeditems.created_at', '>=', $request->from_date);
        }
        if($request->to_date)
        {
            $totalWEBSale->whereDate('bookeditems.created_at', '<=', $request->to_date);
        }
        if(!empty($selected_b_or_m))
        {
            if($request->choose_type=='Modelo') {
                $totalWEBSale->join('modelos', function ($join) {
                    $join->on('productos.modelo_id', '=', 'modelos.id');
                })->where('productos.modelo_id', $selected_b_or_m);
            } elseif($request->choose_type=='Marca') {
                $totalWEBSale->join('marcas', function ($join) {
                    $join->on('productos.marca_id', '=', 'marcas.id');
                })->where('productos.marca_id', $selected_b_or_m);
            } elseif($request->choose_type=='Productos') {
                $totalWEBSale->where('productos.id', $selected_b_or_m);
            } elseif($request->choose_type=='Item') {
                $totalWEBSale->join('items', function ($join) {
                    $join->on('productos.item_id', '=', 'items.id');
                })->where('productos.item_id', $selected_b_or_m);
            }
        }

        if(auth()->user()->hasRole('admin'))
        {
            $getWEBRecord = $totalWEBSale->get();
        }
        else
        {
            $getWEBRecord = $totalWEBSale->where('bookings.created_by', auth()->id())->get();
        }
        $totalWEBAmount = 0;
        foreach ($getWEBRecord as $key => $items) {
            $totalWEBAmount = $totalWEBAmount + (($items->itemqty - $items->return_qty) * $items->itemPrice);
        }

        //Date wise list
        $dateList = $this->dateList($from_date, $to_date, $withList);

        return view('reports.product-sales-report', compact('from_date','to_date','totalPOSAmount', 'totalWEBAmount', 'getPOSRecord', 'getWEBRecord','dateList','withList','productList','choose_type','selected_b_or_m','nombre'));
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
