<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\booking;
use App\PurchaseOrder;

class ReturnController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:direct-sales-return', ['only' => ['directSalesReturn']]);
        $this->middleware('permission:direct-purchase-return', ['only' => ['directPurchaseReturn']]);
    }

    public function directSalesReturn()
    {
      	return view('returns.direct-sales-return');
    }

    public function directPurchaseReturn()
    {
      	return view('returns.direct-purchase-return');
    }

    public function getOrderList(Request $request)
    {
    	$result = booking::select('id','tranjectionid as text')
        ->where('tranjectionid', 'like', '%' . $request->searchTerm. '%')
        ->whereIn('deliveryStatus', ['Return','Delivered'])
        ->orderBy('id','ASC')
        ->get()->toArray();
      	echo json_encode($result);
    }

    public function getSalesOrderInformation(Request $request)
    {
    	$saleInfo = booking::find($request->orderId);
      	if($saleInfo)
      	{
      		return view('returns.get-sale-order-information', compact('saleInfo'));
      	}
        return 'not-found'; 
    }

    public function getSalesOrderHistory(Request $request)
    {
      $saleInfo = booking::find($request->orderId);
      return view('returns.get-sale-order-history', compact('saleInfo')); 
    }

    public function getPurchaseOrderList(Request $request)
    {
      $result = PurchaseOrder::select('id','po_no as text')
        ->where('po_no', 'like', '%' . $request->searchTerm. '%')
        ->whereIn('po_status', ['Receiving','Completed'])
        ->get()->toArray();
        echo json_encode($result);
    }

    public function getPurchaseOrderInformation(Request $request)
    {
      $poInfo = PurchaseOrder::find($request->orderId);
        if($poInfo)
        {
          return view('returns.get-purchase-order-information', compact('poInfo'));
        }
        return 'not-found'; 
    }

    public function getPurchaseOrderHistory(Request $request)
    {
      $poInfo = PurchaseOrder::find($request->orderId);
      return view('returns.get-purchase-order-history', compact('poInfo')); 
    }
}
