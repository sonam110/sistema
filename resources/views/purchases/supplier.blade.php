@extends('layouts.master')
@section('content')

@if(Request::segment(1)==='supplier-create' || Request::segment(1)==='supplier-edit')
@if(Request::segment(1)==='supplier-create')
<?php
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
?>
@else
<?php
$id             = $supplier->id;
$name           = $supplier->name;
$email          = $supplier->email;
$company_name   = $supplier->company_name;
$address        = $supplier->address;
$phone          = $supplier->phone;
$city           = $supplier->city;
$state          = $supplier->state;
$vat_number     = $supplier->vat_number;
$status         = $supplier->status;
?>
@endif

@if(Auth::user()->hasAnyPermission(['supplier-create','supplier-edit']) || Auth::user()->hasRole('admin'))

{{ Form::open(array('route' => 'supplier-save', 'class'=> 'form-horizontal','enctype'=>'multipart/form-data', 'files'=>true, 'autocomplete'=>'off')) }}
{!! Form::hidden('id',$id,array('class'=>'form-control')) !!}
@csrf
<div class="row row-deck">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    @if(Request::segment(1)==='supplier-create')
                    Add
                    @else
                    Edit
                    @endif
                    Proveedor
                </h3>
                @can('supplier-list')
                <div class="card-options">
                    <a href="{{ route('supplier-list') }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
                </div>
                @endcan
            </div>
            <div class="card-body">
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
                            {!! Form::text('city',$city,array('id'=>'city','class'=> $errors->has('city') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Ciudad', 'autocomplete'=>'off','required'=>'required')) !!}
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
                            {!! Form::text('vat_number',$vat_number,array('id'=>'vat_number','class'=> $errors->has('vat_number') ? 'form-control is-invalid vat_number-invalid' : 'form-control', 'placeholder'=>'CUIT', 'autocomplete'=>'off')) !!}
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
                                '1' => 'activo',
                                '0' => 'Inativo',
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
@elseif(Request::segment(1)==='supplier-view')
@can('supplier-view')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title ">Info Proveedor</h3>
                <div class="card-options">
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('supplier-list') }}"> <i class="fa fa-plus"></i> Nuevo Proveedor</a>
                    &nbsp;&nbsp;&nbsp;
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-group">
                            <li class="list-group-item justify-content-between">
                                Nombre
                                <span class="badgetext">{{ $user->name }}</span>
                            </li>

                            <li class="list-group-item justify-content-between">
                                Email
                                <span class="badgetext">{{ $user->email }}</span>
                            </li>
                            <li class="list-group-item justify-content-between">
                                Teléfono
                                <span class="badgetext">{{ $user->phone }}</span>
                            </li>
                            <li class="list-group-item justify-content-between">
                                Estado
                                @if($user->status=='0')
                                <span class="badgetext text-danger">
                                    Inactivo
                                </span>
                                @else
                                <span class="badgetext text-success">
                                    Activo
                                </span>
                                @endif
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group">
                            <li class="list-group-item justify-content-between">
                                Compañía
                                <span class="badgetext">{{ $user->company_name }}</span>
                            </li>
                            <li class="list-group-item justify-content-between">
                                CUIT
                                <span class="badgetext">{{ $user->vat_number }}</span>
                            </li>
                            <li class="list-group-item justify-content-between">
                                Domicilio
                                <span class="badgetext">{{ $user->address }}</span>
                            </li>
                            <li class="list-group-item justify-content-between">
                                Ciudad
                                <span class="badgetext">{{ $user->city }}</span>
                            </li>
                            <li class="list-group-item justify-content-between">
                                Estado
                                <span class="badgetext">{{ $user->state }}</span>
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
@can('supplier-list')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title ">Gestión de Proveedores</h3>
                <div class="card-options">
                    @can('supplier-create')
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('supplier-create') }}"> <i class="fa fa-plus"></i> Nuevo Proveedor</a>
                    @endcan
                    &nbsp;&nbsp;&nbsp;<a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
            {{ Form::open(array('route' => 'supplier-action', 'class'=> 'form-horizontal', 'autocomplete'=>'off')) }}
            @csrf
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">#</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Domicilio</th>
                                <th>Estado</th>
                                <th scope="col"width="10%">Acciónon</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($suppliers as $key => $rows)
                            <tr>
                                <td>
                                    <label class="custom-control custom-checkbox">
                                        {{ Form::checkbox('boxchecked[]', $rows->id,'', array('class' => 'colorinput-input custom-control-input', 'id'=>'')) }}
                                        <span class="custom-control-label"></span>
                                    </label>
                                </td>
                                <td>{!!$key+1!!}</td>
                                <td>{!!$rows->name!!}</td>
                                <td>{!!$rows->email!!}</td>
                                <td>{!!$rows->phone!!}</td>
                                <td>{!!$rows->address!!}</td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-xs ">
                                        @if($rows->status=='0')
                                        <span class="text-danger">Inactivo</span>
                                        @else
                                        <span class="text-success">Activo</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-xs">
                                        @can('supplier-view')
                                        <a class="btn btn-sm btn-secondary" href="{{ route('supplier-view',base64_encode($rows->id)) }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ver"><i class="fa fa-eye"></i></a>
                                        @endcan
                                        @can('supplier-edit')
                                        <a class="btn btn-sm btn-primary" href="{{ route('supplier-edit',base64_encode($rows->id)) }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Editar"><i class="fa fa-edit"></i></a>
                                        @endcan
                                        @can('supplier-delete')
                                        <a class="btn btn-sm btn-danger" href="{{ route('supplier-delete',base64_encode($rows->id)) }}" onClick="return confirm('Está seguro que desea eliminarlo?');" data-toggle="tooltip" data-placement="top" title="" data-original-title="Borrar"><i class="fa fa-trash"></i></a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @can('supplier-action')
                <div class="row div-margin">
                    <div class="col-md-3 col-sm-6 col-xs-6">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-hand-o-right"></i> </span>
                                {{ Form::select('cmbaction', array(
                                ''              => 'Acción',
                                'Active'        => 'Activo',
                                'Inactive'      => 'Inactivo',
                                'Delete'        => 'Eliminar'),
                                '', array('class'=>'form-control','id'=>'cmbaction'))}}
                            </div>
                        </div>
                        <div class="col-md-8 col-sm-6 col-xs-6">
                            <div class="input-group">
                                <button type="submit" class="btn btn-danger pull-right" name="Action" onClick="return delrec(document.getElementById('cmbaction').value);">Aplicar</button>
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
