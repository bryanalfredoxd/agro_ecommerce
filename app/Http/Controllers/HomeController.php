<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;

class HomeController extends Controller
{
    /**
     * Muestra la página de inicio (Landing Page).
     */
    public function __invoke()
    {
        // 1. CATEGORÍAS PRINCIPALES
        $categoriasPrincipales = Categoria::whereNull('categoria_padre_id')
                                          ->take(6)
                                          ->get();

        // 2. PRODUCTOS DESTACADOS
        $productosDestacados = Producto::where('destacado', 1)
            ->where('stock_total', '>', 0)
            ->whereNull('eliminado_at')
            ->with(['marca', 'categoria', 'imagenes' => function($q) {
                $q->where('es_principal', 1);
            }])
            // CORRECCIÓN: Cambiamos latest() por orderBy('id', 'desc')
            ->orderBy('id', 'desc') 
            ->take(8)
            ->get();

        // 3. LÓGICA DE RELLENO (FALLBACK)
        if ($productosDestacados->count() < 4) {
            $cantidadFaltante = 8 - $productosDestacados->count();
            
            $relleno = Producto::where('stock_total', '>', 0)
                ->whereNull('eliminado_at')
                ->whereNotIn('id', $productosDestacados->pluck('id'))
                ->with(['marca', 'categoria', 'imagenes'])
                // CORRECCIÓN: Aquí también cambiamos latest()
                ->orderBy('id', 'desc')
                ->take($cantidadFaltante)
                ->get();
            
            $productosDestacados = $productosDestacados->merge($relleno);
        }

        return view('welcome', compact('categoriasPrincipales', 'productosDestacados'));
    }
}