<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Mail\SaleOrder as SaleOrderMail;
use App\Mail\SaleOrderFactura as SaleOrderFacturaMail;
use App\Notifications\SaleOrderNotification;
use Notification;
use App\booking;
use App\bookeditem;
use App\BookeditemGeneric;
use App\Producto;
use App\BookingPaymentThrough;
use App\CouponCodeCustomer;
use App\CouponCode;
use App\CouponDiscount;
use App\User;
use App\InterestRate;
use App\Websitesetting;
use DB;
use PDF;
use Mail;
use Braghetto\Hokoml\Hokoml;
use App\neofactura\Wsfev1;


class SalesOrderController extends Controller
{
    function __construct()
    {
        $this->middleware(['permission:sales-order-list']);
    }

    public function salesOrders()
    {
	    return view('sales.sales-order-list');
    }

    public function salesOrderDatatable(Request $request)
    {
    	if(auth()->user()->hasRole('admin'))
    	{
    		$query = booking::with('createdBy')->select('bookings.id','bookings.created_by','bookings.firstname','bookings.lastname','bookings.tranjectionid','bookings.payableAmount','bookings.paymentThrough','bookings.orderstatus','bookings.deliveryStatus','bookings.created_at', 'bookings.shipping_guide','bookings.final_invoice','bookings.cae_fac','bookings.cae_type')->where('bookings.orderstatus','!=','pending');
    	}
    	else
    	{
    		$query = booking::with('createdBy')->select('bookings.id','bookings.created_by','bookings.firstname','bookings.lastname','bookings.tranjectionid','bookings.payableAmount','bookings.paymentThrough','bookings.orderstatus','bookings.deliveryStatus','bookings.created_at', 'bookings.shipping_guide','bookings.final_invoice','bookings.cae_fac','bookings.cae_type')->where('bookings.created_by', auth()->id());
    	}
        return datatables($query)
            ->addColumn('checkbox', function ($query)
            {
                $checkbox = null;
                if($query->deliveryStatus != 'Cancel')
                {
                    $checkbox = '<label class="custom-control custom-checkbox">
                       <input type="checkbox"  name="boxchecked[]" value="' . $query->id . '"  class ="colorinput-input custom-control-input allChecked" id="boxchecked">
                         <span class="custom-control-label"></span>
                        </label>';
                }
                return $checkbox;
            })
        	->addColumn('placed_by', function ($query)
	        {
	        	if($query->createdBy)
	        	{
	        		return '<strong>'.$query->createdBy->name .' '.$query->createdBy->lastname.'</strong>';
	        	}
	            return '-';
	        })
	        ->editColumn('tranjectionid', function ($query)
	        {
	            return '<strong>'.$query->tranjectionid.'</strong>';
	        })
	        ->addColumn('customer_name', function ($query)
	        {
	            return '<strong>'.$query->firstname .' '.$query->lastname.'</strong>';
	        })
	        ->editColumn('created_at', function ($query)
	        {
	            return $query->created_at->format('Y-m-d');
	        })
	        ->editColumn('payableAmount', function ($query)
	        {
	            return '<strong>$ '.number_format($query->payableAmount,2,',','.').'</strong>';
	        })
	        ->editColumn('deliveryStatus', function ($query)
	        {
	            if ($query->deliveryStatus == 'Process')
	            {
	                $status = '<span class="badge badge-info">'.$query->deliveryStatus.'</span>';
	            }
	            elseif ($query->deliveryStatus == 'Cancel')
	            {
	                $status = '<span class="badge badge-danger">'.$query->deliveryStatus.'</span>';
	            }
	            elseif ($query->deliveryStatus == 'Delivered')
	            {
	                $status = '<span class="badge badge-success">'.$query->deliveryStatus.'</span>';
	            }
	            elseif ($query->deliveryStatus == 'Return')
	            {
	                $status = '<span class="badge badge-warning">'.$query->deliveryStatus.'</span>';
	            }
                elseif ($query->deliveryStatus == 'Cancel')
                {
                    $status = '<span class="badge badge-danger">'.$query->deliveryStatus.'</span>';
                }
	            else
	            {
	                $status = '<span class="badge badge-default">'.$query->deliveryStatus.'</span>';
	            }
	            return $status;
	        })
            ->editColumn('shipping_guide', function ($query)
            {
                if ($query->shipping_guide != null)
                {
                    $shipping_guide = '<span class="text-center text-success font-size-22" data-toggle="tooltip" data-placement="top" title="'.$query->shipping_guide.'" data-original-title="'.$query->shipping_guide.'"><i class="fe fe-check-circle"></i></span>';
                }
                else
                {
                    $shipping_guide = '<span class="text-center text-danger font-size-22" data-toggle="tooltip" data-placement="top" title="Not Done" data-original-title="Not Done"><i class="fe fe-circle"></i></span>';
                }
                return $shipping_guide;
            })
            ->editColumn('final_invoice', function ($query)
            {
                if ($query->final_invoice != null)
                {
                    $final_invoice = '<span class="text-center text-success font-size-22" data-toggle="tooltip" data-placement="top" title="'.$query->final_invoice.' / '.$query->cae_type.' '.$query->cae_fac.'" data-original-title="'.$query->final_invoice.'"><i class="fe fe-check-circle"></i></span>';
                }
                else
                {
                    $final_invoice = '<span class="text-center text-danger font-size-22" data-toggle="tooltip" data-placement="top" title="Not Done" data-original-title="Not Done"><i class="fe fe-circle"></i></span>';
                }
                return $final_invoice;
            })
	        ->addColumn('action', function ($query)
	        {
	        	$factuelec = auth()->user()->can('sales-order-download') ? '<a class="btn btn-sm btn-default" target="_blank" href="'.route('sales-order-facturar',base64_encode($query->id)).'" data-toggle="tooltip" data-placement="top" title="Facturar" data-original-title="Facturar"><i class="fa fa-money"></i></a>' : '';
                $download = auth()->user()->can('sales-order-download') ? '<a class="btn btn-sm btn-default" target="_blank" href="'.route('sales-order-download',base64_encode($query->id)).'" data-toggle="tooltip" data-placement="top" title="Download / Print" data-original-title="Descargar / Imprimir"><i class="fa fa-download"></i></a>' : '';
	        	$return = '';
                if($query->deliveryStatus=='Delivered')
                {
                    $return = auth()->user()->can('sales-order-return') ? '<a class="btn btn-sm btn-warning" href="'.route('sales-order-return',base64_encode($query->id)).'" data-toggle="tooltip" data-placement="top" title="Return Product" data-original-title="Devolver Producto"><i class="fa fa-mail-reply"></i></a>' : '';
                }
	        	$view = auth()->user()->can('sales-order-view') ? '<a class="btn btn-sm btn-info" href="'.route('sales-order-view',base64_encode($query->id)).'" data-toggle="tooltip" data-placement="top" title="Ver Orden" data-original-title="Ver Pedido"><i class="fa fa-eye"></i></a>' : '';

	        	return '<div class="btn-group btn-group-xs">'.$factuelec.$download.$return.$view.'</div>';
	        })
        ->escapeColumns(['action'])
        ->addIndexColumn()
        ->make(true);
    }

    public function salesOrderFacturar($id)
    {
    if(booking::find(base64_decode($id)))
        {


        $booking = booking::find(base64_decode($id));
        $user = user::find($booking->userId);

        $CUIT = '20187412065';
        $MODO = 1; //afip\Wsaa::MODO_HOMOLOGACION;
        $puntoVenta=13;
        if ($user->doc_type=='CUIT')
          {
          $letra='A';
          $codigoTipoComprobante = '01';
          $codigoTipoDocumento = '80';
          $TipoDocumento = 'CUIT';
          $nomCliente = $booking->companyname;
          }
          else
          {
          $letra='B';
          $codigoTipoComprobante = '06';
          $codigoTipoDocumento= '96';
          $TipoDocumento = 'DNI';
          $nomCliente = $booking->firstname.' '.$booking->lastname;
          }

        if (!$booking->cae_nro)
        {

         $afip = new Wsfev1($CUIT,$MODO);
         $numeroComprobante = $afip->consultarUltimoComprobanteAutorizado($puntoVenta,$codigoTipoComprobante);
         $numeroComprobante++;
         //die('--'.$numeroComprobante);

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
         "importeTotal" => $booking->payableAmount,
         "importeOtrosTributos" => 0.000,
         "importeGravado" => round($booking->payableAmount / 1.21,2),
         "importeNoGravado" => 0.000,
         "importeExento" => 0.000,
         "importeIVA" => round($booking->payableAmount - round($booking->payableAmount / 1.21,2),2),
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
                    "importe" => round($booking->payableAmount - round($booking->payableAmount / 1.21,2),2),
                    "BaseImp" => round($booking->payableAmount / 1.21,2),
                )
            ),
         "Tributos" => Array(),
         "CbtesAsoc" => Array()
         );

         try {
         $afip = new Wsfev1($CUIT,$MODO);
         $result = $afip->emitirComprobante($voucher);
         if ($result['cae'])
          {
          // grabar los datos
          $booking->cae_fac = $puntoVenta.'-'.$numeroComprobante;
          $booking->cae_nro = $result['cae'];
          $booking->cae_type = $letra;
          $booking->final_invoice=date("Y-m-d");
          $booking->cae_vto =
              substr($result['fechaVencimientoCAE'],6,2).'/'.
              substr($result['fechaVencimientoCAE'],4,2).'/'.
              substr($result['fechaVencimientoCAE'],0,4);
          $booking->save();
          }
         //return array("cae" => $cae, "fechaVencimientoCAE" => $fecha_vencimiento);
         //print_r($result);
         } catch (Exception $e) {
         echo 'Falló la ejecución: ' . $e->getMessage();
         }
        }

	     // Imprimir la factura
         if ($booking->cae_nro)
         {
         $vec=explode('-',$booking->cae_fac);
         // generar qr
         $vecqr=array (
          'ver' => '1',
          'fecha' => date("Ymd"),
          'cuit' => $CUIT,
          'ptoVta' => $vec[0],
          'tipoCmp' => $codigoTipoComprobante,
          'nroCmp' => $vec[1],
          'importe' => round($booking->payableAmount * 100,0),
          'moneda' => 'ARS',
          'ctz' => 1,
          'tipoDocRec' => $codigoTipoDocumento,
          'nroDocRec' => $user->doc_number,
          'tipoCodAut' => 'E',
          'codAut' => $booking->cae_nro
           );
        $texto = 'https://www.afip.gob.ar/fe/qr/?p='.base64_encode(json_encode($vecqr)); //
        \PHPQRCode\QRcode::png($texto, sys_get_temp_dir().'/'.$booking->cae_nro.".png", 'L', 3, 2);
        if ($booking->created_by==3) {
          $payMode='PayWay';
        }
        else {
          $bookingPaymentThrough = BookingPaymentThrough::with('booking')->where('booking_id', base64_decode($id))->first();
          switch ($bookingPaymentThrough->payment_mode) {
            case 'Credit Card': $payMode='Tarjeta de Credito';break;
            case 'Debit Card': $payMode='Tarjeta de Debito';break;
            case 'Cash': $payMode='Efectivo';break;
            case 'Cheque': $payMode='Cheque';break;
            case 'Installment': $payMode='Cuotas';break;
            case 'Transfers': $payMode='Transferencia';break;
          }
        }

         $data = [
	          'booking' => $booking,
              'user' => $user,
              'qr' => sys_get_temp_dir().'/'.$booking->cae_nro.".png",
              'paymode' => $payMode
	        ];
	      $pdf = PDF::loadView('sales.sales-order-factura', $data);

          //$user->email='martinosval@gmail.com';
          //jagaiberlinsky@gmail.com
          Mail::to($user->email)->send(new SaleOrderFacturaMail($pdf,$booking));
          //notify()->success('Factura enviada correctamente');
	      return $pdf->stream($booking->tranjectionid.'.pdf');
          }
        }
        notify()->error('Oops!!!, algo salió mal, intente de nuevo.');
        return redirect()->back();

    }

    public function salesOrderCreate()
    {
        return View('sales.sales-order-list');
    }

    public function salesOrderSave(Request $request)
    {


        $this->validate($request, [
            'customer_id' 	=> 'required|integer|exists:users,id',
            //"product_id"    => "required|array|min:1",
            //"product_id.*"  => "required|string|distinct|min:1",
        ]);

        DB::beginTransaction();
        try {
            $getCustomerInfo = User::find($request->customer_id);
            $booking = new booking;
            $booking->created_by        = auth()->id();
            $booking->userId            = $getCustomerInfo->id;
            $booking->email             = $getCustomerInfo->email;
            $booking->country           = $getCustomerInfo->country;
            $booking->firstname         = $getCustomerInfo->name;
            $booking->lastname          = $getCustomerInfo->lastname;
            $booking->companyname       = $getCustomerInfo->companyname;
            $booking->address1          = $getCustomerInfo->address1;
            $booking->address2          = $getCustomerInfo->address2;
            $booking->city              = $getCustomerInfo->city;
            $booking->state             = $getCustomerInfo->state;
            $booking->postcode          = $getCustomerInfo->postcode;
            $booking->phone             = $getCustomerInfo->phone;

            $booking->shipping_email    = $request->shipping_email;
            $booking->shipping_country  = $request->shipping_country;
            $booking->shipping_firstname= $request->shipping_name;
            $booking->shipping_lastname = $request->shipping_lastname;
            $booking->shipping_companyname = $request->shipping_companyname;
            $booking->shipping_address1 = $request->shipping_address1;
            $booking->shipping_address2 = $request->shipping_address2;
            $booking->shipping_city     = $request->shipping_city;
            $booking->shipping_state    = $request->shipping_state;
            $booking->shipping_postcode = $request->shipping_postcode;
            $booking->shipping_phone    = $request->shipping_phone;

            $booking->orderNote         = $request->remark;
            $booking->tranjectionid     = time().rand(0000,9999);
            $booking->amount            = $request->total_amount;
            $booking->installments      = 0;
            //$booking->interestAmount    = $request->customer_id;
            $booking->tax_percentage    = $request->tax_percentage;
            $booking->tax_amount        = $request->tax_amount;
            $booking->shipping_charge   = $request->shipping_charge;
            $booking->payableAmount     = $request->gross_amount;
            $booking->is_coupon_apply   = ($request->coupon_id!='') ? 1:0;
            $booking->coupon_id         = $request->coupon_id;
            $booking->discount_percentage  = $request->coupon_discount;
            $booking->coupon_discount     = $request->max_dis;

            $booking->paymentThrough    = 'POS';
            $booking->orderstatus       = 'approved';
            //$booking->due_condition     = $request->customer_id;
            //$booking->deliveryStatus    = 'Delivered';
            $booking->ip_address        = $request->ip();
            $booking->save();
            $this->applyCoupon($booking->id);

            foreach ($request->product_id as $key => $product) {
                if(!empty($product))
                {
                    $bookingItem = new bookeditem;
                    $bookingItem->bookingId = $booking->id;
                    $bookingItem->itemid    = $product;
                    $bookingItem->itemqty   = $request->required_qty[$key];
                    $bookingItem->itemPrice = $request->price[$key];
                    $bookingItem->save();


                    //Stock Deduct pausar si la categoria es un accesorio
                    $updateStock = Producto::find($product);
                    $newStock= $updateStock->stock - $request->required_qty[$key];
                    if ($updateStock->publicable ==1 )
                    {
                      $updateStock->activo = 1  ;
                    }
                    else if ($newStock<1) {
                      $updateStock->activo = 0  ;
                    }
                    $updateStock->stock = $newStock;
                    $updateStock->save();
                    //Stock Deduct

                    //Start ***Available Quantity update in ML
                    // $response = $this->updateStockMl($product, $request->required_qty[$key]);
                    $response = $this->actStockMl($product, $newStock,$updateStock->publicable);
                    $bookingItem->is_stock_updated_in_ml = $response;
                    $bookingItem->save();
                    //End ***Available Quantity update in ML

                }
            }

            if(isset($request->add_gen_product) && $request->add_gen_product)
            {
                foreach ($request->gen_product_name as $key => $product) {
                    if(!empty($product))
                    {
                        $bookingItem = new BookeditemGeneric;
                        $bookingItem->booking_id = $booking->id;
                        $bookingItem->item_name = $product;
                        $bookingItem->itemqty   = $request->gen_required_qty[$key];
                        $bookingItem->itemPrice = $request->gen_price[$key];
                        $bookingItem->save();
                    }
                }
            }

            if($request->payment_through=='Partial Payment')
            {
              $getOfferDay = Websitesetting::select('ahora12')->first();
              if ($getOfferDay->ahora12=='yes') {
                $getPercentageValue = InterestRate::where('id', 2)->first();
              }
              else {
                $getPercentageValue = InterestRate::where('id', 1)->first();
              }
                foreach ($request->partial_payment_mode as $key => $value) {
                    $payment = new BookingPaymentThrough;
                    $payment->booking_id    = $booking->id;
                    $payment->payment_mode  = $value;
                    $payment->amount        = $request->partial_amount[$key];
                    if($value=='Cheque')
                    {
                        $payment->cheque_number = $request->cheque_number[$key];
                        $payment->bank_detail   = $request->bank_detail[$key];
                    }
                    else if($value=='Installment')
                    {
                        $payment->no_of_installment  = $request->no_of_installment[$key];
                        $payment->installment_amount = $request->installment_amount[$key];
                        $booking->installments    = $request->no_of_installment[$key];
                    }
                    else if($value=='Credit Card')
                    {
                      $duration = 'month_'.$request->no_of_installment[$key];
                      $getCoef = 1+($getPercentageValue->$duration/100);
                        $cashPrice =  $request->gross_amount / $getCoef ;
                        $payment->card_brand  = $request->card_brand[$key];
                        $payment->card_number = $request->card_number[$key];
                        $payment->no_of_installment  = $request->no_of_installment[$key];
                        $booking->installments    = $request->no_of_installment[$key];
                        $booking->interestAmount  = $request->gross_amount - $cashPrice;
                  }
                    $payment->save();
                    $booking->save();
                }
            }

            //Send Notification
            $details = [
                'body'      => 'Orden Numero #'.$booking->tranjectionid. ' realizada por '.auth()->user()->name.'. El monto del Pedido es $'.$booking->payableAmount,
                'actionText'=> 'Ver Pedido',
                'actionURL' => route('sales-order-view',base64_encode($booking->id)),
                'order_id'  => $booking->id
            ];

            Notification::send(User::first(), new SaleOrderNotification($details));
	        DB::commit();
            //send Mail
            Mail::to($getCustomerInfo->email)->send(new SaleOrderMail($booking));
	        notify()->success('Hecha, Orden de venta generada exitosamente.');
            return redirect()->route('sales-order-create');
        } catch (\Exception $exception) {
            DB::rollback();
            notify()->error('Error, Oops!!!, algo salió mal, intente de nuevo.'. $exception->getMessage());
            return redirect()->back()->withInput();
        } catch (\Throwable $exception) {
            DB::rollback();
            notify()->error('Error, Oops!!!, algo salió mal, intente de nuevo.'. $exception->getMessage());
            return redirect()->back()->withInput();
        }
    }
    private function applyCoupon($bookingId)
    {
        $booking = booking::where('id',$bookingId)->first();;
        if($booking->coupon_id!=''){
            $checkCustomer = CouponCodeCustomer::where('user_id',$booking->userId)->where('coupon_id',$booking->coupon_id)->first();
            if(!empty($checkCustomer)){
                $checkCustomer->status = 1;
                $checkCustomer->save();
            } else{
                $addCouponToCustomer = new CouponCodeCustomer;
                $addCouponToCustomer->coupon_id = $booking->coupon_id;
                $addCouponToCustomer->user_id = $booking->userId;
                $addCouponToCustomer->status = 1;
                $addCouponToCustomer->save();
            }
        }
        return;
    }

    public function salesOrderView($id)
    {
        if(booking::find(base64_decode($id)))
        {
            $booking = booking::find(base64_decode($id));
            $user = user::find($booking->userId);
            return View('sales.sales-order-list', compact('booking'),compact('user'));
        }
        notify()->error('Oops!!!, algo salió mal, intente de nuevo.');
        return redirect()->back();
    }

    public function salesOrderDownload($id)
    {
        if(booking::find(base64_decode($id)))
        {
            $booking = booking::find(base64_decode($id));
            $user = user::find($booking->userId);
	        $data = [
	            'booking' => $booking,
              'user' => $user
	        ];
	        $pdf = PDF::loadView('sales.sales-order-download', $data);
	        return $pdf->stream($booking->tranjectionid.'.pdf');
        }
        notify()->error('Oops!!!, algo salió mal, intente de nuevo.');
        return redirect()->back();
    }

    public function salesOrderAction(Request $request)
    {
        $data  = $request->all();
        foreach($request->input('boxchecked') as $action)
        {
            $checkCurrentStatus = booking::select('id','tranjectionid','tax_percentage', 'amount', 'tax_amount', 'payableAmount', 'deliveryStatus')->find($action);
            if(($checkCurrentStatus->deliveryStatus=='Process' || $checkCurrentStatus->deliveryStatus=='Delivered') && $request->cmbaction=='Cancel')
            {
                booking::where('id', $action)->update(['deliveryStatus' => $request->input('cmbaction')]);

                $bookeditems = bookeditem::select('id', 'itemid', 'itemqty', 'return_qty')->where('bookingId', $action)->get();
                foreach ($bookeditems as $key => $item) {
                    $productos = Producto::select('id', 'stock')->find($item->itemid);
                    if($productos)
                    {
                        $productos->stock = $productos->stock + ($item->itemqty - $item->return_qty);
                        $productos->save();

                        /************************************************************/
                        //update record order item

                        // start update booking price
                        $calTax = ((($item->itemqty - $item->return_qty) * $item->itemPrice) * $checkCurrentStatus->tax_percentage)/100;
                        $totalAmountDeduct = (($checkCurrentStatus->payableAmount - (($item->itemqty - $item->return_qty) * $item->itemPrice)) + $calTax);

                        $checkCurrentStatus->amount = ($checkCurrentStatus->amount - (($item->itemqty - $item->return_qty) * $item->itemPrice));
                        $checkCurrentStatus->tax_amount = ($checkCurrentStatus->tax_amount - $calTax);
                        $checkCurrentStatus->payableAmount = $checkCurrentStatus->payableAmount - $totalAmountDeduct;
                        $checkCurrentStatus->save();
                        // End update booking price

                        //start Update Booking payment through amount
                        $bookingPaymentThrough = BookingPaymentThrough::with('booking')->where('booking_id', $action)->where('amount','>=', $totalAmountDeduct)->first();
                        if($bookingPaymentThrough)
                        {
                            $bookingPaymentThrough->amount = $bookingPaymentThrough->amount - $totalAmountDeduct;
                            if($bookingPaymentThrough->payment_mode=='Installment')
                            {
                                // Installment amount change if payment through installment
                                $bookingPaymentThrough->installment_amount = round((($bookingPaymentThrough->amount - $totalAmountDeduct) / $bookingPaymentThrough->no_of_installment), 2);
                            }
                            $bookingPaymentThrough->save();
                        }
                        //end Update Booking payment through amount
                        /************************************************************/

                        //Start ***Available Quantity update in ML
                        if(($item->itemqty - $item->return_qty)>0)
                        {
                            $response = $this->addStockMl($productos->id, ($item->itemqty - $item->return_qty));
                        }
                        //End ***Available Quantity update in ML
                    }
                }
            }
            else
            {
                booking::where('id', $action)->update(['deliveryStatus' => $request->input('cmbaction')]);
            }
        }
        notify()->success('Success, Delivery status successfully changed.');
        return redirect()->back();
    }

    public function getCustomerList(Request $request)
    {
        $result = User::select('id', DB::raw('CONCAT(users.name, \' \', users.lastname, \' / \', users.email) as text'))
            ->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->searchTerm. '%')
                      ->orWhere('lastname', 'like', '%' . $request->searchTerm. '%')
                      ->orWhere('phone', 'like', '%' . $request->searchTerm. '%');
            })
          ->where('status', '0')
          ->where('userType', '1')
          ->orderBy('name', 'ASC')
          ->get()->toArray();
        echo json_encode($result);
    }

    public function getProductPrice(Request $request)
    {
        $result = Producto::select('id','precio','stock')->find($request->productId);
        return response()->json($result);
    }
    public function getCustomerInfo(Request $request)
    {
        $result = User::select('id','name','lastname','email','companyname','address1','address2','phone','city','state','country','postcode')->find($request->customerId);
        return response()->json($result);
    }

    public function editSalesOrderModal(Request $request)
    {
        $error = 'Error';
        if(booking::find(base64_decode($request->id)))
        {
            $booking = booking::find(base64_decode($request->id));
            return View('sales.edit-sales-order-modal', compact('booking', 'error'));
        }
        $error = '';
        return View('sales.edit-sales-order-modal')->with('error');
    }

    public function saveSalesOrderModal(Request $request)
    {
        if(booking::find(base64_decode($request->id)))
        {
            $booking = booking::find(base64_decode($request->id));
            if(isset($request->shipping_guide) && $request->shipping_guide)
            {
                $booking->shipping_guide = date('Y-m-d');
            }
            if(isset($request->final_invoice) && $request->final_invoice)
            {
                $booking->final_invoice = date('Y-m-d');
            }
            $booking->orderNote = $request->orderNote;
            $booking->save();

            notify()->success('Success!!!, Nota de Pedido actualizada.');
            return redirect()->back();
        }
        notify()->error('Oops!!!, algo salió mal, intente de nuevo.');
        return redirect()->back();
    }

    private function updateStockMl($productoId, $purchaseQty)
    {
        $ml = auth()->user()->hasRole('ML') ;// no actualizar si es de ML
        $is_stock_updated_in_ml = '0';
        $records = Producto::select('id','nombre','categoria_id','stock','precio','publicable','mla_id')
                ->where('id', $productoId)
                ->where('disponible', '1')
                ->where('mla_id', '!=', null)
                ->orderBy('mla_id')
                ->first();
        if($records && !empty($records->mla_id) && !$ml)
        {
            $mlas = new Hokoml(\Config::get('mercadolibre'), env('ML_ACCESS_TOKEN',''), env('ML_USER_ID',''));
            $response = $mlas->product()->find($records->mla_id);
            // $noPausar = ($records->publicable) ? 'active' : 'paused' ;
            // dd($noPausar) ;
            if($response['http_code']==200)
            {
                //if product found
                $variationsArr  = array();
                $manifacturArr  = array();
                $variations = $response['body']['variations'];
                foreach ($variations as $key => $variation) {
                    if(($variation['available_quantity'] - $purchaseQty)<=0 && $records->publicable == 1) // pausar si cantdad==0 y la categoria es un accesorio
                    {
                      $manifacturArr[] = [
                        'id'          => 'MANUFACTURING_TIME',
                        'value_name'  => '21 días'
                      ];
                        $variationsArr[] = [
                            'id'    => $variation['id'],
                            'available_quantity' => 200
                        ];
                    }
                    else
                    {
                      $manifacturArr[] = [
                          'id'          => 'MANUFACTURING_TIME',
                          'value_name'  => null
                      ];
                        $variationsArr[] = [
                            'id'    => $variation['id'],
                            'available_quantity' => $variation['available_quantity'] - $purchaseQty,
                      ];
                    }
                }

                if(is_array($variationsArr) && sizeof($variationsArr)>0)
                {
                    //if variation found then update variation available quantity
                    if(($variation['available_quantity'] - $purchaseQty)<=0)
                    {
                        $response = $mlas->product()->update($records->mla_id, [
                            'variations' => $variationsArr,
                            'sale_terms' => $manifacturArr
                        ]);
                    }
                    else
                    {
                        $response = $mlas->product()->update($records->mla_id, [
                            'variations' => $variationsArr,
                            'sale_terms' => $manifacturArr
                        ]);
                    }
                }
                else
                {
                    //if variation not found then update main available quantity
                    $mainList     = $response['body'];
                    if(($mainList['available_quantity'] - $purchaseQty)<=0 && $noPausar == 'active') // pausar si la categoria es sabanas o almohadas
                    {
                        $response = $mlas->product()->update($records->mla_id, [
                            'available_quantity'    => 200,
                            'sale_terms'            => $manifacturArr
                        ]);
                    }
                    else
                    {
                        $response = $mlas->product()->update($records->mla_id, [
                            'available_quantity'    => $mainList['available_quantity'] - $purchaseQty,
                            'sale_terms'            => $manifacturArr
                        ]);
                    }
                }
                if($response['http_code']==200)
                {
                    $is_stock_updated_in_ml = '1';
                }
            }
        }
        return $is_stock_updated_in_ml;
    }

    private function addStockMl($productoId, $purchaseQty)
    {
        $ml = auth()->user()->hasRole('ML') ;// no actualizar si es de ML
        $is_stock_updated_in_ml = '0';
        $records = Producto::select('id','nombre','stock','precio','mla_id')
                ->where('id', $productoId)
                ->where('disponible', '1')
                ->where('mla_id', '!=', null)
                ->orderBy('mla_id')
                ->first();
        if($records && !empty($records->mla_id) && !$ml)
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
    private function actStockMl($productoId, $actStock,$activar)
    {
        $is_stock_updated_in_ml = '0';
        $records = Producto::select('id','nombre','stock','precio','publicable','mla_id')
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
                $manifacturArr[] = [
                    'id'          => 'MANUFACTURING_TIME',
                    'value_name'  => '21 días'
                ];
                $variations     = $response['body']['variations'];
                foreach ($variations as $key => $variation) {
                  if($actStock<=0 && $activar) // pausar si la categoria es sabana
                  {
                      $variationsArr[] = [
                          'id'    => $variation['id'],
                          'available_quantity' => 200
                      ];
                  }
                  else
                  {
                      $variationsArr[] = [
                          'id'    => $variation['id'],
                          'available_quantity' => $actStock
                      ];
                  }
                }

                if(is_array($variationsArr) && sizeof($variationsArr)>0)
                {
                    //if variation found then update variation available quantity
                    if($actStock<=0)
                    {
                        $response = $mlas->product()->update($records->mla_id, [
                            'variations' => $variationsArr,
                            'sale_terms' => $manifacturArr
                        ]);
                    }
                    else
                    {
                        $response = $mlas->product()->update($records->mla_id, [
                            'variations' => $variationsArr
                        ]);
                    }
                }
                else
                {
                    //if variation not found then update main available quantity
                    $mainList     = $response['body'];
                     //$mainList['available_quantity'] +
                     if($actStock<=0 && $activar) // pausar si la categoria es sabanas o almohadas
                     {
                         $response = $mlas->product()->update($records->mla_id, [
                             'available_quantity'    => 200,
                             'sale_terms'            => $manifacturArr
                         ]);
                     }
                     else
                     {
                         $response = $mlas->product()->update($records->mla_id, [
                             'available_quantity'    => $actStock
                         ]);
                     }
                }
                if($response['http_code']==200)
                {
                    $is_stock_updated_in_ml = '1';
                }
            }
        }
        return $is_stock_updated_in_ml;
    }

    /*----------Coupon Code------------------------------*/
    public function couponList(Request $request)
  {
    try{
    if($request->customer_id!=0){
      $user_id = $request->customer_id;
      $checkUserHasOrder = booking::where('userId',$user_id)->where('orderstatus','approved')->count();
      $pids = explode(',', @$request->pids);
      $product = Producto::whereIn('id',$pids)->get();
      $itemIds = $product->pluck('item_id')->toArray();
      $categoriaIds = $product->pluck('categoria_id')->toArray();
      $marcaIds = $product->pluck('marca_id')->toArray();
      $modeloId = $product->pluck('modelo_id')->toArray();
      $allCoupons = [];
      if(count($product) >0){
        $getCustomerCoupon =[];
        $getNewUserCoupons =[];
        $getCoupons =[];
        $getCustomerCoupon = CouponCodeCustomer::where('user_id',$user_id)->join('coupon_codes','coupon_codes.id','=','coupon_code_customers.coupon_id')->where('coupon_codes.user_type','2')->where('coupon_codes.status','1')->whereDate('coupon_codes.coupon_expity','>=',date('Y-m-d'))->pluck('coupon_code_customers.coupon_id')->toArray();
        if($checkUserHasOrder<1){
            $getNewUserCoupons = CouponCode::where('user_type','3')->where('status','1')->whereDate('coupon_expity','>=',date('Y-m-d'))->pluck('id')->toArray();
        } else{
            $getCoupons = CouponCode::where('user_type','1')->where('status','1')->whereDate('coupon_expity','>=',date('Y-m-d'))->pluck('id')->toArray();
        }
        $arrayMarge = array_merge($getCustomerCoupon,$getCoupons,$getNewUserCoupons);
        $allCoupons = CouponCode::whereIn('id',$arrayMarge)
          ->where(function ($allCoupons) use ($marcaIds,$itemIds,$categoriaIds,$modeloId) {
                if(!empty($marcaIds)){
                    $allCoupons->orWhereIn('type_id', $marcaIds)->where('type','Marca');
                }
                if(!empty($itemIds)){
                    $allCoupons->orWhereIn('type_id', $itemIds)->where('type','Item');
                }
                if(!empty($categoriaIds)){
                    $allCoupons->orWhereIn('type_id', $categoriaIds)->where('type','categoría');
                }
                if(!empty($modeloId)){
                    $allCoupons->orWhereIn('type_id', $modeloId)->where('type','Modelo');
                }
          })

          ->get();

      }
       return View('sales.coupon-list',compact('allCoupons'));

    } else{
      $data = [
                'type'      => 'error',
                'message'      => 'Por favor seleccione cliente',
      ];
      return response()->json($data, 200);
    }
    } catch (\Exception $exception) {
      $data = [
                'type'      => 'error',
                'message'      => $exception->getMessage(),
      ];
      return response()->json($data, 200);

    }
  }
   public function checkCouponCode(Request $request)
  {
     try{
    $is_apply = false;
    if($request->customer_id!=0){
        $user_id = $request->customer_id;
        $checkCoupon = CouponCode::where('coupon_code',$request->coupon_code)->whereDate('coupon_expity','>=',date('Y-m-d'))->first();

        if(empty($checkCoupon)){
        $data = [
                  'type'      => 'error',
                  'message'      => 'Sorry ,this coupon is not valid',
        ];
        return response()->json($data, 200);
        }

        if($checkCoupon->user_type=='1'){
        $checkAlreadyuse = $checkUserhasCoupon = CouponCodeCustomer::where('user_id',$user_id)->where('coupon_id',$checkCoupon->id)->where('status','1')->first();

            if(!empty($checkAlreadyuse)){
              $data = [
                        'type'      => 'error',
                        'message'      => 'Sorry ,this coupon is already used',
              ];
              return response()->json($data, 200);
            }
          $is_apply= true;

        }elseif($checkCoupon->user_type=='3'){
          $checkAlreadyuse = $checkUserhasCoupon = CouponCodeCustomer::where('user_id',$user_id)->where('coupon_id',$checkCoupon->id)->where('status','1')->first();

              if(!empty($checkAlreadyuse)){
                $data = [
                          'type'      => 'error',
                          'message'      => 'Sorry ,this coupon is already used',
                ];
                return response()->json($data, 200);
              }
            $is_apply= true;

        } else{
        $checkUserhasCoupon = CouponCodeCustomer::where('user_id',$user_id)->where('coupon_id',$checkCoupon->id)->where('status','0')->first();
          if(empty($checkUserhasCoupon)){
            $data = [
                      'type'      => 'error',
                      'message'      => 'Sorry ,this coupon is not valid for this user',
            ];
            return response()->json($data, 200);
          }
          $is_apply =true;
        }
        $depend_on_category = (!empty($checkCoupon->depend_on_category)) ? explode(',',$checkCoupon->depend_on_category) : [];
        if($is_apply == true){
            $subtotal =0;
            $pids = explode(',', @$request->pids);
            $required_qty = explode(',', @$request->required_qty);
            $price = explode(',', @$request->price);
            if(count($pids) >0){
                foreach ($pids as $key => $val) {
                    $product = Producto::select('id','marca_id','item_id','categoria_id','modelo_id','precio')->where('id',$val)->first();
                    if(($checkCoupon->type =="Marca") && ($checkCoupon->type_id == $product->marca_id )){
                      $subtotal += @$required_qty[$key]*@$price[$key];

                    }
                    elseif(($checkCoupon->type =='Item') && ($checkCoupon->type_id == $product->item_id )){
                      $subtotal +=  @$required_qty[$key]*@$price[$key];

                    }
                    elseif(($checkCoupon->type =='categoría') && ($checkCoupon->type_id == $product->categoria_id )){
                        if($checkCoupon->is_depend='1' && in_array($product->categoria_id,$depend_on_category)){
                            $subtotal +=  @$required_qty[$key]*@$price[$key];
                        } else{
                            $subtotal +=  @$required_qty[$key]*@$price[$key];
                        }

                    }
                    elseif(($checkCoupon->type =='Modelo') && ($checkCoupon->type_id == $product->modelo_id )){
                      $subtotal +=  @$required_qty[$key]*@$price[$key];

                    }
                }
            }

          //$number = str_replace(',', '',$request->subtotal);
            $number = $subtotal;

            $couponDis = CouponDiscount::where('coupon_id',$checkCoupon->id)->where('min','<=',$number)
                 ->where('max','>=',$number)->first();

            if(empty($couponDis)){
                $data = [
                          'type'      => 'error',
                          'message'      => 'Sorry ,this coupon is not valid for this item',
                ];
                return response()->json($data, 200);
            }
          $coupon_dis = ($number* $couponDis->percentage_discount) /100;
          $max_saving = round($coupon_dis, 2) ;

          $data = [
                    'type'      => 'success',
                    'subtotal'      => $subtotal,
                    'max_saving'      => $max_saving,
                    'coupon_id'      => $checkCoupon->id,
                    'coupon_discount'      => $couponDis->percentage_discount,
          ];
          return response()->json($data, 200);

        }

    } else{
      $data = [
                'type'      => 'error',
                'message'      => 'Sorry ,User does not exist in our system',
      ];
      return response()->json($data, 200);
    }
    } catch (\Exception $exception) {
      $data = [
                'type'      => 'error',
                'message'      => $exception->getMessage(),
      ];
      return response()->json($data, 200);

    }
  }
  public function applyForCoupon(Request $request)
  {
    if($request->coupon_code!=''){
      $data = [
                'type'      => 'success',
        ];
      return response()->json($data, 200);

    } else{
        $data = [
            'type'      => 'error',

      ];
      return response()->json($data, 200);
    }
  }

}
