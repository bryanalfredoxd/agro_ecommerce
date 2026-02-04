<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\Producto;
use App\Models\ConfiguracionTienda; // Para el IVA
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CarritoController extends Controller
{
    // 1. Ver el carrito (Página principal del carrito)
    public function index()
    {
        $userId = Auth::id();
        
        // Traemos los items con la info del producto y su imagen principal
        $items = Carrito::where('usuario_id', $userId)
            ->with(['producto.imagenes' => function($q) {
                $q->where('es_principal', 1);
            }])
            ->get();

        // Obtener configuración para IVA
        $config = DB::table('configuracion_tienda')->first();
        $ivaPorcentaje = $config ? $config->iva_porcentaje : 16.00;

        // Cálculos
        $subtotal = 0;
        foreach ($items as $item) {
            // Validar que el producto exista y tenga precio
            if ($item->producto) {
                $subtotal += $item->producto->precio_venta_usd * $item->cantidad;
            }
        }

        $montoIva = $subtotal * ($ivaPorcentaje / 100);
        $total = $subtotal + $montoIva;

        return view('carrito.index', compact('items', 'subtotal', 'montoIva', 'total', 'ivaPorcentaje'));
    }

    // 2. Añadir producto (AJAX desde el catálogo)
    public function add(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|numeric|min:1'
        ]);

        $userId = Auth::id();
        $producto = Producto::find($request->producto_id);

        // Validar Stock
        if ($producto->stock_total < $request->cantidad) {
            return response()->json([
                'status' => 'error',
                'message' => 'Stock insuficiente. Disponibles: ' . number_format($producto->stock_total, 0)
            ], 422);
        }

        // Buscar si ya existe en el carrito
        $carritoItem = Carrito::where('usuario_id', $userId)
                              ->where('producto_id', $request->producto_id)
                              ->first();

        if ($carritoItem) {
            // Si ya existe, validamos que la suma no supere el stock
            $nuevaCantidad = $carritoItem->cantidad + $request->cantidad;
            
            if ($producto->stock_total < $nuevaCantidad) {
                return response()->json(['status' => 'error', 'message' => 'No puedes añadir más cantidad de la disponible en stock.'], 422);
            }

            $carritoItem->cantidad = $nuevaCantidad;
            $carritoItem->save(); // El trigger 'actualizado_at' de la BD se encarga de la fecha
        } else {
            // Crear nuevo registro
            Carrito::create([
                'usuario_id' => $userId,
                'producto_id' => $request->producto_id,
                'cantidad' => $request->cantidad
            ]);
        }

        // Contar total de items para actualizar el badge del header (opcional)
        $count = Carrito::where('usuario_id', $userId)->sum('cantidad');

        return response()->json([
            'status' => 'success',
            'message' => 'Producto agregado al carrito',
            'cart_count' => $count
        ]);
    }

    // 3. Actualizar cantidad (AJAX desde la vista del carrito)
    public function update(Request $request)
    {
        $item = Carrito::where('id', $request->id)->where('usuario_id', Auth::id())->first();
        
        if (!$item) return response()->json(['status' => 'error'], 404);

        $producto = Producto::find($item->producto_id);

        if ($producto->stock_total < $request->cantidad) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Stock máximo alcanzado'
            ], 422);
        }

        $item->cantidad = $request->cantidad;
        $item->save();

        return response()->json(['status' => 'success']);
    }

    // 4. Eliminar item
    public function remove(Request $request)
    {
        Carrito::where('id', $request->id)->where('usuario_id', Auth::id())->delete();
        return response()->json(['status' => 'success']);
    }
}