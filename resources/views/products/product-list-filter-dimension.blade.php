<table id="product-table" class="table table-striped table-bordered">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th>MLA ID</th>
            <th>Nombre</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Weight</th>
            <th>Medidas</th>
            <th>New&nbsp;Medidas</th>
        </tr>
    </thead>
    <tbody>
        @forelse($records as $key => $res)
        <tr>
            <td>{{$key+1}}</td>
            <td width="15%"><strong>{{$res->mla_id}}</strong></td>
            <td>{{$res->nombre}}</td>
            <td>{{@$res->marca->nombre}}</td>
            <td>{{@$res->modelo->nombre}}</td>
            <td>{{@$res->weight}}</td>
            <td>{{@$res->medida->nombre}}</td>
            <td class="text-info"><strong class="new-medida">-</strong></td>
        </tr>
        @empty
        <tr>
            <td colspan="8">
                <div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Records not found or products MLS ID is empty.</div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
<script type="text/javascript">
function priceUpdate() {
    var $tblrows = $("#product-table tbody tr");
    var medida = $("#medida").val();
    var weight = $("#weight").val();
    $tblrows.each(function (index) {
        var $tblrow = $(this);        
        $tblrow.find('.new-medida').html(medida +','+weight);
    });
}
</script>