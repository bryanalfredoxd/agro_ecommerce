<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CajaFisica;
use App\Models\SesionCaja;
use App\Models\Producto;
use App\Models\TasaCambio;
use App\Models\ConfiguracionTienda; // <-- Asegúrate de importar esto
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $sesionActiva = SesionCaja::where('cajero_usuario_id', Auth::id())
                                  ->whereNull('fecha_cierre')
                                  ->first();

        if (!$sesionActiva) {
            if(CajaFisica::count() == 0) {
                CajaFisica::create(['nombre' => 'Caja Principal 01', 'activa' => 1]);
            }
            $cajas = CajaFisica::where('activa', 1)->get();
            return view('admin.pos.apertura', compact('cajas'));
        }

        $tasaCambio = TasaCambio::latest('creado_at')->first();
        $valorTasa = $tasaCambio ? $tasaCambio->valor_tasa : 1; 

        // Traemos el IVA desde tu configuración (Si no existe, asume 16)
        $config = ConfiguracionTienda::first();
        $porcentajeIva = $config ? $config->iva_porcentaje : 16.00;

        return view('admin.pos.terminal', compact('sesionActiva', 'valorTasa', 'porcentajeIva'));
    }

    public function abrirCaja(Request $request)
    {
        $request->validate([
            'caja_id' => 'required|exists:cajas_fisicas,id',
            'monto_inicial_usd' => 'required|numeric|min:0'
        ]);

        $existe = SesionCaja::where('cajero_usuario_id', Auth::id())->whereNull('fecha_cierre')->exists();
        if ($existe) {
            return redirect()->route('admin.pos.index')->with('error', 'Ya tienes una sesión de caja abierta.');
        }

        SesionCaja::create([
            'caja_id' => $request->caja_id,
            'cajero_usuario_id' => Auth::id(),
            'fecha_apertura' => now(),
            'monto_inicial_usd' => $request->monto_inicial_usd
        ]);

        return redirect()->route('admin.pos.index')->with('success', 'Caja abierta exitosamente.');
    }

    // BÚSQUEDA DE PRODUCTOS (Traemos Categoría para la Tarjeta)
    public function buscarProducto(Request $request)
    {
        $busqueda = $request->buscar;
        
        $exactMatch = Producto::with('categoria')->where('codigo_barras', $busqueda)
                              ->orWhere('sku', $busqueda)->first();

        if ($exactMatch && $request->has('exact')) {
            return response()->json(['exact' => true, 'producto' => $exactMatch]);
        }

        $productos = Producto::with('categoria')
                             ->where(function($q) use ($busqueda) {
                                 $q->where('nombre', 'LIKE', "%{$busqueda}%")
                                   ->orWhere('codigo_barras', 'LIKE', "%{$busqueda}%")
                                   ->orWhere('sku', 'LIKE', "%{$busqueda}%");
                             })
                             ->whereNull('eliminado_at')
                             ->where('stock_total', '>', 0)
                             ->limit(12)
                             ->get();

        return response()->json(['exact' => false, 'productos' => $productos]);
    }

    // PROCESAR LA VENTA (Con IVA y Referencias)
    public function procesarVenta(Request $request)
    {
        $request->validate([
            'metodo_pago' => 'required|in:efectivo_usd,efectivo_bs,punto_venta,pago_movil,zelle,binance,transferencia',
            'referencia_pago' => 'nullable|string|max:100', // Nueva Validación
            'carrito' => 'required|array',
            'carrito.*.id' => 'required|exists:productos,id',
            'carrito.*.cantidad' => 'required|numeric|min:0.01'
        ]);

        try {
            DB::beginTransaction();

            $sesion = SesionCaja::where('cajero_usuario_id', Auth::id())->whereNull('fecha_cierre')->firstOrFail();
            
            $tasaCambio = TasaCambio::latest('creado_at')->first();
            $tasa = $tasaCambio ? $tasaCambio->valor_tasa : 1;

            $config = ConfiguracionTienda::first();
            $porcentajeIva = $config ? $config->iva_porcentaje : 16.00;

            // 1. Calcular Subtotal
            $subtotal_usd = 0;
            foreach ($request->carrito as $item) {
                $producto = Producto::findOrFail($item['id']);
                if ($producto->stock_total < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para: {$producto->nombre}");
                }
                $precio = $producto->precio_oferta_usd ?: $producto->precio_venta_usd;
                $subtotal_usd += ($precio * $item['cantidad']);
            }

            // 2. Calcular IVA y Totales
            $iva_usd = $subtotal_usd * ($porcentajeIva / 100);
            $gran_total_usd = $subtotal_usd + $iva_usd;
            $gran_total_ves = $gran_total_usd * $tasa;

            // 3. Crear el Pedido (NACERÁ COMO PENDIENTE)
            $pedido = \App\Models\Pedido::create([
                'canal_venta' => 'tienda_fisica',
                'sesion_caja_id' => $sesion->id,
                'tasa_cambio_id' => $tasaCambio ? $tasaCambio->id : null,
                'subtotal_usd' => $subtotal_usd,
                'costo_delivery_usd' => 0,
                'descuento_usd' => 0,
                'total_usd' => $gran_total_usd,
                'total_ves_calculado' => $gran_total_ves,
                'estado' => 'pendiente' // <--- CAMBIO CRÍTICO: Nace en pendiente
            ]);

            // 4. Insertar Detalles del Pedido
            foreach ($request->carrito as $item) {
                $producto = Producto::find($item['id']);
                $precio = $producto->precio_oferta_usd ?: $producto->precio_venta_usd;

                \App\Models\DetallePedido::create([ 
                    'pedido_id' => $pedido->id,
                    'producto_id' => $producto->id,
                    'cantidad_solicitada' => $item['cantidad'],
                    'cantidad_real_despachada' => $item['cantidad'],
                    'precio_historico_usd' => $precio
                ]);
            }

            // 4.5. ¡MAGIA! AHORA DESPERTAMOS AL TRIGGER ACTUALIZANDO EL ESTADO
            // Ya que los productos están guardados, cambiamos el estado para que el Trigger lea el UPDATE y descuente todo.
            $pedido->estado = 'completado_caja';
            $pedido->save();

            // 5. Crear el Pago (Guardando la Referencia)
            \App\Models\Pago::create([
                'pedido_id' => $pedido->id,
                'metodo' => $request->metodo_pago,
                'referencia_bancaria' => $request->referencia_pago, // Guardamos Ref
                'monto_usd' => $gran_total_usd,
                'monto_ves' => $gran_total_ves,
                'estado' => 'aprobado',
                'verificado_por_usuario_id' => Auth::id()
            ]);

            // 6. Registrar Movimiento (Ahora siempre guarda los montos)
            \App\Models\MovimientoCaja::create([
                'sesion_caja_id' => $sesion->id,
                'tipo' => 'ingreso',
                'motivo' => 'Venta POS Pedido #' . $pedido->id,
                'monto_usd' => $gran_total_usd,
                'monto_ves' => $gran_total_ves,
            ]);

            // 7. Actualizar totales de la sesión actual
            $sesion->total_ventas_sistema_usd += $gran_total_usd;
            $sesion->total_ventas_sistema_ves += $gran_total_ves;
            $sesion->save();

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Venta procesada con éxito.',
                'pedido_id' => $pedido->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // NUEVO: CERRAR CAJA Y LLENAR TABLA SESIONES_CAJA
    public function cerrarCaja(Request $request)
    {
        $request->validate([
            'dinero_real_usd' => 'required|numeric|min:0'
        ]);

        $sesion = SesionCaja::where('cajero_usuario_id', Auth::id())->whereNull('fecha_cierre')->firstOrFail();
        
        // Lo que el sistema dice que deberías tener (Monto Inicial + Todas las ventas)
        $esperado = $sesion->monto_inicial_usd + $sesion->total_ventas_sistema_usd;
        
        // Si ingresas $100 pero esperabas $120, la diferencia es -$20 (Faltante)
        $diferencia = $request->dinero_real_usd - $esperado;

        $sesion->update([
            'fecha_cierre' => now(),
            'dinero_real_en_caja_usd' => $request->dinero_real_usd,
            'diferencia_usd' => $diferencia,
            'observaciones_cierre' => $request->observaciones
        ]);

        return response()->json(['success' => true, 'message' => 'Caja cerrada correctamente.']);
    }
}