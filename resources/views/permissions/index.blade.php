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
                <h3 class="card-title ">Permisos</h3>
                <div class="card-options">
                    {{--
                    @can('permission-create')
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('permissions.create') }}"> <i class="fa fa-plus"></i> Crear Nuevo Permiso</a>
                    @endcan
                    --}}
                    &nbsp;&nbsp;&nbsp;<a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Go To Back"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nombre</th>
                                <!-- <th scope="col">Action</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i= 0; ?>
                            @foreach ($permissions as $key => $permission)
                            <tr>
                                <th width="5%" scope="row">{{ ++$i }}</th>
                                <th>{{ $permission->name }}</th>
                                {{--<td width="25%">

                                    @can('permission-edit')
                                    <a class="btn btn-sm btn-primary" href="{{ route('permissions.edit',$permission->id) }}"><i class="fa fa-edit"></i> Editar</a>
                                    @endcan

                                    <a class="btn btn-sm btn-info" href="{{ route('permissions.show',$permission->id) }}"><i class="fa fa-info-circle"></i> Detalles</a>

                                </td>--}}
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
