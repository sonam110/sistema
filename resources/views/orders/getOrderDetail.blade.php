 <?php
              $amount = ($order->amount-$order->shipping_charge)+$order->coupon_discount;
     ?>

<div class="modal-header">
	
	<h4 class="modal-title" id="myModalLabel">
		PaymentId  : <span class="text-info">{!!$order->tranjectionid!!}</span><span class="pull-right">
			<span class="text-danger" style="margin-left: 299px;"> <i class="fa fa-usd"></i>{!!$amount !!} </span>
		</span>

	</h4>
	 <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="fa fa-close"></span></button>
</div>
<div class="modal-body">
	<div class="table-responsive">
		@if(Auth::user()->userType=='0')
			@if($order->getCardInfo)
				<div class="panel-group piluku-accordion piluku-accordion-two" id="accordionOne" role="tablist" aria-multiselectable="true">
					<div class="panel panel-default">
						<div class="panel-heading" role="tab" id="headingModalOne">
							<h4 class="panel-title">
								<a class="collapsed" data-toggle="collapse" data-parent="#accordionOne" href="#collapseModalCard" aria-expanded="true" aria-controls="collapseOne">
									Card Information
								</a>
							</h4>
						</div>
						<div id="collapseModalCard" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
							<div class="panel-body">
								<table class="table table-hover table-bordered">
									<tr>
										<th width="25%">Half Payment</th>
										<td width="25%"><strong>{{ $order->getCardInfo->card_type }}</strong></td>
										<th width="25%">Card Number	</th>
										<td width="25%"><strong>{{ $order->getCardInfo->card_number }}</strong></td>
									</tr>
									<tr>
										<th>Expiry Month	</th>
										<td><strong>{{ $order->getCardInfo->card_expiration_month }}</strong></td>
										<th>Expiry Year	</th>
										<td><strong>{{ $order->getCardInfo->card_expiration_year }}</strong></td>
									</tr>
									<tr>
										<th>Security code</th>
										<td><strong>{{ $order->getCardInfo->security_code }}</strong></td>
										<th>Name</th>
										<td><strong>{{ $order->getCardInfo->card_holder_name }}</strong></td>
									</tr>
									<tr>
										<th>Document type	</th>
										<td><strong>{{ $order->getCardInfo->card_holder_doc_type }}</strong></td>
										<th>Document Number	</th>
										<td colspan="3"><strong>{{ $order->getCardInfo->card_holder_doc_number }}</strong></td>
									</tr>
									<tr>
										<th>Summary delivery address number	</th>
										<td><strong>{{ $order->getCardInfo->card_holder_door_number }}</strong></td>
										<th>Birthdate</th>
										<td colspan="3"><strong>{{ date('d-M-Y',strtotime($order->getCardInfo->card_holder_birthday)) }}</strong></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			@endif
		@endif
		<table class="table table-hover table-bordered">
			<tr>
				<th width="25%">Order date	</th>
				<td width="25%">
					<span class="text-success">
						{{ $order->created_at }}
					</span>
				</td>
				<th width="25%">Payment status	</th>
				<td width="25%">
					<span class="text-danger">
						{{ $order->orderstatus }}
					</span>
				</td>
			</tr>

			<tr>
				<th>Name </th>
				<td>{{$order->firstname}} {{$order->lastname}}</td>
				<th>Company </th>
				<td>{{$order->companyname}}</td>
			</tr>

			<tr>
				<th>Pais </th>
				<td>{{$order->country}}</td>
				<th>Home</th>
				<td>{{$order->address1}} {{$order->address2}}</td>
			</tr>

			<tr>
				<th>Location / City	 </th>
				<td>{{$order->city}}</td>
				<th>Province/Party		</th>
				<td>{{$order->state}}</td>
			</tr>

			<tr>
				<th>Postal Code	</th>
				<td>{{$order->postcode}}</td>
				<th>Email </th>
				<td>{{$order->email}}</td>
			</tr>

			<tr>
				<th>Telephone </th>
				<td>{{$order->phone}}</td>
				<th>Order Status </th>
				<td>
					<span class="text-info">
						{{$order->deliveryStatus}}
					</span>
				</td>
			</tr>
			<tr>
				<th>IP adress	 </th>
				<td>{{$order->ip_address}}</td>
				<th>Type | DNI | Door | Birth date.	</th>
				<td>
					<span class="text-info">
						{{$order->address_validation_code}}
					</span>
				</td>
			</tr>
		</table>

		<table class="table table-hover table-bordered">
			<tr>
				<th colspan="4" class="text-center text-danger">
					Shipping Address

				</th>
			</tr>

			<tr>
				<th width="25%">Name </th>
				<td width="25%">{{$order->shipping_firstname}} {{$order->shipping_lastname}}</td>
				<th width="25%">Company </th>
				<td width="25%">{{$order->shipping_companyname}}</td>
			</tr>

			<tr>
				<th>Pais </th>
				<td>{{$order->shipping_country}}</td>
				<th>Delivery address	</th>
				<td>{{$order->shipping_address1}} {{$order->shipping_address2}}</td>
			</tr>

			<tr>
				<th>Location / City	 </th>
				<td>{{$order->shipping_city}}</td>
				<th>Province </th>
				<td>{{$order->shipping_state}}</td>
			</tr>

			<tr>
				<th>Cod. Postal	 </th>
				<td>{{$order->shipping_postcode}}</td>
				<th>E-mail </th>
				<td>{{$order->shipping_email}}</td>
			</tr>

			<tr>
				<th>Telephone </th>
				<td colspan="3">{{$order->shipping_phone}}</td>
			</tr>

			<tr>
				<th>Purchase Notes	 </th>
				<td colspan="3">
					<span class="text-danger">
						{{$order->orderNote}}
					</span>
				</td>
			</tr>
		</table>
          <table class="table table-hover table-bordered">
			<tr>
				<th colspan="6" class="text-center text-danger">
					Fee details

				</th>
			</tr>
           <tr>
				<th width="20%">Total </th>
				<td width="20%">${{$amount}} </td>
				<th width="20%">Total interest	
                </th>
				<td width="20%">${{$order->interestAmount}}</td>
				<th width="20%">Total Shipping	
                </th>
				<td width="20%">${{$order->shipping_charge}}</td>
			</tr>


			<tr>@if($order->is_coupon_apply=='1')
				<th >Coupon Discount</th>
				<td >-${{$order->coupon_discount}}</td>
				@endif
				<th>Final Amount</th>
				<td>${{$order->payableAmount}} </td>
				<th>Term</th>
				<th colspan="3">
         			@if(Auth::user()->userType==0)
                 		{{-- @if($order->due_condition=='12') --}}
        				installment plan Chosen installments: {{$order->installments}}</th>
        				{{-- @endif --}}
					@else
						{{$order->installments}}
			    	@endif
			    </th>
			</tr>
			<?php
			if(!empty($order->installments))
			{
				$installment = $order->installments;
			}
			else
			{
				$installment = 1;
			}
			$insAmount=ROUND(($order->payableAmount/$installment),2);


			?>
			<tr>
				<th >Installment Amount	</th>
				<td colspan="5">${{$insAmount}}</td>

			</tr>


		</table>
		<table class="table table-hover table-bordered">
			<tr>
				<th colspan="6" class="text-center text-danger">
					Product Detail

				</th>
			</tr>

			<tr>
				<th>#</th>
				<th>IMAGEN</th>
				<th>PRODUCTO</th>
				<th>PRECIO</th>
				<th>CANTIDAD</th>
				<th>TOTAL</th>
			</tr>
			<?php $count=1; ?>
			@foreach($order->productDetails as $productDetail)
			<tr>
				<td>{{$count}}</td>
				<td><img src="{{ env('CDN_URL') }}/imagenes/800x600/{{$productDetail->imagen}}" alt="{{$productDetail->nombre}}" class="img-responsive"></td>
				<td>
					{{$productDetail->nombre}}
					@if(auth()->user()->userType!='1')
					@if($productDetail->is_stock_updated_in_ml=='1')
                    <span class="text-success" data-toggle="tooltip" title="" data-original-title="ML stock Updated"><i class="fa fa-circle"></i></span>
                    @else
                    <span class="text-danger" data-toggle="tooltip" title="" data-original-title="ML stock Not Update"><i class="fa fa-circle-thin"></i></span>
                    @endif
                    @endif
				</td>
				<td>${{$productDetail->itemPrice}}</td>
				<td>{{$productDetail->itemqty}}</td>
				<td>${{$productDetail->itemPrice * $productDetail->itemqty}}</td>
			</tr>
			<?php $count++; ?>
			@endforeach
		</table>
	</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
<script type="text/javascript">
	$('body').tooltip({
	    selector: '[data-toggle="tooltip"]'
	});
</script>
