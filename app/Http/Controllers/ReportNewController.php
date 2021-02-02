<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\booking;
use App\PurchaseOrder;
use App\BookingPaymentThrough;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\salesReport;
use App\Exports\purchaseReport;
use App\Producto;

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
    	


    	//Total Web Sale
    	$totalWebSale = booking::where(function($query) {
                $query->where('created_by', null)
                      ->orWhere('created_by', '3');
            });
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
        $dateList = array();
        if($withList=='yes' || (empty($from_date) && empty($to_date)))
        {
            $diff = 6;
            //List Date Wise
            if(!empty($request->from_date) && !empty($request->to_date))
            {
                $earlier = new \DateTime($request->from_date);
                $later = new \DateTime($request->to_date);
                $diff = $later->diff($earlier)->format("%a");
            } elseif(!empty($request->from_date) && empty($request->to_date)) {
                $earlier = new \DateTime($request->from_date);
                $later = new \DateTime(date('Y-m-d'));
                $diff = $later->diff($earlier)->format("%a");
            }
            $today     = new \DateTime();
            $begin     = $today->sub(new \DateInterval('P'.$diff.'D'));
            $end       = new \DateTime();
            $end       = $end->modify('+1 day');
            $interval  = new \DateInterval('P1D');
            $daterange = new \DatePeriod($begin, $interval, $end);
            foreach ($daterange as $date) {
                $dateList[] = $date->format("Y-m-d");
            }
        }
    
        return view('reports.sales-report-new', compact('from_date','to_date','totalPOSSaleAmount', 'totalWEBSaleAmount', 'totalPOSSalePaymentMethodAmount', 'totalPOSSaleCashAmount','dateList','withList'));
    }
}
