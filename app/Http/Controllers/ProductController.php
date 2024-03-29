<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Braghetto\Hokoml\Hokoml;
use App\Producto;
use App\Categoria;
use App\Marca;
use App\Modelo;
use App\Item;
use App\Exports\ProductExport;
use Excel;
use Session;
class ProductController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:product-list', ['only' => ['products','productsDatatable']]);
        $this->middleware('permission:price-change-ml', ['only' => ['priceChangeMl','getSelectedTypeList','getFilteredProductList','priceChangeMLUpdate','dimensionChangeMl']]);
    }

    public function products()
    {
        return view('products.products');
    }

    public function productsDatatable(Request $request)
    {
        $query = Producto::select('*')->with('categoria','marca','modelo','item','altura','garantia','medida','postura','tecnologia')->where('disponible',1);
        return datatables($query)
            ->editColumn('stock', function ($query)
            {
                return '<span class="badge badge-success">'.$query->stock.'</span>';
            })
            ->editColumn('precio', function ($query)
            {
                return '<strong>'.$query->precio.'</strong>';
            })
            ->addColumn('item', function ($query)
            {
                return @$query->item->nombre;
            })
            ->addColumn('categoria', function ($query)
            {
                return @$query->categoria->nombre;
            })
            ->addColumn('marca', function ($query)
            {
                return @$query->marca->nombre;
            })
            ->addColumn('modelo', function ($query)
            {
                return @$query->modelo->nombre;
            })
            ->addColumn('medida', function ($query)
            {
                return @$query->medida->nombre;
            })
            ->addColumn('altura', function ($query)
            {
                return @$query->altura->nombre;
            })
            ->addColumn('tecnologia', function ($query)
            {
                return @$query->tecnologia->nombre;
            })
            ->addColumn('garantia', function ($query)
            {
                return @$query->garantia->nombre;
            })
            ->addColumn('postura', function ($query)
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
        } elseif($request->type=='Marca') {
            $data = Marca::select('id', 'nombre as text')->where('activo', '1')->orderBy('nombre');
        } elseif($request->type=='Productos') {
            $data = Producto::select('id', 'nombre as text')->where('disponible', '1')->orderBy('nombre');
        } elseif($request->type=='MlaId') {
            $data = Producto::select('id', 'mla_id as text')->where('disponible', '1')->orderBy('mla_id');
        } else {
            $data = Item::select('id', 'nombre as text')->where('activo', '1')->orderBy('nombre');
        }

        if($request->type=='MlaId')
        {
            if($request->searchTerm!='')
            {
                $records = $data->where('mla_id', 'like', '%' . $request->searchTerm. '%');
            }
        }
        else
        {
            if($request->searchTerm!='')
            {
                $records = $data->where('nombre', 'like', '%' . $request->searchTerm. '%');
            }
        }

        $records = $data->get()->toArray();
        echo json_encode($records);
    }

    public function productListFilter(Request $request)
    {
        $searchTerm = $request->searchTerm;
        if($request->type=='Modelo') {
            $data = Producto::with('marca','modelo')->select('id','nombre','marca_id','item_id','modelo_id','stock','precio', 'mla_id')
                ->where('modelo_id', $request->searchTerm);
        } elseif($request->type=='Marca') {
            $data = Producto::with('marca','modelo')->select('id','nombre','marca_id','item_id','modelo_id','stock','precio', 'mla_id')
                ->where('marca_id', $request->searchTerm);
        } elseif($request->type=='Productos') {
            $data = Producto::select('id','nombre','marca_id','item_id','modelo_id','stock','precio', 'mla_id')
                ->where('id', $request->searchTerm);
        } elseif($request->type=='MlaId') {
            $data = Producto::select('id','nombre','marca_id','item_id','modelo_id','stock','precio', 'mla_id')
                ->where('id', $request->searchTerm);
        } else {
            $data = Producto::with('marca','modelo')->select('id','nombre','marca_id','item_id','modelo_id','stock','precio', 'mla_id')
                ->where('item_id', $request->searchTerm);
        }
        $records = $data->where('disponible', '1')
                ->where('mla_id', '!=', null)
                ->orderBy('mla_id')->with('marca','item','modelo')->get();
        return view('products.product-list-filter', compact('records'));
    }

    public function priceChangeMLUpdate(Request $request)
    {
        $this->validate($request, [
            'selected_b_or_m'   => 'required|numeric',
            'percentage_amount' => 'required',
        ]);

        if($request->selected_b_or_m =='Both'){
            $this->validate($request, [
                'fixed_amount' => 'required',
            ]);
        }

        $searchTerm = $request->selected_b_or_m;
        if($request->choose_type=='Modelo') {
            $data = Producto::select('id','categoria_id','item_id','marca_id','modelo_id','medida_id','altura_id','stock','precio','publicable','mla_id')
                ->where('modelo_id', $searchTerm);
        } elseif($request->choose_type=='Marca') {
            $data = Producto::select('id','categoria_id','item_id','marca_id','modelo_id','medida_id','altura_id','stock','precio','publicable','mla_id')
                ->where('marca_id', $searchTerm);
        } elseif($request->choose_type=='Productos') {
            $data = Producto::select('id','categoria_id','item_id','marca_id','modelo_id','medida_id','altura_id','stock','precio','publicable','mla_id')
                ->where('id', $searchTerm);
        } elseif($request->choose_type=='MlaId') {
            $data = Producto::select('id','categoria_id','item_id','marca_id','modelo_id','medida_id','altura_id','stock','precio','publicable','mla_id')
                ->where('id', $searchTerm);
        } else {
            $data = Producto::select('id','categoria_id','item_id','marca_id','modelo_id','medida_id','altura_id','stock','precio','publicable','mla_id')
                ->where('item_id', $searchTerm);
        }
        $records = $data->where('disponible', '1')
                ->where('mla_id', '!=', null)
                ->orderBy('mla_id')->with('marca', 'modelo')->get();
        if($records->count()<1)
        {
            notify()->error('Error, Records not found. Please try again.');
            return redirect()->back()->withInput();
        }

        $mlas = new Hokoml(\Config::get('mercadolibre'), env('ML_ACCESS_TOKEN',''), env('ML_USER_ID',''));

        $notUpdate = '';
        $errorUpdate = '';
        $successUpdate = '';
        $retVal = '';
        foreach ($records as $key => $product)
        {
            $response = $mlas->product()->find($product->mla_id);
            // $app = $mlas->app();
            // $resp = $app->categoryAttributes('MLA1611');
            // dd($resp);
            $retVal = ($product->stock > 0) ? 'active' : 'paused' ;
            if($response['http_code']==200)
            {
                //Calculation Start
                // dd($response);
                $currentPrice = $product->precio;
                if($request->calculation_type=='Amount')
                {
                    $newPrice = round($currentPrice  + $request->percentage_amount,0);
                }
                elseif($request->calculation_type=='Percentage')
                {
                    $newPrice = round($currentPrice + ($currentPrice * $request->percentage_amount)/100,0);
                }
                else
                {
                    $newPrice = ($currentPrice +  $request->fixed_amount + ($currentPrice * $request->percentage_amount)/100);
                }
                $newPrice = round($newPrice, 2);
                //Calculation End
                $newTitle = str_replace(',','',$product->categoria->descripcion.' '.$product->marca->nombre.' '.$product->item->nombre.' '.$product->medida->width.'x'.$product->medida->long.' cm. '.$product->medida->alias);
                $tagArr = array('</p>','<br />','</li>','</ul>' ) ;
                $charArr = array(PHP_EOL,PHP_EOL,'- ','- ' );
//                 $descripcion = strip_tags(str_replace($tagArr,$charArr, $product->modelo->descripcion)) ;
//                 $newDescription = $descripcion.' '.
// '
// ENVIOS A DOMICILIO
// Las Entregas se realizan en domicilio dentro de los 7 dias (hábiles)
// Tambien puede retirar de nuestro Negocio en el barrio de Barracas
// (a 5 min. de Puerto Madero) siempre que la medida esté en stock (solicite confirmación).
// Consulte costos de envío.
// EXPOSICION y VENTAS CON MAS DE 100 MODELOS
// En nuestro Showroom contamos con todos los modelos de las mejores marcas como, Simmons, La Cardeuse ,Suavestar , Cannon , Springwall , Delpa , Topacio ,
// Belmo , NaturalFoam , Gani , Litoral, Sensorial etc. para que puedan probar y elegir sin apremios y con el mejor asesoramiento cual es el que mejor adapta a su necesidad.
// HORARIO DE ATENCION
// NUESTRA ZONA
// Estamos en Barracas a 5 minutos de Puerto Madero ';
//
//                 dd($newDescription);  //if product found
                $variationsArr  = array();
                $manifacturArr  = array();
                $variations     = $response['body']['variations'];
                $nStock= ($product->stock < 1) ? 0 : $product->stock;
                foreach ($variations as $key => $variation) {
                    if($nStock && $product->publicable==1) // pausar  si la categoria es sabanas o almohadas
                    {
                      $manifacturArr[] = [
                        'id'          => 'MANUFACTURING_TIME',
                        'value_name'  => '21 días'
                      ];
                        $variationsArr[] = [
                            'id'        => $variation['id'],
                            //  'price'     => $newPrice,
                            //  'title'     => $newTitle,
                            'available_quantity'=> 200,
                            'status'=> 'active'
                        ];
                    }
                    else
                    {
                      $manifacturArr[] = [
                          'id'          => 'MANUFACTURING_TIME',
                          'value_name'  => null
                      ];
                      $variationsArr[] = [
                            'id'    => $variation['id'],
                            //  'price' => $newPrice,
                            //  'title' => $newTitle,
                          'available_quantity' => $nStock
                        ];
                    }
                }
                if(is_array($variationsArr) && sizeof($variationsArr)>0)
                {
                    //if variation found then update variation price
                        $response = $mlas->product()->update($product->mla_id, [
                            //  'title' => $newTitle,
                            'variations' => $variationsArr,
                            'sale_terms' => $manifacturArr
                        ]);
                }
                  else
                {
                    //if variation not found then update main price
                    if($nStock==0 && $product->publicable==1) // pausar  si la categoria es sabanas o almohadas
                    {
                      // $manifacturArr[]=[ 'id'  => 'MANUFACTURING_TIME','value_name'  => '21 días'];
                        $response = $mlas->product()->update($product->mla_id, [
                              //  'price'             => $newPrice,
                              //  'title'             => $newTitle,
                            'available_quantity'  => 200,
                            'sale_terms'        => $manifacturArr
                        ]);
                    }
                    else
                    {
                      // $manifacturArr[]=[ 'id'  => 'MANUFACTURING_TIME','value_name'  => null];
                        $response = $mlas->product()->update($product->mla_id, [
                            //  'price' => $newPrice,
                            //  'title' => $newTitle,
                            'available_quantity'  => $nStock ,
                            'sale_terms'  => $manifacturArr
                        ]);
                    }
                }
                // $response = $mlas->product()->update($product->mla_id, [ 'description' => ['plain_text' => $newDescription ] ]);
                $variationsArr  = array();
                $manifacturArr = array();
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
            \Session::flash('error', 'Estos productos no se encontraron en ML :<br><strong>'. $notUpdate.'</strong>');
        }
        if(!empty($errorUpdate)) {
            \Session::flash('error', 'Lista de Productos con errrores que NO se actualizaron en ML:<br><strong>'. $errorUpdate.'</strong>');
        }
        if(!empty($successUpdate)) {
            \Session::flash('success', 'Lista de Productos actualizados exitosamente en ML:<br><strong>'. $successUpdate.'</strong>');
            notify()->success('Realizada!!!, Product price succssfully update in ML.');
        }
        return redirect()->back();
    }

    ///Dimension module
    public function dimensionChangeMl()
    {
        return view('products.dimension-change-ml');
    }

    public function getSelectedTypeListDimension(Request $request)
    {
        if($request->type=='Modelo') {
            $data = Modelo::select('id', 'nombre as text')->where('activo', '1')->orderBy('nombre');
        } elseif($request->type=='Marca') {
            $data = Marca::select('id', 'nombre as text')->where('activo', '1')->orderBy('nombre');
        } elseif($request->type=='Productos') {
            $data = Producto::select('id', 'nombre as text')->where('disponible', '1')->orderBy('nombre');
        } elseif($request->type=='MlaId') {
            $data = Producto::select('id', 'mla_id as text')->where('disponible', '1')->orderBy('mla_id');
        } else {
            $data = Item::select('id', 'nombre as text')->where('activo', '1')->orderBy('nombre');
        }

        if($request->type=='MlaId')
        {
            if($request->searchTerm!='')
            {
                $records = $data->where('mla_id', 'like', '%' . $request->searchTerm. '%');
            }
        }
        else
        {
            if($request->searchTerm!='')
            {
                $records = $data->where('nombre', 'like', '%' . $request->searchTerm. '%');
            }
        }

        $records = $data->get()->toArray();
        echo json_encode($records);
    }

    public function productListFilterDimension(Request $request)
    {
        $searchTerm = $request->searchTerm;
        if($request->type=='Modelo') {
            $data = Producto::select('id','nombre','marca_id','item_id','modelo_id','stock','precio', 'mla_id', 'medida_id', 'altura_id', 'weight')
                ->where('modelo_id', $request->searchTerm);
        } elseif($request->type=='Marca') {
            $data = Producto::select('id','nombre','marca_id','item_id','modelo_id','stock','precio', 'mla_id', 'medida_id', 'altura_id', 'weight')
                ->where('publicable',1)->where('marca_id', $request->searchTerm);
        } elseif($request->type=='Productos') {
            $data = Producto::select('id','nombre','marca_id','item_id','modelo_id','stock','precio', 'mla_id', 'medida_id', 'altura_id', 'weight')
                ->where('id', $request->searchTerm);
        } elseif($request->type=='MlaId') {
            $data = Producto::select('id','nombre','marca_id','item_id','modelo_id','stock','precio', 'mla_id', 'medida_id', 'altura_id', 'weight')
                ->where('id', $request->searchTerm);
        } else {
            $data = Producto::select('id','nombre','marca_id','item_id','modelo_id','stock','precio', 'mla_id', 'medida_id', 'altura_id', 'weight')
                ->where('item_id', $request->searchTerm);
        }
        $records = $data->where('disponible', '1')
                ->where('mla_id', '!=', null)
                ->orderBy('mla_id')->get();
        return view('products.product-list-filter-dimension', compact('records'));
    }

    public function DimensionChangeMLUpdate(Request $request)
    {
        $this->validate($request, [
            'selected_b_or_m'   => 'required|numeric',
            "length"    => "required|array",
            "length.*"  => "required|string",
            "width"     => "required|array",
            "width.*"   => "required|string",
            "height"    => "required|array",
            "height.*"  => "required|string",
            "weight"    => "required|array",
            "weight.*"  => "required|string",
        ]);
        if(is_array($request->mla_id) && sizeof($request->mla_id)<1)
        {
            notify()->error('Error, Records not found. Please try again.');
            return redirect()->back()->withInput();
        }

        $mlas = new Hokoml(\Config::get('mercadolibre'), env('ML_ACCESS_TOKEN',''), env('ML_USER_ID',''));

        $notUpdate = '';
        $errorUpdate = '';
        $successUpdate = '';
        foreach ($request->mla_id as $key => $mlaID)
        {
            if(!empty($mlaID))
            {
                $response = $mlas->product()->find($mlaID);
                if($response['http_code']==200)
                {
                  $variationsArr  = array();
                  $variations     = $response['body']['variations'];

                    $dimensions = $request->length[$key].'x'.$request->width[$key].'x'.$request->height[$key];

                    $shippingArr = [
                        // 'mode' => 'me1',
                        'dimensions'    => str_replace(' ', '', $dimensions.','.$request->weight[$key]),
                        'local_pick_up' => true,
                        'free_shipping' => false,
                        'logistic_type' => 'default'
                    ];
                    $response = $mlas->product()->update($mlaID, [
                        'shipping' => $shippingArr
                    ]);
                    $shippingArr  = array();

                    if($response['http_code']!=200)
                    {
                        $errorUpdate.= $mlaID .' error is:'.$response['body']['message'].',<br>';
                    }
                    if($response['http_code']==200)
                    {
                        //$mode = $response['body']['shipping']['mode'];
                        //$this->updateShippingMode($mlaID, $mode);
                        $successUpdate.= $mlaID.',<br>';
                    }
                }
                else
                {
                    $notUpdate.= $mlaID .',<br>';
                }
            }
            foreach ($variations as $key => $variation) {
              $variationsArr[] = [
                'id'        => $variation['id'],
                'shipping'  => $shippingArr

              ];
            }
            if(is_array($variationsArr) && sizeof($variationsArr)>0)
            {                         $response = $mlas->product()->update($mlaID, [
                                        'variations' => $variationsArr,
                                    ]);
            }

        }
        if(!empty($notUpdate)) {
            \Session::flash('error', 'Estos productos no se encontraron en ML :<br><strong>'. $notUpdate.'</strong>');
        }
        if(!empty($errorUpdate)) {
            \Session::flash('error', 'Lista de Productos con errrores que NO se actualizaron en ML:<br><strong>'. $errorUpdate.'</strong>');
        }
        if(!empty($successUpdate)) {
            \Session::flash('success', 'Lista de Productos actualizados exitosamente en ML:<br><strong>'. $successUpdate.'</strong>');
            notify()->success('Realizada!!!, Product price succssfully update in ML.');
        }
        return redirect()->back();
    }

    public function mlListShippingModeMe1()
    {
        return view('products.ml-list-shipping-mode-me1');
    }

    public function getSelectedTypeListShippingInfo(Request $request)
    {
        if($request->type=='Modelo') {
            $data = Modelo::select('id', 'nombre as text')->where('activo', '1')->orderBy('nombre');
        } elseif($request->type=='Marca') {
            $data = Marca::select('id', 'nombre as text')->where('activo', '1')->orderBy('nombre');
        } elseif($request->type=='Productos') {
            $data = Producto::select('id', 'nombre as text')->where('disponible', '1')->orderBy('nombre');
        } elseif($request->type=='MlaId') {
            $data = Producto::select('id', 'mla_id as text')->where('disponible', '1')->orderBy('mla_id');
        } else {
            $data = Item::select('id', 'nombre as text')->where('activo', '1')->orderBy('nombre');
        }

        if($request->type=='MlaId')
        {
            if($request->searchTerm!='')
            {
                $records = $data->where('mla_id', 'like', '%' . $request->searchTerm. '%');
            }
        }
        else
        {
            if($request->searchTerm!='')
            {
                $records = $data->where('nombre', 'like', '%' . $request->searchTerm. '%');
            }
        }

        $records = $data->get()->toArray();
        echo json_encode($records);
    }

    public function productListFilterHavingME1Status(Request $request)
    {
        $searchTerm = $request->searchTerm;
        if($request->type=='Modelo') {
            $data = Producto::select('id','nombre','marca_id','item_id','modelo_id','stock','precio', 'mla_id', 'medida_id', 'altura_id', 'shipping_mode')
                ->where('modelo_id', $request->searchTerm);
        } elseif($request->type=='Marca') {
            $data = Producto::select('id','nombre','marca_id','item_id','modelo_id','stock','precio', 'mla_id', 'medida_id', 'altura_id', 'shipping_mode')
                ->where('marca_id', $request->searchTerm);
        } elseif($request->type=='Productos') {
            $data = Producto::select('id','nombre','marca_id','item_id','modelo_id','stock','precio', 'mla_id', 'medida_id', 'altura_id', 'shipping_mode')
                ->where('id', $request->searchTerm);
        } elseif($request->type=='MlaId') {
            $data = Producto::select('id','nombre','marca_id','item_id','modelo_id','stock','precio', 'mla_id', 'medida_id', 'altura_id', 'shipping_mode')
                ->where('id', $request->searchTerm);
        } else {
            $data = Producto::select('id','nombre','marca_id','item_id','modelo_id','stock','precio', 'mla_id', 'medida_id', 'altura_id', 'shipping_mode')
                ->where('item_id', $request->searchTerm);
        }
        $records = $data->where('disponible', '1')
                ->where('mla_id', '!=', null)
                ->orderBy('mla_id')->get();
        return view('products.product-list-filter-having-me1-status', compact('records'));
    }

    public function mlListShippingModeMe1Update(Request $request)
    {
        $this->validate($request, [
            'selected_b_or_m'   => 'required|numeric',
            'shipping_mode'     => 'required',
        ]);
        if(is_array($request->mla_id) && sizeof($request->mla_id)<1)
        {
            notify()->error('Error, Records not found. Please try again.');
            return redirect()->back()->withInput();
        }

        $mlas = new Hokoml(\Config::get('mercadolibre'), env('ML_ACCESS_TOKEN',''), env('ML_USER_ID',''));

        $notUpdate = '';
        $errorUpdate = '';
        $successUpdate = '';
        foreach ($request->mla_id as $key => $mlaID)
        {
            if(!empty($mlaID))
            {
                $response = $mlas->product()->find($mlaID);
                if($response['http_code']==200)
                {
                    $shippingArr = [
                        'mode' => $request->shipping_mode
                    ];

                    if($request->sale_terms=='No-Change')
                    {
                        $response = $mlas->product()->update($mlaID, [
                            'shipping'  => $shippingArr
                        ]);
                    }
                    elseif($response['body']['available_quantity']>0 )
                    {
                        $manifacturArr[] = [
                          'id'          => 'MANUFACTURING_TIME',
                          'value_name'  => null
                        ];

                        $response = $mlas->product()->update($mlaID, [
                            'shipping'      => $shippingArr,
                            'sale_terms'    => $manifacturArr
                        ]);
                    }
                    elseif($response['body']['available_quantity']>150)
                    {
                        $manifacturArr[] = [
                          'id'          => 'MANUFACTURING_TIME',
                          'value_name'  => '21 días'
                        ];

                        $response = $mlas->product()->update($mlaID, [
                            'shipping'      => $shippingArr,
                            'sale_terms'    => $manifacturArr
                        ]);
                    }
                    $shippingArr    = array();
                    $variationsArr  = array();
                    $manifacturArr  = array();
                    if($response['http_code']!=200)
                    {
                        $errorUpdate.= $mlaID .' error is:'.$response['body']['message'].',<br>';
                    }
                    if($response['http_code']==200)
                    {
                        //$mode = 'not_specified';
                        $this->updateShippingMode($mlaID, $mode);
                        $successUpdate.= $mlaID.',<br>';
                    }
                }
                else
                {
                    $notUpdate.= $mlaID .',<br>';
                }
            }
        }
        if(!empty($notUpdate)) {
            \Session::flash('error', 'Estos productos no se encontraron en ML :<br><strong>'. $notUpdate.'</strong>');
        }
        if(!empty($errorUpdate)) {
            \Session::flash('error', 'Lista de Productos con errrores que NO se actualizaron en ML:<br><strong>'. $notUpdate.'</strong>');
        }
        if(!empty($successUpdate)) {
            \Session::flash('success', 'Lista de Productos actualizados exitosamente en ML:<br><strong>'. $successUpdate.'</strong>');
            notify()->success('Realizada!!!, Product shipping mode succssfully update in ML.');
        }
        return redirect()->back();
    }

    private function updateShippingMode($mlaID, $mode)
    {
        $updateProductStatus = Producto::where('mla_id', $mlaID)->first();
        $updateProductStatus->shipping_mode = $mode;
        $updateProductStatus->sync_date = date('Y-m-d');
        $updateProductStatus->save();
        return true;
    }

    public function syncShippingInfoFromMl()
    {
        //update data one a day
        $getList = Producto::where('mla_id', '!=', null)
            ->where(function ($query) {
                $query->where('sync_date', null)
                      ->orWhere('sync_date', '<', date('Y-m-d'));
            })
            ->limit(100)
            ->get();
        $mlas = new Hokoml(\Config::get('mercadolibre'), env('ML_ACCESS_TOKEN',''), env('ML_USER_ID',''));

        $notUpdate = '';
        $errorUpdate = '';
        $successUpdate = '';
        foreach ($getList as $key => $rec)
        {
            $mlaID = $rec->mla_id;
            if(!empty($mlaID))
            {
                $response = $mlas->product()->find($mlaID);
                if($response['http_code']==200)
                {
                    $mode = $response['body']['shipping']['mode'];
                    $this->updateShippingMode($mlaID, $mode);
                    $successUpdate.= $mlaID.',<br>';
                    //dd($response);
                }
                else
                {
                    $notUpdate.= $mlaID .',<br>';
                }
            }
        }
        if(!empty($notUpdate)) {
            \Session::flash('error', 'Estos productos no se encontraron en ML :<br><strong>'. $notUpdate.'</strong>');
        }
        if(!empty($errorUpdate)) {
            \Session::flash('error', 'Lista de Productos con errrores que NO se actualizaron en ML:<br><strong>'. $notUpdate.'</strong>');
        }
        if(!empty($successUpdate)) {
            \Session::flash('success', 'Modo de envío del producto, actualizado correctamente desde ML:<br><strong>'. $successUpdate.'</strong>');
            notify()->success('Realizada!!!, Product shipping mode succssfully sync from ML.');
        }
        return redirect()->back();
    }

    public function addProductsOnML()
    {
        $products = Producto::where('activo', '1')->whereNull('mla_id')->with('categoria:id,nombre','item:id,nombre','marca:id,nombre','modelo:id,nombre')->get();
        $category = Categoria::whereNull('mla_category_id')->count();
        return view('products.add-products-on-ml', compact('products','category'));
    }

    public function saveProductsOnML(Request $request)
    {
        if(empty($request->boxchecked))
        {
            \Session::flash('warning', 'Please check atleast one product.');
            notify()->warning('Advertencia!!!, Marque al menos un producto.');
        }
        $mlas = new Hokoml(\Config::get('mercadolibre'), env('ML_ACCESS_TOKEN',''), env('ML_USER_ID',''));
        //$response = $mlas->product()->pause('MLA1115198921');
        //$delete = $mlas->product()->delete('MLA1115198921');
        //dd($delete);

        $errorAdding = '';
        $successAdding = '';
        foreach ($request->boxchecked as $key => $product)
        {
            $productInfo = Producto::find($product);
            if(empty($productInfo->mla_id))
            {
                $pictures[]=['source' => env('CDN_URL').'/imagenes/800x600/'.$productInfo->imagen];
                foreach ($productInfo->imagens as $image) {
                    $pictures[] = ['source' => env('CDN_URL').'/imagenes/800x600/'.$image->nombre];
                }
                $dimension = $productInfo->medida->long.'x'.$productInfo->medida->width.'x'.$productInfo->altura->high.','.($productInfo->weight);
                $addTitle = str_replace(',','',$productInfo->categoria->descripcion.' '.$productInfo->marca->nombre.' '.$productInfo->item->nombre.' '.$productInfo->medida->nombre.' '.$productInfo->medida->alias);
                $tagArr = array('</p>','<br />','</li>','</ul>' );
                $charArr = array(PHP_EOL,PHP_EOL,'- ','- ' );
                $addDescription = strip_tags(str_replace($tagArr,$charArr, $productInfo->modelo->descripcion)).' '.
                                    '
ENVIOS A DOMICILIO
Las Entregas se realizan en domicilio dentro de los 7 días (hábiles)
Tambien puede retirar de nuestro Negocio en el barrio de Barracas
(a 5 min. de Puerto Madero) siempre que la medida esté en stock (solicite confirmación).
Consulte costos de envío.
EXPOSICION y VENTAS CON MAS DE 100 MODELOS
En nuestro Showroom contamos con todos los modelos de las mejores marcas como, Simmons, La Cardeuse ,Suavestar , Cannon , Springwall , Delpa , Topacio ,
Belmo , NaturalFoam , Gani , Litoral, Sensorial etc. para que puedan probar y elegir sin apremios y con el mejor asesoramiento cual es el que mejor adapta a su necesidad.
HORARIO DE ATENCION
Estamos de Lunes a Viernes de 9 a 14 hs. y de 15 a 19 hs. y los Sábados de 10 a 17 hs.
NUESTRA ZONA
Estamos en Barracas a 5 minutos de Puerto Madero';
                $tamanio = $productInfo->medida->alias ;
                $addItemObj = [
                  'title' => $addTitle,
                    'category_id' => $productInfo->categoria->mla_category_id,
                    'price' => $productInfo->precio ,
                    'currency_id' => 'ARS',
                    'available_quantity' => ($productInfo->stock>0) ? $productInfo->stock : 200,
                    'buying_mode' => 'buy_it_now',
                    'listing_type_id' => 'gold_special',
                    'automatic_relist' => false,
                    'condition' => 'new',
                    //  actualizar luego de agregar el producto 'description' => ['plain_text' => $addDescription ],
                    'sale_terms' => [
                         [
                            'id' => 'WARRANTY_TYPE',
                            'value_name' => 'Garantía de fábrica'
                         ],
                         [
                            'id' => 'WARRANTY_TIME',
                            'value_name' => @$productInfo->garantia->nombre
                         ]
                      ],
                    'pictures' => $pictures,
                    'attributes' => [
                        [
                            'id' => 'BRAND',
                            'value_name' => @$productInfo->marca->nombre
                        ],
                        [
                            'id' => 'MODEL',
                            'value_name' => @$productInfo->modelo->nombre
                        ],
                        [
                          'id' => (@$productInfo->categoria_id==1) ? 'MATTRESS_SIZE' : 'BOX_SPRING_SIZE',
                          'value_name' => $tamanio
                        ],
                        [
                            'id' => 'LENGTH',
                            'value_name' => @$productInfo->medida->long.'cm'
                        ],
                        [
                            'id' => 'WIDTH',
                            'value_name' => @$productInfo->medida->width.'cm'
                        ],
                        [
                            'id' => 'HEIGHT',
                            'value_name' => @$productInfo->altura->high.'cm'
                        ],
                        [
                            'id' => 'FILLING_TYPE',
                            'value_name' => @$productInfo->tecnologia->nombre
                        ],
                        [
                            'id' => 'FIRMNESS',
                            'value_name' => @$productInfo->postura->nombre
                        ]
                        ],
                        'shipping' => [
                            'dimensions'    => $dimension,
                            'local_pick_up' => true
                        ]
                ];
                $response = $mlas->product()->create($addItemObj);

                if($response['http_code']==200 || $response['http_code']==201)
                {
                    $productInfo->mla_id = $response['body']['id'];
                    $productInfo->save();
                    $successAdding.= $productInfo->nombre.' - ML product_id:'.$productInfo->mla_id.',<br>';
                    $response = $mlas->product()->update($productInfo->mla_id, [ 'description' => ['plain_text' => $addDescription ] ]);
                }
                else
                {
                    $errorAdding.= $productInfo->nombre .' error is:'.$response['body']['message'].',<br>';
                }
            }
        }
        if(!empty($errorAdding)) {
            \Session::flash('error', 'Lista de productos con errores que NO se agregaron en ML:<br><strong>'. $errorAdding.'</strong>');
        }
        if(!empty($successAdding)) {
            \Session::flash('success', 'Lista de productos agregados con éxito en ML:<br><strong>'. $successAdding.'</strong>');
            notify()->success('Realizada!!!, Lista de productos agregados con éxito en ML');
        }
        return redirect()->back();
    }

    public function updateMLCat()
    {
        $mlas = new Hokoml(\Config::get('mercadolibre'), env('ML_ACCESS_TOKEN',''), env('ML_USER_ID',''));
        $categories = Categoria::whereNull('mla_category_id')->get();
        $notAdded = '';
        foreach ($categories as $key => $category) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://api.mercadolibre.com/sites/MLA/domain_discovery/search?limit=1&q='.$category->nombre,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
              CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.env('ML_ACCESS_TOKEN'),
                'Content-Type: application/json'
              ),
            ));

            $response = curl_exec($curl);
            $response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if($response!="[]" && $response!='Bad Request')
            {
                if($response_code==200)
                {
                    $responseDecode = json_decode($response, true);
                    $category->mla_category_id = $responseDecode[0]['category_id'];
                    $category->save();
                }
                else
                {
                    $notAdded.= $category->nombre .',<br>';
                }
            }
            else
            {
                $notAdded.= $category->nombre .',<br>';
            }
        }
        if(!empty($notAdded)) {
            \Session::flash('error', 'Some categories are not updated ML category_id, Please try again. Not added categories are :<br><strong>'. $notAdded.'</strong>');
        }

        \Session::flash('success', 'La identificación de la categoría se actualizó correctamente');
        notify()->success('Realizada!!!, La identificación de la categoría se actualizó correctamente.');
        return redirect()->back();
    }

    public function updatePriceEcxel()
    {
        return view('products.update-price-excel');
    }
    public function productListFilterExcel(Request $request)
    {
        $searchTerm = $request->searchTerm;
        if($request->type=='Modelo') {
            $data = Producto::select('id','nombre','marca_id','item_id','modelo_id','stock','precio','mla_id')
                ->where('modelo_id', $request->searchTerm);
        } elseif($request->type=='Marca') {
            $data = Producto::select('id','nombre','marca_id','item_id','modelo_id','stock','precio','mla_id')
                ->where('marca_id', $request->searchTerm);
        } elseif($request->type=='Productos') {
            $data = Producto::select('id','nombre','marca_id','item_id','modelo_id','stock','precio', 'mla_id')
                ->where('id', $request->searchTerm);
        } elseif($request->type=='MlaId') {
            $data = Producto::select('id','nombre','marca_id','item_id','modelo_id','stock','precio', 'mla_id')
                ->where('id', $request->searchTerm);
        } else {
            $data = Producto::select('id','nombre','marca_id','item_id','modelo_id','stock','precio','mla_id')
                ->where('item_id', $request->searchTerm);
        }
        $records = $data->with('marca','modelo')
                    ->get();
        return view('products.product-list-filter-excel', compact('records'));
    }
    public function exportImportProduct(Request $request)
    {
        if($request->submit_type =='export'){
            $fileName = \Str::slug($request->choose_type).'-'.time().'.csv';
            return  Excel::download(new ProductExport($request->selected_b_or_m,$request->choose_type),$fileName);

        }
        if($request->submit_type =='Save'){
            if(count($request->id) >0 ){
                foreach ($request->id as $key => $id) {
                    $product = Producto::find($id);
                    $product->precio =  $request->price[$key];
                    $product->save();

                }
            }
            notify()->success('Success, Product price successfully changed.');
            return redirect()->back();

        }

        if($request->submit_type =='Uplaod'){
            $this->validate($request, [
                'file'     => 'required|max:10000',
            ]);
            $formatCheck = ['csv'];
            $file = $request->file;

            $extension =  strtolower($file->getClientOriginalExtension());

            if(!in_array($extension, $formatCheck))
            {
                notify()->error('Error, Only .csv, files are acceptable.');
                return redirect()->back()->withInput();
            }

            $csv  = array_map("str_getcsv", file($file,FILE_SKIP_EMPTY_LINES ));
            if(count($csv) <1){
                notify()->error('Error, Empty file.');
                return redirect()->back()->withInput();
            }

            $keys  = array_shift($csv);
            if(!in_array('Id',$keys)){
                notify()->error('Error, You must have a column name  Id and Precio in your excel sheet.');
                return redirect()->back()->withInput();
            }
            if(!in_array('Precio',$keys)){
                notify()->error('Error, You must have a column name  Id and Precio in your excel sheet.');
                return redirect()->back()->withInput();
            }

            foreach ($csv  as $i => $val) {
               $csv[$i] = array_combine($keys, $val);
               $product = Producto::where('id',$csv[$i]['Id'])->first();
               if(!empty($product)){
                    $product->precio = $csv[$i]['Precio'];
                    $product->save();
               }
            }
            notify()->success('Success, Product price successfully changed.');

            return redirect()->back();
        }


    }

}
