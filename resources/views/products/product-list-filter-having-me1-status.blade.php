<table id="product-table" class="table table-striped table-bordered">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th>MLA ID</th>
            <th>Nombre</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Item</th>
            <th>Stock</th>
            <th>Shipping&nbsp;Mode</th>
        </tr>
    </thead>
    <tbody>
        @forelse($records as $key => $res)
        <tr>
            <td>{{$key+1}}</td>
            <td width="15%"><input type="hidden" class="form-control" name="mla_id[]" value="{{$res->mla_id}}" required=""><strong>{{$res->mla_id}}</strong></td>
            <td>{{$res->nombre}}</td>
            <td>{{@$res->marca->nombre}}</td>
            <td>{{@$res->modelo->nombre}}</td>
            <td>{{@$res->item->nombre}}</td>
            <td>{{$res->stock}}</td>
            <td class="text-info"><strong>{{$res->shipping_mode}}</strong></td>
        </tr>
        @empty
        <tr>
            <td colspan="8">
                <div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Records not found or products MLS ID is empty or Shipping mode is not <strong>ME1</strong>.</div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>