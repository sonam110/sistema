@extends('layouts.master')
@section('content')
@include('includes.message')
<!-- <div class="page-header">
    <ol class="breadcrumb breadcrumb-arrow mt-3">
        <li><a href="{{route('dashboard') }}">Dashboard</a></li>
        <li class="active"><span>Update Permission</span></li>
    </ol>
</div> -->
<div class="row row-deck">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Actualizar Permiso</h3>
                <div class="card-options">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
            <div class="card-body">
                {!! Form::model($permissions, ['method' => 'PATCH','route' => ['permissions.update', $permissions->id]]) !!}
                @csrf

                    <div class="form-group">
                        <label for="name" class="form-label">Nombre</label>
                        {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control', 'required')) !!}
                        @if ($errors->has('name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                        @endif
                    </div>

                    <div class="form-footer text-center">
                        <hr>
                        {!! Form::submit('Update', array('class'=>'btn btn-primary')) !!}
                    </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@endsection
