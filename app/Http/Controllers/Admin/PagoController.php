<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Factura;
use App\Mail\PedidoAprobadoMail;
use App\Mail\FacturaTicketMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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

            // Si es un pedido de Retiro en Sede y con pago presencial (efectivo o punto de venta/Biopago):
            // El cliente ya canceló en caja y se le entregó el producto en el mostrador.
            $esRetiroPresencial = !$pedido->es_delivery && $pago->esPresencial();
            if ($esRetiroPresencial) {
                $pedido->estado = 'entregado';
                $pedido->save();
            }

            // Verificamos o emitimos la factura correspondiente
            $factura = Factura::with([
                'pedido.detalles.producto',
                'pedido.pagos',
                'pedido.usuario',
                'pedido.tasaCambio'
            ])->where('pedido_id', $pedido->id)->first();

            if (!$factura) {
                $factura = Factura::emitirParaPedido($pedido);
                if ($factura) {
                    $factura->load([
                        'pedido.detalles.producto',
                        'pedido.pagos',
                        'pedido.usuario',
                        'pedido.tasaCambio'
                    ]);
                }
            }

            DB::commit();

            // Enviar notificaciones por correo al cliente
            try {
                $cliente = $pedido->usuario;
                if ($cliente && filter_var($cliente->email, FILTER_VALIDATE_EMAIL)) {
                    // 1. Notificación formal de pago aprobado y confirmación de orden
                    Mail::to($cliente->email)
                        ->send(new PedidoAprobadoMail($pedido, $factura));

                    // 2. Factura digital en formato Ticket (80mm) adjunta/enviada al cliente
                    if ($factura) {
                        Mail::to($cliente->email)
                            ->send(new FacturaTicketMail($factura));
                    }
                }
            } catch (\Throwable $mailEx) {
                Log::warning("No se pudo enviar correo de confirmación/ticket para pedido #{$pedido->id}: " . $mailEx->getMessage());
            }

            $mensajeRetorno = $esRetiroPresencial 
                ? 'Pago cobrado exitosamente en caja y pedido completado como ENTREGADO. Factura y Ticket (80mm) emitidos y enviados al correo.'
                : 'Pago aprobado. El inventario ha sido descontado y la confirmación con el Ticket (80mm) enviada al correo del cliente.';

            return response()->json(['success' => true, 'message' => $mensajeRetorno]);

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