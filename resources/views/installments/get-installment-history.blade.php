<div class="row row-deck">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    This Sale Order Installment History
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                    	<div class="table-responsive">
		                    <table id="example" class="table table-striped table-bordered">
								<thead>
									<tr>
										<th scope="col">#</th>
										<th>Creado por</th>
										<th>Monto</th>
										<th>Fecha de Pago</th>
									</tr>
								</thead>
								<tbody>
									@foreach($saleInfo->bookingInstallmentPaids as $key => $installment)
									<tr>
										<td>{{$key+1}}</td>
										<td>{{$installment->createdBy->name}} {{$installment->createdBy->lastname}}</td>
										<td>{{$installment->amount}}</td>
										<td>{{$installment->created_at->format('Y-m-d')}}</td>
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
</div>
