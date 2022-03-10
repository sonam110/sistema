@extends('layouts.master')
@section('content')
@if(Auth::user()->hasAnyPermission(['purchase-report']) || Auth::user()->hasRole('admin'))
<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title "><b>Filtro</b></h3>
				<div class="card-options">
                  &nbsp;&nbsp;&nbsp;<a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
				</div>
			</div>
			<div class="card-body">
				{{ Form::open(array('route' => 'purchase-concept-report', 'class'=> 'form-horizontal', 'autocomplete'=>'off')) }}
				@csrf
				<div class="row">
					<div class="col-lg-4 col-md-4 col-sm-5">
						<div class="form-group">
							<label for="from_date" class="form-label">Desde</label>
							{!! Form::date('from_date',$from_date,array('id'=>'from_date','class'=> 'form-control', 'placeholder'=>'From Date', 'autocomplete'=>'off','required')) !!}
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-5">
						<div class="form-group">
							<label for="from_date" class="form-label">Hasta</label>
							<div class="row gutters-xs">
								<div class="col">
									{!! Form::date('to_date',$to_date,array('id'=>'to_date','class'=> 'form-control', 'placeholder'=>'To Date', 'autocomplete'=>'off')) !!}
								</div>
								<span class="col-auto">
									<button type="submit" class="btn btn-primary" type="button" data-toggle="tooltip" data-placement="right" title="" data-original-title="Show Record">Ir!</button>
								</span>
							</div>
						</div>
					</div>
				</div>
				{{ Form::close() }}
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="card">
			<div class="card-body">
				<div id="containerConcept"></div>
			</div>
			<div class="card-body">
				<div id="containerProvee"></div>
			</div>
		</div>
	</div>
</div>

@endif
@endsection
@section('extrajs')
{!! Html::script('assets/plugins/highcharts/highcharts.js') !!}
{!! Html::script('assets/plugins/highcharts/highcharts-3d.js') !!}
<script>

var collection=[
 @foreach($totalProveeData as $key => $dat)
     { name:'{{$dat->name}}',value:{{$dat->total}} },
 @endforeach
]
var obj={
 @foreach($totalProveeData as $key => $dat)
     '{{$dat->name}}' : {{$dat->total}},
 @endforeach
}

var categories = collection.map(point => point.name);
var seriesData = collection.map(point => point.value);
var categoriesObj = Object.keys(obj);
var seriesDataObj = Object.values(obj);

var chart = Highcharts.chart('containerProvee', {
    title: {
        text: 'Compras por Proveedores'
    },
    chart: {type: 'bar'}, 
    xAxis: {
        categories: categoriesObj,
        title: {
               text: 'Proveedores'
               }        
    }, 
    yAxis : {
      title: {
       text:'Monto'
       }
    },
    series: [{
        name: 'Monto',
        colorByPoint: true,
        data: seriesDataObj
    }],
    legend: true,
    credits: {
			enabled: false
	}
    
});

var collection=[
 @foreach($totalConceptsData as $key => $dat)
     { name:'{{$dat->concepto}}',value:{{$dat->total}} },
 @endforeach
]
var obj={
 @foreach($totalConceptsData as $key => $dat)
     '{{$dat->concepto}}' : {{$dat->total}},
 @endforeach
}

var categories = collection.map(point => point.name);
var seriesData = collection.map(point => point.value);
var categoriesObj = Object.keys(obj);
var seriesDataObj = Object.values(obj);

var chart = Highcharts.chart('containerConcept', {
    title: {
        text: 'Compras por Concepto'
    },
    chart: {type: 'bar'}, 
    xAxis: {
        categories: categoriesObj,
        title: {
               text: 'Conceptos'
               }        
    }, 
    yAxis : {
      title: {
       text:'Monto'
       }
    },
    series: [{
        name: 'Monto',
        colorByPoint: true,
        data: seriesDataObj
    }],
    legend: true,
    credits: {
			enabled: false
	}
    
});

</script>
@endsection
