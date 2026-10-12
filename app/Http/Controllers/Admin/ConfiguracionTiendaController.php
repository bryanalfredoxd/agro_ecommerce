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
                'nombre_empresa' => 'Agrosuministros Rodríguez',
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
            'recargo_divisas_pct' => 'required|numeric|min:0|max:100',
            'recargo_divisas_aplica' => 'required|in:ambas,web,pos',
            'recargo_bolivares_pct' => 'required|numeric|min:0|max:100',
            'recargo_bolivares_aplica' => 'required|in:ambas,web,pos',
            'recargo_cashea_pct' => 'required|numeric|min:0|max:100',
            'recargo_cashea_aplica' => 'required|in:ambas,web,pos',
            'modo_operativo' => 'required|in:automatico,manual_abierto,manual_cerrado',
            'mensaje_cierre_emergencia' => 'nullable|string|max:500',
        ]);

        $config = ConfiguracionTienda::firstOrFail();

        $datos = [
            'nombre_empresa' => $request->nombre_empresa,
            'iva_porcentaje' => $request->iva_porcentaje,
            'recargo_divisas_pct' => $request->recargo_divisas_pct,
            'recargo_divisas_aplica' => $request->recargo_divisas_aplica,
            'recargo_bolivares_pct' => $request->recargo_bolivares_pct,
            'recargo_bolivares_aplica' => $request->recargo_bolivares_aplica,
            'recargo_cashea_pct' => $request->recargo_cashea_pct,
            'recargo_cashea_aplica' => $request->recargo_cashea_aplica,
            'modo_operativo' => $request->modo_operativo,
            'mensaje_cierre_emergencia' => $request->mensaje_cierre_emergencia,
            'ultimo_editor_id' => Auth::id()
        ];

        $estadoAnterior = (bool) ($config->delivery_activo ?? true);
        if ($request->has('delivery_activo_presente')) {
            $nuevoEstado = $request->has('delivery_activo') ? 1 : 0;
            $datos['delivery_activo'] = $nuevoEstado;

            if ($nuevoEstado !== ($estadoAnterior ? 1 : 0)) {
                session()->flash('delivery_cambiado', [
                    'activo' => (bool) $nuevoEstado,
                    'mensaje' => $nuevoEstado 
                        ? 'Servicio de Delivery HABILITADO en la tienda web.' 
                        : 'Servicio de Delivery PAUSADO temporalmente en la tienda web.'
                ]);
            }
        }

        $config->update($datos);

        // Sincronizar las series de facturación con el IVA global del sistema
        \App\Models\FacturaAjuste::query()->update([
            'porcentaje_iva' => $request->iva_porcentaje
        ]);

        return redirect()->route('admin.configuracion.index')->with('success', 'Configuración actualizada correctamente.');
    }
}