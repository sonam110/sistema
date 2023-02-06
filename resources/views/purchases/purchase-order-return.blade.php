@extends('layouts.master')
@section('content')
@if(Request::segment(1)==='purchase-order-return')
	@if(Auth::user()->hasAnyPermission(['purchase-order-return']) || Auth::user()->hasRole('admin'))

		{{ Form::open(array('route' => 'purchase-order-return-save', 'class'=> 'form-horizontal','enctype'=>'multipart/form-data', 'files'=>true, 'autocomplete'=>'off')) }}
		@csrf
		{!! Form::hidden('purchase_order_id',$poInfo->id,array('id'=>'purchase_order_id','class'=> 'form-control')) !!}
		<div class="row row-deck">
		    <div class="col-lg-12">
		        <div class="card">
		            <div class="card-header">
		                <h3 class="card-title">
		                    Devolución de Orden de Compra
		                </h3>
		                @can('purchase-order-list')
		                <div class="card-options">
		                    <a href="{{ route('purchase-order-list') }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
		                </div>
		                @endcan
		            </div>
		            <div class="card-body">
		                <div class="row">
		                    <div class="col-md-12">
		                    	<div class="table-responsive">
			                    	<table class="table table-striped table-bordered">
				                        <tr>
				                            <th>OC Nro.</th>
				                            <td>{{$poInfo->po_no}}</td>
				                            <th>OC Fecha</th>
				                            <td>{{date('Y-m-d', strtotime($poInfo->po_date))}}</td>
				                        </tr>
				                        <tr>
				                            <th>OC Estado</th>
				                            <td>{{$poInfo->po_status}}</td>
				                            <th>OC Completada el</th>
				                            <td>{{$poInfo->po_completed_date}}</td>
				                        </tr>
				                        <tr>
				                            <th>Iva ({{$poInfo->tax_percentage}}%)</th>
				                            <td><strong>${{$poInfo->tax_amount}}</strong></td>
				                            <th>Monto a Pagar</th>
				                            <td><strong>${{$poInfo->gross_amount}}</strong></td>
				                        </tr>
				                        <tr>
				                            <th>Monto Devuelto</th>
				                            <td colspan="3"><strong>${{$poInfo->totalReturnAmount()}}</strong></td>
				                        </tr>

				                        <tr>
				                            <th>Proveedor</th>
				                            <td>{{$poInfo->supplier->name}}</td>
				                            <th>Compañía </th>
				                            <td>{{$poInfo->supplier->company_name}}</td>
				                        </tr>
				                        <tr>
				                            <th>Domicilio</th>
				                            <td colspan="3">
				                            	{{$poInfo->supplier->address}}, {{$poInfo->supplier->city}}, {{$poInfo->supplier->state}}
				                            </td>
				                        </tr>
				                        <tr>
				                            <th>Teléfono</th>
				                            <td>{{$poInfo->supplier->phone}}</td>
				                            <th>Cuit Nro.</th>
				                            <td>{{$poInfo->supplier->vat_number}}</td>
				                        </tr>

				                        <tr>
				                            <th>Observaciones</th>
				                            <td colspan="3">{{$poInfo->remark}}</td>
				                        </tr>
				                    </table>
				                </div>
		                    </div>

		                </div>

		                <div class="row">
		                    <div class="col-md-12 add-more-section">
		                        <div class="table-responsive">
			                        <table class="table table-striped table-bordered" id="product-table">
			                            <thead>
			                                <tr>
			                                    <th width="5%">#</th>
			                                    <th>Producto</th>
			                                    <th width="10%" class="text-center">Cant. Pedida</th>
			                                    <th width="10%" class="text-center">Precio</th>
			                                    <th width="10%" class="text-center">Cant. Aceptada</th>
			                                    <th width="10%" class="text-center">Cant. Devuelta</th>
			                                    <th width="10%" class="text-center">Máximo a Devolver</th>
			                                    <th width="17%">Devolver</th>
			                                </tr>
			                            </thead>
			                            <tbody>
			                            	@foreach($poInfo->purchaseOrderProducts as $key => $productDetail)
			                                <tr class="add-sec">
			                                    <td>
			                                        {{$key+1}}
			                                    </td>
			                                    <td>
			                                        {{$productDetail->producto->nombre}}
			                                        {!! Form::hidden('purchase_order_product_id[]',$productDetail->id,array('id'=>'purchase_order_product_id'.$key,'class'=> 'form-control')) !!}
			                                        {!! Form::hidden('producto_id[]',$productDetail->producto_id,array('id'=>'producto_id'.$key,'class'=> 'form-control')) !!}
			                                    </td>
			                                    <td class="text-center">
			                                    	{{$productDetail->required_qty}}
			                                    </td>
			                                    <td class="text-center">
			                                        {{$productDetail->price}}
			                                        {!! Form::hidden('return_price[]',$productDetail->price,array('id'=>'return_price'.$key,'class'=> 'form-control')) !!}
			                                    </td>
			                                    <td class="text-center">
			                                        {{$productDetail->accept_qty}}
			                                    </td>
			                                    <td class="text-center">
			                                        {{$productDetail->return_qty}}
			                                    </td>
			                                    <td class="text-center">
			                                        <strong>
			                                        	{{($productDetail->accept_qty- $productDetail->return_qty)}}
			                                        </strong>
			                                    </td>
			                                    <td>
			                                    	@php $remainingQty = $productDetail->required_qty - ($productDetail->accept_qty + $productDetail->return_qty) @endphp
			                                    	<span @if($productDetail->accept_qty == $productDetail->return_qty) hidden @endif>
			                                    		{!! Form::number('return_qty[]',null,array('id'=>'return_qty'.$key,'class'=> $errors->has('return_qty') ? 'form-control is-invalid state-invalid return_qty' : 'form-control return_qty', 'placeholder'=>'Cantidad Devuelta', 'autocomplete'=>'off','min'=>'1','max'=> ($productDetail->accept_qty-$productDetail->return_qty))) !!}
			                                    	</span>
			                                        <span @if($productDetail->accept_qty != $productDetail->return_qty) hidden @endif class="text-danger">
			                                        	No Permitido
			                                        </span>
			                                    </td>
			                                </tr>
			                                @endforeach
			                            </tbody>
			                        </table>
		                        </div>
		                    </div>
		                </div>

		                <div class="row">
		                    <div class="col-md-12">
		                        <div class="form-group">
		                            <label for="return_note" class="form-label">Nota de Devolución</label>
		                            {!! Form::text('return_note',null,array('id'=>'return_note','class'=> $errors->has('return_note') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Nota Devolución', 'autocomplete'=>'off')) !!}
		                        </div>
		                    </div>
		                </div>

		                <div class="form-footer">
		                    {!! Form::submit('Guardar', array('class'=>'btn btn-primary btn-block')) !!}
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
		{{ Form::close() }}
	@endif
@else
	@can('purchase-order-received-list')
	<div class="row">
	    <div class="col-12">
	        <div class="card">
	            <div class="card-header ">
	                <h3 class="card-title ">Orden de Compra, Lista de Productos Devueltos</h3>
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
	                                <th>Cant. Devuelta</th>
	                                <th>Monto Devuelto</th>
	                                <th>Fecha Devolución</th>
	                                <th>Nota de Devolución</th>
	                            </tr>
	                        </thead>

	                    </table>
	                </div>
	            </div>
	        </div>
	</div>
	@endcan
@endif
@endsection

@section('extrajs')
<script type="text/javascript">
$(document).ready( function () {
    var table = $('#datatable').DataTable({
       "processing": true,
       "serverSide": true,
       "ajax":{
           'url' : '{{ route('api.po-return-product-datatable') }}',
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
            { "data": "returned_qty" },
            { "data": "returned_amount" },
            { "data": "returned_date" },
            { "data": "return_note" }
        ]
   });
});
</script>
@endsection
