<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; // ¡Añadimos esto!

class ProductoController extends Controller
{
    // Nombre de la carpeta destino
    private $uploadFolder = 'productos';

    public function index(Request $request)
    {
        // Traemos los productos con sus relaciones para evitar el problema N+1
        $query = Producto::with(['categoria', 'marca']);

        // 1. Buscador (Nombre, SKU o Código de Barras)
        if ($request->filled('buscar')) {
            $busqueda = $request->buscar;
            $query->where(function($q) use ($busqueda) {
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('sku', 'LIKE', "%{$busqueda}%")
                  ->orWhere('codigo_barras', 'LIKE', "%{$busqueda}%");
            });
        }

        // 2. Filtro por Categoría
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        // 3. Filtro rápido (Stock Crítico, Destacados, Combos, Recetados)
        if ($request->filled('filtro_rapido')) {
            switch ($request->filtro_rapido) {
                case 'suspendidos':
                    $query->onlyTrashed(); 
                    break;
                case 'critico':
                    $query->whereColumn('stock_total', '<=', 'stock_minimo_alerta');
                    break;
                case 'destacados':
                    $query->where('destacado', 1);
                    break;
                case 'combos':
                    $query->where('es_combo', 1);
                    break;
                case 'recetados':
                    $query->where('es_controlado', 1);
                    break;
            }
        }

        $productos = $query->orderBy('nombre', 'asc')->paginate(10);

        if ($request->ajax()) {
            return view('admin.productos.partials._table', compact('productos'))->render();
        }

        // Datos para los filtros
        $categorias = Categoria::orderBy('nombre')->get();
        $marcas = Marca::orderBy('nombre')->get();

        return view('admin.productos.index', compact('productos', 'categorias', 'marcas'));
    }

    public function toggleDestacado($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->destacado = !$producto->destacado;
        $producto->save();

        $msj = $producto->destacado ? 'Producto destacado en la tienda.' : 'Producto removido de destacados.';
        return response()->json(['success' => true, 'message' => $msj]);
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->delete(); 

        return response()->json(['success' => true, 'message' => 'Producto eliminado correctamente del catálogo.']);
    }

    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        $marcas = Marca::orderBy('nombre')->get();
        return view('admin.productos.form', compact('categorias', 'marcas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:productos,sku',
            'precio_venta_usd' => 'required|numeric|min:0',
            'imagen' => 'nullable|image|max:2048',
            'stock_total' => 'nullable|numeric|min:0'
        ]);

        $data = $request->except(['imagen', '_token']);
        
        $data['es_controlado'] = $request->has('es_controlado') ? 1 : 0;
        $data['es_combo'] = $request->has('es_combo') ? 1 : 0;

        // --- CAMBIO AQUÍ ---
        if ($request->hasFile('imagen')) {
            $data['imagen_url'] = $this->uploadImage($request->file('imagen'));
        }

        Producto::create($data);

        return redirect()->route('admin.productos.index')->with('success', 'Producto creado exitosamente.');
    }

    public function edit($id)
    {
        $producto = Producto::findOrFail($id);
        $categorias = Categoria::orderBy('nombre')->get();
        $marcas = Marca::orderBy('nombre')->get();
        
        return view('admin.productos.form', compact('producto', 'categorias', 'marcas'));
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:productos,sku,'.$id,
            'precio_venta_usd' => 'required|numeric|min:0',
            'imagen' => 'nullable|image|max:2048',
            'stock_total' => 'nullable|numeric|min:0'
        ]);

        $data = $request->except(['imagen', '_token', '_method']);
        
        $data['es_controlado'] = $request->has('es_controlado') ? 1 : 0;
        $data['es_combo'] = $request->has('es_combo') ? 1 : 0;

        // --- CAMBIO AQUÍ ---
        if ($request->hasFile('imagen')) {
            if ($producto->imagen_url) {
                $this->deleteImage($producto->imagen_url);
            }
            $data['imagen_url'] = $this->uploadImage($request->file('imagen'));
        }

        $producto->update($data);

        return redirect()->route('admin.productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function restore($id)
    {
        $producto = Producto::withTrashed()->findOrFail($id);
        $producto->restore(); 

        return response()->json(['success' => true, 'message' => 'Producto reactivado y visible nuevamente.']);
    }

    // ==========================================
    // MÉTODOS PRIVADOS PARA IMÁGENES
    // ==========================================

    private function uploadImage($file)
    {
        $destinationPath = public_path('img/upload/' . $this->uploadFolder);
        
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }
        
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($destinationPath, $fileName);
        
        return 'img/upload/' . $this->uploadFolder . '/' . $fileName;
    }

    private function deleteImage($imagePath)
    {
        $fullPath = public_path($imagePath);
        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}