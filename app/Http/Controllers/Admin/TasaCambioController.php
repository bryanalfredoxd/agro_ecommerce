<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TasaCambioadmin;
use App\Models\ConfiguracionApi;
use App\Models\AuditoriaLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TasaCambioController extends Controller
{
    public function index(Request $request)
    {
        // 1. Obtener la tasa actual (La última insertada)
        $tasaActual = TasaCambioadmin::latest('creado_at')->first();
        
        // 2. Obtener la API activa actual
        $apiActiva = ConfiguracionApi::where('activo', 1)->first();

        // 3. Consulta para la tabla histórica (AJAX)
        $query = TasaCambioadmin::with('editor')->withCount('pedidos');

        if ($request->filled('fuente') && $request->fuente !== 'all') {
            $query->where('fuente', $request->fuente);
        }

        $tasas = $query->orderBy('creado_at', 'desc')->paginate(10);

        if ($request->ajax()) {
            return view('admin.tasas-cambio.partials._table', compact('tasas'))->render();
        }

        return view('admin.tasas-cambio.index', compact('tasaActual', 'apiActiva', 'tasas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'valor_tasa' => 'required|numeric|min:1'
        ]);

        DB::beginTransaction();
        try {
            // Guardar la tasa previa para el Log
            $tasaAnterior = TasaCambioadmin::latest('creado_at')->first();

            // 1. Crear la nueva tasa MANUAL
            $nuevaTasa = TasaCambioadmin::create([
                'codigo_moneda' => 'USD',
                'moneda_base' => 'VES',
                'valor_tasa' => $request->valor_tasa,
                'fuente' => 'MANUAL',
                'usuario_editor_id' => Auth::id(),
                'creado_at' => now()
            ]);

            // 2. Registrar en Auditoría (Seguridad estricta)
            AuditoriaLog::create([
                'usuario_id' => Auth::id(),
                'evento' => 'MODIFICACION_TASA_MANUAL',
                'tabla_afectada' => 'tasas_cambio',
                'registro_id' => $nuevaTasa->id,
                'valores_anteriores' => $tasaAnterior ? json_encode(['valor_tasa' => $tasaAnterior->valor_tasa]) : null,
                'valores_nuevos' => json_encode(['valor_tasa' => $request->valor_tasa]),
                'direccion_ip' => $request->ip(),
                'fecha' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Tasa de cambio actualizada correctamente a Bs ' . number_format($request->valor_tasa, 2),
                'nueva_tasa' => number_format($request->valor_tasa, 2)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Ocurrió un error al guardar la tasa.'], 500);
        }
    }
}