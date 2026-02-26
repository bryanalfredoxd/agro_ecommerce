<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File; // Añadimos esta importación
use App\Models\Carrito;
use App\Models\Pedido;
use App\Models\Pago;
use App\Models\TasaCambio;
use App\Models\CuentaBancaria;

class CheckoutController extends Controller
{
    // Definimos la carpeta donde se guardarán los comprobantes
    private $uploadFolder = 'pagos';

    public function index()
    {
        $usuario = Auth::user();
        
        // Cargamos carrito con la relación 'producto'
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
        
        // Cuentas activas
        $metodosPago = CuentaBancaria::where('activo', 1)->get();

        $zonas = DB::table('zonas_delivery')->where('activa', 1)->get();
        
        return view('checkout.index', compact('carrito', 'subtotal', 'montoIva', 'totalUsd', 'totalVes', 'tasaValor', 'direcciones', 'metodosPago', 'zonas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'metodo_entrega' => 'required|in:delivery,pickup',
            'direccion_id' => 'required_if:metodo_entrega,delivery|nullable|exists:direcciones_usuarios,id',
            'metodo_pago_id' => 'required|exists:cuentas_bancarias,id', 
            'referencia' => 'nullable|string|max:100',
            
            // --- CAMBIO DE SEGURIDAD AQUÍ ---
            // Aumentamos a 5MB (5120 KB) para aceptar fotos de celulares modernos sin problemas.
            // Si suben algo mayor a 5MB, Laravel lo bloquea automáticamente.
            'comprobante' => 'nullable|image|max:5120' 
        ], [
            'comprobante.max' => 'La imagen del comprobante no debe pesar más de 5MB.',
            'comprobante.image' => 'El archivo debe ser una imagen (JPG, PNG, JPEG).'
        ]);

        // 1. Buscamos la cuenta bancaria real
        $cuentaBancaria = CuentaBancaria::findOrFail($request->metodo_pago_id);
        $tipoMetodoEnum = $cuentaBancaria->tipo_metodo;

        $usuario = Auth::user();
        
        return DB::transaction(function () use ($request, $usuario, $tipoMetodoEnum) {
            
            // 2. Validar Stock (Bloqueo para evitar concurrencia)
            $carrito = Carrito::where('usuario_id', $usuario->id)->lockForUpdate()->get();
            if ($carrito->isEmpty()) throw new \Exception('El carrito está vacío.');

            $subtotal = 0;
            foreach ($carrito as $item) {
                if ($item->producto->stock_total < $item->cantidad) {
                    throw new \Exception("Stock insuficiente para: " . $item->producto->nombre);
                }
                $subtotal += $item->producto->precio_venta_usd * $item->cantidad;
            }

            // 3. Calcular Totales Finales
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

            // 4. Crear Pedido
            $pedido = Pedido::create([
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

            // 5. Guardar Detalles
            foreach ($carrito as $item) {
                DB::table('pedido_detalles')->insert([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $item->producto_id,
                    'cantidad_solicitada' => $item->cantidad,
                    'precio_historico_usd' => $item->producto->precio_venta_usd,
                ]);
            }

            // 6. Subir Imagen y Registrar Pago (CAMBIO IMPORTANTE AQUÍ)
            $rutaCapture = null;
            if ($request->hasFile('comprobante')) {
                // Usamos nuestro método personalizado en lugar de store()
                $rutaCapture = $this->uploadImage($request->file('comprobante'));
            }

            Pago::create([
                'pedido_id' => $pedido->id,
                'metodo' => $tipoMetodoEnum,
                'monto_usd' => $totalUsd,
                'monto_ves' => $totalVes,
                'referencia_bancaria' => $request->referencia,
                'captura_pago_url' => $rutaCapture,
                'estado' => 'revision'
            ]);

            // 7. Limpiar Carrito
            Carrito::where('usuario_id', $usuario->id)->delete();

            return redirect()->route('perfil.pedidos')->with('success', '¡Pedido #' . $pedido->id . ' registrado correctamente! En breve verificaremos tu pago.');

        });
    }

    /**
     * Método privado para subir imágenes a public/img/upload/pagos/
     */
    private function uploadImage($file)
    {
        // Definir la carpeta de destino
        $destinationPath = public_path('img/upload/' . $this->uploadFolder);
        
        // Crear la carpeta si no existe
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }
        
        // Generar nombre único para la imagen
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        // Mover el archivo a la carpeta destino
        $file->move($destinationPath, $fileName);
        
        // Retornar la ruta relativa para guardar en BD
        return 'img/upload/' . $this->uploadFolder . '/' . $fileName;
    }

    /**
     * Método privado para eliminar imágenes (opcional, por si necesitas borrar comprobantes)
     */
    private function deleteImage($imagePath)
    {
        $fullPath = public_path($imagePath);
        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}