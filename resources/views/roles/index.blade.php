@extends('layouts.master')
@section('content')

<!-- <div class="page-header">
    <ol class="breadcrumb breadcrumb-arrow mt-3">
        <li><a href="{{route('dashboard') }}">Dashboard</a></li>
        <li class="active"><span>Role Management</span></li>
    </ol>
</div> -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title ">Role Management</h3>
                <div class="card-options">
                    @can('role-create')
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('roles.create') }}"> <i class="fa fa-plus"></i> Create New Role</a>
                    @endcan
                    &nbsp;&nbsp;&nbsp;<a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Go To Back"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $key => $role)
                            <tr>
                                <th width="5%" scope="row">{{ ++$i }}</th>
                                <th>{{ $role->name }}</th>
                                <td width="25%">
                                    @can('role-edit')
                                    <a class="btn btn-sm btn-primary" href="{{ route('roles.edit',$role->id) }}"><i class="fa fa-edit"></i> Edit</a>
                                    @endcan
                                    @can('role-delete')
                                    <a class="btn btn-sm btn-danger" href="{{ route('role-delete',$role->id) }}" onClick="return confirm('Are you sure you want to delete this?');"><i class="fa fa-trash"></i> Delete</a>
                                    @endcan
                                    <a class="btn btn-sm btn-info" href="{{ route('roles.show',$role->id) }}"><i class="fa fa-info-circle"></i> Details</a>
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