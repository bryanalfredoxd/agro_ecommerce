<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\RecetaVeterinaria;
use App\Models\Pago;
use App\Models\TasaCambio;
use App\Models\InventarioLote;
use App\Models\User; 
use App\Models\EstadisticaVentaDiaria; 
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user && !$user->tienePermiso('ver_dashboard')) {
            if ($user->tienePermiso('crear_venta_manual')) {
                return redirect()->route('admin.pos.index');
            }
            if ($user->tieneAlgunPermiso(['ver_pedidos', 'ver_pedidos_todos', 'ver_pedidos_por_verificar', 'ver_pedidos_despacho', 'ver_pedidos_en_ruta', 'ver_pedidos_completados', 'ver_pedidos_cancelados', 'gestionar_pagos'])) {
                return redirect()->route('admin.pedidos.index');
            }
            if ($user->tienePermiso('ver_productos')) {
                return redirect()->route('admin.productos.index');
            }
            if ($user->tienePermiso('gestionar_caja_diaria')) {
                return redirect()->route('admin.caja_diaria.index');
            }
            if ($user->tienePermiso('ver_facturas')) {
                return redirect()->route('admin.facturas.index');
            }
            if ($user->tienePermiso('gestionar_zonas_delivery') || $user->tienePermiso('ver_logistica')) {
                return redirect()->route('admin.zonas_delivery.index');
            }
            if ($user->tienePermiso('ver_usuarios')) {
                return redirect()->route('admin.usuarios.index');
            }
            return redirect('/')->with('error', 'No tienes permisos asignados para visualizar módulos administrativos.');
        }

        $hoy = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();

        // 1. Ventas del Día (LEYENDO DIRECTAMENTE DE LA TABLA DE ESTADÍSTICAS)
        $estadisticaHoy = EstadisticaVentaDiaria::whereDate('fecha_reporte', $hoy)->first();
        
        $ventasHoyUsd = $estadisticaHoy ? $estadisticaHoy->total_ingresos_usd : 0;
        $ventasHoyVes = $estadisticaHoy ? $estadisticaHoy->total_ingresos_ves : 0;

        // 2. Pedidos Operativos
        $pedidosPendientes = Pedido::where('estado', 'pendiente')->count();
        $pedidosNuevosHoy = Pedido::whereDate('creado_at', $hoy)->count();

        // 3. Pagos por Revisar
        $pagosPendientes = Pago::where('estado', 'revision')->count();

        // 4. Tasa de Cambio Actual
        $tasaActiva = TasaCambio::latest('creado_at')->first();
        $tasaCambioActual = $tasaActiva ? $tasaActiva->valor_tasa : 0;
        $fuenteTasa = $tasaActiva ? "Fuente: " . $tasaActiva->fuente : 'Sin Tasa Configurada';

        // 5. Alertas de Inventario General
        $alertasStock = Producto::whereNull('eliminado_at')
                    ->whereColumn('stock_total', '<=', 'stock_minimo_alerta')
                    ->count();

        // 6. Lotes a punto de Vencer
        $fechaLimite = Carbon::today()->addDays(30);
        $lotesPorVencer = InventarioLote::where('activo', 1)
                    ->where('cantidad_restante', '>', 0)
                    ->whereBetween('fecha_vencimiento', [$hoy, $fechaLimite])
                    ->count();

        // 7. Devoluciones del Mes y de Hoy
        $devolucionesMesQuery = \App\Models\Devolucion::where('estado', 'procesada')
            ->where('fecha_devolucion', '>=', $inicioMes);
        $devolucionesMesUsd = (float) (clone $devolucionesMesQuery)->sum('monto_total_usd');
        $devolucionesMesVes = (float) (clone $devolucionesMesQuery)->sum('monto_total_ves');
        $devolucionesMesCount = (int) (clone $devolucionesMesQuery)->count();

        $devolucionesHoyCount = \App\Models\Devolucion::where('estado', 'procesada')
            ->whereDate('fecha_devolucion', $hoy)
            ->count();

        $recetasPendientes = 0;

        // 8. Nuevos Clientes en el Mes
        $nuevosUsuariosMes = User::where('creado_at', '>=', $inicioMes)->count(); 

        return view('admin.dashboard', compact(
            'ventasHoyUsd', 
            'ventasHoyVes',
            'pedidosPendientes', 
            'pedidosNuevosHoy',
            'pagosPendientes',
            'tasaCambioActual',
            'fuenteTasa',
            'alertasStock', 
            'lotesPorVencer',
            'recetasPendientes',
            'devolucionesMesUsd',
            'devolucionesMesVes',
            'devolucionesMesCount',
            'devolucionesHoyCount',
            'nuevosUsuariosMes'
        ));
    }

    public function fetchNotificaciones()
    {
        $alertas = [];
        $totalAlertas = 0;
        
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // PROTECCIÓN 1: Si la sesión expiró y el AJAX intenta buscar datos, devolvemos vacío para no dar error.
        if (!$user) {
            return response()->json([
                'total' => 0, 
                'cantidad_eventos' => 0, 
                'alertas' => []
            ]);
        }

        // PROTECCIÓN 2: Verificamos que el método exista en el modelo del usuario para evitar el "Undefined method" en tiempo de ejecución
        if (!method_exists($user, 'tienePermiso')) {
            return response()->json(['total' => 0, 'cantidad_eventos' => 0, 'alertas' => []]);
        }

        // 1. Alerta de Pedidos Pendientes
        if ($user->tienePermiso('ver_pedidos') || $user->tienePermiso('ver_kpis_operativos')) {
            $pedidos = Pedido::where('estado', 'pendiente')->count();
            if ($pedidos > 0) {
                $alertas[] = [
                    'icono' => 'shopping_bag',
                    'color' => 'text-blue-600',
                    'bg' => 'bg-blue-50',
                    'titulo' => 'Pedidos Pendientes',
                    'mensaje' => "Tienes $pedidos pedidos web esperando despacho.",
                    'url' => route('admin.pedidos.index', ['filtro_estado' => 'pendientes']),
                    'tiempo' => 'Ahora'
                ];
                $totalAlertas += $pedidos;
            }
        }

        // 2. Alerta de Pagos en Revisión
        if ($user->tienePermiso('gestionar_pagos') || $user->tienePermiso('ver_kpis_financieros')) {
            $pagos = Pago::where('estado', 'revision')->count();
            if ($pagos > 0) {
                $alertas[] = [
                    'icono' => 'fact_check',
                    'color' => 'text-amber-600',
                    'bg' => 'bg-amber-50',
                    'titulo' => 'Pagos por Verificar',
                    'mensaje' => "Hay $pagos transferencias esperando aprobación.",
                    'url' => route('admin.pagos.index', ['filtro_estado' => 'revision']),
                    'tiempo' => 'Urgente'
                ];
                $totalAlertas += $pagos;
            }
        }

        // 3. Alerta de Stock Crítico
        if ($user->tienePermiso('ver_productos') || $user->tienePermiso('ver_kpis_operativos')) {
            $stock = Producto::whereNull('eliminado_at')->whereColumn('stock_total', '<=', 'stock_minimo_alerta')->count();
            if ($stock > 0) {
                $alertas[] = [
                    'icono' => 'inventory_2',
                    'color' => 'text-orange-600',
                    'bg' => 'bg-orange-50',
                    'titulo' => 'Stock Crítico',
                    'mensaje' => "$stock productos requieren reabastecimiento pronto.",
                    'url' => route('admin.productos.index', ['filtro_rapido' => 'critico']),
                    'tiempo' => 'Revisar'
                ];
                $totalAlertas += $stock;
            }
        }

        // 4. Alerta de Récipes Médicos (Deshabilitado a solicitud del cliente)
        /*
        if ($user->tienePermiso('ver_recetas') || $user->tienePermiso('ver_kpis_operativos')) {
            $recetas = RecetaVeterinaria::where('estado', 'pendiente')->count();
            if ($recetas > 0) {
                $alertas[] = [
                    'icono' => 'stethoscope',
                    'color' => 'text-purple-600',
                    'bg' => 'bg-purple-50',
                    'titulo' => 'Récipes Pendientes',
                    'mensaje' => "Tienes $recetas recetas veterinarias por auditar.",
                    'url' => '#', 
                    'tiempo' => 'Ahora'
                ];
                $totalAlertas += $recetas;
            }
        }
        */

        // 5. Último Pedido Pendiente para Alertas Nativas (Windows)
        $ultimoPedidoData = null;
        if ($user->tienePermiso('ver_pedidos') || $user->tienePermiso('ver_kpis_operativos')) {
            $ultimoPedido = Pedido::with('usuario')
                ->where('estado', 'pendiente')
                ->latest('id')
                ->first();

            if ($ultimoPedido) {
                $clienteNombre = $ultimoPedido->usuario 
                    ? trim($ultimoPedido->usuario->nombre . ' ' . ($ultimoPedido->usuario->apellido ?? ''))
                    : 'Cliente Web';
                
                $ultimoPedidoData = [
                    'id' => $ultimoPedido->id,
                    'cliente' => $clienteNombre,
                    'total_usd' => number_format($ultimoPedido->total_usd, 2),
                    'canal' => $ultimoPedido->canal_venta,
                    'creado_at' => $ultimoPedido->creado_at ? \Carbon\Carbon::parse($ultimoPedido->creado_at)->diffForHumans() : 'Reciente',
                    'url' => route('admin.pedidos.index', ['filtro_estado' => 'pendientes'])
                ];
            }
        }

        // 6. Último Pago por Verificar para Alertas Nativas (Windows)
        $ultimoPagoData = null;
        if ($user->tienePermiso('gestionar_pagos') || $user->tienePermiso('ver_kpis_financieros')) {
            $ultimoPago = Pago::with('pedido.usuario')
                ->where('estado', 'revision')
                ->latest('id')
                ->first();

            if ($ultimoPago) {
                $clienteNombre = $ultimoPago->pedido && $ultimoPago->pedido->usuario
                    ? trim($ultimoPago->pedido->usuario->nombre . ' ' . ($ultimoPago->pedido->usuario->apellido ?? ''))
                    : 'Cliente';

                $ultimoPagoData = [
                    'id' => $ultimoPago->id,
                    'pedido_id' => $ultimoPago->pedido_id,
                    'cliente' => $clienteNombre,
                    'monto_usd' => number_format($ultimoPago->monto_usd, 2),
                    'metodo' => strtoupper(str_replace('_', ' ', $ultimoPago->metodo)),
                    'referencia' => $ultimoPago->referencia_bancaria ?? 'S/R',
                    'url' => route('admin.pagos.index', ['filtro_estado' => 'revision'])
                ];
            }
        }

        return response()->json([
            'total' => count($alertas),
            'cantidad_eventos' => $totalAlertas,
            'alertas' => $alertas,
            'ultimo_pedido' => $ultimoPedidoData,
            'ultimo_pago' => $ultimoPagoData,
            'delivery_activo' => \App\Models\ConfiguracionTienda::deliveryHabilitado()
        ]);
    }
}