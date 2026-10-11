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

        // 1. Buscador inteligente multi-criterio (Nombre, SKU, Código de Barras, ID, Categoría o Marca)
        if ($request->filled('buscar')) {
            $busqueda = trim($request->buscar);
            $cleanNumeric = ltrim(preg_replace('/[^0-9]/', '', $busqueda), '0');

            $query->where(function($q) use ($busqueda, $cleanNumeric) {
                if (!empty($cleanNumeric) && is_numeric($cleanNumeric)) {
                    $q->orWhere('id', (int) $cleanNumeric);
                }

                $q->orWhere('nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('sku', 'LIKE', "%{$busqueda}%")
                  ->orWhere('codigo_barras', 'LIKE', "%{$busqueda}%")
                  ->orWhere('descripcion', 'LIKE', "%{$busqueda}%")
                  ->orWhereHas('categoria', function($c) use ($busqueda) {
                      $c->where('nombre', 'LIKE', "%{$busqueda}%");
                  })
                  ->orWhereHas('marca', function($m) use ($busqueda) {
                      $m->where('nombre', 'LIKE', "%{$busqueda}%");
                  });
            });
        }

        // 2. Filtro por Categoría
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        // 3. Filtro por Marca
        if ($request->filled('marca_id')) {
            $query->where('marca_id', $request->marca_id);
        }

        // 4. Filtro Rápido (Tabs Inteligentes)
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

        // Estadísticas para KPIs y Smart Tabs
        $stats = [
            'total' => Producto::count(),
            'critico' => Producto::whereColumn('stock_total', '<=', 'stock_minimo_alerta')->count(),
            'destacados' => Producto::where('destacado', 1)->count(),
            'combos' => Producto::where('es_combo', 1)->count(),
            'recetados' => Producto::where('es_controlado', 1)->count(),
            'suspendidos' => Producto::onlyTrashed()->count(),
            'valor_inventario_usd' => (float) (Producto::selectRaw('SUM(stock_total * precio_venta_usd) as total')->value('total') ?? 0),
        ];

        // Tasa de cambio oficial para referencia en Bs
        $tasaVes = (float) \App\Models\TasaCambio::obtenerValorUSD();

        $productos = $query->orderBy('nombre', 'asc')->paginate(12)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('admin.productos.partials._table', compact('productos', 'stats', 'tasaVes'))->render(),
                'stats' => $stats
            ]);
        }

        $categorias = Categoria::orderBy('nombre')->get();
        $marcas = Marca::orderBy('nombre')->get();

        return view('admin.productos.index', compact('productos', 'categorias', 'marcas', 'stats', 'tasaVes'));
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
        $ivaPorcentaje = \App\Models\ConfiguracionTienda::iva();
        return view('admin.productos.form', compact('categorias', 'marcas', 'ivaPorcentaje'));
    }

    public function store(Request $request)
    {
        $ivaPorcentaje = \App\Models\ConfiguracionTienda::iva();
        $incluyeIva = $request->boolean('precio_incluye_iva');

        $request->validate([
            'nombre' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:productos,sku',
            'precio_venta_usd' => 'required|numeric|min:0.01',
            'precio_oferta_usd' => 'nullable|numeric|min:0|lt:precio_venta_usd',
            'costo_promedio_usd' => 'nullable|numeric|min:0',
            'imagen' => 'nullable|image|max:2048',
            'stock_total' => 'nullable|numeric|min:0',
            'stock_minimo_alerta' => 'nullable|numeric|min:0|lte:stock_total',
            'venta_minima' => 'nullable|numeric|min:0',
            'paso_venta' => 'nullable|numeric|min:0',
            'contenido_neto' => 'nullable|numeric|min:0',
        ], [
            'precio_oferta_usd.lt' => 'El precio de oferta no puede ser mayor ni igual al precio de venta.',
            'stock_minimo_alerta.lte' => 'La alerta de stock no puede ser mayor al stock físico total.',
        ]);

        // Cálculo del precio base desglosando IVA si el usuario especificó que ya lo incluye
        $precioVentaInput = (float) $request->precio_venta_usd;
        $precioBaseVenta = $incluyeIva 
            ? round($precioVentaInput / (1 + ($ivaPorcentaje / 100)), 2)
            : round($precioVentaInput, 2);

        $precioBaseOferta = null;
        if ($request->filled('precio_oferta_usd')) {
            $precioOfertaInput = (float) $request->precio_oferta_usd;
            $precioBaseOferta = $incluyeIva 
                ? round($precioOfertaInput / (1 + ($ivaPorcentaje / 100)), 2)
                : round($precioOfertaInput, 2);
        }

        // Validación de costo contra la base imponible del precio
        if ($request->filled('costo_promedio_usd') && (float)$request->costo_promedio_usd > $precioBaseVenta) {
            return back()->withInput()->withErrors([
                'costo_promedio_usd' => 'El costo promedio ($' . number_format($request->costo_promedio_usd, 2) . ' USD) no puede ser mayor a la base imponible del producto ($' . number_format($precioBaseVenta, 2) . ' USD).'
            ]);
        }

        // Evitamos enviar campos no pertenecientes a la BD
        $data = $request->except(['imagen', '_token', 'attr_keys', 'attr_values', 'tipo_codigo_barras']);
        $data['precio_venta_usd'] = $precioBaseVenta;
        $data['precio_oferta_usd'] = $precioBaseOferta;
        $data['precio_incluye_iva'] = $incluyeIva ? 1 : 0;
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

        // 1. Creamos el producto primero
        $producto = Producto::create($data);

        // 2. LÓGICA DE CÓDIGO DE BARRAS AUTOMÁTICO
        if ($request->tipo_codigo_barras === 'no' || empty($producto->codigo_barras)) {
            $producto->codigo_barras = (string)$producto->id;
            $producto->save();
        }

        return redirect()->route('admin.productos.index')->with('success', 'Producto creado exitosamente.');
    }

    public function edit($id)
    {
        $producto = Producto::findOrFail($id);
        $categorias = Categoria::orderBy('nombre')->get();
        $marcas = Marca::orderBy('nombre')->get();
        $ivaPorcentaje = \App\Models\ConfiguracionTienda::iva();
        return view('admin.productos.form', compact('producto', 'categorias', 'marcas', 'ivaPorcentaje'));
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);
        $ivaPorcentaje = \App\Models\ConfiguracionTienda::iva();
        $incluyeIva = $request->boolean('precio_incluye_iva');

        $request->validate([
            'nombre' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:productos,sku,'.$id,
            'precio_venta_usd' => 'required|numeric|min:0.01',
            'precio_oferta_usd' => 'nullable|numeric|min:0|lt:precio_venta_usd',
            'costo_promedio_usd' => 'nullable|numeric|min:0',
            'imagen' => 'nullable|image|max:2048',
            'stock_total' => 'nullable|numeric|min:0',
            'stock_minimo_alerta' => 'nullable|numeric|min:0|lte:stock_total',
            'venta_minima' => 'nullable|numeric|min:0',
            'paso_venta' => 'nullable|numeric|min:0',
            'contenido_neto' => 'nullable|numeric|min:0',
        ], [
            'precio_oferta_usd.lt' => 'El precio de oferta no puede ser mayor ni igual al precio de venta.',
            'stock_minimo_alerta.lte' => 'La alerta de stock no puede ser mayor al stock físico total.',
        ]);

        // Cálculo del precio base desglosando IVA si el usuario especificó que ya lo incluye
        $precioVentaInput = (float) $request->precio_venta_usd;
        $precioBaseVenta = $incluyeIva 
            ? round($precioVentaInput / (1 + ($ivaPorcentaje / 100)), 2)
            : round($precioVentaInput, 2);

        $precioBaseOferta = null;
        if ($request->filled('precio_oferta_usd')) {
            $precioOfertaInput = (float) $request->precio_oferta_usd;
            $precioBaseOferta = $incluyeIva 
                ? round($precioOfertaInput / (1 + ($ivaPorcentaje / 100)), 2)
                : round($precioOfertaInput, 2);
        }

        // Validación de costo contra la base imponible del precio
        if ($request->filled('costo_promedio_usd') && (float)$request->costo_promedio_usd > $precioBaseVenta) {
            return back()->withInput()->withErrors([
                'costo_promedio_usd' => 'El costo promedio ($' . number_format($request->costo_promedio_usd, 2) . ' USD) no puede ser mayor a la base imponible del producto ($' . number_format($precioBaseVenta, 2) . ' USD).'
            ]);
        }

        $data = $request->except(['imagen', '_token', '_method', 'attr_keys', 'attr_values', 'motivo_cambio', 'tipo_codigo_barras']);
        $data['precio_venta_usd'] = $precioBaseVenta;
        $data['precio_oferta_usd'] = $precioBaseOferta;
        $data['precio_incluye_iva'] = $incluyeIva ? 1 : 0;
        $data['es_controlado'] = $request->has('es_controlado') ? 1 : 0;
        $data['es_combo'] = $request->has('es_combo') ? 1 : 0;

        // LÓGICA DE CÓDIGO DE BARRAS AUTOMÁTICO EN LA EDICIÓN
        if ($request->tipo_codigo_barras === 'no') {
            $data['codigo_barras'] = (string)$producto->id;
        }

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

        $precioViejo = (float) $producto->precio_venta_usd;
        $precioNuevo = (float) $precioBaseVenta;

        if ($precioViejo !== $precioNuevo) {
            \App\Models\HistoricoPrecioProducto::create([
                'producto_id' => $producto->id,
                'precio_anterior_usd' => $precioViejo,
                'precio_nuevo_usd' => $precioNuevo,
                'motivo_cambio' => $request->filled('motivo_cambio') ? $request->motivo_cambio : 'Actualización regular desde catálogo',
                'usuario_editor_id' => \Illuminate\Support\Facades\Auth::id()
            ]);
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