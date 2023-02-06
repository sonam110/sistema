@extends('layouts.master')
@section('content')
@can('purchase-order-list')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title ">Productos pedidos pero no recibidos</h3>
                <div class="card-options">
                    @can('purchase-order-create')
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('purchase-order-create') }}"> <i class="fa fa-plus"></i> Realizar Nueva Orden de Compra</a>
                    @endcan
                    &nbsp;&nbsp;&nbsp;<a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th>Oc Nro.</th>
                                <th>Oc&nbsp;Fecha</th>
                                <th>Proveedor</th>
                                <th>Producto</th>
                                <th>Cant. Requerida</th>
                                <th>Cant. Recibida</th>
                                <th>Acción</th>
                            </tr>
                        </thead>

                    </table>
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
           'url' : '{{ route('api.products-ordered-but-not-received-list') }}',
           'type' : 'POST'
        },
       'headers': {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        "order": [["1", "asc" ]],
        "columns": [
            { "data": 'DT_RowIndex', "name": 'DT_RowIndex' , orderable: false, searchable: false },
            { "data": "po_no" },
            { "data": "po_date" },
            { "data": 'supplier'},
            { "data": "product_name" },
            { "data": "required_qty" },
            { "data": "accept_qty" },
            { "data": "action" }
        ]
   });
});
</script>
@endsection
