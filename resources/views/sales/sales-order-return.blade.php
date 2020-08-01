@extends('layouts.master')
@section('content')
@if(Request::segment(1)==='sales-order-return')
	@if(Auth::user()->hasAnyPermission(['sales-order-return']) || Auth::user()->hasRole('admin'))

		{{ Form::open(array('route' => 'sales-order-return-save', 'class'=> 'form-horizontal','enctype'=>'multipart/form-data', 'files'=>true, 'autocomplete'=>'off')) }}
		@csrf
		{!! Form::hidden('booking_id',$saleInfo->id,array('id'=>'booking_id','class'=> 'form-control')) !!}
		<div class="row row-deck">
		    <div class="col-lg-12">
		        <div class="card">
		            <div class="card-header">
		                <h3 class="card-title">
		                    Sale Order Return 
		                </h3>
		                @can('sales-order-list')
		                <div class="card-options">
		                    <a href="{{ route('sales-order-list') }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Go To Back"><i class="fa fa-mail-reply"></i></a>
		                </div>
		                @endcan
		            </div>
		            <div class="card-body">
		                <div class="row">
		                    <div class="col-md-12">
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
				                            <td><strong>{{$saleInfo->amount}}</strong></td>
				                        </tr>
				                        <tr>
				                            <th>Tax ({{$saleInfo->tax_percentage}}%)</th>
				                            <td><strong>{{$saleInfo->tax_amount}}</strong></td>
				                            <th>Payable Amount</th>
				                            <td><strong>{{$saleInfo->payableAmount}}</strong></td>
				                        </tr>
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

		                <div class="row">
		                    <div class="col-md-12 add-more-section">
		                        <div class="table-responsive">
			                        <table class="table table-striped table-bordered" id="product-table">
			                            <thead>
			                                <tr>
			                                    <th width="5%">#</th>
			                                    <th>Product Name</th>
			                                    <th width="10%" class="text-center">Purchased Qty</th>
			                                    <th width="10%" class="text-center">Price</th>
			                                    <th width="10%" class="text-center">Returned Qty</th>
			                                    <th width="17%">Return qty</th>
			                                </tr>
			                            </thead>
			                            <tbody>
			                            	@foreach($saleInfo->getBookeditem as $key => $productDetail)
			                                <tr class="add-sec">
			                                    <td>
			                                        {{$key+1}}
			                                    </td>
			                                    <td>
			                                        {{$productDetail->producto->nombre}}
			                                        {!! Form::hidden('bookeditem_id[]',$productDetail->id,array('id'=>'bookeditem_id','class'=> 'form-control')) !!}
			                                        {!! Form::hidden('producto_id[]',$productDetail->itemid,array('id'=>'producto_id','class'=> 'form-control')) !!}
			                                    </td>
			                                    <td class="text-center">
			                                    	<strong>{{$productDetail->itemqty}}</strong>
			                                    </td>
			                                    <td class="text-center">
			                                        {{$productDetail->itemPrice}}
			                                    </td>
			                                    <td class="text-center">
			                                        <strong>{{$productDetail->return_qty}}</strong>
			                                    </td>
			                                    <td>
			                                    	<span @if($productDetail->itemqty == $productDetail->return_qty) hidden @endif>
			                                    		{!! Form::number('return_qty[]',null,array('id'=>'return_qty','class'=> $errors->has('return_qty') ? 'form-control is-invalid state-invalid return_qty' : 'form-control return_qty', 'placeholder'=>'Return qty', 'autocomplete'=>'off','min'=>'1','max'=> ($productDetail->itemqty-$productDetail->return_qty))) !!}
			                                    	</span>
			                                        <span @if($productDetail->itemqty != $productDetail->return_qty) hidden @endif class="text-danger">
			                                        	All Returned
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
		                            <label for="return_note" class="form-label">Return Note</label>
		                            {!! Form::text('return_note',null,array('id'=>'return_note','class'=> $errors->has('return_note') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Return Note', 'autocomplete'=>'off')) !!}
		                        </div>
		                    </div>
		                </div>

		                <div class="form-footer">
		                    {!! Form::submit('Save', array('class'=>'btn btn-primary btn-block')) !!}
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
		{{ Form::close() }}
	@endif
@else
	@can('sales-order-return-list')
	<div class="row">
	    <div class="col-12">
	        <div class="card">
	            <div class="card-header">
	                <h3 class="card-title">Sale Order Return Product List</h3>
	                <div class="card-options">
	                    @can('sales-order-create')
	                    <a class="btn btn-sm btn-outline-primary" href="{{ route('sales-order-create') }}"> <i class="fa fa-plus"></i> Create New Sale Order</a>
	                    @endcan
	                    &nbsp;&nbsp;&nbsp;<a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Go To Back"><i class="fa fa-mail-reply"></i></a>
	                </div>
	            </div>
	            <div class="card-body">
	                <div class="table-responsive">
	                    <table id="datatable" class="table table-striped table-bordered">
	                        <thead>
	                            <tr>
	                                <th scope="col">#</th>
	                                <th>Order Number</th>
	                                <th>Order&nbsp;Date</th>
	                                <th>Customer</th>
	                                <th>Product Name</th>
	                                <th>Returned Qty</th>
	                                <th>Returned Date</th>
	                                <th>Return Note</th>
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
           'url' : '{{ route('api.sales-return-product-datatable') }}',
           'type' : 'POST'
        },
       'headers': {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        /*"order": [["1", "asc" ]],*/
        "columns": [
            { "data": 'DT_RowIndex'},
            { "data": "tranjectionid" },
            { "data": "order_date" },
            { "data": 'customer'},
            { "data": "product_name" },
            { "data": "returned_qty" },
            { "data": "returned_date" },
            { "data": "return_note" }
        ]
   });
});
</script>
@endsection