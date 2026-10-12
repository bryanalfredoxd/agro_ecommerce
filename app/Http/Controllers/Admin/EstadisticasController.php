<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Pago;
use App\Models\ZonaDelivery;
use App\Models\Producto;
use App\Models\TasaCambio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EstadisticasController extends Controller
{
    /**
     * Mapeo de métodos de pago a nombres amigables y colores
     */
    protected static array $METODOS_INFO = [
        'efectivo_usd' => ['nombre' => 'Efectivo Divisas ($)', 'color' => '#10b981', 'icono' => 'payments'],
        'pago_movil'   => ['nombre' => 'Pago Móvil (Bs)', 'color' => '#0284c7', 'icono' => 'smartphone'],
        'zelle'        => ['nombre' => 'Zelle ($)', 'color' => '#8b5cf6', 'icono' => 'bolt'],
        'cashea'       => ['nombre' => 'Cashea Financiado', 'color' => '#f59e0b', 'icono' => 'shopping_bag'],
        'punto_venta'  => ['nombre' => 'Punto de Venta / Débito', 'color' => '#ec4899', 'icono' => 'credit_card'],
        'binance'      => ['nombre' => 'Binance Pay', 'color' => '#eab308', 'icono' => 'currency_bitcoin'],
        'transferencia'=> ['nombre' => 'Transferencia Bancaria', 'color' => '#2563eb', 'icono' => 'account_balance'],
        'efectivo_bs'  => ['nombre' => 'Efectivo Bolívares (Bs)', 'color' => '#14b8a6', 'icono' => 'money'],
    ];

    /**
     * Pantalla Principal de Estadísticas y Analítica
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user && !$user->esAdmin() && 
            !$user->tienePermiso('ver_dashboard') && 
            !$user->tienePermiso('ver_kpis_financieros') && 
            !$user->tienePermiso('ver_pedidos')) {
            return redirect()->route('admin.dashboard')->with('error', 'No tienes permisos asignados para visualizar el módulo de estadísticas.');
        }

        $datos = $this->procesarMetricas($request);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($datos);
        }

        return view('admin.estadisticas.index', $datos);
    }

    /**
     * Endpoint API para refresco reactivo por AJAX
     */
    public function data(Request $request)
    {
        $datos = $this->procesarMetricas($request);
        return response()->json($datos);
    }

    /**
     * Lógica central de procesamiento estadístico
     */
    protected function procesarMetricas(Request $request): array
    {
        $periodo = $request->input('periodo', 'mes');
        $canal = $request->input('canal', 'todos');
        $fechaDesdeInput = $request->input('fecha_desde');
        $fechaHastaInput = $request->input('fecha_hasta');

        // 1. Tasa BCV Actual
        $tasaActiva = TasaCambio::latest('creado_at')->first();
        $tasaBcv = $tasaActiva ? (float)$tasaActiva->valor_tasa : 1.0;

        // 2. Determinar Rango de Fechas
        $hoy = Carbon::today();
        $fechaDesde = null;
        $fechaHasta = null;
        $etiquetaPeriodo = 'Todo el Histórico';

        switch ($periodo) {
            case 'hoy':
                $fechaDesde = Carbon::today()->startOfDay();
                $fechaHasta = Carbon::today()->endOfDay();
                $etiquetaPeriodo = 'Hoy (' . $hoy->format('d/m/Y') . ')';
                break;
            case 'semana':
                $fechaDesde = Carbon::now()->startOfWeek()->startOfDay();
                $fechaHasta = Carbon::now()->endOfWeek()->endOfDay();
                $etiquetaPeriodo = 'Esta Semana (' . $fechaDesde->format('d/m') . ' - ' . $fechaHasta->format('d/m/Y') . ')';
                break;
            case 'mes':
                $fechaDesde = Carbon::now()->startOfMonth()->startOfDay();
                $fechaHasta = Carbon::now()->endOfMonth()->endOfDay();
                $etiquetaPeriodo = 'Este Mes (' . Carbon::now()->isoFormat('MMMM YYYY') . ')';
                break;
            case 'mes_anterior':
                $fechaDesde = Carbon::now()->subMonth()->startOfMonth()->startOfDay();
                $fechaHasta = Carbon::now()->subMonth()->endOfMonth()->endOfDay();
                $etiquetaPeriodo = 'Mes Anterior (' . Carbon::now()->subMonth()->isoFormat('MMMM YYYY') . ')';
                break;
            case 'ultimos_30':
                $fechaDesde = Carbon::now()->subDays(30)->startOfDay();
                $fechaHasta = Carbon::now()->endOfDay();
                $etiquetaPeriodo = 'Últimos 30 Días';
                break;
            case 'anio':
                $fechaDesde = Carbon::now()->startOfYear()->startOfDay();
                $fechaHasta = Carbon::now()->endOfYear()->endOfDay();
                $etiquetaPeriodo = 'Año ' . Carbon::now()->year;
                break;
            case 'personalizado':
                if (!empty($fechaDesdeInput)) {
                    $fechaDesde = Carbon::parse($fechaDesdeInput)->startOfDay();
                }
                if (!empty($fechaHastaInput)) {
                    $fechaHasta = Carbon::parse($fechaHastaInput)->endOfDay();
                }
                $etiquetaPeriodo = ($fechaDesde ? $fechaDesde->format('d/m/Y') : 'Inicio') . ' hasta ' . ($fechaHasta ? $fechaHasta->format('d/m/Y') : 'Hoy');
                break;
            case 'historico':
            default:
                $periodo = 'historico';
                $fechaDesde = null;
                $fechaHasta = null;
                $etiquetaPeriodo = 'Histórico Completo';
                break;
        }

        // 3. Consulta Base de Pedidos Válidos (no cancelados/rechazados)
        $pedidosBaseQuery = Pedido::whereNotIn('estado', ['cancelado', 'rechazado']);
        if ($fechaDesde) $pedidosBaseQuery->where('creado_at', '>=', $fechaDesde);
        if ($fechaHasta) $pedidosBaseQuery->where('creado_at', '<=', $fechaHasta);
        if ($canal !== 'todos') $pedidosBaseQuery->where('canal_venta', $canal);

        // 4. KPIs Financieros Globales
        $totalPedidos = (clone $pedidosBaseQuery)->count();
        $totalIngresosUsd = (float)(clone $pedidosBaseQuery)->sum('total_usd');
        $totalIngresosVes = (float)(clone $pedidosBaseQuery)->sum('total_ves_calculado');
        $ticketPromedioUsd = $totalPedidos > 0 ? round($totalIngresosUsd / $totalPedidos, 2) : 0.00;
        $ticketPromedioVes = $totalPedidos > 0 ? round($totalIngresosVes / $totalPedidos, 2) : 0.00;

        // 4.1 Devoluciones y Reembolsos en el Período
        $devolucionesQuery = \App\Models\Devolucion::where('estado', 'procesada');
        if ($fechaDesde) $devolucionesQuery->where('fecha_devolucion', '>=', $fechaDesde);
        if ($fechaHasta) $devolucionesQuery->where('fecha_devolucion', '<=', $fechaHasta);
        if ($canal !== 'todos') {
            $devolucionesQuery->whereHas('pedido', function($qp) use ($canal) {
                $qp->where('canal_venta', $canal);
            });
        }
        $totalDevolucionesUsd = (float)(clone $devolucionesQuery)->sum('monto_total_usd');
        $totalDevolucionesVes = (float)(clone $devolucionesQuery)->sum('monto_total_ves');
        $cantidadDevoluciones = (int)(clone $devolucionesQuery)->count();
        $ingresosNetosUsd = max(0, $totalIngresosUsd - $totalDevolucionesUsd);
        $ingresosNetosVes = max(0, $totalIngresosVes - $totalDevolucionesVes);

        // 5. Unidades Totales Vendidas en el Período
        $unidadesQuery = DetallePedido::join('pedidos', 'pedido_detalles.pedido_id', '=', 'pedidos.id')
            ->whereNotIn('pedidos.estado', ['cancelado', 'rechazado']);
        if ($fechaDesde) $unidadesQuery->where('pedidos.creado_at', '>=', $fechaDesde);
        if ($fechaHasta) $unidadesQuery->where('pedidos.creado_at', '<=', $fechaHasta);
        if ($canal !== 'todos') $unidadesQuery->where('pedidos.canal_venta', $canal);
        $totalUnidadesVendidas = (float)$unidadesQuery->sum('pedido_detalles.cantidad_solicitada');

        // ==========================================
        // 6. TOP PRODUCTOS MÁS VENDIDOS
        // ==========================================
        $topProductosQuery = DetallePedido::join('pedidos', 'pedido_detalles.pedido_id', '=', 'pedidos.id')
            ->join('productos', 'pedido_detalles.producto_id', '=', 'productos.id')
            ->leftJoin('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->whereNotIn('pedidos.estado', ['cancelado', 'rechazado']);

        if ($fechaDesde) $topProductosQuery->where('pedidos.creado_at', '>=', $fechaDesde);
        if ($fechaHasta) $topProductosQuery->where('pedidos.creado_at', '<=', $fechaHasta);
        if ($canal !== 'todos') $topProductosQuery->where('pedidos.canal_venta', $canal);

        $topProductos = $topProductosQuery->selectRaw('
            productos.id,
            productos.nombre,
            productos.sku,
            productos.unidad_medida,
            productos.stock_total,
            productos.stock_minimo_alerta,
            productos.imagen_url,
            productos.precio_venta_usd,
            productos.precio_oferta_usd,
            categorias.nombre as categoria_nombre,
            SUM(pedido_detalles.cantidad_solicitada) as total_unidades,
            SUM(pedido_detalles.cantidad_solicitada * pedido_detalles.precio_historico_usd) as total_usd,
            COUNT(DISTINCT pedidos.id) as total_ordenes
        ')
        ->groupBy(
            'productos.id', 'productos.nombre', 'productos.sku', 'productos.unidad_medida',
            'productos.stock_total', 'productos.stock_minimo_alerta', 'productos.imagen_url',
            'productos.precio_venta_usd', 'productos.precio_oferta_usd', 'categorias.nombre'
        )
        ->orderByDesc('total_unidades')
        ->get();

        // Calcular porcentajes y campos formateados para los productos
        $topProductosFormatted = $topProductos->map(function ($p, $idx) use ($totalIngresosUsd, $tasaBcv) {
            $unidades = (float)$p->total_unidades;
            $usd = (float)$p->total_usd;
            $ves = $usd * $tasaBcv;
            $pctParticipacion = $totalIngresosUsd > 0 ? round(($usd / $totalIngresosUsd) * 100, 1) : 0;
            $stock = (float)$p->stock_total;
            $alerta = (float)($p->stock_minimo_alerta ?: 5);

            $imgUrl = null;
            if ($p->imagen_url) {
                $imgUrl = str_starts_with($p->imagen_url, 'http') 
                    ? $p->imagen_url 
                    : asset(ltrim($p->imagen_url, '/'));
            }

            return [
                'ranking' => $idx + 1,
                'id' => $p->id,
                'nombre' => $p->nombre,
                'sku' => $p->sku ?: 'S/SKU',
                'categoria' => $p->categoria_nombre ?: 'Sin Categoría',
                'foto' => $imgUrl,
                'unidad' => $p->unidad_medida ?: 'ud',
                'unidades_vendidas' => $unidades,
                'total_usd' => $usd,
                'total_ves' => $ves,
                'precio_promedio' => $unidades > 0 ? round($usd / $unidades, 2) : (float)$p->precio_venta_usd,
                'ordenes' => (int)$p->total_ordenes,
                'pct_participacion' => $pctParticipacion,
                'stock_actual' => $stock,
                'estado_stock' => $stock <= 0 ? 'agotado' : ($stock <= $alerta ? 'critico' : 'normal')
            ];
        });

        // Top 10 para Gráficos Chart.js
        $top10 = $topProductosFormatted->take(10);
        $chartTopProductos = [
            'labels' => $top10->pluck('nombre')->map(fn($n) => mb_substr($n, 0, 22) . (mb_strlen($n) > 22 ? '...' : ''))->toArray(),
            'unidades' => $top10->pluck('unidades_vendidas')->toArray(),
            'ingresos' => $top10->pluck('total_usd')->toArray(),
        ];

        // Producto Estrella
        $productoEstrella = $topProductosFormatted->first();

        // ==========================================
        // 7. MÉTODOS DE PAGO MÁS UTILIZADOS
        // ==========================================
        $pagosQuery = Pago::join('pedidos', 'pagos.pedido_id', '=', 'pedidos.id')
            ->where('pagos.estado', 'aprobado')
            ->whereNotIn('pedidos.estado', ['cancelado', 'rechazado']);

        if ($fechaDesde) $pagosQuery->where('pedidos.creado_at', '>=', $fechaDesde);
        if ($fechaHasta) $pagosQuery->where('pedidos.creado_at', '<=', $fechaHasta);
        if ($canal !== 'todos') $pagosQuery->where('pedidos.canal_venta', $canal);

        $metodosPagoRaw = $pagosQuery->selectRaw('
            pagos.metodo,
            COUNT(*) as total_transacciones,
            SUM(pagos.monto_usd) as total_usd,
            SUM(pagos.monto_ves) as total_ves
        ')
        ->groupBy('pagos.metodo')
        ->orderByDesc('total_transacciones')
        ->get();

        $totalPagosUsd = (float)$metodosPagoRaw->sum('total_usd');
        $totalTransaccionesPagos = (int)$metodosPagoRaw->sum('total_transacciones');

        $metodosPagoFormatted = $metodosPagoRaw->map(function ($m) use ($totalPagosUsd, $totalTransaccionesPagos) {
            $info = self::$METODOS_INFO[$m->metodo] ?? [
                'nombre' => ucfirst(str_replace('_', ' ', $m->metodo)),
                'color' => '#64748b',
                'icono' => 'credit_card'
            ];

            $usd = (float)$m->total_usd;
            $txs = (int)$m->total_transacciones;
            $pctMonto = $totalPagosUsd > 0 ? round(($usd / $totalPagosUsd) * 100, 1) : 0;
            $pctTxs = $totalTransaccionesPagos > 0 ? round(($txs / $totalTransaccionesPagos) * 100, 1) : 0;

            return [
                'clave' => $m->metodo,
                'nombre' => $info['nombre'],
                'color' => $info['color'],
                'icono' => $info['icono'],
                'transacciones' => $txs,
                'total_usd' => $usd,
                'total_ves' => (float)$m->total_ves,
                'pct_monto' => $pctMonto,
                'pct_transacciones' => $pctTxs
            ];
        });

        $chartMetodosPago = [
            'labels' => $metodosPagoFormatted->pluck('nombre')->toArray(),
            'data_usd' => $metodosPagoFormatted->pluck('total_usd')->toArray(),
            'data_txs' => $metodosPagoFormatted->pluck('transacciones')->toArray(),
            'colores' => $metodosPagoFormatted->pluck('color')->toArray()
        ];

        // ==========================================
        // 8. COMPARATIVA: WEB vs POS (TIENDA FÍSICA)
        // ==========================================
        $canalesQuery = Pedido::whereNotIn('estado', ['cancelado', 'rechazado']);
        if ($fechaDesde) $canalesQuery->where('creado_at', '>=', $fechaDesde);
        if ($fechaHasta) $canalesQuery->where('creado_at', '<=', $fechaHasta);

        $canalesRaw = $canalesQuery->selectRaw('
            canal_venta,
            COUNT(*) as total_pedidos,
            SUM(total_usd) as total_usd,
            SUM(total_ves_calculado) as total_ves,
            AVG(total_usd) as ticket_promedio_usd
        ')
        ->groupBy('canal_venta')
        ->get()
        ->keyBy('canal_venta');

        $webData = $canalesRaw->get('web');
        $posData = $canalesRaw->get('tienda_fisica');

        $webPedidos = $webData ? (int)$webData->total_pedidos : 0;
        $webUsd = $webData ? (float)$webData->total_usd : 0.00;
        $webVes = $webData ? (float)$webData->total_ves : 0.00;
        $webTicket = $webPedidos > 0 ? round($webUsd / $webPedidos, 2) : 0.00;

        $posPedidos = $posData ? (int)$posData->total_pedidos : 0;
        $posUsd = $posData ? (float)$posData->total_usd : 0.00;
        $posVes = $posData ? (float)$posData->total_ves : 0.00;
        $posTicket = $posPedidos > 0 ? round($posUsd / $posPedidos, 2) : 0.00;

        $sumaCanalesUsd = $webUsd + $posUsd;
        $webPct = $sumaCanalesUsd > 0 ? round(($webUsd / $sumaCanalesUsd) * 100, 1) : 0;
        $posPct = $sumaCanalesUsd > 0 ? round(($posUsd / $sumaCanalesUsd) * 100, 1) : 0;

        // Veredicto Comparativo
        $canalGanadorIngresos = ($posUsd >= $webUsd) ? 'POS (Tienda Física)' : 'Tienda Web (E-commerce)';
        $canalGanadorVolumen = ($posPedidos >= $webPedidos) ? 'POS (Tienda Física)' : 'Tienda Web (E-commerce)';
        
        $comparativaCanales = [
            'web' => [
                'pedidos' => $webPedidos,
                'total_usd' => $webUsd,
                'total_ves' => $webVes,
                'ticket_promedio' => $webTicket,
                'porcentaje' => $webPct
            ],
            'pos' => [
                'pedidos' => $posPedidos,
                'total_usd' => $posUsd,
                'total_ves' => $posVes,
                'ticket_promedio' => $posTicket,
                'porcentaje' => $posPct
            ],
            'canal_ganador_ingresos' => $canalGanadorIngresos,
            'canal_ganador_volumen' => $canalGanadorVolumen,
            'diferencia_usd' => abs($posUsd - $webUsd)
        ];

        // ==========================================
        // 9. LOGÍSTICA Y ZONAS DE DELIVERY
        // ==========================================
        $deliveryBaseQuery = Pedido::whereNotIn('estado', ['cancelado', 'rechazado']);
        if ($fechaDesde) $deliveryBaseQuery->where('creado_at', '>=', $fechaDesde);
        if ($fechaHasta) $deliveryBaseQuery->where('creado_at', '<=', $fechaHasta);
        if ($canal !== 'todos') $deliveryBaseQuery->where('canal_venta', $canal);

        $pedidosDelivery = (clone $deliveryBaseQuery)->where(function ($q) {
            $q->whereNotNull('zona_delivery_id')
              ->orWhere('costo_delivery_usd', '>', 0);
        })->count();

        $pedidosRetiro = (clone $deliveryBaseQuery)->whereNull('zona_delivery_id')
            ->where(function($q) {
                $q->whereNull('costo_delivery_usd')->orWhere('costo_delivery_usd', '<=', 0);
            })->count();

        $ingresosDeliveryUsd = (float)(clone $deliveryBaseQuery)->sum('costo_delivery_usd');

        // Zonas más solicitadas
        $zonasQuery = Pedido::leftJoin('zonas_delivery', 'pedidos.zona_delivery_id', '=', 'zonas_delivery.id')
            ->whereNotIn('pedidos.estado', ['cancelado', 'rechazado']);

        if ($fechaDesde) $zonasQuery->where('pedidos.creado_at', '>=', $fechaDesde);
        if ($fechaHasta) $zonasQuery->where('pedidos.creado_at', '<=', $fechaHasta);
        if ($canal !== 'todos') $zonasQuery->where('pedidos.canal_venta', $canal);

        $zonasStats = $zonasQuery->selectRaw('
            COALESCE(zonas_delivery.nombre_zona, "Zona Sin Registro / Personalizada") as zona_nombre,
            zonas_delivery.precio_delivery_usd as tarifa_base,
            COUNT(*) as total_pedidos,
            SUM(pedidos.costo_delivery_usd) as total_flete_usd
        ')
        ->where(function ($q) {
            $q->whereNotNull('pedidos.zona_delivery_id')
              ->orWhere('pedidos.costo_delivery_usd', '>', 0);
        })
        ->groupBy('zonas_delivery.nombre_zona', 'zonas_delivery.precio_delivery_usd')
        ->orderByDesc('total_pedidos')
        ->get()
        ->map(function ($z) {
            return [
                'zona' => $z->zona_nombre,
                'pedidos' => (int)$z->total_pedidos,
                'flete_usd' => (float)$z->total_flete_usd,
                'tarifa_base' => (float)($z->tarifa_base ?? 0)
            ];
        });

        // ==========================================
        // 10. EVOLUCIÓN TEMPORAL (SERIE PARA CHART.JS)
        // ==========================================
        $timelineQuery = Pedido::whereNotIn('estado', ['cancelado', 'rechazado']);
        if ($fechaDesde) $timelineQuery->where('creado_at', '>=', $fechaDesde);
        if ($fechaHasta) $timelineQuery->where('creado_at', '<=', $fechaHasta);
        if ($canal !== 'todos') $timelineQuery->where('canal_venta', $canal);

        // Agrupación inteligente según el período
        if ($periodo === 'hoy') {
            $timelineRaw = $timelineQuery->selectRaw('
                DATE_FORMAT(creado_at, "%H:00") as punto_tiempo,
                COUNT(*) as pedidos,
                SUM(total_usd) as total_usd
            ')
            ->groupBy(DB::raw('DATE_FORMAT(creado_at, "%H:00")'))
            ->orderBy('punto_tiempo')
            ->get();
        } elseif (in_array($periodo, ['anio', 'historico'])) {
            $timelineRaw = $timelineQuery->selectRaw('
                DATE_FORMAT(creado_at, "%Y-%m") as punto_tiempo,
                COUNT(*) as pedidos,
                SUM(total_usd) as total_usd
            ')
            ->groupBy(DB::raw('DATE_FORMAT(creado_at, "%Y-%m")'))
            ->orderBy('punto_tiempo')
            ->get();
        } else {
            // Por defecto: diario (semana, mes, ultimos_30, personalizado)
            $timelineRaw = $timelineQuery->selectRaw('
                DATE(creado_at) as punto_tiempo,
                COUNT(*) as pedidos,
                SUM(total_usd) as total_usd
            ')
            ->groupBy(DB::raw('DATE(creado_at)'))
            ->orderBy('punto_tiempo')
            ->get();
        }

        $serieLabels = [];
        $serieIngresos = [];
        $seriePedidos = [];

        foreach ($timelineRaw as $t) {
            $label = $t->punto_tiempo;
            try {
                if (str_contains($label, '-')) {
                    if (strlen($label) === 7) {
                        $label = Carbon::createFromFormat('Y-m', $label)->isoFormat('MMM YYYY');
                    } else {
                        $label = Carbon::parse($label)->isoFormat('DD MMM');
                    }
                }
            } catch (\Throwable $e) {}

            $serieLabels[] = $label;
            $serieIngresos[] = round((float)$t->total_usd, 2);
            $seriePedidos[] = (int)$t->pedidos;
        }

        $chartTimeline = [
            'labels' => $serieLabels,
            'ingresos' => $serieIngresos,
            'pedidos' => $seriePedidos
        ];

        return [
            'periodo' => $periodo,
            'canal' => $canal,
            'fecha_desde' => $fechaDesdeInput,
            'fecha_hasta' => $fechaHastaInput,
            'etiqueta_periodo' => $etiquetaPeriodo,
            'tasa_bcv' => $tasaBcv,
            'kpis' => [
                'total_ingresos_usd' => $totalIngresosUsd,
                'total_ingresos_ves' => $totalIngresosVes,
                'total_devoluciones_usd' => $totalDevolucionesUsd,
                'total_devoluciones_ves' => $totalDevolucionesVes,
                'cantidad_devoluciones' => $cantidadDevoluciones,
                'ingresos_netos_usd' => $ingresosNetosUsd,
                'ingresos_netos_ves' => $ingresosNetosVes,
                'total_pedidos' => $totalPedidos,
                'total_unidades' => $totalUnidadesVendidas,
                'ticket_promedio_usd' => $ticketPromedioUsd,
                'ticket_promedio_ves' => $ticketPromedioVes,
                'ingresos_delivery_usd' => $ingresosDeliveryUsd,
            ],
            'top_productos' => $topProductosFormatted,
            'producto_estrella' => $productoEstrella,
            'productoEstrella' => $productoEstrella,
            'chart_top_productos' => $chartTopProductos,
            'metodos_pago' => $metodosPagoFormatted,
            'chart_metodos_pago' => $chartMetodosPago,
            'comparativa_canales' => $comparativaCanales,
            'logistica' => [
                'pedidos_delivery' => $pedidosDelivery,
                'pedidos_retiro' => $pedidosRetiro,
                'total_envios' => $pedidosDelivery + $pedidosRetiro,
                'pct_delivery' => ($pedidosDelivery + $pedidosRetiro) > 0 ? round(($pedidosDelivery / ($pedidosDelivery + $pedidosRetiro)) * 100, 1) : 0,
                'ingresos_flete_usd' => $ingresosDeliveryUsd,
                'zonas' => $zonasStats
            ],
            'chart_timeline' => $chartTimeline
        ];
    }
}
