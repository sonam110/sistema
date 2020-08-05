<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Producto;

class ProductController extends Controller
{
	function __construct()
    {
        $this->middleware(['permission:product-list']);
    }

    public function products()
    {
      	return view('products.products');
    }

    public function productsDatatable(Request $request)
    {
        $query = Producto::select('*')->with('categoria','marca','modelo','item','altura','garantia','medida','postura','tecnologia')->get();
        return datatables($query)
	        ->editColumn('stock', function ($query)
	        {
	            return '<span class="badge badge-success">'.$query->stock.'</span>';
	        })
	        ->editColumn('precio', function ($query)
	        {
	            return '<strong>'.$query->precio.'</strong>';
	        })
	        ->editColumn('item', function ($query)
	        {
	            return $query->item->nombre;
	        })
	        ->editColumn('categoria', function ($query)
	        {
	            return $query->categoria->nombre;
	        })
	        ->editColumn('marca', function ($query)
	        {
	            return $query->marca->nombre;
	        })
	        ->editColumn('modelo', function ($query)
	        {
	            return $query->modelo->nombre;
	        })
	        ->editColumn('medida', function ($query)
	        {
	            return $query->medida->nombre;
	        })
	        ->editColumn('altura', function ($query)
	        {
	            return $query->altura->nombre;
	        })
	        ->editColumn('tecnologia', function ($query)
	        {
	            return $query->tecnologia->nombre;
	        })
	        ->editColumn('garantia', function ($query)
	        {
	            return $query->garantia->nombre;
	        })
	        ->editColumn('postura', function ($query)
	        {
	            return $query->postura->nombre;
	        })
	        ->editColumn('activo', function ($query)
	        {
	            if ($query->activo == 0)
	            {
	                $status = '<span class="badge badge-danger">Inactive</span>';
	            }
	            else
	            {
	                $status = '<span class="badge badge-success">Active</span>';
	            }
	            return $status;
	        })
        ->escapeColumns([''])
        ->addIndexColumn()
        ->make(true);
    }

    	
}
