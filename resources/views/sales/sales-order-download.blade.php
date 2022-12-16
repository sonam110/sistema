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
                                <img src="{{ env('CDN_URL') }}/imagenes/{!! $appSetting->website_logo !!}" class="" height="80px" width="200px">
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
			                    <center><span class="uppercase">Nota de Pedido</span></center>
			                </td>
			            </tr>
			            <tr>
			                <td colspan="2">&nbsp;</td>
			            </tr>
                    	<tr>
                    		<td>Nota de Pedido no. #: {{$booking->tranjectionid}}</td>
                    		<td>Creada: {{date('Y-m-d', strtotime($booking->created_at))}}</td>
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

                            <td>
                                <strong>Dirección de entrega</strong><br>
                                {{$booking->shipping_firstname}} {{$booking->shipping_lastname}}<br>
                                 <br>
                                {{$booking->shipping_email}}<br>
                                {{$booking->shipping_companyname}}<br>
                                {{$booking->shipping_address1}} {{$booking->shipping_address2}},<br> {{$booking->shipping_city}}, {{$booking->shipping_state}}, {{$booking->shipping_postcode}}<br>
                                {{$booking->shipping_phone}}
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
            @foreach($booking->productDetails as $productDetail)
            <tr class="item">
                <td>
                    {{$productDetail->nombre}}
                </td>
                <td>
                    <center>{{$productDetail->itemqty - $productDetail->return_qty}}</center>
                </td>
                <td>
                    <center>${{number_format($productDetail->itemPrice, 2, '.', ',')}}</center>
                </td>

                <td>
                    <center>${{number_format($productDetail->itemPrice * ($productDetail->itemqty - $productDetail->return_qty), 2, '.', ',')}}</center>
                </td>
            </tr>
            @endforeach

            @foreach($booking->getBookeditemGeneric as $genProductDetail)
            <tr class="item">
                <td>
                    {{$genProductDetail->item_name}}
                </td>
                <td>
                    <center>{{$genProductDetail->itemqty}}</center>
                </td>
                <td>
                    <center>${{number_format($genProductDetail->itemPrice, 2, '.', ',')}}</center>
                </td>

                <td>
                    <center>${{number_format($genProductDetail->itemPrice * $genProductDetail->itemqty, 2, '.', ',')}}</center>
                </td>
            </tr>
            @endforeach

            <tr class="total">
                <td></td>
                <td colspan="2"><strong>SubTotal:</strong> </td>
                <td>
                   <center>${{number_format($booking->amount, 2, '.', ',')}}</center>
                </td>
            </tr>
            <tr class="total">
                <td></td>
                <td colspan="2"><strong>Costo de envío:</strong> </td>
                <td>
                   <center>${{number_format($booking->shipping_charge, 2, '.', ',')}}</center>
                </td>
            </tr>
            @if($booking->is_coupon_apply=='1')
            <tr class="total">
                <td></td>
                <td colspan="2"><strong>Cupón de descuento:</strong> </td>
                <td>
                   <center>-${{number_format($booking->coupon_discount, 2, '.', ',')}}</center>
                </td>
            </tr>
            @endif
            <tr class="total">
                <td></td>
                <td colspan="2"><strong>Total impuestos: ({{$booking->tax_percentage}}%)</strong> </td>
                <td>
                   <center>${{number_format($booking->tax_amount, 2, '.', ',')}}</center>
                </td>
            </tr>

            <tr class="total">
            @if(Auth::user()->userType==0)
                @if($booking->due_condition=='12')
	                <td colspan="3"><strong>plazo:  plan visa  : 7/ Cuotas elegidas: </strong></td>
	                <td><strong>{{$booking->installments}}</strong></td>
                @endif
			@else
				<td></td>
                <td colspan="2"><strong>plazo:</strong></td>
                <td><strong><center>{{$booking->installments}}</center></strong></td>
			@endif
			</tr>

            <tr class="total">
                <td></td>
                <td colspan="2"><strong>Monto Total:</strong> </td>
                <td>
                   <strong><center>${{number_format($booking->payableAmount, 2, '.', ',')}}</center></strong>
                </td>
            </tr>

        </table>
        <div class="row">
          <span class="uppercase">Observaciones :: @if($booking->deliveryStatus=='Cancel')  <span style="color: #ff0000;">Orden cancelada</span> @endif</span>
          <div class="form-group">
             {{$booking->orderNote}}
          </div>

        </div>
    </div>
</body>
</html>
