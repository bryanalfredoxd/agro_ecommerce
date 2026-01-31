<?php

namespace App\Http\Controllers;

use App\Models\HorarioSemanal;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    // Obtener horario de hoy
    public function obtenerHorarioHoy()
    {
        $horarioHoy = HorarioSemanal::obtenerHorarioHoy();
        
        if (!$horarioHoy) {
            return response()->json([
                'abierto' => false,
                'mensaje' => 'Cerrado hoy',
                'horario' => null
            ]);
        }
        
        return response()->json([
            'abierto' => $horarioHoy->es_laborable,
            'mensaje' => $horarioHoy->es_laborable ? 'Abierto' : 'Cerrado hoy',
            'horario' => [
                'apertura' => $horarioHoy->hora_apertura,
                'cierre' => $horarioHoy->hora_cierre,
                'formateado' => HorarioSemanal::obtenerHorarioHoyFormateado()
            ]
        ]);
    }
    
    // Verificar si está abierto ahora
    public function estaAbiertoAhora()
    {
        return response()->json([
            'abierto' => HorarioSemanal::estaAbiertoAhora(),
            'horario_hoy' => HorarioSemanal::obtenerHorarioHoyFormateado()
        ]);
    }
}