@extends('layouts.master')
@section('content')
{!! Html::style('assets/js/bootstrap-fileupload/bootstrap-fileupload.css') !!}
@can('price-change-ml')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title ">Actualizar precio a través de Excel</h3>
                <div class="card-options">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
            <div class="card-body">
                {{ Form::open(array('route' => 'export-import-product', 'class'=> 'form-horizontal','enctype'=>'multipart/form-data', 'files'=>true, 'autocomplete'=>'off')) }}
                @csrf
                <div class="row">
                    <div class="col-md-4 col-sm-4">
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

                    <div class="col-md-4 col-sm-4">
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

                 
                    
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                                            <label for="file" class="form-label">Import/Export</label>
                                            
                                            <div>
                                                <span class="btn btn-danger btn-file">
                                                    <span class="fileupload-new"><i class="fa fa-paper-clip"></i> Choose File</span>
                                                    {!! Form::file('file',array('id'=>'file','data-icon'=>'false', 'accept'=>'.csv',  'onchange'=> 'readURL(this)')) !!}

                                                </span> 
                                                 {!! Form::submit('Uplaod', array('class'=>'btn btn-primary','title'=>'Import & Change Price','name'=>'submit_type','value'=>'Uplaod')) !!}
                                                    <button type="submit" class="btn btn-primary btn-icon-primary tooltips" data-placement="top" title="download sample file" name="submit_type" value="export"> <i class="fa fa-download"></i></button>
                                            </div>
                                            @if ($errors->has('file'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('file') }}</strong>
                                            </span>
                                            @endif

                                        </div>
                                 
                                </div>
                        </div>

                <div class="row">
                  <div class="col-md-12">
                    <div class="table-responsive" id="product-list-filter"></div>
                  </div>
                </div>
                <div class="form-footer">
                    {!! Form::submit('Save', array('class'=>'btn btn-primary btn-block','name'=>'submit_type','value'=>'Save')) !!}
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
      url: "{{route('api.get-selected-type-list')}}",
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
      url: "{{route('api.get-selected-type-list')}}",
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


function changeTitle(e)
{
    if(e.value=='Both'){
        $('#fixed-price-div').css('display', 'block');
        $('#percentage_amount_text').html('Percentage');
    } else{
        $('#fixed-price-div').css('display', 'none');
        $('#percentage_amount_text').html(e.value);
    }
    priceUpdate();
}
function priceUpdate() {
    var $tblrows = $("#product-table tbody tr");
    var calculation_type = $("#calculation_type").val();
    var percentage_amount = $("#percentage_amount").val();
    var fixed_amount = $("#fixed_amount").val();
    $tblrows.each(function (index) {
        var $tblrow = $(this);
        var price = $tblrow.find(".current-price").val();
        if(calculation_type=='Amount')
        {
          var newPrice = (parseFloat(price) + parseFloat(percentage_amount));
            // var newPrice = parseFloat(percentage_amount) + parseFloat(price);  number_format($money, 0,',','.')
        }
        else if(calculation_type=='Percentage')
        {
            var newPrice = (parseFloat(price) + (parseFloat(percentage_amount) * parseFloat(price))/100);
          //  alert(price);
        }
        else
        {
            var newPrice = (parseFloat(price)+ parseFloat(fixed_amount) + (parseFloat(percentage_amount) * parseFloat(price))/100);
        }
        if (!isNaN(newPrice)) {
            $tblrow.find('.changed-price').html(newPrice.toFixed(0));
            //alert(newPrice);
        }
    });
}

function getProductFilteredList(e)
{
  $('#product-list-filter').hide();
  $.ajax({
      url: "{{route('product-list-filter-excel')}}",
      type: "POST",
      data: "type="+$("#choose_type").val()+"&searchTerm="+e.value,
      success:function(info){
        $('#product-list-filter').html(info);
        $('#product-list-filter').show();
        priceUpdate();
      }
  });
}
</script>
@endsection
