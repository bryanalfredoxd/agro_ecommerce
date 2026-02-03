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
            'direccion_texto' => 'required|string',
            'referencia_punto' => 'nullable|string',
            'geo_latitud' => 'nullable|numeric',  // Nuevo
            'geo_longitud' => 'nullable|numeric', // Nuevo
            'es_principal' => 'nullable|boolean'
        ]);

        $user = auth()->user();

        // 2. LÓGICA DE PRINCIPAL:
        // Si el usuario marcó el check (viene como '1'), buscamos TODAS las direcciones 
        // de este usuario que sean principales y las ponemos en 0.
        if ($request->has('es_principal') && $request->es_principal == 1) {
            $user->direcciones()->where('es_principal', 1)->update(['es_principal' => 0]);
        }

        // 3. Crear registro
        $user->direcciones()->create([
            'alias' => $validated['alias'],
            'direccion_texto' => $validated['direccion_texto'],
            'referencia_punto' => $validated['referencia_punto'],
            'geo_latitud' => $validated['geo_latitud'],   // Guardamos Lat
            'geo_longitud' => $validated['geo_longitud'], // Guardamos Lng
            'es_principal' => $request->has('es_principal') ? 1 : 0,
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