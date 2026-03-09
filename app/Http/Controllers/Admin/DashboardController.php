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
}