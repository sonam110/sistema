@extends('layouts.master')
@section('content')
	@if(Auth::user()->hasAnyPermission(['direct-purchase-return']) || Auth::user()->hasRole('admin'))

		{{ Form::open(array('route' => 'sales-order-return-save', 'class'=> 'form-horizontal','enctype'=>'multipart/form-data', 'files'=>true, 'autocomplete'=>'off')) }}
		@csrf
		<div class="row row-deck">
		    <div class="col-lg-12">
		        <div class="card">
		            <div class="card-header">
		                <h3 class="card-title">
		                    Purchase Order Return 
		                </h3>
		                @can('sales-order-list')
		                <div class="card-options">
		                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Go To Back"><i class="fa fa-mail-reply"></i></a>
		                </div>
		                @endcan
		            </div>
		            <div class="card-body">
		                <div class="row">
		                    <div class="col-md-12">

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
	@endif
@endsection

@section('extrajs')

@endsection