<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Controladores Frontend / Auth
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\DireccionController;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\TasaCambioController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\SplashController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

// Controladores Admin
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\MarcaController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\HistoricoPrecioController;
use App\Http\Controllers\Admin\PedidoController;
use App\Http\Controllers\Admin\PagoController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\FacturaController;
use App\Http\Controllers\Admin\FacturacionConfigController;
use App\Http\Controllers\Admin\CajaDiariaController;
use App\Http\Controllers\Admin\HorarioadminController;
use App\Http\Controllers\Admin\ConfiguracionTiendaController;
use App\Http\Controllers\Admin\CuentaBancariaController;
use App\Http\Controllers\Admin\ZonaDeliveryController;
use App\Http\Controllers\Admin\TasaCambioController as AdminTasaCambioController;
use App\Http\Controllers\Admin\CotizacionController;
use App\Http\Controllers\Admin\SolicitudProductoController;

/*
|--------------------------------------------------------------------------
| Web Routes - Agrosuministros Rodríguez
|--------------------------------------------------------------------------
*/

// --- 1. RUTA PRINCIPAL (HOME) ---
Route::get('/', HomeController::class)->name('home');

// --- 2. CATÁLOGO DE PRODUCTOS ---
Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo');
Route::get('/catalogo/buscar-en-vivo', [CatalogoController::class, 'buscarEnVivo'])->name('catalogo.buscar-en-vivo');
Route::post('/catalogo/solicitar', [CatalogoController::class, 'solicitarProducto'])->name('catalogo.solicitar');
Route::get('/catalogo/producto/{id}', [CatalogoController::class, 'show'])->name('catalogo.show');
Route::get('/producto/{id}', [CatalogoController::class, 'show'])->name('producto.show');

// --- 3. RUTAS DE INVITADOS (GUEST) ---
Route::middleware('guest')->group(function () {
    Route::get('/registro', [RegisterController::class, 'create'])->name('register');
    Route::post('/registro', [RegisterController::class, 'store']);

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// --- 4. RUTAS AUTENTICADAS ---
Route::middleware('auth')->group(function () {
    // Cerrar Sesión
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');

    // Redirección inteligente del Dashboard según el rol
    Route::get('/dashboard', function () {
        if (Auth::user()?->esAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('perfil');
    })->name('dashboard');

    // Perfil del Usuario
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil');
    Route::put('/perfil/actualizar', [PerfilController::class, 'updateDatos'])->name('perfil.update');
    Route::put('/perfil/password', [PerfilController::class, 'updatePassword'])->name('password.update');
    Route::get('/perfil/pedidos', [PerfilController::class, 'pedidos'])->name('perfil.pedidos');
    Route::get('/perfil/pedidos/{id}', [PerfilController::class, 'detallePedido'])->name('perfil.pedido.detalle');
    Route::get('/perfil/pedidos/{id}/ticket', [FacturaController::class, 'ticketPorPedido'])->name('perfil.pedido.ticket');
    Route::get('/perfil/pedidos/{id}/factura', [FacturaController::class, 'facturaPorPedido'])->name('perfil.pedido.factura');
    Route::get('/factura/{id}/ticket', [FacturaController::class, 'ticket'])->name('factura.ticket');
    Route::get('/factura/{id}/imprimir', [FacturaController::class, 'imprimir'])->name('factura.imprimir');

    // Direcciones del Usuario
    Route::post('/perfil/direccion', [DireccionController::class, 'store'])->name('direccion.store');
    Route::patch('/perfil/direccion/{id}/principal', [DireccionController::class, 'setPrincipal'])->name('direccion.principal');
    Route::delete('/perfil/direccion/{id}', [DireccionController::class, 'destroy'])->name('direccion.destroy');

    // Carrito de Compras
    Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
    Route::post('/carrito/add', [CarritoController::class, 'add'])->name('carrito.add');
    Route::post('/carrito/update', [CarritoController::class, 'update'])->name('carrito.update');
    Route::post('/carrito/remove', [CarritoController::class, 'remove'])->name('carrito.remove');

    // Checkout y Finalización de Compra
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Favoritos del Usuario
    Route::get('/perfil/favoritos', [FavoritoController::class, 'index'])->name('perfil.favoritos');
});

// --- 5. RUTAS UTILITARIAS Y AJAX PÚBLICAS ---
Route::post('/favoritos/toggle', [FavoritoController::class, 'toggle'])->name('favoritos.toggle');
Route::get('/tasa-cambio/actual', [TasaCambioController::class, 'obtenerTasaActual'])->name('tasa-cambio.actual');
Route::get('/horario/hoy', [HorarioController::class, 'obtenerHorarioHoy'])->name('horario.hoy');
Route::get('/horario/abierto-ahora', [HorarioController::class, 'estaAbiertoAhora'])->name('horario.abierto-ahora');
Route::post('/splash/mark-shown', [SplashController::class, 'markAsShown'])->name('splash.mark-shown');
Route::get('/splash/should-show', [SplashController::class, 'shouldShow'])->name('splash.should-show');

// ==========================================
// RUTAS DE ADMINISTRACIÓN (Prefijo /admin)
// ==========================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Notificaciones en tiempo real
    Route::get('/notificaciones/fetch', [DashboardController::class, 'fetchNotificaciones'])->name('notificaciones.fetch');

    // Pantalla Principal - Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Módulo: Usuarios y Roles
    Route::resource('roles', RoleController::class)->except(['create', 'show', 'edit'])->middleware('permiso:gestionar_roles_permisos');
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index')->middleware('permiso:ver_usuarios');
    Route::get('/usuarios/{id}/permisos-extra', [UsuarioController::class, 'getPermisosExtra'])->name('usuarios.permisos_extra.get')->middleware('permiso:gestionar_roles_permisos');
    Route::post('/usuarios/{id}/permisos-extra', [UsuarioController::class, 'updatePermisosExtra'])->name('usuarios.permisos_extra.post')->middleware('permiso:gestionar_roles_permisos');

    // Módulo: Finanzas, Facturación y Caja
    Route::get('/facturas', [FacturaController::class, 'index'])->name('facturas.index')->middleware('permiso:ver_facturas');
    Route::get('/facturas/{id}', [FacturaController::class, 'show'])->name('facturas.show')->middleware('permiso:ver_facturas');
    Route::get('/facturas/{id}/ticket', [FacturaController::class, 'ticket'])->name('facturas.ticket')->middleware('permiso:ver_facturas');
    Route::get('/facturas/{id}/imprimir', [FacturaController::class, 'imprimir'])->name('facturas.imprimir')->middleware('permiso:ver_facturas');
    Route::post('/facturas/{id}/anular', [FacturaController::class, 'anular'])->name('facturas.anular')->middleware('permiso:configurar_facturacion');

    Route::middleware('permiso:gestionar_caja_diaria')->group(function () {
        Route::get('/caja-diaria', [CajaDiariaController::class, 'index'])->name('caja_diaria.index');
        Route::get('/caja-diaria/{id}/movimientos', [CajaDiariaController::class, 'movimientos'])->name('caja_diaria.movimientos');
        Route::get('/caja-diaria/{id}/detalle', [CajaDiariaController::class, 'detalleTurno'])->name('caja_diaria.detalle');
        Route::get('/caja-diaria/{id}/pdf', [CajaDiariaController::class, 'generarPdfCierre'])->name('caja_diaria.pdf');
        Route::get('/caja-diaria/{id}', [CajaDiariaController::class, 'show'])->name('caja_diaria.show');
        Route::post('/caja-diaria/cajas', [CajaDiariaController::class, 'storeCaja'])->name('caja_diaria.cajas.store');
        Route::put('/caja-diaria/cajas/{id}', [CajaDiariaController::class, 'updateCaja'])->name('caja_diaria.cajas.update');
        Route::post('/caja-diaria/cajas/{id}/toggle', [CajaDiariaController::class, 'toggleCaja'])->name('caja_diaria.cajas.toggle');
        Route::delete('/caja-diaria/cajas/{id}', [CajaDiariaController::class, 'destroyCaja'])->name('caja_diaria.cajas.destroy');
    });

    Route::middleware('permiso:configurar_facturacion')->group(function () {
        Route::get('/configuracion-facturacion', [FacturacionConfigController::class, 'index'])->name('facturacion_config.index');
        Route::post('/configuracion-facturacion', [FacturacionConfigController::class, 'store'])->name('facturacion_config.store');
        Route::put('/configuracion-facturacion/{id}', [FacturacionConfigController::class, 'update'])->name('facturacion_config.update');
        Route::post('/configuracion-facturacion/{id}/toggle', [FacturacionConfigController::class, 'toggleStatus'])->name('facturacion_config.toggle');
    });

    Route::middleware('permiso:gestionar_tasas_cambio')->group(function () {
        Route::get('/tasas-cambio', [AdminTasaCambioController::class, 'index'])->name('tasas-cambio.index');
        Route::post('/tasas-cambio', [AdminTasaCambioController::class, 'store'])->name('tasas-cambio.store');
        Route::post('/tasas-cambio/sincronizar-bcv', [AdminTasaCambioController::class, 'sincronizarBCV'])->name('tasas-cambio.sincronizar');
    });

    // Módulo: Catálogo e Inventario
    Route::middleware('permiso:gestionar_categorias_marcas')->group(function () {
        Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
        Route::post('/categorias', [CategoriaController::class, 'store'])->name('categorias.store');
        Route::post('/categorias/{id}', [CategoriaController::class, 'update'])->name('categorias.update');
        Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy'])->name('categorias.destroy');
        Route::post('/categorias/{id}/restore', [CategoriaController::class, 'restore'])->name('categorias.restore');

        Route::get('/marcas', [MarcaController::class, 'index'])->name('marcas.index');
        Route::post('/marcas', [MarcaController::class, 'store'])->name('marcas.store');
        Route::put('/marcas/{id}', [MarcaController::class, 'update'])->name('marcas.update');
        Route::delete('/marcas/{id}', [MarcaController::class, 'destroy'])->name('marcas.destroy');
        Route::post('/marcas/{id}/toggle', [MarcaController::class, 'toggleStatus'])->name('marcas.toggle');
    });

    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index')->middleware('permiso:ver_productos');
    Route::middleware('permiso:gestionar_productos')->group(function () {
        Route::get('/productos/crear', [ProductoController::class, 'create'])->name('productos.create'); 
        Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store'); 
        Route::get('/productos/{id}/editar', [ProductoController::class, 'edit'])->name('productos.edit'); 
        Route::put('/productos/{id}', [ProductoController::class, 'update'])->name('productos.update'); 
        Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');
        Route::post('/productos/{id}/destacado', [ProductoController::class, 'toggleDestacado'])->name('productos.destacado');
        Route::post('/productos/{id}/restore', [ProductoController::class, 'restore'])->name('productos.restore');
        Route::get('/productos/{id}/imagenes', [ProductoController::class, 'obtenerImagenes'])->name('productos.imagenes.get');
        Route::post('/productos/{id}/imagenes', [ProductoController::class, 'subirImagenesGaleria'])->name('productos.imagenes.store');
        Route::delete('/productos/imagenes/{imagenId}', [ProductoController::class, 'eliminarImagenGaleria'])->name('productos.imagenes.destroy');
    });

    Route::get('/historico-precios', [HistoricoPrecioController::class, 'index'])->name('historico_precios.index')->middleware('permiso:ver_historico_precios');
    Route::get('/solicitudes-insumos', [SolicitudProductoController::class, 'index'])->name('solicitudes_productos.index')->middleware('permiso:ver_productos,gestionar_productos,gestionar_solicitudes_productos');
    Route::post('/solicitudes-insumos/{id}/estado', [SolicitudProductoController::class, 'actualizarEstado'])->name('solicitudes_productos.actualizar_estado')->middleware('permiso:ver_productos,gestionar_productos,gestionar_solicitudes_productos');
    Route::delete('/solicitudes-insumos/{id}', [SolicitudProductoController::class, 'destroy'])->name('solicitudes_productos.destroy')->middleware('permiso:ver_productos,gestionar_productos,gestionar_solicitudes_productos');

    // Módulo: Ventas y Pedidos
    Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index')->middleware('permiso:ver_pedidos,gestionar_pagos');
    Route::get('/pedidos/{id}', [PedidoController::class, 'show'])->name('pedidos.show')->middleware('permiso:ver_pedidos,gestionar_pagos');
    Route::get('/pedidos/{id}/ticket', [FacturaController::class, 'ticketPorPedido'])->name('pedidos.ticket')->middleware('permiso:ver_pedidos,gestionar_pagos');
    Route::get('/pedidos/{id}/factura', [FacturaController::class, 'facturaPorPedido'])->name('pedidos.factura')->middleware('permiso:ver_pedidos,gestionar_pagos');
    Route::post('/pedidos/{id}/estado', [PedidoController::class, 'updateStatus'])->name('pedidos.updateStatus')->middleware('permiso:actualizar_estado_pedido');
    Route::post('/pedidos/{id}/aprobar-pago', [PedidoController::class, 'aprobarPago'])->name('pedidos.aprobarPago')->middleware('permiso:gestionar_pagos');
    Route::post('/pedidos/{id}/rechazar-pago', [PedidoController::class, 'rechazarPago'])->name('pedidos.rechazarPago')->middleware('permiso:gestionar_pagos');

    Route::middleware('permiso:gestionar_pagos')->group(function () {
        Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
        Route::post('/pagos/{id}/aprobar', [PagoController::class, 'aprobar'])->name('pagos.aprobar');
        Route::post('/pagos/{id}/rechazar', [PagoController::class, 'rechazar'])->name('pagos.rechazar');
    });

    // Módulo: Terminal POS (Punto de Venta)
    Route::middleware('permiso:crear_venta_manual')->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::post('/pos/abrir-caja', [PosController::class, 'abrirCaja'])->name('pos.abrirCaja');
        Route::post('/pos/buscar-producto', [PosController::class, 'buscarProducto'])->name('pos.buscarProducto'); 
        Route::get('/pos/buscar-cliente', [PosController::class, 'buscarCliente'])->name('pos.buscarCliente');
        Route::get('/pos/consultar-cedula', [PosController::class, 'consultarCedula'])->name('pos.consultarCedula');
        Route::post('/pos/procesar-venta', [PosController::class, 'procesarVenta'])->name('pos.procesarVenta');
        Route::post('/pos/enviar-ticket-correo', [PosController::class, 'enviarTicketCorreo'])->name('pos.enviarTicketCorreo');
        Route::get('/pos/resumen-cierre', [PosController::class, 'resumenCierre'])->name('pos.resumenCierre');
        Route::post('/pos/cerrar-caja', [PosController::class, 'cerrarCaja'])->name('pos.cerrarCaja');
        Route::get('/pos/imprimir-cierre/{id}', [PosController::class, 'imprimirCierre'])->name('pos.imprimirCierre');
    });

    // Módulo: Cotizaciones y Presupuestos
    Route::get('/cotizaciones', [CotizacionController::class, 'index'])->name('cotizaciones.index');
    Route::get('/cotizaciones/crear', [CotizacionController::class, 'create'])->name('cotizaciones.create');
    Route::post('/cotizaciones', [CotizacionController::class, 'store'])->name('cotizaciones.store');
    Route::get('/cotizaciones/buscar-producto', [CotizacionController::class, 'buscarProducto'])->name('cotizaciones.buscar-producto');
    Route::get('/cotizaciones/buscar-cliente', [CotizacionController::class, 'buscarCliente'])->name('cotizaciones.buscar-cliente');
    Route::get('/cotizaciones/{id}', [CotizacionController::class, 'show'])->name('cotizaciones.show');
    Route::get('/cotizaciones/{id}/imprimir', [CotizacionController::class, 'imprimir'])->name('cotizaciones.imprimir');
    Route::get('/cotizaciones/{id}/pdf', [CotizacionController::class, 'descargarPdf'])->name('cotizaciones.pdf');
    Route::post('/cotizaciones/{id}/enviar-correo', [CotizacionController::class, 'enviarCorreo'])->name('cotizaciones.enviar_correo');
    Route::post('/cotizaciones/{id}/estado', [CotizacionController::class, 'cambiarEstado'])->name('cotizaciones.estado');
    Route::delete('/cotizaciones/{id}', [CotizacionController::class, 'destroy'])->name('cotizaciones.destroy');

    // Módulo: Sistema y Configuración
    Route::middleware('permiso:gestionar_horarios')->group(function () {
        Route::get('/horarios', [HorarioadminController::class, 'index'])->name('horarios.index');
        Route::post('/horarios/actualizar', [HorarioadminController::class, 'updateAll'])->name('horarios.updateAll');
    });

    Route::middleware('permiso:configurar_tienda')->group(function () {
        Route::get('/configuracion-tienda', [ConfiguracionTiendaController::class, 'index'])->name('configuracion.index');
        Route::post('/configuracion-tienda', [ConfiguracionTiendaController::class, 'update'])->name('configuracion.update');
    });

    Route::middleware('permiso:gestionar_cuentas_banco')->group(function () {
        Route::get('/cuentas-bancarias', [CuentaBancariaController::class, 'index'])->name('cuentas_bancarias.index');
        Route::post('/cuentas-bancarias', [CuentaBancariaController::class, 'store'])->name('cuentas_bancarias.store');
        Route::put('/cuentas-bancarias/{id}', [CuentaBancariaController::class, 'update'])->name('cuentas_bancarias.update');
        Route::delete('/cuentas-bancarias/{id}', [CuentaBancariaController::class, 'destroy'])->name('cuentas_bancarias.destroy');
        Route::post('/cuentas-bancarias/{id}/toggle', [CuentaBancariaController::class, 'toggleStatus'])->name('cuentas_bancarias.toggle');
    });

    // Módulo: Logística y Zonas de Delivery (Leaflet Map)
    Route::get('/zonas-delivery', [ZonaDeliveryController::class, 'index'])->name('zonas_delivery.index')->middleware('permiso:gestionar_zonas_delivery,ver_logistica');
    Route::middleware('permiso:gestionar_zonas_delivery')->group(function () {
        Route::post('/zonas-delivery', [ZonaDeliveryController::class, 'store'])->name('zonas_delivery.store');
        Route::put('/zonas-delivery/{id}', [ZonaDeliveryController::class, 'update'])->name('zonas_delivery.update');
        Route::delete('/zonas-delivery/{id}', [ZonaDeliveryController::class, 'destroy'])->name('zonas_delivery.destroy');
        Route::post('/zonas-delivery/{id}/toggle', [ZonaDeliveryController::class, 'toggle'])->name('zonas_delivery.toggle');
        Route::post('/zonas-delivery/toggle-master', [ZonaDeliveryController::class, 'toggleMasterDelivery'])->name('zonas_delivery.toggle_master');
        Route::post('/zonas-delivery/tienda-ubicacion', [ZonaDeliveryController::class, 'actualizarUbicacionTienda'])->name('zonas_delivery.tienda_ubicacion');
    });
});