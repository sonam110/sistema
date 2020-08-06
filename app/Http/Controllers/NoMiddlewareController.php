<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Producto;
use App\Supplier;
use App\booking;
use App\SalesOrderReturn;
use DB;

class NoMiddlewareController extends Controller
{
    public function screenlock($currtime,$id,$randnum)
  	{
	    Auth::logout();
	    return view('screen-lock')->with('currtime', $currtime)->with('id', $id)->with('randnum',$randnum);
  	}

  	public function getProductList(Request $request)
    {
      $result = Producto::select('id','nombre as text')
        ->where('nombre', 'like', '%' . $request->searchTerm. '%')
        ->where('activo', 1)
        ->orderBy('nombre', 'ASC')
        ->get()->toArray();
      echo json_encode($result);
    }

    public function getSupplierList(Request $request)
    {
      $result = Supplier::select('id', DB::raw('CONCAT(suppliers.name, \' / \', suppliers.phone) as text'))
        ->where(function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->searchTerm. '%')
                  ->orWhere('phone', 'like', '%' . $request->searchTerm. '%');
        })
        ->where('status', '1')
        ->orderBy('name', 'ASC')
        ->get()->toArray();
      echo json_encode($result);
    }

    public function salesReturnByToken($bokingID, $token)
    {
      $saleInfo = booking::find(base64_decode($bokingID));
      if($saleInfo)
      {
        $returnProduct = SalesOrderReturn::where('return_token', $token)->get();
        return view('sales.sales-return-by-token', compact('saleInfo', 'returnProduct'));
      }
      notify()->error('Oops!!!, something went wrong, please try again.');
      return redirect()->back();
    }
}
