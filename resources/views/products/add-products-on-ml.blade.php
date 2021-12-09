@extends('layouts.master')
@section('content')

@can('price-change-ml')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title ">Add products on ML</h3>
                <div class="card-options">
                    <a href="{{ route('sync-cat-for-ml') }}"  onClick="return confirm('Are you sure you want to sync category IDs from ML to Local Database?');" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Are you sure you want to sync category IDs from ML to Local Database? Currently {{$category}} are not have an ML category_id"><i class="fa fa-refresh"></i>
                        <i class="badge badge-warning  badge-pill">{{$category}}</i>
                    </a>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
            <div class="card-body">
                {{ Form::open(array('route' => 'save-products-on-ml', 'class'=> 'form-horizontal', 'autocomplete'=>'off')) }}
                @csrf
                <div class="row">
                  <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th> </th>
                                    <th scope="col">#</th>
                                    <th>Nombre</th>
                                    <th>Stock</th>
                                    <th>Precio</th>
                                    <th>Item</th>
                                    <th>Categoria</th>
                                    <th>ML Categoria_id</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $key => $product)
                                <tr>
                                    <td @if(empty(@$product->categoria->mla_category_id)) class="bg-danger"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Please SYNC category first. ML category_id is blank." @endif>
                                        @if(!empty(@$product->categoria->mla_category_id))
                                        <label class="custom-control custom-checkbox">
                                            {{ Form::checkbox('boxchecked[]', $product->id,'', array('class' => 'colorinput-input custom-control-input')) }}
                                            <span class="custom-control-label"></span>
                                        </label>
                                        @endif
                                    </td>
                                    <td scope="col">{{$key+1}}</td>
                                    <td>{{$product->nombre}}</td>
                                    <td>{{$product->stock}}</td>
                                    <td><strong>{{$product->precio}}</strong></td>
                                    <td>{{@$product->item->nombre}}</td>
                                    <td>{{@$product->categoria->nombre}}</td>
                                    <td><strong class="text-success">{{@$product->categoria->mla_category_id}}</strong></td>
                                    <td>{{@$product->marca->nombre}}</td>
                                    <td>{{@$product->modelo->nombre}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                  </div>
                </div>
                <div class="form-footer">
                    {!! Form::submit('Agregar productos en ML', array('class'=>'btn btn-primary btn-block','id'=>'add-products-in-ml')) !!}
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@endcan

@endsection

@section('extrajs')
<script type="text/javascript">
$("#checkAll").click(function(){
    $(':checkbox.allChecked').not(this).prop('checked', this.checked);
});
</script>
@endsection
