{{ Form::open(array('route' => 'api.save-sales-order-modal', 'class'=> 'form-horizontal','enctype'=>'multipart/form-data', 'files'=>true, 'autocomplete'=>'off')) }}
@csrf
<div class="modal-header pd-x-20">
	<h6 class="modal-title"><strong>Edit Observaciones / Shipping Guide / Final Invoice </strong></h6>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
@if(empty($error))
<div class="modal-body pd-20">
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				Error!!! Data not found...
			</div>
		</div>
	</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-sm btn-outline-success" data-dismiss="modal">Close</button>
</div>
@else
{!! Form::hidden('id',base64_encode($booking->id),array('class'=>'form-control')) !!}
<div class="modal-body pd-20">
	<div class="row">
		@if(empty($booking->shipping_guide))
		<div class="col-xs-12 col-sm-6 col-md-6">
            <label class="custom-control custom-checkbox">
                {{ Form::checkbox('shipping_guide', 1, false, array('class' => 'custom-control-input', 'id'=>'shipping_guide_chk')) }}
                <span class="custom-control-label">Shipping Guide</span>
            </label>
        </div>
        @else
        <div class="col-xs-12 col-sm-6 col-md-6">
            <label>
                <span>Shipping Guide: </span>
                <strong>{{$booking->shipping_guide}}</strong>
            </label>
        </div>
        @endif

        @if(empty($booking->final_invoice))
        <div class="col-xs-12 col-sm-6 col-md-6">
            <label class="custom-control custom-checkbox">
            	{{ Form::checkbox('final_invoice', 1, false, array('class' => 'custom-control-input', 'id'=>'final_invoice_chk')) }}
                <span class="custom-control-label">Final Invoice</span>
            </label>
        </div>
        @else
        <div class="col-xs-12 col-sm-6 col-md-6">
            <label>
                <span>Final Invoice: </span>
                <strong>{{$booking->final_invoice}}</strong>
            </label>
        </div>
        @endif

		<div class="col-md-12">
			<div class="form-group">
				<label for="orderNote" class="form-label">Observaciones</label>
				{!! Form::textarea('orderNote',$booking->orderNote,array('id'=>'orderNote','class'=> $errors->has('orderNote') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Observaciones', 'autocomplete'=>'off')) !!}
				@if ($errors->has('orderNote'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('orderNote') }}</strong>
				</span>
				@endif
			</div>
		</div>


	</div>
</div>
<div class="modal-footer">
	{!! Form::submit('Guardar', array('class'=>'btn btn-sm btn-outline-primary')) !!}
	<button type="button" class="btn btn-sm btn-outline-success" data-dismiss="modal">Close</button>
</div>
@endif
{{ Form::close() }}
