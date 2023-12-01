@extends('layouts.master')
@section('content')
@if(Auth::user()->hasAnyPermission(['sales-report']) || Auth::user()->hasRole('admin'))
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
				{{ Form::open(array('route' => 'product-sales-report', 'class'=> 'form-horizontal', 'autocomplete'=>'off')) }}
				@csrf
				<div class="row">
					<div class="col-lg-2 col-md-2 col-sm-4">
						<div class="form-group">
							<label for="from_date" class="form-label">Mostrar Lista </label>
							<label class="custom-control custom-checkbox">
                                <input class="colorinput-input custom-control-input" id="withList" name="withList" type="checkbox" value="yes" {{($withList=='yes') ? 'checked' : '' }}>
                                <span class="custom-control-label">Si</span>
                            </label>
						</div>
					</div>

					<div class="col-lg-2 col-md-2 col-sm-4">
						<div class="form-group">
							<label for="from_date" class="form-label">Productos Lista </label>
							<label class="custom-control custom-checkbox">
                                <input class="colorinput-input custom-control-input" id="productList" name="productList" type="checkbox" value="yes" {{($productList=='yes') ? 'checked' : '' }}>
                                <span class="custom-control-label">Si</span>
                            </label>
						</div>
					</div>

					<div class="col-lg-2 col-md-2 col-sm-6">
                        <div class="form-group">
                            <label for="choose_type" class="form-label">Filtrar por <span class="text-danger">*</span></label>
                            <div class="row gutters-xs">
                                <div class="col">
                                    <select name="choose_type" class="form-control" onchange="getListType(this)" id="choose_type">
                                        <option value='' selected="">All</option>
                                        <option value='Marca'>Marca</option>
                                        <option value='Item'>Item</option>
                                        <option value='Modelo'>Modelo</option>
																				<option value='Categoria'>Categoria</option>
                                        <option value='Productos'>Productos</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-6" id="recordFilter" style="display: none;">
                        <div class="form-group">
                            <label for="selected-b-or-m-list" class="form-label">Select <span id="selected_type">Marca</span> <span class="text-danger">*</span></label>
                            <div class="row gutters-xs">
                                <div class="col">
                                    <select name="selected_b_or_m" class="form-control selected-b-or-m-list" id="selected-b-or-m-list" data-placeholder="Ingrese el Nombre">
                                    </select>
                                </div>
                                @if ($errors->has('selected_b_or_m'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('selected_b_or_m') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

					<div class="col-lg-2 col-md-2 col-sm-6">
						<div class="form-group">
							<label for="from_date" class="form-label">Desde</label>
							{!! Form::date('from_date',@$from_date,array('id'=>'from_date','class'=> 'form-control', 'placeholder'=>'From Date', 'autocomplete'=>'off')) !!}
						</div>
					</div>

					<div class="col-lg-4 col-md-4 col-sm-6">
						<div class="form-group">
							<label for="from_date" class="form-label">Hasta</label>
							<div class="row gutters-xs">
								<div class="col">
									{!! Form::date('to_date',@$to_date,array('id'=>'to_date','class'=> 'form-control', 'placeholder'=>'To Date', 'autocomplete'=>'off')) !!}
								</div>
								<span class="col-auto">
									<button type="submit" class="btn btn-primary" type="button" data-toggle="tooltip" data-placement="right" title="" data-original-title="Mostrar Registros">Ir!</button>
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

<div class="row row-cards">
	<div class="col-xl-4 col-lg-6 col-md-12 col-sm-12">
		<div class="card card-counter bg-gradient-primary shadow-primary">
			<div class="card-body">
				<div class="row">
					<div class="col-8">
						<div class="mt-4 mb-0 text-white">
							<h3 class="mb-0">$ @convert2($totalPOSAmount + $totalWEBAmount)</h3>
							<p class="text-white mt-1">Total General <br>(ST + WEB)</p>
						</div>
					</div>
					<div class="col-4">
						<i class="fa fa-money mt-3 mb-0"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-4 col-lg-6 col-md-12 col-sm-12">
		<div class="card card-counter bg-gradient-success shadow-success">
			<div class="card-body">
				<div class="row">
					<div class="col-8">
						<div class="mt-4 mb-0 text-white">
							<h3 class="mb-0">$ @convert2($totalPOSAmount)</h3>
							<p class="text-white mt-1">Total POS <br>(ST) </p>
						</div>
					</div>
					<div class="col-4">
						<i class="fa fa-money mt-3 mb-0"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-4 col-lg-6 col-md-12 col-sm-12">
		<div class="card card-counter bg-gradient-success shadow-success">
			<div class="card-body">
				<div class="row">
					<div class="col-8">
						<div class="mt-4 mb-0 text-white">
							<h3 class="mb-0">$ @convert2($totalWEBAmount)</h3>
							<p class="text-white mt-1">Total WEB <br>(Web) </p>
						</div>
					</div>
					<div class="col-4">
						<i class="fa fa-money mt-3 mb-0"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	@if(auth()->user()->hasRole('admin'))
	<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
		<div class="card card-counter">
			<div class="card-body">
				<div class="row">
					<div calss="card-title">Total de ventas del empleado:</div>
				</div>
			</div>
		</div>
	</div>
	@foreach($getEmployeeSales as $emSales)
	<?php
		$user = App\User::select('id','name','email')->where('id',$emSales->created_by)->first();
		$empPosSales = App\bookeditem::select('bookeditems.id','bookeditems.itemqty','bookeditems.return_qty','bookeditems.itemPrice')
            ->join('bookings', function ($join) {
                $join->on('bookeditems.bookingId', '=', 'bookings.id');
            })
            ->join('productos', function ($join) {
                $join->on('bookeditems.itemid', '=', 'productos.id');
            })
            ->where('bookings.created_by', '!=', 3)
            ->where('bookings.created_by',$emSales->created_by)
            ->whereNotIn('bookings.deliveryStatus',['Cancel','Return']);
          if(!empty($from_date))
        {

            $empPosSales->whereDate('bookeditems.created_at', '>=', $from_date);

        }
        if(!empty($to_date))
        {

            $empPosSales->whereDate('bookeditems.created_at', '<=', $to_date);

        }

        $empPosSalesList = $empPosSales->get();

        $totalPOSEmpAmount=0;
        foreach ($empPosSalesList as $key => $items) {
          $totalPOSEmpAmount = $totalPOSEmpAmount + (($items->itemqty - $items->return_qty) * $items->itemPrice);
        }
     ?>
	<div class="col-xl-4 col-lg-6 col-md-12 col-sm-12">
		<div class="card card-counter bg-gradient-warning shadow-warning">
			<div class="card-body">
				<div class="row">
					<div class="col-8">
						<div class="mt-4 mb-0 text-white">
							<h3 class="mb-0">$ @convert2($totalPOSEmpAmount) </h3>
							<p class="text-white mt-1">{{ $user->name .''. $user->lastname }} <br>({{ $user->email }}) </p>
						</div>
					</div>
					<div class="col-4">
						<i class="fa fa-money mt-3 mb-0"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	@endforeach
	@endif

</div>

@if($withList=='yes')
<div class="row">
	<div class="col-12">
		<div class="table-responsive">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title ">
						@if(!empty($choose_type))
							<strong>{{$choose_type}} : <span class="text-primary">{{$nombre}}</span>Cant: {{$totalPOSCount}}</strong>
						<br>
						@endif
						<b>Reporte del período @if(empty($from_date)): Registro de últimos 7 días @else Desde: <span class="text-primary">{{empty($from_date) ? date('Y-m-d') : $from_date }} @endif</span> Hasta <span class="text-primary">{{empty($to_date) ? date('Y-m-d') : $to_date }}</span></b></h3>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table id="example" class="table table-striped table-bordered">
							 <thead>
	                            <tr>
	                                <th scope="col">#</th>
	                                <th class="text-center">Fecha</th>
	                                <th class="text-center">Total General <br>(ST + WEB)</th>
	                                <th class="text-center">Total de ventas <br>(ST)</th>
	                                <th class="text-center">Total de ventas <br>(Web)</th>
	                            </tr>
	                        </thead>
							<tbody>
								@php
								$totalPOS = 0;
								$totalWEB = 0;
								@endphp
								@foreach(array_reverse($dateList) as $key => $date)
								@php
								$rec = getProductSalesReport($date, $choose_type, $selected_b_or_m);
								$totalPOS += $rec['totalPOSAmount'];
								$totalWEB += $rec['totalWEBAmount'];
								@endphp
								<tr>
									<td class="text-center"><strong>{{$key+1}}</strong></td>
									<td class="text-center"><strong>{{$date}}</strong></td>
									<td class="text-center"><strong>${{$rec['totalPOSAmount'] + $rec['totalWEBAmount']}}</strong></td>
									<td class="text-center"><strong>${{$rec['totalPOSAmount']}}</strong></td>
									<td class="text-center"><strong>${{$rec['totalWEBAmount']}}</strong></td>
								</tr>
								@endforeach
							</tbody>
							<tfoot>
								<tr>
									<th colspan="2" style="text-align:right">Total:</th>
									<th class="text-center">${{ $totalPOS+$totalWEB }}</th>
									<th class="text-center">${{ $totalPOS }}</th>
									<th class="text-center">${{ $totalWEB }}</th>
								</tr>
							</tfoot>
						</table>
					</div>

				</div>

			</div>
		</div>
	</div>
</div>
@endif

@if($productList=='yes')
<div class="row">
	<div class="col-12">
		<div class="table-responsive">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title ">
						@if(!empty($choose_type))
							<strong>{{$choose_type}} : <span class="text-primary">{{$nombre}}</span>Cant: {{$totalPOSCount}}</strong>
						<br>
						@endif
						<b>Reporte del período @if(empty($from_date)): Registro de últimos 7 días @else Desde: <span class="text-primary">{{empty($from_date) ? date('Y-m-d') : $from_date }} @endif</span> Hasta <span class="text-primary">{{empty($to_date) ? date('Y-m-d') : $to_date }}</span></b>
					</h3>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table id="example2" class="table table-striped table-bordered">
							 <thead>
	                            <tr>
	                                <th scope="col">#</th>
	                                <th class="text-center">&nbsp;&nbsp;&nbsp;Fecha&nbsp;&nbsp;&nbsp;</th>
	                                <th class="text-center">Tipo</th>
	                                <th class="text-center">NOMBRE</th>
	                                <th class="text-center">MARCA</th>
	                                <th class="text-center">MODELO</th>
	                                <th class="text-center">ITEM</th>
	                                <th class="text-center">Precio</th>
	                                <th class="text-center">Cant</th>
	                                <th class="text-center">Devueltas</th>
	                                <th class="text-center">Total</th>
	                            </tr>
	                        </thead>
							<tbody>
								@php $rec = getProductList($from_date, $to_date, $choose_type, $selected_b_or_m) @endphp
								@foreach($rec as $key => $row)
								<tr>
									<td class="text-center"><strong>{{$key+1}}</strong></td>
									<td class="text-center"><strong>{{$row->created_at->format('Y-m-d')}}</strong></td>
									<td><span class="badge badge-default {{\Illuminate\Support\Str::slug(($row->booking->created_by==3) ? 'WEB' :'POS')}}">{{ ($row->booking->created_by==3) ? 'WEB' :'POS' }}</span></td>
									<td>{{@$row->producto->nombre}}</td>
									<td>{{@$row->producto->marca->nombre}}</td>
									<td>{{@$row->producto->modelo->nombre}}</td>
									<td>{{@$row->producto->item->nombre}}</td>
									<td class="text-center"><strong>${{$row->itemPrice}}</strong></td>
									<td class="text-center"><strong>{{$row->itemqty}}</strong></td>
									<td class="text-center"><strong>{{$row->return_qty}}</strong></td>
									<td class="text-center"><strong>${{$row->itemPrice * ($row->itemqty-$row->return_qty)}}</strong></td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>

				</div>

			</div>
		</div>
	</div>
</div>
@endif

<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Total Ventas</h3>
			</div>
			<div class="card-body">
				<div id="highchart4"></div>
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
	function getListType(e)
	{
		if(e.value=='')
		{
			$("#recordFilter").hide();
		}
		else
		{
			$("#recordFilter").show();
		}
		$('#selected_type').html(e.value);
		$.ajax({
			url: "{{route('api.type-list-all')}}",
			type: "POST",
			data: "type="+e.value+"&searchTerm=",
			success:function(info){
				$('#selected-b-or-m-list').html(info);
			}
		});
	}

	$('.selected-b-or-m-list').select2({
	    placeholder: "Enter Name",
	    allowClear: true,
	    ajax: {
	      url: "{{route('api.type-list-all')}}",
	      type: "post",
	      dataType: 'json',
	      delay: 250,
	      data: function (params) {
	          return {
	              searchTerm: params.term, // search term
	              type: $("#choose_type").val(), // selected Type
	          };
	      },
	      processResults: function (response) {
	          return {
	              results: response
	          };
	      },
	      cache: false
	  }
	});

	/* ---hightchart4----*/
	Highcharts.chart('highchart4', {
		chart: {
			type: 'pie',
			options: {
				enabled: true,
				alpha: 45,
				beta: 0
			}
		},
		exporting: {
			enabled: false
		},
		credits: {
			enabled: false
		},
		title: {
			text: ''
		},
		tooltip: {
			pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
		},
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				depth: 35,
				dataLabels: {
					enabled: true,
					format: '{point.name}'
				}
			}
		},
		series: [{
			type: 'pie',
			name: 'Sales',
			data: [
				['Venta Directa: ${!!round($totalPOSAmount, 2)!!}', {!!round($totalPOSAmount, 2)!!}],
				['Venta Website: $ {!!round($totalWEBAmount, 2)!!}', {!!round($totalWEBAmount, 2)!!}]
			],
			colors: ['#ff685c ', '#32cafe']
		}]
	});
</script>
@endsection
