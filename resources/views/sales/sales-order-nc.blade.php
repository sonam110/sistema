@php
$dif = 1;
$sub = 0
@endphp
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>NC Dormicentro Soñemos</title>

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
                                <img src="{{ env('CDN_URL') }}/imagenes/{!! $appSetting->website_logo !!}" class="" height="80px" width="200px">
                            </td>

                            <td>
                            	<strong>Dormicentro Soñemos</strong><br>
								CUIT 20-18741206-5<br>
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
			                    <center><span class="uppercase">Nota de Credito {{$return->cae_type}} Nro {{$return->cae_fac}}</span></center>
			                </td>
			            </tr>
                    	<tr>
                    		<td>Creada: {{date('Y-m-d', strtotime($return->created_at))}}</td>
                    	</tr>
                        <tr>
                            <td>
                            	<strong>Facturar a</strong><br>
                                {{$booking->firstname}} {{$booking->lastname}}<br>
                                {{$user->doc_type}} {{$user->doc_number}} <br>
                                {{$booking->email}}<br>
                                {{$booking->companyname}}<br>
                                {{$booking->address1}} {{$booking->address2}},<br> {{$booking->city}}, {{$booking->state}}, {{$booking->postcode}}<br>
                                {{$booking->phone}}
                            </td>

                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td>
                    Descripción de los artículos
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
            @if($return->cae_type=='A')
              @php ($dif = 1.21)
            @endif
            @php ($sub = $return->return_amount/$dif);
            <tr class="item">
                <td>
                    {{$return->nombre}}
                </td>
                <td>
                    <center>{{$return->return_qty}}</center>
                </td>
                <td>
                    <center>${{number_format($sub / ($return->return_qty), 2, '.', ',')}}</center>
                </td>

                <td>
                    <center>${{number_format(($return->return_amount/$dif), 2, '.', ',')}}</center>
                </td>
            </tr>

            <tr class="total">
                <td align="center" rowspan="5">
                 <table>
                   <tr>
                     <td><img src="{{$qr}}" width="125"></td>
                     <td style="text-align:left;border-top:0px;font-size:11px">
                       Comprobante autorizado AFIP<br><br>
                       CAE<br> {{$return->cae_nro}}<br><br>
                       Vto<br> {{$return->cae_vto}}
                     </td>
                   </tr>
                 </table>

                 <br>

                </td>
                <td colspan="2"><strong>SubTotal:</strong> </td>
                <td>
                   <center>${{number_format($sub, 2, '.', ',')}}</center>
                </td>
            </tr>

            @if($return->cae_type=='A')
            <tr class="total">
                <td colspan="2"><strong>Total I.V.A : (21%)</strong> </td>
                <td>
                   <center>${{number_format(($return->return_amount) -$sub, 2, '.', ',')}}</center>
                </td>
            </tr>
            @else
            @endif
            <tr class="total">
                <td colspan="2"><strong>Monto Total:</strong> </td>
                <td>
                   <strong><center>${{number_format(($return->return_amount) , 2, '.', ',')}}</center></strong>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
