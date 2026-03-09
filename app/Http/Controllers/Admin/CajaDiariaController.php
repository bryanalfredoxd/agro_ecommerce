<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SesionCaja;
use Illuminate\Http\Request;

class CajaDiariaController extends Controller
{
    public function index(Request $request)
    {
        $query = SesionCaja::with(['caja', 'cajero']);

        // Filtro por Estado de la Caja (Abierta o Cerrada)
        if ($request->filled('filtro_estado') && $request->filtro_estado !== 'todas') {
            if ($request->filtro_estado === 'abierta') {
                $query->whereNull('fecha_cierre');
            } else {
                $query->whereNotNull('fecha_cierre');
            }
        }

        $sesiones = $query->orderBy('fecha_apertura', 'desc')->paginate(10);

        if ($request->ajax()) {
            return view('admin.caja_diaria.partials._table', compact('sesiones'))->render();
        }

        return view('admin.caja_diaria.index', compact('sesiones'));
    }

    public function movimientos($id)
    {
        $sesion = SesionCaja::with(['caja', 'cajero', 'movimientos'])->findOrFail($id);
        
        return view('admin.caja_diaria.partials._movimientos', compact('sesion'))->render();
    }
}