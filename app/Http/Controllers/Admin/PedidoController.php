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
        $user = Auth::user();
        $esAdmin = $user->esAdmin();
        $allowedTabs = $this->obtenerTabsPermitidos($user);

        if (empty($allowedTabs)) {
            abort(403, 'No tienes permisos asignados para visualizar el módulo de pedidos.');
        }

        // Determinar el tab activo:
        // Si el usuario solicita un tab, validar que esté en sus permitidos.
        // Si no está permitido o no se envió, usar el primero permitido ('todos' si está disponible, o el primer tab operativo).
        $requestedEstado = $request->filtro_estado;
        if ($requestedEstado && in_array($requestedEstado, $allowedTabs)) {
            $filtroEstado = $requestedEstado;
        } else {
            if (in_array('todos', $allowedTabs) && empty($requestedEstado)) {
                $filtroEstado = 'todos';
            } else {
                $filtroEstado = $allowedTabs[0];
            }
        }

        $query = Pedido::with([
            'usuario',
            'pago.verificador',
            'pagos.verificador',
            'factura',
            'detalles.producto.categoria',
            'detalles.producto.marca'
        ]);

        // RESTRICCIÓN DE SEGURIDAD ESTRICTA:
        // Si el usuario no tiene permiso global ('todos'), restringir la consulta ÚNICAMENTE
        // a los estados correspondientes a los tabs autorizados.
        if (!in_array('todos', $allowedTabs)) {
            $query->where(function($q) use ($allowedTabs) {
                $first = true;
                foreach ($allowedTabs as $tab) {
                    $method = $first ? 'where' : 'orWhere';
                    $first = false;

                    switch ($tab) {
                        case 'por_verificar':
                            $q->$method(function($sub) {
                                $sub->whereHas('pagos', fn($p) => $p->where('estado', 'revision'));
                            });
                            break;
                        case 'por_despachar':
                            $q->$method(function($sub) {
                                $sub->whereIn('estado', ['pagado', 'preparacion']);
                            });
                            break;
                        case 'en_ruta':
                            $q->$method('estado', 'en_ruta');
                            break;
                        case 'completados':
                            $q->$method(function($sub) {
                                $sub->whereIn('estado', ['entregado', 'completado_caja']);
                            });
                            break;
                        case 'cancelados':
                            $q->$method(function($sub) {
                                $sub->whereIn('estado', ['devuelto', 'cancelado']);
                            });
                            break;
                    }
                }
            });
        }

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

        // Filtro por Estado según el Tab Activo
        if ($filtroEstado !== 'todos') {
            switch ($filtroEstado) {
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

        // Métricas / Contadores para los Tabs y KPIs (Protegidos por permisos)
        $puedeVerTotal = in_array('todos', $allowedTabs);
        $puedeVerVerificar = in_array('por_verificar', $allowedTabs);
        $puedeVerDespacho = in_array('por_despachar', $allowedTabs);
        $puedeVerRuta = in_array('en_ruta', $allowedTabs);
        $puedeVerCompletados = in_array('completados', $allowedTabs);
        $puedeVerCancelados = in_array('cancelados', $allowedTabs);
        $puedeVerVentasFinancieras = $esAdmin || $user->tienePermiso('ver_kpis_financieros') || $user->tienePermiso('ver_estadisticas_analitica');

        $countPorVerificar = $puedeVerVerificar ? Pedido::whereHas('pagos', fn($p) => $p->where('estado', 'revision'))->count() : 0;
        $countPorDespachar = $puedeVerDespacho ? Pedido::whereIn('estado', ['pagado', 'preparacion'])->count() : 0;
        $countEnRuta = $puedeVerRuta ? Pedido::where('estado', 'en_ruta')->count() : 0;
        $countCompletados = $puedeVerCompletados ? Pedido::whereIn('estado', ['entregado', 'completado_caja'])->count() : 0;
        $countCancelados = $puedeVerCancelados ? Pedido::whereIn('estado', ['devuelto', 'cancelado'])->count() : 0;

        $countTotal = $puedeVerTotal 
            ? Pedido::count() 
            : ($countPorVerificar + $countPorDespachar + $countEnRuta + $countCompletados + $countCancelados);

        $stats = [
            'total' => $countTotal,
            'por_verificar' => $countPorVerificar,
            'por_despachar' => $countPorDespachar,
            'en_ruta' => $countEnRuta,
            'completados' => $countCompletados,
            'cancelados' => $countCancelados,
            'ventas_total_usd' => $puedeVerVentasFinancieras 
                ? (float) Pedido::whereIn('estado', ['pagado', 'preparacion', 'en_ruta', 'entregado', 'completado_caja'])->sum('total_usd') 
                : 0.0,
        ];

        $pedidos = $query->orderBy('creado_at', 'desc')->paginate(12)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('admin.pedidos.partials._table', compact('pedidos', 'stats', 'allowedTabs', 'filtroEstado'))->render(),
                'stats' => $stats,
                'filtro_estado' => $filtroEstado,
                'allowed_tabs' => $allowedTabs
            ]);
        }

        return view('admin.pedidos.index', compact('pedidos', 'stats', 'allowedTabs', 'filtroEstado'));
    }

    /**
     * Determina qué tabs del listado de pedidos tiene permitido visualizar el usuario.
     */
    public function obtenerTabsPermitidos($user): array
    {
        if ($user->esAdmin() || $user->tienePermiso('ver_pedidos_todos')) {
            return ['todos', 'por_verificar', 'por_despachar', 'en_ruta', 'completados', 'cancelados'];
        }

        $allowed = [];

        if ($user->tienePermiso('ver_pedidos_por_verificar') || $user->tienePermiso('gestionar_pagos')) {
            $allowed[] = 'por_verificar';
        }
        if ($user->tienePermiso('ver_pedidos_despacho')) {
            $allowed[] = 'por_despachar';
        }
        if ($user->tienePermiso('ver_pedidos_en_ruta')) {
            $allowed[] = 'en_ruta';
        }
        if ($user->tienePermiso('ver_pedidos_completados')) {
            $allowed[] = 'completados';
        }
        if ($user->tienePermiso('ver_pedidos_cancelados')) {
            $allowed[] = 'cancelados';
        }

        // Si tiene ver_pedidos general pero no tiene configurado ningún permiso granular específico
        if (empty($allowed) && $user->tienePermiso('ver_pedidos')) {
            return ['todos', 'por_verificar', 'por_despachar', 'en_ruta', 'completados', 'cancelados'];
        }

        return $allowed;
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

        $user = Auth::user();
        if (!$pedido->puedeSerVistoPor($user)) {
            abort(403, 'No tienes permiso para consultar los detalles de este pedido.');
        }

        return view('admin.pedidos.partials._detalles', compact('pedido'))->render();
    }

    // Actualizar Estado Logístico
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:preparacion,en_ruta,entregado,cancelado'
        ]);

        $pedido = Pedido::findOrFail($id);
        $user = Auth::user();

        // 0. Barrera RBAC/IDOR: Verificar que el usuario tenga acceso a esta orden
        if (!$pedido->puedeSerVistoPor($user)) {
            return response()->json([
                'success' => false,
                'message' => 'No cuentas con autorización para operar sobre este pedido.'
            ], 403);
        }
        
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

        // 3. Barrera: Control de cancelaciones
        if ($request->estado === 'cancelado' && !$user->esAdmin() && !$user->tienePermiso('ver_pedidos_cancelados')) {
            return response()->json([
                'success' => false,
                'message' => 'No cuentas con autorización para cancelar órdenes.'
            ], 403);
        }

        // 4. Barrera: Si es solo repartidor / en ruta
        $esSoloReparto = !$user->esAdmin() && $user->tienePermiso('ver_pedidos_en_ruta') && !$user->tienePermiso('ver_pedidos_despacho') && !$user->tienePermiso('ver_pedidos_todos');
        if ($esSoloReparto) {
            if (!in_array($request->estado, ['en_ruta', 'entregado'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Como repartidor, solo puedes actualizar pedidos a En Ruta o Entregado.'
                ], 403);
            }
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
        if (!$pedido->puedeSerVistoPor(Auth::user())) {
            return response()->json(['success' => false, 'message' => 'No tienes autorización para auditar este pago.'], 403);
        }
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
        if (!$pedido->puedeSerVistoPor(Auth::user())) {
            return response()->json(['success' => false, 'message' => 'No tienes autorización para auditar este pago.'], 403);
        }
        if (!$pedido->pago) {
            return response()->json(['success' => false, 'message' => 'Este pedido no tiene un pago reportado.'], 404);
        }
        $pagoController = app(\App\Http\Controllers\Admin\PagoController::class);
        return $pagoController->rechazar($pedido->pago->id);
    }
}