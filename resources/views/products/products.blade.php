@extends('layouts.master')
@section('content')

@can('product-list')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title ">Productos</h3>
                <div class="card-options">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th>Nombre</th>
                                <th>Stock</th>
                                <th>Precio</th>
                                <th>Item</th>
                                <th>Categoria</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Medida</th>
                                <th>Altura</th>
                                <!-- <th>Tecnologia</th> -->
                                <!-- <th>Garantia</th> -->
                                <!-- <th>Postura</th> -->
                                <th>Activo</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endcan

@endsection

@section('extrajs')
<script type="text/javascript">
$(document).ready( function () {
    var table = $('#datatable').DataTable({
       "processing": true,
       "serverSide": true,
       "ajax":{
           'url' : '{{ route('api.products-datatable') }}',
           'type' : 'POST'
        },
       'headers': {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        "order": [["1", "asc" ]],
        "columns": [
            { "data": 'DT_RowIndex'},
            { "data": "nombre" },
            { "data": "stock" },
            { "data": "precio" },
            { "data": "item" },
            { "data": "categoria" },
            { "data": "marca" },
            { "data": "modelo" },
            { "data": "medida" },
            { "data": "altura" },
            // { "data": "tecnologia" },
            // { "data": "garantia" },
            // { "data": "postura" },
            { "data": "activo" }
        ]
   });
});
</script>
@endsection
