<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionTienda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfiguracionTiendaController extends Controller
{
    public function index()
    {
        // Obtenemos la configuración (si no existe, la crea con valores por defecto)
        $config = ConfiguracionTienda::firstOrCreate(
            ['id' => 1], 
            [
                'nombre_empresa' => 'Corpo Agrícola',
                'iva_porcentaje' => 16.00,
                'modo_operativo' => 'automatico',
                'mensaje_cierre_emergencia' => 'Nuestra tienda se encuentra temporalmente cerrada por mantenimiento. Disculpe las molestias.'
            ]
        );

        // Cargamos la relación para saber quién fue el último en editar
        $config->load('ultimoEditor');

        return view('admin.configuracion.index', compact('config'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nombre_empresa' => 'required|string|max:100',
            'iva_porcentaje' => 'required|numeric|min:0|max:100',
            'modo_operativo' => 'required|in:automatico,manual_abierto,manual_cerrado',
            'mensaje_cierre_emergencia' => 'nullable|string|max:500',
        ]);

        $config = ConfiguracionTienda::firstOrFail();

        $config->update([
            'nombre_empresa' => $request->nombre_empresa,
            'iva_porcentaje' => $request->iva_porcentaje,
            'modo_operativo' => $request->modo_operativo,
            'mensaje_cierre_emergencia' => $request->mensaje_cierre_emergencia,
            'ultimo_editor_id' => Auth::id()
        ]);

        return redirect()->route('admin.configuracion.index')->with('success', 'Configuración actualizada correctamente.');
    }
}