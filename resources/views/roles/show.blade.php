@extends('layouts.master')
@section('content')

<!-- <div class="page-header">
    <ol class="breadcrumb breadcrumb-arrow mt-3">
        <li><a href="{{route('dashboard') }}">Dashboard</a></li>
        <li class="active"><span>Show Role Management</span></li>
    </ol>
</div> -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title ">Show Role Management</h3>
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
                                <th scope="col">Name</th>
                                <th scope="col">Permissions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $role->name }}</td>
                                <td>
                                    <ul class="list-group">
                                        @if(!empty($rolePermissions))
                                        @foreach($rolePermissions as $v)
                                        <li class="listorder">{{ $v->name }} </li>
                                        @endforeach
                                        @endif
                                    </ul>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
