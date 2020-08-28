<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pedido Dormicentro Soñemos</title>

    <style>
    .invoice-box {
        max-width: 800px;
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
    .rtl {
        direction: rtl;
        font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
    }

    .rtl table {
        text-align: right;
    }

    .rtl table tr td:nth-child(2) {
        text-align: left;
    }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="4">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="{{ env('CDN_URL').$appSetting->website_logo}}" class="" height="80px" width="200px">
                            </td>

                            <td>
                                <strong>Dormicentro Soñemos</strong><br>
                                Av. Reg. de Patricios 554<br>
                                C.A.B.A , CP 1265.<br>
                                (54) 11 4302-3939 /(54) 11  4307-4456
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="4">
                    <table>
                        <tr class="heading">
                            <td colspan="2">
                                <center><span class="uppercase">Orden de Compra</span></center>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Información del Proveedor</strong><br>
                                {{$poInfo->supplier->name}} <br>
                                {{$poInfo->supplier->company_name}}<br>
                                {{$poInfo->supplier->address}},<br>
                                {{$poInfo->supplier->city}}, {{$poInfo->supplier->state}}<br>
                                {{$poInfo->supplier->phone}}<br>
                                <strong>Cuit No.: {{$poInfo->supplier->vat_number}}</strong>
                            </td>
                            <td>
                                OC No. #: {{$poInfo->po_no}}<br>
                                OC Fecha: {{date('Y-m-d', strtotime($poInfo->po_date))}}
                                @if(Auth::user()->hasRole('admin'))
                                <br>
                                Estado: <strong>{{$poInfo->po_status}}</strong>

                                @if($poInfo->po_status=='Completed')
                                    <br>
                                    <strong>OC Realizada: {{$poInfo->po_completed_date}}</strong>
                                @endif
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td>
                    Producto
                </td>

                <td>
                    <center>Cantidad</center>
                </td>

                <td>
                    <center>Precio</center>
                </td>

                <td>
                    <center>Total</center>
                </td>
            </tr>
            @foreach($poInfo->purchaseOrderProducts as $productDetail)
            <tr class="item">
                <td>
                    {{$productDetail->producto->nombre}}
                </td>
                <td>
                    <center>{{$productDetail->required_qty}}</center>
                </td>
                <td>
                    <center>${{number_format($productDetail->price, 2, '.', ',')}}</center>
                </td>

                <td>
                    <center>${{number_format($productDetail->required_qty * $productDetail->price, 2, '.', ',')}}</center>
                </td>
            </tr>
            @endforeach
            <tr class="total">
                <td></td>
                <td colspan="2"><strong>Total:</strong> </td>
                <td>
                   <center>${{number_format($poInfo->total_amount, 2, '.', ',')}}</center>
                </td>
            </tr>
            <tr class="total">
                <td></td>
                <td colspan="2"><strong>Iva ({{$poInfo->tax_percentage}}%):</strong> </td>
                <td>
                   <center>${{number_format($poInfo->tax_amount, 2, '.', ',')}}</center>
                </td>
            </tr>
            <tr class="total">
                <td></td>
                <td colspan="2"><strong>SubTotal:</strong> </td>
                <td>
                   <strong><center>${{number_format($poInfo->gross_amount, 2, '.', ',')}}</center></strong>
                </td>
            </tr>

            <tr>
                <td colspan="4"><hr>Observaciones : {{$poInfo->remark}}</td>
            </tr>
        </table>
    </div>
</body>
</html>
