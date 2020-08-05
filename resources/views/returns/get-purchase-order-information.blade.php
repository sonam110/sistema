<div class="row">
    <div class="col-md-12">
    	<div class="table-responsive">
        	<table class="table table-striped table-bordered">
                <tr>
                    <th>PO No.</th>
                    <td>{{$poInfo->po_no}}</td>
                    <th>PO Date</th>
                    <td>{{date('Y-m-d', strtotime($poInfo->po_date))}}</td>
                </tr>
                <tr>
                    <th>PO Status</th>
                    <td>{{$poInfo->po_status}}</td>
                    <th>PO Completed Date</th>
                    <td>{{$poInfo->po_completed_date}}</td>
                </tr>
                <tr>
                    <th>Tax ({{$poInfo->tax_percentage}}%)</th>
                    <td><strong>${{$poInfo->tax_amount}}</strong></td>
                    <th>Payable Amount</th>
                    <td><strong>${{$poInfo->gross_amount}}</strong></td>
                </tr>
                <tr>
                    <th>Returned Amount</th>
                    <td colspan="3"><strong>${{$poInfo->totalReturnAmount()}}</strong></td>
                </tr>
                
                <tr>
                    <th>Supplier Name</th>
                    <td>{{$poInfo->supplier->name}}</td>
                    <th>Company Name</th>
                    <td>{{$poInfo->supplier->company_name}}</td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td colspan="3">
                    	{{$poInfo->supplier->address}}, {{$poInfo->supplier->city}}, {{$poInfo->supplier->state}}
                    </td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>{{$poInfo->supplier->phone}}</td>
                    <th>Vat No.</th>
                    <td>{{$poInfo->supplier->vat_number}}</td>
                </tr>

                <tr>
                    <th>Order Remark</th>
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
                        <th>Product Name</th>
                        <th width="10%" class="text-center">Required Qty</th>
                        <th width="10%" class="text-center">Price</th>
                        <th width="10%" class="text-center">Accepted Qty</th>
                        <th width="10%" class="text-center">Returned Qty</th>
                        <th width="10%" class="text-center">Return Max Qty</th>
                        <th width="17%">Return qty</th>
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
                        		{!! Form::number('return_qty[]',null,array('id'=>'return_qty'.$key,'class'=> $errors->has('return_qty') ? 'form-control is-invalid state-invalid return_qty' : 'form-control return_qty', 'placeholder'=>'Return qty', 'autocomplete'=>'off','min'=>'1','max'=> ($productDetail->accept_qty-$productDetail->return_qty))) !!}
                        	</span>
                            <span @if($productDetail->accept_qty != $productDetail->return_qty) hidden @endif class="text-danger">
                            	Not Allowed
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
            <label for="return_note" class="form-label">Return Note</label>
            {!! Form::text('return_note',null,array('id'=>'return_note','class'=> $errors->has('return_note') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Return Note', 'autocomplete'=>'off')) !!}
        </div>
    </div>
</div>