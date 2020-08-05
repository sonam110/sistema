<div class="row row-deck">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    This Purchase Order Return History 
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
		                                <th>Product Name</th>
		                                <th>Returned Qty</th>
		                                <th>Returned Amount</th>
		                                <th>Returned Date</th>
		                                <th>Return Note</th>
		                            </tr>
		                        </thead>
		                        <tbody>
		                        	@forelse($poInfo->purchaseOrderReturns as $key => $product)
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
			                                	${{$product->return_price}}
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
							                		<strong>Info! </strong> Purchase return history not found.
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