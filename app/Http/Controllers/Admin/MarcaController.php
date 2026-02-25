<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    public function index(Request $request)
    {
        $query = Marca::query();

        // Buscador por nombre o país
        if ($request->filled('buscar')) {
            $busqueda = $request->buscar;
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('pais_origen', 'LIKE', "%{$busqueda}%");
            });
        }

        // Filtro por estado activo/inactivo
        if ($request->filled('estado') && $request->estado !== 'all') {
            $query->where('activo', $request->estado);
        }

        $marcas = $query->orderBy('nombre', 'asc')->paginate(10);

        if ($request->ajax()) {
            return view('admin.marcas.partials._table', compact('marcas'))->render();
        }

        return view('admin.marcas.index', compact('marcas'));
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
        $marca = Marca::findOrFail($id);
        
        // Aquí en el futuro puedes agregar una validación:
        // if ($marca->productos()->count() > 0) { return error... }

        $marca->delete();

        return response()->json(['success' => true, 'message' => 'Marca eliminada.']);
    }

    // Método para cambiar el estado rápidamente desde la tabla
    public function toggleStatus($id)
    {
        $marca = Marca::findOrFail($id);
        $marca->activo = !$marca->activo; // Invierte el valor (1 a 0, o 0 a 1)
        $marca->save();

        $estadoStr = $marca->activo ? 'activada' : 'inactivada';
        return response()->json(['success' => true, 'message' => "Marca {$estadoStr} correctamente."]);
    }
}