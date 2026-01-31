<?php

namespace App\Http\Controllers;

use App\Models\TasaCambio;
use Illuminate\Http\Request;

class TasaCambioController extends Controller
{
    // Solo necesitamos un método para obtener la tasa actual
    public function obtenerTasaActual()
    {
        $tasa = TasaCambio::obtenerTasaUSD();
        
        if (!$tasa) {
            // Si no hay tasa en la BD, retornar un valor por defecto
            return response()->json([
                'valor_tasa' => '0.00',
                'codigo_moneda' => 'USD',
                'moneda_base' => 'VES'
            ]);
        }
        
        return response()->json([
            'valor_tasa' => number_format($tasa->valor_tasa, 2),
            'codigo_moneda' => $tasa->codigo_moneda,
            'moneda_base' => $tasa->moneda_base
        ]);
    }
}