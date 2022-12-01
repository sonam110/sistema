
	<div class="row row-cards">
		<div class="col-xl-4 col-lg-6 col-md-12 col-sm-12">
			<div class="card card-counter bg-gradient-primary shadow-primary">
				<div class="card-body">
					<div class="row">
						<div class="col-8">
							<div class="mt-4 mb-0 text-white">
								<h3 class="mb-0">{{ $totalStockSum }}</h3>
								<p class="text-white mt-1">Total General </p>
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
								<h3 class="mb-0">{{ $totalStockSum }}</h3>
								<p class="text-white mt-1">Total</p>
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


	@if($productList=='yes')
	<div class="row">
		<div class="col-12">
			<div class="table-responsive">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title ">
							@if(!empty($choose_type))
								<strong>{{$choose_type}} : <span class="text-primary">{{$nombre}}</span></strong>
							<br>
							@endif
							
						</h3>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table id="example2" class="table table-striped table-bordered">
								 <thead>
		                            <tr>
		                                <th scope="col" class="text-center">#</th>
		                                @if($choose_type=='Marca')
		        						<th class="text-center">MARCA</th>
		        						@endif
		        						@if($choose_type=='Modelo')
		                                <th class="text-center">MODELO</th>
		                                @endif
		        						<th class="text-center">NOMBRE</th>
		        						
		                                <th class="text-center">Existencias</th>
		                            </tr>
		                        </thead>
								<tbody>
									
									@foreach($totalProducts as $key => $row)
									<tr>
										<td class="text-center"><strong>{{$key+1}}</strong></td>
										@if($choose_type=='Marca')
										<td>{{@$row->marca->nombre}}</td>
										@endif
										@if($choose_type=='Modelo')
										<td>{{@$row->modelo->nombre}}</td>
										@endif
										<td class="text-center">{{@$row->nombre}}</td>

										<td class="text-center">{{@$row->stock}}</td>
										
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

<script type="text/javascript">
$(function(e) {
	$('#example2').DataTable();
} );

</script>

