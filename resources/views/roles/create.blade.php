@extends('layouts.master')
@section('content')
@include('includes.message')
<!-- <div class="page-header">
    <ol class="breadcrumb breadcrumb-arrow mt-3">
        <li><a href="{{route('dashboard') }}">Dashboard</a></li>
        <li class="active"><span>Create New Role</span></li>
    </ol>
</div> -->
<div class="row row-deck">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create New Role</h3>
                <div class="card-options">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
            <div class="card-body">
                {!! Form::open(array('route' => 'roles.store','method'=>'POST')) !!}
                @csrf

                    <div class="form-group">
                        <label for="name" class="form-label">Name</label>
                        {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control','required')) !!}
                        @if ($errors->has('name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="Permission" class="form-label">Permission</label>
                        <div class="row"> 
                            @foreach($permission as $key => $value)
                            <div class="col-xs-12 col-sm-6 col-md-3">
                                <label class="custom-control custom-checkbox">
                                    <input type="checkbox" class="colorinput-input custom-control-input" name="permission[]" value="{!!$value->id!!}">
                                    <span class="custom-control-label">{!!$value->name!!}</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-footer text-center">
                        <hr>
                        {!! Form::submit('Guardar', array('class'=>'btn btn-primary')) !!}
                    </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@endsection

