@extends('layouts.master')
@section('content')

<!-- <div class="page-header">
    <ol class="breadcrumb breadcrumb-arrow mt-3">
        <li><a href="{{route('dashboard') }}">Dashboard</a></li>
        <li class="active"><span>Show Permission</span></li>
    </ol>
</div> -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title ">Ver Permisos</h3>
                <div class="card-options">
                    @can('permission-create')
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('permissions.create') }}"> <i class="fa fa-plus"></i> Crear Nuevo Permiso</a>
                    @endcan
                    &nbsp;&nbsp;&nbsp;<a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">Nombre</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $Permissions->name }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
