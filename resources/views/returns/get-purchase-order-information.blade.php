<div class="row">
    <div class="col-md-12">
    	<div class="table-responsive">
        	<table class="table table-striped table-bordered">
                <tr>
                    <th>OC Nro.</th>
                    <td>{{$poInfo->po_no}}</td>
                    <th>OC Fecha</th>
                    <td>{{date('Y-m-d', strtotime($poInfo->po_date))}}</td>
                </tr>
                <tr>
                    <th>OC Estado</th>
                    <td>{{$poInfo->po_status}}</td>
                    <th>OC Completada día</th>
                    <td>{{$poInfo->po_completed_date}}</td>
                </tr>
                <tr>
                    <th>Iva ({{$poInfo->tax_percentage}}%)</th>
                    <td><strong>${{$poInfo->tax_amount}}</strong></td>
                    <th>Monto a pagar</th>
                    <td><strong>${{$poInfo->gross_amount}}</strong></td>
                </tr>
                <tr>
                    <th>Monto Devuelto</th>
                    <td colspan="3"><strong>${{$poInfo->totalReturnAmount()}}</strong></td>
                </tr>

                <tr>
                    <th>Vendedor</th>
                    <td>{{$poInfo->supplier->name}}</td>
                    <th>Compañía</th>
                    <td>{{$poInfo->supplier->company_name}}</td>
                </tr>
                <tr>
                    <th>Domicilio</th>
                    <td colspan="3">
                    	{{$poInfo->supplier->address}}, {{$poInfo->supplier->city}}, {{$poInfo->supplier->state}}
                    </td>
                </tr>
                <tr>
                    <th>Teléfono</th>
                    <td>{{$poInfo->supplier->phone}}</td>
                    <th>Vat No.</th>
                    <td>{{$poInfo->supplier->vat_number}}</td>
                </tr>

                <tr>
                    <th>Observaciones</th>
                    <td colspan="3">{{$poInfo->remark}}</td>
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
                        <th>Producto</th>
                        <th width="10%" class="text-center">Cant. Requireda</th>
                        <th width="10%" class="text-center">Precio</th>
                        <th width="10%" class="text-center">Cant. Aceptada</th>
                        <th width="10%" class="text-center">Cantidad Devuelta</th>
                        <th width="10%" class="text-center">Máximo a Devolver</th>
                        <th width="17%">Devolver</th>
                    </tr>
                </thead>
                <tbody>
                	@foreach($poInfo->purchaseOrderProducts as $key => $productDetail)
                    <tr class="add-sec">
                        <td>
                            {{$key+1}}
                        </td>
                        <td>
                            {{$productDetail->producto->nombre}}
                            {!! Form::hidden('purchase_order_product_id[]',$productDetail->id,array('id'=>'purchase_order_product_id'.$key,'class'=> 'form-control')) !!}
                            {!! Form::hidden('producto_id[]',$productDetail->producto_id,array('id'=>'producto_id'.$key,'class'=> 'form-control')) !!}
                        </td>
                        <td class="text-center">
                        	{{$productDetail->required_qty}}
                        </td>
                        <td class="text-center">
                            {{$productDetail->price}}
                            {!! Form::hidden('return_price[]',$productDetail->price,array('id'=>'return_price'.$key,'class'=> 'form-control')) !!}
                        </td>
                        <td class="text-center">
                            {{$productDetail->accept_qty}}
                        </td>
                        <td class="text-center">
                            {{$productDetail->return_qty}}
                        </td>
                        <td class="text-center">
                            <strong>
                            	{{($productDetail->accept_qty- $productDetail->return_qty)}}
                            </strong>
                        </td>
                        <td>
                        	@php $remainingQty = $productDetail->required_qty - ($productDetail->accept_qty + $productDetail->return_qty) @endphp
                        	<span @if($productDetail->accept_qty == $productDetail->return_qty) hidden @endif>
                        		{!! Form::number('return_qty[]',null,array('id'=>'return_qty'.$key,'class'=> $errors->has('return_qty') ? 'form-control is-invalid state-invalid return_qty' : 'form-control return_qty', 'placeholder'=>'Cant. Devuelta', 'autocomplete'=>'off','min'=>'1','max'=> ($productDetail->accept_qty-$productDetail->return_qty))) !!}
                        	</span>
                            <span @if($productDetail->accept_qty != $productDetail->return_qty) hidden @endif class="text-danger">
                            	No Permitido
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
