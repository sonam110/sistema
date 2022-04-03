@extends('layouts.master')
@section('content')

@if(Request::segment(1)==='purchase-invoice-create')

	@if(Auth::user()->hasAnyPermission(['purchase-invoice-create']) || Auth::user()->hasRole('admin'))

		{{ Form::open(array('route' => 'purchase-invoice-save', 'class'=> 'form-horizontal','enctype'=>'multipart/form-data', 'files'=>true, 'autocomplete'=>'off')) }}
		@csrf
		<div class="row row-deck">
		    <div class="col-lg-12">
		        <div class="card">
		            <div class="card-header">
		                <h3 class="card-title">
		                    Nueva Factura
		                </h3>
		                @can('purchase-invoice-list')
		                <div class="card-options">
		                    <a href="{{ route('purchase-invoice-list') }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
		                </div>
		                @endcan
		            </div>
		            <div class="card-body">
		                <div class="row">
		                    <div class="col-md-4">
		                    	<div class="form-group">
									<label for="supplier_id" class="form-label">Proveedor <span class="text-danger">*</span></label>
									<div class="row gutters-xs">
										<div class="col">
											<select name="supplier_id" class="form-control supplier-list-select-2" data-placeholder="Entre Nombre Proveedor" required="">
				                                <option value='0'>- Buscar Proveedor -</option>
				                            </select>
										</div>
										@if(Auth::user()->hasAnyPermission(['supplier-create']) || Auth::user()->hasRole('admin'))
										<span class="col-auto" data-toggle="tooltip" data-placement="top" title="" data-original-title="Agregar Proveedor">
											<button class="btn btn-primary" type="button" data-toggle="modal" data-target="#add-modal" id="add-modal-id"><i class="fe fe-plus"></i></button>
										</span>
										@endif
										@if ($errors->has('supplier_id'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('supplier_id') }}</strong>
			                            </span>
			                            @endif
									</div>
								</div>
		                    </div>

		                    <div class="col-md-4">
		                        <div class="form-group">
		                            <label for="po_date" class="form-label">Fecha <span class="text-danger">*</span></label>
		                            {!! Form::date('po_date',null,array('id'=>'po_date','class'=> $errors->has('po_date') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Fecha', 'autocomplete'=>'off','required'=>'required')) !!}
		                            @if ($errors->has('po_date'))
		                            <span class="invalid-feedback" role="alert">
		                                <strong>{{ $errors->first('po_date') }}</strong>
		                            </span>
		                            @endif
		                        </div>
		                    </div>
                            
		                    <div class="col-md-4">
		                    	<div class="form-group">
									<label for="concept_id" class="form-label">Concepto <span class="text-danger">*</span></label>
									<div class="row gutters-xs">
										<div class="col">
											<select name="concept_id" class="form-control concept-list-select-2" data-placeholder="Entre Concepto" required="required">
				                                <option value='0'>- Buscar Concepto -</option>
				                            </select>
										</div>
										@if ($errors->has('concept_id'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('concept_id') }}</strong>
			                            </span>
			                            @endif
									</div>
								</div>
		                    </div>                            

		                    <div class="col-md-4">
		                        <div class="form-group1">
                                    <label for="po_no" class="form-label">Numero (0000-00000000) </label>
 									<div class="row">
                                     <div class="col">
                                     <select name="type" class="form-control" data-placeholder="" required="">
   	                                  <option value='2'>FAC</option>
                                      <option value='3'>NC</option>
   	                                </select>
		                            </div>
 									<div class="col">
                                    {!! Form::text('po_no',null,array('id'=>'po_no','class'=> $errors->has('po_no') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Numero', 'autocomplete'=>'off','required'=>'required')) !!}
                                    </div>
                                   </div> 
                                </div>
		                    </div>
                            
		                    <div class="col-md-4">
		                        <div class="form-group">
		                            <label for="total_amount" class="form-label">Subtotal <span class="text-danger">*</span></label>
		                            {!! Form::number('total_amount','0',array('id'=>'total_amount','class'=> $errors->has('total_amount') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Subtotal', 'autocomplete'=>'off','required'=>'required','step'=>'any', 'min'=>'0')) !!}
		                            @if ($errors->has('total_amount'))
		                            <span class="invalid-feedback" role="alert">
		                                <strong>{{ $errors->first('total_amount') }}</strong>
		                            </span>
		                            @endif
		                        </div>
		                    </div>
                            
		                    <div class="col-md-4">
		                        <div class="form-group">
		                            <label for="tax_percentage" class="form-label">Iva % <span class="text-danger">*</span></label>
		                            {!! Form::number('tax_percentage','0',array('id'=>'tax_percentage','class'=> $errors->has('tax_percentage') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Iva %', 'autocomplete'=>'off','required'=>'required','step'=>'any', 'min'=>'0', 'max'=>'30')) !!}
		                            @if ($errors->has('tax_percentage'))
		                            <span class="invalid-feedback" role="alert">
		                                <strong>{{ $errors->first('tax_percentage') }}</strong>
		                            </span>
		                            @endif
		                        </div>
		                    </div>
                            
		                    <div class="col-md-4">
		                        <div class="form-group">
		                            <label for="perc_iibb" class="form-label">IIBB <span class="text-danger"></span></label>
		                            {!! Form::number('perc_iibb','0',array('id'=>'perc_iibb','class'=> $errors->has('perc_iibb') ? 'form-control is-invalid state-invalid' : 'form-control', 'autocomplete'=>'off','step'=>'any','min'=>'0')) !!}
		                            @if ($errors->has('perc_iibb'))
		                            <span class="invalid-feedback" role="alert">
		                                <strong>{{ $errors->first('perc_iibb') }}</strong>
		                            </span>
		                            @endif
		                        </div>
		                    </div>

		                    <div class="col-md-4">
		                        <div class="form-group">
		                            <label for="perc_iva" class="form-label">Percp IVA <span class="text-danger"></span></label>
		                            {!! Form::number('perc_iva','0',array('id'=>'perc_iva','class'=> $errors->has('perc_iva') ? 'form-control is-invalid state-invalid' : 'form-control','step'=>'any', 'autocomplete'=>'off', 'min'=>'0')) !!}
		                            @if ($errors->has('perc_iva'))
		                            <span class="invalid-feedback" role="alert">
		                                <strong>{{ $errors->first('perc_iva') }}</strong>
		                            </span>
		                            @endif
		                        </div>
		                    </div>

		                    <div class="col-md-4">
		                        <div class="form-group">
		                            <label for="perc_gan" class="form-label">Percp Ganancias <span class="text-danger"></span></label>
		                            {!! Form::number('perc_gan','0',array('id'=>'perc_gan','class'=> $errors->has('perc_gan') ? 'form-control is-invalid state-invalid' : 'form-control','step'=>'any', 'autocomplete'=>'off', 'min'=>'0')) !!}
		                            @if ($errors->has('perc_gan'))
		                            <span class="invalid-feedback" role="alert">
		                                <strong>{{ $errors->first('perc_gan') }}</strong>
		                            </span>
		                            @endif
		                        </div>
		                    </div>
                            
		                    <div class="col-md-12">
		                        <div class="form-group">
		                            <label for="remark" class="form-label">Observaciones </label>
		                            {!! Form::text('remark',null,array('id'=>'remark','class'=> $errors->has('remark') ? 'form-control is-invalid state-invalid' : 'form-control', 'placeholder'=>'Comentarios', 'autocomplete'=>'off')) !!}
		                        </div>
		                    </div>

		                </div>

		                <div class="row">
		                    <div class="col-md-12">
		                        <table class="table">
	                                <tr>
	                                    <th width="80%"  class="text-right">Iva</th>
	                                    <th>{!! Form::number('tax_amount',null,array('id'=>'tax_amount','class'=> $errors->has('tax_amount') ? 'form-control is-invalid state-invalid tax_amount' : 'form-control tax_amount', 'placeholder'=>'Iva', 'autocomplete'=>'off','required'=>'required','min'=>'1','step'=>'any', 'readonly')) !!}</th>
	                                </tr>
	                                <tr>
	                                    <th class="text-right">Total</th>
	                                    <th>{!! Form::number('gross_amount',null,array('id'=>'gross_amount','class'=> $errors->has('gross_amount') ? 'form-control is-invalid state-invalid gross_amount' : 'form-control gross_amount', 'placeholder'=>'Total', 'autocomplete'=>'off','required'=>'required','min'=>'1','step'=>'any', 'readonly')) !!}</th>
	                                </tr>
		                        </table>
		                    </div>
		                </div>

		                <div class="form-footer">
		                    {!! Form::submit('Guardar', array('class'=>'btn btn-primary btn-block')) !!}
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
		{{ Form::close() }}

	@endif
@elseif(Request::segment(1)==='purchase-invoice-view')
	@can('purchase-invoice-view')
	<style>
	    .invoice-box {
	        margin: auto;
	        padding: 10px;
	        border: 1px solid #eee;
	        box-shadow: 0 0 10px rgba(0, 0, 0, .15);
	        font-size: 16px;
	        line-height: 24px;
	        font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
	        color: #555;
	    }
	    .text-center{
	    	text-align: center!important;
	    }
	    .uppercase{
	    	text-transform: uppercase;
	    }
	    .invoice-box table {
	        width: 100%;
	        line-height: inherit;
	        text-align: left;
	    }

	    .invoice-box table td {
	        padding: 5px;
	        vertical-align: top;
	    }

	    .invoice-box table tr td:nth-child(2) {
	        text-align: right;
	    }

	    .invoice-box table tr.top table td {
	        padding-bottom: 20px;
	    }

	    .invoice-box table tr.top table td.title {
	        font-size: 45px;
	        line-height: 45px;
	        color: #333;
	    }

	    .invoice-box table tr.information table td {
	        padding-bottom: 10px;
	    }

	    .invoice-box table tr.heading td {
	        background: #eee;
	        border-bottom: 1px solid #ddd;
	        font-weight: bold;
	    }

	    .invoice-box table tr.details td {
	        padding-bottom: 20px;
	    }

	    .invoice-box table tr.item td{
	        border-bottom: 1px solid #eee;
	    }

	    .invoice-box table tr.item.last td {
	        border-bottom: none;
	    }

	    .invoice-box table tr.total td:nth-child(2) {
	        border-top: 2px solid #eee;
	        font-weight: bold;
	    }

	    @media only screen and (max-width: 600px) {
	        .invoice-box table tr.top table td {
	            width: 100%;
	            display: block;
	            text-align: center;
	        }

	        .invoice-box table tr.information table td {
	            width: 100%;
	            display: block;
	            text-align: center;
	        }
	    }

	    /** RTL **/
	    /*.rtl {
	        direction: rtl;
	        font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
	    }

	    .rtl table {
	        text-align: right;
	    }

	    .rtl table tr td:nth-child(2) {
	        text-align: left;
	    }*/
    </style>
	<div class="row">
	    <div class="col-12">
	        <div class="card">
	            <div class="card-header ">
	                <h3 class="card-title ">Información de la Factura</h3>
	                <div class="card-options">
	                    @can('purchase-order-create')
	                    <a class="btn btn-sm btn-outline-primary" href="{{ route('purchase-invoice-create') }}"> <i class="fa fa-plus"></i> Nueva Factura de Compra</a>
	                    @endcan
	                    &nbsp;&nbsp;&nbsp;
	                    &nbsp;&nbsp;&nbsp;
	                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
	                </div>
	            </div>
	            <div class="card-body">
	                <div class="row">
	                    <div class="col-md-12">
	                    	<div class="table-responsive">
			                    <div class="invoice-box">
							        <table cellpadding="0" cellspacing="0">
							            <tr>
							                <td colspan="2"><strong>Número</strong> </td>
							                <td>
							                   {{$poInfo->po_no}}
							                </td>
							            </tr>
							            <tr>
							                <td colspan="2"><strong>Fecha</strong> </td>
							                <td>
							                   {{date('Y-m-d', strtotime($poInfo->po_date))}}
							                </td>
							            </tr>
							            <tr>
							                <td colspan="2"><strong>Proveedor</strong> </td>
							                <td>
							                   {{$poInfo->supplier->name}}
							                </td>
							            </tr>
							            <tr>
							                <td colspan="2"><strong>Concepto</strong> </td>
							                <td>
							                   {{$poInfo->concept->description}}
							                </td>
							            </tr>
							            <tr>
							                <td colspan="2"><strong>Subtotal:</strong> </td>
							                <td>
							                   ${{number_format($poInfo->total_amount, 2, '.', ',')}}
							                </td>
							            </tr>
							            <tr>
							                <td colspan="2"><strong>Iva ({{$poInfo->tax_percentage}}%):</strong> </td>
							                <td>
							                   ${{number_format($poInfo->tax_amount, 2, '.', ',')}}
							                </td>
							            </tr>
							            <tr>
							                <td colspan="2"><strong>Perc. IIBB:</strong> </td>
							                <td>
							                   ${{number_format($poInfo->perc_iibb, 2, '.', ',')}}
							                </td>
							            </tr>
							            <tr>
							                <td colspan="2"><strong>Perc. Ganancia:</strong> </td>
							                <td>
							                   ${{number_format($poInfo->perc_gan, 2, '.', ',')}}
							                </td>
							            </tr>
							            <tr>
							                <td colspan="2"><strong>Perc. IVA:</strong> </td>
							                <td>
							                   ${{number_format($poInfo->perc_iva, 2, '.', ',')}}
							                </td>
							            </tr>

							            <tr>
							                <td colspan="2"><strong>Total:</strong> </td>
							                <td>
							                   <strong>${{number_format($poInfo->gross_amount, 2, '.', ',')}}</strong>
							                </td>
							            </tr>

							            <tr>
							                <td colspan="4"><hr>Observaciones : {{$poInfo->remark}}</td>
							            </tr>
							        </table>
							    </div>
			                </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>

	@endcan
@else
	@can('purchase-invoice-list')
	<div class="row">
	    <div class="col-12">
	        <div class="card">
	            <div class="card-header ">
	                <h3 class="card-title ">Gestión Facturas de compra</h3>
	                <div class="card-options">
	                    @can('purchase-invoice-create')
	                    <a class="btn btn-sm btn-outline-primary" href="{{ route('purchase-invoice-create') }}"> <i class="fa fa-plus"></i> Nueva Factura</a>
	                    @endcan
	                    &nbsp;&nbsp;&nbsp;<a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
	                </div>
	            </div>
	            {{ Form::open(array('route' => 'purchase-order-action', 'class'=> 'form-horizontal', 'autocomplete'=>'off')) }}
	            @csrf
	            <div class="card-body">
	                <div class="table-responsive">
	                    <table id="datatable" class="table table-striped table-bordered">
	                        <thead>
	                            <tr>
	                                <th scope="col"></th>
	                                <th>Comp</th>
                                    <th>Número</th>
	                                <th>Fecha</th>
	                                <th>Proveedor</th>
	                                <th>Monto</th>
	                                <th>Concepto</th>
                                    <th>Pagada</th>
	                                <th scope="col" width="10%">Acción</th>
	                            </tr>
	                        </thead>

	                    </table>
	                </div>

	                </div>
	                {{ Form::close() }}
	            </div>
	        </div>
	</div>
	@endcan
@endif
@endsection

@section('extrajs')
<script type="text/javascript">
$(document).ready( function () {
    var table = $('#datatable').DataTable({
       "processing": true,
       "serverSide": true,
       "ajax":{
           'url' : '{{ route('api.purchase-invoice-datatable') }}',
           'type' : 'POST'
        },
       'headers': {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        //"order": [["1", "asc" ]],
        "columns": [
            { "data": 'DT_RowIndex'},
            { "data": "type" },
            { "data": "po_no" },
            { "data": "po_date" },
            { "data": "supplier" },
            { "data": "invoice_amount" },
            { "data": "concept" },
            { "data": "payment" },
            { "data": "action" }
        ]
   });
});


$('.concept-list-select-2').select2({
    ajax: {
      url: "{{route('api.get-concept-list')}}",
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

$('.supplier-list-select-2').select2({
    ajax: {
      url: "{{route('api.get-supplier-list')}}",
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
$("input").bind("keyup click keydown change", function(e) {
    //alert('dd');
    var tax = $("#tax_percentage").val();
    var totalAmount = $("#total_amount").val();
    var taxAmount = (totalAmount * tax) / 100;
    $('.tax_amount').val(taxAmount.toFixed(2));
    var per_iibb = 0;
    var per_gan = 0;
    var per_iva = 0;
    if ($('#perc_iibb').val()) {per_iibb=$('#perc_iibb').val();}
    if ($('#perc_gan').val()) {per_gan=$('#perc_gan').val();}
    if ($('#perc_iva').val()) {per_iva=$('#perc_iva').val();}
    var grossAmount = 
       parseFloat(totalAmount) + 
       parseFloat(per_iibb) +
       parseFloat(per_gan) +
       parseFloat(per_iva) +
       parseFloat(taxAmount)
       ;
    $('.gross_amount').val(grossAmount.toFixed(2));    
    //calculationAmountPO();
});
$(document).on("click", "#add-modal-id", function () {
   $('#add-section').hide();
   $('.loading').show();
   $.ajax({
     url: "{{route('api.add-supplier-modal')}}",
     type: 'POST',
     data: "id=supplier",
     success:function(info){
       $('#add-section').html(info);
       $('.loading').hide();
       $('#add-section').show();
     }
   });
 });


</script>
@endsection
