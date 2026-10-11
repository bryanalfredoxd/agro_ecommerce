<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SolicitudProducto;
use Illuminate\Http\Request;

class SolicitudProductoController extends Controller
{
    /**
     * Listado general de solicitudes de insumos y productos realizadas por clientes.
     */
    public function index(Request $request)
    {
        $query = SolicitudProducto::with('usuario');

        // 1. Buscador por texto
        if ($request->filled('buscar')) {
            $busqueda = trim($request->buscar);
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre_producto', 'LIKE', "%{$busqueda}%")
                  ->orWhere('descripcion_adicional', 'LIKE', "%{$busqueda}%")
                  ->orWhere('nombre_contacto', 'LIKE', "%{$busqueda}%")
                  ->orWhere('telefono_contacto', 'LIKE', "%{$busqueda}%")
                  ->orWhereHas('usuario', function ($u) use ($busqueda) {
                      $u->where('nombre', 'LIKE', "%{$busqueda}%")
                        ->orWhere('apellido', 'LIKE', "%{$busqueda}%")
                        ->orWhere('email', 'LIKE', "%{$busqueda}%")
                        ->orWhere('telefono', 'LIKE', "%{$busqueda}%");
                  });
            });
        }

        // 2. Filtro por estado
        if ($request->filled('estado') && $request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        }

        // 3. Filtro por tipo de solicitante (registrado vs visitante)
        if ($request->filled('tipo_cliente')) {
            if ($request->tipo_cliente === 'registrado') {
                $query->whereNotNull('usuario_id');
            } elseif ($request->tipo_cliente === 'visitante') {
                $query->whereNull('usuario_id');
            }
        }

        // 4. Orden
        if ($request->get('orden') === 'antiguo') {
            $query->oldest('fecha_peticion');
        } else {
            $query->latest('fecha_peticion');
        }

        // 5. KPIs resumidos
        $stats = [
            'total' => SolicitudProducto::count(),
            'pendientes' => SolicitudProducto::where('estado', 'pendiente')->count(),
            'revisados' => SolicitudProducto::where('estado', 'revisado')->count(),
            'agregados' => SolicitudProducto::where('estado', 'agregado')->count(),
            'rechazados' => SolicitudProducto::where('estado', 'rechazado')->count(),
        ];

        $solicitudes = $query->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('admin.solicitudes.partials._table', compact('solicitudes'))->render(),
                'stats' => $stats
            ]);
        }

        return view('admin.solicitudes.index', compact('solicitudes', 'stats'));
    }

    /**
     * Actualizar el estado de una solicitud (pendiente, revisado, agregado, rechazado).
     */
    public function actualizarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,revisado,agregado,rechazado'
        ]);

        $solicitud = SolicitudProducto::findOrFail($id);
        $solicitud->estado = $request->estado;
        $solicitud->save();

        $mensajes = [
            'pendiente' => 'Solicitud marcada como Pendiente.',
            'revisado' => 'Solicitud marcada como En Revisión / Cotizando con proveedores.',
            'agregado' => '¡Solicitud marcada como Agregada al Inventario!',
            'rechazado' => 'Solicitud marcada como Descartada / No disponible.',
        ];

        $msj = $mensajes[$request->estado] ?? 'Estado actualizado correctamente.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msj,
                'solicitud' => $solicitud,
                'badge' => $solicitud->badge_estado
            ]);
        }

        return redirect()->back()->with('success', $msj);
    }

    /**
     * Eliminar registro de solicitud.
     */
    public function destroy(Request $request, $id)
    {
        $solicitud = SolicitudProducto::findOrFail($id);
        $nombre = $solicitud->nombre_producto;
        $solicitud->delete();

        $msj = "Solicitud de '{$nombre}' eliminada correctamente.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msj
            ]);
        }

        return redirect()->route('admin.solicitudes_productos.index')->with('success', $msj);
    }
}
