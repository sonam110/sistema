@extends('layouts.master')
@section('content')

@if(Request::segment(1)==='installment-receive')

@if(Auth::user()->hasAnyPermission(['installment-receive']) || Auth::user()->hasRole('admin'))

<div class="row row-deck">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">
					Create New Installment Receiving
				</h3>
				@can('installment-order-list')
				<div class="card-options">
					<a href="{{ route('installment-order-list') }}" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="right" title="" data-original-title="Go To Back"><i class="fa fa-mail-reply"></i></a>
				</div>
				@endcan
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="booking_id" class="form-label">Order Number <span class="text-danger">*</span></label>
							<select name="booking_id" class="form-control order-list-select-2" data-placeholder="Enter Order Number" required="" onchange="getDetail(this)">
								<option value='0'>- Search Order -</option>
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
                		<strong>Warning! </strong> Order Not found. Please try again.
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
									<th width="20%">Order No.</th>
									<td width="30%"><strong>{{$saleInfo->tranjectionid}}</strong></td>
									<th width="20%">Order Date</th>
									<td>{{date('Y-m-d', strtotime($saleInfo->created_at))}}</td>
								</tr>
								<tr>
									<th>Status</th>
									<td>{{$saleInfo->deliveryStatus}}</td>
									<th>Total Amount</th>
									<td><strong>${{$saleInfo->amount}}</strong></td>
								</tr>
								<tr>
									<th>Tax ({{$saleInfo->tax_percentage}}%)</th>
									<td><strong>${{$saleInfo->tax_amount}}</strong></td>
									<th>Payable Amount</th>
									<td><strong>${{$saleInfo->payableAmount}}</strong></td>
								</tr>
								<tr>
									<th>Returned Amount</th>
									<td colspan="3"><strong>${{$saleInfo->totalReturnAmount()}}</strong></td>
								</tr>
							</table>
						</div>
						<div class="table-responsive">
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>Payment Mode</th>
										<th>Amount</th>
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
											<span class="text-left bolder">Bank Info:</span> 
											<span class="pull-right">{{$payment->bank_detail}}</span>
											@elseif($payment->payment_mode=='Installment')
											<span class="text-left bolder">No. of Installment:</span> 
											<span class="pull-right">{{$payment->no_of_installment}}</span>
											<br>
											<span class="text-left bolder">Installment Amount:</span>
											<span class="pull-right">${{$payment->installment_amount}}</span>
											<br>
											<span class="text-left bolder">Paid Installment:</span>
											<span class="pull-right">{{$payment->paid_installment}}</span>
											<br>
											<span class="text-left bolder">Is Installment Complete:</span>
											<span class="pull-right">{!!($payment->is_installment_complete=='1' ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>')!!}</span>
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
									<th width="20%">Customer Name</th>
									<td width="30%">{{$saleInfo->firstname}} {{$saleInfo->lastname}}</td>
									<th width="20%">Company Name</th>
									<td>{{$saleInfo->companyname}}</td>
								</tr>
								<tr>
									<th>Address</th>
									<td colspan="3">
										{{$saleInfo->address1}}, 
										{{$saleInfo->address2}}, {{$saleInfo->city}}, {{$saleInfo->state}}
									</td>
								</tr>
								<tr>
									<th>Phone</th>
									<td colspan="3">{{$saleInfo->phone}}</td>
								</tr>

								<tr>
									<th>Order Remark</th>
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
				<h3 class="card-title ">Installment Receiving History</h3>
				<div class="card-options">
					@can('installment-receive')
					<a class="btn btn-sm btn-outline-primary" href="{{ route('installment-receive') }}"> <i class="fa fa-plus"></i> Create New Receiving</a>
					@endcan
					&nbsp;&nbsp;&nbsp;
					<a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Go To Back"><i class="fa fa-mail-reply"></i></a>
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
										<th>Created By</th>
										<th>Amount</th>
										<th>Paid Date</th>
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
				<h3 class="card-title ">Installment Order Management</h3>
				<div class="card-options">
					@can('installment-receive')
					<a class="btn btn-sm btn-outline-primary" href="{{ route('installment-receive') }}"> <i class="fa fa-plus"></i> Create New Receiving</a>
					@endcan
					&nbsp;&nbsp;&nbsp;<a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Go To Back"><i class="fa fa-mail-reply"></i></a>
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
								<th>Placed By</th>
								<th>Number</th>
								<th>Customer Name</th>
								<th>Order Date</th>
								<th>Total Installment Amount</th>
								<th>No. of Installment</th>
								<th>Installment Per Month</th>
								<th>Paid Installments</th>
								<th>Installment Complete</th>
								<th scope="col" width="10%">Action</th>
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
							''              => 'Action',
							'Change Status Completed' => 'Change Status Completed'), 
							'', array('class'=>'form-control','id'=>'cmbaction'))}} 
						</div>
					</div>
					<div class="col-md-8 col-sm-6 col-xs-6">
						<div class="input-group">
							<button type="submit" class="btn btn-danger pull-right" name="Action" onClick="return delrec(document.getElementById('cmbaction').value);">Apply</button>
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
        //"order": [["1", "asc" ]],
        "columns": [
        { "data": 'DT_RowIndex'},
        { "data": 'checkbox'},
        { "data": 'placed_by'},
        { "data": "tranjectionid"},
        { "data": "customer_name"},
        { "data": "order_date"},
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