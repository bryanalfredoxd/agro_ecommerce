<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DireccionUsuario;

class DireccionController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validamos también latitud y longitud
        $validated = $request->validate([
            'alias' => 'required|string|max:50',
            'zona_delivery_id' => 'nullable|exists:zonas_delivery,id',
            'direccion_texto' => 'required|string',
            'referencia_punto' => 'nullable|string',
            'geo_latitud' => 'nullable|numeric',
            'geo_longitud' => 'nullable|numeric',
            'es_principal' => 'nullable|boolean'
        ]);

        $user = auth()->user();

        // 2. LÓGICA DE PRINCIPAL:
        if ($request->has('es_principal') && $request->es_principal == 1) {
            $user->direcciones()->where('es_principal', 1)->update(['es_principal' => 0]);
        }

        // Si es la primera dirección, marcarla como principal por defecto
        $esPrincipal = ($request->has('es_principal') && $request->es_principal == 1) || ($user->direcciones()->count() === 0);

        // 3. Crear registro
        $user->direcciones()->create([
            'alias' => $validated['alias'],
            'zona_delivery_id' => $validated['zona_delivery_id'] ?? null,
            'direccion_texto' => $validated['direccion_texto'],
            'referencia_punto' => $validated['referencia_punto'] ?? null,
            'geo_latitud' => $validated['geo_latitud'] ?? null,
            'geo_longitud' => $validated['geo_longitud'] ?? null,
            'es_principal' => $esPrincipal ? 1 : 0,
        ]);

        return back()->with('success-message', 'Dirección y ubicación guardadas correctamente.');
    }

    public function setPrincipal($id)
    {
        $user = auth()->user();
        
        // Buscamos la dirección y verificamos que sea del usuario (seguridad)
        $direccion = $user->direcciones()->findOrFail($id);

        // 1. Quitamos 'principal' a TODAS las direcciones de este usuario
        $user->direcciones()->update(['es_principal' => 0]);

        // 2. Asignamos 'principal' a la seleccionada
        $direccion->update(['es_principal' => 1]);

        return back()->with('success-message', 'Dirección principal actualizada correctamente.');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $direccion = $user->direcciones()->findOrFail($id);
        
        $direccion->delete();
        
        return back()->with('success-message', 'Dirección eliminada.');
    }
}