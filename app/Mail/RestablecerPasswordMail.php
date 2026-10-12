<?php

namespace App\Mail;

use App\Models\User;
use App\Models\ConfiguracionTienda;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RestablecerPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $resetUrl;
    public $config;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $resetUrl, ?ConfiguracionTienda $config = null)
    {
        $this->user = $user;
        $this->resetUrl = $resetUrl;
        $this->config = $config ?: ConfiguracionTienda::first();
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $nombreTienda = $this->config->nombre_comercio ?? $this->config->nombre_empresa ?? 'Agrosuministros Rodríguez';

        return $this->subject('Recuperación de Contraseña - ' . $nombreTienda)
                    ->view('emails.restablecer_password');
    }
}
