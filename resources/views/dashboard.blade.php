@extends('layouts.master')
@section('content')
<div class="row row-cards">
	<div class="col-xl-3 col-lg-6 col-md-12 col-sm-12">
		<div class="card card-counter bg-gradient-primary shadow-primary">
			<div class="card-body">
				<div class="row">
					<div class="col-8">
						<div class="mt-4 mb-0 text-white">
							<h3 class="mb-0">100</h3>
							<p class="text-white mt-1">Revenue </p>
						</div>
					</div>
					<div class="col-4">
						<i class="fa fa-money mt-3 mb-0"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-lg-6 col-md-12 col-sm-12">
		<div class="card card-counter bg-gradient-secondary shadow-secondary">
			<div class="card-body">
				<div class="row">
					<div class="col-8">
						<div class="mt-4 mb-0 text-white">
							<h3 class="mb-0">20</h3>
							<p class="text-white mt-1">Sale Return</p>
						</div>
					</div>
					<div class="col-4">
						<i class="fa fa-cart-arrow-down mt-3 mb-0"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-lg-6 col-md-12 col-sm-12">
		<div class="card card-counter bg-gradient-warning shadow-warning">
			<div class="card-body">
				<div class="row">
					<div class="col-8">
						<div class="mt-4 mb-0 text-white">
							<h3 class="mb-0">0</h3>
							<p class="text-white mt-1">Purchase Return</p>
						</div>
					</div>
					<div class="col-4">
						<i class="fa fa-cart-arrow-down mt-3 mb-0"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-lg-6 col-md-12 col-sm-12">
		<div class="card card-counter bg-gradient-success shadow-success">
			<div class="card-body">
				<div class="row">
					<div class="col-8">
						<div class="mt-4 mb-0 text-white">
							<h3 class="mb-0">14,563</h3>
							<p class="text-white mt-1">Total Sale</p>
						</div>
					</div>
					<div class="col-4"> <i class="fa fa-bar-chart mt-3 mb-0"></i> </div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row row-cards">
	<div class="col-lg-12 col-sm-12">
		<div class="card ">
			<div class="card-header">
				<h3 class="card-title">Monthly Report</h3>
			</div>
			<div class="card-body text-center">
				<div id="echart1" class="chartsh chart-dropshadow"></div>
			</div>
		</div>
	</div>
</div>

<div class="row row-cards">
	<div class="col-lg-12 col-sm-12">
		<div class="card ">
			<div class="card-header">
				<h3 class="card-title">Recent Transaction (last 30 Records)</h3>
			</div>
			<div class="card-body text-center">
				<div class="table-responsive">
					<table class="table table-hover card-table table-striped table-vcenter table-outline text-nowrap">
						<thead>
							<tr>
								<th scope="col">#</th>
								<th scope="col">Employee</th>
								<th scope="col">Order No.</th>
								<th scope="col">Date</th>
								<th scope="col">Amount</th>
								<th scope="col">Payment Mode</th>
								<th scope="col">Order Status</th>
								<th scope="col">Action</th>
							</tr>
						</thead>
						<tbody>
							@foreach(getLast30DaysSale() as $key => $record)
							<tr>
								<th scope="row">{{$key+1}}</th>
								<td>{{($record->createdBy) ? $record->createdBy->name : 'Website'}}</td>
								<td>{{$record->tranjectionid}}</td>
								<td>{{$record->created_at->format('Y-m-d')}}</td>
								<td>${{$record->payableAmount}}</td>
								<td>{{$record->paymentThrough}}</td>
								<td>
									@if ($record->deliveryStatus == 'Process')
										<span class="badge badge-info">{{$record->deliveryStatus}}</span>
									@elseif ($record->deliveryStatus == 'Cancel')
						            	<span class="badge badge-danger">{{$record->deliveryStatus}}</span>
						            @elseif ($record->deliveryStatus == 'Delivered')
						            	<span class="badge badge-success">{{$record->deliveryStatus}}</span>
						            @elseif ($record->deliveryStatus == 'Return')
						            	<span class="badge badge-danger">{{$record->deliveryStatus}}</span>
						            @else
						            	<span class="badge badge-default">{{$record->deliveryStatus}}</span>
						            @endif
								</td>
								<td>
									<div class="btn-group btn-group-xs">
										@can('sales-order-view')
										<a class="btn btn-sm btn-info" href="{{route('sales-order-view',base64_encode($record->id))}}" data-toggle="tooltip" data-placement="top" title="View Order" data-original-title="View Order"><i class="fa fa-eye"></i></a>
										@endcan
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
@section('extrajs')
{!! Html::script('assets/plugins/peitychart/jquery.peity.min.js') !!}
{!! Html::script('assets/plugins/peitychart/peitychart.init.js') !!}
{!! Html::script('assets/plugins/echarts/echarts.js') !!}
<script type="text/javascript">
$(function(e) {
	'use strict'
	var chartdata = [{
		name: 'Sale',
		type: 'bar',
		data: [@php echo getLast30DaysSaleCounts(); @endphp]
	}];
	var chart = document.getElementById('echart1');
	var barChart = echarts.init(chart);
	var option = {
		grid: {
			top: '6',
			right: '0',
			bottom: '17',
			left: '25',
		},
		xAxis: {
			data: [@php echo getLast30Days(); @endphp],
			axisLine: {
				lineStyle: {
					color: '#eaeaea'
				}
			},
			axisLabel: {
				fontSize: 10,
				color: '#000'
			}
		},
		tooltip: {
			show: true,
			showContent: true,
			alwaysShowContent: true,
			triggerOn: 'mousemove',
			trigger: 'axis',
			axisPointer: {
				label: {
					show: false,
				}
			}
		},
		yAxis: {
			splitLine: {
				lineStyle: {
					color: '#eaeaea'
				}
			},
			axisLine: {
				lineStyle: {
					color: '#eaeaea'
				}
			},
			axisLabel: {
				fontSize: 10,
				color: '#000'
			}
		},
		series: chartdata,
		color: ['#ff685c ', '#32cafe', ]
	};
	barChart.setOption(option);
});
</script>
@endsection
