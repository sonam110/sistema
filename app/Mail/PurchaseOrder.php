<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\PurchaseOrder as PurchaseOrderModel;
use PDF;

class PurchaseOrder extends Mailable
{
    use Queueable, SerializesModels;
    public $purchaseOrder;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.purchaseOrder')
                    ->with('purchaseOrder', $this->purchaseOrder)
                    ->attachData($this->purchaseOrderGenerate($this->purchaseOrder['id']), $this->purchaseOrder['po_no'].'.pdf');
    }

    public function purchaseOrderGenerate($po_id)
    {
        $poInfo = PurchaseOrderModel::find($po_id);
        $data = [
                'poInfo' => $poInfo
            ];
        $pdf = PDF::loadView('purchases.purchase-order-download', $data);
        return $pdf->output();
    }
}
