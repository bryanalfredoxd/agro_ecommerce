<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ZonaDelivery;
use Illuminate\Http\Request;

class ZonaDeliveryController extends Controller
{
    /**
     * Pantalla principal de gestión de zonas de delivery con mapa Leaflet
     */
    public function index(Request $request)
    {
        $zonas = ZonaDelivery::withCount(['direcciones', 'pedidos'])
            ->orderBy('id', 'asc')
            ->get();

        $totalZonas = $zonas->count();
        $zonasActivas = $zonas->where('activa', 1)->count();
        $precioPromedio = $zonas->avg('precio_delivery_usd') ?? 0;
        $maxRadio = $zonas->max('radio_km') ?? 5;

        // Si la petición es AJAX y solicita solo el JSON de zonas para el mapa
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'zonas' => $zonas
            ]);
        }

        $configTienda = \App\Models\ConfiguracionTienda::first();
        $tiendaLat = $configTienda && $configTienda->latitud ? (float) $configTienda->latitud : 8.7622;
        $tiendaLng = $configTienda && $configTienda->longitud ? (float) $configTienda->longitud : -70.4106;
        $deliveryActivo = $configTienda ? (bool) ($configTienda->delivery_activo ?? true) : true;

        return view('admin.zonas_delivery.index', compact(
            'zonas',
            'totalZonas',
            'zonasActivas',
            'precioPromedio',
            'maxRadio',
            'tiendaLat',
            'tiendaLng',
            'configTienda',
            'deliveryActivo'
        ));
    }

    /**
     * Crear una nueva zona de delivery
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_zona' => 'required|string|max:100',
            'precio_delivery_usd' => 'required|numeric|min:0',
            'tiempo_estimado_minutos' => 'nullable|integer|min:1|max:1440',
            'requiere_vehiculo_carga' => 'nullable|boolean',
            'activa' => 'nullable|boolean',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'radio_km' => 'nullable|numeric|min:0.1|max:200',
            'color_mapa' => 'nullable|string|max:20',
            'descripcion' => 'nullable|string|max:500'
        ]);

        $validated['requiere_vehiculo_carga'] = $request->has('requiere_vehiculo_carga') ? 1 : 0;
        $validated['activa'] = $request->has('activa') ? 1 : 0;
        
        // Defaults si no se especifican coordenadas (Barinitas centro)
        $validated['latitud'] = $validated['latitud'] ?? 8.7622;
        $validated['longitud'] = $validated['longitud'] ?? -70.4106;
        $validated['radio_km'] = $validated['radio_km'] ?? 3.0;
        $validated['color_mapa'] = $validated['color_mapa'] ?? '#1e549e';

        $zona = ZonaDelivery::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Zona de delivery creada exitosamente.',
                'zona' => $zona
            ]);
        }

        return redirect()->route('admin.zonas_delivery.index')
            ->with('success', "Zona \"{$zona->nombre_zona}\" creada con éxito.");
    }

    /**
     * Actualizar una zona existente
     */
    public function update(Request $request, $id)
    {
        $zona = ZonaDelivery::findOrFail($id);

        $validated = $request->validate([
            'nombre_zona' => 'required|string|max:100',
            'precio_delivery_usd' => 'required|numeric|min:0',
            'tiempo_estimado_minutos' => 'nullable|integer|min:1|max:1440',
            'requiere_vehiculo_carga' => 'nullable|boolean',
            'activa' => 'nullable|boolean',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'radio_km' => 'nullable|numeric|min:0.1|max:200',
            'color_mapa' => 'nullable|string|max:20',
            'descripcion' => 'nullable|string|max:500'
        ]);

        $validated['requiere_vehiculo_carga'] = $request->has('requiere_vehiculo_carga') ? 1 : 0;
        $validated['activa'] = $request->has('activa') ? 1 : 0;

        $zona->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Zona actualizada con éxito.',
                'zona' => $zona
            ]);
        }

        return redirect()->route('admin.zonas_delivery.index')
            ->with('success', "Zona \"{$zona->nombre_zona}\" actualizada correctamente.");
    }

    /**
     * Alternar estado activo/inactivo rápidamente
     */
    public function toggle($id)
    {
        $zona = ZonaDelivery::findOrFail($id);
        $zona->activa = !$zona->activa;
        $zona->save();

        return response()->json([
            'success' => true,
            'activa' => $zona->activa,
            'message' => $zona->activa ? 'Zona activada.' : 'Zona pausada.'
        ]);
    }

    /**
     * Alternar el interruptor maestro del servicio de Delivery (Habilitado / Pausado)
     */
    public function toggleMasterDelivery(Request $request)
    {
        $config = \App\Models\ConfiguracionTienda::firstOrCreate(['id' => 1]);
        if ($request->has('estado')) {
            $nuevoEstado = $request->boolean('estado');
        } else {
            $nuevoEstado = !((bool) ($config->delivery_activo ?? true));
        }
        $config->delivery_activo = $nuevoEstado ? 1 : 0;
        $config->ultimo_editor_id = auth()->id();
        $config->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'delivery_activo' => $nuevoEstado,
                'message' => $nuevoEstado ? 'Servicio de Delivery HABILITADO en la tienda.' : 'Servicio de Delivery PAUSADO temporalmente en la tienda.'
            ]);
        }

        return redirect()->route('admin.zonas_delivery.index')
            ->with('success', $nuevoEstado ? 'Servicio de Delivery HABILITADO exitosamente.' : 'Servicio de Delivery PAUSADO temporalmente.');
    }

    /**
     * Eliminar zona de delivery (con protección si tiene pedidos o direcciones)
     */
    public function destroy(Request $request, $id)
    {
        $zona = ZonaDelivery::withCount(['direcciones', 'pedidos'])->findOrFail($id);

        if ($zona->pedidos_count > 0 || $zona->direcciones_count > 0) {
            // No podemos borrarla por integridad referencial, la desactivamos
            $zona->activa = 0;
            $zona->save();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'action' => 'desactivada',
                    'message' => "La zona tiene {$zona->pedidos_count} pedidos asociados. Se ha desactivado en lugar de eliminarse para proteger el historial."
                ]);
            }

            return redirect()->route('admin.zonas_delivery.index')
                ->with('warning', "La zona \"{$zona->nombre_zona}\" tiene historial de pedidos. Ha sido pausada en lugar de eliminarse.");
        }

        $zona->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'action' => 'eliminada',
                'message' => 'Zona eliminada permanentemente del sistema.'
            ]);
        }

        return redirect()->route('admin.zonas_delivery.index')
            ->with('success', "Zona \"{$zona->nombre_zona}\" eliminada correctamente.");
    }

    /**
     * Actualizar la geolocalización central de la tienda
     */
    public function actualizarUbicacionTienda(Request $request)
    {
        $request->validate([
            'latitud' => 'required|numeric|between:-90,90',
            'longitud' => 'required|numeric|between:-180,180',
        ]);

        $config = \App\Models\ConfiguracionTienda::firstOrCreate(['id' => 1]);
        $config->update([
            'latitud' => $request->latitud,
            'longitud' => $request->longitud,
            'ultimo_editor_id' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ubicación central de la tienda guardada correctamente.',
            'latitud' => (float) $config->latitud,
            'longitud' => (float) $config->longitud,
        ]);
    }
}
