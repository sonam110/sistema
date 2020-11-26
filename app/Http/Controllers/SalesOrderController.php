<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Mail\SaleOrder as SaleOrderMail;
use App\Notifications\SaleOrderNotification;
use Notification;
use App\booking;
use App\bookeditem;
use App\BookeditemGeneric;
use App\Producto;
use App\BookingPaymentThrough;
use App\User;
use DB;
use PDF;
use Mail;


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
    		$query = booking::select('id','created_by','firstname','lastname','tranjectionid','payableAmount','paymentThrough','deliveryStatus','created_at')->where('created_by', '!=', null)->orderBy('id','DESC')->with('createdBy')->get();
    	}
    	else
    	{
    		$query = booking::select('id','created_by','firstname','lastname','tranjectionid','payableAmount','paymentThrough','deliveryStatus','created_at')->where('created_by', '!=', null)->where('created_by', auth()->id())->orderBy('id','DESC')->with('createdBy')->get();
    	}
        return datatables($query)
            ->addColumn('checkbox', function ($query)
            {
                return '<label class="custom-control custom-checkbox">
                       <input type="checkbox"  name="boxchecked[]" value="' . $query->id . '"  class ="colorinput-input custom-control-input allChecked" id="boxchecked">
                         <span class="custom-control-label"></span>
                        </label>';
            })
        	->editColumn('placed_by', function ($query)
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
	        ->editColumn('customer_name', function ($query)
	        {
	            return '<strong>'.$query->firstname .' '.$query->lastname.'</strong>';
	        })
	        ->editColumn('order_date', function ($query)
	        {
	            return $query->created_at->format('Y-m-d');
	        })
	        ->editColumn('payableAmount', function ($query)
	        {
	            return '<strong>$'.$query->payableAmount.'</strong>';
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
	                $status = '<span class="badge badge-danger">'.$query->deliveryStatus.'</span>';
	            }
	            else
	            {
	                $status = '<span class="badge badge-default">'.$query->deliveryStatus.'</span>';
	            }
	            return $status;
	        })
	        ->addColumn('action', function ($query)
	        {
	        	$download = auth()->user()->can('sales-order-download') ? '<a class="btn btn-sm btn-default" target="_blank" href="'.route('sales-order-download',base64_encode($query->id)).'" data-toggle="tooltip" data-placement="top" title="Download / Print" data-original-title="Descargar / Imprimir"><i class="fa fa-download"></i></a>' : '';
	        	$return = '';
                if($query->deliveryStatus=='Delivered')
                {
                    $return = auth()->user()->can('sales-order-return') ? '<a class="btn btn-sm btn-warning" href="'.route('sales-order-return',base64_encode($query->id)).'" data-toggle="tooltip" data-placement="top" title="Return Product" data-original-title="Devolver Producto"><i class="fa fa-mail-reply"></i></a>' : '';
                }
	        	$view = auth()->user()->can('sales-order-view') ? '<a class="btn btn-sm btn-info" href="'.route('sales-order-view',base64_encode($query->id)).'" data-toggle="tooltip" data-placement="top" title="Ver Orden" data-original-title="Ver Pedido"><i class="fa fa-eye"></i></a>' : '';

	        	return '<div class="btn-group btn-group-xs">'.$download.$return.$view.'</div>';
	        })
        ->escapeColumns(['action'])
        ->addIndexColumn()
        ->make(true);
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

            $booking->shipping_email    = $getCustomerInfo->email;
            $booking->shipping_country  = $getCustomerInfo->country;
            $booking->shipping_firstname= $getCustomerInfo->name;
            $booking->shipping_lastname = $getCustomerInfo->lastname;
            $booking->shipping_companyname = $getCustomerInfo->companyname;
            $booking->shipping_address1 = $getCustomerInfo->address1;
            $booking->shipping_address2 = $getCustomerInfo->address2;
            $booking->shipping_city     = $getCustomerInfo->city;
            $booking->shipping_state    = $getCustomerInfo->state;
            $booking->shipping_postcode = $getCustomerInfo->postcode;
            $booking->shipping_phone    = $getCustomerInfo->phone;

            $booking->orderNote         = $request->remark;
            $booking->tranjectionid     = time().rand(0000,9999);
            $booking->amount            = $request->total_amount;
            //$booking->installments      = 0;
            //$booking->interestAmount    = $request->customer_id;
            $booking->tax_percentage    = $request->tax_percentage;
            $booking->tax_amount        = $request->tax_amount;
            $booking->payableAmount     = $request->gross_amount;
            //$booking->paymentThrough    = $request->payment_through;
            $booking->orderstatus       = 'approved';
            //$booking->due_condition     = $request->customer_id;
            //$booking->deliveryStatus    = 'Delivered';
            $booking->ip_address        = $request->ip();
            $booking->save();

            foreach ($request->product_id as $key => $product) {
                if(!empty($product))
                {
                    $bookingItem = new bookeditem;
                    $bookingItem->bookingId = $booking->id;
                    $bookingItem->itemid    = $product;
                    $bookingItem->itemqty   = $request->required_qty[$key];
                    $bookingItem->itemPrice = $request->price[$key];
                    $bookingItem->save();

                    //Stock Deduct
                    $updateStock = Producto::find($product);
                    $updateStock->stock = $updateStock->stock - $request->required_qty[$key];
                    $updateStock->save();
                    //Stock Deduct
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
                    }
                    else if($value=='Credit Card')
                    {
                        $payment->card_brand  = $request->card_brand[$key];
                        $payment->card_number = $request->card_number[$key];
                    }
                    $payment->save();
                }
            }


            //send Mail
            Mail::to($getCustomerInfo->email)->send(new SaleOrderMail($booking));

            //Send Notification
            $details = [
                'body'      => 'Order Number #'.$booking->tranjectionid. ' has been placed by '.auth()->user()->name.'. the order amount is $'.$booking->payableAmount,
                'actionText'=> 'Ver Pedido',
                'actionURL' => route('sales-order-view',base64_encode($booking->id)),
                'order_id'  => $booking->id
            ];

            Notification::send(User::first(), new SaleOrderNotification($details));

	        DB::commit();
	        notify()->success('Success, Sale order created successfully.');
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

    public function salesOrderView($id)
    {
        if(booking::find(base64_decode($id)))
        {
            $booking = booking::find(base64_decode($id));
            return View('sales.sales-order-list', compact('booking'));
        }
        notify()->error('Oops!!!, algo salió mal, intente de nuevo.');
        return redirect()->back();
    }

    public function salesOrderDownload($id)
    {
        if(booking::find(base64_decode($id)))
        {
            $booking = booking::find(base64_decode($id));
	        $data = [
	            'booking' => $booking
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
            booking::where('id', $action)->update(['deliveryStatus' => $request->input('cmbaction')]);
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
}
