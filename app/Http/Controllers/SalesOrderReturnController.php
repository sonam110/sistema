<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Notifications\SaleOrderNotification;
use App\booking;
use App\bookeditem;
use App\SalesOrderReturn;
use App\BookingPaymentThrough;
use App\Producto;
use DB;
use Notification;
use App\User;
use Braghetto\Hokoml\Hokoml;
use App\neofactura\Wsfev1;
use PDF;

class SalesOrderReturnController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:sales-order-return', ['except' => ['salesOrderReturnSave']]);
    }

    public function salesOrderReturnList()
    {
	    return view('sales.sales-order-return');
    }

    public function salesReturnProductDatatable(Request $request)
    {
        $query = SalesOrderReturn::select('*')->orderBy('id','DESC')->with('booking', 'producto')->get();
        return datatables($query)
	        ->editColumn('tranjectionid', function ($query)
		        {
		            return '<strong>'.$query->booking->tranjectionid.'</strong>';
		        })
	       	->editColumn('order_date', function ($query)
		        {
		            return $query->created_at->format('Y-m-d');
		        })
	       	->editColumn('customer', function ($query)
		        {
		            return $query->booking->firstname.' '.$query->booking->lastname;
		        })
	        ->editColumn('product_name', function ($query)
		        {
		            return $query->producto->nombre;
		        })
	        ->editColumn('returned_qty', function ($query)
		        {
		            return '<strong>'.$query->return_qty.'</strong>';
		        })
	        ->editColumn('returned_amount', function ($query)
		        {
		            return '<strong>$'.$query->return_amount.'</strong>';
		        })
	        ->editColumn('returned_date', function ($query)
		        {
		        	return $query->created_at->format('Y-m-d');
		        })
   	        ->addColumn('action', function ($query)
	           {
               $factuelec = auth()->user()->can('sales-order-download') ? '<a class="btn btn-sm btn-default" target="_blank" href="'.route('sales-order-return-nc',base64_encode($query->id)).'" data-toggle="tooltip" data-placement="top" title="Nota de Credito '.$query->cae_fac.'" data-original-title="Nota de Credito"><i class="fa fa-money"></i></a>' : '';
               return '<div class="btn-group btn-group-xs">'.$factuelec.'</div>';
               })
        ->escapeColumns(['action'])
        ->addIndexColumn()
        ->make(true);
    }

    public function salesOrderReturnNC($id)
    {
    //$query = SalesOrderReturn::select('*')->orderBy('id','DESC')->with('booking', 'producto')->get();
    if(SalesOrderReturn::find(base64_decode($id)))
        {
        $SalesOrderReturn = SalesOrderReturn::find(base64_decode($id));
        $booking = booking::find($SalesOrderReturn->booking_id);
        $user = user::find($booking->userId);

        $CUIT = '20187412065';
        //$CUIT = '23250993099';
        $MODO = 1; //afip\Wsaa::MODO_HOMOLOGACION;
        $puntoVenta=13;

        if ($user->doc_type=='CUIT')
          {
          $letra='A';
          $codigoTipoComprobante = '03';
          $codigoTipoComprobante2 = '01';
          $codigoTipoDocumento = '80';
          $TipoDocumento = 'CUIT';
          $nomCliente = $booking->companyname;
          }
          else
          {
          $letra='B';
          $codigoTipoComprobante = '08';
          $codigoTipoComprobante2 = '06';
          $codigoTipoDocumento= '96';
          $TipoDocumento = 'DNI';
          $nomCliente = $booking->firstname.' '.$booking->lastname;
          }

        if (!$SalesOrderReturn->cae_nro)
        {
        //die('gg '.$booking->cae_nro);
        if (!$booking->cae_nro)
         {
         notify()->error('No se puede hacer nota de credito');
         return redirect()->back();
         }

         $afip = new Wsfev1($CUIT,$MODO);
         $numeroComprobante = $afip->consultarUltimoComprobanteAutorizado($puntoVenta,$codigoTipoComprobante);
         $numeroComprobante++;

        $vecPta=explode('-',$booking->cae_fac);
        $voucher = Array(
         "idVoucher" => base64_decode($id),
         "numeroComprobante" => $numeroComprobante, // Debe estar sincronizado con el último número de AFIP
         "numeroPuntoVenta" => $puntoVenta,
         "cae" => 0,
         "letra" => $letra,
         "fechaVencimientoCAE" => "",
         //"tipoResponsable" => "IVA Responsable Inscripto",
         "nombreCliente" =>  $nomCliente,
         "domicilioCliente" => $booking->address1.' '.$booking->address2.' '.$booking->city.' '.$booking->state,
         "fechaComprobante" => date("Ymd"),
         "codigoTipoComprobante" => $codigoTipoComprobante,
         "TipoComprobante" => "Factura",
         "codigoConcepto" => 1,
         "codigoMoneda" => "PES",
         "cotizacionMoneda" => 1.000,
         //"fechaDesde" => "20190303",
         //"fechaHasta" => "20190303",
         "fechaVtoPago" => date("Ymd"),
         "codigoTipoDocumento" => $codigoTipoDocumento,
         "TipoDocumento" => $TipoDocumento,
         "numeroDocumento" => $user->doc_number, // Debe ser diferente al CUIT emisor
         "importeTotal" => $SalesOrderReturn->return_amount,
         "importeOtrosTributos" => 0.000,
         "importeGravado" => round($SalesOrderReturn->return_amount / 1.21,2),
         "importeNoGravado" => 0.000,
         "importeExento" => 0.000,
         "importeIVA" => round($SalesOrderReturn->return_amount - round($SalesOrderReturn->return_amount / 1.21,2),2),
         //"codigoPais" => 200,
         //"idiomaComprobante" => 1,
         "NroRemito" => 0,
         "CondicionVenta" => "Efectivo",
         "subtotivas" => Array
           (
            0 => Array
                (
                    "codigo" => 5,
                    "Alic" => 21,
                    "importe" => round($SalesOrderReturn->return_amount - round($SalesOrderReturn->return_amount / 1.21,2),2),
                    "BaseImp" => round($SalesOrderReturn->return_amount / 1.21,2),
                )
            ),
         "Tributos" => Array(),
         "CbtesAsoc" => Array
                    (
                     0 => Array(
                    "Tipo" => $codigoTipoComprobante2,
                    "PtoVta" => $vecPta[0],
                    "Nro" => $vecPta[1]
                     )
                     )
         );

         try {
         $afip = new Wsfev1($CUIT,$MODO);
         $result = $afip->emitirComprobante($voucher);
         if ($result['cae'])
          {
          // grabar los datos
          $SalesOrderReturn->cae_fac = $puntoVenta.'-'.$numeroComprobante;
          $SalesOrderReturn->cae_nro = $result['cae'];
          $SalesOrderReturn->cae_type = $letra;
          //$booking->final_invoice=date("Y-m-d");
          $SalesOrderReturn->cae_vto =
              substr($result['fechaVencimientoCAE'],6,2).'/'.
              substr($result['fechaVencimientoCAE'],4,2).'/'.
              substr($result['fechaVencimientoCAE'],0,4);
          $SalesOrderReturn->save();
          }
         //return array("cae" => $cae, "fechaVencimientoCAE" => $fecha_vencimiento);
         //print_r($result);
         } catch (Exception $e) {
         notify()->error('Oops!!!, algo salió mal, intente de nuevo.');
         return redirect()->back();
         //echo 'Falló la ejecución: ' . $e->getMessage();
         }
        }

	     // Imprimir la factura
         if ($SalesOrderReturn->cae_nro)
         {
         // generar qr
         $vec=explode('-',$SalesOrderReturn->cae_fac);
         $vecqr=array (
          'ver' => '1',
          'fecha' => date("Ymd"),
          'cuit' => $CUIT,
          'ptoVta' => $vec[0],
          'tipoCmp' => $codigoTipoComprobante,
          'nroCmp' => $vec[1],
          'importe' => round($SalesOrderReturn->return_amount * 100,0),
          'moneda' => 'ARS',
          'ctz' => 1,
          'tipoDocRec' => $codigoTipoDocumento,
          'nroDocRec' => $user->doc_number,
          'tipoCodAut' => 'E',
          'codAut' => $SalesOrderReturn->cae_nro
           );
        $texto = 'https://www.afip.gob.ar/fe/qr/?p='.base64_encode(json_encode($vecqr)); //
        \PHPQRCode\QRcode::png($texto, sys_get_temp_dir().'/'.$SalesOrderReturn->cae_nro.".png", 'L', 3, 2);

         $data = [
	          'booking' => $booking,
              'user' => $user,
              'return' => $SalesOrderReturn,
              'qr' => sys_get_temp_dir().'/'.$SalesOrderReturn->cae_nro.".png"
	        ];
	      $pdf = PDF::loadView('sales.sales-order-nc', $data);
	      return $pdf->stream($booking->tranjectionid.'.pdf');
          }
        }
        notify()->error('Oops!!!, algo salió mal, intente de nuevo.');
        return redirect()->back();

    }

    public function salesOrderReturn($id)
    {
    	if(booking::where('deliveryStatus', 'Delivered')->find(base64_decode($id)))
        {
            $saleInfo = booking::find(base64_decode($id));
	        return view('sales.sales-order-return', compact('saleInfo'));
        }
        notify()->error('Oops!!!, algo salió mal, intente de nuevo.');
        return redirect()->back();
    }

    public function salesOrderReturnSave(Request $request)
    {
        DB::beginTransaction();
        try {
        	$return_token = Str::random(15);
        	$getTax = booking::select('id','tranjectionid','tax_percentage', 'amount', 'tax_amount', 'payableAmount')->find($request->booking_id);
        	foreach ($request->return_qty as $key => $returnQty) {
	    		if(!empty($returnQty))
	  			{
	  				$calTax = (($returnQty * $request->itemPrice[$key]) * $getTax->tax_percentage)/100;
	  				$salesOrderReturn = new SalesOrderReturn;
			        $salesOrderReturn->booking_id 		= $request->booking_id;
			        $salesOrderReturn->bookeditem_id	= $request->bookeditem_id[$key];
			        $salesOrderReturn->producto_id    	= $request->producto_id[$key];
			        $salesOrderReturn->return_token  	= $return_token;
			        $salesOrderReturn->return_qty  		= $returnQty;
			        $salesOrderReturn->return_amount  	= (($returnQty * $request->itemPrice[$key]) + $calTax);
			        $salesOrderReturn->return_note  	= $request->return_note;
			        $salesOrderReturn->save();

			        //Stock In Start
		        	$getStock = Producto::select('id','stock')->find($request->producto_id[$key]);
		        	$getStock->stock = $getStock->stock + $returnQty;
		        	$getStock->save();
		        	//Stock In End

                    /************************************************************/
		        	//update record order item
		        	$updateOrderQty = bookeditem::select('id','return_qty')->find($request->bookeditem_id[$key]);
		        	$updateOrderQty->return_qty = $updateOrderQty->return_qty + $returnQty;
		        	$updateOrderQty->save();

                    // start update booking price
                    $getTax->amount = ($getTax->amount - ($returnQty * $request->itemPrice[$key]));
                    $getTax->tax_amount = ($getTax->tax_amount - $calTax);
                    $getTax->payableAmount = $getTax->payableAmount - $salesOrderReturn->return_amount;
                    $getTax->save();
                    // End update booking price

                    //start Update Booking payment through amount
                    $bookingPaymentThrough = BookingPaymentThrough::where('booking_id', $getTax->id)->where('amount','>=', $salesOrderReturn->return_amount)->first();
                    if($bookingPaymentThrough)
                    {

                        $bookingPaymentThrough->amount = $bookingPaymentThrough->amount - $salesOrderReturn->return_amount;
                        if($bookingPaymentThrough->payment_mode=='Installment')
                        {
                            // Installment amount change if payment through installment
                            $bookingPaymentThrough->installment_amount = round((($bookingPaymentThrough->amount - $salesOrderReturn->return_amount) / $bookingPaymentThrough->no_of_installment), 2);
                            $bookingPaymentThrough->save();
                        }
                  }
                    //end Update Booking payment through amount
                    /************************************************************/

		        	//Start ***Available Quantity update in ML
                    $response = $this->addStockMl($request->producto_id[$key], $returnQty);
                    //End ***Available Quantity update in ML
			    }
			}
			$changeStatus = true;
			$checkSaleStatus = bookeditem::where('bookingId', $request->booking_id)->get();
			foreach ($checkSaleStatus as $key => $checkbothQty) {
				if($checkbothQty->itemqty!= (int) $checkbothQty->return_qty)
				{
					$changeStatus = false;
					break;
				}
			}

			if($changeStatus)
			{
				$updateStatus = booking::select('id', 'deliveryStatus')->find($request->booking_id);
				$updateStatus->deliveryStatus = 'Return';
				$updateStatus->save();
			}
			$totalReverseAmount = SalesOrderReturn::where('return_token', $return_token)->sum('return_amount');
			//Send Notification
            $details = [
                'body'      => 'Orden Numero #'.$getTax->tranjectionid. ' producto devuelto por '.auth()->user()->name.'. Monto Devuelto $'.$totalReverseAmount.'. Nota de Devolución #: '.$request->return_note,
                'actionText'=> 'Ver Pedido',
                'actionURL' => route('sales-return-by-token', [base64_encode($request->booking_id),$return_token]),
                'order_id'  => $request->booking_id
            ];
            Notification::send(User::first(), new SaleOrderNotification($details));
	        DB::commit();
	        notify()->success('Hecha , Cantidad devuelta exitosamente en la Orden de venta.');
            return redirect()->route('sales-order-list');
        } catch (\Exception $exception) {
            DB::rollback();
            dd($exception);
            notify()->error('Error, Oops!!!, algo salió mal, intente de nuevo.'. $exception->getMessage());
            return redirect()->back()->withInput();
        } catch (\Throwable $exception) {
            DB::rollback();
            dd($exception->getMessage());
            notify()->error('Error, Oops!!!, algo salió mal, intente de nuevo.'. $exception->getMessage());
            return redirect()->back()->withInput();
        }
    }

    private function addStockMl($productoId, $purchaseQty)
    {
        $is_stock_updated_in_ml = '0';
        $records = Producto::select('id','nombre','stock','precio','mla_id')
                ->where('id', $productoId)
                ->where('disponible', '1')
                ->where('mla_id', '!=', null)
                ->orderBy('mla_id')
                ->first();
        if($records && !empty($records->mla_id))
        {
            $mlas = new Hokoml(\Config::get('mercadolibre'), env('ML_ACCESS_TOKEN',''), env('ML_USER_ID',''));
            $response = $mlas->product()->find($records->mla_id);
            if($response['http_code']==200)
            {
                //if product found
                $variationsArr  = array();
                $variations     = $response['body']['variations'];
                foreach ($variations as $key => $variation) {
                    $variationsArr[] = [
                        'id'    => $variation['id'],
                        'available_quantity' => $variation['available_quantity'] + $purchaseQty
                    ];
                }

                if(is_array($variationsArr) && sizeof($variationsArr)>0)
                {
                    //if variation found then update variation available quantity
                    $response = $mlas->product()->update($records->mla_id, [
                        'variations' => $variationsArr
                    ]);
                }
                else
                {
                    //if variation not found then update main available quantity
                    $mainList     = $response['body'];
                    $response = $mlas->product()->update($records->mla_id, [
                        'available_quantity'  => $mainList['available_quantity'] + $purchaseQty
                    ]);
                }
                if($response['http_code']==200)
                {
                    $is_stock_updated_in_ml = '1';
                }
            }
        }
        return $is_stock_updated_in_ml;
    }
}
