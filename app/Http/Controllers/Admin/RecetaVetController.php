<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RecetaVet;
use App\Models\RecetaProducto;
use App\Models\Producto;
use App\Models\User;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class RecetaVetController extends Controller
{
    public function index()
    {
        $recetas = RecetaVet::with(['cliente', 'pedido', 'recetaProductos.producto'])
            ->orderBy('creado_at', 'desc')
            ->paginate(15);

        $estadisticas = [
            'total' => RecetaVet::count(),
            'pendientes' => RecetaVet::where('estado', 'pendiente')->count(),
            'aprobadas' => RecetaVet::where('estado', 'aprobada')->count(),
            'rechazadas' => RecetaVet::where('estado', 'rechazada')->count(),
            'expiradas' => RecetaVet::where('estado', 'expirada')->count(),
            'por_vencer' => RecetaVet::porVencer()->count(),
        ];

        return view('admin.recetas.index', compact('recetas', 'estadisticas'));
    }

    public function show($id)
    {
        $receta = RecetaVet::with(['cliente', 'pedido', 'revisor', 'recetaProductos.producto'])
            ->findOrFail($id);

        return view('admin.recetas.show', compact('receta'));
    }

    public function create()
    {
        $productos = Producto::where('es_controlado', true)->get();
        $clientes = User::where('activo', true)->orderBy('nombre')->get();
        $pedidos = Pedido::with('usuario')
            ->whereIn('estado', ['pendiente', 'pagado', 'preparacion'])
            ->orderBy('creado_at', 'desc')
            ->get();

        return view('admin.recetas.create', compact('productos', 'clientes', 'pedidos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'cliente_usuario_id' => 'required|exists:usuarios,id',
            'veterinario_nombre' => 'required|string|max:200',
            'veterinario_matricula' => 'nullable|string|max:50',
            'cliente_animal_tipo' => 'required|string|max:100',
            'cliente_animal_cantidad' => 'nullable|integer|min:1',
            'fecha_prescription' => 'required|date',
            'fecha_vencimiento_receta' => 'required|date|after:fecha_prescription',
            'archivo_url' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB
            'observaciones' => 'nullable|string',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad_prescrita' => 'required|numeric|min:0.001',
            'productos.*.dosis_instrucciones' => 'nullable|string',
        ]);

        // Subir archivo si existe
        $archivoUrl = null;
        if ($request->hasFile('archivo_url')) {
            $archivoUrl = $request->file('archivo_url')->store('recetas', 'public');
        }

        // Crear receta
        $receta = RecetaVet::create([
            'pedido_id' => $request->pedido_id,
            'cliente_usuario_id' => $request->cliente_usuario_id,
            'veterinario_nombre' => $request->veterinario_nombre,
            'veterinario_matricula' => $request->veterinario_matricula,
            'cliente_animal_tipo' => $request->cliente_animal_tipo,
            'cliente_animal_cantidad' => $request->cliente_animal_cantidad,
            'fecha_prescription' => $request->fecha_prescription,
            'fecha_vencimiento_receta' => $request->fecha_vencimiento_receta,
            'archivo_url' => $archivoUrl,
            'observaciones' => $request->observaciones,
            'estado' => 'pendiente',
        ]);

        // Crear productos de la receta
        foreach ($request->productos as $productoData) {
            RecetaProducto::create([
                'receta_id' => $receta->id,
                'producto_id' => $productoData['producto_id'],
                'cantidad_prescrita' => $productoData['cantidad_prescrita'],
                'dosis_instrucciones' => $productoData['dosis_instrucciones'] ?? null,
                'autorizado' => false,
            ]);
        }

        return redirect()->route('admin.recetas.show', $receta->id)
            ->with('success', 'Receta veterinaria creada correctamente.');
    }

    public function edit($id)
    {
        $receta = RecetaVet::with(['recetaProductos.producto'])->findOrFail($id);
        $productos = Producto::where('es_controlado', true)->get();
        $clientes = User::where('activo', true)->orderBy('nombre')->get();
        $pedidos = Pedido::with('usuario')
            ->whereIn('estado', ['pendiente', 'pagado', 'preparacion'])
            ->orderBy('creado_at', 'desc')
            ->get();

        return view('admin.recetas.edit', compact('receta', 'productos', 'clientes', 'pedidos'));
    }

    public function update(Request $request, $id)
    {
        $receta = RecetaVet::findOrFail($id);

        $request->validate([
            'veterinario_nombre' => 'required|string|max:200',
            'veterinario_matricula' => 'nullable|string|max:50',
            'cliente_animal_tipo' => 'required|string|max:100',
            'cliente_animal_cantidad' => 'nullable|integer|min:1',
            'fecha_prescription' => 'required|date',
            'fecha_vencimiento_receta' => 'required|date|after:fecha_prescription',
            'archivo_url' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'observaciones' => 'nullable|string',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad_prescrita' => 'required|numeric|min:0.001',
            'productos.*.dosis_instrucciones' => 'nullable|string',
        ]);

        // Subir nuevo archivo si existe
        if ($request->hasFile('archivo_url')) {
            // Eliminar archivo anterior si existe
            if ($receta->archivo_url) {
                Storage::disk('public')->delete($receta->archivo_url);
            }
            $archivoUrl = $request->file('archivo_url')->store('recetas', 'public');
            $receta->archivo_url = $archivoUrl;
        }

        // Actualizar receta
        $receta->update([
            'veterinario_nombre' => $request->veterinario_nombre,
            'veterinario_matricula' => $request->veterinario_matricula,
            'cliente_animal_tipo' => $request->cliente_animal_tipo,
            'cliente_animal_cantidad' => $request->cliente_animal_cantidad,
            'fecha_prescription' => $request->fecha_prescription,
            'fecha_vencimiento_receta' => $request->fecha_vencimiento_receta,
            'observaciones' => $request->observaciones,
        ]);

        // Eliminar productos existentes y crear nuevos
        $receta->recetaProductos()->delete();
        foreach ($request->productos as $productoData) {
            RecetaProducto::create([
                'receta_id' => $receta->id,
                'producto_id' => $productoData['producto_id'],
                'cantidad_prescrita' => $productoData['cantidad_prescrita'],
                'dosis_instrucciones' => $productoData['dosis_instrucciones'] ?? null,
                'autorizado' => false,
            ]);
        }

        return redirect()->route('admin.recetas.show', $receta->id)
            ->with('success', 'Receta veterinaria actualizada correctamente.');
    }

    public function aprobar(Request $request, $id)
    {
        $request->validate([
            'observaciones_revision' => 'nullable|string',
        ]);

        $receta = RecetaVet::findOrFail($id);

        if (!$receta->puedeSerRevisada()) {
            return redirect()->back()->with('error', 'Esta receta no puede ser revisada.');
        }

        $receta->update([
            'estado' => 'aprobada',
            'usuario_revisa_id' => Auth::id(),
            'fecha_revision' => now(),
            'observaciones' => ($receta->observaciones ? $receta->observaciones . "\n\n" : '') .
                              "APROBADA: " . ($request->observaciones_revision ?? 'Sin observaciones adicionales'),
        ]);

        // Autorizar todos los productos
        $receta->recetaProductos()->update(['autorizado' => true]);

        return redirect()->route('admin.recetas.show', $receta->id)
            ->with('success', 'Receta veterinaria aprobada correctamente.');
    }

    public function rechazar(Request $request, $id)
    {
        $request->validate([
            'observaciones_revision' => 'required|string|min:3',
        ]);

        $receta = RecetaVet::findOrFail($id);

        if (!$receta->puedeSerRevisada()) {
            return redirect()->back()->with('error', 'Esta receta no puede ser revisada.');
        }

        $receta->update([
            'estado' => 'rechazada',
            'usuario_revisa_id' => Auth::id(),
            'fecha_revision' => now(),
            'observaciones' => ($receta->observaciones ? $receta->observaciones . "\n\n" : '') .
                              "RECHAZADA: " . $request->observaciones_revision,
        ]);

        return redirect()->route('admin.recetas.show', $receta->id)
            ->with('success', 'Receta veterinaria rechazada.');
    }

    public function destroy($id)
    {
        $receta = RecetaVet::findOrFail($id);

        // Eliminar archivo si existe
        if ($receta->archivo_url) {
            Storage::disk('public')->delete($receta->archivo_url);
        }

        $receta->delete();

        return redirect()->route('admin.recetas.index')
            ->with('success', 'Receta veterinaria eliminada correctamente.');
    }
}