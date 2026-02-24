<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventarioLote;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    public function index()
    {
        // Obtener todos los lotes de inventario con sus relaciones
        $lotes = InventarioLote::with(['producto', 'proveedor'])
            ->where('activo', true)
            ->orderBy('fecha_vencimiento', 'asc')
            ->paginate(15);

        // Estadísticas del inventario
        $totalProductos = Producto::count();
        $totalLotes = InventarioLote::where('activo', true)->count();
        
        $lotesVencidos = InventarioLote::where('activo', true)
            ->where('fecha_vencimiento', '<', now())
            ->count();
            
        $lotesPorVencer = InventarioLote::where('activo', true)
            ->where('fecha_vencimiento', '>=', now())
            ->where('fecha_vencimiento', '<=', now()->addDays(30))
            ->count();

        // Valor total del inventario (CORREGIDO: Quitamos el ->where('activo', true) porque la vista no tiene esa columna)
        $valorTotalInventario = DB::table('view_inventario_valorado')
            ->sum('valor_total_usd');

        return view('admin.inventario.index', compact(
            'lotes',
            'totalProductos',
            'totalLotes',
            'lotesVencidos',
            'lotesPorVencer',
            'valorTotalInventario'
        ));
    }

    public function show($id)
    {
        $lote = InventarioLote::with(['producto', 'proveedor'])->findOrFail($id);

        return view('admin.inventario.show', compact('lote'));
    }

    public function edit($id)
    {
        $lote = InventarioLote::with(['producto', 'proveedor'])->findOrFail($id);

        return view('admin.inventario.edit', compact('lote'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'numero_lote' => 'required|string|max:50',
            'fecha_vencimiento' => 'required|date|after:today',
            'ubicacion_almacen' => 'nullable|string|max:50',
        ]);

        $lote = InventarioLote::findOrFail($id);
        $lote->update($request->only(['numero_lote', 'fecha_vencimiento', 'ubicacion_almacen']));

        return redirect()->route('admin.inventario.show', $lote->id)
            ->with('success', 'Lote actualizado correctamente.');
    }

    public function agregarStock(Request $request, $id)
    {
        $request->validate([
            'cantidad_agregar' => 'required|numeric|min:0.001',
            'costo_unitario_nuevo' => 'required|numeric|min:0.0001',
        ]);

        $lote = InventarioLote::findOrFail($id);

        // Calcular nuevo costo promedio ponderado
        $cantidadActual = $lote->cantidad_restante;
        $costoActual = $lote->costo_unitario_usd;
        $cantidadNueva = $request->cantidad_agregar;
        $costoNuevo = $request->costo_unitario_nuevo;

        $nuevaCantidadTotal = $cantidadActual + $cantidadNueva;
        $nuevoCostoPromedio = (($cantidadActual * $costoActual) + ($cantidadNueva * $costoNuevo)) / $nuevaCantidadTotal;

        // Actualizar lote
        $lote->update([
            'cantidad_restante' => $nuevaCantidadTotal,
            'costo_unitario_usd' => $nuevoCostoPromedio,
        ]);

        // Actualizar stock total del producto
        $lote->producto->increment('stock_total', $cantidadNueva);

        return redirect()->route('admin.inventario.show', $lote->id)
            ->with('success', 'Stock agregado correctamente.');
    }

    public function archivar(Request $request, $id)
    {
        $lote = InventarioLote::findOrFail($id);

        // Solo permitir archivar si no hay stock restante
        if ($lote->cantidad_restante > 0) {
            return redirect()->route('admin.inventario.show', $lote->id)
                ->with('error', 'No se puede archivar un lote con stock restante.');
        }

        $lote->update(['activo' => false]);

        return redirect()->route('admin.inventario.index')
            ->with('success', 'Lote archivado correctamente.');
    }
}