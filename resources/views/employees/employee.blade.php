@extends('layouts.master')
@section('content')

@if(Request::segment(1)==='employee-create' || Request::segment(1)==='employee-edit')
@if(Request::segment(1)==='employee-create')
<?php
$id             = '';
$name           = '';
$email          = '';
$address1       = '';
$phone          = '';
$status         = '';
$userRole       = '';
?>
@else
<?php
$id             = $user->id;
$name           = $user->name;
$email          = $user->email;
$address1       = $user->address1;
$phone          = $user->phone;
$status         = $user->status;
$userRole       = $userRole;
?>
@endif

@if(Auth::user()->hasAnyPermission(['employee-create','employee-edit']) || Auth::user()->hasRole('admin'))

{{ Form::open(array('route' => 'employee-save', 'class'=> 'form-horizontal','enctype'=>'multipart/form-data', 'files'=>true, 'autocomplete'=>'off')) }}
{!! Form::hidden('id',$id,array('class'=>'form-control')) !!}
@csrf
<div class="row row-deck">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    @if(Request::segment(1)==='employee-create')
                    Add
                    @else
                    Edit
                    @endif
                    Employee
                </h3>
                @can('employee-list')
                <div class="card-options">
                    <a href="{{ route('employee-list') }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
                </div>
                @endcan
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                            {!! Form::text('name',$name,array('id'=>'name','class'=> $errors->has('name') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Name', 'autocomplete'=>'off','required'=>'required')) !!}
                            @if ($errors->has('name'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email" class="form-label">Email  <span class="text-danger">*</span></label>
                            {!! Form::text('email',$email,array('id'=>'email','class'=> $errors->has('email') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Email Address', 'autocomplete'=>'off','required'=>'required')) !!}
                            @if ($errors->has('email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password" class="form-label">Contraseña</label>
                            {!! Form::password('password',array('id'=>'password','class'=> $errors->has('password') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Password', 'autocomplete'=>'off')) !!}
                            @if ($errors->has('password'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="confirm-password" class="form-label">Confirme Contrasea</label>
                            {!! Form::password('confirm-password',array('id'=>'confirm-password','class'=> $errors->has('confirm-password') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Confirm Password', 'autocomplete'=>'off')) !!}
                            @if ($errors->has('confirm-password'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('confirm-password') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone" class="form-label">Mobil <span class="text-danger">*</span></label>
                            {!! Form::text('phone',$phone,array('id'=>'phone','class'=> $errors->has('phone') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Mobile', 'autocomplete'=>'off','required'=>'required')) !!}
                            @if ($errors->has('phone'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('phone') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="address1" class="form-label">Domicilio <span class="text-danger">*</span></label>
                            {!! Form::text('address1',$address1,array('id'=>'address1','class'=> $errors->has('address1') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Address', 'autocomplete'=>'off','required'=>'required')) !!}
                            @if ($errors->has('address1'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('address1') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="roles" class="form-label">Rol <span class="text-danger">*</span></label>
                            {!! Form::select('roles', $roles,$userRole, array('class' => 'form-control')) !!}
                            @if ($errors->has('roles'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('roles') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
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
                    {!! Form::submit('Guardar', array('class'=>'btn btn-primary btn-block')) !!}
                </div>
            </div>
        </div>
    </div>
</div>
{{ Form::close() }}

@endif
@elseif(Request::segment(1)==='employee-view')
@can('employee-view')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title ">Employee Information</h3>
                <div class="card-options">
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('users-list') }}"> <i class="fa fa-plus"></i> Create Employee</a>
                    &nbsp;&nbsp;&nbsp;
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-group">
                            <li class="list-group-item justify-content-between">
                                Name
                                <span class="badgetext">{{ $user->name }}</span>
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
                                Auto lock time out
                                <span class="badgetext">{{ $user->locktimeout }} minute</span>
                            </li>
                            <li class="list-group-item justify-content-between">
                                Status
                                @if($user->status=='0')
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
                    <div class="col-md-6">
                        <ul class="list-group">

                            <li class="list-group-item justify-content-between">
                                Address
                                <span class="badgetext">{{ $user->address1 }}</span>
                            </li>
                            <li class="list-group-item justify-content-between">
                                City
                                <span class="badgetext">{{ $user->city }}</span>
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
@can('employee-list')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title ">Employee Management</h3>
                <div class="card-options">
                    @can('employee-create')
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('employee-create') }}"> <i class="fa fa-plus"></i> Create New Employee</a>
                    @endcan
                    &nbsp;&nbsp;&nbsp;<a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
            {{ Form::open(array('route' => 'employee-action', 'class'=> 'form-horizontal', 'autocomplete'=>'off')) }}
            @csrf
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Domicilio</th>
                                <th>Estado</th>
                                <th scope="col"width="10%">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($data as $key => $rows)
                            <tr>
                                <td>
                                    <label class="custom-control custom-checkbox">
                                        {{ Form::checkbox('boxchecked[]', $rows->id,'', array('class' => 'colorinput-input custom-control-input', 'id'=>'')) }}
                                        <span class="custom-control-label"></span>
                                    </label>
                                </td>
                                <td>{!!$key+1!!}</td>
                                <td>{!!$rows->name!!} {!!$rows->lastname!!}</td>
                                <td>{!!$rows->email!!}</td>
                                <td>{!!$rows->phone!!}</td>
                                <td>{!!$rows->address1!!}</td>
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
                                        @can('employee-view')
                                        <a class="btn btn-sm btn-secondary" href="{{ route('employee-view',base64_encode($rows->id)) }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ver"><i class="fa fa-eye"></i></a>
                                        @endcan
                                        @can('employee-edit')
                                        <a class="btn btn-sm btn-primary" href="{{ route('employee-edit',base64_encode($rows->id)) }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Editar"><i class="fa fa-edit"></i></a>
                                        @endcan
                                        @can('employee-delete')
                                        <a class="btn btn-sm btn-danger" href="{{ route('employee-delete',base64_encode($rows->id)) }}" onClick="return confirm('Está seguro que desea eliminarlo?');" data-toggle="tooltip" data-placement="top" title="" data-original-title="Eliminar"><i class="fa fa-trash"></i></a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @can('employee-action')
                <div class="row div-margin">
                    <div class="col-md-3 col-sm-6 col-xs-6">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-hand-o-right"></i> </span>
                                {{ Form::select('cmbaction', array(
                                ''              => 'Accion',
                                'Active'        => 'Active',
                                'Inactive'      => 'Inactive',
                                'Delete'        => 'Eliminar'),
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
