<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CuentaBancaria;
use Illuminate\Http\Request;

class CuentaBancariaController extends Controller
{
    public function index(Request $request)
    {
        $query = CuentaBancaria::query();

        if ($request->filled('buscar')) {
            $busqueda = $request->buscar;
            $query->where(function($q) use ($busqueda) {
                $q->where('nombre_titular', 'LIKE', "%{$busqueda}%")
                  ->orWhere('banco_entidad', 'LIKE', "%{$busqueda}%")
                  ->orWhere('email', 'LIKE', "%{$busqueda}%");
            });
        }

        if ($request->filled('tipo') && $request->tipo !== 'todos') {
            $query->where('tipo_metodo', $request->tipo);
        }

        $cuentas = $query->orderBy('activo', 'desc')->orderBy('tipo_metodo', 'asc')->paginate(10);

        if ($request->ajax()) {
            return view('admin.cuentas_bancarias.partials._table', compact('cuentas'))->render();
        }

        return view('admin.cuentas_bancarias.index', compact('cuentas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo_metodo' => 'required|in:pago_movil,zelle,efectivo_usd,efectivo_bs,transferencia,punto_venta,binance,biopago',
            'nombre_titular' => 'nullable|string|max:255',
            'banco_entidad' => 'nullable|string|max:255',
            'numero_cuenta' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:255',
            'identificacion' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'instrucciones_adicionales' => 'nullable|string'
        ]);

        CuentaBancaria::create($request->all());

        return response()->json(['success' => true, 'message' => 'Método de pago registrado exitosamente.']);
    }

    public function update(Request $request, $id)
    {
        $cuenta = CuentaBancaria::findOrFail($id);

        $request->validate([
            'tipo_metodo' => 'required|in:pago_movil,zelle,efectivo_usd,efectivo_bs,transferencia,punto_venta,binance,biopago',
            // Puedes agregar más validaciones dependiendo del método si lo deseas
        ]);

        // Asegurarnos de limpiar los campos que no se envían
        $data = $request->all();
        $camposNulos = ['nombre_titular', 'banco_entidad', 'numero_cuenta', 'telefono', 'identificacion', 'email', 'instrucciones_adicionales'];
        foreach($camposNulos as $campo) {
            if(!isset($data[$campo])) $data[$campo] = null;
        }

        $cuenta->update($data);

        return response()->json(['success' => true, 'message' => 'Método de pago actualizado correctamente.']);
    }

    public function destroy($id)
    {
        CuentaBancaria::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Método de pago eliminado.']);
    }

    public function toggleStatus($id)
    {
        $cuenta = CuentaBancaria::findOrFail($id);
        $cuenta->activo = !$cuenta->activo;
        $cuenta->save();

        $estado = $cuenta->activo ? 'activado' : 'desactivado';
        return response()->json(['success' => true, 'message' => "Método de pago $estado correctamente."]);
    }
}