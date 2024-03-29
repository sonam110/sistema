<html>
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>Dormicentro, Colchones y Sommiers de las mejores Marcas</title>
<style>
@import url('https://fonts.googleapis.com/css?family=Roboto+Condensed&display=swap');
</style>
<style type="text/css">
  @font-face {
      font-family: 'Roboto Condensed', sans-serif;
      src: url(https://fonts.googleapis.com/css?family=Roboto+Condensed&display=swap);
  }
  body{
    font-family: 'Roboto Condensed', sans-serif;
    color: #fff;
  }
  .table {
    color: #fff;
  }
  .table table {
    border-collapse: collapse;
    width: 100%;
    color: #fff;
  }

  .table th, .table td {
    text-align: left;
    padding: 8px;
    border-top: 1px solid #b326b3;
    color: #fff;
  }

  .table tr:nth-child(even) {
    background-color: #492749;
  }
  .ii a[href], a {
      color: #fff!important;
  }
</style>
</head>
<body>
  <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#fff">
  <tr>
    <td>
        <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#b326b3" style="padding-bottom: 30px;padding: 15px; font-family: 'Roboto Condensed', sans-serif;">
          <tr>
            <td>
                <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#262626" style="padding: 15px 15px 0px 15px; font-family: 'Roboto Condensed', sans-serif;">
                  <tr>
                    <td>
                      <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#993799" style="padding: 15px; font-family: 'Roboto Condensed', sans-serif;">
                        <tr>
                          <td>
                            <div>
                              <center style="color: #fff; font-size: 18px;text-decoration: underline; font-family: 'Roboto Condensed', sans-serif; text-transform: uppercase;">Orden de venta desde {{env('APP_NAME')}}</center>
                            </div>
                          </td>
                        </tr>
                      </table>

                      <table cellspacing="0" border="0" cellpadding="0" width="100%" style="padding: 15px 15px 0px 15px; font-family: 'Roboto Condensed', sans-serif;">
                        <tr>
                          <td width="60%">
                            <div style="border: 3px solid #fff; width: 225px; padding: 10px; background: #b326b3; float: right; font-family: 'Roboto Condensed', sans-serif;">
                              <center style="color: #fff; font-size: 16px; font-family: 'Roboto Condensed', sans-serif;"><?php echo date('d/m/Y, H:i A') ?></center>
                            </div>
                          </td>
                          <td>
                            <div style="float: right;">
                              <img src="{{ env('CDN_URL') }}/imagenes/{!! $appSetting->website_logo !!}" width="115">
                            </div>
                          </td>
                        </tr>

                        <tr>
                          <td colspan="2" style="color: #fff; font-family: 'Roboto Condensed', sans-serif;">
                             {{$saleOrder['firstname']}} {{$saleOrder['lastname']}},
                            <br>
                            ¡Gracias por visitarnos y realizar su compra!
                            Nos alegra que haya encontrado lo que buscaba.
                            Nuestro objetivo es que siempre estés satisfecho con lo que nos compraste.
                            Esperamos verte nuevamente. ¡Que tengas un gran día!  <br><br>
                          </td>
                        </tr>
                      </table>

                    </td>
                  </tr>

                  <tr>
                    <td>
                      <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#262626" style="padding: 5px; color: #030303; background: #fff; text-align: center;border: 2px solid #f79646; font-family: 'Roboto Condensed', sans-serif;">
                        <tr>
                          <td style="font-family: 'Roboto Condensed', sans-serif;">
                            <center>
                              <p style="font-size: 16px; line-height: 22px; font-family: Georgia, Times New Roman, Times, serif; color: #030303; margin: 0px;">
                                Dormicentro Soñemos
                                <br>
                                Av. Reg. de Patricios 554
                                <br>
                                C.A.B.A , CP 1265.
                                <br>
                                Mensajes WhatsApp: 11 5467-8526 <br> tel. de linea: 11 4307-4456
                                <br>
                                email: ventas@dormicentro.com
                                <br>
                                Estamos de Lunes a Viernes de 10 a 14 y de 15 a 19 hs. <br> Sábados de 10 a 17 hs
                                <br>
                              </p>
                              </center>
                          </td>
                        </tr>
                      </table>
                      <br>
                    </td>
                  </tr>
                  <br>
                </table>
              </td>
          </tr>
        </table>
    </td>
  </tr>
</table>

</body>
</html>
