<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductoController extends Controller
{
    private $uploadFolder = 'productos';

    public function index(Request $request)
    {
        $query = Producto::with(['categoria', 'marca']);

        if ($request->filled('buscar')) {
            $busqueda = $request->buscar;
            $query->where(function($q) use ($busqueda) {
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('sku', 'LIKE', "%{$busqueda}%")
                  ->orWhere('codigo_barras', 'LIKE', "%{$busqueda}%");
            });
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

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
        // REGLAS ANTI-BUGS EN EL BACKEND
        $request->validate([
            'nombre' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:productos,sku',
            'precio_venta_usd' => 'required|numeric|min:0',
            // La oferta debe ser estrictamente MENOR (lt) al precio de venta
            'precio_oferta_usd' => 'nullable|numeric|min:0|lt:precio_venta_usd',
            // El costo debe ser MENOR O IGUAL (lte) al precio de venta
            'costo_promedio_usd' => 'nullable|numeric|min:0|lte:precio_venta_usd',
            'imagen' => 'nullable|image|max:2048',
            'stock_total' => 'nullable|numeric|min:0',
            // La alerta nunca puede ser mayor al stock total
            'stock_minimo_alerta' => 'nullable|numeric|min:0|lte:stock_total',
            'venta_minima' => 'nullable|numeric|min:0',
            'paso_venta' => 'nullable|numeric|min:0',
            'contenido_neto' => 'nullable|numeric|min:0',
        ], [
            'precio_oferta_usd.lt' => 'El precio de oferta no puede ser mayor ni igual al precio de venta.',
            'costo_promedio_usd.lte' => 'El costo promedio no puede ser mayor al precio de venta (pérdida).',
            'stock_minimo_alerta.lte' => 'La alerta de stock no puede ser mayor al stock físico total.',
        ]);

        $data = $request->except(['imagen', '_token', 'attr_keys', 'attr_values']);
        
        $data['es_controlado'] = $request->has('es_controlado') ? 1 : 0;
        $data['es_combo'] = $request->has('es_combo') ? 1 : 0;

        if ($request->has('attr_keys') && is_array($request->attr_keys)) {
            $atributos = [];
            foreach ($request->attr_keys as $index => $key) {
                if (!empty($key) && !empty($request->attr_values[$index])) {
                    $atributos[$key] = $request->attr_values[$index];
                }
            }
            $data['atributos_json'] = !empty($atributos) ? json_encode($atributos) : null;
        }

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
            'precio_oferta_usd' => 'nullable|numeric|min:0|lt:precio_venta_usd',
            'costo_promedio_usd' => 'nullable|numeric|min:0|lte:precio_venta_usd',
            'imagen' => 'nullable|image|max:2048',
            'stock_total' => 'nullable|numeric|min:0',
            'stock_minimo_alerta' => 'nullable|numeric|min:0|lte:stock_total',
            'venta_minima' => 'nullable|numeric|min:0',
            'paso_venta' => 'nullable|numeric|min:0',
            'contenido_neto' => 'nullable|numeric|min:0',
        ], [
            'precio_oferta_usd.lt' => 'El precio de oferta no puede ser mayor ni igual al precio de venta.',
            'costo_promedio_usd.lte' => 'El costo promedio no puede ser mayor al precio de venta (pérdida).',
            'stock_minimo_alerta.lte' => 'La alerta de stock no puede ser mayor al stock físico total.',
        ]);

        $data = $request->except(['imagen', '_token', '_method', 'attr_keys', 'attr_values']);
        
        $data['es_controlado'] = $request->has('es_controlado') ? 1 : 0;
        $data['es_combo'] = $request->has('es_combo') ? 1 : 0;

        if ($request->has('attr_keys') && is_array($request->attr_keys)) {
            $atributos = [];
            foreach ($request->attr_keys as $index => $key) {
                if (!empty($key) && !empty($request->attr_values[$index])) {
                    $atributos[$key] = $request->attr_values[$index];
                }
            }
            $data['atributos_json'] = !empty($atributos) ? json_encode($atributos) : null;
        } else {
             $data['atributos_json'] = null; 
        }

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
        return response()->json(['success' => true, 'message' => 'Producto reactivado.']);
    }

    private function uploadImage($file)
    {
        $destinationPath = public_path('img/upload/' . $this->uploadFolder);
        if (!File::exists($destinationPath)) File::makeDirectory($destinationPath, 0755, true);
        
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($destinationPath, $fileName);
        return 'img/upload/' . $this->uploadFolder . '/' . $fileName;
    }

    private function deleteImage($imagePath)
    {
        $fullPath = public_path($imagePath);
        if (File::exists($fullPath)) File::delete($fullPath);
    }
}