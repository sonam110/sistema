@php
$id             = '';
$name           = '';
$lastname       = '';
$email          = '';
$companyname    = '';
$address1       = '';
$address2       = '';
$city           = '';
$state          = '';
$country        = '';
$postcode       = '';
$phone          = '';
$status         = '';
$doc_type       = '';
$doc_number     = '';
@endphp
{{ Form::open(array('route' => 'customer-save', 'class'=> 'form-horizontal','enctype'=>'multipart/form-data', 'files'=>true, 'autocomplete'=>'off')) }}
{!! Form::hidden('id',$id,array('class'=>'form-control')) !!}
@csrf
<div class="modal-header pd-x-20">
	<h6 class="modal-title"><strong>Add New Customer</strong></h6>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<div class="modal-body pd-20">
	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
				<label for="name" class="form-label">First Name <span class="text-danger">*</span></label>
				{!! Form::text('name',$name,array('id'=>'name','class'=> $errors->has('name') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'First Name', 'autocomplete'=>'off','required'=>'required')) !!}
				@if ($errors->has('name'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('name') }}</strong>
				</span>
				@endif
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label for="lastname" class="form-label">Last Name <span class="text-danger">*</span></label>
				{!! Form::text('lastname',$lastname,array('id'=>'lastname','class'=> $errors->has('lastname') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Last Name', 'autocomplete'=>'off','required'=>'required')) !!}
				@if ($errors->has('lastname'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('lastname') }}</strong>
				</span>
				@endif
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label for="doc_type" class="form-label">Document Type <span class="text-danger">*</span></label>
				{!! Form::select('doc_type', [
				'DNI'       => 'DNI',
				'CUIT'      => 'CUIT',
				'PASSPORT'  => 'PASSPORT',
				], $doc_type, array('class' => 'form-control','placeholder'=>'-- Document Type --','required'=>'required')) !!}
				@if ($errors->has('doc_type'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('doc_type') }}</strong>
				</span>
				@endif
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label for="doc_number" class="form-label">Document Number <span class="text-danger">*</span></label>
				{!! Form::text('doc_number', $doc_number, array('class' => 'form-control','required'=>'required')) !!}
				@if ($errors->has('doc_number'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('doc_number') }}</strong>
				</span>
				@endif
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
				{!! Form::text('email',$email,array('id'=>'email','class'=> $errors->has('email') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Email Address', 'autocomplete'=>'off','required'=>'required')) !!}
				@if ($errors->has('email'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('email') }}</strong>
				</span>
				@endif
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label for="companyname" class="form-label">Company Name</label>
				{!! Form::text('companyname',$companyname,array('id'=>'companyname','class'=> $errors->has('companyname') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Company Name', 'autocomplete'=>'off')) !!}
				@if ($errors->has('companyname'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('companyname') }}</strong>
				</span>
				@endif
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label for="address1" class="form-label">Address1</label>
				{!! Form::text('address1',$address1,array('id'=>'address1','class'=> $errors->has('address1') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Address', 'autocomplete'=>'off')) !!}
				@if ($errors->has('address1'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('address1') }}</strong>
				</span>
				@endif
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label for="address2" class="form-label">Address2</label>
				{!! Form::text('address2',$address2,array('id'=>'address2','class'=> $errors->has('address2') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Address2', 'autocomplete'=>'off')) !!}
				@if ($errors->has('address2'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('address2') }}</strong>
				</span>
				@endif
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label for="city" class="form-label">City</label>
				{!! Form::text('city',$city,array('id'=>'city','class'=> $errors->has('city') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'City', 'autocomplete'=>'off')) !!}
				@if ($errors->has('city'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('city') }}</strong>
				</span>
				@endif
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label for="state" class="form-label">State</label>
				{!! Form::text('state',$state,array('id'=>'state','class'=> $errors->has('state') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'State', 'autocomplete'=>'off')) !!}
				@if ($errors->has('state'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('state') }}</strong>
				</span>
				@endif
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label for="country" class="form-label">Country</label>
				{!! Form::text('country',$country,array('id'=>'country','class'=> $errors->has('country') ? 'form-control is-invalid country-invalid' : 'form-control', 'placeholder'=>'Country', 'autocomplete'=>'off')) !!}
				@if ($errors->has('country'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('country') }}</strong>
				</span>
				@endif
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label for="postcode" class="form-label">Post code</label>
				{!! Form::text('postcode',$postcode,array('id'=>'postcode','class'=> $errors->has('postcode') ? 'form-control is-invalid postcode-invalid' : 'form-control', 'placeholder'=>'Post code', 'autocomplete'=>'off')) !!}
				@if ($errors->has('postcode'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('postcode') }}</strong>
				</span>
				@endif
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
				{!! Form::text('phone',$phone,array('id'=>'phone','class'=> $errors->has('phone') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Phone', 'autocomplete'=>'off','required'=>'required')) !!}
				@if ($errors->has('phone'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('phone') }}</strong>
				</span>
				@endif
			</div>
		</div>


		<div class="col-md-4">
			<div class="form-group">
				<label for="status" class="form-label">Status <span class="text-danger">*</span></label>
				{!! Form::select('status', [
				'0' => 'Active',
				'1' => 'Inative',
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
	{!! Form::submit('Save', array('class'=>'btn btn-sm btn-outline-primary')) !!}
	<button type="button" class="btn btn-sm btn-outline-success" data-dismiss="modal">Close</button>
</div>
{{ Form::close() }}