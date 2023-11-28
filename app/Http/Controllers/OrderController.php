<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\booking;
use DB;
use PDF;
use Mail;
use Auth;

class OrderController extends Controller
{
    public function allOrders()
  {
    $allOrders = booking::select('bookings.*', 'bookings.id as id')
        ->orderBy('bookings.id', 'DESC')->where(function($query) {
          $query->where('created_by', '3');
                })->get();

    return View('orders.allOrders')
            ->with('allOrders', $allOrders);
  }
  public function orderListDatatable(Request $request)
  {
    $query = booking::select('bookings.*')
        ->where(function($query) {
          $query->where('bookings.created_by', '3');
                });
      return datatables($query)
        ->addColumn('checkbox', function ($query)
          {
              $hidden = '';
              if($query->deliveryStatus=='Cancel'){
                 $hidden = 'hidden';
              }
              $checkbox = '<span '.$hidden.'>
                    <input type="checkbox" id="c'.$query->id.'a" class="chkNumber" name="boxchecked[]" value="'.$query->id.'" />
                    <label for="c'.$query->id.'a"><span></span></label>
                  </span>';

              return $checkbox;
          })

          ->editColumn('orderstatus', function ($query)
          {
              if ($query->orderstatus == 'approved')
              {
                 $status = ' <span class="text-success">Aprobada</span>';

              }

              else
              {
                  $status = '<span class="text-danger">Pendiente</span>';
              }
              return $status;
          })
          ->editColumn('firstname', function ($query)
          {

              return $query->firstname.' '.$query->lastname;
          })
          ->editColumn('created_at', function ($query)
          {
            return date('d M Y', strtotime($query->created_at));
          })
          ->editColumn('payableAmount', function ($query)
          {

              return '<i class="fa fa-usd"></i>'.$query->payableAmount.'';
          })

          ->addColumn('action', function ($query)
          {
            $check='';
            $outline='';
            $alert='';
            $download='';
            if($query->deliveryStatus=='Delivered'){
                 $check .='<a href="javascript:;" class="btn btn-sm btn-success tooltips" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delivered"><i class="fa fa-check"></i></a>';
            } elseif($query->deliveryStatus=='Process') {
             $outline .=' <a href="javascript:;" class="btn btn-sm btn-info tooltips" data-toggle="tooltip" data-placement="top" title="" data-original-title="Process"><i class="fa fa-spinner"></i></a>';
            }else{
              $alert .='<a href="javascript:;" class="btn btn-sm btn-danger tooltips" data-toggle="tooltip" data-placement="top" title="" data-original-title="Cancel"><i class="fa fa-warning"></i></a>';
            }
            $view  ='<a data-toggle="modal" data-target="#orderData" id="getOrderDetail" data-id="'.$query->id.'" href="#" class="btn btn-sm btn-secondary tooltips" data-placement="top" title="View Order"><i class="fa fa-eye"></i></a>';
            if($query->tranjectionid !=''){
              $download .='<a data-toggle="tooltip" href="'.route('generate-invoice',$query->tranjectionid).'" class="btn btn-sm btn-warning tooltips" data-placement="top" title="Descargar pedido"><i class="fa fa-download"></i></a>';
            }

           
            return '<div class="btn-group btn-group-xs">'.$check.$outline.$alert.$view.$download.'</div>';
          })
        ->escapeColumns(['action'])
        ->addIndexColumn()
        ->make(true);
  }

  public function actionOrders(Request $request)
    {
      $data  = $request->all();
      foreach($request->input('boxchecked') as $action)
      {
        $currentStatus = booking::select('id', 'deliveryStatus')->where('orderstatus', 'approved')->find($action);
        if($currentStatus)
        {
          if($currentStatus->deliveryStatus=='Cancel')
          {
            if($request->cmbaction!='Cancel')
            {
              $this->stockDeduct($action);
            }
          }
          else
          {
            if($request->cmbaction=='Cancel')
            {
              $this->stockBack($action);
            }
          }
        }

        booking::where('id', $action)->update(array('deliveryStatus' => $request->input('cmbaction')));

      }
       notify()->success('Success, Estado de los pedidos actualizado...');
     
      return \Redirect()->back();
  }

  private function stockBack($bookingId)
  {
      $booking = booking::find($bookingId);
      foreach ($booking->getBookeditem as $key => $bookedItem) {
          $product = Producto::find($bookedItem->itemid);
          $product->stock = $product->stock + $bookedItem->itemqty;
          $product->save();

          //Start ***Available Quantity update in ML
          if($bookedItem->itemqty>0)
          {
            $response = $this->addStockMl($product->id, $bookedItem->itemqty);
          }
          //End ***Available Quantity update in ML
      }
      return;
  }

  private function stockDeduct($bookingId)
  {
      $booking = booking::find($bookingId);
      foreach ($booking->getBookeditem as $key => $bookedItem) {
          $product = Producto::find($bookedItem->itemid);
          $product->stock = $product->stock - $bookedItem->itemqty;
          $product->save();
      }
      return;
  }


   public function getOrderDetail(Request $request)
  {
      $order = booking::select('users.*','bookings.*','bookings.id as id')
        ->where('bookings.id', $request->id)
        ->join('bookeditems', function ($join) {
                  $join->on('bookeditems.bookingId', '=', 'bookings.id');
              })
        ->join('users', function ($join) {
                  $join->on('users.id', '=', 'bookings.userId');
              })
        ->orderBy('bookings.id', 'DESC')
        ->first();
       

      return View('orders.getOrderDetail')->with('order', $order);
  }
  public function generateInvoiceUser($transaction_id)
    {
        if(Booking::where('tranjectionid', $transaction_id)->where('userId', Auth::id())->count()<1 && Auth::user()->userType!='0')
        {
            return redirect()->back()->with('message', 'Error, algo salió mal. Inténtalo de nuevo.');
        }
        $getInfo = Booking::where('tranjectionid', $transaction_id)->first();
        $bookingDetail = Booking::with('getBookeditem')->find($getInfo->id);
        $data = [
            'booking' => $bookingDetail
        ];
        $pdf = PDF::loadView('invoice', $data);
        return $pdf->download($transaction_id.'.pdf');
    }
}
