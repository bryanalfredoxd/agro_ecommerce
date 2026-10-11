<?php

namespace App\Mail;

use App\Models\Cotizacion;
use App\Models\ConfiguracionTienda;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CotizacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $cotizacion;
    public $config;
    protected $pdfBytes;

    /**
     * Create a new message instance.
     */
    public function __construct(Cotizacion $cotizacion, ?ConfiguracionTienda $config = null, ?string $pdfBytes = null)
    {
        $this->cotizacion = $cotizacion;
        $this->config = $config ?: ConfiguracionTienda::first();
        $this->pdfBytes = $pdfBytes;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $numero = $this->cotizacion->numero_cotizacion;

        // Generar el PDF si no se pasó pregenerado
        $pdfContent = $this->pdfBytes;
        if (!$pdfContent) {
            $pdf = Pdf::loadView('cotizaciones.imprimir_pdf', [
                'cotizacion' => $this->cotizacion,
                'config' => $this->config
            ])->setPaper('a4', 'portrait');
            $pdfContent = $pdf->output();
        }

        $mail = $this->subject("Cotización Oficial #{$numero} - Agrosuministros Rodríguez")
                     ->view('emails.cotizacion');

        if ($pdfContent) {
            $mail->attachData($pdfContent, "Cotizacion-{$numero}.pdf", [
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }
}
