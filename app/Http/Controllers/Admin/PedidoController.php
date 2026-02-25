<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        // Traemos los pedidos con su usuario para evitar N+1 queries
        $query = Pedido::with('usuario');

        // 1. Buscador (Por ID de pedido, nombre o documento del cliente)
        if ($request->filled('buscar')) {
            $busqueda = $request->buscar;
            $query->where(function($q) use ($busqueda) {
                $q->where('id', 'LIKE', "%{$busqueda}%")
                  ->orWhereHas('usuario', function($u) use ($busqueda) {
                      $u->where('nombre', 'LIKE', "%{$busqueda}%")
                        ->orWhere('apellido', 'LIKE', "%{$busqueda}%")
                        ->orWhere('documento_identidad', 'LIKE', "%{$busqueda}%")
                        ->orWhere('email', 'LIKE', "%{$busqueda}%");
                  });
            });
        }

        // 2. Filtros por Pestañas (Estados agrupados)
        if ($request->filled('filtro_estado') && $request->filtro_estado !== 'todos') {
            switch ($request->filtro_estado) {
                case 'pendientes':
                    $query->where('estado', 'pendiente');
                    break;
                case 'en_proceso':
                    $query->whereIn('estado', ['pagado', 'preparacion']);
                    break;
                case 'en_ruta':
                    $query->where('estado', 'en_ruta');
                    break;
                case 'completados':
                    $query->whereIn('estado', ['entregado', 'completado_caja']);
                    break;
                case 'cancelados':
                    $query->whereIn('estado', ['devuelto', 'cancelado']);
                    break;
            }
        }

        // 3. Ordenamiento (Los más recientes primero siempre en pedidos)
        $pedidos = $query->orderBy('creado_at', 'desc')->paginate(10);

        if ($request->ajax()) {
            return view('admin.pedidos.partials._table', compact('pedidos'))->render();
        }

        return view('admin.pedidos.index', compact('pedidos'));
    }

    // Actualizar estado rápidamente desde el modal
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,pagado,preparacion,en_ruta,entregado,devuelto,cancelado,completado_caja'
        ]);

        $pedido = Pedido::findOrFail($id);
        
        // Guardamos el nuevo estado. 
        // ¡IMPORTANTE! Tus Triggers SQL actuarán aquí automáticamente si pasa a pagado o devuelto.
        $pedido->estado = $request->estado;
        $pedido->save();

        return response()->json([
            'success' => true, 
            'message' => 'El estado del pedido #'.$pedido->id.' ha sido actualizado a: '.strtoupper($request->estado)
        ]);
    }
}