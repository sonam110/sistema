<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\booking;
use PDF;

class SaleOrder extends Mailable
{
    use Queueable, SerializesModels;
    public $saleOrder;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($saleOrder)
    {
        $this->saleOrder = $saleOrder;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.saleOrder')
                    ->with('saleOrder', $this->saleOrder)
                    ->attachData($this->saleOrderGenerate($this->saleOrder['id']), $this->saleOrder['tranjectionid'].'.pdf');
    }

    public function saleOrderGenerate($id)
    {
        $booking = booking::find($id);
        $data = [
                'booking' => $booking
            ];
        $pdf = PDF::loadView('sales.sales-order-download', $data);
        return $pdf->output();
    }
}
