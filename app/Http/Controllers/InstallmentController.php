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
        $query = BookingPaymentThrough::select('*')->where('payment_mode', 'Installment')->orderBy('is_installment_complete','ASC')->get();
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
	        	if($query->booking->createdBy)
	        	{
	        		return '<strong>'.$query->booking->createdBy->name .' '.$query->booking->createdBy->lastname.'</strong>';
	        	}
	            return '-';
	        })
	        ->editColumn('tranjectionid', function ($query)
	        {
	            return '<strong>'.$query->booking->tranjectionid.'</strong>';
	        })
	        ->editColumn('customer_name', function ($query)
	        {
	            return '<strong>'.$query->booking->firstname .' '.$query->booking->lastname.'</strong>';
	        })
	        ->editColumn('order_date', function ($query)
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
	        ->editColumn('installment_status', function ($query)
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
                $history = auth()->user()->can('installment-paid-history') ? '<a class="btn btn-sm btn-primary" href="'.route('installment-paid-history',base64_encode($query->booking->id)).'" data-toggle="tooltip" data-placement="top" title="View History" data-original-title="View History"><i class="fa fa-list"></i></a>' : '';
	        	$view = auth()->user()->can('sales-order-view') ? '<a class="btn btn-sm btn-info" href="'.route('sales-order-view',base64_encode($query->booking->id)).'" data-toggle="tooltip" data-placement="top" title="View Order" data-original-title="View Order"><i class="fa fa-eye"></i></a>' : '';
	        	

	        	return '<div class="btn-group btn-group-xs">'.$history.$view.'</div>';
	        })
        ->escapeColumns(['action'])
        ->addIndexColumn()
        ->make(true);
    }

    public function installmentPaidHistory($id)
    {
    	if(booking::find(base64_decode($id)))
        {
            $saleInfo = booking::find(base64_decode($id));
            return View('installments.installment-order-list', compact('saleInfo'));
        }
        notify()->error('Oops!!!, something went wrong, please try again.');
        return redirect()->back();
    }

    public function installmentReceive()
    {
    	return View('installments.installment-order-list');
    }

    public function installmentReceiveSave(Request $request)
    {
        $this->validate($request, [
            'booking_id' 	=> 'required|integer|exists:bookings,id',
            'amount'     	=> 'required',
        ]);

        DB::beginTransaction();
        try {
        	$installmentReceive = new BookingInstallmentPaid;
	        $installmentReceive->booking_id	= $request->booking_id;
	        $installmentReceive->created_by = auth()->id();
	        $installmentReceive->amount 	= $request->amount;
	        $installmentReceive->save();
	        if($installmentReceive)
	        {
	        	$updateMain = BookingPaymentThrough::where('booking_id', $request->booking_id)
	        	->where('payment_mode', 'Installment')
	        	->where('is_installment_complete', 0)
	        	->where('installment_amount', $request->amount)
	        	->first();
	        	$updateMain->paid_installment = $updateMain->paid_installment + 1;
	        	$updateMain->save();
	        	if($updateMain->no_of_installment==$updateMain->paid_installment)
	        	{
	        		$updateMain->is_installment_complete = 1;
	        		$updateMain->save();
	        	}
	        }
	        //Send Notification
            $details = [
                'body'      => 'Order Number #'.$installmentReceive->booking->tranjectionid. ' new installment received by '.auth()->user()->name.'. Received amount is $'.$amount,
                'actionText'=> 'View Order',
                'actionURL' => route('installment-paid-history',base64_encode($request->booking_id)),
                'order_id'  => $request->booking_id
            ];
  
            Notification::send(User::first(), new SaleOrderNotification($details));

	        DB::commit();
	        notify()->success('Success, Installment Received created successfully.');
            return redirect()->back(); 
        } catch (\Exception $exception) {
            DB::rollback();
            notify()->error('Error, Oops!!!, something went wrong, please try again.');
            return redirect()->back()->withInput(); 
        } catch (\Throwable $exception) {
            DB::rollback();
            notify()->error('Error, Oops!!!, something went wrong, please try again.');
            return redirect()->back()->withInput();
        }
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
      	notify()->success('Success, Status successfully changed to the selected orders.');
      	return redirect()->back();
  	}

  	public function getInstalmentOrderList(Request $request)
    {
    	$result = booking::select('id','tranjectionid as text')
        ->where('tranjectionid', 'like', '%' . $request->searchTerm. '%')
        ->whereIn('deliveryStatus', ['Return','Delivered'])
        ->orderBy('id','ASC')
        ->get()->toArray();
      	echo json_encode($result);
    }
}
