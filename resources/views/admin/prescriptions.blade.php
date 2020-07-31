@extends('layouts.master')
@section('content')

<!-- <div class="page-header">
    <ol class="breadcrumb breadcrumb-arrow mt-3">
        <li><a href="{{route('dashboard') }}">Dashboard</a></li>
        <li class="active"><span>Prescription Management</span></li>
    </ol>
</div> -->
@if(Request::segment(1)==='edit-prescription' || Request::segment(1)==='create-prescription')
@if(Request::segment(1)==='create-prescription')
<?php
$id                 = '';
$prescribed_date    = date('Y-m-d');
$any_comment        = '';
?>
@else
<?php
$id                 = $data->id;
$prescribed_date    = $data->prescribed_date;
$any_comment        = $data->any_comment ;
?>
@endif

{{ Form::open(array('route' => 'save-prescription', 'class'=> 'form-horizontal','enctype'=>'multipart/form-data', 'files'=>true)) }}
{!! Form::hidden('id',$id,array('class'=>'form-control')) !!}
@csrf
<div class="row row-deck">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    @if(Request::segment(1)==='create-prescription')
                    Add
                    @else
                    Edit
                    @endif
                    Prescription
                </h3>
                <div class="card-options">
                    <a href="{{ route('categories') }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Go To Back"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="prescribed_date" class="form-label">Prescription Date</label>
                            {!! Form::date('prescribed_date',$prescribed_date,array('id'=>'prescribed_date','class'=> $errors->has('prescribed_date') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Prescription Date', 'autocomplete'=>'off','required'=>'required')) !!}
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="any_comment" class="form-label">Any Comment / Suggession</label>
                            {!! Form::text('any_comment',$any_comment,array('id'=>'any_comment','class'=> $errors->has('any_comment') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Any Comment / Suggession', 'autocomplete'=>'off')) !!}
                        </div>
                    </div>                  

                </div>

                <div class="row">
                    <div class="col-md-12 add-more-section">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th width="30%">Medicine Name</th>
                                    <th>Dosage / Day</th>
                                    <th>For Days</th>
                                    <th width="30%">Comment</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="add-sec">
                                    <td>
                                        <button type="button" class="btn btn-sm btn-success addMore"><i class="fa fa-plus"></i></button>
                                    </td>
                                    <td>
                                        <select name="medicine_id[]" class="form-control select2-show-search" data-placeholder="Enter Medicine Name">
                                            <option value='0'>- Search user -</option>ect>
                                    </td>
                                    <td>
                                        {!! Form::text('takeperday[]',null,array('id'=>'takeperday','class'=> $errors->has('takeperday') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Dosage / Day', 'autocomplete'=>'off','required'=>'required')) !!}
                                    </td>
                                    <td>
                                        {!! Form::text('howMuchDays[]',null,array('id'=>'howMuchDays','class'=> $errors->has('howMuchDays') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'For Days', 'autocomplete'=>'off','required'=>'required')) !!}
                                    </td>
                                    <td>
                                        {!! Form::text('comment[]',null,array('id'=>'comment','class'=> $errors->has('comment') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Comment', 'autocomplete'=>'off','required'=>'required')) !!}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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

@elseif(Request::segment(1)==='view-prescription')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title ">Prescription Management</h3>
                <div class="card-options">
                    <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ route('print-prescription',$data->id) }}"> <i class="fa fa-print"></i> Print</a>
                    &nbsp;&nbsp;&nbsp;
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('create-prescription') }}"> <i class="fa fa-plus"></i> Create New</a>
                    &nbsp;&nbsp;&nbsp;
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Go To Back"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">#{{$data->prescribed_date}}</h3>
            </div>

            <div class="card-body" id="printData">

                <div class="dropdown-divider"></div>
                <strong>
                    #Prescription Comment : {{(!empty($data->any_comment)) ? $data->any_comment : '-'}} 

                </strong>
                <br>
                <div class="table-responsive push">
                    <table class="table table-bordered table-hover">
                        <tr class="">
                            <th class="text-center" width="5%">#</th>
                            <th>Medicine</th>
                            <th class="text-right" width="10%">Dosage</th>
                            <th class="text-right" width="15%">No. of days</th>
                        </tr>
                        @foreach($data->PrescriptionMedicines as $key => $rec)
                        <tr>
                            <td class="text-center">{{$key+1}}</td>
                            <td>
                                <p class="font-w600 mb-1">{{$rec->medicine_name}}</p>
                                <div class="text-muted">({{$rec->comment}})</div>
                            </td>
                            <td class="text-right">{{$rec->takeperday}}</td>
                            <td class="text-right">{{$rec->howMuchDays}}</td>
                        </tr>
                        @endforeach
                        <tr class="noPrint">
                            <td colspan="5" class="text-right">
                                <button type="button" class="btn btn-warning" onclick="printPres('print')"><i class="si si-printer"></i> Print Prescription</button>
                            </td>
                        </tr>
                    </table>
                </div>
                <p class="text-muted text-center">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
            </div>
        </div>
    </div>
</div>
@else
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title ">Prescription Management</h3>
                <div class="card-options">
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('create-prescription') }}"> <i class="fa fa-plus"></i> Create New</a>
                    &nbsp;&nbsp;&nbsp;
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Go To Back"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
            {{ Form::open(array('route' => 'action-prescription', 'class'=> 'form-horizontal', 'autocomplete'=>'off')) }}
            @csrf
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th scope="col" width="5%"></th>
                                <th scope="col" width="10%">#</th>
                                <th scope="col">Date</th>
                                <th scope="col">Any Comment</th>
                                <th scope="col" width="10%">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($prescriptions as $key => $rows)
                            <tr>
                                <td>
                                    <label class="custom-control custom-checkbox">
                                        {{ Form::checkbox('boxchecked[]', $rows->id,'', array('class' => 'colorinput-input custom-control-input', 'id'=>'')) }}
                                        <span class="custom-control-label"></span>
                                    </label>
                                </td>
                                <td>{!! $key+1 !!}</td>
                                <td>{!! $rows->prescribed_date !!}</td>
                                <td>{!! $rows->any_comment !!}</td>
                                <td>
                                    <div class="btn-group btn-group-xs">
                                        <!-- <a class="btn btn-sm btn-primary" href="{{ route('edit-prescription',$rows->id) }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="fa fa-edit"></i></a> -->
                                        <a class="btn btn-sm btn-success" href="{{ route('view-prescription',$rows->id) }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="fa fa-eye"></i></a>
                                        <a class="btn btn-sm btn-info" target="_blank" href="{{ route('print-prescription',$rows->id) }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print"><i class="fa fa-print"></i></a>
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
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endif

@endsection