@extends('layouts.master')
@section('content')

	<div class="row row-deck">
		    <div class="col-lg-12">
		        <div class="card">
		            <div class="card-header">
		                <h3 class="card-title">
		                    Información de devolución de pedidos de venta
		                </h3>
		            </div>
		            <div class="card-body">
		                <div class="row">
		                    <div class="col-md-12">
		                    	<div class="table-responsive">
			                    	<table class="table table-striped table-bordered">
				                        <tr>
				                            <th width="20%">Pedido No.</th>
				                            <td width="30%"><strong>{{$saleInfo->tranjectionid}}</strong></td>
				                            <th width="20%">Fecha Pedido</th>
				                            <td>{{date('Y-m-d', strtotime($saleInfo->created_at))}}</td>
				                        </tr>
				                        <tr>
				                            <th>Estado</th>
				                            <td>{{$saleInfo->deliveryStatus}}</td>
				                            <th>SubTotal</th>
				                            <td><strong>${{$saleInfo->amount}}</strong></td>
				                        </tr>
				                        <tr>
				                            <th>Iva ({{$saleInfo->tax_percentage}}%)</th>
				                            <td><strong>${{$saleInfo->tax_amount}}</strong></td>
				                            <th>Total</th>
				                            <td><strong>${{$saleInfo->payableAmount}}</strong></td>
				                        </tr>
				                        <tr>
				                            <th>Monto Devuelto</th>
				                            <td colspan="3"><strong>${{$saleInfo->totalReturnAmount()}}</strong></td>
				                        </tr>
				                    </table>
				                </div>
				                <div class="table-responsive">
			                    	<table class="table table-striped table-bordered">
				                        <tr>
				                            <th width="20%">Cliente</th>
				                            <td width="30%">{{$saleInfo->firstname}} {{$saleInfo->lastname}}</td>
				                            <th width="20%">Compañía</th>
				                            <td>{{$saleInfo->companyname}}</td>
				                        </tr>
				                        <tr>
				                            <th>Domicilio</th>
				                            <td colspan="3">
				                            	{{$saleInfo->address1}},
				                            	{{$saleInfo->address2}}, {{$saleInfo->city}}, {{$saleInfo->state}}
				                            </td>
				                        </tr>
				                        <tr>
				                            <th>Teléfono</th>
				                            <td colspan="3">{{$saleInfo->phone}}</td>
				                        </tr>

				                        <tr>
				                            <th>Observaciones</th>
				                            <td colspan="3">{{$saleInfo->remark}}</td>
				                        </tr>
				                    </table>
				                </div>
		                    </div>
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
	<div class="row">
	    <div class="col-12">
	        <div class="card">
	            <div class="card-body">
	                <div class="table-responsive">
	                    <table id="datatable" class="table table-striped table-bordered">
	                        <thead>
	                            <tr>
	                                <th scope="col">#</th>
	                                <th>Producto</th>
	                                <th>Cant. Devuelta</th>
	                                <th>Monto Devuelto</th>
	                                 <th>Fecha Devolución</th>
	                                <th>Nota Devolución</th>
	                            </tr>
	                        </thead>
	                        <tbody>
	                        	@foreach($returnProduct as $key => $product)
	                        	<tr>
	                                <td>{{$key+1}}</td>
	                                <td>{{$product->producto->nombre}}</td>
	                                <td><strong>{{$product->return_qty}}</strong></td>
	                                <td><strong>${{$product->return_amount}}</strong></td>
	                                <td>{{$product->created_at->format('Y-m-d')}}</td>
	                                <td>{{$product->return_note}}</td>
	                            </tr>
	                        	@endforeach
	                        </tbody>
	                    </table>
	                </div>
	            </div>
	        </div>
	</div>

@endsection

@section('extrajs')

@endsection
