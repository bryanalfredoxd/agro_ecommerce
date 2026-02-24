<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido; 
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $pedidos = Pedido::with('usuario')->orderBy('creado_at', 'desc')->paginate(15);

        return view('admin.pedidos.index', compact('pedidos'));
    }

    public function show($id)
    {
        // 1. Buscamos el pedido y lo guardamos en la variable $pedido
        $pedido = Pedido::with(['usuario', 'zonaDelivery', 'pago'])->findOrFail($id);

        // 2. Buscamos los detalles de los productos
        $detalles = DB::table('pedido_detalles')
            ->join('productos', 'pedido_detalles.producto_id', '=', 'productos.id')
            ->select('pedido_detalles.*', 'productos.nombre', 'productos.sku')
            ->where('pedido_id', $id)
            ->get();

        // 3. AQUÍ ESTÁ LA MAGIA: Le enviamos la variable $pedido y $detalles a la vista
        return view('admin.pedidos.show', compact('pedido', 'detalles'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,pagado,preparacion,en_ruta,entregado,devuelto,cancelado,completado_caja'
        ]);

        $pedido = Pedido::findOrFail($id);
        $pedido->estado = $request->estado;
        $pedido->save();

        return redirect()->route('admin.pedidos.show', $id)
                         ->with('success', 'Estado del pedido actualizado correctamente.');
    }
}