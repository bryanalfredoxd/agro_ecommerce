<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Factura;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function index(Request $request)
    {
        // Traemos la factura junto con su pedido
        $query = Factura::with('pedido');

        // 1. Buscador (Por número de factura, cliente o RIF)
        if ($request->filled('buscar')) {
            $busqueda = $request->buscar;
            $query->where(function($q) use ($busqueda) {
                $q->where('numero_factura', 'LIKE', "%{$busqueda}%")
                  ->orWhere('nombre_razon_social', 'LIKE', "%{$busqueda}%")
                  ->orWhere('cedula_rif_cliente', 'LIKE', "%{$busqueda}%");
            });
        }

        // 2. Filtros por Pestañas (Estado)
        if ($request->filled('filtro_estado') && $request->filtro_estado !== 'todas') {
            $query->where('estado', $request->filtro_estado);
        }

        $facturas = $query->orderBy('fecha_emision', 'desc')->paginate(10);

        if ($request->ajax()) {
            return view('admin.facturas.partials._table', compact('facturas'))->render();
        }

        return view('admin.facturas.index', compact('facturas'));
    }

    public function show($id)
    {
        // Cargamos la factura con toda la estructura de productos y pagos del pedido asociado
        $factura = Factura::with([
            'pedido.detalles.producto',
            'pedido.pago',
            'pedido.usuario'
        ])->findOrFail($id);

        $config = \App\Models\ConfiguracionTienda::first();

        return view('admin.facturas.partials._detalle', compact('factura', 'config'))->render();
    }

    public function anular($id)
    {
        $factura = Factura::findOrFail($id);
        
        if ($factura->estado === 'anulada') {
            return response()->json(['success' => false, 'message' => 'La factura ya se encuentra anulada.'], 422);
        }

        $factura->estado = 'anulada';
        $factura->save();

        return response()->json(['success' => true, 'message' => 'Factura anulada correctamente en el sistema.']);
    }

    /**
     * Vista de impresión en formato Ticket Térmico (80mm)
    /**
     * Vista de impresión en formato Ticket Térmico (80mm)
     */
    public function ticket($id)
    {
        $factura = Factura::with(['pedido.detalles.producto', 'pedido.pago', 'pedido.pagos', 'pedido.usuario'])->findOrFail($id);

        $user = auth()->user();
        if ($user && !$user->esAdmin() && !$user->esPersonal() && $factura->pedido && $factura->pedido->usuario_id !== $user->id) {
            abort(403, 'No autorizado para visualizar este comprobante.');
        }

        $config = \App\Models\ConfiguracionTienda::first();
        return view('facturas.ticket', compact('factura', 'config'));
    }

    /**
     * Vista de impresión en formato Factura A4 / Fiscal
     */
    public function imprimir($id)
    {
        $factura = Factura::with(['pedido.detalles.producto', 'pedido.pago', 'pedido.pagos', 'pedido.usuario'])->findOrFail($id);

        $user = auth()->user();
        if ($user && !$user->esAdmin() && !$user->esPersonal() && $factura->pedido && $factura->pedido->usuario_id !== $user->id) {
            abort(403, 'No autorizado para visualizar este comprobante.');
        }

        $config = \App\Models\ConfiguracionTienda::first();
        return view('facturas.factura', compact('factura', 'config'));
    }

    /**
     * Ticket térmico accediendo mediante el ID del pedido
     */
    public function ticketPorPedido($pedidoId)
    {
        $factura = Factura::with(['pedido.detalles.producto', 'pedido.pago', 'pedido.pagos', 'pedido.usuario'])
            ->where('pedido_id', $pedidoId)
            ->first();

        if (!$factura) {
            // Si el pedido aún no tiene factura pero está pagado o en proceso final, intentamos generarla
            $pedido = \App\Models\Pedido::findOrFail($pedidoId);
            if (in_array($pedido->estado, ['pagado', 'en_ruta', 'en_camino', 'entregado', 'completado_caja', 'aprobado'])) {
                \App\Models\Factura::emitirParaPedido($pedido);
                $factura = Factura::with(['pedido.detalles.producto', 'pedido.pago', 'pedido.pagos', 'pedido.usuario'])
                    ->where('pedido_id', $pedidoId)
                    ->first();
            }
        }

        if (!$factura) {
            abort(404, 'No hay factura emitida para este pedido.');
        }

        $user = auth()->user();
        if ($user && !$user->esAdmin() && !$user->esPersonal() && $factura->pedido && $factura->pedido->usuario_id !== $user->id) {
            abort(403, 'No autorizado para visualizar este comprobante.');
        }

        $config = \App\Models\ConfiguracionTienda::first();
        return view('facturas.ticket', compact('factura', 'config'));
    }

    /**
     * Factura A4 / Fiscal accediendo mediante el ID del pedido
     */
    public function facturaPorPedido($pedidoId)
    {
        $factura = Factura::with(['pedido.detalles.producto', 'pedido.pago', 'pedido.pagos', 'pedido.usuario'])
            ->where('pedido_id', $pedidoId)
            ->first();

        if (!$factura) {
            // Si el pedido aún no tiene factura pero está pagado o en proceso final, intentamos generarla
            $pedido = \App\Models\Pedido::findOrFail($pedidoId);
            if (in_array($pedido->estado, ['pagado', 'en_ruta', 'en_camino', 'entregado', 'completado_caja', 'aprobado'])) {
                \App\Models\Factura::emitirParaPedido($pedido);
                $factura = Factura::with(['pedido.detalles.producto', 'pedido.pago', 'pedido.pagos', 'pedido.usuario'])
                    ->where('pedido_id', $pedidoId)
                    ->first();
            }
        }

        if (!$factura) {
            abort(404, 'No hay factura emitida para este pedido.');
        }

        $user = auth()->user();
        if ($user && !$user->esAdmin() && !$user->esPersonal() && $factura->pedido && $factura->pedido->usuario_id !== $user->id) {
            abort(403, 'No autorizado para visualizar este comprobante.');
        }

        $config = \App\Models\ConfiguracionTienda::first();
        return view('facturas.factura', compact('factura', 'config'));
    }
}