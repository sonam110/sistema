<div class="row">
    <div class="col-md-12">
    	<div class="table-responsive">
        	<table class="table table-striped table-bordered">
                <tr>
                    <th width="20%">Pedido Nro.</th>
                    <td width="30%"><strong>{{$saleInfo->tranjectionid}}</strong></td>
                    <th width="20%">Fecha de Pedido</th>
                    <td>{{date('Y-m-d', strtotime($saleInfo->created_at))}}</td>
                </tr>
                <tr>
                    <th>Estado</th>
                    <td>{{$saleInfo->deliveryStatus}}</td>
                    <th>Shipping Guide</th>
                    <td>{{$saleInfo->shipping_guide}}</td>
                </tr>
                <tr>
                    <th>Final Invoice</th>
                    <td>{{$saleInfo->final_invoice}}</td>
                    <th>Monto Total</th>
                    <td><strong>${{$saleInfo->amount}}</strong></td>
                </tr>
                <tr>
                    <th>Iva ({{$saleInfo->tax_percentage}}%)</th>
                    <td><strong>${{$saleInfo->tax_amount}}</strong></td>
                    <th>Monto a pagar</th>
                    <td><strong>${{$saleInfo->payableAmount}}</strong></td>
                </tr>
                <tr>
                    <th>Monto Devuelto</th>
                    <td colspan="3"><strong>${{$saleInfo->totalReturnAmount()}}</strong></td>
                </tr>
            </table>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Forma de Pago</th>
                        <th>Importe</th>
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
                                <span class="text-left bolder">Banco:</span>
                                <span class="pull-right">{{$payment->bank_detail}}</span>
                            @elseif($payment->payment_mode=='Credit Card')
                                <span class="text-left bolder">Card Brand :</span>
                                <span class="pull-right">{{$payment->card_brand}}</span>
                                <br>
                                <span class="text-left bolder">Card Number:</span>
                                <span class="pull-right">{{$payment->card_number}}</span>
                            @elseif($payment->payment_mode=='Installment')
                                <span class="text-left bolder">Nro. de Cuotas:</span>
                                <span class="pull-right">{{$payment->no_of_installment}}</span>
                                <br>
                                <span class="text-left bolder">Importe de Cuotas:</span>
                                <span class="pull-right">${{$payment->installment_amount}}</span>
                                <br>
                                <span class="text-left bolder">Cuotas Pagas:</span>
                                <span class="pull-right">{{$payment->paid_installment}}</span>
                                <br>
                                <span class="text-left bolder">Cuotas Canceladas ?:</span>
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
                    <th width="20%">Nombre de Cliente</th>
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
                    <th>Observaciones</th>
                    <td colspan="3">{{$saleInfo->remark}}</td>
                </tr>
            </table>
        </div>
    </div>

</div>

<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr class="heading">
                        <th>#</th>
                        <th>Producto Agregado</th>
                        <th class="text-center">Cantidad Comprada</th>
                        <th class="text-center">Precio</th>
                        <th class="text-center">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($saleInfo->getBookeditemGeneric as  $key => $genProductDetail)
                    <tr class="item">
                        <td>
                            {{$key+1}}
                        </td>
                        <td>
                            {{$genProductDetail->item_name}}
                        </td>
                        <td>
                            <center>{{$genProductDetail->itemqty}}</center>
                        </td>
                        <td>
                            <center>${{number_format($genProductDetail->itemPrice, 2, '.', ',')}}</center>
                        </td>

                        <td>
                            <center>${{number_format($genProductDetail->itemPrice * $genProductDetail->itemqty, 2, '.', ',')}}</center>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
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
                        <th>Producto</th>
                        <th width="10%" class="text-center">Cantidad Comprada</th>
                        <th width="10%" class="text-center">Precio</th>
                        <th width="10%" class="text-center">Cantidad Devuelta</th>
                        <th width="10%" class="text-center">Máximo a Devolver</th>
                        <th width="17%">Devolver</th>
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
                            {!! Form::hidden('bookeditem_id[]',$productDetail->id,array('id'=>'bookeditem_id'.$key,'class'=> 'form-control')) !!}
                            {!! Form::hidden('producto_id[]',$productDetail->itemid,array('id'=>'producto_id'.$key,'class'=> 'form-control')) !!}
                        </td>
                        <td class="text-center">
                        	<strong>{{$productDetail->itemqty}}</strong>
                        </td>
                        <td class="text-center">
                            {{$productDetail->itemPrice}}
                            {!! Form::hidden('itemPrice[]',$productDetail->itemPrice,array('id'=>'itemPrice'.$key,'class'=> 'form-control')) !!}
                        </td>
                        <td class="text-center">
                            <strong>{{$productDetail->return_qty}}</strong>
                        </td>
                        <td class="text-center">
                            <strong>
                            	{{($productDetail->itemqty- $productDetail->return_qty)}}
                            </strong>
                        </td>
                        <td>
                        	<span @if($productDetail->itemqty == $productDetail->return_qty) hidden @endif>
                        		{!! Form::number('return_qty[]',null,array('id'=>'return_qty'.$key,'class'=> $errors->has('return_qty') ? 'form-control is-invalid state-invalid return_qty' : 'form-control return_qty', 'placeholder'=>'Cant. Devuelta', 'autocomplete'=>'off','min'=>'1','max'=> ($productDetail->itemqty-$productDetail->return_qty))) !!}
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
            <label for="return_note" class="form-label">Nota de Devolución</label>
            {!! Form::text('return_note',null,array('id'=>'return_note','class'=> $errors->has('return_note') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Nota Devolución', 'autocomplete'=>'off')) !!}
        </div>
    </div>
</div>
