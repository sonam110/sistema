<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\InstallmentReceive;
use App\Notifications\SaleOrderNotification;
use Notification;
use App\booking;
use App\BookingPaymentThrough;
use App\BookingInstallmentPaid;
use App\User;
use DB;
use PDF;
use Mail;

class InstallmentController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:installment-order-list', ['only' => ['installmentOrderList']]);
        $this->middleware('permission:installment-paid-history', ['only' => ['installmentPaidHistory']]);
        $this->middleware('permission:installment-receive', ['only' => ['installmentReceive','installmentReceiveSave']]);
        $this->middleware('permission:installment-action', ['only' => ['installmentAction']]);
    }

    public function installmentOrderList()
    {
      	return view('installments.installment-order-list');
    }

    public function installmentOrderDatatable(Request $request)
    {
        $query = BookingPaymentThrough::with('booking')->select('*')->where('payment_mode', 'Installment')->where('is_installment_complete',0);
        return datatables($query)
        	->addColumn('checkbox', function ($query)
	        {
	            return '<label class="custom-control custom-checkbox">
	                   <input type="checkbox"  name="boxchecked[]" value="' . $query->id . '"  class ="colorinput-input custom-control-input allChecked" id="boxchecked">
	                     <span class="custom-control-label"></span>
	                    </label>';
	        })
        	->addColumn('placed_by', function ($query)
	        {
	        	if($query->booking->createdBy)
	        	{
	        		return '<strong>'.$query->booking->createdBy->name .' '.$query->booking->createdBy->lastname.'</strong>';
	        	}
	            return '-';
	        })
	        ->addColumn('tranjectionid', function ($query)
	        {
	            return '<strong>'.$query->booking->tranjectionid.'</strong>';
	        })
	        ->addColumn('customer_name', function ($query)
	        {
	            return '<strong>'.$query->booking->firstname .' '.$query->booking->lastname.'</strong>';
	        })
	        ->addColumn('order_date', function ($query)
	        {
	            return $query->booking->created_at->format('Y-m-d');
	        })
	        ->editColumn('amount', function ($query)
	        {
	            return '<strong>$'.$query->amount.'</strong>';
	        })
	        ->editColumn('no_of_installment', function ($query)
	        {
	        	return '<span class="badge badge-primary">'.$query->no_of_installment.'</span>';
	        })
	        ->editColumn('installment_amount', function ($query)
	        {
	            return '<strong>$'.$query->installment_amount.'</strong>';
	        })
	        ->editColumn('paid_installment', function ($query)
	        {
	        	return '<span class="badge badge-primary">'.$query->paid_installment.'</span>';
	        })
	        ->addColumn('installment_status', function ($query)
	        {
	            if ($query->is_installment_complete == 0)
	            {
	                $status = '<span class="badge badge-danger">No</span>';
	            }
	            else
	            {
	            	$status = '<span class="badge badge-success">Yes</span>';
	            }
	            return $status;
	        })
	        ->addColumn('action', function ($query)
	        {
                $history = auth()->user()->can('installment-paid-history') ? '<a class="btn btn-sm btn-primary" href="'.route('installment-paid-history',['id'=>base64_encode($query->booking->id),'paymentThroughId'=>base64_encode($query->id)]).'" data-toggle="tooltip" data-placement="top" title="Ver Historial" data-original-title="Ver Historial"><i class="fa fa-list"></i></a>' : '';
	        	$view = auth()->user()->can('sales-order-view') ? '<a class="btn btn-sm btn-info" href="'.route('sales-order-view',base64_encode($query->booking->id)).'" data-toggle="tooltip" data-placement="top" title="Ver Orden" data-original-title="Ver Pedido"><i class="fa fa-eye"></i></a>' : '';


	        	return '<div class="btn-group btn-group-xs">'.$history.$view.'</div>';
	        })
        ->escapeColumns(['action'])
        ->addIndexColumn()
        ->make(true);
    }

    public function installmentPaidHistory($id, $paymentThroughId)
    {
    	if(booking::find(base64_decode($id)))
        {
            $saleInfo = booking::with([
                'bookingInstallmentPaids' => function ($query) use ($paymentThroughId) {
                    $query->where('booking_payment_through_id', base64_decode($paymentThroughId));
                }
            ])->find(base64_decode($id));
            return View('installments.installment-order-list', compact('saleInfo'));
        }
        notify()->error('Oops!!!, algo salió mal, intente de nuevo.');
        return redirect()->back();
    }

    public function installmentReceive()
    {
    	return View('installments.installment-order-list');
    }

    public function installmentReceiveSave(Request $request)
    //$bookingId, $paymentThroughId
    {
        $insInfo = BookingPaymentThrough::where('booking_id',base64_decode($request->bookingId))->where('id', base64_decode($request->paymentThroughId))->where('is_installment_complete', '0')->first();

        if($insInfo)
        {
          if ($insInfo->installment_partial_amount > 0)
            {
            $mc = $insInfo->installment_partial_amount;
            }
            else
            {
            $mc = $insInfo->installment_amount;
            }

           if ((!is_numeric($request->amount)) ||
              ($request->amount > $mc))
              {
               notify()->error('Error, Oops!!!, Monto incorrecto');
               return redirect()->back()->withInput();
              }


           DB::beginTransaction();
            try {
                $installmentReceive = new BookingInstallmentPaid;
                $installmentReceive->booking_id = base64_decode($request->bookingId);
                $installmentReceive->booking_payment_through_id = base64_decode($request->paymentThroughId);
                $installmentReceive->created_by = auth()->id();
                //$installmentReceive->amount     = $insInfo->installment_amount;
                $installmentReceive->payment_mode = $request->payment_mode;
                $installmentReceive->amount     = $request->amount;
                $installmentReceive->save();
                if($installmentReceive)
                {
                    if ($request->amount==$mc)
                     {
                     $insInfo->paid_installment = $insInfo->paid_installment + 1;
                     $insInfo->installment_partial_amount = 0;
                     if($insInfo->no_of_installment==$insInfo->paid_installment)
                       {
                       $insInfo->is_installment_complete = 1;
                       }

                     }
                     else
                     {
                     $insInfo->installment_partial_amount = $mc - $request->amount;
                     //return $mc;
                     }
                    $insInfo->save();
                }
                //Send Notification
                $details = [
                    'body'      => 'Orden Numero #'.$installmentReceive->booking->tranjectionid. ' Nuevo cobro recibido por '.auth()->user()->name.'. Monto recibido $'.$request->amount,
                    'actionText'=> 'Ver Pedido',
                    'actionURL' => route('installment-paid-history',['id'=>$request->bookingId,'paymentThroughId'=>$request->paymentThroughId]),
                    'order_id'  => base64_encode($request->bookingId)
                ];

                Notification::send(User::first(), new SaleOrderNotification($details));

                DB::commit();
                notify()->success('Hecho , Cobro recibido exitosamente.');
                return redirect()->back();
            } catch (\Exception $exception) {
                DB::rollback();
                dd($exception);
                notify()->error('Error, Oops!!!, algo salió mal, intente de nuevo.');
                return redirect()->back()->withInput();
            } catch (\Throwable $exception) {
                DB::rollback();
                dd($exception);
                notify()->error('Error, Oops!!!, algo salió mal, intente de nuevo.');
                return redirect()->back()->withInput();
            }
        }
        notify()->error('Oops!!!, algo salió mal, intente de nuevo.');
        return redirect()->back();
    }

    public function installmentAction(Request $request)
    {
      	$data  = $request->all();
      	foreach($request->input('boxchecked') as $action)
      	{
            if($request->input('cmbaction')=='Change Status Completed')
            {
                $changeStatus = BookingPaymentThrough::find($action);
                $changeStatus->is_installment_complete = 1;
                $changeStatus->save();
                Mail::to($changeStatus->booking->email)->send(new InstallmentReceive($changeStatus));
            }
      	}
      	notify()->success('Hecho, El estado se cambió con éxito en los pedidos seleccionados.');
      	return redirect()->back();
  	}

  	public function getInstalmentOrderList(Request $request)
    {
    	$result = booking::select('bookings.id','tranjectionid as text')
            ->join('booking_payment_throughs', function ($join) {
                $join->on('booking_payment_throughs.booking_id', '=', 'bookings.id');
            })
            ->where('tranjectionid', 'like', '%' . $request->searchTerm. '%')
            ->whereIn('deliveryStatus', ['Return','Delivered','Process'])
            ->where('booking_payment_throughs.payment_mode', 'Installment')
            ->where('booking_payment_throughs.is_installment_complete', '0')
            ->orderBy('bookings.id','ASC')
            ->groupBy('booking_id')
            ->get()->toArray();
      	echo json_encode($result);
    }

    public function getInstallmentOrderInformation(Request $request)
    {
        $saleInfo = booking::find($request->orderId);
        if($saleInfo)
        {
            return view('installments.get-installment-order-information', compact('saleInfo'));
        }
        return 'not-found';
    }

    public function getInstallmentHistory(Request $request)
    {
      $saleInfo = booking::find($request->orderId);
      return view('installments.get-installment-history', compact('saleInfo'));
    }
}
