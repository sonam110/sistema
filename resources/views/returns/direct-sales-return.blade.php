@extends('layouts.master')
@section('content')
	@if(Auth::user()->hasAnyPermission(['direct-sales-return']) || Auth::user()->hasRole('admin'))

		{{ Form::open(array('route' => 'sales-order-return-save', 'class'=> 'form-horizontal','enctype'=>'multipart/form-data', 'files'=>true, 'autocomplete'=>'off')) }}
		@csrf
		<div class="row row-deck">
		    <div class="col-lg-12">
		        <div class="card">
		            <div class="card-header">
		                <h3 class="card-title">
		                    Devolución de pedidos
		                </h3>
		                @can('sales-order-list')
		                <div class="card-options">
		                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
		                </div>
		                @endcan
		            </div>
		            <div class="card-body">
		                <div class="row">
		                    <div class="col-md-12">
		                        <div class="form-group">
		                            <label for="booking_id" class="form-label">Pedido Número <span class="text-danger">*</span></label>
		                            <select name="booking_id" class="form-control order-list-select-2" data-placeholder="ingrese Nro. de Pedido" required="" onchange="getDetail(this)">
		                                <option value='0'>- Buscar Pedido -</option>
		                            </select>
		                            @if ($errors->has('booking_id'))
		                            <span class="invalid-feedback" role="alert">
		                                <strong>{{ $errors->first('booking_id') }}</strong>
		                            </span>
		                            @endif
		                        </div>
		                    </div>
		                </div>

		                <div id="orderInformation" style="display: none;"></div>
		                <div id="errorShow" style="display: none;">
		                	<div class="alert alert-warning" role="alert">
		                		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		                		<strong>Precaución! </strong> No se encuentra el pedido, pruebe de nuevo.
		                	</div>
		                </div>

		                <div class="form-footer">
		                    {!! Form::submit('Guardar', array('class'=>'btn btn-primary btn-block','id'=>'getInfoBtn', 'disabled')) !!}
		                </div>
		            </div>
		        </div>
		    </div>
		</div>

		<div id="orderHistory" style="display: none;"></div>
		{{ Form::close() }}
	@endif
@endsection

@section('extrajs')
<script type="text/javascript">
	$('.order-list-select-2').select2({
	    ajax: {
	      url: "{{route('api.get-order-list')}}",
	      type: "post",
	      dataType: 'json',
	      delay: 250,
	      data: function (params) {
	          return {
	              searchTerm: params.term // search term
	          };
	      },
	      processResults: function (response) {
	          return {
	              results: response
	          };
	      },
	      cache: true
	  }
	});

	function getDetail(e)
	{
		$("#getInfoBtn").attr('disabled', true);
		$("#getInfoBtn").val('Loading...');
		$("#errorShow").hide();
		$("#orderInformation").hide();
		$("#orderHistory").hide();
		$.ajax({
		    url: "{{route('api.get-sales-order-information')}}",
		    type: "POST",
		    data: "orderId="+e.value,
		    success:function(info){
		    	if(info=='not-found')
		    	{
		    		$("#errorShow").show();
		    		$("#orderInformation").hide();
		    	}
		    	else
		    	{
		    		$("#errorShow").hide();
		    		$("#orderInformation").html(info);
		    		$("#orderInformation").show();
		    		$("#getInfoBtn").attr('disabled', false);
		    		$("#getInfoBtn").val('Save');
		    		getHistory(e.value);
		    	}
		    }
		});
	}

	function getHistory(orderId) {
		$.ajax({
		    url: "{{route('api.get-sales-order-history')}}",
		    type: "POST",
		    data: "orderId="+orderId,
		    success:function(info){
		    	$("#orderHistory").html(info);
		    	$("#orderHistory").show();
		    }
		});
	}
</script>
@endsection
