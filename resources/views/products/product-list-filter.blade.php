<table id="product-table" class="table table-striped table-bordered">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th>MLA ID</th>
            <th>Nombre</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Stock</th>
            <th>Precio</th>
            <th>New&nbsp;Price</th>
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
            <td>{{$res->stock}}</td>
            <td>
                <strong class="text-primary">${{$res->precio}}</strong>
                <input type="hidden" value="{{$res->precio}}" class="current-price">
            </td>
            <td class="text-info">$<strong class="changed-price">0.00</strong>
              <!-- {!! Form::number('change_price[]',null,array('id'=>'change_price','class'=> 'form-control change_price', 'placeholder'=>'nuevo precio', 'autocomplete'=>'off','min'=>'1','max'=> '1000')) !!} -->
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
          var newPrice = (parseFloat(price) + (16 * parseFloat(price))/100)+parseFloat(percentage_amount);
            // var newPrice = parseFloat(percentage_amount) + parseFloat(price);  number_format($money, 0,',','.')
        }
        else if(calculation_type=='Percentage')
        {
            var newPrice = (parseFloat(price) + (parseFloat(percentage_amount) * parseFloat(price))/100);
        }
        else
        {
            var newPrice = (parseFloat(price)+ parseFloat(fixed_amount) + (parseFloat(percentage_amount) * parseFloat(price))/100);
        }
        if (!isNaN(newPrice)) {
            $tblrow.find('.changed-price').html(newPrice.toFixed(0));
        }
    });
}
</script>
