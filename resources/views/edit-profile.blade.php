@extends('layouts.master')
@section('extracss')
{!! Html::style('assets/js/bootstrap-fileupload/bootstrap-fileupload.css') !!}
@endsection
@section('content')

<!-- <div class="page-header">
	<ol class="breadcrumb breadcrumb-arrow mt-3">
		<li><a href="{{route('dashboard') }}">Dashboard</a></li>
		<li class="active"><span>Edit Profile</span></li>
	</ol>
</div> -->
<div class="row row-deck">
	<div class="col-lg-4">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Change Password</h3>
			</div>
			<div class="card-body">
				{{ Form::open(array('route' => 'change-password', 'class'=> 'form-horizontal')) }}
				@csrf
					<div class="row mb-2">
						<div class="">
							<span class="avatar brround avatar-xl cover-image" data-image-src=""></span>
						</div>
						<div class="text-center">
							<h3 class="mb-1 ">{{$user->name}}</h3>
							<span>{{$user->email}}</span>
						</div>
					</div>

					<div class="form-group" hidden="">
						<label for="email" class="form-label">Email-Address</label>
						{!! Form::text('email',$user->email,array('id'=>'email','class'=> $errors->has('email') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Email-Address', 'autocomplete'=>'off','required'=>'required','readonly')) !!}
					</div>
					<div class="form-group">
						<label for="old_password" class="form-label">Old Password</label>
						{!! Form::password('old_password',array('id'=>'old_password','class'=> $errors->has('old_password') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Old Password', 'autocomplete'=>'off','required'=>'required')) !!}
						@if ($errors->has('old_password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('old_password') }}</strong>
                        </span>
                        @endif
					</div>

					<div class="form-group">
						<label for="new_password" class="form-label">New Password</label>
						{!! Form::password('new_password',array('id'=>'new_password','class'=>$errors->has('new_password') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'New Password', 'autocomplete'=>'off','required'=>'required')) !!}
						@if ($errors->has('new_password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('new_password') }}</strong>
                        </span>
                        @endif
					</div>

					<div class="form-group">
						<label for="new_password_confirmation" class="form-label">Confirm Password</label>
						{!! Form::password('new_password_confirmation',array('id'=>'new_password_confirmation','class'=>$errors->has('new_password_confirmation') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Confirm Password', 'autocomplete'=>'off','required'=>'required')) !!}
						@if ($errors->has('new_password_confirmation'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('new_password_confirmation') }}</strong>
                        </span>
                        @endif
					</div>

					<div class="form-footer">
						{!! Form::submit('Update Password', array('class'=>'btn btn-primary btn-block')) !!}
					</div>
				{{ Form::close() }}
			</div>
		</div>
	</div>
	<div class="col-lg-8">
		{{ Form::open(array('route' => 'update-profile', 'class'=> 'card','enctype'=>'multipart/form-data', 'files'=>true)) }}
		@csrf
		<input type="hidden" name="oldAvatar" value="{{ $user->avatar }}">
			<div class="card-header">
				<h3 class="card-title">Edit Profile</h3>
				<div class="card-options">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Go To Back"><i class="fa fa-mail-reply"></i></a>
                </div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-sm-6 col-md-6">
						<div class="form-group">
							<label for="name" class="form-label">Name</label>
							{!! Form::text('name',$user->name,array('id'=>'name','class'=> $errors->has('name') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Name', 'autocomplete'=>'off','required'=>'required')) !!}
							@if ($errors->has('name'))
	                        <span class="invalid-feedback" role="alert">
	                            <strong>{{ $errors->first('name') }}</strong>
	                        </span>
	                        @endif
						</div>
					</div>

					<div class="col-sm-6 col-md-6">
						<div class="form-group">
							<label for="companyname" class="form-label">Company Name</label>
							{!! Form::text('companyname',$user->companyname,array('id'=>'companyname','class'=> $errors->has('companyname') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Company Name', 'autocomplete'=>'off','required'=>'required')) !!}
							@if ($errors->has('companyname'))
	                        <span class="invalid-feedback" role="alert">
	                            <strong>{{ $errors->first('companyname') }}</strong>
	                        </span>
	                        @endif
						</div>
					</div>

					<div class="col-md-12">
						<div class="form-group">
							<label for="address" class="form-label">Address</label>
							{!! Form::text('address',$user->address1,array('id'=>'address','class'=> $errors->has('address') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Address', 'autocomplete'=>'off','required'=>'required')) !!}
							@if ($errors->has('address'))
	                        <span class="invalid-feedback" role="alert">
	                            <strong>{{ $errors->first('address') }}</strong>
	                        </span>
	                        @endif
						</div>
					</div>
					<div class="col-sm-6 col-md-4">
						<div class="form-group">
							<label for="city" class="form-label">City</label>
							{!! Form::text('city',$user->city,array('id'=>'city','class'=> $errors->has('city') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'City', 'autocomplete'=>'off','required'=>'required')) !!}
							@if ($errors->has('city'))
	                        <span class="invalid-feedback" role="alert">
	                            <strong>{{ $errors->first('city') }}</strong>
	                        </span>
	                        @endif
						</div>
					</div>

					<div class="col-sm-6 col-md-4">
						<div class="form-group">
							<label for="mobile" class="form-label">Mobile</label>
							{!! Form::number('mobile',$user->phone,array('id'=>'mobile','class'=> $errors->has('mobile') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Mobile', 'autocomplete'=>'off','required'=>'required')) !!}
							@if ($errors->has('mobile'))
	                        <span class="invalid-feedback" role="alert">
	                            <strong>{{ $errors->first('mobile') }}</strong>
	                        </span>
	                        @endif
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label for="locktimeout" class="form-label">App Auto Lock Time</label>
							{!! Form::number('locktimeout',$user->locktimeout,array('id'=>'locktimeout','class'=> $errors->has('locktimeout') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'10', 'autocomplete'=>'off', 'min'=>'10')) !!}
							@if ($errors->has('locktimeout'))
	                        <span class="invalid-feedback" role="alert">
	                            <strong>{{ $errors->first('locktimeout') }}</strong>
	                        </span>
	                        @endif
						</div>
					</div>

				</div>
			</div>
			<div class="card-footer text-right">
				{!! Form::submit('Update Profile', array('class'=>'btn btn-primary')) !!}
			</div>
		{{ Form::close() }}
	</div>
</div>
@endsection
@section('extracss')

@endsection
