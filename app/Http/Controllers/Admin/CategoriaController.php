<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CategoriaController extends Controller
{
    /**
     * Nombre de la carpeta donde se guardarán las imágenes
     * Corresponde al nombre de la funcionalidad/controlador
     */
    private $uploadFolder = 'categorias';
    
    public function index(Request $request)
    {
        $query = Categoria::with('padre');

        // 1. Buscador
        if ($request->filled('buscar')) {
            $query->where('nombre', 'LIKE', "%{$request->buscar}%");
        }

        // 2. Filtro de Estado (Activas / Deshabilitadas)
        if ($request->filled('estado')) {
            if ($request->estado === 'deshabilitadas') {
                $query->onlyTrashed();
            } elseif ($request->estado === 'todas') {
                $query->withTrashed();
            }
        }

        // 3. Filtro de Tipo (Jerarquía)
        if ($request->filled('tipo') && $request->tipo !== 'todas') {
            if ($request->tipo === 'principales') {
                $query->whereNull('categoria_padre_id');
            } elseif ($request->tipo === 'subcategorias') {
                $query->whereNotNull('categoria_padre_id');
            }
        }

        $categorias = $query->orderBy('categoria_padre_id', 'asc')->orderBy('nombre', 'asc')->paginate(10);

        if ($request->ajax()) {
            return view('admin.categorias.partials._table', compact('categorias'))->render();
        }

        // Para el select del Modal (Obtenemos activas para no asignar padres borrados)
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
            // Guardar imagen en public/img/upload/categorias/
            $data['imagen_url'] = $this->uploadImage($request->file('imagen'));
        }

        Categoria::create($data);

        return response()->json(['success' => true, 'message' => 'Categoría creada exitosamente.']);
    }

    public function update(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:100',
            'categoria_padre_id' => 'nullable|exists:categorias,id|not_in:'.$id,
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048'
        ]);

        $data = $request->only(['nombre', 'categoria_padre_id']);

        if ($request->hasFile('imagen')) {
            // Borrar imagen anterior si existe
            if ($categoria->imagen_url) {
                $this->deleteImage($categoria->imagen_url);
            }
            
            // Guardar nueva imagen
            $data['imagen_url'] = $this->uploadImage($request->file('imagen'));
        }

        $categoria->update($data);

        return response()->json(['success' => true, 'message' => 'Categoría actualizada.']);
    }

    public function destroy($id)
    {
        $categoria = Categoria::findOrFail($id);

        if ($categoria->subcategorias()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'No puedes deshabilitar esta categoría porque tiene subcategorías activas.']);
        }

        // YA NO ELIMINAMOS LA IMAGEN FÍSICA
        $categoria->delete(); // Hace el SoftDelete (llena la columna eliminado_at)

        return response()->json(['success' => true, 'message' => 'Categoría deshabilitada correctamente.']);
    }

    public function restore($id)
    {
        // Buscamos la categoría incluso si está borrada
        $categoria = Categoria::withTrashed()->findOrFail($id);
        
        // Si es una subcategoría, verificamos que su padre no esté eliminado
        if ($categoria->categoria_padre_id) {
            $padre = Categoria::withTrashed()->find($categoria->categoria_padre_id);
            if ($padre && $padre->trashed()) {
                return response()->json(['success' => false, 'message' => 'No puedes reactivar esta subcategoría porque su categoría padre está deshabilitada.']);
            }
        }

        $categoria->restore(); // Reactiva la categoría (vuelve eliminado_at a NULL)

        return response()->json(['success' => true, 'message' => 'Categoría reactivada y visible en la tienda.']);
    }

    /**
     * Método privado para subir imágenes
     * Usa la propiedad $uploadFolder para determinar la carpeta
     */
    private function uploadImage($file)
    {
        // Definir la carpeta de destino dinámica
        $destinationPath = public_path('img/upload/' . $this->uploadFolder);
        
        // Crear la carpeta si no existe
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }
        
        // Generar nombre único para la imagen
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        // Mover el archivo a la carpeta destino
        $file->move($destinationPath, $fileName);
        
        // Retornar la ruta relativa para guardar en BD
        return 'img/upload/' . $this->uploadFolder . '/' . $fileName;
    }

    /**
     * Método privado para eliminar imágenes
     */
    private function deleteImage($imagePath)
    {
        $fullPath = public_path($imagePath);
        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}