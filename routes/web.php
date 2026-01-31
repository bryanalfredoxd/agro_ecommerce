<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TasaCambioController;

Route::get('/', function () {
    return view('welcome');
});

// Ruta para obtener la tasa actual (si la necesitas para AJAX)
Route::get('/tasa-cambio/actual', [TasaCambioController::class, 'obtenerTasaActual'])
    ->name('tasa-cambio.actual');