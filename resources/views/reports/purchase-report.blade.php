@extends('layouts.master')
@section('content')
<div class="row">
	<div class="col-12">
		<div class="table-responsive">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title "><b>Purchase Report</b></h3>
					<div class="card-options">
                     
					</div>
				</div>
				<div class="card-body">
                  <div class="row">
                     <div class="col-md-3">
                        
                        <select class="form-control dateRange" name="dateRange" id="dateRange">
                           <option value="" selected="">-- Filter By --</option>
                           <option value="day">Day</option>
                           <option value="week">Week</option>
                           <option value="month">Month</option>
                        </select>
                     </div>
                       <div class="col-md-3">
                       	<a  class="btn btn-sm btn-outline-success" href="javacrip:;"  id="download">Download Report</a>
                       </div>
                     
                  </div>
               </div>
				<div class="card-body">
					<div class="table-responsive">
						<table id="datatable" class="table table-striped table-bordered w-100">
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
    "order": [["1", "desc" ]],
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
	    success:function(response){
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