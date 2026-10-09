<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SesionCaja;
use App\Models\CajaFisica;
use App\Models\User;
use App\Models\Pedido;
use App\Models\Pago;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ConfiguracionTienda;
use App\Models\TasaCambio;
use Carbon\Carbon;

class CajaDiariaController extends Controller
{
    public function index(Request $request)
    {
        $query = SesionCaja::with(['caja', 'cajero', 'pedidos']);

        // 1. Filtros de auditoría
        if ($request->filled('filtro_estado') && $request->filtro_estado !== 'todas') {
            if ($request->filtro_estado === 'abierta') {
                $query->whereNull('fecha_cierre');
            } else {
                $query->whereNotNull('fecha_cierre');
            }
        }

        if ($request->filled('caja_id')) {
            $query->where('caja_id', $request->caja_id);
        }

        if ($request->filled('cajero_id')) {
            $query->where('cajero_usuario_id', $request->cajero_id);
        }

        if ($request->filled('fecha')) {
            $query->whereDate('fecha_apertura', $request->fecha);
        }

        if ($request->filled('buscar')) {
            $term = trim($request->buscar);
            $query->where(function($q) use ($term) {
                $q->whereHas('cajero', function($sq) use ($term) {
                    $sq->where('nombre', 'LIKE', "%{$term}%")
                       ->orWhere('apellido', 'LIKE', "%{$term}%")
                       ->orWhere('documento_identidad', 'LIKE', "%{$term}%");
                })->orWhereHas('caja', function($sq) use ($term) {
                    $sq->where('nombre', 'LIKE', "%{$term}%");
                })->orWhere('id', $term);
            });
        }

        $sesiones = $query->orderBy('fecha_apertura', 'desc')->paginate(12);

        // 2. KPIs de Control en Tiempo Real
        $hoy = Carbon::today();
        $sesionesHoy = SesionCaja::whereDate('fecha_apertura', $hoy)->get();
        $kpi_ventas_hoy_usd = (float) $sesionesHoy->sum('total_ventas_sistema_usd');
        $kpi_ventas_hoy_ves = (float) $sesionesHoy->sum('total_ventas_sistema_ves');
        $kpi_turnos_activos = SesionCaja::whereNull('fecha_cierre')->count();
        $kpi_cajas_activas = CajaFisica::where('activa', 1)->count();
        $kpi_total_cajas = CajaFisica::count();
        $kpi_descuadres_hoy = $sesionesHoy->whereNotNull('fecha_cierre')->filter(function($s) {
            return abs((float)$s->diferencia_usd) >= 0.01;
        })->count();

        // 3. Catálogos para filtros y administración
        $cajasFisicas = CajaFisica::with(['cajeroAsignado', 'sesionActiva.cajero'])->orderBy('id')->get();
        $cajeros = User::where('rol_id', '!=', 4)->orderBy('nombre')->get();

        if ($request->ajax()) {
            if ($request->get('tab') === 'cajas') {
                return view('admin.caja_diaria.partials._cajas_table', compact('cajasFisicas', 'cajeros'))->render();
            }
            return view('admin.caja_diaria.partials._table', compact('sesiones'))->render();
        }

        return view('admin.caja_diaria.index', compact(
            'sesiones', 
            'cajasFisicas', 
            'cajeros', 
            'kpi_ventas_hoy_usd', 
            'kpi_ventas_hoy_ves', 
            'kpi_turnos_activos', 
            'kpi_cajas_activas', 
            'kpi_total_cajas', 
            'kpi_descuadres_hoy'
        ));
    }

    /**
     * Muestra la página completa de auditoría detallada de un turno.
     */
    public function show($id)
    {
        $datos = $this->obtenerDatosTurno($id);
        return view('admin.caja_diaria.show', $datos);
    }

    /**
     * Genera el PDF oficial de cierre de caja (Reporte Z) estructurado y listo para imprimir o descargar.
     */
    public function generarPdfCierre($id)
    {
        $datos = $this->obtenerDatosTurno($id);
        
        $pdf = Pdf::loadView('admin.caja_diaria.pdf_cierre', $datos)
                  ->setPaper('letter', 'portrait');

        $nombreArchivo = "cierre_caja_turno_" . str_pad($datos['sesion']->id, 6, '0', STR_PAD_LEFT) . ".pdf";
        return $pdf->stream($nombreArchivo);
    }

    /**
     * Endpoint para consultar el desglose de turno (utilizado para modal o vista rápida).
     */
    public function detalleTurno($id)
    {
        $datos = $this->obtenerDatosTurno($id);
        return view('admin.caja_diaria.partials._detalle_turno', $datos)->render();
    }

    public function movimientos($id)
    {
        return $this->show($id);
    }

    /**
     * Extrae y procesa los datos financieros exhaustivos del turno.
     */
    private function obtenerDatosTurno($id): array
    {
        $sesion = SesionCaja::with(['caja', 'cajero', 'movimientos'])->findOrFail($id);
        
        $pedidos = Pedido::with(['usuario', 'pagos', 'factura', 'detalles.producto'])
                         ->where('sesion_caja_id', $sesion->id)
                         ->latest()
                         ->get();

        $pedidosIds = $pedidos->pluck('id');
        $pagos = Pago::whereIn('pedido_id', $pedidosIds)->where('estado', 'aprobado')->get();

        $nombresMetodos = [
            'efectivo_usd' => 'Efectivo USD ($)',
            'efectivo_bs'  => 'Efectivo Bolívares (VES)',
            'pago_movil'   => 'Pago Móvil (VES)',
            'punto_venta'  => 'Punto de Venta / Tarjeta',
            'zelle'        => 'Zelle ($)',
            'binance'      => 'Binance Pay (USDT)',
            'transferencia'=> 'Transferencia Bancaria',
            'biopago'      => 'Biopago'
        ];

        $desgloseMetodos = [];
        $totalEfectivoUsd = 0;
        $totalEfectivoVes = 0;
        $totalRecaudadoUsd = 0;
        $totalRecaudadoVes = 0;

        foreach ($pagos->groupBy('metodo') as $metodo => $grupo) {
            $sumUsd = (float) $grupo->sum('monto_usd');
            $sumVes = (float) $grupo->sum('monto_ves');
            $totalRecaudadoUsd += $sumUsd;
            $totalRecaudadoVes += $sumVes;

            if ($metodo === 'efectivo_usd') {
                $totalEfectivoUsd += $sumUsd;
            } elseif ($metodo === 'efectivo_bs') {
                $totalEfectivoVes += $sumVes;
            }

            $desgloseMetodos[] = [
                'metodo_key' => $metodo,
                'metodo' => $nombresMetodos[$metodo] ?? strtoupper(str_replace('_', ' ', $metodo)),
                'cantidad' => $grupo->count(),
                'total_usd' => $sumUsd,
                'total_ves' => $sumVes,
                'porcentaje' => 0 // Se calcula abajo
            ];
        }

        // Calcular porcentajes de participación por método
        if ($totalRecaudadoUsd > 0) {
            foreach ($desgloseMetodos as &$d) {
                $d['porcentaje'] = round(($d['total_usd'] / $totalRecaudadoUsd) * 100, 1);
            }
        }

        // Estadísticas complementarias
        $totalProductosVendidos = 0;
        foreach ($pedidos as $p) {
            if ($p->detalles) {
                $totalProductosVendidos += (float) $p->detalles->sum('cantidad');
            }
        }

        $clientesUnicos = $pedidos->pluck('usuario_id')->filter()->unique()->count();
        if ($clientesUnicos === 0 && $pedidos->count() > 0) {
            $clientesUnicos = $pedidos->count();
        }

        $ticketPromedioUsd = $pedidos->count() > 0 ? ($totalRecaudadoUsd / $pedidos->count()) : 0;
        $esperadoEfectivoUsd = (float) $sesion->monto_inicial_usd + $totalEfectivoUsd;
        $diferenciaUsd = $sesion->fecha_cierre !== null ? (float) $sesion->diferencia_usd : 0;

        $config = ConfiguracionTienda::first();
        $tasaCambio = TasaCambio::latest('creado_at')->first();

        return compact(
            'sesion',
            'pedidos',
            'pagos',
            'desgloseMetodos',
            'totalEfectivoUsd',
            'totalEfectivoVes',
            'totalRecaudadoUsd',
            'totalRecaudadoVes',
            'totalProductosVendidos',
            'clientesUnicos',
            'ticketPromedioUsd',
            'esperadoEfectivoUsd',
            'diferenciaUsd',
            'config',
            'tasaCambio'
        );
    }

    // ==========================================
    // GESTIÓN DE CAJAS FÍSICAS (ADMIN)
    // ==========================================
    public function storeCaja(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:cajas_fisicas,nombre',
            'cajero_asignado_id' => 'nullable|exists:usuarios,id',
        ], [
            'nombre.unique' => 'Ya existe una caja registrada con este nombre.'
        ]);

        $caja = CajaFisica::create([
            'nombre' => trim($request->nombre),
            'cajero_asignado_id' => $request->filled('cajero_asignado_id') ? $request->cajero_asignado_id : null,
            'activa' => 1
        ]);

        return response()->json([
            'success' => true,
            'message' => "Caja '{$caja->nombre}' creada exitosamente."
        ]);
    }

    public function updateCaja(Request $request, $id)
    {
        $caja = CajaFisica::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:50|unique:cajas_fisicas,nombre,' . $id,
            'cajero_asignado_id' => 'nullable|exists:usuarios,id',
        ], [
            'nombre.unique' => 'Ya existe otra caja con este nombre.'
        ]);

        $caja->update([
            'nombre' => trim($request->nombre),
            'cajero_asignado_id' => $request->filled('cajero_asignado_id') ? $request->cajero_asignado_id : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Caja '{$caja->nombre}' actualizada correctamente."
        ]);
    }

    public function toggleCaja($id)
    {
        $caja = CajaFisica::findOrFail($id);

        if ($caja->activa && $caja->sesionActiva) {
            return response()->json([
                'success' => false,
                'message' => "No puedes desactivar '{$caja->nombre}' porque tiene un turno abierto en este momento."
            ], 422);
        }

        $caja->activa = !$caja->activa;
        $caja->save();

        $txt = $caja->activa ? 'habilitada para operar' : 'desactivada';
        return response()->json([
            'success' => true,
            'message' => "Caja '{$caja->nombre}' $txt."
        ]);
    }

    public function destroyCaja($id)
    {
        $caja = CajaFisica::findOrFail($id);

        if ($caja->sesiones()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => "No se puede eliminar '{$caja->nombre}' porque tiene historial de turnos. Puedes desactivarla para que no se use."
            ], 422);
        }

        $caja->delete();
        return response()->json([
            'success' => true,
            'message' => "Caja eliminada exitosamente."
        ]);
    }
}