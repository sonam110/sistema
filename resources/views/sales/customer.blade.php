@extends('layouts.master')
@section('content')

@if(Request::segment(1)==='customer-create' || Request::segment(1)==='customer-edit')
@if(Request::segment(1)==='customer-create')
<?php
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
?>
@else
<?php
$id             = $customer->id;
$name           = $customer->name;
$lastname       = $customer->lastname;
$email          = $customer->email;
$companyname    = $customer->companyname;
$address1       = $customer->address1;
$address2       = $customer->address2;
$city           = $customer->city;
$state          = $customer->state;
$country        = $customer->country;
$postcode       = $customer->postcode;
$phone          = $customer->phone;
$status         = $customer->status;
$doc_type       = $customer->doc_type;
$doc_number     = $customer->doc_number;
?>
@endif

@if(Auth::user()->hasAnyPermission(['customer-create','customer-edit']) || Auth::user()->hasRole('admin'))

{{ Form::open(array('route' => 'customer-save', 'class'=> 'form-horizontal','enctype'=>'multipart/form-data', 'files'=>true, 'autocomplete'=>'off')) }}
{!! Form::hidden('id',$id,array('class'=>'form-control')) !!}
@csrf
<div class="row row-deck">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    @if(Request::segment(1)==='customer-create')
                    Add
                    @else
                    Edit
                    @endif
                    Customer
                </h3>
                @can('customer-list')
                <div class="card-options">
                    <a href="{{ route('customer-list') }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Go To Back"><i class="fa fa-mail-reply"></i></a>
                </div>
                @endcan
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
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

                    <div class="col-md-3">
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

                    <div class="col-md-3">
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

                    <div class="col-md-3">
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

                    <div class="col-md-3">
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

                    <div class="col-md-3">
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

                    <div class="col-md-3">
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

                    <div class="col-md-3">
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
                <div class="form-footer">
                    {!! Form::submit('Save', array('class'=>'btn btn-primary btn-block')) !!}
                </div>
            </div>
        </div>
    </div>
</div>
{{ Form::close() }}

@endif
@elseif(Request::segment(1)==='customer-view')
@can('customer-view')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title ">Customer Information</h3>
                <div class="card-options">
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('customer-list') }}"> <i class="fa fa-plus"></i> Create Customer</a>
                    &nbsp;&nbsp;&nbsp;
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Go To Back"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-group">
                            <li class="list-group-item justify-content-between">
                                Name
                                <span class="badgetext">{{ $user->name }} {{ $user->lastname }}</span>
                            </li>

                            <li class="list-group-item justify-content-between">
                                Document Type
                                <span class="badgetext">{{ $user->doc_type }}</span>
                            </li>

                            <li class="list-group-item justify-content-between">
                                Document Number
                                <span class="badgetext">{{ $user->doc_number }}</span>
                            </li>

                            <li class="list-group-item justify-content-between">
                                Name
                                <span class="badgetext">{{ $user->name }} {{ $user->lastname }}</span>
                            </li>

                            <li class="list-group-item justify-content-between">
                                Email
                                <span class="badgetext">{{ $user->email }}</span>
                            </li>

                            <li class="list-group-item justify-content-between">
                                Phone
                                <span class="badgetext">{{ $user->phone }}</span>
                            </li>
                            <li class="list-group-item justify-content-between">
                                Company Name
                                <span class="badgetext">{{ $user->companyname }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group">
                            <li class="list-group-item justify-content-between">
                                Address 1
                                <span class="badgetext">{{ $user->address1 }}</span>
                            </li>
                            <li class="list-group-item justify-content-between">
                                Address 2
                                <span class="badgetext">{{ $user->address2 }}</span>
                            </li> 
                            <li class="list-group-item justify-content-between">
                                City
                                <span class="badgetext">{{ $user->city }}</span>
                            </li>
                            <li class="list-group-item justify-content-between">
                                State
                                <span class="badgetext">{{ $user->state }}</span>
                            </li>
                            <li class="list-group-item justify-content-between">
                                Country
                                <span class="badgetext">{{ $user->country }}</span>
                            </li>
                            <li class="list-group-item justify-content-between">
                                Post Code
                                <span class="badgetext">{{ $user->postcode }}</span>
                            </li>
                            <li class="list-group-item justify-content-between">
                                Status
                                @if($user->status=='1') 
                                <span class="badgetext text-danger">
                                    Inactive
                                </span>
                                @else
                                <span class="badgetext text-success">
                                    Active
                                </span>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endcan
@else
@can('customer-list')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title ">Customer Management</h3>
                <div class="card-options">
                    @can('customer-create')
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('customer-create') }}"> <i class="fa fa-plus"></i> Create New Customer</a>
                    @endcan
                    &nbsp;&nbsp;&nbsp;<a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Go To Back"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
            {{ Form::open(array('route' => 'customer-action', 'class'=> 'form-horizontal', 'autocomplete'=>'off')) }}
            @csrf
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">#</th>
                                <th>Name</th>
                                <th>Doc Type</th>
                                <th>Doc Number</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th scope="col"width="10%">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($customers as $key => $rows)
                            <tr>
                                <td>
                                    <label class="custom-control custom-checkbox">
                                        {{ Form::checkbox('boxchecked[]', $rows->id,'', array('class' => 'colorinput-input custom-control-input', 'id'=>'')) }}
                                        <span class="custom-control-label"></span>
                                    </label>
                                </td>
                                <td>{!!$key+1!!}</td>
                                <td>{!!$rows->name!!}</td>
                                <td>{!!$rows->doc_type!!}</td>
                                <td>{!!$rows->doc_number!!}</td>
                                <td>{!!$rows->email!!}</td>
                                <td>{!!$rows->phone!!}</td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-xs ">
                                        @if($rows->status=='1') 
                                        <span class="text-danger">Inactive</span>
                                        @else 
                                        <span class="text-success">Active</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-xs">
                                        @can('customer-view')
                                        <a class="btn btn-sm btn-secondary" href="{{ route('customer-view',base64_encode($rows->id)) }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="fa fa-eye"></i></a>
                                        @endcan
                                        @can('customer-edit')
                                        <a class="btn btn-sm btn-primary" href="{{ route('customer-edit',base64_encode($rows->id)) }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="fa fa-edit"></i></a>
                                        @endcan
                                        @can('customer-delete')
                                        <a class="btn btn-sm btn-danger" href="{{ route('customer-delete',base64_encode($rows->id)) }}" onClick="return confirm('Are you sure you want to delete this?');" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="fa fa-trash"></i></a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @can('customer-action')
                <div class="row div-margin">
                    <div class="col-md-3 col-sm-6 col-xs-6">
                        <div class="input-group"> 
                            <span class="input-group-addon">
                                <i class="fa fa-hand-o-right"></i> </span> 
                                {{ Form::select('cmbaction', array(
                                ''              => 'Action', 
                                'Active'        => 'Active',
                                'Inactive'      => 'Inactive',
                                'Delete'        => 'Delete'), 
                                '', array('class'=>'form-control','id'=>'cmbaction'))}} 
                            </div>
                        </div>
                        <div class="col-md-8 col-sm-6 col-xs-6">
                            <div class="input-group">
                                <button type="submit" class="btn btn-danger pull-right" name="Action" onClick="return delrec(document.getElementById('cmbaction').value);">Apply</button>
                            </div>
                        </div>
                    </div>
                    @endcan
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    @endcan
    @endif

    @endsection