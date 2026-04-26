<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\TasaCambio; // <-- IMPORTANTE: Agregar esta línea

class HomeController extends Controller
{
    /**
     * Muestra la página de inicio (Landing Page).
     */
    public function __invoke()
    {
        $tasaDolar = TasaCambio::obtenerValorUSD() ?? 1; 
        
        // 1. CATEGORÍAS PRINCIPALES
        $categoriasPrincipales = Categoria::whereNull('categoria_padre_id')
                                          ->take(20)
                                          ->get();

        // 2. PRODUCTOS DESTACADOS
        $productosDestacados = Producto::where('destacado', 1)
            ->where('stock_total', '>', 0)
            ->whereNull('eliminado_at')
            ->with(['marca', 'categoria', 'imagenes' => function($q) {
                $q->where('es_principal', 1);
            }])
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
                ->orderBy('id', 'desc')
                ->take($cantidadFaltante)
                ->get();
            
            $productosDestacados = $productosDestacados->merge($relleno);
        }

        // AGREGAMOS $tasaDolar a la función compact()
        return view('welcome', compact('categoriasPrincipales', 'productosDestacados', 'tasaDolar'));
    }
}