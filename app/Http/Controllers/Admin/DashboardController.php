<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria; // Importamos tu modelo Eloquent
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today()->toDateString();
        $inicioSemana = Carbon::now()->startOfWeek()->toDateString(); // Lunes de esta semana

        // Extraer las categorías principales usando tu Modelo Eloquent
        $categoriasPrincipales = Categoria::whereNull('categoria_padre_id')->get();

        // 1. Ventas del día
        $ventasHoy = DB::table('view_ventas_diarias_detalladas')
            ->whereDate('fecha', $hoy)
            ->first();

        $totalIngresosUsd = $ventasHoy ? $ventasHoy->total_ventas_usd : 0;
        $totalPedidosHoy = $ventasHoy ? $ventasHoy->total_pedidos : 0;

        // 2. Ventas de la semana
        $ventasSemana = DB::table('view_ventas_diarias_detalladas')
            ->whereBetween('fecha', [$inicioSemana, $hoy])
            ->selectRaw('SUM(total_ventas_usd) as total_ventas_semana, SUM(total_pedidos) as total_pedidos_semana')
            ->first();

        $totalIngresosSemana = $ventasSemana ? $ventasSemana->total_ventas_semana : 0;
        $totalPedidosSemana = $ventasSemana ? $ventasSemana->total_pedidos_semana : 0;

        // 3. Pedidos pendientes o en preparación
        $pedidosActivos = DB::table('pedidos')
            ->whereIn('estado', ['pendiente', 'pagado', 'preparacion'])
            ->count();

        // 4. Alertas de Inventario (Usando tu vista de inventario valorado)
        $stockCritico = DB::table('view_inventario_valorado')
            ->where('estado_stock', 'CRÍTICO')
            ->count();

        // 5. Alertas de Vencimiento (Usando tu vista de productos por vencer)
        $productosPorVencer = DB::table('view_productos_proximos_vencer')
            ->whereIn('estado_vencimiento', ['PRÓXIMO A VENCER', 'ALERTA MEDIA'])
            ->count();

        // 6. Recetas veterinarias pendientes de revisión
        $recetasPendientes = DB::table('recetas_veterinarias')
            ->where('estado', 'pendiente')
            ->count();

        // 7. Últimos 5 pedidos para una tabla rápida
        $ultimosPedidos = DB::table('pedidos')
            ->join('usuarios', 'pedidos.usuario_id', '=', 'usuarios.id')
            ->select('pedidos.id', 'usuarios.nombre', 'usuarios.apellido', 'pedidos.total_usd', 'pedidos.estado', 'pedidos.creado_at')
            ->orderBy('pedidos.creado_at', 'desc')
            ->limit(5)
            ->get();

        // 8. Productos más vendidos (top 3 por cantidad total vendida)
        $productosMasVendidos = DB::table('pedido_detalles')
            ->join('productos', 'pedido_detalles.producto_id', '=', 'productos.id')
            ->select('productos.nombre', 'productos.id', DB::raw('SUM(pedido_detalles.cantidad_solicitada) as total_vendido'))
            ->groupBy('productos.id', 'productos.nombre')
            ->orderBy('total_vendido', 'desc')
            ->limit(3)
            ->get();

        return view('admin.dashboard', compact(
            'categoriasPrincipales',
            'totalIngresosUsd', 
            'totalPedidosHoy', 
            'totalIngresosSemana',
            'totalPedidosSemana',
            'pedidosActivos', 
            'stockCritico', 
            'productosPorVencer',
            'recetasPendientes',
            'ultimosPedidos',
            'productosMasVendidos'
        ));
    }
}