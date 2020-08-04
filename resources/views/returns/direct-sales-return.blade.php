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
		                    Sale Order Return 
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
		                        <div class="form-group">
		                            <label for="booking_id" class="form-label">Order Number <span class="text-danger">*</span></label>
		                            <select name="booking_id" class="form-control order-list-select-2" data-placeholder="Enter Order Number" required="" onchange="getDetail(this)">
		                                <option value='0'>- Search Order -</option>
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
		                		<strong>Warning! </strong> Order Not found. Please try again.
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
		$("#errorShow").hide();
		$("#orderInformation").hide();
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
		    	}
		    }
		});
	}
</script>
@endsection