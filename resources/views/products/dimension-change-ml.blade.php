@extends('layouts.master')
@section('content')

@can('price-change-ml')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title ">Cambio de dimensión ML</h3>
                <div class="card-options">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
            <div class="card-body">
                {{ Form::open(array('route' => 'api.dimension-ml-update', 'class'=> 'form-horizontal', 'autocomplete'=>'off')) }}
                @csrf
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="choose_type" class="form-label">Choose Type <span class="text-danger">*</span></label>
                            <div class="row gutters-xs">
                                <div class="col">
                                    <select name="choose_type" class="form-control" required="" onchange="getListType(this)" id="choose_type">
                                        <option value='Marca' selected="">Marca</option>
                                        <option value='Item'>Item</option>
                                        <option value='Modelo'>Modelo</option>
                                        <option value='Productos'>Productos</option>
                                        <option value='MlaId'>MlaId</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-6">
                        <div class="form-group">
                            <label for="selected-b-or-m-list" class="form-label">Select <span id="selected_type">Marca</span> <span class="text-danger">*</span></label>
                            <div class="row gutters-xs">
                                <div class="col">
                                    <select name="selected_b_or_m" class="form-control selected-b-or-m-list" id="selected-b-or-m-list" data-placeholder="Ingrese el Nombre" required="" onchange="getProductFilteredList(this)">
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

                <div class="row">
                  <div class="col-md-12">
                    <div class="table-responsive" id="product-list-filter"></div>
                  </div>
                </div>
                <div class="form-footer">
                    {!! Form::submit('Guardar', array('class'=>'btn btn-primary btn-block','id'=>'update-ml-price')) !!}
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@endcan

@endsection

@section('extrajs')
<script type="text/javascript">
function getListType(e)
{
  $("#product-list-filter").html('');
  $('#selected_type').html(e.value);
  $.ajax({
      url: "{{route('api.get-selected-type-list-dimension')}}",
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
      url: "{{route('api.get-selected-type-list-dimension')}}",
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


function getProductFilteredList(e)
{
  $('#product-list-filter').hide();
  $.ajax({
      url: "{{route('api.product-list-filter-dimension')}}",
      type: "POST",
      data: "type="+$("#choose_type").val()+"&searchTerm="+e.value,
      success:function(info){
        $('#product-list-filter').html(info);
        $('#product-list-filter').show();
        weightUpdate();
      }
  });
}
</script>
@endsection
