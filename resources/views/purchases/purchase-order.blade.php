@extends('layouts.master')
@section('content')

@if(Request::segment(1)==='purchase-order-create')

	@if(Auth::user()->hasAnyPermission(['purchase-order-create']) || Auth::user()->hasRole('admin'))

		{{ Form::open(array('route' => 'purchase-order-save', 'class'=> 'form-horizontal','enctype'=>'multipart/form-data', 'files'=>true, 'autocomplete'=>'off')) }}
		@csrf
		<div class="row row-deck">
		    <div class="col-lg-12">
		        <div class="card">
		            <div class="card-header">
		                <h3 class="card-title">
		                    Purchase Order Create 
		                </h3>
		                @can('purchase-order-list')
		                <div class="card-options">
		                    <a href="{{ route('purchase-order-list') }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Go To Back"><i class="fa fa-mail-reply"></i></a>
		                </div>
		                @endcan
		            </div>
		            <div class="card-body">
		                <div class="row">
		                    <div class="col-md-4">
		                        <div class="form-group">
		                            <label for="supplier_id" class="form-label">Supplier Name <span class="text-danger">*</span></label>
		                            <select name="supplier_id" class="form-control supplier-list-select-2" data-placeholder="Enter Supplier Name" required="">
		                                <option value='0'>- Search Supplier -</option>
		                            </select>
		                            @if ($errors->has('supplier_id'))
		                            <span class="invalid-feedback" role="alert">
		                                <strong>{{ $errors->first('supplier_id') }}</strong>
		                            </span>
		                            @endif
		                        </div>
		                    </div>

		                    <div class="col-md-4">
		                        <div class="form-group">
		                            <label for="po_date" class="form-label">PO Date <span class="text-danger">*</span></label>
		                            {!! Form::date('po_date',null,array('id'=>'po_date','class'=> $errors->has('po_date') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'PO Date', 'autocomplete'=>'off','required'=>'required')) !!}
		                            @if ($errors->has('po_date'))
		                            <span class="invalid-feedback" role="alert">
		                                <strong>{{ $errors->first('po_date') }}</strong>
		                            </span>
		                            @endif
		                        </div>
		                    </div>

		                    <div class="col-md-4">
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

		                    <div class="col-md-12">
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
		                                    <th>Product Name</th>
		                                    <th width="17%">Qty</th>
		                                    <th width="17%">Price</th>
		                                    <th width="17%">Subtotal</th>
		                                </tr>
		                            </thead>
		                            <tbody>
		                                <tr class="add-sec">
		                                    <td>
		                                        <button type="button" class="btn btn-sm btn-success addMore"><i class="fa fa-plus"></i></button>
		                                    </td>
		                                    <td>
		                                        <select name="product_id[]" class="form-control product-list-select-2" data-placeholder="Enter Product Name">
		                                            <option value='0'>- Search Product -</option>
		                                        </select>
		                                    </td>
		                                    <td>
		                                        {!! Form::number('required_qty[]',null,array('id'=>'required_qty','class'=> $errors->has('required_qty') ? 'form-control is-invalid state-invalid required_qty' : 'form-control required_qty', 'placeholder'=>'Quantity', 'autocomplete'=>'off','required'=>'required','min'=>'1', 'onkeyup'=>'calculationAmount()')) !!}
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
	                                    <th width="80%" class="text-right">Total Amount</th>
	                                    <th>{!! Form::number('total_amount',null,array('id'=>'total_amount','class'=> $errors->has('total_amount') ? 'form-control is-invalid state-invalid total_amount' : 'form-control total_amount', 'placeholder'=>'Total Amount', 'autocomplete'=>'off','required'=>'required','min'=>'1','step'=>'any','readonly')) !!}</th>
	                                </tr>
	                                <tr>
	                                    <th class="text-right">Tax Amount</th>
	                                    <th>{!! Form::number('tax_amount',null,array('id'=>'tax_amount','class'=> $errors->has('tax_amount') ? 'form-control is-invalid state-invalid tax_amount' : 'form-control tax_amount', 'placeholder'=>'Tax Amount', 'autocomplete'=>'off','required'=>'required','min'=>'1','step'=>'any', 'readonly')) !!}</th>
	                                </tr>
	                                <tr>
	                                    <th class="text-right">Gross Amount</th>
	                                    <th>{!! Form::number('gross_amount',null,array('id'=>'gross_amount','class'=> $errors->has('gross_amount') ? 'form-control is-invalid state-invalid gross_amount' : 'form-control gross_amount', 'placeholder'=>'Gross Amount', 'autocomplete'=>'off','required'=>'required','min'=>'1','step'=>'any', 'readonly')) !!}</th>
	                                </tr>
		                        </table>
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
@elseif(Request::segment(1)==='purchase-order-view')
	@can('purchase-order-view')
	<style>
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
	                <h3 class="card-title ">Purchase Order Information</h3>
	                <div class="card-options">
	                    @can('purchase-order-create')
	                    <a class="btn btn-sm btn-outline-primary" href="{{ route('purchase-order-create') }}"> <i class="fa fa-plus"></i> Create New Purchase Order</a>
	                    @endcan
	                    &nbsp;&nbsp;&nbsp;
	                    @can('purchase-order-download')
	                    <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ route('purchase-order-download', base64_encode($poInfo->id)) }}"> <i class="fa fa-download"></i> Download / Print Purchase Order</a>
	                    @endcan
	                    &nbsp;&nbsp;&nbsp;
	                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Go To Back"><i class="fa fa-mail-reply"></i></a>
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
										                    <center><span class="uppercase">Purchase Order</span></center>
										                </td>
										            </tr>
										            <tr>
										                <td colspan="2">&nbsp;</td>
										            </tr>
							                        <tr>
							                            <td>
							                            	<strong>Supplier Information</strong><br>
							                                {{$poInfo->supplier->name}} <br>
							                                {{$poInfo->supplier->company_name}}<br>
							                                {{$poInfo->supplier->address}},<br> 
							                                {{$poInfo->supplier->city}}, {{$poInfo->supplier->state}}<br>
							                                {{$poInfo->supplier->phone}}<br>
							                                <strong>Vat No.: {{$poInfo->supplier->vat_number}}</strong>
							                            </td>
							                            <td>
							                            	PO No. #: {{$poInfo->po_no}}<br>
							                    			PO Date: {{date('Y-m-d', strtotime($poInfo->po_date))}}<br>
							                    			Status: <strong>{{$poInfo->po_status}}</strong>

							                    			@if($poInfo->po_status=='Completed')
							                    				<br>
							                    				<strong>PO Completed Date: {{$poInfo->po_completed_date}}</strong>
							                    			@endif
							                            </td>
							                        </tr>
							                    </table>
							                </td>
							            </tr>

							            <tr class="heading">
							                <td>
							                    Product Information
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
							            @foreach($poInfo->purchaseOrderProducts as $productDetail)
							            <tr class="item">
							                <td>
							                    {{$productDetail->producto->nombre}}
							                </td>
							                <td>
							                    <center>{{$productDetail->required_qty}}</center>
							                </td>
							                <td>
							                	<center>${{number_format($productDetail->price, 2, '.', ',')}}</center>
							                </td>

							                <td>
							                	<center>${{number_format($productDetail->required_qty * $productDetail->price, 2, '.', ',')}}</center>
							                </td>
							            </tr>
							            @endforeach
							            <tr class="total">
							                <td></td>
							                <td colspan="2"><strong>Total:</strong> </td>
							                <td>
							                   <center>${{number_format($poInfo->total_amount, 2, '.', ',')}}</center>
							                </td>
							            </tr>
							            <tr class="total">
							                <td></td>
							                <td colspan="2"><strong>Tax ({{$poInfo->tax_percentage}}%):</strong> </td>
							                <td>
							                   <center>${{number_format($poInfo->tax_amount, 2, '.', ',')}}</center>
							                </td>
							            </tr>
							            <tr class="total">
							                <td></td>
							                <td colspan="2"><strong>Gross Amount:</strong> </td>
							                <td>
							                   <strong><center>${{number_format($poInfo->gross_amount, 2, '.', ',')}}</center></strong>
							                </td>
							            </tr>

							            <tr>
							                <td colspan="4"><hr>Remark : {{$poInfo->remark}}</td>
							            </tr>
							        </table>
							    </div>
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
	                <h3 class="card-title ">Purchase Order Receiving Information</h3>
	            </div>
	            <div class="card-body">
	                <div class="row">
	                    <div class="col-md-12">
	                    	<div class="table-responsive">
	                    		<table id="example" class="table table-striped table-bordered">
			                        <thead>
			                            <tr>
			                                <th scope="col">#</th>
			                                <th>Product Name</th>
			                                <th>Received Date</th>
			                                <th>Received Qty</th>
			                            </tr>
			                        </thead>
			                        <tbody>
			                        	@foreach($poInfo->purchaseOrderReceivings as $key => $recProduct)
			                        	<tr>
			                        		<td>{{$key+1}}</td>
			                        		<td>{{$recProduct->producto->nombre}}</td>
			                        		<td>{{$recProduct->created_at}}</td>
			                        		<td>{{$recProduct->received_qty}}</td>
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
	@can('purchase-order-list')
	<div class="row">
	    <div class="col-12">
	        <div class="card">
	            <div class="card-header ">
	                <h3 class="card-title ">Purchase Order Management</h3>
	                <div class="card-options">
	                    @can('purchase-order-create')
	                    <a class="btn btn-sm btn-outline-primary" href="{{ route('purchase-order-create') }}"> <i class="fa fa-plus"></i> Create New Purchase Order</a>
	                    @endcan
	                    &nbsp;&nbsp;&nbsp;<a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Go To Back"><i class="fa fa-mail-reply"></i></a>
	                </div>
	            </div>
	            {{ Form::open(array('route' => 'purchase-order-action', 'class'=> 'form-horizontal', 'autocomplete'=>'off')) }}
	            @csrf
	            <div class="card-body">
	                <div class="table-responsive">
	                    <table id="datatable" class="table table-striped table-bordered">
	                        <thead>
	                            <tr>
	                                <th scope="col"></th>
	                                <th scope="col">#</th>
	                                <th>Po Number</th>
	                                <th>Po Date</th>
	                                <th>Supplier</th>
	                                <th>Invoice Amount</th>
	                                <th>Status</th>
	                                <th scope="col" width="10%">Action</th>
	                            </tr>
	                        </thead>

	                    </table>
	                </div>

	                @can('purchase-order-action')
	                <div class="row div-margin">
	                    <div class="col-md-3 col-sm-6 col-xs-6">
	                        <div class="input-group"> 
	                            <span class="input-group-addon">
	                                <i class="fa fa-hand-o-right"></i> </span> 
	                                {{ Form::select('cmbaction', array(
	                                ''              => 'Action',
	                                'Sent'        	=> 'Send Purchase Order To Supplier',
	                                'Delete'        => 'Delete'), 
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
           'url' : '{{ route('api.purchase-order-datatable') }}',
           'type' : 'POST'
        },
       'headers': {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        //"order": [["1", "asc" ]],
        "columns": [
            { "data": 'DT_RowIndex'},
            { "data": 'checkbox'},
            { "data": "po_no" },
            { "data": "po_date" },
            { "data": "supplier" },
            { "data": "invoice_amount" },
            { "data": "po_status" },
            { "data": "action" }
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

$('.supplier-list-select-2').select2({
    ajax: {
      url: "{{route('api.get-supplier-list')}}",
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
});

</script>
@endsection