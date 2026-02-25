<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\RecetaVeterinaria;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();

        // 1. Ventas del Día (Suma de total_usd de pedidos pagados/completados hoy)
        $ventasHoy = Pedido::whereIn('estado', ['pagado', 'completado_caja', 'entregado'])
                    ->whereDate('creado_at', $hoy)
                    ->sum('total_usd');

        // 2. Pedidos Pendientes (Cantidad de pedidos que requieren atención)
        $pedidosPendientes = Pedido::where('estado', 'pendiente')->count();

        // 3. Alertas de Stock (Productos cuyo stock es menor o igual al mínimo de alerta)
        $alertasStock = Producto::whereNull('eliminado_at')
                    ->whereColumn('stock_total', '<=', 'stock_minimo_alerta')
                    ->count();

        // 4. Récipes por Aprobar
        $recetasPendientes = RecetaVeterinaria::where('estado', 'pendiente')->count();

        // Pasamos los datos a la vista del Dashboard
        return view('admin.dashboard', compact(
            'ventasHoy', 
            'pedidosPendientes', 
            'alertasStock', 
            'recetasPendientes'
        ));
    }
}