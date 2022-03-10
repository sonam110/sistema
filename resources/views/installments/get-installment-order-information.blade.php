<div class="row">
    <div class="col-md-12">
    	<div class="table-responsive">
        	<table class="table table-striped table-bordered">
                <tr>
                    <th width="20%">Orden No.</th>
                    <td width="30%"><strong>{{$saleInfo->tranjectionid}}</strong></td>
                    <th width="20%">Fecha de Pedido</th>
                    <td>{{date('Y-m-d', strtotime($saleInfo->created_at))}}</td>
                </tr>
                <tr>
                    <th>Estado</th>
                    <td>{{$saleInfo->deliveryStatus}}</td>
                    <th>Monto Total</th>
                    <td><strong>${{$saleInfo->amount}}</strong></td>
                </tr>
                <tr>
                    <th>Costo de envío</th>
                    <td><strong>${{$saleInfo->shipping_charge}}</strong></td>
                    <th>Iva ({{$saleInfo->tax_percentage}}%)</th>
                    <td><strong>${{$saleInfo->tax_amount}}</strong></td>
                </tr>
                <tr>
                    <th>Total a Pagar</th>
                    <td><strong>${{$saleInfo->payableAmount}}</strong></td>
                    <th>Monto devuelto</th>
                    <td><strong>${{$saleInfo->totalReturnAmount()}}</strong></td>
                </tr>
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
                    <th>Observación del pedido</th>
                    <td colspan="3">{{$saleInfo->remark}}</td>
                </tr>
            </table>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Modo de Pago</th>
                        <th>Monto</th>
                        <th colspan="3">Pago</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($saleInfo->bookingPaymentThroughs as $key => $payment)
                    <tr class="item @if($payment->payment_mode=='Installment') highlight-section @endif ">
                        <th>{{$payment->payment_mode}}</th>
                        <td>
                            <strong>${{$payment->amount}}
                            </strong>
                        </td>
                        <td colspan="3" width="60%">
                            @if($payment->payment_mode=='Cheque')
                                <span class="text-left bolder">Cheque No. :</span>
                                <span class="pull-right">{{$payment->cheque_number}}</span>
                                <br>
                                <span class="text-left bolder">Banco:</span>
                                <span class="pull-right">{{$payment->bank_detail}}</span>
                            @elseif($payment->payment_mode=='Installment')
                                <span class="text-left bolder">No. of Cuota:</span>
                                <span class="pull-right">{{$payment->no_of_installment}}</span>
                                <br>
                                <span class="text-left bolder">Monto Cuota:</span>
                                <span class="pull-right">${{$payment->installment_amount}}</span>
                                <br>
                                @if($payment->installment_partial_amount>0) 
                                <span class="text-left bolder">Pago parcial:</span>
                                <span class="pull-right">${{$payment->installment_amount - $payment->installment_partial_amount}}</span>
                                <br>
                                @endif
                                <span class="text-left bolder">Pago de Cuota:</span>
                                <span class="pull-right">{{$payment->paid_installment}}</span>
                                <br>
                                <span class="text-left bolder">Cuotas Canceladas ?:</span>
                                <span class="pull-right">{!!($payment->is_installment_complete=='1' ? '<span class="badge badge-success">Si</span>' : '<span class="badge badge-danger">No</span>')!!}</span>
                            @endif
                        </td>
                    </tr>
                    @if($payment->payment_mode=='Installment' && $payment->is_installment_complete=='0')
                    <tr class="highlight-section">
                                  		<th>
			                        			{!! Form::select('payment_mode',[
		                                    		'Debit Card'  	=> 'Débito',
		                                    		'Credit Card' 	=> 'Crédito',
		                                    		'Cash' 			=> 'Efectivo',
		                                    		'Cheque' 		=> 'Cheques',
													'Transfers' 	=> 'Transferencias',
		            		                        ],null,array('id'=>'payment_mode','class'=> $errors->has('payment_mode') ? 'form-control is-invalid state-invalid payment_mode' : 'form-control payment_mode', 'autocomplete'=>'off','onchange'=>'paymentCheckInput(this)')) !!}
			                        		</th>
			                        		<th>
                              <input type="number" class="form-control length" value=@if($payment->installment_partial_amount>0) {{$payment->installment_partial_amount}} @else {{$payment->installment_amount}} @endif id="paymentAmount">
			                        		</th>
                                            <th>
                                <a href="javascript:void(0)" class="btn btn-success btn-block" onClick="cusPay('{{base64_encode($saleInfo->id)}}','{{base64_encode($payment->id)}}')" 
                                    data-toggle="tooltip" data-placement="top" title="">
                                    <i class="fe fe-check mr-2"></i> Recibir
                                </a>
                                            
                                            </th>
                           </tr>                 
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
  function cusPay(b,p)
  {
  if (confirm('Está seguro que desea recibir este $'+$('#paymentAmount').val()+' ?'))
  {
  window.location="{{route('installment-receive-save')}}?bookingId="+b+"&paymentThroughId="+p+
    "&amount="+$('#paymentAmount').val()+'&payment_mode='+$('#payment_mode').val(); 
  /*
  $.ajax({
      url: "{{route('installment-receive-save')}}",
      type: "POST",
      data: "bookingId="+b+"&paymentThroughId="+p+"&amount="+$('#paymentAmount').val(),
      success:function(info){
        alert(info);
        //$('#selected-b-or-m-list').html(info);
      }
  });*/
  }  
  }
  
</script>

