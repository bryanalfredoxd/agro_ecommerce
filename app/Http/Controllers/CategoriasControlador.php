<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriasControlador extends Controller
{
    /**
     * Muestra la página principal con las categorías reales.
     */
    public function index()
    {
        // Traemos solo las categorías que son padres (principales)
        $categorias = Categoria::whereNull('categoria_padre_id')->get();

        return view('welcome', compact('categorias'));
    }
}