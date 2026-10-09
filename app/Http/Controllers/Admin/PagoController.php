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
        $params = $request->all();
        if (!isset($params['filtro_estado']) || $params['filtro_estado'] === 'revision') {
            $params['filtro_estado'] = 'por_verificar';
        }
        return redirect()->route('admin.pedidos.index', $params);
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

            // Verificamos que no tenga factura ya emitida (por seguridad)
            if(!\App\Models\Factura::where('pedido_id', $pedido->id)->exists()){
                \App\Models\Factura::emitirParaPedido($pedido);
            }

            DB::commit();

            // Enviar notificación por correo al cliente
            try {
                $cliente = $pedido->usuario;
                if ($cliente && filter_var($cliente->email, FILTER_VALIDATE_EMAIL)) {
                    \Illuminate\Support\Facades\Mail::to($cliente->email)
                        ->send(new \App\Mail\PedidoAprobadoMail($pedido));
                }
            } catch (\Throwable $mailEx) {
                \Illuminate\Support\Facades\Log::warning("No se pudo enviar correo de confirmación para pedido #{$pedido->id}: " . $mailEx->getMessage());
            }

            return response()->json(['success' => true, 'message' => 'Pago aprobado. El inventario ha sido descontado y el cliente notificado por correo.']);

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