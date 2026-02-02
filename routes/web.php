<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TasaCambioController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\HomeController; 
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


// --- 5. RUTAS UTILITARIAS (AJAX / API INTERNA) ---

// Tasa de cambio
Route::get('/tasa-cambio/actual', [TasaCambioController::class, 'obtenerTasaActual'])
    ->name('tasa-cambio.actual');

// Horarios
Route::get('/horario/hoy', [HorarioController::class, 'obtenerHorarioHoy'])
    ->name('horario.hoy');
Route::get('/horario/abierto-ahora', [HorarioController::class, 'estaAbiertoAhora'])
    ->name('horario.abierto-ahora');