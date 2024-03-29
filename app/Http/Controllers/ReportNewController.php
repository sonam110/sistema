<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\booking;
use App\bookeditem;
use App\BookeditemGeneric;
use App\PurchaseOrder;
use App\BookingPaymentThrough;
use App\BookingInstallmentPaid;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\salesReport;
use App\Exports\purchaseReport;
use App\Producto;
use App\Marca;
use App\Categoria;
use App\Modelo;
use App\Item;
use DB;

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

        $vecPaids = array();
        $vecPaids['Credit Card']='T. Credito';
        $vecPaids['Debit Card']='T. Debito';
        $vecPaids['Cash']='Efectivo';
        $vecPaids['Transfers']='Transferencia';
        $vecPaids['Installment']='Cta cte';
        $vecPaids['Cheque']='Cheque';
        $vecPaids['']='Sin definir';

         //Total Web Sale
        $totalWebSale = booking::where('created_by', '3')->where('orderstatus', 'approved')->whereNotIn('bookings.deliveryStatus',['Cancel','Return']);
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
          $totalWEBSaleAmount = $totalWebSale->sum('payableAmount');
        }
        else
        {
          $totalWEBSaleAmount = $totalWebSale->where('bookings.created_by', auth()->id())->sum('payableAmount');
        }

        //Total interest Sale
        $totalPosIntSale = booking::where('created_by','!=','3')->where('orderstatus', 'approved')->whereNotIn('bookings.deliveryStatus',['Cancel','Return']);
       $totalWebIntSale = booking::where('created_by', '3')->where('orderstatus', 'approved')->whereNotIn('bookings.deliveryStatus',['Cancel','Return']);
       if($request->from_date)
       {
         $from_date = $request->from_date;
         $totalPosIntSale->whereDate('created_at', '>=', $request->from_date);
         $totalWebIntSale->whereDate('created_at', '>=', $request->from_date);
       }
       if($request->to_date)
       {
         $to_date = $request->to_date;
         $totalPosIntSale->whereDate('created_at', '<=', $request->to_date);
         $totalWebIntSale->whereDate('created_at', '<=', $request->to_date);
       }
       if(auth()->user()->hasRole('admin'))
       {
         $totalPOSInterestSaleAmount = $totalPosIntSale->sum('interestAmount');
         $totalWEBInterestSaleAmount = $totalWebIntSale->sum('interestAmount');
       }
       else
       {
         $totalPOSInterestSaleAmount = $totalPosIntSale->where('bookings.created_by', auth()->id())->sum('interestAmount');
         $totalWEBInterestSaleAmount = $totalWebIntSale->where('bookings.created_by', auth()->id())->sum('interestAmount');
       }

    	//Total POS Sale
    	$totalPOSSale = BookingPaymentThrough::join('bookings', function ($join) {
            $join->on('booking_payment_throughs.booking_id', '=', 'bookings.id');
        })
        ->where('orderstatus', 'approved')
        ->whereNotIn('bookings.deliveryStatus',['Cancel','Return']);
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
    	$totalPOSSalePaids = BookingPaymentThrough::join('bookings', function ($join) {
                $join->on('booking_payment_throughs.booking_id', '=', 'bookings.id');
            })
          ->selectRaw('sum(booking_payment_throughs.amount) as total, booking_payment_throughs.payment_mode')
          ->groupBy('booking_payment_throughs.payment_mode');

    	if($request->from_date)
    	{
    		$from_date = $request->from_date;
    		$totalPOSSalePaids->whereDate('booking_payment_throughs.created_at', '>=', $request->from_date);
    	}
    	if($request->to_date)
    	{
    		$to_date = $request->to_date;
    		$totalPOSSalePaids->whereDate('booking_payment_throughs.created_at', '<=', $request->to_date);
    	}
        if(auth()->user()->hasRole('admin'))
        {
            $totalPOSSalePaids = $totalPOSSalePaids->get();
        }
        else
        {
            $totalPOSSalePaids = $totalPOSSalePaids->where('bookings.created_by', auth()->id())->get();
        }

        $totalInstPaids = BookingInstallmentPaid::join('booking_payment_throughs', function ($join) {
              $join->on('booking_installment_paids.booking_payment_through_id', '=', 'booking_payment_throughs.id');
            })
          ->selectRaw('sum(booking_installment_paids.amount) as total, booking_installment_paids.payment_mode')
          ->groupBy('booking_installment_paids.payment_mode');
          //->where('orderstatus','approved')
          //->get();
          //die($totalInstPaids);

      	if($request->from_date)
      	{
      		$from_date = $request->from_date;
      		$totalInstPaids->whereDate('booking_installment_paids.created_at', '>=', $request->from_date);
      	}
      	if($request->to_date)
      	{
      		$to_date = $request->to_date;
      		$totalInstPaids->whereDate('booking_installment_paids.created_at', '<=', $request->to_date);
      	}
          if(auth()->user()->hasRole('admin'))
          {
               $totalINSSaleAmountPaids = $totalInstPaids->get();
          }
          else
          {
              $totalINSSaleAmountPaids = $totalInstPaids->where('booking_installment_paids.created_by', auth()->id())->get();
          }
        $totalINSSaleIns = 0;
        foreach ($totalINSSaleAmountPaids as $key => $value)
          {
            $totalINSSaleIns += $value['total'];
          }


        // Cobranzas Ventas x vendedor
        $totalBookUsers = Booking::join('booking_payment_throughs', function ($join) {
              $join->on('bookings.id', '=', 'booking_payment_throughs.booking_id');
            })->join('users', function ($join) {
              $join->on('bookings.created_by', '=', 'users.id');
            })
          ->where('orderstatus','approved')
          ->whereNotIn('bookings.deliveryStatus',['Cancel','Return'])
          ->selectRaw('sum(booking_payment_throughs.amount) as total, users.lastname,payment_mode')
          ->groupBy('users.lastname','payment_mode');
          //->get();
          //die($totalInstPaids);

      	if($request->from_date)
      	{
      		$from_date = $request->from_date;
      		$totalBookUsers->whereDate('booking_payment_throughs.created_at', '>=', $request->from_date);
      	}
      	if($request->to_date)
      	{
      		$to_date = $request->to_date;
      		$totalBookUsers->whereDate('booking_payment_throughs.created_at', '<=', $request->to_date);
      	}
          if(auth()->user()->hasRole('admin'))
          {
               $totalBookUsers2 = $totalBookUsers->get();
          }
          else
          {
              $totalBookUsers2 = $totalBookUsers->where('bookings.created_by', auth()->id())->get();
          }
        $totalBookUsers3 = array();
        $i=1;
        foreach ($totalBookUsers2 as $key => $value)
          {
            $value['add']='';
            $totalBookUsers3[$value['lastname']][$i++] = $value;
            if (isset($totalBookUsers3[$value['lastname']][0]))
              {
              $totalBookUsers3[$value['lastname']][0]=$totalBookUsers3[$value['lastname']][0]+$value['total'];
              }
              else
              {
              $totalBookUsers3[$value['lastname']][0]=$value['total'];
              }
          }

        // Cobros de ventas  x vendedor
        unset($totalBookUsers);
        unset($totalBookUsers2);
        $totalBookUsers = BookingInstallmentPaid::join('booking_payment_throughs', function ($join) {
              $join->on('booking_installment_paids.booking_payment_through_id', '=', 'booking_payment_throughs.id');
            })->join('bookings', function ($join) {
              $join->on('bookings.id', '=', 'booking_payment_throughs.booking_id');
            })->join('users', function ($join) {
              $join->on('bookings.created_by', '=', 'users.id');
            })
          ->where('orderstatus','approved')
          ->whereNotIn('bookings.deliveryStatus',['Cancel','Return'])
          ->selectRaw('sum(booking_installment_paids.amount) as total, users.lastname,booking_installment_paids.payment_mode')
          ->groupBy('users.lastname','booking_installment_paids.payment_mode');
          //->get();
          //die($totalInstPaids);

      	if($request->from_date)
      	{
      		$from_date = $request->from_date;
      		$totalBookUsers->whereDate('booking_installment_paids.created_at', '>=', $request->from_date);
      	}
      	if($request->to_date)
      	{
      		$to_date = $request->to_date;
      		$totalBookUsers->whereDate('booking_installment_paids.created_at', '<=', $request->to_date);
      	}
          if(auth()->user()->hasRole('admin'))
          {
               $totalBookUsers2 = $totalBookUsers->get();
          }
          else
          {
              $totalBookUsers2 = $totalBookUsers->where('bookings.created_by', auth()->id())->get();
          }
        foreach ($totalBookUsers2 as $key => $value)
          {
            $value['add']='(P)';
            $totalBookUsers3[$value['lastname']][$i++] = $value;
            if (isset($totalBookUsers3[$value['lastname']][0]))
              {
              $totalBookUsers3[$value['lastname']][0]=$totalBookUsers3[$value['lastname']][0]+$value['total'];
              }
              else
              {
              $totalBookUsers3[$value['lastname']][0]=$value['total'];
              }
          }

          // totales de ventas  x vendedor
          unset($totalBookUsers);
          unset($totalBookUsers2);
          $totalBookUsers = Booking::join('users', function ($join) {
                $join->on('bookings.created_by', '=', 'users.id');
              })
            ->where('orderstatus','approved')
            ->whereNotIn('bookings.deliveryStatus',['Cancel','Return'])
            ->selectRaw('sum(bookings.payableAmount) as total, users.lastname')
            ->groupBy('users.lastname');
            //->get();
            //die($totalInstPaids);

        	if($request->from_date)
        	{
        		$from_date = $request->from_date;
        		$totalBookUsers->whereDate('bookings.created_at', '>=', $request->from_date);
        	}
        	if($request->to_date)
        	{
        		$to_date = $request->to_date;
        		$totalBookUsers->whereDate('bookings.created_at', '<=', $request->to_date);
        	}
            if(auth()->user()->hasRole('admin'))
            {
                 $totalBookUsers2 = $totalBookUsers->get();
            }
            else
            {
                $totalBookUsers2 = $totalBookUsers->where('bookings.created_by', auth()->id())->get();
            }
          foreach ($totalBookUsers2 as $key => $value)
            {
              $value['add']='';
              $totalBookUsers4[$value['lastname']][$i++] = $value;
              if (isset($totalBookUsers4[$value['lastname']][0]))
                {
                $totalBookUsers4[$value['lastname']][0]=$totalBookUsers4[$value['lastname']][0]+$value['total'];
                }
                else
                {
                $totalBookUsers4[$value['lastname']][0]=$value['total'];
                }
            }
        //Date wise list
        $dateList = $this->dateList($from_date, $to_date, $withList);

        return view('reports.sales-report-new', compact('from_date','to_date','totalPOSSaleAmount', 'totalWEBSaleAmount', 'totalPOSSalePaymentMethodAmount',
           'totalPOSSalePaids', 'totalINSSaleAmountPaids','totalINSSaleIns','dateList','withList','vecPaids','totalBookUsers3','totalBookUsers4','totalPOSInterestSaleAmount','totalWEBInterestSaleAmount'));
    }

    public function typeListAll(Request $request)
    {
        if($request->type=='Modelo') {
            $data = Modelo::select('id', 'nombre as text')->where('activo', '1')->orderBy('nombre');
        } elseif($request->type=='Marca') {
            $data = Marca::select('id', 'nombre as text')->where('activo', '1')->orderBy('nombre');
        } elseif($request->type=='Productos') {
            $data = Producto::select('id', 'nombre as text')->with('marca','item','modelo','categoria')->where('disponible', '1')->orderBy('nombre');
          } elseif($request->type=='Categoria') {
              $data = Categoria::select('id', 'nombre as text')->where('activo', '1')->orderBy('nombre');
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
        $from_date  = $request->from_date;
        $to_date  = $request->to_date;
        $diff = 6;
        $today      = new \DateTime();
        $earlier    = $today->sub(new \DateInterval('P'.$diff.'D'));
        $later      = new \DateTime(date('Y-m-d'));
        if(!empty($from_date) && !empty($to_date))
        {
            $earlier    = new \DateTime($from_date);
            $later      = new \DateTime($to_date);
        } elseif(!empty($from_date) && empty($to_date)) {
            $earlier    = new \DateTime($from_date);
            $later      = new \DateTime(date('Y-m-d'));
        }

       
        $withList   = $request->withList;
        $productList        = $request->productList;
        $choose_type        = $request->choose_type;
        $selected_b_or_m    = $request->selected_b_or_m;
        $nombre     = null;

        //Total POS Sale
        $totalPOSSale = bookeditem::select('bookeditems.id','bookeditems.itemqty','bookeditems.return_qty','bookeditems.itemPrice','bookings.created_by')
            ->join('bookings', function ($join) {
                $join->on('bookeditems.bookingId', '=', 'bookings.id');
            })
            ->join('productos', function ($join) {
                $join->on('bookeditems.itemid', '=', 'productos.id');
            })
            ->where('bookings.created_by', '!=', 3)
            ->whereNotIn('bookings.deliveryStatus',['Cancel','Return']);

            //Total POS VentaEspecial
            $totalPOSVentaEspecial = BookeditemGeneric::select('*')
                ->join('bookings', function ($join) {
                    $join->on('bookeditem_generics.booking_id', '=', 'bookings.id');
                })
                ->where('bookings.orderstatus','approved')
                ->whereNotIn('bookings.deliveryStatus',['Cancel','Return']);


        
           
            $totalPOSSale->whereDate('bookeditems.created_at', '>=', $earlier);
            $totalPOSVentaEspecial->whereDate('bookeditem_generics.created_at', '>=', $earlier);
        
            
            $totalPOSSale->whereDate('bookeditems.created_at', '<=', $later);
            $totalPOSVentaEspecial->whereDate('bookeditem_generics.created_at', '<=', $later);
        

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

                $data = Producto::select('id', 'nombre')->with('marca','item','modelo')->find($selected_b_or_m);
                $nombre = $data->nombre;
              } elseif($request->choose_type=='Categoria') {
                  $totalPOSSale->join('categorias', function ($join) {
                      $join->on('productos.categoria_id', '=', 'categorias.id');
                  })->where('productos.marca_id', $selected_b_or_m);

                  $data = Categoria::select('id', 'nombre')->find($selected_b_or_m);
                  $nombre = $data->nombre;
            } elseif($request->choose_type=='Item') {
                $totalPOSSale->join('items', function ($join) {
                    $join->on('productos.item_id', '=', 'items.id');
                })->where('productos.item_id', $selected_b_or_m);

                $data = Producto::select('id', 'nombre')->with('marca','item','modelo')->where('item_id', $selected_b_or_m)->first();
                $nombre = $data->nombre;
            }
        }

        if(auth()->user()->hasRole('admin'))
        {
            $getPOSRecord = $totalPOSSale->get();
            $getPOSRegistro = $totalPOSVentaEspecial->get();
            $getEmployeeSales = $totalPOSSale->groupBy('bookings.created_by')->get();
        }
        else
        {
            $getPOSRecord = $totalPOSSale->where('bookings.created_by', auth()->id())->get();
            $getPOSRegistro = $totalPOSVentaEspecial->where('bookings.created_by', auth()->id())->get();
            $getEmployeeSales =[];
        }
        $totalPOSAmount = 0;
        $totalPOSCount = 0;
        foreach ($getPOSRegistro as $nkey => $nitems) {
          $totalPOSAmount = $totalPOSAmount + (($nitems->itemqty - $nitems->return_qty) * $nitems->itemPrice);
          $totalPOSCount = $totalPOSCount + ($nitems->itemqty - $nitems->return_qty) ;
        }
        foreach ($getPOSRecord as $key => $items) {
          $totalPOSAmount = $totalPOSAmount + (($items->itemqty - $items->return_qty) * $items->itemPrice);
          $totalPOSCount = $totalPOSCount + ($items->itemqty - $items->return_qty) ;
        }

         //dd($getEmployeeSales);
        // die();

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
          $totalWEBAmount = $totalWebSale->where('orderstatus', 'approved')->sum('payableAmount');
        }
        else
        {
          $totalWEBAmount = $totalWebSale->where('orderstatus', 'approved')->where('bookings.created_by', auth()->id())->sum('payableAmount');
        }

        //Date wise list
        $dateList = $this->dateList($from_date, $to_date, $withList);

        return view('reports.product-sales-report', compact('from_date','to_date','totalPOSAmount','totalPOSCount', 'totalWEBAmount', 'getPOSRecord','dateList','withList','productList','choose_type','selected_b_or_m','nombre','getEmployeeSales'));
    }
    public function productStockReport(Request $request)
    {

        $productList        = $request->productList;
        $choose_type        = $request->choose_type;
        $selected_b_or_m    = $request->selected_b_or_m;
        $nombre     = null;

        $query = Producto::select('*')->with('marca','modelo');
        $totalSum = $query->sum(DB::raw('precio *stock'));
        if(!empty($selected_b_or_m))
        {
            if($request->choose_type=='Modelo') {
                $query->where('modelo_id', $selected_b_or_m);
                $data = Modelo::select('id', 'nombre')->find($selected_b_or_m);
                $nombre = $data->nombre;
            } elseif($request->choose_type=='Marca') {
                $query->where('marca_id', $selected_b_or_m);
                $data = Marca::select('id', 'nombre')->find($selected_b_or_m);
                $nombre = $data->nombre;

            }
        }

        $totalProducts = $query->get();
        $totalStockSum = $query->sum(DB::raw('precio *stock'));
        //Date wise list


        return view('reports.product-stock-report', compact('totalProducts', 'totalStockSum','totalSum','choose_type','selected_b_or_m','nombre','productList'));
    }
    public function productStockReportFilter(Request $request)
    {

        $productList        = $request->productList;
        $choose_type        = $request->choose_type;
        $selected_b_or_m    = $request->selected_b_or_m;
        $nombre     = null;

        $query = Producto::select('*')->with('marca','modelo');
        $totalSum = $query->sum(DB::raw('precio *stock'));
        if(!empty($selected_b_or_m))
        {
            if($request->choose_type=='Modelo') {
                $query->where('modelo_id', $selected_b_or_m);
                $data = Modelo::select('id', 'nombre')->find($selected_b_or_m);
                $nombre = $data->nombre;
            } elseif($request->choose_type=='Marca') {
                $query->where('marca_id', $selected_b_or_m);
                $data = Marca::select('id', 'nombre')->find($selected_b_or_m);
                $nombre = $data->nombre;

            }
        }

        $totalProducts = $query->get();
        $totalStockSum = $query->sum(DB::raw('precio *stock'));
        //Date wise list


        return view('reports.product-stock-report-filter', compact('totalProducts', 'totalStockSum','totalSum','choose_type','selected_b_or_m','nombre','productList'));
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
