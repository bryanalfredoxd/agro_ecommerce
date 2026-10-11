<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller; 
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\TasaCambio; 
use App\Models\SolicitudProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatalogoController extends Controller
{
    public function index(Request $request)
    {
        // ==========================================
        // OBTENEMOS LA TASA DE CAMBIO ACTUAL
        // ==========================================
        $tasaDolar = TasaCambio::obtenerValorUSD() ?? 1;

        // 1. Consulta base
        $query = Producto::where('stock_total', '>', 0)
                         ->whereNull('eliminado_at');

        // 2. Filtro por Búsqueda
        if ($request->filled('buscar')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%')
                  ->orWhere('descripcion', 'like', '%' . $request->buscar . '%');
            });
        }

        // 3. Filtros
        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }

        if ($request->filled('marca')) {
            $query->where('marca_id', $request->marca);
        }

        // 4. Ordenamiento
        if ($request->orden == 'precio_asc') {
            $query->orderBy('precio_venta_usd', 'asc');
        } elseif ($request->orden == 'precio_desc') {
            $query->orderBy('precio_venta_usd', 'desc');
        } elseif ($request->orden == 'nombre_asc') {
            $query->orderBy('nombre', 'asc');
        } else {
            $query->orderBy('id', 'desc');
        }

        // 5. Ejecución
        $productos = $query->with(['categoria', 'marca', 'imagenes' => function($q) {
                                $q->where('es_principal', 1);
                           }])
                           ->paginate(12)
                           ->withQueryString();

        // 6. Listas para la vista con conteo de productos activos
        $categorias = Categoria::whereHas('productos', function($q) {
                $q->where('stock_total', '>', 0)->whereNull('eliminado_at');
            })
            ->withCount(['productos' => function($q) {
                $q->where('stock_total', '>', 0)->whereNull('eliminado_at');
            }])
            ->orderBy('nombre', 'asc')
            ->get();

        $marcas = Marca::where('activo', 1)
            ->whereHas('productos', function($q) {
                $q->where('stock_total', '>', 0)->whereNull('eliminado_at');
            })
            ->withCount(['productos' => function($q) {
                $q->where('stock_total', '>', 0)->whereNull('eliminado_at');
            }])
            ->orderBy('nombre', 'asc')
            ->get();

        $totalProductosDisponibles = Producto::where('stock_total', '>', 0)->whereNull('eliminado_at')->count();

        // Obtener favoritos del usuario autenticado
        $favoritosIds = Auth::check()
            ? \Illuminate\Support\Facades\DB::table('favoritos')->where('usuario_id', Auth::id())->pluck('producto_id')->toArray()
            : [];

        // Si es petición AJAX, devolver solo la vista parcial
        if ($request->ajax()) {
            return view('catalogo.partials.products', compact('productos', 'tasaDolar', 'favoritosIds', 'categorias', 'marcas', 'totalProductosDisponibles'))->render();
        }

        // Para petición normal, devolver vista completa
        return view('catalogo.index', compact('productos', 'categorias', 'marcas', 'tasaDolar', 'favoritosIds', 'totalProductosDisponibles'));
    }

    public function solicitarProducto(Request $request)
    {
        $rules = [
            'nombre_producto' => 'required|string|max:255',
            'descripcion_adicional' => 'nullable|string|max:1000',
        ];

        $messages = [
            'nombre_producto.required' => 'Debes indicar el nombre del insumo o producto que necesitas.',
        ];

        if (!Auth::check()) {
            $rules['nombre_contacto'] = 'required|string|max:150';
            $rules['telefono_contacto'] = 'required|string|max:50';
            $messages['nombre_contacto.required'] = 'Por favor ingresa tu nombre de contacto.';
            $messages['telefono_contacto.required'] = 'Por favor ingresa un teléfono o WhatsApp para contactarte.';
        } else {
            $rules['nombre_contacto'] = 'nullable|string|max:150';
            $rules['telefono_contacto'] = 'nullable|string|max:50';
        }

        $request->validate($rules, $messages);

        $usuario = Auth::user();

        $solicitud = SolicitudProducto::create([
            'usuario_id' => $usuario ? $usuario->id : null,
            'nombre_contacto' => $usuario ? null : trim($request->nombre_contacto),
            'telefono_contacto' => $usuario ? null : trim($request->telefono_contacto),
            'nombre_producto' => trim($request->nombre_producto),
            'descripcion_adicional' => $request->descripcion_adicional ? trim($request->descripcion_adicional) : null,
            'estado' => 'pendiente'
        ]);

        $mensaje = '¡Hemos recibido tu solicitud! Nuestro equipo gestionará el insumo a la brevedad.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'solicitud' => $solicitud
            ]);
        }

        return redirect()->back()->with('success', $mensaje);
    }

    /**
     * Búsqueda en vivo (Live Search) para el buscador del encabezado.
     */
    public function buscarEnVivo(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([
                'productos' => [],
                'total' => 0,
                'query' => $q
            ]);
        }

        $tasaDolar = TasaCambio::obtenerValorUSD() ?? 1;

        $productos = Producto::where('stock_total', '>', 0)
            ->whereNull('eliminado_at')
            ->where(function ($query) use ($q) {
                $query->where('nombre', 'like', '%' . $q . '%')
                      ->orWhere('descripcion', 'like', '%' . $q . '%')
                      ->orWhere('sku', 'like', '%' . $q . '%')
                      ->orWhereHas('categoria', function ($cat) use ($q) {
                          $cat->where('nombre', 'like', '%' . $q . '%');
                      })
                      ->orWhereHas('marca', function ($m) use ($q) {
                          $m->where('nombre', 'like', '%' . $q . '%');
                      });
            })
            ->with(['categoria', 'marca', 'imagenes' => function ($img) {
                $img->where('es_principal', 1);
            }])
            ->take(6)
            ->get()
            ->map(function ($producto) use ($tasaDolar) {
                $img = $producto->imagenes->first()?->imagen_url ?? $producto->imagen_url;

                return [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'categoria' => $producto->categoria?->nombre ?? 'General',
                    'marca' => $producto->marca?->nombre ?? '',
                    'precio_usd' => number_format($producto->precio_venta_usd, 2),
                    'precio_bs' => number_format($producto->precio_venta_usd * $tasaDolar, 2, ',', '.'),
                    'stock_total' => (float) $producto->stock_total,
                    'unidad_medida' => $producto->unidad_medida,
                    'imagen' => $img ? asset($img) : null,
                    'url' => route('catalogo.show', $producto->id)
                ];
            });

        return response()->json([
            'productos' => $productos,
            'total' => $productos->count(),
            'query' => $q
        ]);
    }

    /**
     * Muestra la pantalla de detalle completo del producto con su galería de imágenes
     */
    public function show($id)
    {
        $producto = Producto::with([
            'categoria', 
            'marca', 
            'imagenes' => function ($q) {
                $q->orderBy('orden', 'asc')->orderBy('id', 'asc');
            }
        ])
        ->whereNull('eliminado_at')
        ->findOrFail($id);

        $tasaDolar = TasaCambio::obtenerValorUSD() ?? 1;
        $ivaPorcentaje = \App\Models\ConfiguracionTienda::iva();

        $esFavorito = Auth::check()
            ? \Illuminate\Support\Facades\DB::table('favoritos')
                ->where('usuario_id', Auth::id())
                ->where('producto_id', $producto->id)
                ->exists()
            : false;

        // Productos relacionados (misma categoría o marca)
        $relacionados = Producto::where('id', '!=', $producto->id)
            ->where(function($q) use ($producto) {
                if ($producto->categoria_id) {
                    $q->where('categoria_id', $producto->categoria_id);
                }
                if ($producto->marca_id) {
                    $q->orWhere('marca_id', $producto->marca_id);
                }
            })
            ->where('stock_total', '>', 0)
            ->whereNull('eliminado_at')
            ->take(4)
            ->get();

        return view('catalogo.show', compact('producto', 'tasaDolar', 'ivaPorcentaje', 'esFavorito', 'relacionados'));
    }
}