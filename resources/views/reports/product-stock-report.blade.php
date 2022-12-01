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
				{{ Form::open(array('route' => 'product-stock-report', 'id'=>'myform','class'=> 'form-horizontal', 'autocomplete'=>'off')) }}
				@csrf
				<div class="row">
					

					<div class="col-lg-2 col-md-2 col-sm-4">
						<div class="form-group">
							<label for="from_date" class="form-label">Productos Lista </label>
							<label class="custom-control custom-checkbox">
                                <input class="colorinput-input custom-control-input" id="productList" name="productList" type="checkbox" value="yes" {{($productList=='yes') ? 'checked' : '' }}>
                                <span class="custom-control-label">Si</span>
                            </label>
						</div>
					</div> 

					<div class="col-lg-2 col-md-2 col-sm-3">
                        <div class="form-group">
                            <label for="choose_type" class="form-label">Filtrar por <span class="text-danger">*</span></label>
                            <div class="row gutters-xs">
                                <div class="col">
                                    <select name="choose_type" class="form-control" onchange="getListType(this)" id="choose_type">
                                        <option value='' selected="" disabled>All</option>
                                        <option value='Marca' {{($choose_type=='Marca') ? 'selected' : '' }}>Marca</option>

                                        <option value='Modelo' {{($choose_type=='Modelo') ? 'selected' : '' }}>Modelo</option>
                                    
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-4" id="recordFilter" style="display: none;">
                        <div class="form-group">
                            <label for="selected-b-or-m-list" class="form-label">Select  <span id="selected_type">Marca</span> </label>
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
					
				</div>
				{{ Form::close() }}
			</div>
		</div>
	</div>
</div>
<div id="row-data">
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

<!-- -----Product List Table -->

</div>


@endif
@endsection
@section('extrajs')

<script>
	$(document).ready(function(){
		$('#choose_type,.selected-b-or-m-list,#productList').on('change', function(e) {
			
			var productList = $("input#productList:checked").val();
			var choose_type = $('#choose_type').val();
			var selected_b_or_m = $('.selected-b-or-m-list').val();
		     $.ajax({
				url: "{{route('product-stock-report-filter')}}",
				type: "POST",
				data: {productList:productList,choose_type:choose_type,selected_b_or_m:selected_b_or_m},
				success:function(info){
					$('#row-data').html(info);
				}
			});
	    });
	  
	 });



	function getListType(e)
	{
		$('#selected-b-or-m-list').html('');
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

	
</script>
@endsection
