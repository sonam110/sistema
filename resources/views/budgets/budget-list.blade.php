@extends('layouts.master')
@section('content')
<style type="text/css">
.custom-search {
  position: relative;
  width: 300px;
}
.custom-search-input {
  width: 100%;
  border: 1px solid #ccc;
/*  border-radius: 100px;*/
  padding: 10px 100px 10px 20px;
  line-height: 1;
  box-sizing: border-box;
  outline: none;
}
.custom-search-botton {
  position: absolute;
  right: 0px;
  top: 0px;
  bottom: 0px;
  border: 0;
  background: #d672f1;
  color: #fff;
  outline: none;
  margin: 0;
  padding: 0 10px;
  z-index: 2;
}
</style>

@if(Request::segment(1)==='budget-create')

	@if(Auth::user()->hasAnyPermission(['budget-create']) || Auth::user()->hasRole('admin'))

		{{ Form::open(array('route' => 'budget-save', 'class'=> 'form-horizontal','enctype'=>'multipart/form-data', 'files'=>true, 'autocomplete'=>'off')) }}
		<input type="hidden" name="max_dis" id="max_dis" value="0" >
		@csrf
		<div class="row row-deck">
		    <div class="col-lg-12">
		        <div class="card">
		            <div class="card-header">
		                <h3 class="card-title">
		                   Crear nuevo presupuesto
		                </h3>
		               
		                <div class="card-options">
		                    <a href="{{ route('all-budget') }}" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
		                </div>
		              
		            </div>
		            <div class="card-body">
		                <div class="row">
		                    <div class="col-md-4">
		                    	<div class="form-group">
									<label for="customer_id" class="form-label">Cliente <span class="text-danger">*</span></label>
									<div class="row gutters-xs">
										<div class="col">
											<select name="customer_id"  id="customer_id" class="form-control customer-list-select-2" data-placeholder="Ingrese el Nombre" required="" onChange="customerInfo(this);">
				                                <option value='0'>- Buscar Clientes -</option>
				                            </select>
										</div>
										@if(Auth::user()->hasAnyPermission(['customer-create']) || Auth::user()->hasRole('admin'))
										<span class="col-auto" data-toggle="tooltip" data-placement="top" title="" data-original-title="Agregar Cliente">
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
		                            <label for="tax_percentage" class="form-label">Iva % <span class="text-danger">*</span></label>
		                            {!! Form::number('tax_percentage','0',array('id'=>'tax_percentage','class'=> $errors->has('tax_percentage') ? 'form-control is-invalid state-invalid inputf' : 'form-control inputf', 'placeholder'=>'Iva %', 'autocomplete'=>'off','required'=>'required', 'min'=>'0')) !!}
		                            @if ($errors->has('tax_percentage'))
		                            <span class="invalid-feedback" role="alert">
		                                <strong>{{ $errors->first('tax_percentage') }}</strong>
		                            </span>
		                            @endif
		                        </div>
		                    </div>

		                    <div class="col-md-6">
		                        <div class="form-group">
		                            <label for="remark" class="form-label">Observaciones</label>
		                            {!! Form::text('observation',null,array('id'=>'observation','class'=> $errors->has('observation') ? 'form-control is-invalid state-invalid inputf' : 'form-control inputf', 'placeholder'=>'Observaciones', 'autocomplete'=>'off')) !!}
		                        </div>
		                    </div>
		                    <div class="col-md-12">
		                        <div class="form-group">
		                            <label for="remark" class="form-label">Comentario</label>
		                            {!! Form::textarea('comment',null,array('id'=>'comment','class'=> $errors->has('comment') ? 'form-control is-invalid state-invalid inputf' : 'form-control inputf', 'placeholder'=>'Comentario', 'autocomplete'=>'off','rows'=>'2')) !!}
		                        </div>
		                    </div>

		                </div>


		                <div class="row">
		                    <div class="col-md-12 add-more-section table-responsive">
		                        <table class="table table-striped table-bordered" id="product-table">
		                            <thead>
		                                <tr>
		                                    <th width="5%"></th>
		                                    <th>Producto <span class="text-danger">*</span></th>
		                                    <th width="5%">Stock</th>
		                                    <th width="17%">Cant. <span class="text-danger">*</span></th>
		                                    <th width="17%">Precio <span class="text-danger">*</span></th>
		                                    <th width="17%">Subtotal <span class="text-danger">*</span></th>
		                                </tr>
		                            </thead>
		                            <tbody>
		                                <tr class="add-sec">
		                                    <td>
		                                        <button type="button" class="btn btn-sm btn-success addMore"><i class="fa fa-plus"></i></button>
		                                    </td>
		                                    <td>
		                                        <select name="product_id[]" class="form-control product-list-select-2" data-placeholder="Ingrese el Nombre" onchange="getPrice(this)">
		                                            <option value='0'>- Buscar Producto -</option>
		                                        </select>
		                                    </td>
		                                    <td>
		                                        <span class="badge badge-success current_stock"></i>0</span>
		                                    </td>
		                                    <td>
		                                        {!! Form::number('required_qty[]',null,array('id'=>'required_qty','class'=> $errors->has('required_qty') ? 'form-control is-invalid state-invalid required_qty inputf' : 'form-control required_qty inputf', 'placeholder'=>'Cantidad', 'autocomplete'=>'off', 'onkeyup'=>'calculationAmount();clearCouponCode();')) !!}
		                                    </td>
		                                    <td>
		                                        {!! Form::number('price[]',null,array('id'=>'price','class'=> $errors->has('price') ? 'form-control is-invalid state-invalid price inputf' : 'form-control price inputf', 'placeholder'=>'Precio', 'autocomplete'=>'off','min'=>'0','step'=>'any', 'onkeyup'=>'calculationAmount();clearCouponCode();')) !!}
		                                    </td>
		                                    <td>
		                                        {!! Form::number('subtotal[]',null,array('id'=>'subtotal','class'=> $errors->has('subtotal') ? 'form-control is-invalid state-invalid subtotal inputf' : 'form-control subtotal inputf', 'placeholder'=>'Subtotal', 'autocomplete'=>'off','readonly','min'=>'0')) !!}
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
	                                    <th width="80%" class="text-right">SubTotal  <span class="text-danger">*</span></th>
	                                    <th>{!! Form::number('total_amount',null,array('id'=>'total_amount','class'=> $errors->has('total_amount') ? 'form-control is-invalid state-invalid total_amount inputf' : 'form-control total_amount inputf', 'placeholder'=>'SubTotal', 'autocomplete'=>'off','required'=>'required','min'=>'1','step'=>'any','readonly')) !!}</th>
	                                </tr>
	                                <tr>
	                                    <th class="text-right">Costo de envío <span class="text-danger">*</span></th>
	                                    <th>{!! Form::number('shipping_charge',0,array('id'=>'shipping_charge','class'=> $errors->has('shipping_charge') ? 'form-control is-invalid state-invalid shipping_charge inputf' : 'form-control shipping_charge inputf', 'placeholder'=>'Shipping Cost', 'autocomplete'=>'off','required'=>'required','min'=>'0','step'=>'any','onkeyup'=>'calculationAmount()')) !!}</th>
	                                </tr>
	                                <tr>
	                                    <th class="text-right">Iva 21% <span class="text-danger">*</span></th>
	                                    <th>{!! Form::number('tax_amount',null,array('id'=>'tax_amount','class'=> $errors->has('tax_amount') ? 'form-control is-invalid state-invalid tax_amount inputf' : 'form-control tax_amount inputf', 'placeholder'=>'Iva 21%', 'autocomplete'=>'off','required'=>'required','min'=>'0','step'=>'any', 'readonly')) !!}</th>
	                                </tr>
	                               
	                                

	                                <tr>
	                                    <th class="text-right">Total <span class="text-danger">*</span></th>
	                                    <th>{!! Form::number('gross_amount',null,array('id'=>'gross_amount','class'=> $errors->has('gross_amount') ? 'form-control is-invalid state-invalid gross_amount ' : 'form-control gross_amount ', 'placeholder'=>'Total', 'autocomplete'=>'off','required'=>'required','min'=>'1','step'=>'any', 'readonly')) !!}</th>
	                                </tr>
	                               
		                        </table>
		                    </div>
		                </div>

		                
		                <hr>
		  
		                <div class="form-footer">
		                    {!! Form::submit('Guardar', array('class'=>'btn btn-primary btn-block','id'=>'payment-button')) !!}
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
		{{ Form::close() }}

	@endif
@elseif(Request::segment(1)==='budget-view')

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
	                <h3 class="card-title ">Información de presupuesto</h3>
	                <div class="card-options">
	               
	                    <a class="btn btn-sm btn-outline-primary" href="{{ route('budget-create') }}"> <i class="fa fa-plus"></i>  Crear nuevo presupuesto</a>
	                    
	                    &nbsp;&nbsp;&nbsp;
	                   
	                    <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ route('budget-download', base64_encode($budget->id)) }}"> <i class="fa fa-download"></i> Descargar / Imrimir</a>
	                   
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
							                                <img src="{{ env('CDN_URL') }}/imagenes/{!! $appSetting->website_logo !!}" class="" height="80px" width="200px">
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
										                    <center><span class="uppercase">NOTA DE PRESUPUESTO</span></center>
										                </td>
										            </tr>
										            <tr>
										                <td colspan="2">&nbsp;</td>
										            </tr>
							                    	<tr>
							         
							                    		<td>
							                    			Creada : {{date('Y-m-d', strtotime($budget->created_at))}}
							                    			<br>
							                    			Información al cliente:
							                    			<strong id="shipping_guide_date">
							                    			{{ $user->name}} 	{{ $user->lastname}}
							                    			</strong>

							                    			
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
							            @foreach($budget->productDetails as $productDetail)
							            <tr class="item">
							                <td>
							                    @if($productDetail->is_stock_updated_in_ml=='1')
							                    <span class="text-success" data-toggle="tooltip" title="" data-original-title="ML stock Updated"><i class="fa fa-circle"></i></span>
							                    @else
							                    <span class="text-danger" data-toggle="tooltip" title="" data-original-title="ML stock Not Update"><i class="fa fa-circle-thin"></i></span>
							                    @endif
							                    {{$productDetail->nombre}}
							                </td>
							                <td>
							                    <center>{{$productDetail->itemqty - $productDetail->return_qty}}</center>
							                </td>
							                <td>
							                	<center>${{number_format($productDetail->itemPrice, 2, '.', ',')}}</center>
							                </td>

							                <td>
							                	<center>${{number_format($productDetail->itemPrice * ($productDetail->itemqty - $productDetail->return_qty), 2, '.', ',')}}</center>
							                </td>
							            </tr>
							            @endforeach

							    
                          <tr class="total">
                            <td></td>
                            <td colspan="2"><strong>Costo de envío:</strong> </td>
                            <td>
                              <center>${{number_format($budget->shipping_charge, 2, '.', ',')}}</center>
                            </td>
                          </tr>
                        
							            <tr class="total">
							                <td></td>
							                <td colspan="2"><strong>SubTotal:</strong> </td>
							                <td>
							                   <center>${{number_format($budget->total, 2, '.', ',')}}</center>
							                </td>
							            </tr>
							            <tr class="total">
							                <td></td>
							                <td colspan="2"><strong>Total impuestos: ({{$budget->tax_percentage}}%)</strong> </td>
							                <td>
							                   <center>${{number_format($budget->tax_amount, 2, '.', ',')}}</center>
							                </td>
							            </tr>

							          
							            <tr class="total">
							                <td></td>
							                <td colspan="2"><strong>Total:</strong> </td>
							                <td>
							                   <strong><center>${{number_format($budget->payable_amount, 2, '.', ',')}}</center></strong>
							                </td>
							            </tr>
							            <tr class="total">
							                <td colspan="4"><hr></td>
							            </tr>

							            
							            	<td colspan="4"><span id="orderNoteSpan">{{$budget->observation}}</span></td>

							            </tr>
							            <td colspan="4"><strong>Comentario:</strong><span id="orderNoteSpan">{{$budget->comment}}</span></td>
							            </tr>
							          </table>

							           
							        </table>
							    </div>
			                </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>

	
@else

	<div class="row">
	    <div class="col-12">
	        <div class="card">
	            <div class="card-header ">
	                <h3 class="card-title ">lista de presupuesto </h3>
	                <div class="card-options">
	                   
	                    <a class="btn btn-sm btn-outline-primary" href="{{ route('budget-create') }}"> <i class="fa fa-plus"></i>Iniciar Nueva Venta</a>
	                  
	                    &nbsp;&nbsp;&nbsp;<a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
	                </div>
	            </div>
	            {{ Form::open(array('route' => 'budget-action', 'class'=> 'form-horizontal', 'autocomplete'=>'off')) }}
            	@csrf
	            <div class="card-body">
	                <div class="table-responsive">
	                    <table id="datatable" class="table table-striped table-bordered">
	                        <thead>
	                            <tr>
	                                <th scope="col"></th>
	                                <th scope="col">#</th>
	                                <th>Hecha por</th>
	                                <th>Nombre Cliente</th>
	                                <th>Fecha</th>
	                                <th>Monto</th>
	                                <!-- <th>Forma de Pago</th> -->
	                                <th>Estado</th>
	                                <th scope="col" width="10%">Acción</th>
	                            </tr>
	                        </thead>

	                    </table>
	                </div>

	               
	                <div class="row div-margin">
	                    <div class="col-md-3 col-sm-6 col-xs-6">
	                        <div class="input-group">
	                            <span class="input-group-addon">
	                                <i class="fa fa-hand-o-right"></i> </span>
	                                {{ Form::select('cmbaction', array(
	                                ''              => '-- Estado --',
	                                'Active'       => 'Active',
	                                'Inactive'     => 'Inactive'),
	                                '', array('class'=>'form-control','id'=>'cmbaction'))}}
	                            </div>
	                        </div>
	                        <div class="col-md-8 col-sm-6 col-xs-6">
	                            <div class="input-group">
	                                <button type="submit" class="btn btn-danger pull-right" name="Action" onClick="return delrec(document.getElementById('cmbaction').value);">Aplicar</button>
	                            </div>
	                        </div>
	                    </div>
	                  
	                </div>
                	{{ Form::close() }}
                </div>
            </div>
        </div>
	</div>

@endif


<div id="coupon-list-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
       aria-hidden="true" data-backdrop="static" data-keyboard="false">
   <div class="modal-dialog " role="document">
      <div class="modal-content shadow" style="border-radius: 0.75rem!important;" id="coupon-list-section">
      </div>
   </div>
</div>

@endsection

@section('extrajs')
<script type="text/javascript">

$(document).ready( function () {
    var table = $('#datatable').DataTable({
       "processing": true,
       "serverSide": true,
       "ajax":{
           'url' : '{{ route('api.budget-datatable') }}',
           'type' : 'POST'
        },
       'headers': {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        "order": [["3", "desc" ]],
        "columns": [
            { "data": 'checkbox'},
            { "data": 'DT_RowIndex', "name": 'DT_RowIndex' , orderable: false, searchable: false },
            { "data": 'placed_by',"name": 'createdBy.name'},
            { "data": "customer_name" },
            { "data": "created_at"},
            { "data": "payableAmount"},
            { "data": "status"},
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
 		clearCouponCode();
    calculationAmount();
    checkPayment();
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
          $('#coupon-error-div').hide();
      },
      cache: true
  }
});
$(".inputf").bind("keyup keydown change", function(e) {
		clearCouponCode();
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
	    	clearCouponCode();
	      $(e).closest('tr').find('.price').val(info.precio);
	      $(e).closest('tr').find('.current_stock').text(info.stock);
	      //$(e).closest('tr').find('.required_qty').attr('max', info.stock);
	    }
	});
}

function clearCouponCode(){
	$('#max_dis').val('0');
	$('#coupon_id').val('');
	$('#coupon_discount').val('');
	$('.coupon-amount').text('Aplicar cupón');
	//toastr.info('Coupon Code remove', {timeOut: 3000});
}

function customerInfo(e)
{

	$.ajax({
	    url: "{{route('api.get-customer-info')}}",
	    type: "POST",
	    data: "customerId="+e.value,
	    success:function(info){
	   			clearCouponCode();
	        calculationAmount();
	        checkPayment();
		    $('#shipping_name').val(info.name);
		    $('#shipping_lastname').val(info.lastname);
		    $('#shipping_email').val(info.email);
		    $('#shipping_companyname').val(info.companyname);
		    $('#shipping_address1').val(info.address1);
		    $('#shipping_address2').val(info.address2);
		    $('#shipping_state').val(info.state);
		    $('#shipping_city').val(info.city);
		    $('#shipping_country').val(info.country);
		    $('#shipping_postcode').val(info.postcode);
		    $('#shipping_phone').val(info.phone);

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

$('.addMoreGen').on('click', function(){
    var i = $('.add-sec-gen').length + 1;
    var $addmore = $(this).closest('tr').clone();
    $addmore.find('[id]').each(function(){this.id+=i});
    $addmore.find('.btn').removeClass('btn-success').addClass('btn-danger');
    $addmore.find("input:text").val("").end();
    $addmore.find("input:hidden").val("").end();
    $addmore.find('.btn').html('<i class="fa fa-minus"></i>');
    $addmore.find('.btn').attr('onClick', '$(this).closest("tr").remove();');
    $addmore.appendTo('.add-more-gen-section tbody');
 		clearCouponCode();
    calculationAmount();
    checkPayment();
});

$(document).on("click", "#add_gen_product_div", function () {
	var checkbox = document.getElementById("add_gen_product");
	if(checkbox.checked)
	{
		$("#generic_product_div").show();
	}
	else
	{
		$("#generic_product_div").hide();
	}
	$(".generic_product").val('');
		clearCouponCode();
    calculationAmount();
    checkPayment();
 });

$(document).on("click", ".btn-danger", function () {
		clearCouponCode();
    calculationAmount();
    checkPayment();
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
