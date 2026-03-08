<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        // (Tu función index actual se mantiene igual)
        $query = Pedido::with('usuario');

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

        $pedidos = $query->orderBy('creado_at', 'desc')->paginate(10);

        if ($request->ajax()) {
            return view('admin.pedidos.partials._table', compact('pedidos'))->render();
        }

        return view('admin.pedidos.index', compact('pedidos'));
    }

    // NUEVO: Traer detalles para el modal de despacho
    public function show($id)
    {
        // Cargamos el pedido con el usuario, los detalles, el producto de cada detalle, su categoría y marca
        $pedido = Pedido::with([
            'usuario', 
            'detalles.producto.categoria', 
            'detalles.producto.marca'
        ])->findOrFail($id);

        // Devolvemos un pedazo de HTML renderizado listo para inyectar en el Modal
        return view('admin.pedidos.partials._detalles', compact('pedido'))->render();
    }

    // ACTUALIZADO: Seguridad Estricta para Logística
    public function updateStatus(Request $request, $id)
    {
        // Logística SOLO puede manejar estos 4 estados
        $request->validate([
            'estado' => 'required|in:preparacion,en_ruta,entregado,cancelado'
        ]);

        $pedido = Pedido::findOrFail($id);
        
        // 1. Barrera: Si no está pagado, logística no puede tocarlo.
        if ($pedido->estado === 'pendiente') {
            return response()->json([
                'success' => false, 
                'message' => '¡Bloqueado! Finanzas debe verificar el pago antes de poder despachar la mercancía.'
            ], 422);
        }

        // 2. Barrera: Si ya es un estado final, no se puede alterar desde aquí.
        $estadosFinales = ['entregado', 'completado_caja', 'devuelto', 'cancelado'];
        if (in_array($pedido->estado, $estadosFinales)) {
            return response()->json([
                'success' => false, 
                'message' => 'Este pedido ya está finalizado ('.strtoupper($pedido->estado).'). Si hay un problema, usa el módulo de Devoluciones.'
            ], 422);
        }

        $pedido->estado = $request->estado;
        $pedido->save();

        return response()->json([
            'success' => true, 
            'message' => 'Estado logístico actualizado a: '.strtoupper(str_replace('_', ' ', $request->estado))
        ]);
    }
}