<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Factura;
use App\Models\Devolucion;
use App\Models\DevolucionDetalle;
use App\Models\Producto;
use App\Models\InventarioLote;
use App\Models\TasaCambio;
use App\Models\ConfiguracionTienda;
use App\Models\MovimientoCaja;
use App\Models\SesionCaja;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DevolucionController extends Controller
{
    /**
     * Listado general de devoluciones procesadas
     */
    public function index(Request $request)
    {
        $query = Devolucion::with(['factura', 'pedido', 'cliente', 'usuario', 'detalles.producto']);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('codigo_devolucion', 'LIKE', "%{$buscar}%")
                  ->orWhere('motivo_general', 'LIKE', "%{$buscar}%")
                  ->orWhereHas('factura', function($qf) use ($buscar) {
                      $qf->where('numero_factura', 'LIKE', "%{$buscar}%")
                         ->orWhere('nombre_razon_social', 'LIKE', "%{$buscar}%")
                         ->orWhere('cedula_rif_cliente', 'LIKE', "%{$buscar}%");
                  });
            });
        }

        if ($request->filled('metodo_reembolso') && $request->metodo_reembolso !== 'todos') {
            $query->where('metodo_reembolso', $request->metodo_reembolso);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_devolucion', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_devolucion', '<=', $request->fecha_hasta);
        }

        $devoluciones = $query->orderBy('fecha_devolucion', 'desc')->paginate(15);

        // Métricas rápidas
        $totalDevueltoUsd = (float) Devolucion::where('estado', 'procesada')->sum('monto_total_usd');
        $totalDevueltoVes = (float) Devolucion::where('estado', 'procesada')->sum('monto_total_ves');
        $cantidadDevoluciones = Devolucion::where('estado', 'procesada')->count();

        return view('admin.devoluciones.index', compact(
            'devoluciones', 'totalDevueltoUsd', 'totalDevueltoVes', 'cantidadDevoluciones'
        ));
    }

    /**
     * Endpoint API para consultar datos de la factura antes de abrir el modal de devolución
     */
    public function datosParaDevolucion($facturaId)
    {
        $factura = Factura::with([
            'pedido.detalles.producto',
            'pedido.detalles.lote',
            'pedido.usuario',
            'devoluciones.detalles'
        ])->findOrFail($facturaId);

        if ($factura->estado === 'anulada') {
            return response()->json([
                'success' => false,
                'message' => 'No se puede procesar una devolución para una factura anulada fiscalmente.'
            ], 422);
        }

        // Tasa actual BCV
        $tasaActiva = TasaCambio::latest('creado_at')->first();
        $tasaActual = $tasaActiva ? (float)$tasaActiva->valor_tasa : (float)($factura->valor_tasa_bcv ?: 1.0);

        // Preparamos los ítems calculando cantidades ya devueltas
        $items = [];
        $totalYaDevueltoUsd = 0;

        if ($factura->pedido && $factura->pedido->detalles) {
            foreach ($factura->pedido->detalles as $det) {
                // Sumar devoluciones previas de este ítem
                $yaDevuelto = (float) DevolucionDetalle::where('pedido_detalle_id', $det->id)
                    ->whereHas('devolucion', function($q) {
                        $q->where('estado', 'procesada');
                    })->sum('cantidad_devuelta');

                $cantidadOriginal = (float) $det->cantidad_solicitada;
                $cantidadDisponible = max(0, $cantidadOriginal - $yaDevuelto);
                $precioUnitario = (float) $det->precio_historico_usd;

                $totalYaDevueltoUsd += ($yaDevuelto * $precioUnitario);

                $items[] = [
                    'pedido_detalle_id' => $det->id,
                    'producto_id' => $det->producto_id,
                    'nombre_producto' => $det->producto ? $det->producto->nombre : 'Producto sin catálogo',
                    'sku' => $det->producto ? $det->producto->sku : 'S/SKU',
                    'unidad' => $det->producto ? ($det->producto->unidad_medida ?: 'ud') : 'ud',
                    'cantidad_original' => $cantidadOriginal,
                    'ya_devuelto' => $yaDevuelto,
                    'cantidad_disponible' => $cantidadDisponible,
                    'precio_unitario_usd' => $precioUnitario,
                    'subtotal_original_usd' => round($cantidadOriginal * $precioUnitario, 2),
                    'lote_codigo' => $det->lote ? $det->lote->numero_lote : null,
                    'lote_id' => $det->inventario_lote_id,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'factura' => [
                'id' => $factura->id,
                'numero_factura' => $factura->numero_factura,
                'fecha_emision' => \Carbon\Carbon::parse($factura->fecha_emision)->format('d/m/Y h:i A'),
                'cliente_nombre' => $factura->nombre_razon_social ?: 'Consumidor Final',
                'cliente_documento' => $factura->cedula_rif_cliente ?: 'V-00000000',
                'total_usd' => (float)$factura->total_usd,
                'total_ves' => (float)$factura->total_ves,
                'tasa_factura' => (float)$factura->valor_tasa_bcv,
                'estado' => $factura->estado,
                'total_ya_devuelto_usd' => round($totalYaDevueltoUsd, 2),
            ],
            'tasa_bcv_actual' => $tasaActual,
            'items' => $items,
            'devoluciones_anteriores' => $factura->devoluciones->map(function($dev) {
                return [
                    'id' => $dev->id,
                    'codigo' => $dev->codigo_devolucion,
                    'monto_usd' => (float)$dev->monto_total_usd,
                    'monto_ves' => (float)$dev->monto_total_ves,
                    'metodo' => $dev->metodo_reembolso,
                    'motivo' => $dev->motivo_general,
                    'fecha' => \Carbon\Carbon::parse($dev->fecha_devolucion)->format('d/m/Y h:i A'),
                ];
            })
        ]);
    }

    /**
     * Procesar y registrar la devolución
     */
    public function procesarDevolucion(Request $request, $facturaId)
    {
        $factura = Factura::with(['pedido.detalles.producto'])->findOrFail($facturaId);

        if ($factura->estado === 'anulada') {
            return response()->json([
                'success' => false,
                'message' => 'No es posible procesar una devolución sobre una factura fiscalmente anulada.'
            ], 422);
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.pedido_detalle_id' => 'required|integer',
            'items.*.cantidad' => 'required|numeric|min:0',
            'motivo_general' => 'required|string|max:255',
            'metodo_reembolso' => 'required|string|in:efectivo_usd,efectivo_bs,pago_movil,transferencia,punto_venta,nota_credito,otro',
            'observaciones' => 'nullable|string|max:1000',
            'reingresar_stock' => 'nullable|boolean',
        ], [
            'motivo_general.required' => 'Debe indicar el motivo principal de la devolución.',
            'metodo_reembolso.required' => 'Debe seleccionar el método por el cual se reembolsará el dinero al cliente.',
        ]);

        $itemsDevolver = [];
        $totalReembolsoUsd = 0;
        $tasaBcv = (float)($factura->valor_tasa_bcv ?: 1.0);
        $tasaActiva = TasaCambio::latest('creado_at')->first();
        if ($tasaActiva && $tasaActiva->valor_tasa > 0) {
            $tasaBcv = (float)$tasaActiva->valor_tasa;
        }

        // Validar cantidades de cada ítem
        foreach ($request->items as $itemReq) {
            $cantidad = (float)($itemReq['cantidad'] ?? 0);
            if ($cantidad <= 0) continue;

            $detalle = $factura->pedido ? $factura->pedido->detalles->firstWhere('id', $itemReq['pedido_detalle_id']) : null;
            if (!$detalle) {
                return response()->json([
                    'success' => false,
                    'message' => 'Uno de los productos seleccionados no pertenece a este pedido.'
                ], 422);
            }

            // Calcular cuánto se ha devuelto previamente de este ítem
            $yaDevuelto = (float) DevolucionDetalle::where('pedido_detalle_id', $detalle->id)
                ->whereHas('devolucion', function($q) {
                    $q->where('estado', 'procesada');
                })->sum('cantidad_devuelta');

            $disponible = max(0, (float)$detalle->cantidad_solicitada - $yaDevuelto);

            if ($cantidad > $disponible + 0.0001) {
                return response()->json([
                    'success' => false,
                    'message' => "La cantidad indicada para '{$detalle->producto->nombre}' ({$cantidad}) supera la cantidad disponible por devolver ({$disponible})."
                ], 422);
            }

            $precioUnitario = (float)$detalle->precio_historico_usd;
            $subtotalItemUsd = round($cantidad * $precioUnitario, 2);
            $totalReembolsoUsd += $subtotalItemUsd;

            $itemsDevolver[] = [
                'pedido_detalle_id' => $detalle->id,
                'producto_id' => $detalle->producto_id,
                'inventario_lote_id' => $detalle->inventario_lote_id,
                'cantidad_devuelta' => $cantidad,
                'precio_unitario_usd' => $precioUnitario,
                'monto_subtotal_usd' => $subtotalItemUsd,
                'motivo_devolucion' => $itemReq['motivo_devolucion'] ?? 'cliente_arrepentido',
                'estado_producto' => $itemReq['estado_producto'] ?? 'reincorporado',
                'observaciones' => $itemReq['observaciones_item'] ?? null,
            ];
        }

        if (empty($itemsDevolver) || $totalReembolsoUsd <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Debe ingresar al menos una cantidad válida mayor a cero para procesar la devolución.'
            ], 422);
        }

        $totalReembolsoVes = round($totalReembolsoUsd * $tasaBcv, 2);
        $reingresarStock = (bool) $request->input('reingresar_stock', true);
        $user = Auth::user();

        DB::beginTransaction();
        try {
            // 1. Determinar tipo de devolución (total vs parcial)
            $totalFacturaUsd = (float)$factura->total_usd;
            $totalDevueltoPrevio = (float) Devolucion::where('factura_id', $factura->id)
                ->where('estado', 'procesada')
                ->sum('monto_total_usd');

            $esTotal = ($totalDevueltoPrevio + $totalReembolsoUsd) >= ($totalFacturaUsd - 0.01);
            $tipoDevolucion = $esTotal ? 'total' : 'parcial';

            // 2. Crear cabecera de la devolución
            $codigoDevolucion = Devolucion::generarCodigo();

            $devolucion = Devolucion::create([
                'codigo_devolucion' => $codigoDevolucion,
                'factura_id' => $factura->id,
                'pedido_id' => $factura->pedido_id,
                'cliente_id' => $factura->pedido ? $factura->pedido->usuario_id : null,
                'usuario_id' => $user ? $user->id : 1,
                'tipo_devolucion' => $tipoDevolucion,
                'monto_total_usd' => $totalReembolsoUsd,
                'monto_total_ves' => $totalReembolsoVes,
                'tasa_bcv' => $tasaBcv,
                'metodo_reembolso' => $request->metodo_reembolso,
                'motivo_general' => $request->motivo_general,
                'observaciones' => $request->observaciones,
                'reingresar_stock' => $reingresarStock,
                'estado' => 'procesada',
                'fecha_devolucion' => Carbon::now(),
            ]);

            // 3. Crear detalles y actualizar inventario
            foreach ($itemsDevolver as $itemData) {
                DevolucionDetalle::create([
                    'devolucion_id' => $devolucion->id,
                    'pedido_id' => $factura->pedido_id,
                    'pedido_detalle_id' => $itemData['pedido_detalle_id'],
                    'producto_id' => $itemData['producto_id'],
                    'inventario_lote_id' => $itemData['inventario_lote_id'],
                    'cantidad_devuelta' => $itemData['cantidad_devuelta'],
                    'precio_unitario_usd' => $itemData['precio_unitario_usd'],
                    'monto_subtotal_usd' => $itemData['monto_subtotal_usd'],
                    'motivo_devolucion' => in_array($itemData['motivo_devolucion'], [
                        'producto_danado', 'no_corresponde', 'vencido', 'sobrante', 'error_despacho', 'cliente_arrepentido'
                    ]) ? $itemData['motivo_devolucion'] : 'cliente_arrepentido',
                    'estado_producto' => in_array($itemData['estado_producto'], [
                        'recibido', 'en_verificacion', 'reincorporado', 'descarte', 'devuelto_proveedor'
                    ]) ? $itemData['estado_producto'] : 'reincorporado',
                    'costo_reposicion_usd' => $itemData['precio_unitario_usd'],
                    'observaciones' => $itemData['observaciones'],
                    'usuario_recibe_id' => $user ? $user->id : 1,
                    'fecha_recepcion' => Carbon::now(),
                    'usuario_procesa_id' => $user ? $user->id : 1,
                    'fecha_procesamiento' => Carbon::now(),
                ]);

                // Reingresar stock al catálogo si está marcado y el estado no es descarte
                if ($reingresarStock && in_array($itemData['estado_producto'], ['reincorporado', 'recibido'])) {
                    if ($itemData['producto_id']) {
                        Producto::where('id', $itemData['producto_id'])
                            ->increment('stock_total', $itemData['cantidad_devuelta']);
                    }

                    if ($itemData['inventario_lote_id']) {
                        InventarioLote::where('id', $itemData['inventario_lote_id'])
                            ->increment('cantidad_restante', $itemData['cantidad_devuelta']);
                    }
                }
            }

            // 4. Actualizar estado de la factura a 'reembolsada'
            $factura->estado = 'reembolsada';
            $factura->save();

            // 5. Si hay una sesión de caja activa del usuario y el reembolso es en efectivo, registrar egreso
            if ($user && in_array($request->metodo_reembolso, ['efectivo_usd', 'efectivo_bs'])) {
                try {
                    $sesionCaja = SesionCaja::where('usuario_id', $user->id)
                        ->where('estado', 'abierta')
                        ->first();

                    if ($sesionCaja && class_exists(MovimientoCaja::class)) {
                        $moneda = $request->metodo_reembolso === 'efectivo_usd' ? 'USD' : 'VES';
                        $montoMov = $moneda === 'USD' ? $totalReembolsoUsd : $totalReembolsoVes;

                        MovimientoCaja::create([
                            'sesion_caja_id' => $sesionCaja->id,
                            'tipo' => 'egreso',
                            'concepto' => "Reembolso {$codigoDevolucion} (Factura {$factura->numero_factura})",
                            'monto' => $montoMov,
                            'moneda' => $moneda,
                            'usuario_id' => $user->id,
                            'fecha' => Carbon::now(),
                        ]);
                    }
                } catch (\Throwable $e) {
                    // No bloquea la devolución si la caja no está en uso
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Devolución {$codigoDevolucion} procesada exitosamente.",
                'devolucion' => [
                    'id' => $devolucion->id,
                    'codigo' => $codigoDevolucion,
                    'monto_usd' => $totalReembolsoUsd,
                    'monto_ves' => $totalReembolsoVes,
                    'tipo' => $tipoDevolucion,
                    'metodo' => $devolucion->metodo_reembolso,
                ]
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al registrar la devolución: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Ver detalle completo de una devolución (Modal o JSON)
     */
    public function show($id)
    {
        $devolucion = Devolucion::with([
            'factura',
            'pedido.usuario',
            'usuario',
            'detalles.producto',
            'detalles.lote'
        ])->findOrFail($id);

        $config = ConfiguracionTienda::first();

        return view('admin.devoluciones.partials._detalle', compact('devolucion', 'config'))->render();
    }

    /**
     * Comprobante oficial de Devolución para impresión (Formato Térmico 80mm)
     */
    public function comprobante($id)
    {
        $devolucion = Devolucion::with([
            'factura',
            'pedido.usuario',
            'usuario',
            'detalles.producto'
        ])->findOrFail($id);

        $config = ConfiguracionTienda::first();

        return view('admin.devoluciones.comprobante', compact('devolucion', 'config'));
    }
}
