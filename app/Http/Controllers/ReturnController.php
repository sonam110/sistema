<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\booking;

class ReturnController extends Controller
{
    function __construct()
    {
        $this->middleware(['role:admin','permission:direct-sales-return|direct-purchase-return']);
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
}
