<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HorarioSemanal;
use Illuminate\Http\Request;

class HorarioadminController extends Controller
{
    public function index()
    {
        // 1. Verificamos que existan los 7 días en la BD. Si no, los creamos por defecto.
        for ($i = 1; $i <= 7; $i++) {
            HorarioSemanal::firstOrCreate(
                ['dia_semana' => $i],
                [
                    'es_laborable' => ($i <= 5), // Lunes a Viernes (1-5) son laborables por defecto
                    'hora_apertura' => '08:00:00',
                    'hora_cierre' => '17:00:00'
                ]
            );
        }

        // 2. Traemos los horarios ordenados de Lunes (1) a Domingo (7)
        $horarios = HorarioSemanal::orderBy('dia_semana', 'asc')->get();

        return view('admin.horarios.index', compact('horarios'));
    }

    public function updateAll(Request $request)
    {
        $datos = $request->input('horarios', []);

        // Recorremos siempre del 1 (Lunes) al 7 (Domingo), pase lo que pase
        for ($dia = 1; $dia <= 7; $dia++) {
            
            // Si el día existe en los datos y su checkbox fue marcado, es 1. Si no, es 0 (Cerrado).
            $es_laborable = isset($datos[$dia]['es_laborable']) ? 1 : 0;
            
            $updateData = [
                'es_laborable' => $es_laborable
            ];

            // Solo actualizamos las horas si fueron enviadas (es decir, si no estaban en disabled)
            if (isset($datos[$dia]['hora_apertura'])) {
                $updateData['hora_apertura'] = $datos[$dia]['hora_apertura'];
            }
            if (isset($datos[$dia]['hora_cierre'])) {
                $updateData['hora_cierre'] = $datos[$dia]['hora_cierre'];
            }

            HorarioSemanal::where('dia_semana', $dia)->update($updateData);
        }

        return response()->json([
            'success' => true, 
            'message' => 'Horario semanal actualizado correctamente.'
        ]);
    }
}