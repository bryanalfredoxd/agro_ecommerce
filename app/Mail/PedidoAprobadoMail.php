<?php

namespace App\Mail;

use App\Models\Pedido;
use App\Models\Factura;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PedidoAprobadoMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $pedido;
    public $factura;
    public $config;

    /**
     * Create a new message instance.
     */
    public function __construct(Pedido $pedido, ?Factura $factura = null, ?\App\Models\ConfiguracionTienda $config = null)
    {
        $this->pedido = $pedido;
        $this->factura = $factura ?: Factura::where('pedido_id', $pedido->id)->first();
        $this->config = $config ?: \App\Models\ConfiguracionTienda::first();
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
