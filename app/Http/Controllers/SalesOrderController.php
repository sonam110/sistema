<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\booking;
use App\bookeditem;
use App\Producto;
use App\InterestRate;
use App\Websitesetting;
use App\CardInfo;
use App\User;
use DB;
use PDF;

class SalesOrderController extends Controller
{
    function __construct()
    {
        $this->middleware(['role:admin','permission:sales-order-list']);
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
	            return '<strong>'.$query->payableAmount.'</strong>';
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
	        	$download = auth()->user()->can('sales-order-download') ? '<a class="btn btn-sm btn-default" target="_blank" href="'.route('sales-order-download',base64_encode($query->id)).'" data-toggle="tooltip" data-placement="top" title="Download" data-original-title="Download"><i class="fa fa-download"></i></a>' : '';
	        	
	        	$view = auth()->user()->can('sales-order-view') ? '<a class="btn btn-sm btn-info" href="'.route('sales-order-view',base64_encode($query->id)).'" data-toggle="tooltip" data-placement="top" title="View Order" data-original-title="View Order"><i class="fa fa-eye"></i></a>' : '';

	        	return '<div class="btn-group btn-group-xs">'.$download.$view.'</div>';
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
        notify()->info('info, Under working.');
        return redirect()->back(); 

        $this->validate($request, [
            'supplier_id' 	=> 'required|integer|exists:suppliers,id',
            'po_date'     	=> 'required',
            "product_id"    => "required|array|min:1",
            "product_id.*"  => "required|string|distinct|min:1",
        ]);

        DB::beginTransaction();
        try {

	        DB::commit();
	        notify()->success('Success, Sale order created successfully.');
            return redirect()->route('purchase-order-list'); 
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

    public function salesOrderView($id)
    {
        if(booking::find(base64_decode($id)))
        {
            $booking = booking::find(base64_decode($id));
            return View('sales.sales-order-list', compact('booking'));
        }
        notify()->error('Oops!!!, something went wrong, please try again.');
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
        notify()->error('Oops!!!, something went wrong, please try again.');
        return redirect()->back();
    }

    public function getCustomerList(Request $request)
    {
        $result = User::select('id', DB::raw('CONCAT(users.name, \' \', users.lastname, \' / \', users.phone) as text'))
            ->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->searchTerm. '%')
                      ->orWhere('lastname', 'like', '%' . $request->searchTerm. '%')
                      ->orWhere('phone', 'like', '%' . $request->searchTerm. '%');
            })
          ->where('status', '0')
          ->where('userType', '1')
          ->get()->toArray();
        echo json_encode($result);
    }
}
