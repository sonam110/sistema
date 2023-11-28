@extends('layouts.master')
@section('content')
@can('employee-list')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title ">Order Management</h3>
                <div class="card-options">
                    &nbsp;&nbsp;&nbsp;<a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary"  data-toggle="tooltip" data-placement="right" title="" data-original-title="Volver"><i class="fa fa-mail-reply"></i></a>
                </div>
            </div>
            {{ Form::open(array('route' => 'actionOrders', 'class'=> 'form-horizontal', 'autocomplete'=>'off')) }}
            @csrf
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th></th>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Pago Id</th>
                            <th>Medio de pago</th>
                            <th>Estado del Pago</th>
                            <th>Total</th>
                            <th>Fecha</th>
                            <th width="10%">View</th>
                        </tr>

                        <tbody>
                        </tbody>
                    </table>
                </div>

                @can('employee-action')
                <div class="row div-margin">
                    <div class="col-md-3 col-sm-6 col-xs-6">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-hand-o-right"></i> </span>
                               {{ Form::select('cmbaction', array(
                              ''            => 'Change Order Status',
                              'Delivered'   => 'Delivered',
                              'Process'     => 'Process',
                              'Cancel'      => 'Cancel'),
                              '', array('class'=>'form-control','id'=>'cmbaction'))}} 
                            </div>
                        </div>
                        <div class="col-md-8 col-sm-6 col-xs-6">
                            <div class="input-group">
                                <button type="submit" class="btn btn-danger pull-right" name="Action" onClick="return delrec(document.getElementById('cmbaction').value);">Apply</button>
                            </div>
                        </div>
                    </div>
                    @endcan
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    @endcan
    <div class="modal fade" id="orderData" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static"
 data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div id="orderDetails"></div>
        </div>
    </div>
</div>
    @endsection
@section('extrajs')

<script type="text/javascript">
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-Token': $('meta[name="_token"]').attr('content')
        }
      });
    });
    $(document).ready( function () {
        var table = $('#datatable').DataTable({
           "processing": true,
           "serverSide": true,
           "ajax":{
               'url' : '{{ route('api.order-list-datatable') }}',
               'type' : 'POST'
            },
           'headers': {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            "order": [["7", "DESC" ]],
            "columns": [
                { "data": 'checkbox'},
                { "data": 'DT_RowIndex', "name": 'DT_RowIndex' , orderable: false, searchable: false },
                { "data": 'firstname'},
                { "data": "tranjectionid"},
                { "data": "paymentThrough"},
                { "data": "orderstatus"},
                { "data": "payableAmount"},
                { "data": "created_at"},
                { "data": "action"}
            ]
       });
    });

    $(document).on("click", "#getOrderDetail", function () {
    var id = $(this).data('id');
        $.ajax({
            url: appurl+"getOrderDetail",
            type: 'POST',
            data: "id="+id,
            success:function(info){
                $('#orderDetails').html(info);
            }
        });
    });
</script>

@endsection
