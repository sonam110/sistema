@extends('layouts.master')
@section('content')

@can('price-change-ml')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title ">ML list shipping mode ME1</h3>
                <div class="card-options">
                    <a href="{{ route('api.sync-shipping-info-from-ml') }}"  onClick="return confirm('Are you sure you want to sync shipping mode from ML to Local Database?');" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="right" title="" data-original-title="Sync shipping mode form ML: 100 record at a time, Sync till 0 record return"><i class="fa fa-refresh"></i></a>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
            <div class="card-body">
                {{ Form::open(array('route' => 'api.ml-list-shipping-mode-me1-update', 'class'=> 'form-horizontal', 'autocomplete'=>'off')) }}
                @csrf
                <div class="row">
                    <div class="col-md-2 col-sm-6">
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

                    <div class="col-md-4 col-sm-6">
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

                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="shipping_mode" class="form-label">Choose shipping mode <span class="text-danger">*</span></label>
                            <div class="row gutters-xs">
                                <div class="col">
                                    <select name="shipping_mode" class="form-control" required="" id="shipping_mode">
                                        <option value='not_specified' selected>Not Specified</option>
                                        <option value='me1'>ME1</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="sale_terms" class="form-label">Sale Terms</label>
                            <div class="row gutters-xs">
                                <div class="col">
                                    <select name="sale_terms" class="form-control select2" required="" id="sale_terms">
                                        <option value='No-Change'>No Change</option>
                                        <option value='NULL'>Set NULL</option>
                                        @for($i=1; $i<=45; $i++)
                                          <option value='{{ $i }} días'>{{ $i }} días</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <small class="text-info">MANUFACTURING TIME</small>
                        </div>
                    </div>

                </div>

                <div class="row">
                  <div class="col-md-12">
                    <div class="table-responsive" id="product-list-filter"></div>
                  </div>
                </div>
                <div class="form-footer">
                    {!! Form::submit('Ejecutar', array('class'=>'btn btn-primary btn-block','id'=>'update-ml-price')) !!}
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
      url: "{{route('api.get-selected-type-list-shipping-info')}}",
      type: "POST",
      data: "type="+e.value+"&searchTerm=",
      success:function(info){
        $('#selected-b-or-m-list').html(info);
      }
  });
}

$('.select2').select2();

$('.selected-b-or-m-list').select2({
    placeholder: "Enter Name",
    allowClear: true,
    ajax: {
      url: "{{route('api.get-selected-type-list-shipping-info')}}",
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
      url: "{{route('api.product-list-filter-having-me1-status')}}",
      type: "POST",
      data: "type="+$("#choose_type").val()+"&searchTerm="+e.value,
      success:function(info){
        $('#product-list-filter').html(info);
        $('#product-list-filter').show();
      }
  });
}
</script>
@endsection
