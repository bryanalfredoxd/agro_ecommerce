<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class UpdateExchangeRateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url = "https://www.bcv.org.ve/estadisticas/tipo-de-cambio-de-referencia";

    public function __construct()
    {
        //
    }

    public function handle()
    {
        try {
            $html = $this->fetchBCVData();
            $valor = $this->extraerTasa($html);

            if (!$valor) {
                throw new Exception("No se pudo extraer el valor del BCV.");
            }

            // Guardar en la base de datos según tu estructura
            DB::table('tasas_cambio')->insert([
                'codigo_moneda'     => 'USD',
                'moneda_base'      => 'VES',
                'valor_tasa'       => $valor,
                'fuente'           => 'API',
                'usuario_editor_id' => null, // O un ID de sistema si lo tienes
                'creado_at'        => now(),
            ]);

            Log::info("Tasa BCV actualizada exitosamente: {$valor}");

        } catch (Exception $e) {
            Log::error("Error en UpdateExchangeRateJob: " . $e->getMessage());
        }
    }

    private function fetchBCVData()
    {
        // Usamos el cliente HTTP de Laravel con opciones de seguridad relajadas para el BCV
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Accept'     => 'text/html',
        ])
        ->withoutVerifying() // Equivalente a CURLOPT_SSL_VERIFYPEER => false
        ->get($this->url);

        if ($response->failed()) {
            throw new Exception("Error al conectar con el BCV: " . $response->status());
        }

        return $response->body();
    }

    private function extraerTasa($html)
    {
        // Mantengo tu lógica de Regex que es efectiva
        $pattern = '/<div\s+id="dolar"[^>]*>.*?<div[^>]*>\s*<strong>\s*([\d,]+)\s*<\/strong>/s';
        
        if (preg_match($pattern, $html, $matches)) {
            $valor = str_replace(',', '.', trim($matches[1]));
            return (float) $valor;
        }

        // Fallback simple por si falla el anterior
        if (preg_match('/USD.*?<strong>\s*([\d,]+)\s*<\/strong>/s', $html, $matches)) {
            $valor = str_replace(',', '.', trim($matches[1]));
            return (float) $valor;
        }

        return null;
    }
}