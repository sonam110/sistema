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
				{{ Form::open(array('route' => 'sales-report-new', 'class'=> 'form-horizontal', 'autocomplete'=>'off')) }}
				@csrf
				<div class="row">
					<div class="col-lg-2 col-md-2 col-sm-2">
						<div class="form-group">
							<label for="from_date" class="form-label">Mostrar Lista </label>
							<label class="custom-control custom-checkbox">
                                <input class="colorinput-input custom-control-input" id="withList" name="withList" type="checkbox" value="yes" {{($withList=='yes') ? 'checked' : '' }}>
                                <span class="custom-control-label">Si</span>
                            </label>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-5">
						<div class="form-group">
							<label for="from_date" class="form-label">Desde</label>
							{!! Form::date('from_date',@$from_date,array('id'=>'from_date','class'=> 'form-control', 'placeholder'=>'From Date', 'autocomplete'=>'off','required')) !!}
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-5">
						<div class="form-group">
							<label for="from_date" class="form-label">Hasta</label>
							<div class="row gutters-xs">
								<div class="col">
									{!! Form::date('to_date',@$to_date,array('id'=>'to_date','class'=> 'form-control', 'placeholder'=>'To Date', 'autocomplete'=>'off')) !!}
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

<div class="row row-cards">
	<div class="col-xl-4 col-lg-6 col-md-12 col-sm-12">
		<div class="card card-counter bg-gradient-primary shadow-primary">
			<div class="card-body">
				<div class="row">
					<div class="col-8">
						<div class="mt-4 mb-0 text-white">
							<h3 class="mb-0">$ @convert2($totalPOSSaleAmount + $totalWEBSaleAmount)</h3>
							<p class="text-indigo mt-1">- $ @convert2($totalPOSInterestSaleAmount + $totalWEBInterestSaleAmount) (Intereses)</p>
							<p class="text-white mt-1">Total Vendido <br>(ST + WEB)</p>
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
							<h3 class="mb-0">$ @convert2($totalPOSSaleAmount)</h3>
							<p class="text-indigo mt-1">- $ @convert2($totalPOSInterestSaleAmount) (Intereses)</p>
							<p class="text-white mt-1">Total de Ventas Pos <br>(ST) </p>
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
							<h3 class="mb-0">$@convert2($totalWEBSaleAmount)</h3>
							<p class="text-indigo mt-1">- $ @convert2($totalWEBInterestSaleAmount) (Intereses)</p>
							<p class="text-white mt-1">Total de Ventas Web <br>(Web) </p>
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
		<div class="card card-counter bg-gradient-warning shadow-warning">
			<div class="card-body">
				<div class="row">
					<div class="col-8">
						<div class="mt-4 mb-0 text-white">
							<h3 class="mb-0">$ @convert2($totalPOSSaleAmount)</h3>
							<p class="text-white mt-1">Venta por tipo de pago
                            <table width="100%">
                            @foreach($totalPOSSalePaids as $key => $payment)
                               <tr>
                                 <td>{{$vecPaids[$payment->payment_mode]}}</td><td align="right">@convert2($payment->total)</td>
                               </tr>
                            @endforeach
                            </table>
                            </p>
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
		<div class="card card-counter bg-gradient-info shadow-info">
			<div class="card-body">
				<div class="row">
					<div class="col-8">
						<div class="mt-4 mb-0 text-white">
							<h3 class="mb-0">$ @convert2($totalINSSaleIns) </h3>
							<p class="text-white mt-1">Cobro de Saldos pendientes
                            <table width="100%">
                            @foreach($totalINSSaleAmountPaids as $key => $payment)
                               <tr>
                                 <td>{{$vecPaids[$payment->payment_mode]}}</td><td align="right">@convert2($payment->total)</td>
                               </tr>
                            @endforeach
                            </table>
                            </p>
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
							<!-- <h3 class="mb-0">$ @convert2($totalINSSaleIns) </h3> -->
							<p class="text-white mt-1">Total ventas por Vendedor
                            <table width="100%">
															@foreach($totalBookUsers4 as $key => $userA)
															@foreach($userA as $key2 => $userB)
															@if ($key2!=0)
															<tr>
																<td>{{$userB->lastname}} {{$userB->add}}</td><td align="right">@convert2($userB->total)</td>
															</tr>
															@endif
															@endforeach
															@endforeach
                            </table>
                            </p>
						</div>
					</div>
					<div class="col-4">
						<i class="fa fa-money mt-3 mb-0"></i>
					</div>
				</div>
			</div>
		</div>
  </div>


  </div>

  <div class="row row-cards">
  @foreach($totalBookUsers3 as $key => $userA)
	<div class="col-xl-3 col-lg-6 col-md-12 col-sm-12">
		<div class="card card-counter bg-secondary shadow-info">
			<div class="card-body">
				<div class="row">
					<div class="col-8_">
						<div class="mt-4 mb-0 text-white">
							<h3 class="mb-0">$ @convert2($userA[0]) </h3>
							<p class="text-white mt-0">Cobros <b>{{$key}}</b>
                            <table width="100%">
                               @foreach($userA as $key2 => $userB)
                               @if ($key2!=0)
                               <tr>
                                 <td>{{$vecPaids[$userB->payment_mode]}} {{$userB->add}}</td><td align="right">@convert2($userB->total)</td>
                               </tr>
                               @endif
                               @endforeach
                            </table>
                            </p>
						</div>
					</div>
				</div>
			</div>
		</div>
  </div>
  @endforeach


</div>



@if($withList=='yes')
<div class="row">
	<div class="col-12">
		<div class="table-responsive">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title "><b>Reporte del período @if(empty($from_date)): Registro de últimos 7 días @else Desde: <span class="text-primary">{{empty($to_date) ? date('Y-m-d') : $to_date }} @endif</span> Hasta <span class="text-primary">{{$from_date}}</span></b></h3>
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
	                                <th class="text-center">Total Tarjetas <br>(ST)</th>
	                                <th class="text-center">Total Efectivo <br>(ST)</th>
	                            </tr>
	                        </thead>
							<tbody>
								@foreach(array_reverse($dateList) as $key => $date)
								@php $rec = getSalesReport($date) @endphp
								<tr>
									<td class="text-center"><strong>{{$key+1}}</strong></td>
									<td class="text-center"><strong>{{$date}}</strong></td>
									<td class="text-center"><strong>${{$rec['totalPOSSaleAmount'] + $rec['totalWEBSaleAmount']}}</strong></td>
									<td class="text-center"><strong>${{$rec['totalPOSSaleAmount']}}</strong></td>
									<td class="text-center"><strong>${{$rec['totalWEBSaleAmount']}}</strong></td>
									<td class="text-center"><strong>${{$rec['totalPOSSalePaymentMethodAmount']}}</strong></td>
									<td class="text-center"><strong>${{$rec['totalPOSSaleCashAmount']}}</strong></td>
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
				['Venta Directa: ${!!round($totalPOSSaleAmount, 2)!!}', {!!round($totalPOSSaleAmount, 2)!!}],
				['Venta Website: $ {!!round($totalWEBSaleAmount, 2)!!}', {!!round($totalWEBSaleAmount, 2)!!}]
			],
			colors: ['#ff685c ', '#32cafe']
		}]
	});
</script>
@endsection
