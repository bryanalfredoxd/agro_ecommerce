<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HistoricoPrecioProducto;
use Illuminate\Http\Request;

class HistoricoPrecioController extends Controller
{
    public function index(Request $request)
    {
        $query = HistoricoPrecioProducto::with(['producto', 'editor']);

        if ($request->filled('buscar')) {
            $busqueda = $request->buscar;
            $query->whereHas('producto', function($q) use ($busqueda) {
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('sku', 'LIKE', "%{$busqueda}%");
            });
        }

        $historicos = $query->orderBy('creado_at', 'desc')->paginate(15);

        if ($request->ajax()) {
            return view('admin.historico_precios.partials._table', compact('historicos'))->render();
        }

        return view('admin.historico_precios.index', compact('historicos'));
    }
}