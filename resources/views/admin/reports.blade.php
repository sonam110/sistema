@extends('layouts.master')
@section('content')

<!-- <div class="page-header">
    <ol class="breadcrumb breadcrumb-arrow mt-3">
        <li><a href="{{route('dashboard') }}">Dashboard</a></li>
        <li class="active"><span>Prescription Management</span></li>
    </ol>
</div> -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title ">Reports Management</h3>
                <div class="card-options">
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('create-prescription') }}"> <i class="fa fa-plus"></i> Create New Prescription</a>
                    &nbsp;&nbsp;&nbsp;
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Go To Back"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th scope="col" width="10%">#</th>
                                <th scope="col">Date</th>
                                <th scope="col">Any Comment</th>
                                <th scope="col" width="10%">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($reports as $key => $rows)
                            <tr>
                                <td>{!! $key+1 !!}</td>
                                <td>{!! $rows->prescribed_date !!}</td>
                                <td>{!! $rows->any_comment !!}</td>
                                <td>
                                    <div class="btn-group btn-group-xs">
                                        <a class="btn btn-sm btn-success" href="{{ route('view-prescription',$rows->id) }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="fa fa-eye"></i></a>
                                        <a class="btn btn-sm btn-info" target="_blank" href="{{ route('print-prescription',$rows->id) }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print"><i class="fa fa-print"></i></a>
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

@endsection