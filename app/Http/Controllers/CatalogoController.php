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
        } else {
            $query->orderBy('id', 'desc');
        }

        // 5. Ejecución
        $productos = $query->with(['categoria', 'imagenes' => function($q) {
                                $q->where('es_principal', 1);
                           }])
                           ->paginate(12)
                           ->withQueryString();

        // 6. Listas para la vista
        $categorias = Categoria::has('productos')->get();
        $marcas = Marca::where('activo', 1)->get();

        // Si es petición AJAX, devolver solo la vista parcial (INYECTAMOS TASA AQUÍ)
        if ($request->ajax()) {
            return view('catalogo.partials.products', compact('productos', 'tasaDolar'))->render();
        }

        // Para petición normal, devolver vista completa (INYECTAMOS TASA AQUÍ TAMBIÉN)
        return view('catalogo.index', compact('productos', 'categorias', 'marcas', 'tasaDolar'));
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
}