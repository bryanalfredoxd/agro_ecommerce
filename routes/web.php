<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\TasaCambioController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí registramos las rutas para tu aplicación.
|
*/

// --- 1. RUTA PRINCIPAL (HOME) ---
Route::get('/', function () {
    return view('welcome');
})->name('home');


// --- 2. RUTAS DE REGISTRO (GUEST) ---
// Solo accesibles si NO estás logueado
Route::middleware('guest')->group(function () {
    // Mostrar formulario
    Route::get('/registro', [RegisterController::class, 'create'])->name('register');
    
    // Procesar datos
    Route::post('/registro', [RegisterController::class, 'store']);

 // --- LOGIN (NUEVO) ---
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    // Aquí agregarías las rutas de Login cuando creemos el LoginController
    // Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    // Route::post('/login', [LoginController::class, 'login']);
});


// --- 3. RUTAS DE AUTENTICACIÓN (AUTH) ---
// Solo accesibles si ESTÁS logueado
Route::middleware('auth')->group(function () {
    
    // Cerrar Sesión (Esta es la que usa tu Header/Menu Móvil)
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');

    // Ejemplo de ruta protegida (Panel de Usuario)
    Route::get('/dashboard', function () {
        return view('welcome'); // Por ahora redirige al home, luego harás una vista 'dashboard'
    })->name('dashboard');
});

// Ruta para obtener la tasa actual (si la necesitas para AJAX)
Route::get('/tasa-cambio/actual', [TasaCambioController::class, 'obtenerTasaActual'])
    ->name('tasa-cambio.actual');

    // Rutas para horarios (si necesitas AJAX)
Route::get('/horario/hoy', [HorarioController::class, 'obtenerHorarioHoy'])
    ->name('horario.hoy');
    
Route::get('/horario/abierto-ahora', [HorarioController::class, 'estaAbiertoAhora'])
    ->name('horario.abierto-ahora');



