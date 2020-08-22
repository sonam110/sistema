@extends('layouts.master')
@section('content')
<div class="row">
	<div class="col-12">
		<div class="table-responsive">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title "><b>Sales Report</b></h3>
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
	                                <th>Placed By</th>
	                                <th>Number</th>
	                                <th>Customer Name</th>
	                                <th>Order Date</th>
	                                <th>Amount</th>
	                                <th>Payment Mode</th>
	                                <th>Delivery Status</th>
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
           'url' : '{{ route('sales-report-list') }}',
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
        { "data": 'placed_by'},
        { "data": "tranjectionid"},
        { "data": "customer_name"},
        { "data": "order_date"},
        { "data": "payableAmount"},
        { "data": "paymentThrough"},
        { "data": "deliveryStatus"},
        { "data": "action"}
        ]
  });
});

$(document).on('click','#download',function(){
    var dateRange= $('#dateRange').val();
	   $.ajax({
	    url: '{{ route('download-sales-report') }}',
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