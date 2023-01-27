<table id="product-table" class="table table-striped table-bordered">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th>Nombre</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Stock</th>
            <th>Precio</th>
        </tr>
    </thead>
    <tbody>
        @forelse($records as $key => $res)
        <tr>
            <td>{{$key+1}}</td>
            <td>{{$res->nombre}}</td>
            <td>{{@$res->marca->nombre}}</td>
            <td>{{@$res->modelo->nombre}}</td>
            <td>{{$res->stock}}</td>
            <td>
                <strong class="text-primary">${{$res->precio}}</strong>
                <input type="hidden" value="{{$res->precio}}" class="current-price">
            </td>
           
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
/*function priceUpdate() {
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
}*/
</script>
