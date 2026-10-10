<?php

namespace App\Mail;

use App\Models\Factura;
use App\Models\ConfiguracionTienda;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FacturaTicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public $factura;
    public $config;

    /**
     * Create a new message instance.
     */
    public function __construct(Factura $factura, ?ConfiguracionTienda $config = null)
    {
        $this->factura = $factura;
        $this->config = $config ?: ConfiguracionTienda::first();
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Ticket Digital de Compra #' . $this->factura->numero_factura . ' - Agrosuministros Rodríguez')
                    ->view('emails.factura_ticket');
    }
}
