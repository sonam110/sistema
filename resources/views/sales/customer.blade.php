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
                    <a href="{{ route('customer-list') }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
                </div>
                @endcan
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
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

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="lastname" class="form-label">Apellido <span class="text-danger">*</span></label>
                            {!! Form::text('lastname',$lastname,array('id'=>'lastname','class'=> $errors->has('lastname') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Apellido', 'autocomplete'=>'off','required'=>'required')) !!}
                            @if ($errors->has('lastname'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('lastname') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="doc_type" class="form-label">Documento Tipo <span class="text-danger">*</span></label>
                            {!! Form::select('doc_type', [
                                'DNI'       => 'DNI',
                                'CUIT'      => 'CUIT',
                                'PASSPORT'  => 'PASSAPORTE',
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
                            <label for="doc_number" class="form-label">Número de Documento <span class="text-danger">*</span></label>
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
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            {!! Form::text('email',$email,array('id'=>'email','class'=> $errors->has('email') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Email ', 'autocomplete'=>'off','required'=>'required')) !!}
                            @if ($errors->has('email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="companyname" class="form-label">Compañía </label>
                            {!! Form::text('companyname',$companyname,array('id'=>'companyname','class'=> $errors->has('companyname') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Compañía', 'autocomplete'=>'off')) !!}
                            @if ($errors->has('companyname'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('companyname') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="address1" class="form-label">Domicilio</label>
                            {!! Form::text('address1',$address1,array('id'=>'address1','class'=> $errors->has('address1') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Domicilio', 'autocomplete'=>'off')) !!}
                            @if ($errors->has('address1'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('address1') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="address2" class="form-label">Domicilio 2</label>
                            {!! Form::text('address2',$address2,array('id'=>'address2','class'=> $errors->has('address2') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Domicilio', 'autocomplete'=>'off')) !!}
                            @if ($errors->has('address2'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('address2') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="city" class="form-label">Ciudad</label>
                            {!! Form::text('city',$city,array('id'=>'city','class'=> $errors->has('city') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Ciudad', 'autocomplete'=>'off')) !!}
                            @if ($errors->has('city'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('city') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="state" class="form-label">Provincia</label>
                            {!! Form::text('state',$state,array('id'=>'state','class'=> $errors->has('state') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Provincia', 'autocomplete'=>'off')) !!}
                            @if ($errors->has('state'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('state') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="country" class="form-label">Pais</label>
                            {!! Form::text('country',$country,array('id'=>'country','class'=> $errors->has('country') ? 'form-control is-invalid country-invalid' : 'form-control', 'placeholder'=>'Pais', 'autocomplete'=>'off')) !!}
                            @if ($errors->has('country'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('country') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="postcode" class="form-label">Código postal</label>
                            {!! Form::text('postcode',$postcode,array('id'=>'postcode','class'=> $errors->has('postcode') ? 'form-control is-invalid postcode-invalid' : 'form-control', 'placeholder'=>'Código Postal', 'autocomplete'=>'off')) !!}
                            @if ($errors->has('postcode'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('postcode') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
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


                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                            {!! Form::select('status', [
                                '0' => 'Activo',
                                '1' => 'Inativo',
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
@elseif(Request::segment(1)==='customer-view')
@can('customer-view')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title ">Información de Cliente</h3>
                <div class="card-options">
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('customer-list') }}"> <i class="fa fa-plus"></i>Añadir Nuevo Cliente</a>
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
                                <span class="badgetext">{{ $user->name }} {{ $user->lastname }}</span>
                            </li>

                            <li class="list-group-item justify-content-between">
                                Tipo de Documento
                                <span class="badgetext">{{ $user->doc_type }}</span>
                            </li>

                            <li class="list-group-item justify-content-between">
                                Documento Número
                                <span class="badgetext">{{ $user->doc_number }}</span>
                            </li>

                            <li class="list-group-item justify-content-between">
                                Nombre
                                <span class="badgetext">{{ $user->name }} {{ $user->lastname }}</span>
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
                                Comapñía
                                <span class="badgetext">{{ $user->companyname }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group">
                            <li class="list-group-item justify-content-between">
                                Domicilio 1
                                <span class="badgetext">{{ $user->address1 }}</span>
                            </li>
                            <li class="list-group-item justify-content-between">
                                Domicilio 2
                                <span class="badgetext">{{ $user->address2 }}</span>
                            </li>
                            <li class="list-group-item justify-content-between">
                                Ciudad
                                <span class="badgetext">{{ $user->city }}</span>
                            </li>
                            <li class="list-group-item justify-content-between">
                                Provincia
                                <span class="badgetext">{{ $user->state }}</span>
                            </li>
                            <li class="list-group-item justify-content-between">
                                Pais
                                <span class="badgetext">{{ $user->country }}</span>
                            </li>
                            <li class="list-group-item justify-content-between">
                                Código postal
                                <span class="badgetext">{{ $user->postcode }}</span>
                            </li>
                            <li class="list-group-item justify-content-between">
                                Estado
                                @if($user->status=='1')
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
                <h3 class="card-title ">Gestionar Clientes</h3>
                <div class="card-options">
                    @can('customer-create')
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('customer-create') }}"> <i class="fa fa-plus"></i>Agregar Clientes </a>
                    @endcan
                    &nbsp;&nbsp;&nbsp;<a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
            {{ Form::open(array('route' => 'customer-action', 'class'=> 'form-horizontal', 'autocomplete'=>'off')) }}
            @csrf
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">#</th>
                                <th>Id</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Doc Tipo</th>
                                <th>Doc Número</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th scope="col"width="10%">Acción</th>
                            </tr>
                        </thead>

                        <!-- <tbody>
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
                                        <span class="text-danger">Inactivo</span>
                                        @else
                                        <span class="text-success">Activo</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-xs">
                                        @can('customer-view')
                                        <a class="btn btn-sm btn-secondary" href="{{ route('customer-view',base64_encode($rows->id)) }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ver"><i class="fa fa-eye"></i></a>
                                        @endcan
                                        @can('customer-edit')
                                        <a class="btn btn-sm btn-primary" href="{{ route('customer-edit',base64_encode($rows->id)) }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Editar"><i class="fa fa-edit"></i></a>
                                        @endcan
                                        @can('customer-delete')
                                        <a class="btn btn-sm btn-danger" href="{{ route('customer-delete',base64_encode($rows->id)) }}" onClick="return confirm('Está seguro que desea eliminarlo?');" data-toggle="tooltip" data-placement="top" title="" data-original-title="Eliminar"><i class="fa fa-trash"></i></a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody> -->
                    </table>
                </div>

                @can('customer-action')
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
@section('extrajs')
<script type="text/javascript">
$(document).ready( function () {
    var table = $('#datatable').DataTable({
       "processing": true,
       "serverSide": true,
       "ajax":{
           'url' : '{{ route('api.customer-list-datatable') }}',
           'type' : 'POST'
        },
       'headers': {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        "order": [["2", "desc" ]],
        "columns": [
            { "data": 'checkbox'},
            { "data": 'DT_RowIndex', "name": 'DT_RowIndex' , orderable: false, searchable: false },
            { "data": 'id'},
            { "data": 'name'},
            { "data": 'lastname'},
            { "data": "doc_type"},
            { "data": "doc_number"},
            { "data": "email"},
            { "data": "phone"},
            { "data": "status"},
            { "data": "action"}
        ]
   });
});
</script>
@endsection
