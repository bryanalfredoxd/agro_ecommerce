<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        // Traemos las categorías con su padre para la tabla
        $query = Categoria::with('padre');

        if ($request->filled('buscar')) {
            $query->where('nombre', 'LIKE', "%{$request->buscar}%");
        }

        $categorias = $query->orderBy('categoria_padre_id', 'asc')->orderBy('nombre', 'asc')->paginate(10);

        // Si es petición AJAX, devolvemos solo la tabla
        if ($request->ajax()) {
            return view('admin.categorias.partials._table', compact('categorias'))->render();
        }

        // Para el select del Modal (Solo categorías principales o todas, elegimos todas para flexibilidad)
        $categoriasPadre = Categoria::orderBy('nombre')->get();

        return view('admin.categorias.index', compact('categorias', 'categoriasPadre'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'categoria_padre_id' => 'nullable|exists:categorias,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048'
        ]);

        $data = $request->only(['nombre', 'categoria_padre_id']);

        if ($request->hasFile('imagen')) {
            // Guarda en storage/app/public/categorias
            $data['imagen_url'] = $request->file('imagen')->store('categorias', 'public');
        }

        Categoria::create($data);

        return response()->json(['success' => true, 'message' => 'Categoría creada exitosamente.']);
    }

    public function update(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:100',
            'categoria_padre_id' => 'nullable|exists:categorias,id|not_in:'.$id, // No puede ser padre de sí misma
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048'
        ]);

        $data = $request->only(['nombre', 'categoria_padre_id']);

        if ($request->hasFile('imagen')) {
            // Borrar imagen anterior si existe
            if ($categoria->imagen_url) {
                Storage::disk('public')->delete($categoria->imagen_url);
            }
            $data['imagen_url'] = $request->file('imagen')->store('categorias', 'public');
        }

        $categoria->update($data);

        return response()->json(['success' => true, 'message' => 'Categoría actualizada.']);
    }

    public function destroy($id)
    {
        $categoria = Categoria::findOrFail($id);

        // Validar que no tenga subcategorías antes de eliminar
        if ($categoria->subcategorias()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'No puedes eliminar esta categoría porque tiene subcategorías asociadas.']);
        }

        // Opcional: Podrías validar también si tiene productos asociados si tuvieras el modelo Producto

        // Eliminar imagen física
        if ($categoria->imagen_url) {
            Storage::disk('public')->delete($categoria->imagen_url);
        }

        $categoria->delete();

        return response()->json(['success' => true, 'message' => 'Categoría eliminada.']);
    }
}