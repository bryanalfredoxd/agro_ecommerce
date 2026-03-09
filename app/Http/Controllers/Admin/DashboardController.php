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

        // 7. Récipes por Aprobar
        $recetasPendientes = RecetaVeterinaria::where('estado', 'pendiente')->count();

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

        // 4. Alerta de Récipes Médicos
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

        return response()->json([
            'total' => count($alertas),
            'cantidad_eventos' => $totalAlertas,
            'alertas' => $alertas
        ]);
    }
}