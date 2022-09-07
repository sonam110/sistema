<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\booking;
use App\user;
use PDF;

class SaleOrderFactura extends Mailable
{
    use Queueable, SerializesModels;
    public $pdf;
    public $saleFact;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pdf,$saleFact)
    {
        $this->pdf = $pdf;
        $this->saleFact = $saleFact;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.saleOrderFactura')
                    ->with('saleOrder', $this->saleFact)
                    ->attachData($this->pdf->output(), 'factura'.'.pdf');
    }
}
