<div class="row row-deck">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Historial de devoluciones en esta Venta 
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
		                                <th>Producto</th>
		                                <th>Cantidad Devuelta</th>
		                                <th>Monto Devuelto</th>
		                                 <th>Fecha Devolución</th>
		                                 <th>Nota de Devolución</th>
		                            </tr>
		                        </thead>
		                        <tbody>
		                        	@forelse($saleInfo->salesOrderReturns as $key => $product)
		                        		<tr>
			                                <td scope="col">{{$key+1}}</td>
			                                <td>{{$product->producto->nombre}}</td>
			                                <td>
			                                	<strong>
			                                	{{$product->return_qty}}
			                                	</strong>
			                                </td>
			                                <td>
			                                	<strong>
			                                	${{$product->return_amount}}
			                                	</strong>
			                                </td>
			                                <td>{{$product->created_at->format('Y-m-d')}}</td>
			                                <td>{{$product->return_note}}</td>
			                            </tr>
		                        	@empty
		                        		<tr>
			                                <td colspan="6" scope="col">
			                                	<div class="alert alert-warning" role="alert">
							                		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							                		<strong>Info! </strong> Sale return history not found.
							                	</div>
			                                </td>
			                            </tr>
		                        	@endforelse
		                        </tbody>
		                    </table>
		                </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
