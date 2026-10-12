<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CajaFisica;
use App\Models\SesionCaja;
use App\Models\Producto;
use App\Models\TasaCambio;
use App\Models\ConfiguracionTienda;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Pago;
use App\Models\MovimientoCaja;
use App\Models\Factura;
use App\Models\User;
use App\Mail\FacturaTicketMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PosController extends Controller
{
    public function index()
    {
        $sesionActiva = SesionCaja::where('cajero_usuario_id', Auth::id())
                                  ->whereNull('fecha_cierre')
                                  ->first();

        if (!$sesionActiva) {
            $user = Auth::user();
            if (CajaFisica::count() == 0) {
                CajaFisica::create(['nombre' => 'Caja Principal 01', 'activa' => 1]);
            }
            
            $cajas = CajaFisica::with(['cajeroAsignado', 'sesionActiva.cajero'])
                               ->where('activa', 1)
                               ->get();

            // Verificar si el usuario tiene una caja asignada específicamente
            $cajaAsignada = null;
            if ($user) {
                $cajaAsignada = CajaFisica::with(['cajeroAsignado', 'sesionActiva.cajero'])
                                          ->where('activa', 1)
                                          ->where('cajero_asignado_id', $user->id)
                                          ->first();
            }

            return view('admin.pos.apertura', compact('cajas', 'cajaAsignada'));
        }

        $tasaCambio = TasaCambio::latest('creado_at')->first();
        $valorTasa = $tasaCambio ? (float)$tasaCambio->valor_tasa : 1; 

        // Traemos el IVA desde tu configuración (Si no existe, asume 16)
        $config = ConfiguracionTienda::first();
        $porcentajeIva = $config ? (float)$config->iva_porcentaje : 16.00;

        // Productos iniciales precargados y categorías activas
        $productosIniciales = Producto::with('categoria')
                                      ->whereNull('eliminado_at')
                                      ->where('stock_total', '>', 0)
                                      ->limit(50)
                                      ->get();

        $categorias = \App\Models\Categoria::whereNull('eliminado_at')->get();
        $recargosPos = ConfiguracionTienda::recargos('pos');

        return view('admin.pos.terminal', compact('sesionActiva', 'valorTasa', 'porcentajeIva', 'productosIniciales', 'categorias', 'recargosPos'));
    }

    public function abrirCaja(Request $request)
    {
        $request->validate([
            'caja_id' => 'required|exists:cajas_fisicas,id',
            'monto_inicial_usd' => 'required|numeric|min:0'
        ]);

        $user = Auth::user();
        $caja = CajaFisica::findOrFail($request->caja_id);

        if (!$caja->activa) {
            return redirect()->route('admin.pos.index')->with('error', 'La caja seleccionada se encuentra inactiva.');
        }

        // Si tiene asignada otra caja y no es admin
        if (!$user->esAdmin() && $user->cajasAsignadas()->exists()) {
            $asignada = $user->cajasAsignadas()->first();
            if ($asignada && $asignada->id != $caja->id) {
                return redirect()->route('admin.pos.index')->with('error', "Solo estás autorizado para abrir tu caja asignada: {$asignada->nombre}.");
            }
        }

        // Validar que la caja no esté ocupada por otro cajero en este momento
        $cajaOcupada = SesionCaja::where('caja_id', $caja->id)
                                ->whereNull('fecha_cierre')
                                ->with('cajero')
                                ->first();
        if ($cajaOcupada) {
            $nombreCajero = $cajaOcupada->cajero ? $cajaOcupada->cajero->nombre : 'otro cajero';
            return redirect()->route('admin.pos.index')->with('error', "La {$caja->nombre} ya tiene un turno abierto por {$nombreCajero}.");
        }

        // Validar que el usuario no tenga ya otra sesión abierta
        $existe = SesionCaja::where('cajero_usuario_id', $user->id)->whereNull('fecha_cierre')->exists();
        if ($existe) {
            return redirect()->route('admin.pos.index')->with('error', 'Ya tienes una sesión de caja abierta.');
        }

        SesionCaja::create([
            'caja_id' => $caja->id,
            'cajero_usuario_id' => $user->id,
            'fecha_apertura' => now(),
            'monto_inicial_usd' => $request->monto_inicial_usd
        ]);

        return redirect()->route('admin.pos.index')->with('success', 'Turno iniciado exitosamente.');
    }

    // BÚSQUEDA DE PRODUCTOS
    public function buscarProducto(Request $request)
    {
        $busqueda = trim($request->input('buscar', ''));
        $categoriaId = $request->input('categoria_id');
        
        if (!empty($busqueda)) {
            $exactMatch = Producto::with('categoria')->where(function($q) use ($busqueda) {
                $q->where('codigo_barras', $busqueda)
                  ->orWhere('sku', $busqueda);
            })->whereNull('eliminado_at')->first();

            if ($exactMatch && $request->boolean('exact')) {
                return response()->json(['exact' => true, 'producto' => $exactMatch]);
            }
        }

        $query = Producto::with('categoria')->whereNull('eliminado_at')->where('stock_total', '>', 0);

        if (!empty($busqueda)) {
            $query->where(function($q) use ($busqueda) {
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('codigo_barras', 'LIKE', "%{$busqueda}%")
                  ->orWhere('sku', 'LIKE', "%{$busqueda}%");
            });
        }

        if (!empty($categoriaId)) {
            $query->where('categoria_id', $categoriaId);
        }

        $productos = $query->limit(80)->get();

        return response()->json(['exact' => false, 'productos' => $productos]);
    }

    // BÚSQUEDA / AUTOCOMPLETADO DE CLIENTE
    public function buscarCliente(Request $request)
    {
        $busqueda = trim($request->input('buscar', ''));
        if (strlen($busqueda) < 2) {
            return response()->json(['clientes' => []]);
        }

        // 1. Buscar en tabla usuarios
        $usuarios = User::where(function($q) use ($busqueda) {
            $q->where('documento_identidad', 'LIKE', "%{$busqueda}%")
              ->orWhere('nombre', 'LIKE', "%{$busqueda}%")
              ->orWhere('apellido', 'LIKE', "%{$busqueda}%")
              ->orWhere('email', 'LIKE', "%{$busqueda}%")
              ->orWhere('telefono', 'LIKE', "%{$busqueda}%");
        })->limit(5)->get()->map(function($u) {
            return [
                'documento' => $u->documento_identidad ?: '',
                'nombre' => trim($u->nombre . ' ' . ($u->apellido ?? '')),
                'email' => $u->email ?: '',
                'telefono' => $u->telefono ?: '',
                'direccion' => '',
                'es_parcial' => empty($u->password_hash),
            ];
        });

        // 2. Buscar en facturas anteriores emitidas en mostrador
        $facturas = Factura::where(function($q) use ($busqueda) {
            $q->where('cedula_rif_cliente', 'LIKE', "%{$busqueda}%")
              ->orWhere('nombre_razon_social', 'LIKE', "%{$busqueda}%");
        })
        ->where('cedula_rif_cliente', '!=', 'V-00000000')
        ->where('nombre_razon_social', '!=', 'Consumidor Final')
        ->with('pedido.usuario')
        ->latest('id')
        ->limit(5)
        ->get()
        ->map(function($f) {
            $email = ($f->pedido && $f->pedido->usuario) ? ($f->pedido->usuario->email ?: '') : '';
            return [
                'documento' => $f->cedula_rif_cliente,
                'nombre' => $f->nombre_razon_social,
                'email' => $email,
                'telefono' => '',
                'direccion' => $f->direccion_fiscal ?: '',
                'es_parcial' => true,
            ];
        });

        $clientes = $usuarios->concat($facturas)->unique('documento')->values();

        return response()->json(['clientes' => $clientes]);
    }

    // CONSULTA AUTOMÁTICA DE CÉDULA / RIF (API CNE + LOCAL)
    public function consultarCedula(Request $request)
    {
        $input = trim($request->input('documento', $request->input('cedula', '')));
        if (empty($input)) {
            return response()->json([
                'success' => false,
                'message' => 'Por favor introduce una cédula o RIF.'
            ], 422);
        }

        // Limpiar y separar prefijo y número
        $nacionalidad = 'V';
        $numero = '';

        if (preg_match('/^([VEJPGvejpg])?[-_\s]?([0-9]+)/i', $input, $matches)) {
            if (!empty($matches[1])) {
                $nacionalidad = strtoupper($matches[1]);
            }
            $numero = $matches[2];
        } else {
            $soloDigitos = preg_replace('/[^0-9]/', '', $input);
            if (!empty($soloDigitos)) {
                $numero = $soloDigitos;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Formato de cédula o RIF inválido.'
                ], 422);
            }
        }

        $documentoFormateado = "{$nacionalidad}-{$numero}";
        $documentoSinGuion = "{$nacionalidad}{$numero}";

        // 1. Buscar en usuarios locales
        $usuarioLocal = User::where(function($q) use ($documentoFormateado, $documentoSinGuion, $numero) {
            $q->where('documento_identidad', $documentoFormateado)
              ->orWhere('documento_identidad', $documentoSinGuion)
              ->orWhere('documento_identidad', $numero);
        })->first();

        if ($usuarioLocal) {
            $nombreCompleto = trim($usuarioLocal->nombre . ' ' . ($usuarioLocal->apellido ?? ''));
            return response()->json([
                'success' => true,
                'fuente' => 'usuario_local',
                'documento' => $usuarioLocal->documento_identidad ?: $documentoFormateado,
                'nombre' => $nombreCompleto,
                'email' => $usuarioLocal->email ?: '',
                'telefono' => $usuarioLocal->telefono ?: '',
                'direccion' => '',
                'es_parcial' => empty($usuarioLocal->password_hash),
            ]);
        }

        // 2. Buscar en facturas previas
        $facturaPrevia = Factura::where(function($q) use ($documentoFormateado, $documentoSinGuion, $numero) {
            $q->where('cedula_rif_cliente', $documentoFormateado)
              ->orWhere('cedula_rif_cliente', $documentoSinGuion)
              ->orWhere('cedula_rif_cliente', $numero);
        })
        ->where('cedula_rif_cliente', '!=', 'V-00000000')
        ->where('nombre_razon_social', '!=', 'Consumidor Final')
        ->latest('id')
        ->first();

        if ($facturaPrevia) {
            return response()->json([
                'success' => true,
                'fuente' => 'factura_previa',
                'documento' => $facturaPrevia->cedula_rif_cliente,
                'nombre' => $facturaPrevia->nombre_razon_social,
                'telefono' => '',
                'direccion' => $facturaPrevia->direccion_fiscal ?: '',
                'es_parcial' => true,
            ]);
        }

        // 3. Consultar API externa del CNE
        $appId = config('services.cedula_api.app_id', env('CEDULA_API_APP_ID', '2118'));
        $token = config('services.cedula_api.token', env('CEDULA_API_TOKEN', 'ad3e6e46e42e96adba76c92c23755b54'));

        try {
            $response = Http::timeout(6)->get('https://api.cedula.com.ve/api/v1', [
                'app_id' => $appId,
                'token' => $token,
                'nacionalidad' => $nacionalidad,
                'cedula' => $numero,
            ]);

            if ($response->successful()) {
                $json = $response->json();
                if (!empty($json) && empty($json['error']) && !empty($json['data'])) {
                    $d = $json['data'];
                    $partes = array_filter([
                        $d['primer_nombre'] ?? '',
                        $d['segundo_nombre'] ?? '',
                        $d['primer_apellido'] ?? '',
                        $d['segundo_apellido'] ?? '',
                    ]);
                    $nombreCompleto = mb_convert_case(trim(implode(' ', $partes)), MB_CASE_TITLE, "UTF-8");

                    $dirCne = '';
                    if (!empty($d['cne'])) {
                        $dirCne = trim(implode(', ', array_filter([
                            $d['cne']['parroquia'] ?? '',
                            $d['cne']['municipio'] ?? '',
                            $d['cne']['estado'] ?? '',
                        ])));
                    }

                    return response()->json([
                        'success' => true,
                        'fuente' => 'cne_api',
                        'documento' => $documentoFormateado,
                        'nombre' => $nombreCompleto,
                        'telefono' => '',
                        'direccion' => $dirCne,
                        'es_parcial' => false,
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Manejo silencioso ante timeout o caída de red de la API
        }

        return response()->json([
            'success' => false,
            'documento' => $documentoFormateado,
            'message' => 'Cédula no encontrada en el CNE ni en registros locales. Puedes ingresar los datos manualmente.',
        ]);
    }

    // PROCESAR LA VENTA (Con IVA, Datos de Cliente, Pago Mixto y Vuelto)
    public function procesarVenta(Request $request)
    {
        $request->validate([
            'carrito' => 'required|array',
            'carrito.*.id' => 'required|exists:productos,id',
            'carrito.*.cantidad' => 'required|numeric|min:0.01',
            'tipo_pago' => 'nullable|in:unico,mixto',
            'metodo_pago' => 'nullable|in:efectivo_usd,efectivo_bs,punto_venta,pago_movil,zelle,binance,transferencia,cashea',
            'referencia_pago' => 'nullable|string|max:100',
            'pagos' => 'nullable|array',
            'cliente_documento' => 'nullable|string|max:30',
            'cliente_nombre' => 'nullable|string|max:150',
            'cliente_email' => 'nullable|email|max:150',
            'cliente_telefono' => 'nullable|string|max:50',
            'cliente_direccion' => 'nullable|string|max:255',
            'monto_recibido_usd' => 'nullable|numeric|min:0',
            'vuelto_usd' => 'nullable|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $sesion = SesionCaja::where('cajero_usuario_id', Auth::id())->whereNull('fecha_cierre')->firstOrFail();
            
            $tasaCambio = TasaCambio::latest('creado_at')->first();
            $tasa = $tasaCambio ? (float)$tasaCambio->valor_tasa : 1;

            $config = ConfiguracionTienda::first();
            $porcentajeIva = $config ? (float)$config->iva_porcentaje : 16.00;

            // 1. Calcular Subtotal y validar existencias
            $subtotal_usd = 0;
            foreach ($request->carrito as $item) {
                $producto = Producto::findOrFail($item['id']);
                if ($producto->stock_total < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para: {$producto->nombre}");
                }
                $precio = $producto->precio_oferta_usd ?: $producto->precio_venta_usd;
                $subtotal_usd += ($precio * $item['cantidad']);
            }

            // 2. Calcular IVA, Recargo por Modalidad y Totales
            $iva_usd = $subtotal_usd * ($porcentajeIva / 100);
            $base_con_iva = $subtotal_usd + $iva_usd;

            $tipoPago = $request->input('tipo_pago', 'unico');
            $metodoPrincipal = $request->input('metodo_pago', 'efectivo_usd');
            $modalidad = 'divisas';
            $recargoPct = 0.00;
            $recargoUsd = 0.00;

            if ($tipoPago !== 'mixto') {
                $modalidad = match($metodoPrincipal) {
                    'cashea' => 'cashea',
                    'efectivo_usd', 'zelle', 'binance' => 'divisas',
                    default => 'bolivares',
                };
                $recargoPct = ConfiguracionTienda::recargoPorcentaje($modalidad, 'pos');
                $recargoUsd = round($base_con_iva * ($recargoPct / 100), 2);
            }

            $gran_total_usd = $base_con_iva + $recargoUsd;
            $gran_total_ves = $gran_total_usd * $tasa;

            // Normalizar datos del cliente
            $clienteDoc = trim($request->input('cliente_documento', ''));
            $clienteNom = trim($request->input('cliente_nombre', ''));
            $clienteDir = trim($request->input('cliente_direccion', ''));
            $clienteTel = trim($request->input('cliente_telefono', ''));
            $clienteEmail = trim($request->input('cliente_email', ''));

            if (empty($clienteDoc) || empty($clienteNom)) {
                $clienteDoc = 'V-00000000';
                $clienteNom = 'Consumidor Final';
            }
            if (empty($clienteDir)) {
                $clienteDir = 'Compra en Tienda Física';
            }

            // Gestión de Usuario (Completo o Registro Parcial para vincular compras)
            $usuarioId = null;
            $emailFinalParaEnvio = null;

            if ($clienteDoc !== 'V-00000000' && $clienteNom !== 'Consumidor Final') {
                $usuarioExistente = User::where('documento_identidad', $clienteDoc)->first();
                if ($usuarioExistente) {
                    $usuarioId = $usuarioExistente->id;
                    $actualizaciones = [];

                    // Si el usuario es parcial y ahora proporciona teléfono, lo completamos
                    if (empty($usuarioExistente->password_hash) && !empty($clienteTel) && empty($usuarioExistente->telefono)) {
                        $actualizaciones['telefono'] = $clienteTel;
                    }

                    // Si se suministró email y el usuario no lo tenía o es parcial, lo asignamos
                    if (!empty($clienteEmail) && filter_var($clienteEmail, FILTER_VALIDATE_EMAIL)) {
                        if (empty($usuarioExistente->email) || empty($usuarioExistente->password_hash)) {
                            $emailOcupado = User::where('email', $clienteEmail)->where('id', '!=', $usuarioExistente->id)->exists();
                            if (!$emailOcupado) {
                                $actualizaciones['email'] = $clienteEmail;
                            }
                        }
                    }

                    if (!empty($actualizaciones)) {
                        $usuarioExistente->update($actualizaciones);
                    }

                    $emailFinalParaEnvio = !empty($clienteEmail) ? $clienteEmail : $usuarioExistente->email;
                } else {
                    // REGISTRO PARCIAL: Se registra DNI y nombre en la base de datos sin contraseña
                    $partesNom = explode(' ', $clienteNom);
                    $primerNombre = $clienteNom;
                    $apellido = null;
                    if (count($partesNom) >= 2) {
                        $primerNombre = $partesNom[0];
                        $apellido = implode(' ', array_slice($partesNom, 1));
                    }

                    $tipoDoc = strtoupper(substr($clienteDoc, 0, 1));
                    $tipoCliente = in_array($tipoDoc, ['J', 'G']) ? 'juridico' : 'natural';

                    // Si trae email, verificar unicidad
                    $emailAGuardar = null;
                    if (!empty($clienteEmail) && filter_var($clienteEmail, FILTER_VALIDATE_EMAIL)) {
                        if (!User::where('email', $clienteEmail)->exists()) {
                            $emailAGuardar = $clienteEmail;
                        }
                    }

                    $nuevoUsuario = User::create([
                        'rol_id' => 4, // Rol Cliente
                        'nombre' => $primerNombre,
                        'apellido' => $apellido,
                        'email' => $emailAGuardar,
                        'password_hash' => null, // Sin contraseña
                        'telefono' => !empty($clienteTel) ? $clienteTel : null,
                        'documento_identidad' => $clienteDoc,
                        'tipo_cliente' => $tipoCliente,
                        'activo' => 1,
                    ]);

                    $usuarioId = $nuevoUsuario->id;
                    $emailFinalParaEnvio = $emailAGuardar ?: $clienteEmail;
                }
            } else {
                // Caso Consumidor Final pero el cliente suministró un email para recibir su ticket
                if (!empty($clienteEmail) && filter_var($clienteEmail, FILTER_VALIDATE_EMAIL)) {
                    $emailFinalParaEnvio = $clienteEmail;
                }
            }

            // 3. Crear el Pedido (Nace en pendiente)
            $pedido = Pedido::create([
                'canal_venta' => 'tienda_fisica',
                'sesion_caja_id' => $sesion->id,
                'usuario_id' => $usuarioId,
                'tasa_cambio_id' => $tasaCambio ? $tasaCambio->id : null,
                'subtotal_usd' => $subtotal_usd,
                'costo_delivery_usd' => 0,
                'descuento_usd' => 0,
                'modalidad_pago' => $modalidad,
                'recargo_porcentaje' => $recargoPct,
                'recargo_usd' => $recargoUsd,
                'total_usd' => $gran_total_usd,
                'total_ves_calculado' => $gran_total_ves,
                'direccion_texto' => $clienteDir,
                'estado' => 'pendiente'
            ]);

            // 4. Insertar Detalles del Pedido
            foreach ($request->carrito as $item) {
                $producto = Producto::find($item['id']);
                $precio = $producto->precio_oferta_usd ?: $producto->precio_venta_usd;

                DetallePedido::create([ 
                    'pedido_id' => $pedido->id,
                    'producto_id' => $producto->id,
                    'cantidad_solicitada' => $item['cantidad'],
                    'cantidad_real_despachada' => $item['cantidad'],
                    'precio_historico_usd' => $precio
                ]);
            }

            // 4.5. Cambiar estado a 'completado_caja' para disparar el trigger de inventario
            $pedido->estado = 'completado_caja';
            $pedido->save();

            // 5. Gestión de Pagos (Único o Mixto)
            $listaPagos = [];
            if ($request->input('tipo_pago') === 'mixto' && is_array($request->pagos) && count($request->pagos) > 0) {
                $sumaPagosUsd = 0;
                foreach ($request->pagos as $p) {
                    $montoUsd = floatval($p['monto_usd'] ?? 0);
                    if ($montoUsd <= 0) continue;
                    $montoVes = floatval($p['monto_ves'] ?? ($montoUsd * $tasa));
                    $metodo = $p['metodo'] ?? 'efectivo_usd';
                    $ref = !empty($p['referencia']) ? trim($p['referencia']) : null;
                    
                    $listaPagos[] = [
                        'metodo' => $metodo,
                        'monto_usd' => $montoUsd,
                        'monto_ves' => $montoVes,
                        'referencia' => $ref,
                    ];
                    $sumaPagosUsd += $montoUsd;
                }

                if (round($sumaPagosUsd, 2) < round($gran_total_usd - 0.02, 2)) {
                    throw new \Exception("La suma de los pagos mixtos ($" . number_format($sumaPagosUsd, 2) . ") no cubre el total de la venta ($" . number_format($gran_total_usd, 2) . ")");
                }
            } else {
                $metodo = $request->input('metodo_pago', 'efectivo_usd');
                $ref = $request->filled('referencia_pago') ? trim($request->referencia_pago) : null;
                $listaPagos[] = [
                    'metodo' => $metodo,
                    'monto_usd' => $gran_total_usd,
                    'monto_ves' => $gran_total_ves,
                    'referencia' => $ref,
                ];
            }

            $metodosUsadosTxt = [];
            foreach ($listaPagos as $pagoItem) {
                Pago::create([
                    'pedido_id' => $pedido->id,
                    'metodo' => $pagoItem['metodo'],
                    'referencia_bancaria' => $pagoItem['referencia'],
                    'monto_usd' => $pagoItem['monto_usd'],
                    'monto_ves' => $pagoItem['monto_ves'],
                    'estado' => 'aprobado',
                    'verificado_por_usuario_id' => Auth::id()
                ]);
                $metodosUsadosTxt[] = strtoupper(str_replace('_', ' ', $pagoItem['metodo']));
            }

            // 6. Registrar Movimiento de Caja
            MovimientoCaja::create([
                'sesion_caja_id' => $sesion->id,
                'tipo' => 'ingreso',
                'motivo' => 'Venta POS Pedido #' . $pedido->id . ' (' . implode(', ', array_unique($metodosUsadosTxt)) . ')',
                'monto_usd' => $gran_total_usd,
                'monto_ves' => $gran_total_ves,
            ]);

            // 7. Actualizar totales de la sesión actual
            $sesion->total_ventas_sistema_usd += $gran_total_usd;
            $sesion->total_ventas_sistema_ves += $gran_total_ves;
            $sesion->save();

            // 8. Emitir Factura Fiscal Automática
            $datosCliente = [
                'cedula_rif_cliente' => $clienteDoc,
                'nombre_razon_social' => $clienteNom,
                'direccion_fiscal' => $clienteDir,
            ];
            $factura = Factura::emitirParaPedido($pedido, $datosCliente);

            DB::commit();

            // 8.5 Enviar Factura Ticket 80mm por correo vía SMTP si el cliente tiene email
            $correoEnviado = false;
            $correoDestino = (!empty($emailFinalParaEnvio) && filter_var($emailFinalParaEnvio, FILTER_VALIDATE_EMAIL)) ? $emailFinalParaEnvio : null;

            if ($factura && $correoDestino) {
                try {
                    $facturaMail = Factura::with([
                        'pedido.detalles.producto',
                        'pedido.pagos',
                        'pedido.usuario',
                        'pedido.tasaCambio'
                    ])->find($factura->id);

                    Mail::to($correoDestino)->send(new FacturaTicketMail($facturaMail, $config));
                    $correoEnviado = true;
                } catch (\Throwable $e) {
                    Log::error("POS: Error enviando ticket SMTP a {$correoDestino} para factura {$factura->numero_factura}: " . $e->getMessage());
                    $correoEnviado = false;
                }
            }

            // 9. Recopilar stock actualizado de los productos vendidos para retorno AJAX
            $productosActualizados = [];
            foreach ($request->carrito as $item) {
                $pActual = Producto::with('categoria')->find($item['id']);
                if ($pActual) {
                    $productosActualizados[] = [
                        'id' => $pActual->id,
                        'stock_total' => (float)$pActual->stock_total,
                        'stock_minimo_alerta' => (float)($pActual->stock_minimo_alerta ?: 5),
                        'unidad_medida' => $pActual->unidad_medida ?: 'ud',
                        'producto' => $pActual
                    ];
                }
            }

            return response()->json([
                'success' => true, 
                'message' => 'Venta procesada con éxito.',
                'pedido_id' => $pedido->id,
                'factura_id' => $factura ? $factura->id : null,
                'numero_factura' => $factura ? $factura->numero_factura : 'N/A',
                'ticket_url' => $factura ? route('factura.ticket', $factura->id) : null,
                'gran_total_usd' => $gran_total_usd,
                'gran_total_ves' => $gran_total_ves,
                'vuelto_usd' => floatval($request->input('vuelto_usd', 0)),
                'cliente' => $clienteNom,
                'correo_enviado' => $correoEnviado,
                'cliente_email' => $correoDestino,
                'productos_actualizados' => $productosActualizados
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // RESUMEN / AUDITORÍA DE CIERRE DE CAJA (ARQUEO X)
    public function resumenCierre()
    {
        $sesion = SesionCaja::with(['caja', 'cajero'])
                            ->where('cajero_usuario_id', Auth::id())
                            ->whereNull('fecha_cierre')
                            ->firstOrFail();

        $tasaCambio = TasaCambio::latest('creado_at')->first();
        $valorTasa = $tasaCambio ? (float)$tasaCambio->valor_tasa : 1;

        // Pedidos realizados en esta sesión
        $pedidosIds = Pedido::where('sesion_caja_id', $sesion->id)->pluck('id');
        $totalPedidos = $pedidosIds->count();

        // Pagos agrupados por método
        $pagos = Pago::whereIn('pedido_id', $pedidosIds)
                     ->where('estado', 'aprobado')
                     ->get();

        $nombresMetodos = [
            'efectivo_usd' => 'Efectivo USD ($)',
            'efectivo_bs'  => 'Efectivo Bolívares (Bs)',
            'pago_movil'   => 'Pago Móvil',
            'punto_venta'  => 'Punto de Venta / Tarjeta',
            'zelle'        => 'Zelle',
            'binance'      => 'Binance Pay',
            'transferencia'=> 'Transferencia Bancaria',
            'biopago'      => 'Biopago'
        ];

        $desgloseMetodos = [];
        $totalEfectivoUsd = 0;

        foreach ($pagos->groupBy('metodo') as $metodo => $grupo) {
            $sumUsd = (float) $grupo->sum('monto_usd');
            $sumVes = (float) $grupo->sum('monto_ves');
            $desgloseMetodos[] = [
                'metodo_key' => $metodo,
                'metodo_label' => $nombresMetodos[$metodo] ?? strtoupper(str_replace('_', ' ', $metodo)),
                'cantidad' => $grupo->count(),
                'total_usd' => $sumUsd,
                'total_ves' => $sumVes
            ];

            if ($metodo === 'efectivo_usd') {
                $totalEfectivoUsd += $sumUsd;
            }
        }

        // Efectivo esperado en gaveta = Monto Inicial + Ventas en Efectivo USD
        $efectivoEsperadoGavetaUsd = (float)$sesion->monto_inicial_usd + $totalEfectivoUsd;

        return response()->json([
            'sesion_id' => $sesion->id,
            'caja_nombre' => $sesion->caja ? $sesion->caja->nombre : 'Caja Principal',
            'cajero_nombre' => $sesion->cajero ? trim($sesion->cajero->nombre . ' ' . $sesion->cajero->apellido) : 'Cajero',
            'fecha_apertura' => \Carbon\Carbon::parse($sesion->fecha_apertura)->format('d/m/Y h:i A'),
            'monto_inicial_usd' => (float)$sesion->monto_inicial_usd,
            'total_pedidos' => $totalPedidos,
            'total_ventas_usd' => (float)$sesion->total_ventas_sistema_usd,
            'total_ventas_ves' => (float)$sesion->total_ventas_sistema_ves,
            'total_efectivo_ventas_usd' => $totalEfectivoUsd,
            'efectivo_esperado_gaveta_usd' => $efectivoEsperadoGavetaUsd,
            'desglose_metodos' => $desgloseMetodos,
            'tasa_bcv' => $valorTasa
        ]);
    }

    // CERRAR CAJA Y REALIZAR ARQUEO
    public function cerrarCaja(Request $request)
    {
        $request->validate([
            'dinero_real_usd' => 'required|numeric|min:0'
        ]);

        $sesion = SesionCaja::where('cajero_usuario_id', Auth::id())->whereNull('fecha_cierre')->firstOrFail();
        
        // Ventas en efectivo USD de esta sesión
        $pedidosIds = Pedido::where('sesion_caja_id', $sesion->id)->pluck('id');
        $efectivoVentasUsd = (float) Pago::whereIn('pedido_id', $pedidosIds)
                                         ->where('metodo', 'efectivo_usd')
                                         ->where('estado', 'aprobado')
                                         ->sum('monto_usd');

        // Efectivo físico esperado = Monto Inicial + Ventas en efectivo USD
        $esperado = (float)$sesion->monto_inicial_usd + $efectivoVentasUsd;
        $diferencia = (float)$request->dinero_real_usd - $esperado;

        $sesion->update([
            'fecha_cierre' => now(),
            'dinero_real_en_caja_usd' => $request->dinero_real_usd,
            'diferencia_usd' => $diferencia,
            'observaciones_cierre' => $request->observaciones
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Caja cerrada correctamente.',
            'sesion_id' => $sesion->id,
            'imprimir_url' => route('admin.pos.imprimirCierre', $sesion->id)
        ]);
    }

    // IMPRIMIR REPORTE DE CIERRE DE TURNO (TICKET 80mm)
    public function imprimirCierre($id)
    {
        $sesion = SesionCaja::with(['caja', 'cajero'])->findOrFail($id);

        $pedidosIds = Pedido::where('sesion_caja_id', $sesion->id)->pluck('id');
        $totalPedidos = $pedidosIds->count();

        $pagos = Pago::whereIn('pedido_id', $pedidosIds)
                     ->where('estado', 'aprobado')
                     ->get();

        $nombresMetodos = [
            'efectivo_usd' => 'Efectivo USD',
            'efectivo_bs'  => 'Efectivo Bs',
            'pago_movil'   => 'Pago Móvil',
            'punto_venta'  => 'Punto de Venta',
            'zelle'        => 'Zelle',
            'binance'      => 'Binance Pay',
            'transferencia'=> 'Transferencia',
            'biopago'      => 'Biopago'
        ];

        $desgloseMetodos = [];
        $totalEfectivoUsd = 0;
        foreach ($pagos->groupBy('metodo') as $metodo => $grupo) {
            $sumUsd = (float) $grupo->sum('monto_usd');
            $sumVes = (float) $grupo->sum('monto_ves');
            $desgloseMetodos[] = [
                'metodo' => $nombresMetodos[$metodo] ?? strtoupper(str_replace('_', ' ', $metodo)),
                'cantidad' => $grupo->count(),
                'total_usd' => $sumUsd,
                'total_ves' => $sumVes
            ];
            if ($metodo === 'efectivo_usd') {
                $totalEfectivoUsd += $sumUsd;
            }
        }

        $config = ConfiguracionTienda::first();

        return view('admin.pos.ticket_cierre', compact('sesion', 'totalPedidos', 'desgloseMetodos', 'totalEfectivoUsd', 'config'));
    }

    /**
     * Enviar o Reenviar Ticket 80mm por correo vía SMTP manualmente desde el POS
     */
    public function enviarTicketCorreo(Request $request)
    {
        $request->validate([
            'factura_id' => 'required|exists:facturas,id',
            'email' => 'required|email|max:150',
        ]);

        try {
            $factura = Factura::with([
                'pedido.detalles.producto',
                'pedido.pagos',
                'pedido.usuario',
                'pedido.tasaCambio'
            ])->findOrFail($request->factura_id);

            $config = ConfiguracionTienda::first();

            Mail::to($request->email)->send(new FacturaTicketMail($factura, $config));

            // Si el cliente en BD no tenía email, actualizamos su registro
            if ($factura->pedido && $factura->pedido->usuario && empty($factura->pedido->usuario->email)) {
                $usuario = $factura->pedido->usuario;
                if (!User::where('email', $request->email)->where('id', '!=', $usuario->id)->exists()) {
                    $usuario->update(['email' => $request->email]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Ticket #{$factura->numero_factura} enviado exitosamente a {$request->email}."
            ]);
        } catch (\Throwable $e) {
            Log::error("POS: Error al enviar ticket manual a {$request->email}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'No se pudo enviar el correo: ' . $e->getMessage()
            ], 500);
        }
    }
}