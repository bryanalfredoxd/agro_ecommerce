<?php

namespace App\Mail;

use App\Models\Pedido;
use App\Models\Factura;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PedidoAprobadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pedido;
    public $factura;

    /**
     * Create a new message instance.
     */
    public function __construct(Pedido $pedido, ?Factura $factura = null)
    {
        $this->pedido = $pedido;
        $this->factura = $factura ?: Factura::where('pedido_id', $pedido->id)->first();
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('¡Pago Aprobado y Pedido Confirmado! - Orden #' . $this->pedido->id . ' - Agrosuministros Rodríguez')
                    ->view('emails.pedido_aprobado');
    }
}
