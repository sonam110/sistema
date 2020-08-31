@extends('layouts.master')
@section('content')

@if(Request::segment(1)==='sales-order-create')

	@if(Auth::user()->hasAnyPermission(['sales-order-create']) || Auth::user()->hasRole('admin'))

		{{ Form::open(array('route' => 'sales-order-save', 'class'=> 'form-horizontal','enctype'=>'multipart/form-data', 'files'=>true, 'autocomplete'=>'off')) }}
		@csrf
		<div class="row row-deck">
		    <div class="col-lg-12">
		        <div class="card">
		            <div class="card-header">
		                <h3 class="card-title">
		                    Sale Order Create 
		                </h3>
		                @can('sales-order-list')
		                <div class="card-options">
		                    <a href="{{ route('sales-order-list') }}" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
		                </div>
		                @endcan
		            </div>
		            <div class="card-body">
		                <div class="row">
		                    <div class="col-md-4">
		                    	<div class="form-group">
									<label for="customer_id" class="form-label">Customer Name <span class="text-danger">*</span></label>
									<div class="row gutters-xs">
										<div class="col">
											<select name="customer_id" class="form-control customer-list-select-2" data-placeholder="Enter Customer Name" required="">
				                                <option value='0'>- Search Customer -</option>
				                            </select>
										</div>
										@if(Auth::user()->hasAnyPermission(['customer-create']) || Auth::user()->hasRole('admin'))
										<span class="col-auto" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Customer">
											<button class="btn btn-primary" type="button" data-toggle="modal" data-target="#add-modal" id="add-modal-id"><i class="fe fe-plus"></i></button>
										</span>
										@endif
										@if ($errors->has('customer_id'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('customer_id') }}</strong>
			                            </span>
			                            @endif
									</div>
								</div>
		                    </div>

		                    <div class="col-md-2">
		                        <div class="form-group">
		                            <label for="tax_percentage" class="form-label">Tax % <span class="text-danger">*</span></label>
		                            {!! Form::number('tax_percentage','0',array('id'=>'tax_percentage','class'=> $errors->has('tax_percentage') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Tax %', 'autocomplete'=>'off','required'=>'required', 'min'=>'0')) !!}
		                            @if ($errors->has('tax_percentage'))
		                            <span class="invalid-feedback" role="alert">
		                                <strong>{{ $errors->first('tax_percentage') }}</strong>
		                            </span>
		                            @endif
		                        </div>
		                    </div>

		                    <div class="col-md-6">
		                        <div class="form-group">
		                            <label for="remark" class="form-label">Any Remark</label>
		                            {!! Form::text('remark',null,array('id'=>'remark','class'=> $errors->has('remark') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Any Remark', 'autocomplete'=>'off')) !!}
		                        </div>
		                    </div>

		                </div>

		                <div class="row">
		                    <div class="col-md-12 add-more-section table-responsive">
		                        <table class="table table-striped table-bordered" id="product-table">
		                            <thead>
		                                <tr>
		                                    <th width="5%"></th>
		                                    <th>Product Name <span class="text-danger">*</span></th>
		                                    <th width="5%">Stock</th>
		                                    <th width="17%">Qty <span class="text-danger">*</span></th>
		                                    <th width="17%">Price <span class="text-danger">*</span></th>
		                                    <th width="17%">Subtotal <span class="text-danger">*</span></th>
		                                </tr>
		                            </thead>
		                            <tbody>
		                                <tr class="add-sec">
		                                    <td>
		                                        <button type="button" class="btn btn-sm btn-success addMore"><i class="fa fa-plus"></i></button>
		                                    </td>
		                                    <td>
		                                        <select name="product_id[]" class="form-control product-list-select-2" data-placeholder="Enter Product Name" onchange="getPrice(this)">
		                                            <option value='0'>- Search Product -</option>
		                                        </select>
		                                    </td>
		                                    <td>
		                                        <span class="badge badge-success current_stock"></i>0</span>
		                                    </td>
		                                    <td>
		                                        {!! Form::number('required_qty[]',null,array('id'=>'required_qty','class'=> $errors->has('required_qty') ? 'form-control is-invalid state-invalid required_qty' : 'form-control required_qty', 'placeholder'=>'Quantity', 'autocomplete'=>'off','required'=>'required', 'onkeyup'=>'calculationAmount()')) !!}
		                                    </td>
		                                    <td>
		                                        {!! Form::number('price[]',null,array('id'=>'price','class'=> $errors->has('price') ? 'form-control is-invalid state-invalid price' : 'form-control price', 'placeholder'=>'Price', 'autocomplete'=>'off','required'=>'required','min'=>'1','step'=>'any', 'onkeyup'=>'calculationAmount()')) !!}
		                                    </td>
		                                    <td>
		                                        {!! Form::number('subtotal[]',null,array('id'=>'subtotal','class'=> $errors->has('subtotal') ? 'form-control is-invalid state-invalid subtotal' : 'form-control subtotal', 'placeholder'=>'Subtotal', 'autocomplete'=>'off','readonly','min'=>'1')) !!}
		                                    </td>
		                                </tr>
		                            </tbody>
		                        </table>
		                    </div>
		                </div>

		                <div class="row">
		                    <div class="col-md-12">
		                        <table class="table">
		                            <tr>
	                                    <th width="80%" class="text-right">Total Amount <span class="text-danger">*</span></th>
	                                    <th>{!! Form::number('total_amount',null,array('id'=>'total_amount','class'=> $errors->has('total_amount') ? 'form-control is-invalid state-invalid total_amount' : 'form-control total_amount', 'placeholder'=>'Total Amount', 'autocomplete'=>'off','required'=>'required','min'=>'1','step'=>'any','readonly')) !!}</th>
	                                </tr>
	                                <tr>
	                                    <th class="text-right">Tax Amount <span class="text-danger">*</span></th>
	                                    <th>{!! Form::number('tax_amount',null,array('id'=>'tax_amount','class'=> $errors->has('tax_amount') ? 'form-control is-invalid state-invalid tax_amount' : 'form-control tax_amount', 'placeholder'=>'Tax Amount', 'autocomplete'=>'off','required'=>'required','min'=>'0','step'=>'any', 'readonly')) !!}</th>
	                                </tr>
	                                <tr>
	                                    <th class="text-right">Gross Amount <span class="text-danger">*</span></th>
	                                    <th>{!! Form::number('gross_amount',null,array('id'=>'gross_amount','class'=> $errors->has('gross_amount') ? 'form-control is-invalid state-invalid gross_amount' : 'form-control gross_amount', 'placeholder'=>'Gross Amount', 'autocomplete'=>'off','required'=>'required','min'=>'1','step'=>'any', 'readonly')) !!}</th>
	                                </tr>
	                                <tr>
	                                    <th class="text-right">Payment Mode <span class="text-danger">*</span></th>
	                                    <th>{!! Form::select('payment_through',[
	                                    		'Credit Card' 	=> 'Credit Card',
	                                    		'Debit Card'  	=> 'Debit Card',
	                                    		'Cash' 			=> 'Cash',
	                                    		'Partial Payment'=> 'Partial Payment',
	                                    	],null,array('id'=>'payment_through','class'=> $errors->has('payment_through') ? 'form-control is-invalid state-invalid payment_through' : 'form-control payment_through', 'placeholder'=>'-- Payment Mode --', 'autocomplete'=>'off','required'=>'required','onchange'=>'paymentThrough(this.value)')) !!}</th>
	                                </tr>
		                        </table>
		                    </div>
		                </div>

		                <div class="row">
		                    <div class="col-md-12 add-more-partial-payment-section table-responsive">
		                        <table class="table partial-payment table-bordered table-striped" id="partial-payment" style="display: none;">
		                        	<thead>
		                        		<tr>
			                        		<th width="5%"></th>
			                        		<th width="25%">Partial Payment Mode <span class="text-danger">*</span></th>
			                        		<th width="25%">Payment Amount (<span class="text-primary" id="remaining_amount"></span>) <span class="text-danger">*</span></th>
			                        		<th width="22%"> <span class="text-danger">*</span></th>
			                        		<th width="23%"> <span class="text-danger">*</span></th>
			                        	</tr>
		                        	</thead>
		                        	<tbody>
		                        		<tr class="partial-payment-add-section">
			                        		<td>
		                                        <button type="button" class="btn btn-sm btn-success add-partial-payment"><i class="fa fa-plus"></i></button>
		                                    </td>
			                        		<th>
			                        			{!! Form::select('partial_payment_mode[]',[
		                                    		'Credit Card' 	=> 'Credit Card',
		                                    		'Debit Card'  	=> 'Debit Card',
		                                    		'Cash' 			=> 'Cash',
		                                    		'Cheque' 		=> 'Cheque',
		                                    		'Installment' 	=> 'Installment',
		                                    	],null,array('id'=>'partial_payment_mode','class'=> $errors->has('partial_payment_mode') ? 'form-control is-invalid state-invalid partial_payment_mode' : 'form-control partial_payment_mode', 'autocomplete'=>'off','onchange'=>'paymentCheckInput(this)')) !!}
			                        		</th>
			                        		<th>
			                        			{!! Form::number('partial_amount[]',null,array('id'=>'partial_amount','class'=> $errors->has('partial_amount') ? 'form-control is-invalid state-invalid partial_amount' : 'form-control partial_amount', 'autocomplete'=>'off','min'=>'0', 'step'=>'any','onkeyup'=>'checkPayment()','onChange'=>'checkPayment()')) !!}
			                        		</th>
			                        		<th>
			                        			<span style="display:none;" class="no_of_installment_span">
			                        				{!! Form::select('no_of_installment[]',[
			                        					1 => 1,
			                        					2 => 2,
			                        					3 => 3,
			                        					4 => 4,
			                        					5 => 5,
			                        					6 => 6,
			                        					7 => 7,
			                        					8 => 8,
			                        					9 => 9,
			                        					10 => 10,
			                        					11 => 11,
			                        					12 => 12
			                        				],null,array('id'=>'no_of_installment','class'=> $errors->has('no_of_installment') ? 'form-control is-invalid state-invalid no_of_installment' : 'form-control no_of_installment', 'autocomplete'=>'off','placeholder'=>'No. of installment','onchange'=>'calculat_intallment_amount(this)')) !!}
			                        			</span>

			                        			<span style="display:none;" class="cheque_number_span">
			                        				{!! Form::text('cheque_number[]',null,array('id'=>'cheque_number','class'=> $errors->has('cheque_number') ? 'form-control is-invalid state-invalid cheque_number' : 'form-control cheque_number', 'autocomplete'=>'off','placeholder'=>'Cheque Number')) !!}
			                        			</span>
			                        		</th>

			                        		<th>
			                        			<span style="display:none;" class="installment_amount_span">
			                        				{!! Form::number('installment_amount[]',null,array('id'=>'installment_amount','class'=> $errors->has('installment_amount') ? 'form-control is-invalid state-invalid installment_amount' : 'form-control installment_amount', 'autocomplete'=>'off','placeholder'=>'Installment Amount','readonly')) !!}
			                        			</span>
			                        			
			                        			<span style="display:none;" class="bank_detail_span">
			                        				{!! Form::text('bank_detail[]',null,array('id'=>'bank_detail','class'=> $errors->has('bank_detail') ? 'form-control is-invalid state-invalid bank_detail' : 'form-control bank_detail', 'autocomplete'=>'off','placeholder'=>'Bank Detail')) !!}
			                        			</span>
			                        		</th>
			                        	</tr>
		                        	</tbody>
		                        </table>
		                    </div>
		                </div>

		                <div class="form-footer">
		                    {!! Form::submit('Guardar', array('class'=>'btn btn-primary btn-block','id'=>'payment-button')) !!}
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
		{{ Form::close() }}

	@endif
@elseif(Request::segment(1)==='sales-order-view')
	@can('sales-order-view')
	<style>
		.bolder {
			font-weight: 700;
		}
	    .invoice-box {
	        margin: auto;
	        padding: 10px;
	        border: 1px solid #eee;
	        box-shadow: 0 0 10px rgba(0, 0, 0, .15);
	        font-size: 16px;
	        line-height: 24px;
	        font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
	        color: #555;
	    }
	    .text-center{
	    	text-align: center!important;
	    }
	    .uppercase{
	    	text-transform: uppercase;
	    }
	    .invoice-box table {
	        width: 100%;
	        line-height: inherit;
	        text-align: left;
	    }

	    .invoice-box table td {
	        padding: 5px;
	        vertical-align: top;
	    }

	    .invoice-box table tr td:nth-child(2) {
	        text-align: right;
	    }

	    .invoice-box table tr.top table td {
	        padding-bottom: 20px;
	    }

	    .invoice-box table tr.top table td.title {
	        font-size: 45px;
	        line-height: 45px;
	        color: #333;
	    }

	    .invoice-box table tr.information table td {
	        padding-bottom: 10px;
	    }

	    .invoice-box table tr.heading td {
	        background: #eee;
	        border-bottom: 1px solid #ddd;
	        font-weight: bold;
	    }

	    .invoice-box table tr.details td {
	        padding-bottom: 20px;
	    }

	    .invoice-box table tr.item td{
	        border-bottom: 1px solid #eee;
	    }

	    .invoice-box table tr.item.last td {
	        border-bottom: none;
	    }

	    .invoice-box table tr.total td:nth-child(2) {
	        border-top: 2px solid #eee;
	        font-weight: bold;
	    }

	    @media only screen and (max-width: 600px) {
	        .invoice-box table tr.top table td {
	            width: 100%;
	            display: block;
	            text-align: center;
	        }

	        .invoice-box table tr.information table td {
	            width: 100%;
	            display: block;
	            text-align: center;
	        }
	    }

	    /** RTL **/
	    /*.rtl {
	        direction: rtl;
	        font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
	    }

	    .rtl table {
	        text-align: right;
	    }

	    .rtl table tr td:nth-child(2) {
	        text-align: left;
	    }*/
    </style>
	<div class="row">
	    <div class="col-12">
	        <div class="card">
	            <div class="card-header ">
	                <h3 class="card-title ">Sale Order Information</h3>
	                <div class="card-options">
	                    @can('sales-order-create')
	                    <a class="btn btn-sm btn-outline-primary" href="{{ route('sales-order-create') }}"> <i class="fa fa-plus"></i> Create New Sale Order</a>
	                    @endcan
	                    &nbsp;&nbsp;&nbsp;
	                    @can('sales-order-download')
	                    <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ route('sales-order-download', base64_encode($booking->id)) }}"> <i class="fa fa-download"></i> Download / Print Sale Order</a>
	                    @endcan
	                    &nbsp;&nbsp;&nbsp;
	                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
	                </div>
	            </div>
	            <div class="card-body">
	                <div class="row">
	                    <div class="col-md-12">
	                    	<div class="table-responsive">
			                    <div class="invoice-box">
							        <table cellpadding="0" cellspacing="0">
							            <tr class="top">
							                <td colspan="4">
							                    <table>
							                        <tr>
							                            <td class="title">
							                                <img src="{{ env('CDN_URL').$appSetting->website_logo}}" class="" height="80px" width="200px">
							                            </td>

							                            <td>
							                            	<strong>Dormicentro Soñemos</strong><br>
															Av. Reg. de Patricios 554<br>
															C.A.B.A , CP 1265.<br>
															(54) 11 4302-3939 /(54) 11  4307-4456
							                            </td>
							                        </tr>
							                    </table>
							                </td>
							            </tr>

							            <tr class="information">
							            	<td colspan="4">
							                    <table>
							                    	<tr class="heading">
										                <td colspan="2">
										                    <center><span class="uppercase">Nota de Pedido</span></center>
										                </td>
										            </tr>
										            <tr>
										                <td colspan="2">&nbsp;</td>
										            </tr>
							                    	<tr>
							                    		<td>Nota de Pedido no. #: {{$booking->tranjectionid}}</td>
							                    		<td>Creado: {{date('Y-m-d', strtotime($booking->created_at))}}</td>
							                    	</tr>
							                      <tr>
							                        <td>Sujeta a confirmación por Dormicentro Soñemos</td>
							                      </tr>
							                        <tr>
							                            <td>
							                            	<strong>Cobrar a</strong><br>
							                                {{$booking->firstname}} {{$booking->lastname}}<br>
							                                {{$booking->companyname}}<br>
							                                {{$booking->address1}} {{$booking->address2}},<br> {{$booking->city}}, {{$booking->state}}, {{$booking->postcode}}<br>
							                                {{$booking->phone}}
							                            </td>

							                            <td>
							                                <strong>Dirección de entrega</strong><br>
							                                {{$booking->shipping_firstname}} {{$booking->shipping_lastname}}<br>
							                                {{$booking->shipping_companyname}}<br>
							                                {{$booking->shipping_address1}} {{$booking->shipping_address2}},<br> {{$booking->shipping_city}}, {{$booking->shipping_state}}, {{$booking->shipping_postcode}}<br>
							                                {{$booking->shipping_phone}}
							                            </td>
							                        </tr>
							                    </table>
							                </td>
							            </tr>

							            <tr class="heading">
							                <td>
							                    Descripción de los artículos
							                </td>

							                <td>
							                    <center>Cantidad</center>
							                </td>

							                <td>
							                    <center>Precio</center>
							                </td>

							                <td>
							                    <center>Total</center>
							                </td>
							            </tr>
							            @foreach($booking->productDetails as $productDetail)
							            <tr class="item">
							                <td>
							                    {{$productDetail->nombre}}
							                </td>
							                <td>
							                    <center>{{$productDetail->itemqty}}</center>
							                </td>
							                <td>
							                	<center>${{number_format($productDetail->itemPrice, 2, '.', ',')}}</center>
							                </td>

							                <td>
							                	<center>${{number_format($productDetail->itemPrice * $productDetail->itemqty, 2, '.', ',')}}</center>
							                </td>
							            </tr>
							            @endforeach
							            <tr class="total">
							                <td></td>
							                <td colspan="2"><strong>Total:</strong> </td>
							                <td>
							                   <center>${{number_format($booking->amount, 2, '.', ',')}}</center>
							                </td>
							            </tr>
							            <tr class="total">
							                <td></td>
							                <td colspan="2"><strong>Total impuestos: ({{$booking->tax_percentage}}%)</strong> </td>
							                <td>
							                   <center>${{number_format($booking->tax_amount, 2, '.', ',')}}</center>
							                </td>
							            </tr>

							            <tr class="total">
							            @if(Auth::user()->userType==0)
							                @if($booking->due_condition=='12')
								                <td colspan="3"><strong>plazo:  plan visa  : 7/ Cuotas elegidas: </strong></td>
								                <td><strong>{{$booking->installments}}</strong></td>
							                @endif
										@else
											<td></td>
							                <td colspan="2"><strong>plazo:</strong></td>
							                <td><strong><center>{{$booking->installments}}</center></strong></td>
										@endif
										</tr>

							            <tr class="total">
							                <td></td>
							                <td colspan="2"><strong>Pago final:</strong> </td>
							                <td>
							                   <strong><center>${{number_format($booking->payableAmount, 2, '.', ',')}}</center></strong>
							                </td>
							            </tr>
							            <tr class="total">
							                <td colspan="4"><hr></td>
							            </tr>
							            <tr class="heading">
							            	<td>Payment Mode</td>
							            	<td><center>Amount</center></td>
							            	<td colspan="2"><center></center></td>
							            </tr>
							            @foreach($booking->bookingPaymentThroughs as $key => $payment)
							            <tr class="item">
							                <td><strong>{{$payment->payment_mode}}</strong></td>
							                <td>
							                	<center>${{$payment->amount}}
							                	</center>
							                </td>
							                <td colspan="2">
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
							        </table>
							    </div>
			                </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>
	@endcan
@else
	@can('sales-order-list')
	<div class="row">
	    <div class="col-12">
	        <div class="card">
	            <div class="card-header ">
	                <h3 class="card-title ">Sale Order Management</h3>
	                <div class="card-options">
	                    @can('sales-order-create')
	                    <a class="btn btn-sm btn-outline-primary" href="{{ route('sales-order-create') }}"> <i class="fa fa-plus"></i> Create New Sale Order</a>
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
	                                <th>Placed By</th>
	                                <th>Number</th>
	                                <th>Customer Name</th>
	                                <th>Order Date</th>
	                                <th>Amount</th>
	                                <th>Payment Mode</th>
	                                <th>Delivery Status</th>
	                                <th scope="col" width="10%">Action</th>
	                            </tr>
	                        </thead>

	                    </table>
	                </div>
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
           'url' : '{{ route('api.sales-order-datatable') }}',
           'type' : 'POST'
        },
       'headers': {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        "order": [["1", "asc" ]],
        "columns": [
            { "data": 'DT_RowIndex'},
            { "data": 'placed_by'},
            { "data": "tranjectionid"},
            { "data": "customer_name"},
            { "data": "order_date"},
            { "data": "payableAmount"},
            { "data": "paymentThrough"},
            { "data": "deliveryStatus"},
            { "data": "action"}
        ]
   });
});

$('.addMore').on('click', function(){
    $('.product-list-select-2').select2("destroy");
    var i = $('.add-sec').length + 1;  
    var $addmore = $(this).closest('tr').clone();
    $addmore.find('[id]').each(function(){this.id+=i});
    $addmore.find('.btn').removeClass('btn-success').addClass('btn-danger');
    $addmore.find("input:text").val("").end();
    $addmore.find("input:hidden").val("").end();
    $addmore.find("select").val("").end();
    $addmore.find(".current_stock").text("0").end();
    $addmore.find('.btn').html('<i class="fa fa-minus"></i>');
    $addmore.find('.btn').attr('onClick', '$(this).closest("tr").remove();');
    $addmore.appendTo('.add-more-section tbody');
    $('.product-list-select-2').select2({
      placeholder: "Enter Item Name",
      allowClear: true,
      ajax: {
        url: "{{route('api.get-product-list')}}",
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
});

$('.product-list-select-2').select2({
    ajax: {
      url: "{{route('api.get-product-list')}}",
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

$('.customer-list-select-2').select2({
    ajax: {
      url: "{{route('api.get-customer-list')}}",
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
$("input").bind("keyup click keydown change", function(e) {
    calculationAmount();
    checkPayment();
});

function getPrice(e)
{
	$.ajax({
	    url: "{{route('api.get-product-price')}}",
	    type: "POST",
	    data: "productId="+e.value,  
	    success:function(info){
	      $(e).closest('tr').find('.price').val(info.precio);
	      $(e).closest('tr').find('.current_stock').text(info.stock);
	      //$(e).closest('tr').find('.required_qty').attr('max', info.stock);
	    }
	});
}

$(document).on("click", "#add-modal-id", function () {
   $('#add-section').hide();
   $('.loading').show();
   $.ajax({
     url: "{{route('api.add-customer-modal')}}",
     type: 'POST',
     data: "id=customer",  
     success:function(info){
       $('#add-section').html(info);
       $('.loading').hide();
       $('#add-section').show();
     }
   });
 });

$('.add-partial-payment').on('click', function(){
    var i = $('.partial-payment-add-section').length + 1;  
    var $addmore = $(this).closest('tr').clone();
    $addmore.find('[id]').each(function(){this.id+=i});
    $addmore.find('.btn').removeClass('btn-success').addClass('btn-danger');
    $addmore.find("input:text").val("").end();
    $addmore.find("input").val("").end();
    $addmore.find("select").val("").end();
    $addmore.find('.btn').html('<i class="fa fa-minus"></i>');
    $addmore.find('.btn').attr('onClick', '$(this).closest("tr").remove();');
    $addmore.appendTo('.add-more-partial-payment-section tbody');
});

</script>
@endsection