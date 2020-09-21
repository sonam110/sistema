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
                    <th>Iva ({{$saleInfo->tax_percentage}}%)</th>
                    <td><strong>${{$saleInfo->tax_amount}}</strong></td>
                    <th>Total a Pagar</th>
                    <td><strong>${{$saleInfo->payableAmount}}</strong></td>
                </tr>
                <tr>
                    <th>Monto devuelto</th>
                    <td colspan="3"><strong>${{$saleInfo->totalReturnAmount()}}</strong></td>
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
                        <th></th>
                        <th>Pago</th>
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
                        <td>
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
                                <span class="text-left bolder">Pago de Cuota:</span>
                                <span class="pull-right">{{$payment->paid_installment}}</span>
                                <br>
                                <span class="text-left bolder">Cuotas Canceladas ?:</span>
                                <span class="pull-right">{!!($payment->is_installment_complete=='1' ? '<span class="badge badge-success">Si</span>' : '<span class="badge badge-danger">No</span>')!!}</span>
                            @endif
                        </td>
                        <td>
                            @if($payment->payment_mode=='Installment' && $payment->is_installment_complete=='0')
                            <div class="form-footer">
                                <a href="{{route('installment-receive-save',['bookingId'=>base64_encode($saleInfo->id),'paymentThroughId'=>base64_encode($payment->id)])}}" class="btn btn-success btn-block" onClick="return confirm('Está seguro que desea recibir este ${{$payment->installment_amount}} EMI?');" data-toggle="tooltip" data-placement="top" title="" data-original-title="Recibe ${{$payment->installment_amount}}">
                                    <i class="fe fe-check mr-2"></i> Receibido ${{$payment->installment_amount}}
                                </a>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
