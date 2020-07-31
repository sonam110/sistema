<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Producto;
use App\Supplier;

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
          ->get()->toArray();
        echo json_encode($result);
    }

    public function getSupplierList(Request $request)
    {
        $result = Supplier::select('id','name as text')
          ->where('name', 'like', '%' . $request->searchTerm. '%')
          ->where('status', '1')
          ->get()->toArray();
        echo json_encode($result);
    }
}
