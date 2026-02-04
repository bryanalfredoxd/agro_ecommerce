<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // Necesario para generar el código del pedido
use App\Models\Carrito;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Pago;
use App\Models\TasaCambio;
use App\Models\CuentaBancaria; // Tu nuevo modelo

class CheckoutController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        
        $carrito = Carrito::where('usuario_id', $usuario->id)->with('producto')->get();
        
        if ($carrito->isEmpty()) {
            return redirect()->route('carrito.index')->with('error', 'Tu carrito está vacío.');
        }

        // Cálculos
        $subtotal = 0;
        foreach ($carrito as $item) {
            $subtotal += $item->producto->precio_venta_usd * $item->cantidad;
        }
        
        $config = DB::table('configuracion_tienda')->first();
        $ivaPorcentaje = $config ? $config->iva_porcentaje : 16.00;
        $montoIva = $subtotal * ($ivaPorcentaje / 100);
        $totalUsd = $subtotal + $montoIva;

        $tasa = TasaCambio::obtenerTasaUSD();
        $tasaValor = $tasa ? $tasa->valor_tasa : 60.00;
        $totalVes = $totalUsd * $tasaValor;

        $direcciones = $usuario->direcciones;
        
        // Obtenemos las cuentas activas desde la base de datos
        $metodosPago = CuentaBancaria::where('activo', 1)->get();

        $zonas = DB::table('zonas_delivery')->where('activa', 1)->get();
        
        return view('checkout.index', compact('carrito', 'subtotal', 'montoIva', 'totalUsd', 'totalVes', 'tasaValor', 'direcciones', 'metodosPago', 'zonas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'metodo_entrega' => 'required|in:delivery,pickup',
            'direccion_id' => 'required_if:metodo_entrega,delivery|nullable|exists:direcciones_usuarios,id',
            // Aquí validamos que el ID enviado exista en la tabla cuentas_bancarias
            'metodo_pago_id' => 'required|exists:cuentas_bancarias,id', 
            'referencia' => 'nullable|string|max:100',
            'comprobante' => 'nullable|image|max:2048'
        ]);

        // --- EL PUENTE ---
        // 1. Buscamos la cuenta bancaria real usando el ID del formulario
        $cuentaBancaria = CuentaBancaria::findOrFail($request->metodo_pago_id);
        
        // 2. Extraemos el 'tipo_metodo' (ej: 'pago_movil', 'zelle')
        // Este valor SÍ es compatible con el ENUM de tu tabla 'pagos'
        $tipoMetodoEnum = $cuentaBancaria->tipo_metodo;

        $usuario = Auth::user();
        
        return DB::transaction(function () use ($request, $usuario, $tipoMetodoEnum) {
            
            // 1. Validar Stock
            $carrito = Carrito::where('usuario_id', $usuario->id)->lockForUpdate()->get();
            if ($carrito->isEmpty()) throw new \Exception('El carrito está vacío.');

            $subtotal = 0;
            foreach ($carrito as $item) {
                if ($item->producto->stock_total < $item->cantidad) {
                    throw new \Exception("Stock insuficiente para: " . $item->producto->nombre);
                }
                $subtotal += $item->producto->precio_venta_usd * $item->cantidad;
            }

            // 2. Calcular Totales
            $config = DB::table('configuracion_tienda')->first();
            $ivaPct = $config ? $config->iva_porcentaje : 16.00;
            
            $costoDelivery = 0;
            $zonaId = null;
            $direccionTexto = 'Retiro en Tienda';
            $lat = null;
            $lng = null;

            if ($request->metodo_entrega === 'delivery') {
                $dir = DB::table('direcciones_usuarios')->where('id', $request->direccion_id)->first();
                if ($dir) {
                    $direccionTexto = $dir->direccion_texto . " (Ref: " . $dir->referencia_punto . ")";
                    $lat = $dir->geo_latitud;
                    $lng = $dir->geo_longitud;
                    
                    $zona = DB::table('zonas_delivery')->first(); 
                    if($zona) {
                        $zonaId = $zona->id;
                        $costoDelivery = $zona->precio_delivery_usd;
                    }
                }
            }

            $totalUsd = $subtotal + ($subtotal * ($ivaPct / 100)) + $costoDelivery;
            
            $tasa = TasaCambio::obtenerTasaUSD();
            $tasaValor = $tasa ? $tasa->valor_tasa : 60.00;
            $tasaId = $tasa ? $tasa->id : null;
            
            $totalVes = $totalUsd * $tasaValor;

            // 3. Crear Pedido
            $pedido = Pedido::create([
                // 'codigo_pedido' => ... (Tu tabla NO tiene esta columna según el SQL, usamos ID)
                'canal_venta' => 'web',
                'usuario_id' => $usuario->id,
                'zona_delivery_id' => $zonaId,
                'tasa_cambio_id' => $tasaId,
                'subtotal_usd' => $subtotal,
                'costo_delivery_usd' => $costoDelivery,
                'descuento_usd' => 0,
                'total_usd' => $totalUsd,
                'total_ves_calculado' => $totalVes,
                'estado' => 'pendiente',
                'direccion_texto' => $direccionTexto,
                'geo_latitud' => $lat,
                'geo_longitud' => $lng,
                'instrucciones_entrega' => $request->observaciones
            ]);

            // 4. Guardar Detalles
            foreach ($carrito as $item) {
                DB::table('pedido_detalles')->insert([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $item->producto_id,
                    'cantidad_solicitada' => $item->cantidad,
                    'precio_historico_usd' => $item->producto->precio_venta_usd,
                ]);
            }

            // 5. Registrar Pago
            $rutaCapture = null;
            if ($request->hasFile('comprobante')) {
                $rutaCapture = $request->file('comprobante')->store('pagos', 'public');
            }

            Pago::create([
                'pedido_id' => $pedido->id,
                'metodo' => $tipoMetodoEnum, // <--- AQUÍ USAMOS EL ENUM 'pago_movil' CORRECTO
                'monto_usd' => $totalUsd,
                'monto_ves' => $totalVes,
                'referencia_bancaria' => $request->referencia,
                'captura_pago_url' => $rutaCapture,
                'estado' => 'revision'
            ]);

            // 6. Limpiar Carrito
            Carrito::where('usuario_id', $usuario->id)->delete();

            return redirect()->route('perfil')->with('success', '¡Pedido #' . $pedido->id . ' registrado correctamente! En breve verificaremos tu pago.');

        });
    }
}