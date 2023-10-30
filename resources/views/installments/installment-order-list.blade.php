@extends('layouts.master')
@section('content')

@if(Request::segment(1)==='installment-receive')

@if(Auth::user()->hasAnyPermission(['installment-receive']) || Auth::user()->hasRole('admin'))

<div class="row row-deck">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">
					Relizar Nuevo cobro de Cuota
				</h3>
				@can('installment-order-list')
				<div class="card-options">
					<a href="{{ route('installment-order-list') }}" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
				</div>
				@endcan
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="booking_id" class="form-label">Orden Número <span class="text-danger">*</span></label>
							<select name="booking_id" class="form-control order-list-select-2" data-placeholder="Enter Order Number" required="" onchange="getDetail(this)">
								<option value='0'>- Buscar Orden -</option>
							</select>
							@if ($errors->has('booking_id'))
							<span class="invalid-feedback" role="alert">
								<strong>{{ $errors->first('booking_id') }}</strong>
							</span>
							@endif
						</div>
					</div>
				</div>

				<div id="installInformation" style="display: none;"></div>
                <div id="errorShow" style="display: none;">
                	<div class="alert alert-warning" role="alert">
                		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                		<strong>Atención! </strong> Orden No encontrada.
                	</div>
                </div>

			</div>
		</div>
	</div>
</div>

<div id="installmentHistory" style="display: none;"></div>

@endif
@elseif(Request::segment(1)==='installment-paid-history')
@can('installment-paid-history')
<div class="row">
	<div class="col-md-12">
		<div class="panel-group1 bg-white" id="accordion1">
			<div class="panel panel-default mb-4">
				<div class="panel-heading1 ">
					<h4 class="panel-title1">
						<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#order-information" aria-expanded="false">Order Detail</a>
					</h4>
				</div>
				<div id="order-information" class="panel-collapse collapse" role="tabpanel" aria-expanded="false">
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table table-striped table-bordered">
								<tr>
									<th width="20%">Orden No.</th>
									<td width="30%"><strong>{{$saleInfo->tranjectionid}}</strong></td>
									<th width="20%">Fecha Pedido</th>
									<td>{{date('Y-m-d', strtotime($saleInfo->created_at))}}</td>
								</tr>
								<tr>
									<th>Estado</th>
									<td>{{$saleInfo->deliveryStatus}}</td>
									<th>Monto Total</th>
									<td><strong>${{$saleInfo->amount}}</strong></td>
								</tr>
								<tr>
									<th>Iva ({{$saleInfo->tax_percentage}}%)</th>
									<td><strong>${{$saleInfo->tax_amount}}</strong></td>
									<th>Monto a Pagar</th>
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
								<thead>
									<tr>
										<th>Modo de Pago</th>
										<th>MOnto</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach($saleInfo->bookingPaymentThroughs as $key => $payment)
									<tr class="item">
										<th>{{$payment->payment_mode}}</th>
										<td>
											<strong>${{$payment->amount}}
											</strong>
										</td>
										<td>
											@if($payment->payment_mode=='Cheque')
											<span class="text-left bolder">Cheque No. :</span>
											<span class="pull-right">{{$payment->cheque_number}}</span>
											<br>
											<span class="text-left bolder">Banco:</span>
											<span class="pull-right">{{$payment->bank_detail}}</span>
											@elseif($payment->payment_mode=='Installment')
											<span class="text-left bolder">No. de Cuotas:</span>
											<span class="pull-right">{{$payment->no_of_installment}}</span>
											<br>
											<span class="text-left bolder">Monto Cuota:</span>
											<span class="pull-right">${{$payment->installment_amount}}</span>
											<br>
											<span class="text-left bolder">Cuota Paga:</span>
											<span class="pull-right">{{$payment->paid_installment}}</span>
											<br>
											<span class="text-left bolder">Cuotas Canceladas ?:</span>
											<span class="pull-right">{!!($payment->is_installment_complete=='1' ? '<span class="text-success">Sí</span>' : '<span class="text-danger">No</span>')!!}</span>
											@endif
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						<div class="table-responsive">
							<table class="table table-striped table-bordered">
								<tr>
									<th width="20%">Nombre del Cliente</th>
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
									<th>Observaciones Pedido</th>
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
			<div class="card-header ">
				<h3 class="card-title ">Historial de recepción de cuotas</h3>
				<div class="card-options">
					@can('installment-receive')
					<a class="btn btn-sm btn-outline-primary" href="{{ route('installment-receive') }}"> <i class="fa fa-plus"></i>Hacer Nuevo Recibo</a>
					@endcan
					&nbsp;&nbsp;&nbsp;
					<a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive">
							<table id="example" class="table table-striped table-bordered">
								<thead>
									<tr>
										<th scope="col">#</th>
										<th>Creado por</th>
										<th>Monto</th>
										<th>Fecha de Pago</th>
									</tr>
								</thead>
								<tbody>
									@foreach($saleInfo->bookingInstallmentPaids as $key => $installment)
									<tr>
										<td>{{$key+1}}</td>
										<td>{{$installment->createdBy->name}} {{$installment->createdBy->lastname}}</td>
										<td>{{$installment->amount}}</td>
										<td>{{$installment->created_at->format('Y-m-d')}}</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endcan
@else
@can('installment-order-list')
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header ">
				<h3 class="card-title ">Gestión de Saldos</h3>
				<div class="card-options">
					@can('installment-receive')
					<a class="btn btn-sm btn-outline-primary" href="{{ route('installment-receive') }}"> <i class="fa fa-plus"></i>Hacer Nuevo Recibo</a>
					@endcan
					&nbsp;&nbsp;&nbsp;<a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
				</div>
			</div>
			{{ Form::open(array('route' => 'installment-action', 'class'=> 'form-horizontal', 'autocomplete'=>'off')) }}
			@csrf
			<div class="card-body">
				<div class="table-responsive">
					<table id="datatable" class="table table-striped table-bordered">
						<thead>
							<tr>
								<th scope="col">#</th>
								<th scope="col"></th>
								<th>Hecho por</th>
								<th>Número</th>
								<th>Nombre de Cliente</th>
								<th>Fecha de Pedido</th>
								<th>Total Pendiente</th>
								<th>No. de Cuotas</th>
								<th>Valor Cuota Mes</th>
								<th>Cuotas Pagadas</th>
								<th>Pagos Completados</th>
								<th scope="col" width="10%">Acción</th>
							</tr>
						</thead>

					</table>
				</div>
				@can('installment-action')
				<hr>
				<div class="row div-margin">
					<div class="col-md-3 col-sm-6 col-xs-6">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-hand-o-right"></i>
							</span>
							{{ Form::select('cmbaction', array(
							''              => 'Acción',
							'Change Status Completed' => 'Saldo Cancelado'),
							'', array('class'=>'form-control','id'=>'cmbaction'))}}
						</div>
					</div>
					<div class="col-md-8 col-sm-6 col-xs-6">
						<div class="input-group">
							<button type="submit" class="btn btn-danger pull-right" name="Action" onClick="return delrec(document.getElementById('cmbaction').value);">Aplicar</button>
						</div>
					</div>
				</div>
				@endcan
			</div>
			{{ Form::close() }}
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
				'url' : '{{ route('api.installment-order-datatable') }}',
				'type' : 'POST'
			},
			'headers': {
				'X-CSRF-TOKEN': '{{ csrf_token() }}'
			},
        "order": [["5", "DESC" ]],
        "columns": [
        { "data": 'DT_RowIndex', "name": 'DT_RowIndex' , orderable: false, searchable: false },
        { "data": 'checkbox'},
        { "data": 'placed_by'},
        { "data": "tranjectionid","name":"booking.tranjectionid"},
        { "data": "customer_name","name":"booking.firstname"},
        { "data": "order_date","name":"booking.created_at"},
        { "data": "amount"},
        { "data": "no_of_installment"},
        { "data": "installment_amount"},
        { "data": "paid_installment"},
        { "data": "installment_status"},
        { "data": "action"}
        ]
    });
	});
	$('.order-list-select-2').select2({
		ajax: {
			url: "{{route('api.get-instalment-order-list')}}",
			type: "post",
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
              searchTerm: params.term // search term
          };
      },
      processResults: function (response) {
      	return {
      		results: response
      	};
      },
      cache: true
  }
});
function getDetail(e)
{
	$("#getInfoBtn").attr('disabled', true);
	$("#getInfoBtn").val('Loading...');
	$("#errorShow").hide();
	$("#installInformation").hide();
	$("#installmentHistory").hide();
	$.ajax({
	    url: "{{route('api.get-installment-order-information')}}",
	    type: "POST",
	    data: "orderId="+e.value,
	    success:function(info){
	    	if(info=='not-found')
	    	{
	    		$("#errorShow").show();
	    		$("#installInformation").hide();
	    	}
	    	else
	    	{
	    		$("#errorShow").hide();
	    		$("#installInformation").html(info);
	    		$("#installInformation").show();
	    		$("#getInfoBtn").attr('disabled', false);
	    		$("#getInfoBtn").val('Save');
	    		getHistory(e.value);
	    	}
	    }
	});
}

function getHistory(orderId) {
	$.ajax({
	    url: "{{route('api.get-installment-history')}}",
	    type: "POST",
	    data: "orderId="+orderId,
	    success:function(info){
	    	$("#installmentHistory").html(info);
	    	$("#installmentHistory").show();
	    }
	});
}
</script>
@endsection
