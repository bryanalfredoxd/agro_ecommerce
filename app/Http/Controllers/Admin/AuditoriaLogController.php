<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditoriaLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditoriaLogController extends Controller
{
    /**
     * Pantalla principal de Logs de Auditoría y Trazabilidad del Sistema.
     */
    public function index(Request $request)
    {
        $query = AuditoriaLog::with(['usuario.rol']);

        // 1. Buscador en vivo (Descripción, evento, tabla, IP, ID registro o usuario)
        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);
            $query->where(function ($q) use ($buscar) {
                $q->where('descripcion', 'LIKE', "%{$buscar}%")
                  ->orWhere('evento', 'LIKE', "%{$buscar}%")
                  ->orWhere('tabla_afectada', 'LIKE', "%{$buscar}%")
                  ->orWhere('registro_id', 'LIKE', "%{$buscar}%")
                  ->orWhere('direccion_ip', 'LIKE', "%{$buscar}%")
                  ->orWhereHas('usuario', function ($qu) use ($buscar) {
                      $qu->where('nombre', 'LIKE', "%{$buscar}%")
                         ->orWhere('apellido', 'LIKE', "%{$buscar}%")
                         ->orWhere('email', 'LIKE', "%{$buscar}%");
                  });
            });
        }

        // 2. Filtro por Módulo / Tabla afectada
        if ($request->filled('modulo') && $request->modulo !== 'todos') {
            $query->where('tabla_afectada', $request->modulo);
        }

        // 3. Filtro por Tipo de Evento / Acción
        if ($request->filled('evento') && $request->evento !== 'todos') {
            $evento = $request->evento;
            if ($evento === 'CREAR') {
                $query->where('evento', 'LIKE', 'CREAR%');
            } elseif ($evento === 'ACTUALIZAR') {
                $query->where('evento', 'LIKE', 'ACTUALIZAR%');
            } elseif ($evento === 'ELIMINAR') {
                $query->where('evento', 'LIKE', 'ELIMINAR%');
            } elseif ($evento === 'SEGURIDAD') {
                $query->where(function($q) {
                    $q->where('evento', 'LIKE', '%ROL%')
                      ->orWhere('evento', 'LIKE', '%PERMISO%')
                      ->orWhere('evento', 'LIKE', '%AUTH%')
                      ->orWhere('evento', 'LIKE', '%LOGIN%');
                });
            } elseif ($evento === 'ESTADO') {
                $query->where('evento', 'LIKE', '%ESTADO%');
            } else {
                $query->where('evento', $evento);
            }
        }

        // 4. Filtro por Usuario responsable
        if ($request->filled('usuario_id') && $request->usuario_id !== 'todos') {
            $query->where('usuario_id', $request->usuario_id);
        }

        // 5. Filtro por Rango de Fechas
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        // Paginación de 15 registros por página para navegación ágil y vista limpia
        $logs = $query->orderBy('fecha', 'desc')->paginate(15)->withQueryString();

        // Si es AJAX (búsqueda en vivo o paginación), retornar parcial
        if ($request->ajax()) {
            return view('admin.logs.partials._table', compact('logs'))->render();
        }

        // Métricas y KPIs de Auditoría
        $hoy = Carbon::today();
        $totalLogs = AuditoriaLog::count();
        $logsHoy = AuditoriaLog::whereDate('fecha', $hoy)->count();
        $usuariosAuditadosHoy = AuditoriaLog::whereDate('fecha', $hoy)
            ->whereNotNull('usuario_id')
            ->distinct('usuario_id')
            ->count('usuario_id');
        $accionesCriticas = AuditoriaLog::where(function($q) {
            $q->where('evento', 'LIKE', 'ELIMIN%')
              ->orWhere('evento', 'LIKE', '%ANULA%')
              ->orWhere('evento', 'LIKE', '%RECHAZ%')
              ->orWhere('evento', 'LIKE', '%ROL%')
              ->orWhere('evento', 'LIKE', '%PERMISO%');
        })->count();

        // Opciones únicas para selectores de filtrado
        $modulosDisponibles = AuditoriaLog::select('tabla_afectada')
            ->whereNotNull('tabla_afectada')
            ->distinct()
            ->orderBy('tabla_afectada')
            ->pluck('tabla_afectada');

        $usuariosAuditados = User::whereIn('id', function($q) {
            $q->select('usuario_id')->from('auditoria_logs')->whereNotNull('usuario_id');
        })->get();

        return view('admin.logs.index', compact(
            'logs',
            'totalLogs',
            'logsHoy',
            'usuariosAuditadosHoy',
            'accionesCriticas',
            'modulosDisponibles',
            'usuariosAuditados'
        ));
    }

    /**
     * Muestra el detalle forense completo de un registro de auditoría (Modal / Inspector).
     */
    public function show($id)
    {
        $log = AuditoriaLog::with(['usuario.rol'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'id' => $log->id,
            'evento' => $log->evento,
            'modulo' => $log->modulo_nombre,
            'tabla' => $log->tabla_afectada,
            'registro_id' => $log->registro_id,
            'descripcion' => $log->descripcion,
            'ip' => $log->direccion_ip,
            'url' => $log->url,
            'metodo_http' => $log->metodo_http,
            'user_agent' => $log->user_agent,
            'fecha' => $log->fecha ? $log->fecha->format('d/m/Y, h:i:s A') : 'N/A',
            'fecha_relativa' => $log->fecha ? $log->fecha->diffForHumans() : '',
            'usuario' => $log->usuario ? [
                'id' => $log->usuario->id,
                'nombre' => $log->usuario->nombre . ' ' . $log->usuario->apellido,
                'email' => $log->usuario->email,
                'rol' => $log->usuario->rol->nombre ?? 'Sin rol'
            ] : null,
            'valores_anteriores' => $log->valores_anteriores_array,
            'valores_nuevos' => $log->valores_nuevos_array,
            'badge_color' => $log->badge_color,
            'icono' => $log->icono
        ]);
    }

    /**
     * Exporta los logs filtrados en formato CSV (Compatible con Excel).
     */
    public function exportar(Request $request)
    {
        $query = AuditoriaLog::with(['usuario.rol']);

        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);
            $query->where(function ($q) use ($buscar) {
                $q->where('descripcion', 'LIKE', "%{$buscar}%")
                  ->orWhere('evento', 'LIKE', "%{$buscar}%")
                  ->orWhere('tabla_afectada', 'LIKE', "%{$buscar}%");
            });
        }
        if ($request->filled('modulo') && $request->modulo !== 'todos') {
            $query->where('tabla_afectada', $request->modulo);
        }
        if ($request->filled('usuario_id') && $request->usuario_id !== 'todos') {
            $query->where('usuario_id', $request->usuario_id);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        $logs = $query->orderBy('fecha', 'desc')->limit(5000)->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="auditoria_sistema_' . date('Y-m-d_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return new StreamedResponse(function () use ($logs) {
            $handle = fopen('php://output', 'w');
            // Escribir BOM UTF-8 para que Excel lo abra con acentos correctos
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'ID',
                'Fecha y Hora',
                'Evento',
                'Módulo / Tabla',
                'Registro ID',
                'Descripción',
                'Usuario Responsable',
                'Email',
                'Rol',
                'Dirección IP',
                'Método HTTP',
                'URL'
            ]);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->id,
                    $log->fecha ? $log->fecha->format('Y-m-d H:i:s') : '',
                    $log->evento,
                    $log->modulo_nombre . " ({$log->tabla_afectada})",
                    $log->registro_id ?? 'N/A',
                    $log->descripcion,
                    $log->usuario ? ($log->usuario->nombre . ' ' . $log->usuario->apellido) : 'Sistema / Anónimo',
                    $log->usuario ? $log->usuario->email : 'N/A',
                    $log->usuario ? ($log->usuario->rol->nombre ?? 'N/A') : 'N/A',
                    $log->direccion_ip,
                    $log->metodo_http,
                    $log->url
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
