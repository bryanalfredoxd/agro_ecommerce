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
        // Validación sencilla
        $request->validate([
            'nombre_producto' => 'required|string|max:255',
            'descripcion_adicional' => 'nullable|string'
        ]);

        // Crear el registro
        SolicitudProducto::create([
            // Laravel checkea si hay sesión, sino guarda null
            'usuario_id' => Auth::check() ? Auth::id() : null,
            'nombre_producto' => $request->nombre_producto,
            'descripcion_adicional' => $request->descripcion_adicional,
            'estado' => 'pendiente'
        ]);

        // Retornar a la vista con un mensaje de éxito
        return redirect()->back()->with('success', '¡Gracias por tu sugerencia! Trabajaremos para agregar el producto pronto.');
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
                    'url' => route('catalogo') . '?buscar=' . urlencode($producto->nombre)
                ];
            });

        return response()->json([
            'productos' => $productos,
            'total' => $productos->count(),
            'query' => $q
        ]);
    }
}