<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    public function index(Request $request)
    {
        $query = Marca::withCount('productos');

        // Buscador por nombre o país
        if ($request->filled('buscar')) {
            $busqueda = trim($request->buscar);
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('pais_origen', 'LIKE', "%{$busqueda}%");
            });
        }

        // Filtro por estado activo/inactivo
        if ($request->filled('estado') && $request->estado !== 'all') {
            $query->where('activo', $request->estado);
        }

        // Estadísticas para KPIs y Contadores
        $stats = [
            'total' => Marca::count(),
            'activas' => Marca::where('activo', 1)->count(),
            'inactivas' => Marca::where('activo', 0)->count(),
            'con_productos' => Marca::has('productos')->count(),
        ];

        $marcas = $query->orderBy('nombre', 'asc')->paginate(12)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('admin.marcas.partials._table', compact('marcas', 'stats'))->render(),
                'stats' => $stats
            ]);
        }

        return view('admin.marcas.index', compact('marcas', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:marcas,nombre',
            'pais_origen' => 'nullable|string|max:100',
        ]);

        $data = $request->only(['nombre', 'pais_origen']);
        $data['activo'] = $request->has('activo') ? 1 : 0;

        Marca::create($data);

        return response()->json(['success' => true, 'message' => 'Marca registrada exitosamente.']);
    }

    public function update(Request $request, $id)
    {
        $marca = Marca::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:100|unique:marcas,nombre,' . $id,
            'pais_origen' => 'nullable|string|max:100',
        ]);

        $data = $request->only(['nombre', 'pais_origen']);
        $data['activo'] = $request->has('activo') ? 1 : 0;

        $marca->update($data);

        return response()->json(['success' => true, 'message' => 'Marca actualizada correctamente.']);
    }

    public function destroy($id)
    {
        $marca = Marca::withCount('productos')->findOrFail($id);
        
        // Protección de integridad referencial
        if ($marca->productos_count > 0) {
            return response()->json([
                'success' => false, 
                'message' => "No puedes eliminar '{$marca->nombre}' porque tiene {$marca->productos_count} producto(s) asignado(s) en el catálogo. Reasigna los productos primero o desactiva la marca."
            ], 422);
        }

        $marca->delete();

        return response()->json(['success' => true, 'message' => 'Marca eliminada correctamente.']);
    }

    // Método para cambiar el estado rápidamente desde la tabla (Toggle AJAX)
    public function toggleStatus($id)
    {
        $marca = Marca::findOrFail($id);
        $marca->activo = !$marca->activo;
        $marca->save();

        $estadoStr = $marca->activo ? 'activada' : 'inactivada';
        return response()->json([
            'success' => true, 
            'activo' => $marca->activo,
            'message' => "Marca '{$marca->nombre}' {$estadoStr} exitosamente."
        ]);
    }
}