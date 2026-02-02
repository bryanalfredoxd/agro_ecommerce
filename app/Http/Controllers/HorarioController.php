<?php

namespace App\Http\Controllers;

use App\Models\HorarioSemanal;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    // Obtener estado completo del horario
    public function obtenerEstadoHorario()
    {
        $estado = HorarioSemanal::obtenerEstadoHorarioHoy();
        
        return response()->json([
            'estado' => $estado['estado'],
            'mensaje' => $estado['mensaje'],
            'horario_formateado' => $estado['formateado'],
            'abierto_ahora' => $estado['estado'] === 'abierto'
        ]);
    }
    
    // Verificar si está abierto ahora
    public function estaAbiertoAhora()
    {
        $estado = HorarioSemanal::obtenerEstadoHorarioHoy();
        
        return response()->json([
            'abierto' => $estado['estado'] === 'abierto',
            'estado' => $estado['estado'],
            'mensaje' => $estado['mensaje'],
            'horario_hoy' => $estado['formateado']
        ]);
    }
}