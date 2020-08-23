@extends('layouts.master')
@section('content')
@if(Auth::user()->hasAnyPermission(['purchase-report']) || Auth::user()->hasRole('admin'))
<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-body">
				<div class="form-group">
					<div class="row gutters-xs">
						<div class="col">
							<select class="form-control dateRange" name="dateRange" id="dateRange">
					           <option value="" selected="" disabled="">-- Purchase Report --</option>
					           <option value="">All Days</option>
					           <option value="day">Today</option>
					           <option value="week">Last 7 Days</option>
					           <option value="month">Last 30 Days</option>
					        </select>
						</div>
						@can('export-purchase-report')
						<span class="col-auto">
							<a href="javascript:;" class="btn btn-primary" type="button" id="download" data-toggle="tooltip" data-placement="right" title="" data-original-title="Export Report"><i class="fe fe-download"></i></a>
						</span>
						@endcan
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="table-responsive">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title "><b>Purchase Report</b></h3>
					<div class="card-options">
                      &nbsp;&nbsp;&nbsp;<a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="right" title="" data-original-title="Go To Back"><i class="fa fa-mail-reply"></i></a>
					</div>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table id="datatable" class="table table-striped table-bordered">
							 <thead>
	                            <tr>
	                                <th scope="col">#</th>
	                                <th>Po Number</th>
	                                <th>Po Date</th>
	                                <th>Supplier</th>
	                                <th>Invoice Amount</th>
	                                <th>Status</th>
	                                <th scope="col" width="10%">Action</th>
	                            </tr>
	                        </thead>
							<tbody>
								
							</tbody>
						</table>
					</div>

				</div>
				
			</div>
		</div>
	</div>
</div>
@endif
@endsection
@section('extrajs')
<script>
var oTable ;
	$('#dateRange').on('change keyup', function(e) {
		oTable.draw();
	});
   $(document).ready( function () {
   oTable = $('#datatable').DataTable({
       "processing": true,
       "serverSide": true,
       "ajax":{
           'url' : '{{ route('purchase-report-list') }}',
           'type' : 'POST',
           'headers': {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        	},
    		data: function (d) {
				d.dateRange = $('.dateRange').val();
			}
    },
    /*"order": [["0", "desc" ]],*/
      "columns": [
            { "data": 'DT_RowIndex'},
            { "data": "po_no" },
            { "data": "po_date" },
            { "data": "supplier" },
            { "data": "invoice_amount" },
            { "data": "po_status" },
            { "data": "action" }
        ]
  });
});

$(document).on('click','#download',function(){
	var dateRange= $('#dateRange').val();
   	$.ajax({
	    url: '{{ route('download-purchase-report') }}',
	    type: 'POST',
	    data:{dateRange:dateRange},   
	    success:function(response) {
		    var obj = JSON.parse(response);
		    var url = obj['url'];
		    var a = document.createElement('a');
		    a.href = url;
		    a.download = obj['fileName'];
		    document.body.appendChild(a);
		    a.click();
		    window.URL.revokeObjectURL(url);
	    	//alert('your file has downloaded!');
    	}
	});
});
</script>
@endsection