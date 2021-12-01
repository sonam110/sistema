<table id="product-table" class="table table-striped table-bordered">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th>MLA ID</th>
            <th>Nombre</th>
            <!-- <th>Marca</th>
            <th>Modelo</th> -->
            <th>Length</th>
            <th>Width</th>
            <th>Height</th>
            <th>Weight&nbsp;(KG)</th>
            <th>NEW&nbsp;MEDIDAS <br> (L<small class="text-danger">x</small>W<small class="text-danger">x</small>H<small class="text-danger">,</small>Weight)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($records as $key => $res)
        <tr>
            <td>{{$key+1}}</td>
            <td width="15%">
                <input type="hidden" class="form-control" name="mla_id[]" value="{{$res->mla_id}}" required="">
                <strong>{{$res->mla_id}}</strong>
            </td>
            <td>{{$res->nombre}}</td>
            <!-- <td>{{@$res->marca->nombre}}</td>
            <td>{{@$res->modelo->nombre}}</td> -->
            <td>
                <input type="number" class="form-control length" name="length[]" value="{{@$res->medida->long}}" required="" onkeyup="weightUpdate()">
            </td>
            <td>
                <input type="number" class="form-control width" name="width[]" value="{{@$res->medida->width}}" required="" onkeyup="weightUpdate()">
            </td>
            <td>
                <input type="number" class="form-control height" name="height[]" value="{{@$res->altura->high}}" required="" onkeyup="weightUpdate()">
            </td>
            <td>
                <input type="hidden" class="form-control weight" name="weight[]" value="{{@$res->weight}}" required="">
                <strong class="weight-show">-</strong>
            </td>
            <td class="text-info" data-container="body" data-toggle="popover" data-popover-color="default" data-placement="top" data-content="The weight value is rounded with the nearest value." data-original-title=""><strong class="new-medida">-</strong></td>
        </tr>
        @empty
        <tr>
            <td colspan="7">
                <div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Records not found or products MLS ID is empty.</div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
<script type="text/javascript">
    $('[data-toggle="popover"]').popover({
        html: true,
        trigger:"hover"
    });
function weightUpdate() {
    var $tblrows = $("#product-table tbody tr");
    var medida = $("#medida").val();
    var weight = $("#weight").val();
    $tblrows.each(function (index) {
        var $tblrow = $(this);
        var length  = $tblrow.find(".length").val();
        var width   = $tblrow.find(".width").val();
        var height  = $tblrow.find(".height").val();
        var weight  = $tblrow.find(".weight").val();

        // calculate
        //Volume = Length * Width * Height
        var weightinMg = (weight * 1000).toFixed('2');
        // var weight = (((length * width * height) / 1000000) * 35).toFixed('2');
        $tblrow.find('.weight-show').html(weight);
        $tblrow.find('.weight').val(weightinMg);

        $tblrow.find('.new-medida').html(length+'<span class="text-danger">x</span>'+width+'<span class="text-danger">x</span>'+height+'<span class="text-danger">,</span>'+Math.round(weightinMg));
    });
}
</script>
