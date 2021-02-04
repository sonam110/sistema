<?php
use App\User;
use App\Supplier;
use App\booking;
use App\SalesOrderReturn;
use App\PurchaseOrderReturn;
use App\PurchaseOrder;
use App\BookingPaymentThrough;
use App\Producto;

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
        $revenue = booking::where('created_by','!=',null)->where('deliveryStatus' ,'!=', 'Cancel')->sum('payableAmount');
    }
    else
    {
        $revenue = booking::where('created_by', auth()->id())->where('deliveryStatus' ,'!=', 'Cancel')->sum('payableAmount');
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
