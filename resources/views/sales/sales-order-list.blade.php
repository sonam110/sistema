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

@if(Request::segment(1)==='sales-order-create')

	@if(Auth::user()->hasAnyPermission(['sales-order-create']) || Auth::user()->hasRole('admin'))

		{{ Form::open(array('route' => 'sales-order-save', 'class'=> 'form-horizontal','enctype'=>'multipart/form-data', 'files'=>true, 'autocomplete'=>'off')) }}
		<input type="hidden" name="max_dis" id="max_dis" value="" >
        <input type="hidden" name="coupon_id" id="coupon_id" value="" >
        <input type="hidden" name="coupon_discount" id="coupon_discount" value="" >
		@csrf
		<div class="row row-deck">
		    <div class="col-lg-12">
		        <div class="card">
		            <div class="card-header">
		                <h3 class="card-title">
		                    Realizar Venta Nueva
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
		                            {!! Form::number('tax_percentage','0',array('id'=>'tax_percentage','class'=> $errors->has('tax_percentage') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Iva %', 'autocomplete'=>'off','required'=>'required', 'min'=>'0')) !!}
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
		                            {!! Form::text('remark',null,array('id'=>'remark','class'=> $errors->has('remark') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Observaciones', 'autocomplete'=>'off')) !!}
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
		                                        {!! Form::number('required_qty[]',null,array('id'=>'required_qty','class'=> $errors->has('required_qty') ? 'form-control is-invalid state-invalid required_qty' : 'form-control required_qty', 'placeholder'=>'Cantidad', 'autocomplete'=>'off', 'onkeyup'=>'calculationAmount()')) !!}
		                                    </td>
		                                    <td>
		                                        {!! Form::number('price[]',null,array('id'=>'price','class'=> $errors->has('price') ? 'form-control is-invalid state-invalid price' : 'form-control price', 'placeholder'=>'Precio', 'autocomplete'=>'off','min'=>'0','step'=>'any', 'onkeyup'=>'calculationAmount()')) !!}
		                                    </td>
		                                    <td>
		                                        {!! Form::number('subtotal[]',null,array('id'=>'subtotal','class'=> $errors->has('subtotal') ? 'form-control is-invalid state-invalid subtotal' : 'form-control subtotal', 'placeholder'=>'Subtotal', 'autocomplete'=>'off','readonly','min'=>'0')) !!}
		                                    </td>
		                                </tr>
		                            </tbody>
		                        </table>
		                    </div>
		                </div>

		                <div class="row">
		                    <div class="col-xs-12 col-sm-6 col-md-3" id="add_gen_product_div">
							    <label class="custom-control custom-checkbox">
							        <input class="colorinput-input custom-control-input" id="add_gen_product" name="add_gen_product" type="checkbox">
							        <span class="custom-control-label text-primary"><strong>Agregar Producto Especial </strong></span>
							    </label>
							</div>
		                </div>

		                <div class="row" id="generic_product_div" style="display: none;">
		                    <div class="col-md-12 add-more-gen-section table-responsive">
		                        <table class="table table-striped table-bordered" id="product-table">
		                            <thead>
		                                <tr>
		                                    <th width="5%"></th>
		                                    <th>Nombre Producto Agregado</th>
		                                    <th width="17%">Cant.</th>
		                                    <th width="17%">Precio</th>
		                                    <th width="17%">Subtotal</th>
		                                </tr>
		                            </thead>
		                            <tbody>
		                                <tr class="add-sec-gen">
		                                    <td>
		                                        <button type="button" class="btn btn-sm btn-success addMoreGen"><i class="fa fa-plus"></i></button>
		                                    </td>
		                                    <td>
		                                        {!! Form::text('gen_product_name[]',null,array('id'=>'gen_product_name','class'=> $errors->has('gen_product_name') ? 'form-control is-invalid state-invalid gen_product_name generic_product' : 'form-control gen_product_name generic_product', 'placeholder'=>'Producto', 'autocomplete'=>'off')) !!}
		                                    </td>
		                                    <td>
		                                        {!! Form::number('gen_required_qty[]',null,array('id'=>'required_qty','class'=> $errors->has('required_qty') ? 'form-control is-invalid state-invalid required_qty generic_product' : 'form-control required_qty generic_product', 'placeholder'=>'Cantidad', 'autocomplete'=>'off','min'=>'0','step'=>'any', 'onkeyup'=>'calculationAmount()')) !!}
		                                    </td>
		                                    <td>
		                                        {!! Form::number('gen_price[]',null,array('id'=>'price','class'=> $errors->has('price') ? 'form-control is-invalid state-invalid price generic_product' : 'form-control price generic_product', 'placeholder'=>'Precio', 'autocomplete'=>'off','min'=>'0','step'=>'any', 'onkeyup'=>'calculationAmount()')) !!}
		                                    </td>
		                                    <td>
		                                        {!! Form::number('gen_subtotal[]',null,array('id'=>'subtotal','class'=> $errors->has('subtotal') ? 'form-control is-invalid state-invalid subtotal generic_product' : 'form-control subtotal generic_product', 'placeholder'=>'Subtotal', 'autocomplete'=>'off','readonly','min'=>'0')) !!}
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
	                                    <th>{!! Form::number('total_amount',null,array('id'=>'total_amount','class'=> $errors->has('total_amount') ? 'form-control is-invalid state-invalid total_amount' : 'form-control total_amount', 'placeholder'=>'SubTotal', 'autocomplete'=>'off','required'=>'required','min'=>'1','step'=>'any','readonly')) !!}</th>
	                                </tr>
	                                <tr>
	                                    <th class="text-right">Costo de envío <span class="text-danger">*</span></th>
	                                    <th>{!! Form::number('shipping_charge',0,array('id'=>'shipping_charge','class'=> $errors->has('shipping_charge') ? 'form-control is-invalid state-invalid shipping_charge' : 'form-control shipping_charge', 'placeholder'=>'Shipping Cost', 'autocomplete'=>'off','required'=>'required','min'=>'0','step'=>'any','onkeyup'=>'calculationAmount()')) !!}</th>
	                                </tr>
	                                <tr>
	                                    <th class="text-right">Iva 21% <span class="text-danger">*</span></th>
	                                    <th>{!! Form::number('tax_amount',null,array('id'=>'tax_amount','class'=> $errors->has('tax_amount') ? 'form-control is-invalid state-invalid tax_amount' : 'form-control tax_amount', 'placeholder'=>'Iva 21%', 'autocomplete'=>'off','required'=>'required','min'=>'0','step'=>'any', 'readonly')) !!}</th>
	                                </tr>
	                                <tr>
	                                    <th class="text-right">Código promocional <span class="text-danger"></span></th>
	                                    <th><strong class="coupon-amount" style="color:red">Aplicar cupón</strong> <a href="javascript:;" id="coupon-list"><span class="apply-coupon"><i class="fa fa-tag fa-lg"></i> </span></a> </th>
	                                   
	                                </tr>
	                                <tr id="coupon-error-div" class="error text-right" style="color: red; display:none;">
	                                
	                                </tr>

	                                <tr>
	                                    <th class="text-right">Total <span class="text-danger">*</span></th>
	                                    <th>{!! Form::number('gross_amount',null,array('id'=>'gross_amount','class'=> $errors->has('gross_amount') ? 'form-control is-invalid state-invalid gross_amount' : 'form-control gross_amount', 'placeholder'=>'Total', 'autocomplete'=>'off','required'=>'required','min'=>'1','step'=>'any', 'readonly')) !!}</th>
	                                </tr>
	                                <tr style="display: none;">
	                                    <th class="text-right">Forma de pago <span class="text-danger">*</span></th>
	                                    <th>{!! Form::select('payment_through',[
  																				'Credit Card' 	=> 'Crédito',
	                                    		'Debit Card'  	=> 'Débito',
	                                    		'Cash' 			=> 'Efectivo',
																					'Transfers'		=> 'Transferencias',
	                                    		'Partial Payment'=> 'Pago Parcial',
	                                    	],'Partial Payment',array('id'=>'payment_through','class'=> $errors->has('payment_through') ? 'form-control is-invalid state-invalid payment_through' : 'form-control payment_through', 'placeholder'=>'Pagos Parciales', 'autocomplete'=>'off','required'=>'required','onchange'=>'paymentThrough(this.value)')) !!}</th>
	                                </tr>
		                        </table>
		                    </div>
		                </div>

		                <div class="row">
		                    <div class="col-md-12 add-more-partial-payment-section table-responsive">
		                        <table class="table partial-payment table-bordered table-striped" id="partial-payment">
		                        	<thead>
		                        		<tr>
			                        		<th width="5%"></th>
			                        		<th width="20%">Forma de pago <span class="text-danger">*</span></th>
			                        		<th width="20%">Monto (<span class="text-primary" id="remaining_amount"></span>) <span class="text-danger">*</span></th>
			                        		<th width="19%"> <span class="text-danger">*</span></th>
			                        		<th width="18%"> <span class="text-danger">*</span></th>
																	<th width="18%"> <span class="text-danger">*</span></th>
			                        	</tr>
		                        	</thead>
		                        	<tbody>
		                        		<tr class="partial-payment-add-section">
			                        		<td>
		                                <button type="button" class="btn btn-sm btn-success add-partial-payment"><i class="fa fa-plus"></i></button>
		                              </td>
			                        		<th>
			                        			{!! Form::select('partial_payment_mode[]',[
																						'Cash' 			=> 'Efectivo',
		                                    		'Debit Card'  	=> 'Débito',
																						'Transfers' 	=> 'Transferencias',
		                                    		'Credit Card' 	=> 'Crédito',
																						'Cheque' 		=> 'Cheques',
		                                    		'Installment' 	=> 'Cta. Cte.',
		                                    	],null,array('id'=>'partial_payment_mode','class'=> $errors->has('partial_payment_mode') ? 'form-control is-invalid state-invalid partial_payment_mode' : 'form-control partial_payment_mode', 'autocomplete'=>'off','onchange'=>'paymentCheckInput(this)')) !!}
			                        		</th>
			                        		<th>
			                        			{!! Form::number('partial_amount[]',null,array('id'=>'partial_amount','class'=> $errors->has('partial_amount') ? 'form-control is-invalid state-invalid partial_amount' : 'form-control partial_amount', 'autocomplete'=>'off','min'=>'0', 'step'=>'any','onkeyup'=>'checkPayment()','onChange'=>'checkPayment()')) !!}
			                        		</th>
			                        		<th>
			                        			<span style="display:none;" class="card_brand_span">
			                        				{!! Form::select('card_brand[]',[
			                        					'VISA' 		=> 'VISA',
			                        					'CABAL' 	=> 'CABAL',
			                        					'MASTERCARD'=> 'MASTERCARD',
																				'AMERICAN EXPRESS' 	=> 'AMEX',
																				'SHOPPING' 	=> 'SHOPPING',
																				'NARANJA' 	=> 'NARANJA'
			                        				],null,array('id'=>'card_brand','class'=> $errors->has('card_brand') ? 'form-control is-invalid state-invalid card_brand' : 'form-control card_brand', 'autocomplete'=>'off','placeholder'=>'--Marca de  Tarjeta--')) !!}
			                        			</span>

			                        			<span style="display:none;" class="bank_detail_span">
					                        				{!! Form::text('bank_detail[]',null,array('id'=>'bank_detail','class'=> $errors->has('bank_detail') ? 'form-control is-invalid state-invalid bank_detail' : 'form-control bank_detail', 'autocomplete'=>'off','placeholder'=>'Banco')) !!}
			                        			</span>

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
																			12 => 12,
																			18 => 18,
																			24 => 24
																			],null,array('id'=>'no_of_installment','class'=> $errors->has('no_of_installment') ? 'form-control is-invalid state-invalid no_of_installment' : 'form-control no_of_installment', 'autocomplete'=>'off','placeholder'=>'Nro. de cuotas','onchange'=>'calculat_intallment_amount(this)')) !!}
																		</span>

																		<span style="display:none;" class="cheque_number_span">
																			{!! Form::text('cheque_number[]',null,array('id'=>'cheque_number','class'=> $errors->has('cheque_number') ? 'form-control is-invalid state-invalid cheque_number' : 'form-control cheque_number', 'autocomplete'=>'off','placeholder'=>'Cheque Número')) !!}
																		</span>
																	</th>

			                        		<th>
			                        			<span style="display:none;" class="card_number_span">
			                        				{!! Form::text('card_number[]',null,array('id'=>'card_number','class'=> $errors->has('card_number') ? 'form-control is-invalid state-invalid card_number' : 'form-control card_number', 'autocomplete'=>'off','placeholder'=>'Tarjeta Número')) !!}
			                        			</span>

			                        			<span style="display:none;" class="installment_amount_span">
			                        				{!! Form::number('installment_amount[]',null,array('id'=>'installment_amount','class'=> $errors->has('installment_amount') ? 'form-control is-invalid state-invalid installment_amount' : 'form-control installment_amount', 'autocomplete'=>'off','placeholder'=>'Monto de cuota','readonly')) !!}
			                        			</span>
			                        		</th>
			                        	</tr>
		                        	</tbody>
		                        </table>
		                    </div>
		                </div>
		                <hr>
		                <h3 class="card-title">
		                   Dirección de Envío

		                </h3>
		                <div class="row">
							<div class="col-md-4">
									<div class="form-group">
										<label for="name" class="form-label">nombre <span class="text-danger"></span></label>
										{!! Form::text('shipping_name','',array('id'=>'shipping_name','class'=> $errors->has('name') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Nombre', 'autocomplete'=>'off')) !!}
										@if ($errors->has('name'))
										<span class="invalid-feedback" role="alert">
											<strong>{{ $errors->first('name') }}</strong>
										</span>
										@endif
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label for="lastname" class="form-label">Apellido <span class="text-danger"></span></label>
										{!! Form::text('shipping_lastname','',array('id'=>'shipping_lastname','class'=> $errors->has('lastname') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Apellido', 'autocomplete'=>'off')) !!}
										@if ($errors->has('lastname'))
										<span class="invalid-feedback" role="alert">
											<strong>{{ $errors->first('lastname') }}</strong>
										</span>
										@endif
									</div>
								</div>


								<div class="col-md-4">
									<div class="form-group">
										<label for="email" class="form-label">Email <span class="text-danger"></span></label>
										{!! Form::text('shipping_email','',array('id'=>'shipping_email','class'=> $errors->has('email') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Email ', 'autocomplete'=>'off')) !!}
										@if ($errors->has('email'))
										<span class="invalid-feedback" role="alert">
											<strong>{{ $errors->first('email') }}</strong>
										</span>
										@endif
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label for="companyname" class="form-label">Compañía</label>
										{!! Form::text('shipping_companyname','',array('id'=>'shipping_companyname','class'=> $errors->has('companyname') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Compañía', 'autocomplete'=>'off')) !!}
										@if ($errors->has('companyname'))
										<span class="invalid-feedback" role="alert">
											<strong>{{ $errors->first('companyname') }}</strong>
										</span>
										@endif
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label for="address1" class="form-label">Domicilio</label>
										{!! Form::text('shipping_address1','',array('id'=>'shipping_address1','class'=> $errors->has('address1') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Domicilio', 'autocomplete'=>'off')) !!}
										@if ($errors->has('address1'))
										<span class="invalid-feedback" role="alert">
											<strong>{{ $errors->first('address1') }}</strong>
										</span>
										@endif
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label for="address2" class="form-label">Domicilio 2</label>
										{!! Form::text('shipping_address2','',array('id'=>'shipping_address2','class'=> $errors->has('address2') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Domicilio', 'autocomplete'=>'off')) !!}
										@if ($errors->has('address2'))
										<span class="invalid-feedback" role="alert">
											<strong>{{ $errors->first('address2') }}</strong>
										</span>
										@endif
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label for="city" class="form-label">Ciudad</label>
										{!! Form::text('shipping_city','',array('id'=>'shipping_city','class'=> $errors->has('city') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Cliudad', 'autocomplete'=>'off')) !!}
										@if ($errors->has('city'))
										<span class="invalid-feedback" role="alert">
											<strong>{{ $errors->first('city') }}</strong>
										</span>
										@endif
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label for="state" class="form-label">Provincia</label>
										{!! Form::text('shipping_state','',array('id'=>'shipping_state','class'=> $errors->has('state') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Estado', 'autocomplete'=>'off')) !!}
										@if ($errors->has('state'))
										<span class="invalid-feedback" role="alert">
											<strong>{{ $errors->first('state') }}</strong>
										</span>
										@endif
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label for="country" class="form-label">Pais</label>
										{!! Form::text('shipping_country','',array('id'=>'shipping_country','class'=> $errors->has('country') ? 'form-control is-invalid country-invalid' : 'form-control', 'placeholder'=>'Pais', 'autocomplete'=>'off')) !!}
										@if ($errors->has('country'))
										<span class="invalid-feedback" role="alert">
											<strong>{{ $errors->first('country') }}</strong>
										</span>
										@endif
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label for="postcode" class="form-label">Código postal</label>
										{!! Form::text('shipping_postcode','',array('id'=>'shipping_postcode','class'=> $errors->has('postcode') ? 'form-control is-invalid postcode-invalid' : 'form-control', 'placeholder'=>'Código postal', 'autocomplete'=>'off')) !!}
										@if ($errors->has('postcode'))
										<span class="invalid-feedback" role="alert">
											<strong>{{ $errors->first('postcode') }}</strong>
										</span>
										@endif
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="phone" class="form-label">Teléfono <span class="text-danger"></span></label>
										{!! Form::text('shipping_phone','',array('id'=>'shipping_phone','class'=> $errors->has('phone') ? 'form-control is-invalid phone-invalid' : 'form-control', 'placeholder'=>'Teléfono', 'autocomplete'=>'off')) !!}
										@if ($errors->has('phone'))
										<span class="invalid-feedback" role="alert">
											<strong>{{ $errors->first('phone') }}</strong>
										</span>
										@endif
									</div>
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
	                <h3 class="card-title ">Información de Ventas</h3>
	                <div class="card-options">
	                	@can('sales-order-action')
										<span class="col-auto" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Observaciones / Remito / Factura Final">
											<button class="btn btn-sm btn-primary" type="button" data-toggle="modal" data-target="#edit-modal" id="edit-modal-id" data-id="{{base64_encode($booking->id)}}"><i class="fa fa-pencil"></i></button>
										</span>
										@endcan

	                    @can('sales-order-create')
	                    <a class="btn btn-sm btn-outline-primary" href="{{ route('sales-order-create') }}"> <i class="fa fa-plus"></i> Realizar nueva orden de Venta</a>
	                    @endcan
	                    &nbsp;&nbsp;&nbsp;
	                    @can('sales-order-download')
	                    <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ route('sales-order-download', base64_encode($booking->id)) }}"> <i class="fa fa-download"></i> Descargar / Imrimir</a>
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
										                    <center><span class="uppercase">Nota de Pedido</span></center>
										                </td>
										            </tr>
										            <tr>
										                <td colspan="2">&nbsp;</td>
										            </tr>
							                    	<tr>
							                    		<td>Nota de Pedido no. #: {{$booking->tranjectionid}}</td>
							                    		<td>
							                    			Creada : {{date('Y-m-d', strtotime($booking->created_at))}}
							                    			<br>
							                    			Remito:
							                    			<strong id="shipping_guide_date">
							                    				@if(!empty($booking->shipping_guide))
							                    					{{date('Y-m-d', strtotime($booking->shipping_guide))}}
							                    				@else
								                    			-
							                    				@endif
							                    			</strong>

							                    			<br>
							                    			Factura Final:
							                    			<strong id="final_invoice_date">
								                    			@if(!empty($booking->final_invoice))
								                    				{{date('Y-m-d', strtotime($booking->final_invoice))}}
								                    			@else
								                    			-
								                    			@endif
							                    			</strong>
							                    		</td>
							                    	</tr>
							                        <tr>
							                            <td>
							                            	<strong>Facturar a</strong><br>
							                                {{$booking->firstname}} {{$booking->lastname}}<br>
																							{{$user->doc_type}} {{$user->doc_number}} <br>
																							{{$booking->email}}<br>
							                                {{$booking->companyname}}<br>
							                                {{$booking->address1}} {{$booking->address2}},<br> {{$booking->city}}, {{$booking->state}}, {{$booking->postcode}}<br>
							                                {{$booking->phone}}
							                            </td>

							                            <td>
							                                <strong>Dirección de entrega</strong><br>
							                                {{$booking->shipping_firstname}} {{$booking->shipping_lastname}}<br>
																							<br>
																							{{$booking->shipping_email}}<br>
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

							            @foreach($booking->getBookeditemGeneric as $genProductDetail)
							            <tr class="item">
							                <td>
							                    {{$genProductDetail->item_name}}
							                </td>
							                <td>
							                    <center>{{$genProductDetail->itemqty - $genProductDetail->return_qty}}</center>
							                </td>
							                <td>
							                	<center>${{number_format($genProductDetail->itemPrice, 2, '.', ',')}}</center>
							                </td>

							                <td>
							                	<center>${{number_format($genProductDetail->itemPrice * ($genProductDetail->itemqty - $genProductDetail->return_qty), 2, '.', ',')}}</center>
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
							                <td colspan="2"><strong>Costo de envío:</strong> </td>
							                <td>
							                   <center>${{number_format($booking->shipping_charge, 2, '.', ',')}}</center>
							                </td>
							            </tr>
							            @if($booking->is_coupon_apply=='1')
							            <tr class="total">
							                <td></td>
							                <td colspan="2"><strong>Cupón de descuento:</strong> </td>
							                <td>
							                   <center>-${{number_format($booking->coupon_discount, 2, '.', ',')}} </center>
							                </td>
							            </tr>
							            @endif
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
								                <td colspan="3"><strong>plazo:  plan visa  : 7/ Cuotas elegidas: 12</strong></td>
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
							            	<td colspan="4">Observaciones @if($booking->deliveryStatus=='Cancel') :: <span class="text-danger">Orden cancelada</span> @endif</td>
							            </tr>
							            <tr>
							            	<td colspan="4"><span id="orderNoteSpan">{{$booking->orderNote}}</span></td>
							            </tr>

							            <tr class="heading">
							            	<td>Forma de pago</td>
							            	<td><center>Monto</center></td>
							            	<td colspan="2"><center></center></td>
							            </tr>
							            @foreach($booking->bookingPaymentThroughs as $key => $payment)
							            <tr class="item">
							                <td><strong>{{$payment->payment_mode}}</strong></td>
							                <td>
							                	<center>${{number_format($payment->amount, 2, '.', ',')}}
							                	</center>
							                </td>
							                <td colspan="2">
							                	@if($payment->payment_mode=='Cheque')
							                		<span class="text-left bolder">Cheque No. :</span>
							                		<span class="pull-right">{{$payment->cheque_number}}</span>
							                		<br>
							                		<span class="text-left bolder">Banco:</span>
							                		<span class="pull-right">{{$payment->bank_detail}}</span>
							                	@elseif($payment->payment_mode=='Credit Card')
							                		<span class="text-left bolder">Tarjeta :</span>
							                		<span class="pull-right">{{$payment->card_brand}}</span>
							                		<br>
																	<span class="text-left bolder">Cuotas:</span>
																	<span class="pull-right">{{$payment->no_of_installment}}</span>
																	<br>
						                		<span class="text-left bolder">Tarjeta Número :</span>
							                		<span class="pull-right">{{$payment->card_number}}</span>
							                	@elseif($payment->payment_mode=='Installment')
							                		<span class="text-left bolder">Pagos:</span>
							                		<span class="pull-right">{{$payment->no_of_installment}}</span>
							                		<br>
							                		<span class="text-left bolder">Monto de cuota:</span>
							                		<span class="pull-right">${{$payment->installment_amount}}</span>
							                		<br>
							                		<span class="text-left bolder">Cuota paga:</span>
							                		<span class="pull-right">{{$payment->paid_installment}}</span>
							                		<br>
							                		<span class="text-left bolder">Pagos Cancelados ?:</span>
							                		<span class="pull-right">{!!($payment->is_installment_complete=='1' ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>')!!}</span>
							                	@endif
							                </td>
							            </tr>
							            @endforeach

							            @if($booking->bookingPaymentThroughs->count()<1)
							            <tr>
							            	<td>{{$booking->paymentThrough}}</td>
							            	<td><center>${{number_format($booking->payableAmount, 2, '.', ',')}}</center></td>
							            	<td colspan="2"><center></center></td>
							            </tr>
							            @endif
							        </table>
							    </div>
			                </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>

	<div class="row row-deck">
	    <div class="col-lg-12">
	        <div class="card">
	            <div class="card-header">
	                <h3 class="card-title">
	                    Historial de devoluciones en esta Venta
	                </h3>
	            </div>
	            <div class="card-body">
	                <div class="row">
	                    <div class="col-md-12">
	                    	<div class="table-responsive">
			                    <table id="example" class="table table-striped table-bordered">
			                        <thead>
			                            <tr>
			                                <th scope="col">#</th>
			                                <th>Producto</th>
			                                <th>Cantidad Devuelta</th>
			                                <th>Monto Devuelto</th>
			                                 <th>Fecha Devolución</th>
			                                 <th>Nota de Devolución</th>
			                            </tr>
			                        </thead>
			                        <tbody>
			                        	@forelse($booking->salesOrderReturns as $key => $product)
			                        		<tr>
				                                <td scope="col">{{$key+1}}</td>
				                                <td>{{$product->producto->nombre}}</td>
				                                <td>
				                                	<strong>
				                                	{{$product->return_qty}}
				                                	</strong>
				                                </td>
				                                <td>
				                                	<strong>
				                                	${{$product->return_amount}}
				                                	</strong>
				                                </td>
				                                <td>{{$product->created_at->format('Y-m-d')}}</td>
				                                <td>{{$product->return_note}}</td>
				                            </tr>
			                        	@empty
			                        		<tr>
				                                <td colspan="6" scope="col">
				                                	<div class="alert alert-warning" role="alert">
								                		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
								                		<strong>Info! </strong> Sale return history not found.
								                	</div>
				                                </td>
				                            </tr>
			                        	@endforelse
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
	@can('sales-order-list')
	<div class="row">
	    <div class="col-12">
	        <div class="card">
	            <div class="card-header ">
	                <h3 class="card-title ">Gestionar Ventas</h3>
	                <div class="card-options">
	                    @can('sales-order-create')
	                    <a class="btn btn-sm btn-outline-primary" href="{{ route('sales-order-create') }}"> <i class="fa fa-plus"></i>Iniciar Nueva Venta</a>
	                    @endcan
	                    &nbsp;&nbsp;&nbsp;<a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
	                </div>
	            </div>
	            {{ Form::open(array('route' => 'sales-order-action', 'class'=> 'form-horizontal', 'autocomplete'=>'off')) }}
            	@csrf
	            <div class="card-body">
	                <div class="table-responsive">
	                    <table id="datatable" class="table table-striped table-bordered">
	                        <thead>
	                            <tr>
	                                <th scope="col"></th>
	                                <th scope="col">#</th>
	                                <th>Hecha por</th>
	                                <th>Número</th>
	                                <th>Nombre Cliente</th>
	                                <th>Fecha Peddo</th>
	                                <th>Monto</th>
	                                <!-- <th>Forma de Pago</th> -->
	                                <th>Estado del envío</th>
	                                <th>Rto</th>
	                                <th>Fac</th>
	                                <th scope="col" width="10%">Acción</th>
	                            </tr>
	                        </thead>

	                    </table>
	                </div>

	                @can('sales-order-action')
	                <div class="row div-margin">
	                    <div class="col-md-3 col-sm-6 col-xs-6">
	                        <div class="input-group">
	                            <span class="input-group-addon">
	                                <i class="fa fa-hand-o-right"></i> </span>
	                                {{ Form::select('cmbaction', array(
	                                ''              => '-- Estado del envío --',
	                                'Process'       => 'Pendiente',
	                                'Cancel'      	=> 'Cancelado',
	                                'Delivered'     => 'Entregado'),
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
	</div>
	@endcan
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
$(document).on('click', '#coupon-list', function(){
  	var customer_id = $('#customer_id').val();
  	var pids=[]; 
	$('select[name="product_id[]"] option:selected').each(function() {
	  pids.push($(this).val());
	});
	    
  	var subtotal = $('#gross_amount').val();
  	$('#coupon-error-div').hide();
  	$('#coupon-list-section').hide();
	$('.loading').show();
 	$.ajax({
      url: "{{ route('coupons-list') }}",
      type: 'POST',
      data: "pids="+pids+"&customer_id="+customer_id+"&subtotal="+subtotal,
      success:function(info){
        if(info['type']=='error'){
          $('.loading').hide();
          $('#coupon-error-div').show();
          $('#coupon-error-div').text(info['message']);
        } else{
        	$('.loading').hide();
            $("#coupon-list-modal").modal('show');
            $('#coupon-list-section').html(info);
            $('#coupon-list-section').show();
        }


      }
  	});
  	});
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
            { "data": 'checkbox'},
            { "data": 'DT_RowIndex'},
            { "data": 'placed_by'},
            { "data": "tranjectionid"},
            { "data": "customer_name"},
            { "data": "order_date"},
            { "data": "payableAmount"},
            /*{ "data": "paymentThrough"},*/
            { "data": "deliveryStatus"},
            { "data": "shipping_guide"},
            { "data": "final_invoice"},
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
    $('#max_dis').val('');
    $('#coupon_id').val('');
    $('#coupon_discount').val('');
    $('.coupon-amount').text('Aplicar cupón');
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
function customerInfo(e)
{

	$.ajax({
	    url: "{{route('api.get-customer-info')}}",
	    type: "POST",
	    data: "customerId="+e.value,
	    success:function(info){

		    $('#max_dis').val('');
	        $('#coupon_id').val('');
	        $('#coupon_discount').val('');
	        $('.coupon-amount').text('Aplicar cupón');
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
    $('#max_dis').val('');
    $('#coupon_id').val('');
    $('#coupon_discount').val('');
    $('.coupon-amount').text('Aplicar cupón');
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
   	$('#max_dis').val('');
    $('#coupon_id').val('');
    $('#coupon_discount').val('');
    $('.coupon-amount').text('Aplicar cupón');
    calculationAmount();
    checkPayment();
 });

$(document).on("click", ".btn-danger", function () {
    $('#max_dis').val('');
    $('#coupon_id').val('');
    $('#coupon_discount').val('');
    $('.coupon-amount').text('Aplicar cupón');
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

$(document).on("click", "#edit-modal-id", function () {
   $('#edit-section').hide();
   $('.loading').show();
   var id = $(this).data('id');
   $.ajax({
     url: "{{route('api.edit-sales-order-modal')}}",
     type: 'POST',
     data: "id="+id,
     success:function(info){
       $('#edit-section').html(info);
       $('.loading').hide();
       $('#edit-section').show();
     }
   });
 });
</script>
@endsection
