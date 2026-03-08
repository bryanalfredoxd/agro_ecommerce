<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    public function index(Request $request)
    {
        // Traemos el pago con su pedido (y el cliente del pedido) y el verificador
        $query = Pago::with(['pedido.usuario', 'verificador']);

        // 1. Buscador (Por referencia o ID de pedido)
        if ($request->filled('buscar')) {
            $busqueda = $request->buscar;
            $query->where(function($q) use ($busqueda) {
                $q->where('referencia_bancaria', 'LIKE', "%{$busqueda}%")
                  ->orWhere('pedido_id', 'LIKE', "%{$busqueda}%");
            });
        }

        if ($request->filtro_estado === 'todos') {
            // No aplicamos filtro where, pero ordenamos para que los pendientes salgan arriba
            $query->orderByRaw("FIELD(estado, 'revision', 'aprobado', 'rechazado')");
        } 
        // Si mandan un estado específico, lo filtramos
        elseif ($request->filled('filtro_estado')) {
            $query->where('estado', $request->filtro_estado);
        } 
        // Si NO mandan nada (primera vez que entra a la página), forzamos 'revision'
        else {
            $query->where('estado', 'revision');
        }

        // Filtro por Método de Pago
        if ($request->filled('filtro_metodo') && $request->filtro_metodo !== 'todos') {
            $query->where('metodo', $request->filtro_metodo);
        }

        $pagos = $query->orderBy('fecha_pago', 'desc')->paginate(10);

        if ($request->ajax()) {
            return view('admin.pagos.partials._table', compact('pagos'))->render();
        }

        return view('admin.pagos.index', compact('pagos'));
    }

    // Método para APROBAR el pago
    public function aprobar($id)
    {
        try {
            DB::beginTransaction();

            $pago = Pago::findOrFail($id);
            
            if ($pago->estado !== 'revision') {
                return response()->json(['success' => false, 'message' => 'Este pago ya fue procesado anteriormente.'], 422);
            }

            // Actualizamos el Pago
            $pago->estado = 'aprobado';
            $pago->verificado_por_usuario_id = Auth::id(); // Guardamos quién lo aprobó
            $pago->save();

            // Actualizamos el Pedido asociado
            $pedido = Pedido::findOrFail($pago->pedido_id);
            $pedido->estado = 'pagado';
            $pedido->save(); // ¡ESTO DISPARA EL TRIGGER SQL DEL INVENTARIO!

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pago aprobado. El inventario ha sido descontado y Logística notificada.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al procesar: ' . $e->getMessage()], 500);
        }
    }

    // Método para RECHAZAR el pago
    public function rechazar($id)
    {
        $pago = Pago::findOrFail($id);
            
        if ($pago->estado !== 'revision') {
            return response()->json(['success' => false, 'message' => 'Este pago ya fue procesado anteriormente.'], 422);
        }

        $pago->estado = 'rechazado';
        $pago->verificado_por_usuario_id = Auth::id();
        $pago->save();

        // Nota: El pedido sigue estando 'pendiente' por si el cliente sube otro pago.

        return response()->json(['success' => true, 'message' => 'Pago rechazado. El pedido sigue en espera de un pago válido.']);
    }
}