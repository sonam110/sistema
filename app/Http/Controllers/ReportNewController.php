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
    	$to_date 	= null;
    	//Total POS Sale
    	$totalPOSSale = BookingPaymentThrough::query();
    	if($request->from_date)
    	{
    		$from_date = $request->from_date;
    		$totalPOSSale->whereDate('created_at', '>=', $request->from_date);
    	}
    	if($request->to_date)
    	{
    		$to_date = $request->to_date;
    		$totalPOSSale->whereDate('created_at', '<=', $request->to_date);
    	}
    	$totalPOSSaleAmount = $totalPOSSale->sum('amount');


    	//Total Web Sale
    	$totalWebSale = booking::where('created_by', null);
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
    	$totalWEBSaleAmount = $totalWebSale->where('orderstatus', 'approved')->sum('payableAmount');


    	//Total POS Sale by payment method 
    	$totalPOSSalePaymentMethod = BookingPaymentThrough::whereNotIn('payment_mode', ['Cash','Installment','Cheque']);
    	if($request->from_date)
    	{
    		$from_date = $request->from_date;
    		$totalPOSSalePaymentMethod->whereDate('created_at', '>=', $request->from_date);
    	}
    	if($request->to_date)
    	{
    		$to_date = $request->to_date;
    		$totalPOSSalePaymentMethod->whereDate('created_at', '<=', $request->to_date);
    	}
    	$totalPOSSalePaymentMethodAmount = $totalPOSSalePaymentMethod->sum('amount');


    	//Total POS Sale Through Cash
    	$totalPOSSaleCash = BookingPaymentThrough::where('payment_mode', 'Cash');
    	if($request->from_date)
    	{
    		$from_date = $request->from_date;
    		$totalPOSSaleCash->whereDate('created_at', '>=', $request->from_date);
    	}
    	if($request->to_date)
    	{
    		$to_date = $request->to_date;
    		$totalPOSSaleCash->whereDate('created_at', '<=', $request->to_date);
    	}
    	$totalPOSSaleCashAmount = $totalPOSSaleCash->sum('amount');

        return view('reports.sales-report-new', compact('from_date','to_date','totalPOSSaleAmount', 'totalWEBSaleAmount', 'totalPOSSalePaymentMethodAmount', 'totalPOSSaleCashAmount'));
    }
}
