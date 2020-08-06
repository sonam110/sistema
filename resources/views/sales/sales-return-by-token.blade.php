@extends('layouts.master')
@section('content')
	
	<div class="row row-deck">
		    <div class="col-lg-12">
		        <div class="card">
		            <div class="card-header">
		                <h3 class="card-title">
		                    Sale Order Return Information 
		                </h3>
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
	            <div class="card-body">
	                <div class="table-responsive">
	                    <table id="datatable" class="table table-striped table-bordered">
	                        <thead>
	                            <tr>
	                                <th scope="col">#</th>
	                                <th>Product Name</th>
	                                <th>Returned Qty</th>
	                                <th>Returned Amount</th>
	                                <th>Returned Date</th>
	                                <th>Return Note</th>
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