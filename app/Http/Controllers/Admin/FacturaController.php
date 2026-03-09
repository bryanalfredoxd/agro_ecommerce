<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Factura;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function index(Request $request)
    {
        // Traemos la factura junto con su pedido
        $query = Factura::with('pedido');

        // 1. Buscador (Por número de factura, cliente o RIF)
        if ($request->filled('buscar')) {
            $busqueda = $request->buscar;
            $query->where(function($q) use ($busqueda) {
                $q->where('numero_factura', 'LIKE', "%{$busqueda}%")
                  ->orWhere('nombre_razon_social', 'LIKE', "%{$busqueda}%")
                  ->orWhere('cedula_rif_cliente', 'LIKE', "%{$busqueda}%");
            });
        }

        // 2. Filtros por Pestañas (Estado)
        if ($request->filled('filtro_estado') && $request->filtro_estado !== 'todas') {
            $query->where('estado', $request->filtro_estado);
        }

        $facturas = $query->orderBy('fecha_emision', 'desc')->paginate(10);

        if ($request->ajax()) {
            return view('admin.facturas.partials._table', compact('facturas'))->render();
        }

        return view('admin.facturas.index', compact('facturas'));
    }

    public function show($id)
    {
        // Cargamos la factura con toda la estructura de productos del pedido asociado
        $factura = Factura::with([
            'pedido.detalles.producto'
        ])->findOrFail($id);

        return view('admin.facturas.partials._detalle', compact('factura'))->render();
    }

    public function anular($id)
    {
        $factura = Factura::findOrFail($id);
        
        if ($factura->estado === 'anulada') {
            return response()->json(['success' => false, 'message' => 'La factura ya se encuentra anulada.'], 422);
        }

        $factura->estado = 'anulada';
        $factura->save();

        return response()->json(['success' => true, 'message' => 'Factura anulada correctamente en el sistema.']);
    }
}