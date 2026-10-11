<?php

namespace App\Mail;

use App\Models\Pedido;
use App\Models\ConfiguracionTienda;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PedidoRecibidoMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $pedido;
    public $config;

    /**
     * Create a new message instance.
     */
    public function __construct(Pedido $pedido, ?ConfiguracionTienda $config = null)
    {
        $this->pedido = $pedido;
        $this->config = $config ?: ConfiguracionTienda::first();
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('¡Hemos recibido tu Pedido #' . $this->pedido->id . '! - Agrosuministros Rodríguez')
                    ->view('emails.pedido_recibido');
    }
}
