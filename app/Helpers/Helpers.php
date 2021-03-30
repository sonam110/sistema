<?php
use App\User;
use App\Supplier;
use App\booking;
use App\bookeditem;
use App\SalesOrderReturn;
use App\PurchaseOrderReturn;
use App\PurchaseOrder;
use App\BookingPaymentThrough;
use App\Producto;
use App\Marca;
use App\Modelo;
use App\Item;

function totalSale()
{
    if(auth()->user()->hasRole('admin'))
    {
        $totalSale = booking::where('created_by','!=',null)->where('deliveryStatus' ,'!=', 'Cancel')->count();
    }
    else
    {
        $totalSale = booking::where('created_by', auth()->id())->where('deliveryStatus' ,'!=', 'Cancel')->count();
    }
    return $totalSale;
}

function revenue()
{
    if(auth()->user()->hasRole('admin'))
    {
        $revenue = booking::where('created_by','!=',null)->where('deliveryStatus' ,'!=', 'Cancel')->sum('amount');
    }
    else
    {
        $revenue = booking::where('created_by', auth()->id())->where('deliveryStatus' ,'!=', 'Cancel')->sum('amount');
    }
    return $revenue;
}

function saleReturn()
{
    if(auth()->user()->hasRole('admin'))
    {
        $saleReturn = SalesOrderReturn::select('return_amount')
        ->join('bookings', function ($join) {
            $join->on('sales_order_returns.booking_id', '=', 'bookings.id');
        })
        ->where('created_by','!=',null)
        ->sum('return_amount');
    }
    else
    {
        $saleReturn = SalesOrderReturn::select('return_amount')
        ->join('bookings', function ($join) {
            $join->on('sales_order_returns.booking_id', '=', 'bookings.id');
        })
        ->where('created_by', auth()->id())
        ->sum('return_amount');
    }
    return $saleReturn;
}

function purchaseReturn()
{
    if(auth()->user()->hasRole('admin'))
    {
        $purchaseReturn = PurchaseOrderReturn::select('return_price')
        ->join('purchase_orders', function ($join) {
            $join->on('purchase_order_returns.purchase_order_id', '=', 'purchase_orders.id');
        })
        ->sum('return_price');
    }
    else
    {
        $purchaseReturn = '0.00';
    }
    return $purchaseReturn;
}

function totalCustomer()
{
    $customers = User::where('userType', '1')->where('status' ,'!=', '2')->count();
    return $customers;
}

function totalSupplier()
{
    $suppliers = Supplier::where('status' ,'!=', '2')->count();
    return $suppliers;
}

function totalPO()
{
    $pos = PurchaseOrder::count();
    return $pos;
}

function getLast30Days()
{
    $today     = new \DateTime();
    $begin     = $today->sub(new \DateInterval('P30D'));
    $end       = new \DateTime();
    $end       = $end->modify('+1 day');
    $interval  = new \DateInterval('P1D');
    $daterange = new \DatePeriod($begin, $interval, $end);
    foreach ($daterange as $date) {
        $dateList[] = '"'.$date->format("Y-m-d").'"';
    }
    $allDates = implode(', ', $dateList);
    return $allDates;
}

function getLast30DaysSaleCounts()
{
    $today     = new \DateTime();
    $begin     = $today->sub(new \DateInterval('P30D'));
    $end       = new \DateTime();
    $end       = $end->modify('+1 day');
    $interval  = new \DateInterval('P1D');
    $daterange = new \DatePeriod($begin, $interval, $end);
    foreach ($daterange as $date) {
        if(auth()->user()->hasRole('admin'))
        {
            $sale[] = booking::whereDate('created_at', $date->format("Y-m-d"))->count();
        }
        else
        {
            $sale[] = booking::whereDate('created_at', $date->format("Y-m-d"))->where('created_by', auth()->id())->count();
        }
    }
    $totalSale = implode(', ', $sale);
    return $totalSale;
}

function getLast30DaysPurcahseCounts()
{
    $today     = new \DateTime();
    $begin     = $today->sub(new \DateInterval('P30D'));
    $end       = new \DateTime();
    $end       = $end->modify('+1 day');
    $interval  = new \DateInterval('P1D');
    $daterange = new \DatePeriod($begin, $interval, $end);
    foreach ($daterange as $date) {
        $purchase[] = PurchaseOrder::where('po_status', '!=', 'Pending')->where('po_date', $date->format("Y-m-d"))->count();
    }
    $totalPurchase = implode(', ', $purchase);
    return $totalPurchase;
}
function getLast30DaysSaleAmount()
{
    $today     = new \DateTime();
    $begin     = $today->sub(new \DateInterval('P30D'));
    $end       = new \DateTime();
    $end       = $end->modify('+1 day');
    $interval  = new \DateInterval('P1D');
    $daterange = new \DatePeriod($begin, $interval, $end);
    foreach ($daterange as $date) {
        if(auth()->user()->hasRole('admin'))
        {
            $sale[] = booking::whereDate('created_at', $date->format("Y-m-d"))->sum('payableAmount');
        }
        else
        {
            $sale[] = booking::whereDate('created_at', $date->format("Y-m-d"))->where('created_by', auth()->id())->sum('payableAmount');
        }
    }
    $totalSale = implode(', ', $sale);
    return $totalSale;
}

function getLast30DaysPurcahseAmount()
{
    $today     = new \DateTime();
    $begin     = $today->sub(new \DateInterval('P30D'));
    $end       = new \DateTime();
    $end       = $end->modify('+1 day');
    $interval  = new \DateInterval('P1D');
    $daterange = new \DatePeriod($begin, $interval, $end);
    foreach ($daterange as $date) {
        $purchase[] = PurchaseOrder::where('po_status', '!=', 'Pending')->where('po_date', $date->format("Y-m-d"))->sum('gross_amount');
    }
    $totalPurchase = implode(', ', $purchase);
    return $totalPurchase;
}

function getLast30DaysSale($record=30)
{
    if(auth()->user()->hasRole('admin'))
    {
        $sales = booking::where('created_by','!=',null)->orderBy('id','DESC')->paginate($record);
    }
    else
    {
        $sales = booking::where('created_by','!=',null)->where('created_by', auth()->id())->orderBy('id','DESC')->paginate($record);
    }
    return $sales;
}

function getWhereRawFromRequest($request)
{
    $w = '';
    if (is_null($request->dateRange) == false) {

        if($request->dateRange == 'day') {
            if ($w != '') {$w = $w . " AND ";}
              $w = $w . "("."DATE(created_at) = '".date('Y-m-d')."')";
        }
        else if($request->dateRange == 'week') {
            $end = date('Y-m-d');
            $start = date('Y-m-d', strtotime('-7 days'));
            if ($w != '') {$w = $w . " AND ";}
              if ($start != '')
              {
              $w = $w . "("."DATE(created_at) >= '".$start."')";
              }
              if (is_null($start) == false && is_null($end) == false) {
              $w = $w . " AND ";
              }
              if ($end != '')
              {
              $w = $w . "("."DATE(created_at) <= '".$end."')";
              }
        }
        else if($request->dateRange == 'month') {
            if ($w != '') {$w = $w . " AND ";}
              $w = $w . "("."MONTH(created_at) = '".date('m')."')";
        }
    }
    return($w);
}

function getSalesReport($date)
{
    //Total POS Sale
    $totalPOSSale = BookingPaymentThrough::join('bookings', function ($join) {
        $join->on('booking_payment_throughs.booking_id', '=', 'bookings.id');
    })
    ->whereDate('booking_payment_throughs.created_at', $date);
    if(auth()->user()->hasRole('admin'))
    {
        $totalPOSSaleAmount = $totalPOSSale->sum('booking_payment_throughs.amount');
    }
    else
    {
        $totalPOSSaleAmount = $totalPOSSale->where('bookings.created_by', auth()->id())->sum('booking_payment_throughs.amount');
    }



    //Total Web Sale
    $totalWebSale = booking::where('created_by',  '3')->whereDate('created_at', $date);
    if(auth()->user()->hasRole('admin'))
    {
        $totalWEBSaleAmount = $totalWebSale->where('orderstatus', 'approved')->sum('payableAmount');
    }
    else
    {
        $totalWEBSaleAmount = $totalWebSale->where('orderstatus', 'approved')->where('bookings.created_by', auth()->id())->sum('payableAmount');
    }



    //Total POS Sale by payment method
    $totalPOSSalePaymentMethod = BookingPaymentThrough::join('bookings', function ($join) {
            $join->on('booking_payment_throughs.booking_id', '=', 'bookings.id');
        })
        ->whereNotIn('booking_payment_throughs.payment_mode', ['Cash','Installment','Cheque'])
        ->whereDate('booking_payment_throughs.created_at', $date);
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
        ->where('booking_payment_throughs.payment_mode', 'Cash')
        ->whereDate('booking_payment_throughs.created_at', $date);
    if(auth()->user()->hasRole('admin'))
    {
        $totalPOSSaleCashAmount = $totalPOSSaleCash->sum('booking_payment_throughs.amount');
    }
    else
    {
        $totalPOSSaleCashAmount = $totalPOSSaleCash->where('bookings.created_by', auth()->id())->sum('booking_payment_throughs.amount');
    }
    $returnData = [
        'totalPOSSaleAmount' => $totalPOSSaleAmount,
        'totalWEBSaleAmount' => $totalWEBSaleAmount,
        'totalPOSSalePaymentMethodAmount' => $totalPOSSalePaymentMethodAmount,
        'totalPOSSaleCashAmount' => $totalPOSSaleCashAmount
    ];
    return $returnData;
}

function getProductSalesReport($date, $choose_type, $selected_b_or_m)
{
    //Total POS Sale
    $totalPOSSale = bookeditem::select('bookeditems.id','bookeditems.itemqty','bookeditems.return_qty','bookeditems.itemPrice')
        ->join('bookings', function ($join) {
            $join->on('bookeditems.bookingId', '=', 'bookings.id');
        })
        ->join('productos', function ($join) {
            $join->on('bookeditems.itemid', '=', 'productos.id');
        })
        ->where('bookings.created_by', '!=', 3)
        ->where('bookings.orderstatus','approved')
        ->whereDate('bookeditems.created_at', $date);
    if(!empty($selected_b_or_m))
    {
        if($choose_type=='Modelo') {
            $totalPOSSale->join('modelos', function ($join) {
                $join->on('productos.modelo_id', '=', 'modelos.id');
            })->where('productos.modelo_id', $selected_b_or_m);
        } elseif($choose_type=='Marca') {
            $totalPOSSale->join('marcas', function ($join) {
                $join->on('productos.marca_id', '=', 'marcas.id');
            })->where('productos.marca_id', $selected_b_or_m);
        } elseif($choose_type=='Productos') {
            $totalPOSSale->where('productos.id', $selected_b_or_m);
        } elseif($choose_type=='Item') {
            $totalPOSSale->join('items', function ($join) {
                $join->on('productos.item_id', '=', 'items.id');
            })->where('productos.item_id', $selected_b_or_m);
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
        ->where('bookings.orderstatus','approved')
        ->whereDate('bookeditems.created_at', $date);
    if(!empty($selected_b_or_m))
    {
        if($choose_type=='Modelo') {
            $totalWEBSale->join('modelos', function ($join) {
                $join->on('productos.modelo_id', '=', 'modelos.id');
            })->where('productos.modelo_id', $selected_b_or_m);
        } elseif($choose_type=='Marca') {
            $totalWEBSale->join('marcas', function ($join) {
                $join->on('productos.marca_id', '=', 'marcas.id');
            })->where('productos.marca_id', $selected_b_or_m);
        } elseif($choose_type=='Productos') {
            $totalWEBSale->where('productos.id', $selected_b_or_m);
        } elseif($choose_type=='Item') {
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

    $returnData = [
        'totalPOSAmount' => $totalPOSAmount,
        'totalWEBAmount' => $totalWEBAmount
    ];
    return $returnData;
}

function getProductList($from_date, $to_date, $choose_type, $selected_b_or_m)
{
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

    $totalSoldProducts = bookeditem::select('bookeditems.*')
        ->join('bookings', function ($join) {
            $join->on('bookeditems.bookingId', '=', 'bookings.id');
        })
        ->join('productos', function ($join) {
            $join->on('bookeditems.itemid', '=', 'productos.id');
        })
        ->where('bookings.orderstatus','approved')
        ->whereDate('bookeditems.created_at', '>=', $earlier)
        ->whereDate('bookeditems.created_at', '<=', $later)
        ->orderBy('id','DESC');
    if(!empty($selected_b_or_m))
    {
        if($choose_type=='Modelo') {
            $totalSoldProducts->join('modelos', function ($join) {
                $join->on('productos.modelo_id', '=', 'modelos.id');
            })->where('productos.modelo_id', $selected_b_or_m);
        } elseif($choose_type=='Marca') {
            $totalSoldProducts->join('marcas', function ($join) {
                $join->on('productos.marca_id', '=', 'marcas.id');
            })->where('productos.marca_id', $selected_b_or_m);
        } elseif($choose_type=='Productos') {
            $totalSoldProducts->where('productos.id', $selected_b_or_m);
        } elseif($choose_type=='Item') {
            $totalSoldProducts->join('items', function ($join) {
                $join->on('productos.item_id', '=', 'items.id');
            })->where('productos.item_id', $selected_b_or_m);
        }
    }

    if(auth()->user()->hasRole('admin'))
    {
        $getRecords = $totalSoldProducts->get();
    }
    else
    {
        $getRecords = $totalSoldProducts->where('bookings.created_by', auth()->id())->get();
    }
    return $getRecords;
}
