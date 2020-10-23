<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Braghetto\Hokoml\Hokoml;
use App\Producto;
use App\Marca;
use App\Modelo;

class ProductController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:product-list', ['only' => ['products','productsDatatable']]);
        $this->middleware('permission:price-change-ml', ['only' => ['priceChangeMl','getSelectedTypeList','getFilteredProductList','priceChangeMLUpdate']]);
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
                return @$query->item->nombre;
            })
            ->editColumn('categoria', function ($query)
            {
                return @$query->categoria->nombre;
            })
            ->editColumn('marca', function ($query)
            {
                return @$query->marca->nombre;
            })
            ->editColumn('modelo', function ($query)
            {
                return @$query->modelo->nombre;
            })
            ->editColumn('medida', function ($query)
            {
                return @$query->medida->nombre;
            })
            ->editColumn('altura', function ($query)
            {
                return @$query->altura->nombre;
            })
            ->editColumn('tecnologia', function ($query)
            {
                return @$query->tecnologia->nombre;
            })
            ->editColumn('garantia', function ($query)
            {
                return @$query->garantia->nombre;
            })
            ->editColumn('postura', function ($query)
            {
                return @$query->postura->nombre;
            })
            ->editColumn('activo', function ($query)
            {
                if ($query->activo == 0)
                {
                    $status = '<span class="badge badge-danger">Inactivo</span>';
                }
                else
                {
                    $status = '<span class="badge badge-success">Activo</span>';
                }
                return $status;
            })
        ->escapeColumns([''])
        ->addIndexColumn()
        ->make(true);
    }

    public function priceChangeMl()
    {
        return view('products.price-change-ml');
    }

    public function getSelectedTypeList(Request $request)
    {
        if($request->type=='Modelo') {
            $data = Modelo::select('id', 'nombre as text')->where('activo', '1')->orderBy('nombre');
        } else {
            $data = Marca::select('id', 'nombre as text')->where('activo', '1')->orderBy('nombre');
        }
        if($request->searchTerm!='')
        {
            $records = $data->where('nombre', 'like', '%' . $request->searchTerm. '%');
        }
        $records = $data->get()->toArray();
        echo json_encode($records);
    }

    public function productListFilter(Request $request)
    {
        $searchTerm = $request->searchTerm;
        if($request->type=='Modelo') {
            $data = Producto::select('id','nombre','marca_id','modelo_id','stock','precio', 'mla_id')
                ->where('marca_id', $request->searchTerm);
        } else {
            $data = Producto::select('id','nombre','marca_id','modelo_id','stock','precio', 'mla_id')
                ->where('marca_id', $request->searchTerm);
        }
        $records = $data->where('activo', '1')
                ->where('mla_id', '!=', null)
                ->orderBy('mla_id')->get();
        return view('products.product-list-filter', compact('records'));
    }

    public function priceChangeMLUpdate(Request $request)
    {
        $this->validate($request, [
            'selected_b_or_m'   => 'required|numeric',
            'percentage_amount' => 'required',
        ]);

        $searchTerm = $request->selected_b_or_m;
        if($request->choose_type=='Modelo') {
            $data = Producto::select('id','nombre','stock','precio','mla_id')
                ->where('marca_id', $searchTerm);
        } else {
            $data = Producto::select('id','nombre','stock','precio','mla_id')
                ->where('marca_id', $searchTerm);
        }
        $records = $data->where('activo', '1')
                ->where('mla_id', '!=', null)
                ->orderBy('mla_id')->get();
        if($records->count()<1)
        {
            notify()->error('Error, Records not found. Please try again.');
            return redirect()->back()->withInput();
        }

        $mlas = new Hokoml(\Config::get('mercadolibre'), env('ML_ACCESS_TOKEN',''), env('ML_USER_ID',''));

        $notUpdate = '';
        $errorUpdate = '';
        $successUpdate = '';
        foreach ($records as $key => $product) 
        {
            $response = $mlas->product()->find($product->mla_id);
            if($response['http_code']==200)
            {
                //Calculation Start
                $currentPrice = $product->precio;
                if($request->calculation_type=='Amount')
                {
                    $newPrice = $currentPrice + $request->percentage_amount;
                }
                else
                {
                    $newPrice = ($currentPrice + ($currentPrice * $request->percentage_amount)/100);
                }
                $newPrice = round($newPrice, 2);
                //Calculation End

                //if product found
                $variationsArr  = array();
                $variations     = $response['body']['variations'];
                foreach ($variations as $key => $variation) {
                    $variationsArr[] = [
                        'id'    => $variation['id'],
                        'price' => 44010.3,
                        //'available_quantity' => $product->stock
                    ];
                }

                if(is_array($variationsArr) && sizeof($variationsArr)>0)
                {
                    //if variation found then update variation price
                    $response = $mlas->product()->update($product->mla_id, [
                        'variations' => $variationsArr
                    ]);
                }
                else
                {
                    //if variation not found then update main price
                    $response = $mlas->product()->update($product->mla_id, [
                        'price' => $newPrice,
                        //'available_quantity'  => $product->stock
                    ]);
                }
                if($response['http_code']!=200)
                {
                    $errorUpdate.= $product->mla_id .' error is:'.$response['body']['message'].',<br>';
                }
                if($response['http_code']==200)
                {
                    $successUpdate.= $product->mla_id.',<br>';
                }
            }
            else
            {
                $notUpdate.= $product->mla_id .',<br>';
            }
        }
        if(!empty($notUpdate)) {
            \Session::flash('error', 'These products not found in the ML :<br><strong>'. $notUpdate.'</strong>');
        }
        if(!empty($errorUpdate)) {
            \Session::flash('error', 'These products not updated in the ML due to some error. Please check list:<br><strong>'. $notUpdate.'</strong>');
        }
        if(!empty($successUpdate)) {
            \Session::flash('success', 'These products succssfully updated in the ML. Updated product list:<br><strong>'. $successUpdate.'</strong>');
            notify()->success('Realizada!!!, Product price succssfully update in ML.');
        }
        return redirect()->back();
    }
}
