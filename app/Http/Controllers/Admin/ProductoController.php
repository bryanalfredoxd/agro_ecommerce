<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
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

    // Acción rápida: Marcar/Desmarcar como Destacado (Estrella)
    public function toggleDestacado($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->destacado = !$producto->destacado;
        $producto->save();

        $msj = $producto->destacado ? 'Producto destacado en la tienda.' : 'Producto removido de destacados.';
        return response()->json(['success' => true, 'message' => $msj]);
    }

    // Borrado lógico
    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->delete(); // Esto llenará la columna 'eliminado_at' automáticamente gracias al SoftDeletes

        return response()->json(['success' => true, 'message' => 'Producto eliminado correctamente del catálogo.']);
    }

    // Mostrar pantalla de Creación
    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        $marcas = Marca::orderBy('nombre')->get();
        return view('admin.productos.form', compact('categorias', 'marcas'));
    }

    // Guardar nuevo producto en la BD
    public function store(Request $request)
    {
        // Validaciones básicas
        $request->validate([
            'nombre' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:productos,sku',
            'precio_venta_usd' => 'required|numeric|min:0',
            'imagen' => 'nullable|image|max:2048'
        ]);

        $data = $request->except(['imagen', '_token']);
        
        // Checkboxes (Si no vienen en el request, son false/0)
        $data['es_controlado'] = $request->has('es_controlado') ? 1 : 0;
        $data['es_combo'] = $request->has('es_combo') ? 1 : 0;

        // Subir Imagen
        if ($request->hasFile('imagen')) {
            $data['imagen_url'] = $request->file('imagen')->store('productos', 'public');
        }

        Producto::create($data);

        return redirect()->route('admin.productos.index')->with('success', 'Producto creado exitosamente.');
    }

    // Mostrar pantalla de Edición
    public function edit($id)
    {
        $producto = Producto::findOrFail($id);
        $categorias = Categoria::orderBy('nombre')->get();
        $marcas = Marca::orderBy('nombre')->get();
        
        return view('admin.productos.form', compact('producto', 'categorias', 'marcas'));
    }

    // Actualizar producto existente
    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:productos,sku,'.$id,
            'precio_venta_usd' => 'required|numeric|min:0',
            'imagen' => 'nullable|image|max:2048'
        ]);

        $data = $request->except(['imagen', '_token', '_method']);
        
        $data['es_controlado'] = $request->has('es_controlado') ? 1 : 0;
        $data['es_combo'] = $request->has('es_combo') ? 1 : 0;

        if ($request->hasFile('imagen')) {
            if ($producto->imagen_url) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($producto->imagen_url);
            }
            $data['imagen_url'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update($data);

        return redirect()->route('admin.productos.index')->with('success', 'Producto actualizado correctamente.');
    }
}