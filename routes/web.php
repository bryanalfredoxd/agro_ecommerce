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
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\MarcaController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\HorarioadminController;

// Nota: Eliminé CategoriaController de aquí porque no lo estamos usando en la Home

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. RUTA PRINCIPAL (HOME) ---
// CORRECCIÓN: Usamos HomeController para que cargue los productos y categorías
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
    
    // Actualizar contraseña (si no usas Fortify/Breeze nativo)
    Route::put('/perfil/password', [PerfilController::class, 'updatePassword'])->name('password.update');

    Route::post('/perfil/direccion', [DireccionController::class, 'store'])->name('direccion.store');

    // NUEVA RUTA: Cambiar dirección principal
    Route::patch('/perfil/direccion/{id}/principal', [DireccionController::class, 'setPrincipal'])->name('direccion.principal');
    
    // OPCIONAL: Ruta para eliminar (te la dejo lista por si acaso)
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




// ==========================================
// RUTAS DE ADMINISTRACIÓN (Solo para rol_id = 1)
// ==========================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Pantalla - Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Seccion - Usuarios y Roles

    // Pantalla - Roles y Permisos
    Route::resource('roles', RoleController::class)->except(['create', 'show', 'edit']);

    // Pantalla - Lista de Usuarios
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/{id}/permisos-extra', [UsuarioController::class, 'getPermisosExtra']);
    Route::post('/usuarios/{id}/permisos-extra', [UsuarioController::class, 'updatePermisosExtra']);

// Seccion - Finanzas y Caja

    // Pantalla - Tasas de Cambio
    Route::get('/tasas-cambio', [App\Http\Controllers\Admin\TasaCambioController::class, 'index'])->name('tasas-cambio.index');
    Route::post('/tasas-cambio', [App\Http\Controllers\Admin\TasaCambioController::class, 'store'])->name('tasas-cambio.store');
    
// Seccion - Catalogo e Inventario

    //Pantalla - Categorias
    Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
    Route::post('/categorias', [CategoriaController::class, 'store'])->name('categorias.store');
    Route::post('/categorias/{id}', [CategoriaController::class, 'update'])->name('categorias.update');
    Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy'])->name('categorias.destroy');

    //Pantalla - Marcas
    Route::get('/marcas', [MarcaController::class, 'index'])->name('marcas.index');
    Route::post('/marcas', [MarcaController::class, 'store'])->name('marcas.store');
    Route::put('/marcas/{id}', [MarcaController::class, 'update'])->name('marcas.update');
    Route::delete('/marcas/{id}', [MarcaController::class, 'destroy'])->name('marcas.destroy');
    Route::post('/marcas/{id}/toggle', [MarcaController::class, 'toggleStatus']);

    //Pantalla - Productos
    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::get('/productos/crear', [ProductoController::class, 'create'])->name('productos.create'); 
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store'); 
    Route::get('/productos/{id}/editar', [ProductoController::class, 'edit'])->name('productos.edit'); 
    Route::put('/productos/{id}', [ProductoController::class, 'update'])->name('productos.update'); 
    Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');
    Route::post('/productos/{id}/destacado', [ProductoController::class, 'toggleDestacado']);

//Seccion - Sistema y Ajustes

    //Pantalla - Horarios
    Route::get('/horarios', [HorarioadminController::class, 'index'])->name('horarios.index');
    Route::post('/horarios/actualizar', [HorarioadminController::class, 'updateAll'])->name('horarios.updateAll');

});