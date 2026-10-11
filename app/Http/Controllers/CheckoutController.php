<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File; // Añadimos esta importación
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\PedidoRecibidoMail;
use App\Models\Carrito;
use App\Models\Pedido;
use App\Models\Pago;
use App\Models\TasaCambio;
use App\Models\CuentaBancaria;
use App\Models\HorarioSemanal;
use App\Models\ZonaDelivery;
use App\Models\ConfiguracionTienda;

class CheckoutController extends Controller
{
    // Definimos la carpeta donde se guardarán los comprobantes
    private $uploadFolder = 'pagos';

    public function index()
    {
        // 1. Barrera de Horario Comercial: Bloquear checkout fuera de horas de apertura
        if (!HorarioSemanal::estaAbiertoAhora()) {
            $estadoHorario = HorarioSemanal::obtenerEstadoHorarioHoy();
            $mensaje = "Nuestra tienda se encuentra cerrada en este momento ({$estadoHorario['mostrar']}). Las compras en línea solo están permitidas dentro del horario comercial.";
            return redirect()->route('carrito.index')->with('tienda_cerrada', $mensaje);
        }

        $usuario = Auth::user();
        
        // Cargamos carrito con la relación 'producto'
        $carrito = Carrito::where('usuario_id', $usuario->id)->with('producto')->get();
        
        if ($carrito->isEmpty()) {
            return redirect()->route('carrito.index')->with('error', 'Tu carrito está vacío.');
        }

        // Cálculos
        $subtotal = 0;
        foreach ($carrito as $item) {
            $precio = $item->producto->precio_oferta_usd ?: $item->producto->precio_venta_usd;
            $subtotal += $precio * $item->cantidad;
        }
        
        $config = ConfiguracionTienda::first();
        $ivaPorcentaje = $config ? (float) $config->iva_porcentaje : 16.00;
        $deliveryActivo = $config ? (bool) ($config->delivery_activo ?? true) : true;
        
        $montoIva = $subtotal * ($ivaPorcentaje / 100);
        $totalUsd = $subtotal + $montoIva;

        $tasa = TasaCambio::obtenerTasaUSD();
        $tasaValor = $tasa ? (float) $tasa->valor_tasa : 60.00;
        $totalVes = $totalUsd * $tasaValor;

        // Cargar y evaluar cobertura geográfica para cada dirección del usuario
        $direcciones = $usuario->direcciones()->with('zonaDelivery')->get();
        foreach ($direcciones as $dir) {
            $zonaCalculada = null;
            $distanciaKm = null;
            $costoFlete = 0;
            $estaEnRadio = false;

            if ($dir->geo_latitud !== null && $dir->geo_longitud !== null) {
                $zonaCalculada = ZonaDelivery::encontrarZonaParaCoordenadas($dir->geo_latitud, $dir->geo_longitud);
                if ($zonaCalculada) {
                    $estaEnRadio = true;
                    $costoFlete = (float) $zonaCalculada->precio_delivery_usd;
                    $distanciaKm = round(ZonaDelivery::calcularDistanciaKm(
                        $dir->geo_latitud,
                        $dir->geo_longitud,
                        $zonaCalculada->latitud,
                        $zonaCalculada->longitud
                    ), 1);
                }
            } elseif ($dir->zonaDelivery && $dir->zonaDelivery->activa) {
                // Fallback si la dirección guardó una zona activa previa sin GPS
                $zonaCalculada = $dir->zonaDelivery;
                $estaEnRadio = true;
                $costoFlete = (float) $zonaCalculada->precio_delivery_usd;
            }

            $dir->cobertura_valida = $estaEnRadio;
            $dir->zona_calculada = $zonaCalculada;
            $dir->costo_delivery_calculado = $costoFlete;
            $dir->distancia_km_calculada = $distanciaKm;
        }
        
        // Cuentas bancarias activas
        $metodosPago = CuentaBancaria::where('activo', 1)->get();

        $zonas = ZonaDelivery::where('activa', 1)->get();
        
        return view('checkout.index', compact(
            'carrito', 'subtotal', 'montoIva', 'totalUsd', 'totalVes', 
            'tasaValor', 'direcciones', 'metodosPago', 'zonas', 'deliveryActivo', 'ivaPorcentaje'
        ));
    }

    public function store(Request $request)
    {
        // 1. Barrera de Horario Comercial en el servidor
        if (!HorarioSemanal::estaAbiertoAhora()) {
            $estadoHorario = HorarioSemanal::obtenerEstadoHorarioHoy();
            return back()->with('error', "No es posible procesar la orden: la tienda está cerrada actualmente ({$estadoHorario['mostrar']}).");
        }

        $request->validate([
            'metodo_entrega' => 'required|in:delivery,pickup',
            'direccion_id' => 'required_if:metodo_entrega,delivery|nullable|exists:direcciones_usuarios,id',
            'metodo_pago_id' => 'required|exists:cuentas_bancarias,id', 
            'referencia' => 'nullable|string|max:100',
            'comprobante' => 'nullable|image|max:5120' 
        ], [
            'comprobante.max' => 'La imagen del comprobante no debe pesar más de 5MB.',
            'comprobante.image' => 'El archivo debe ser una imagen (JPG, PNG, JPEG).'
        ]);

        // 2. Buscamos la cuenta bancaria real
        $cuentaBancaria = CuentaBancaria::findOrFail($request->metodo_pago_id);
        $tipoMetodoEnum = $cuentaBancaria->tipo_metodo;

        $usuario = Auth::user();

        // 3. Validar si el Delivery está activo por el admin
        if ($request->metodo_entrega === 'delivery' && !ConfiguracionTienda::deliveryHabilitado()) {
            return back()->with('error', 'El servicio de delivery se encuentra actualmente pausado por la administración. Por favor selecciona "Retiro en Sede".');
        }
        
        return DB::transaction(function () use ($request, $usuario, $tipoMetodoEnum) {
            
            // 4. Validar Stock
            $carrito = Carrito::where('usuario_id', $usuario->id)->lockForUpdate()->get();
            if ($carrito->isEmpty()) throw new \Exception('El carrito está vacío.');

            $subtotal = 0;
            foreach ($carrito as $item) {
                if ($item->producto->stock_total < $item->cantidad) {
                    throw new \Exception("Stock insuficiente para: " . $item->producto->nombre);
                }
                $precio = $item->producto->precio_oferta_usd ?: $item->producto->precio_venta_usd;
                $subtotal += $precio * $item->cantidad;
            }

            // 5. Determinar Costo de Envío y Validar Radio de Cobertura
            $config = ConfiguracionTienda::first();
            $ivaPct = $config ? (float) $config->iva_porcentaje : 16.00;
            
            $costoDelivery = 0;
            $zonaId = null;
            $direccionTexto = 'Retiro en Sede';
            $lat = null;
            $lng = null;

            if ($request->metodo_entrega === 'delivery') {
                $dir = DB::table('direcciones_usuarios')
                    ->where('id', $request->direccion_id)
                    ->where('usuario_id', $usuario->id)
                    ->first();

                if (!$dir) {
                    throw new \Exception('La dirección seleccionada no es válida para este usuario.');
                }

                // Evaluar si las coordenadas están dentro del radio de alguna zona activa
                $zona = null;
                if ($dir->geo_latitud !== null && $dir->geo_longitud !== null) {
                    $zona = ZonaDelivery::encontrarZonaParaCoordenadas($dir->geo_latitud, $dir->geo_longitud);
                } elseif (!empty($dir->zona_delivery_id)) {
                    $zona = ZonaDelivery::where('id', $dir->zona_delivery_id)->where('activa', 1)->first();
                }

                if (!$zona) {
                    throw new \Exception('Tu dirección se encuentra fuera de nuestro radio de cobertura de delivery. No podemos despachar a esa ubicación. Por favor selecciona "Retiro en Sede".');
                }

                $direccionTexto = $dir->direccion_texto . ($dir->referencia_punto ? " (Ref: " . $dir->referencia_punto . ")" : "");
                $lat = $dir->geo_latitud;
                $lng = $dir->geo_longitud;
                $zonaId = $zona->id;
                $costoDelivery = (float) $zona->precio_delivery_usd;
            }

            $totalUsd = $subtotal + ($subtotal * ($ivaPct / 100)) + $costoDelivery;
            
            $tasa = TasaCambio::obtenerTasaUSD();
            $tasaValor = $tasa ? (float) $tasa->valor_tasa : 60.00;
            $tasaId = $tasa ? $tasa->id : null;
            
            $totalVes = $totalUsd * $tasaValor;

            // 6. Crear Pedido
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
                    'precio_historico_usd' => ($item->producto->precio_oferta_usd ?: $item->producto->precio_venta_usd),
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

            // 8. Enviar correo de confirmación de orden recibida al cliente
            try {
                if ($usuario && filter_var($usuario->email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($usuario->email)->send(new PedidoRecibidoMail($pedido));
                }
            } catch (\Throwable $mailEx) {
                Log::warning("Checkout: No se pudo enviar correo de pedido recibido para orden #{$pedido->id}: " . $mailEx->getMessage());
            }

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