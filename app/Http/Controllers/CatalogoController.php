<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller; 
// SOLUCIÓN ERROR IMAGEN 1: Importamos los modelos aquí arriba
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function index(Request $request)
    {
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

        // 4. Ordenamiento (SOLUCIÓN ERROR IMAGEN 2)
        // Quitamos 'created_at' que no existe y usamos 'id'
        if ($request->orden == 'precio_asc') {
            $query->orderBy('precio_venta_usd', 'asc');
        } elseif ($request->orden == 'precio_desc') {
            $query->orderBy('precio_venta_usd', 'desc');
        } else {
            $query->orderBy('id', 'desc'); // <--- Aquí estaba el fallo del SQLSTATE
        }

        // 5. Ejecución (SOLUCIÓN ERROR IMAGEN 3)
        // Ahora que creaste ProductoImagen.php en el Paso 1, esto funcionará
        $productos = $query->with(['categoria', 'imagenes' => function($q) {
                                $q->where('es_principal', 1);
                           }])
                           ->paginate(12)
                           ->withQueryString();

        // 6. Listas para la vista
        $categorias = Categoria::has('productos')->get();
        $marcas = Marca::where('activo', 1)->get();

        return view('catalogo.index', compact('productos', 'categorias', 'marcas'));
    }
}