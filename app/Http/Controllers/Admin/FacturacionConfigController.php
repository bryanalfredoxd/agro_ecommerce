<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacturaAjuste;
use Illuminate\Http\Request;

class FacturacionConfigController extends Controller
{
    public function index(Request $request)
    {
        $ajustes = FacturaAjuste::orderBy('id', 'asc')->paginate(10);

        if ($request->ajax()) {
            return view('admin.facturacion_config.partials._table', compact('ajustes'))->render();
        }

        return view('admin.facturacion_config.index', compact('ajustes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'serie' => 'required|string|max:10|unique:factura_ajustes,serie',
            'proximo_numero' => 'required|integer|min:1',
            'porcentaje_iva' => 'required|numeric|min:0|max:100',
        ]);

        FacturaAjuste::create([
            'serie' => strtoupper($request->serie),
            'proximo_numero' => $request->proximo_numero,
            'porcentaje_iva' => $request->porcentaje_iva,
            'activo' => 1
        ]);

        return response()->json(['success' => true, 'message' => 'Serie de facturación creada exitosamente.']);
    }

    public function update(Request $request, $id)
    {
        $ajuste = FacturaAjuste::findOrFail($id);

        $request->validate([
            'serie' => 'required|string|max:10|unique:factura_ajustes,serie,' . $id,
            'proximo_numero' => 'required|integer|min:1',
            'porcentaje_iva' => 'required|numeric|min:0|max:100',
        ]);

        $ajuste->update([
            'serie' => strtoupper($request->serie),
            'proximo_numero' => $request->proximo_numero,
            'porcentaje_iva' => $request->porcentaje_iva,
        ]);

        return response()->json(['success' => true, 'message' => 'Serie actualizada correctamente.']);
    }

    public function toggleStatus($id)
    {
        $ajuste = FacturaAjuste::findOrFail($id);
        
        // Regla de negocio: Al menos debe haber una serie activa
        if ($ajuste->activo && FacturaAjuste::where('activo', 1)->count() === 1) {
            return response()->json(['success' => false, 'message' => 'No puedes desactivar todas las series. Debe quedar al menos una activa.'], 422);
        }

        $ajuste->activo = !$ajuste->activo;
        $ajuste->save();

        $estado = $ajuste->activo ? 'activada' : 'desactivada';
        return response()->json(['success' => true, 'message' => "Serie $estado."]);
    }
}