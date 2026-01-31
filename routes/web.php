<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TasaCambioController;
use App\Http\Controllers\HorarioController;

Route::get('/', function () {
    return view('welcome');
});

// Ruta para obtener la tasa actual (si la necesitas para AJAX)
Route::get('/tasa-cambio/actual', [TasaCambioController::class, 'obtenerTasaActual'])
    ->name('tasa-cambio.actual');

    // Rutas para horarios (si necesitas AJAX)
Route::get('/horario/hoy', [HorarioController::class, 'obtenerHorarioHoy'])
    ->name('horario.hoy');
    
Route::get('/horario/abierto-ahora', [HorarioController::class, 'estaAbiertoAhora'])
    ->name('horario.abierto-ahora');