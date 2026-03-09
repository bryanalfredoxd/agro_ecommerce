<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\Producto;
use App\Models\ConfiguracionTienda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CarritoController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        
        $items = Carrito::where('usuario_id', $userId)
            ->with(['producto.imagenes' => function($q) {
                $q->where('es_principal', 1);
            }])
            ->get();

        $config = DB::table('configuracion_tienda')->first();
        $ivaPorcentaje = $config ? $config->iva_porcentaje : 16.00;

        $subtotal = 0;
        foreach ($items as $item) {
            if ($item->producto) {
                // Usar precio de oferta si existe, sino el regular
                $precio = $item->producto->precio_oferta_usd ?: $item->producto->precio_venta_usd;
                $subtotal += $precio * $item->cantidad;
            }
        }

        $montoIva = $subtotal * ($ivaPorcentaje / 100);
        $total = $subtotal + $montoIva;

        return view('carrito.index', compact('items', 'subtotal', 'montoIva', 'total', 'ivaPorcentaje'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'nullable|numeric|min:0.01'
        ]);

        $userId = Auth::id();
        $producto = Producto::find($request->producto_id);

        // Si no mandan cantidad, o mandan menos del mínimo, forzamos la venta mínima
        $cantidadSolicitada = $request->cantidad ?? $producto->venta_minima;
        if ($cantidadSolicitada < $producto->venta_minima) {
            $cantidadSolicitada = $producto->venta_minima;
        }

        // Buscar si ya existe en el carrito
        $carritoItem = Carrito::where('usuario_id', $userId)
                              ->where('producto_id', $request->producto_id)
                              ->first();

        $nuevaCantidad = $carritoItem ? ($carritoItem->cantidad + $cantidadSolicitada) : $cantidadSolicitada;

        // Validar Stock Total
        if ($producto->stock_total < $nuevaCantidad) {
            return response()->json([
                'status' => 'error',
                'message' => 'Stock insuficiente. Solo quedan ' . floatval($producto->stock_total) . ' unidades.'
            ], 422);
        }

        if ($carritoItem) {
            $carritoItem->cantidad = $nuevaCantidad;
            $carritoItem->save();
        } else {
            Carrito::create([
                'usuario_id' => $userId,
                'producto_id' => $request->producto_id,
                'cantidad' => $nuevaCantidad
            ]);
        }

        // Contamos cuántos PRODUCTOS DISTINTOS hay para el badge del Header
        $count = Carrito::where('usuario_id', $userId)->count();

        return response()->json([
            'status' => 'success',
            'message' => 'Producto agregado al carrito',
            'cart_count' => $count
        ]);
    }

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

        // Validar que no intente hackear y poner menos del mínimo
        if ($request->cantidad < $producto->venta_minima) {
            return response()->json([
                'status' => 'error', 
                'message' => 'La cantidad mínima es ' . floatval($producto->venta_minima)
            ], 422);
        }

        $item->cantidad = $request->cantidad;
        $item->save();

        return response()->json(['status' => 'success']);
    }

    public function remove(Request $request)
    {
        Carrito::where('id', $request->id)->where('usuario_id', Auth::id())->delete();
        return response()->json(['status' => 'success']);
    }
}