<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $query = Pedido::with([
            'usuario',
            'pago.verificador',
            'pagos.verificador',
            'factura',
            'detalles.producto.categoria',
            'detalles.producto.marca'
        ]);

        // Buscador Global Inteligente
        if ($request->filled('buscar')) {
            $busqueda = trim($request->buscar);
            $numeroLimpio = ltrim(preg_replace('/[^0-9]/', '', $busqueda), '0');

            $query->where(function($q) use ($busqueda, $numeroLimpio) {
                if (!empty($numeroLimpio) && is_numeric($numeroLimpio)) {
                    $q->where('id', $numeroLimpio);
                } else {
                    $q->where('id', 'LIKE', "%{$busqueda}%");
                }

                $q->orWhereHas('usuario', function($u) use ($busqueda) {
                    $u->where('nombre', 'LIKE', "%{$busqueda}%")
                      ->orWhere('apellido', 'LIKE', "%{$busqueda}%")
                      ->orWhere(DB::raw("CONCAT(nombre, ' ', apellido)"), 'LIKE', "%{$busqueda}%")
                      ->orWhere('documento_identidad', 'LIKE', "%{$busqueda}%")
                      ->orWhere('email', 'LIKE', "%{$busqueda}%")
                      ->orWhere('telefono', 'LIKE', "%{$busqueda}%");
                })
                ->orWhereHas('pagos', function($p) use ($busqueda) {
                    $p->where('referencia_bancaria', 'LIKE', "%{$busqueda}%");
                })
                ->orWhereHas('factura', function($f) use ($busqueda) {
                    $f->where('numero_factura', 'LIKE', "%{$busqueda}%")
                      ->orWhere('cedula_rif_cliente', 'LIKE', "%{$busqueda}%")
                      ->orWhere('nombre_razon_social', 'LIKE', "%{$busqueda}%");
                });
            });
        }

        // Filtro por Estado (Tabs Inteligentes)
        $filtroEstado = $request->filtro_estado ?? 'todos';
        if ($request->filled('filtro_estado') && $request->filtro_estado !== 'todos') {
            switch ($request->filtro_estado) {
                case 'por_verificar':
                    $query->whereHas('pagos', function($p) {
                        $p->where('estado', 'revision');
                    });
                    break;
                case 'pendientes':
                    $query->where('estado', 'pendiente');
                    break;
                case 'por_despachar':
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
                case 'rechazados':
                    $query->whereHas('pagos', function($p) {
                        $p->where('estado', 'rechazado');
                    });
                    break;
            }
        }

        // Filtro por Canal de Venta
        if ($request->filled('filtro_canal') && $request->filtro_canal !== 'todos') {
            $query->where('canal_venta', $request->filtro_canal);
        }

        // Filtro por Método de Pago
        if ($request->filled('filtro_metodo') && $request->filtro_metodo !== 'todos') {
            $query->whereHas('pagos', function($p) use ($request) {
                $p->where('metodo', $request->filtro_metodo);
            });
        }

        // Métricas / Contadores para los Tabs y KPIs
        $stats = [
            'total' => Pedido::count(),
            'por_verificar' => Pedido::whereHas('pagos', fn($p) => $p->where('estado', 'revision'))->count(),
            'por_despachar' => Pedido::whereIn('estado', ['pagado', 'preparacion'])->count(),
            'en_ruta' => Pedido::where('estado', 'en_ruta')->count(),
            'completados' => Pedido::whereIn('estado', ['entregado', 'completado_caja'])->count(),
            'cancelados' => Pedido::whereIn('estado', ['devuelto', 'cancelado'])->count(),
            'ventas_total_usd' => (float) Pedido::whereIn('estado', ['pagado', 'preparacion', 'en_ruta', 'entregado', 'completado_caja'])->sum('total_usd'),
        ];

        $pedidos = $query->orderBy('creado_at', 'desc')->paginate(12)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('admin.pedidos.partials._table', compact('pedidos', 'stats'))->render(),
                'stats' => $stats
            ]);
        }

        return view('admin.pedidos.index', compact('pedidos', 'stats'));
    }

    // Modal de Detalle Completo del Pedido y Auditoría de Pago
    public function show($id)
    {
        $pedido = Pedido::with([
            'usuario', 
            'factura',
            'pago.verificador',
            'pagos.verificador',
            'detalles.producto.categoria', 
            'detalles.producto.marca'
        ])->findOrFail($id);

        return view('admin.pedidos.partials._detalles', compact('pedido'))->render();
    }

    // Actualizar Estado Logístico
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:preparacion,en_ruta,entregado,cancelado'
        ]);

        $pedido = Pedido::findOrFail($id);
        
        // 1. Barrera: Si no está pagado, no se puede despachar
        if ($pedido->estado === 'pendiente') {
            return response()->json([
                'success' => false, 
                'message' => '¡Bloqueado! Debe verificarse y aprobarse el pago antes de poder despachar.'
            ], 422);
        }

        // 2. Barrera: Si ya es un estado final, bloquear cambio arbitrario
        $estadosFinales = ['entregado', 'completado_caja', 'devuelto', 'cancelado'];
        if (in_array($pedido->estado, $estadosFinales)) {
            return response()->json([
                'success' => false, 
                'message' => 'Este pedido ya está finalizado ('.strtoupper($pedido->estado).').'
            ], 422);
        }

        $pedido->estado = $request->estado;
        $pedido->save();

        return response()->json([
            'success' => true, 
            'message' => 'Estado logístico actualizado a: '.strtoupper(str_replace('_', ' ', $request->estado))
        ]);
    }

    // Aprobar Pago desde Pedido
    public function aprobarPago($id)
    {
        $pedido = Pedido::with('pago')->findOrFail($id);
        if (!$pedido->pago) {
            return response()->json(['success' => false, 'message' => 'Este pedido no tiene un pago reportado.'], 404);
        }
        $pagoController = app(\App\Http\Controllers\Admin\PagoController::class);
        return $pagoController->aprobar($pedido->pago->id);
    }

    // Rechazar Pago desde Pedido
    public function rechazarPago($id)
    {
        $pedido = Pedido::with('pago')->findOrFail($id);
        if (!$pedido->pago) {
            return response()->json(['success' => false, 'message' => 'Este pedido no tiene un pago reportado.'], 404);
        }
        $pagoController = app(\App\Http\Controllers\Admin\PagoController::class);
        return $pagoController->rechazar($pedido->pago->id);
    }
}