@extends('layouts.master')
@section('content')

@if(Request::segment(1)==='concept-create' || Request::segment(1)==='concept-edit')
@if(Request::segment(1)==='concept-create')
<?php
$id             = '';
$description           = '';
$status         = '';
?>
@else
<?php
$id             = $concept->id;
$description    = $concept->description;
$status         = $concept->status;
?>
@endif

@if(Auth::user()->hasAnyPermission(['concept-create','concept-edit']) || Auth::user()->hasRole('admin'))

{{ Form::open(array('route' => 'concept-save', 'class'=> 'form-horizontal','enctype'=>'multipart/form-data', 'files'=>true, 'autocomplete'=>'off')) }}
{!! Form::hidden('id',$id,array('class'=>'form-control')) !!}
@csrf
<div class="row row-deck">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    @if(Request::segment(1)==='concept-create')
                    Agregar
                    @else
                    Modificar
                    @endif
                    Concepto
                </h3>
                @can('concept-list')
                <div class="card-options">
                    <a href="{{ route('concept-list') }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
                </div>
                @endcan
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="description" class="form-label">Descripcion <span class="text-danger">*</span></label>
                            {!! Form::text('description',$description,array('id'=>'description','class'=> $errors->has('description') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Descripcion', 'autocomplete'=>'off','required'=>'required')) !!}
                            @if ($errors->has('description'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('description') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                            {!! Form::select('status', [
                                '0' => 'Activo',
                                '1' => 'Inactivo',
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
@else
@can('concept-list')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title ">Conceptos de compras</h3>
                <div class="card-options">
                    @can('concept-create')
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('concept-create') }}"> <i class="fa fa-plus"></i> Nuevo Concepto</a>
                    @endcan
                    &nbsp;&nbsp;&nbsp;<a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
            @csrf
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th>Descripcion</th>
                                <th>Estado</th>
                                <th scope="col"width="10%">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($data as $key => $rows)
                            <tr>
                                <td>{!!$key+1!!}</td>
                                <td>{!!$rows->description!!}</td>
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
                                        @can('concept-edit')
                                        <a class="btn btn-sm btn-primary" href="{{ route('concept-edit',base64_encode($rows->id)) }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Editar"><i class="fa fa-edit"></i></a>
                                        @endcan
                                        @can('concept-delete')
                                        <a class="btn btn-sm btn-danger" href="{{ route('concept-delete',base64_encode($rows->id)) }}" onClick="return confirm('Está seguro que desea eliminarlo?');" data-toggle="tooltip" data-placement="top" title="" data-original-title="Eliminar"><i class="fa fa-trash"></i></a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
        </div>
    </div>
    @endcan
    @endif

    @endsection
