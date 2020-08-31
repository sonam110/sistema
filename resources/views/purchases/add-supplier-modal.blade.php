@php
$id             = '';
$name           = '';
$email          = '';
$company_name   = '';
$address        = '';
$phone          = '';
$city           = '';
$state          = '';
$vat_number     = '';
$status         = '';
@endphp
{{ Form::open(array('route' => 'supplier-save', 'class'=> 'form-horizontal','enctype'=>'multipart/form-data', 'files'=>true, 'autocomplete'=>'off')) }}
{!! Form::hidden('id',$id,array('class'=>'form-control')) !!}
@csrf
<div class="modal-header pd-x-20">
	<h6 class="modal-title"><strong>Agregar Proveedor</strong></h6>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<div class="modal-body pd-20">
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
				{!! Form::text('name',$name,array('id'=>'name','class'=> $errors->has('name') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Nombre', 'autocomplete'=>'off','required'=>'required')) !!}
				@if ($errors->has('name'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('name') }}</strong>
				</span>
				@endif
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="email" class="form-label">Email <span class="text-danger">*</span></label>
				{!! Form::text('email',$email,array('id'=>'email','class'=> $errors->has('email') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Email', 'autocomplete'=>'off','required'=>'required')) !!}
				@if ($errors->has('email'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('email') }}</strong>
				</span>
				@endif
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label for="company_name" class="form-label">Compañía</label>
				{!! Form::text('company_name',$company_name,array('id'=>'company_name','class'=> $errors->has('company_name') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Compañía', 'autocomplete'=>'off')) !!}
				@if ($errors->has('company_name'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('company_name') }}</strong>
				</span>
				@endif
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label for="phone" class="form-label">Teléfono <span class="text-danger">*</span></label>
				{!! Form::text('phone',$phone,array('id'=>'phone','class'=> $errors->has('phone') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Teléfono', 'autocomplete'=>'off','required'=>'required')) !!}
				@if ($errors->has('phone'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('phone') }}</strong>
				</span>
				@endif
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label for="address" class="form-label">Domicilio <span class="text-danger">*</span></label>
				{!! Form::text('address',$address,array('id'=>'address','class'=> $errors->has('address') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Domicilio', 'autocomplete'=>'off','required'=>'required')) !!}
				@if ($errors->has('address'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('address') }}</strong>
				</span>
				@endif
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label for="city" class="form-label">Ciudad <span class="text-danger">*</span></label>
				{!! Form::text('city',$city,array('id'=>'city','class'=> $errors->has('city') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'City', 'autocomplete'=>'off','required'=>'required')) !!}
				@if ($errors->has('city'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('city') }}</strong>
				</span>
				@endif
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label for="state" class="form-label">Estado <span class="text-danger">*</span></label>
				{!! Form::text('state',$state,array('id'=>'state','class'=> $errors->has('state') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Estado', 'autocomplete'=>'off','required'=>'required')) !!}
				@if ($errors->has('state'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('state') }}</strong>
				</span>
				@endif
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label for="vat_number" class="form-label">CUIT</label>
				{!! Form::text('vat_number',$vat_number,array('id'=>'vat_number','class'=> $errors->has('vat_number') ? 'form-control is-invalid vat_number-invalid' : 'form-control', 'placeholder'=>'C.U.I.T', 'autocomplete'=>'off')) !!}
				@if ($errors->has('vat_number'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('vat_number') }}</strong>
				</span>
				@endif
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
				{!! Form::select('status', [
				'1' => 'Active',
				'0' => 'Inative',
				], $status, array('class' => 'form-control')) !!}
				@if ($errors->has('status'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('status') }}</strong>
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
{{ Form::close() }}
