<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TasaCambio;
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
        $tasaActual = TasaCambio::latest('creado_at')->first();
        
        // 2. Obtener la API activa actual
        $apiActiva = ConfiguracionApi::where('activo', 1)->first();

        // 3. Consulta para la tabla histórica (AJAX)
        $query = TasaCambio::with('editor')->withCount('pedidos');

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
            $tasaAnterior = TasaCambio::latest('creado_at')->first();

            // 1. Crear la nueva tasa MANUAL
            $nuevaTasa = TasaCambio::create([
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
                'nueva_tasa' => number_format($request->valor_tasa, 2),
                'fecha_actualizacion' => now()->format('d M Y, h:i A')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Ocurrió un error al guardar la tasa: ' . $e->getMessage()], 500);
        }
    }

    public function sincronizarBCV(Request $request)
    {
        try {
            $valorTasa = $this->obtenerTasaDolarBCV();

            if ($valorTasa === null || $valorTasa <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo obtener la tasa desde el BCV ni sus servidores de respaldo en este momento. Inténtelo más tarde o asigne una tasa manual.'
                ], 502);
            }

            DB::beginTransaction();

            $tasaAnterior = TasaCambio::latest('creado_at')->first();

            // 1. Guardar nueva tasa API
            $nuevaTasa = TasaCambio::create([
                'codigo_moneda' => 'USD',
                'moneda_base' => 'VES',
                'valor_tasa' => $valorTasa,
                'fuente' => 'API',
                'usuario_editor_id' => Auth::id(),
                'creado_at' => now()
            ]);

            // 2. Registrar en Auditoría
            AuditoriaLog::create([
                'usuario_id' => Auth::id(),
                'evento' => 'SINCRONIZACION_TASA_BCV',
                'tabla_afectada' => 'tasas_cambio',
                'registro_id' => $nuevaTasa->id,
                'valores_anteriores' => $tasaAnterior ? json_encode(['valor_tasa' => $tasaAnterior->valor_tasa, 'fuente' => $tasaAnterior->fuente]) : null,
                'valores_nuevos' => json_encode(['valor_tasa' => $valorTasa, 'fuente' => 'API']),
                'direccion_ip' => $request->ip(),
                'fecha' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tasa del BCV sincronizada con éxito: Bs ' . number_format($valorTasa, 2),
                'nueva_tasa' => number_format($valorTasa, 2),
                'valor_exacto' => $valorTasa,
                'fecha_actualizacion' => now()->format('d M Y, h:i A')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al sincronizar la tasa: ' . $e->getMessage()
            ], 500);
        }
    }

    private function obtenerTasaDolarBCV()
    {
        // 1. Intentar scraping oficial BCV
        $url = "https://www.bcv.org.ve/estadisticas/tipo-de-cambio-de-referencia";
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36',
        ]);
        
        $respuesta = curl_exec($curl);
        curl_close($curl);

        if (!empty($respuesta)) {
            $pattern = '/<div\s+id="dolar"[^>]*>.*?<strong[^>]*>\s*([\d,.]+)\s*<\/strong>/is';
            if (preg_match($pattern, $respuesta, $matches)) {
                $val = (float) str_replace(',', '.', trim($matches[1]));
                if ($val > 0) return $val;
            }

            $pattern2 = '/USD.*?<strong[^>]*>\s*([\d,.]+)\s*<\/strong>/is';
            if (preg_match($pattern2, $respuesta, $matches)) {
                $val = (float) str_replace(',', '.', trim($matches[1]));
                if ($val > 0) return $val;
            }
        }

        // 2. Fallback de alta disponibilidad (DolarApi oficial BCV)
        try {
            $apiUrl = "https://ve.dolarapi.com/v1/dolares/oficial";
            $curlApi = curl_init();
            curl_setopt_array($curlApi, [
                CURLOPT_URL => $apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => 'AgroEcommerce/1.0',
            ]);
            $respApi = curl_exec($curlApi);
            curl_close($curlApi);

            if (!empty($respApi)) {
                $json = json_decode($respApi, true);
                if (isset($json['promedio']) && is_numeric($json['promedio']) && $json['promedio'] > 0) {
                    return (float) $json['promedio'];
                }
            }
        } catch (\Throwable $e) {
            // Silencioso
        }

        return null;
    }
}