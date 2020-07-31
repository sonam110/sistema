@extends('layouts.master')
@section('content')

<!-- <div class="page-header">
    <ol class="breadcrumb breadcrumb-arrow mt-3">
        <li><a href="{{route('dashboard') }}">Dashboard</a></li>
        <li class="active"><span>Sub Category Management</span></li>
    </ol>
</div> -->
@if(Request::segment(1)==='edit-sub-category' || Request::segment(1)==='create-sub-category')
@if(Request::segment(1)==='create-sub-category')
<?php
$id             = '';
$category_id    = '';
$name           = '';
?>
@else
<?php
$id             = $data->id;
$category_id    = $data->category_id;
$name           = $data->name;
?>
@endif

{{ Form::open(array('route' => 'save-sub-category', 'class'=> 'form-horizontal','enctype'=>'multipart/form-data', 'files'=>true)) }}
{!! Form::hidden('id',$id,array('class'=>'form-control')) !!}
@csrf
<div class="row row-deck">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    @if(Request::segment(1)==='create-sub-category')
                    Add
                    @else
                    Edit
                    @endif
                    Sub Category
                </h3>
                <div class="card-options">
                    <a href="{{ route('sub-categories') }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Go To Back"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category_id" class="form-label">Category Name</label>
                            {!! Form::select('category_id',$categories,$category_id,array('id'=>'category_id','class'=> $errors->has('category_id') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Category Name', 'autocomplete'=>'off','required'=>'required')) !!}
                            @if ($errors->has('category_id'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('category_id') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name" class="form-label">Sub Category Name</label>
                            {!! Form::text('name',$name,array('id'=>'name','class'=> $errors->has('name') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Sub Category Name', 'autocomplete'=>'off','required'=>'required')) !!}
                            @if ($errors->has('name'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('name') }}</strong>
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


@else
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title ">Sub Category Management</h3>
                <div class="card-options">
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('create-sub-category') }}"> <i class="fa fa-plus"></i> Create New</a>
                    &nbsp;&nbsp;&nbsp;
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Go To Back"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
            {{ Form::open(array('route' => 'action-sub-category', 'class'=> 'form-horizontal', 'autocomplete'=>'off')) }}
            @csrf
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th scope="col" width="5%"></th>
                                <th scope="col" width="10%">#</th>
                                <th scope="col">Category Name</th>
                                <th scope="col">Sub Category Name</th>
                                <th scope="col" width="10%">Status</th>
                                <th scope="col" width="10%">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($subCategories as $key => $rows)
                            <tr>
                                <td>
                                    <label class="custom-control custom-checkbox">
                                        {{ Form::checkbox('boxchecked[]', $rows->id,'', array('class' => 'colorinput-input custom-control-input', 'id'=>'')) }}
                                        <span class="custom-control-label"></span>
                                    </label>
                                </td>
                                <td>{!! $key+1 !!}</td>
                                <td>{!! $rows->Category->name !!}</td>
                                <td>{!! $rows->name !!}</td>
                                
                                <td class="text-center">
                                    <div class="btn-group btn-group-xs ">
                                        @if($rows->status=='0') 
                                        <span class="text-danger">Inactive</span>
                                        @else 
                                        <span class="text-success">Active</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-xs">
                                        <a class="btn btn-sm btn-primary" href="{{ route('edit-sub-category',$rows->id) }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="fa fa-edit"></i></a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row div-margin">
                    <div class="col-md-3 col-sm-6 col-xs-6">
                        <div class="input-group"> 
                            <span class="input-group-addon">
                                <i class="fa fa-hand-o-right"></i> </span> 
                            {{ Form::select('cmbaction', array(
                            ''              => 'Action', 
                            'Active'        => 'Active',
                            'Inactive'  => 'Inactive'), 
                            '', array('class'=>'form-control','id'=>'cmbaction'))}} 
                        </div>
                    </div>
                    <div class="col-md-8 col-sm-6 col-xs-6">
                        <div class="input-group">
                            <button type="submit" class="btn btn-danger pull-right" name="Action" onClick="return delrec(document.getElementById('cmbaction').value);">Apply</button>
                        </div>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endif

@endsection