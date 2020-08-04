<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Mail\SaleOrder as SaleOrderMail;
use App\booking;
use App\bookeditem;
use App\Producto;
use App\InterestRate;
use App\Websitesetting;
use App\CardInfo;
use App\User;
use DB;
use PDF;
use Mail;

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
	        	$download = auth()->user()->can('sales-order-download') ? '<a class="btn btn-sm btn-default" target="_blank" href="'.route('sales-order-download',base64_encode($query->id)).'" data-toggle="tooltip" data-placement="top" title="Download / Print / Print" data-original-title="Download / Print / Print"><i class="fa fa-download"></i></a>' : '';
	        	$return = '';
                if($query->deliveryStatus=='Delivered')
                {
                    $return = auth()->user()->can('sales-order-return') ? '<a class="btn btn-sm btn-warning" href="'.route('sales-order-return',base64_encode($query->id)).'" data-toggle="tooltip" data-placement="top" title="Return Product" data-original-title="Return Product"><i class="fa fa-mail-reply"></i></a>' : '';
                }
	        	$view = auth()->user()->can('sales-order-view') ? '<a class="btn btn-sm btn-info" href="'.route('sales-order-view',base64_encode($query->id)).'" data-toggle="tooltip" data-placement="top" title="View Order" data-original-title="View Order"><i class="fa fa-eye"></i></a>' : '';

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
            "product_id"    => "required|array|min:1",
            "product_id.*"  => "required|string|distinct|min:1",
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
            $booking->paymentThrough    = $request->payment_through;
            //$booking->orderstatus       = 'approved';
            //$booking->due_condition     = $request->customer_id;
            $booking->deliveryStatus    = 'Delivered';
            $booking->ip_address        = $request->ip();
            $booking->save();

            foreach ($request->product_id as $key => $product) {
                $bookingItem = new bookeditem;
                $bookingItem->bookingId = $booking->id;
                $bookingItem->itemid    = $product;
                $bookingItem->itemqty   = $request->required_qty[$key];
                $bookingItem->itemPrice = $request->price[$key];
                $bookingItem->save();

                //Stock Deduct
                $updateStock = Producto::select('id','stock')->find($product);
                $updateStock->stock = $updateStock->stock - $request->required_qty[$key];
                $updateStock->save();
                //Stock Deduct
            }

            //send Mail
            Mail::to($getCustomerInfo->email)->send(new SaleOrderMail($booking));

	        DB::commit();
	        notify()->success('Success, Sale order created successfully.');
            return redirect()->route('sales-order-create'); 
        } catch (\Exception $exception) {
            DB::rollback();
            notify()->error('Error, Oops!!!, something went wrong, please try again.'. $exception->getMessage());
            return redirect()->back()->withInput(); 
        } catch (\Throwable $exception) {
            DB::rollback();
            notify()->error('Error, Oops!!!, something went wrong, please try again.'. $exception->getMessage());
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

    public function getProductPrice(Request $request)
    {
        $result = Producto::select('id','precio','stock')->find($request->productId);
        return response()->json($result);
    }
}
