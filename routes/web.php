<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TasaCambioController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\HomeController; 
use App\Http\Controllers\SplashController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\DireccionController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PedidoController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\RecetaVetController;
use App\Http\Controllers\Admin\ClienteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. RUTA PRINCIPAL (HOME) ---
Route::get('/', HomeController::class)->name('home');

// --- 2. CATÁLOGO ---
Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo');

// --- 3. RUTAS DE GUEST (INVITADOS) ---
Route::middleware('guest')->group(function () {
    // Registro
    Route::get('/registro', [RegisterController::class, 'create'])->name('register');
    Route::post('/registro', [RegisterController::class, 'store']);

    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// --- 4. RUTAS DE AUTH (USUARIOS LOGUEADOS) ---
Route::middleware('auth')->group(function () {
    // Cerrar Sesión
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');

    // Panel de Usuario (Dashboard temporal)
    Route::get('/dashboard', function () {
        return view('welcome'); 
    })->name('dashboard');
});

Route::middleware(['auth'])->group(function () {
    // Vista del perfil
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil');
    
    // Actualizar datos personales
    Route::put('/perfil/actualizar', [PerfilController::class, 'updateDatos'])->name('perfil.update');
    
    // Actualizar contraseña
    Route::put('/perfil/password', [PerfilController::class, 'updatePassword'])->name('password.update');

    Route::post('/perfil/direccion', [DireccionController::class, 'store'])->name('direccion.store');

    // NUEVA RUTA: Cambiar dirección principal
    Route::patch('/perfil/direccion/{id}/principal', [DireccionController::class, 'setPrincipal'])->name('direccion.principal');
    
    // OPCIONAL: Ruta para eliminar
    Route::delete('/perfil/direccion/{id}', [DireccionController::class, 'destroy'])->name('direccion.destroy');

    Route::get('/perfil/pedidos', [App\Http\Controllers\PerfilController::class, 'pedidos'])->name('perfil.pedidos');
    Route::get('/perfil/pedidos/{id}', [App\Http\Controllers\PerfilController::class, 'detallePedido'])->name('perfil.pedido.detalle');

    // Rutas del Carrito
    Route::get('/carrito', [App\Http\Controllers\CarritoController::class, 'index'])->name('carrito.index');
    Route::post('/carrito/add', [App\Http\Controllers\CarritoController::class, 'add'])->name('carrito.add');
    Route::post('/carrito/update', [App\Http\Controllers\CarritoController::class, 'update'])->name('carrito.update');
    Route::post('/carrito/remove', [App\Http\Controllers\CarritoController::class, 'remove'])->name('carrito.remove');

    Route::get('/checkout', [App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store');
});

// --- RUTAS DEL PANEL ADMINISTRATIVO ---
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rutas de Pedidos
    Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::get('/pedidos/{id}', [PedidoController::class, 'show'])->name('pedidos.show');
    Route::put('/pedidos/{id}', [PedidoController::class, 'update'])->name('pedidos.update');

    // Rutas de Inventario
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
    Route::get('/inventario/{id}', [InventarioController::class, 'show'])->name('inventario.show');
    Route::get('/inventario/{id}/edit', [InventarioController::class, 'edit'])->name('inventario.edit');
    Route::put('/inventario/{id}', [InventarioController::class, 'update'])->name('inventario.update');
    Route::post('/inventario/{id}/agregar-stock', [InventarioController::class, 'agregarStock'])->name('inventario.agregar-stock');
    Route::patch('/inventario/{id}/archivar', [InventarioController::class, 'archivar'])->name('inventario.archivar');

    // Rutas de Clientes
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{id}', [ClienteController::class, 'show'])->name('clientes.show');
    Route::get('/clientes/{id}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
    Route::put('/clientes/{id}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::patch('/clientes/{id}/toggle-status', [ClienteController::class, 'toggleStatus'])->name('clientes.toggle-status');
    Route::patch('/clientes/{id}/password', [ClienteController::class, 'updatePassword'])->name('clientes.update-password');
    Route::delete('/clientes/{id}', [ClienteController::class, 'destroy'])->name('clientes.destroy');

    // Rutas de Recetas Veterinarias
    Route::get('/recetas', [RecetaVetController::class, 'index'])->name('recetas.index');
    Route::get('/recetas/create', [RecetaVetController::class, 'create'])->name('recetas.create');
    Route::post('/recetas', [RecetaVetController::class, 'store'])->name('recetas.store');
    Route::get('/recetas/{id}', [RecetaVetController::class, 'show'])->name('recetas.show');
    Route::get('/recetas/{id}/edit', [RecetaVetController::class, 'edit'])->name('recetas.edit');
    Route::put('/recetas/{id}', [RecetaVetController::class, 'update'])->name('recetas.update');
    Route::patch('/recetas/{id}/aprobar', [RecetaVetController::class, 'aprobar'])->name('recetas.aprobar');
    
    // CORRECCIÓN AQUÍ: Cambiamos POST a PATCH para que coincida con la vista
    Route::patch('/recetas/{id}/rechazar', [RecetaVetController::class, 'rechazar'])->name('recetas.rechazar');
    
    Route::delete('/recetas/{id}', [RecetaVetController::class, 'destroy'])->name('recetas.destroy');
});

// --- 5. RUTAS UTILITARIAS (AJAX / API INTERNA) ---

// Tasa de cambio
Route::get('/tasa-cambio/actual', [TasaCambioController::class, 'obtenerTasaActual'])
    ->name('tasa-cambio.actual');

// Horarios
Route::get('/horario/hoy', [HorarioController::class, 'obtenerHorarioHoy'])
    ->name('horario.hoy');
Route::get('/horario/abierto-ahora', [HorarioController::class, 'estaAbiertoAhora'])
    ->name('horario.abierto-ahora');

// Rutas para splash screen
Route::post('/splash/mark-shown', [SplashController::class, 'markAsShown'])
    ->name('splash.mark-shown');
    
Route::get('/splash/should-show', [SplashController::class, 'shouldShow'])
    ->name('splash.should-show');