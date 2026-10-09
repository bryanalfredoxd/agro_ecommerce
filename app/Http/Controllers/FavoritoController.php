<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Producto;
use App\Models\Favorito;
use App\Models\TasaCambio;

class FavoritoController extends Controller
{
    /**
     * Alternar (agregar o quitar) un producto de favoritos para el usuario autenticado.
     */
    public function toggle(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => 'Debes iniciar sesión para guardar productos en tus favoritos.',
                'redirect' => route('login')
            ], 401);
        }

        $request->validate([
            'producto_id' => 'required|integer|exists:productos,id',
        ]);

        $usuarioId = Auth::id();
        $productoId = (int) $request->producto_id;

        $favorito = Favorito::where('usuario_id', $usuarioId)
            ->where('producto_id', $productoId)
            ->first();

        if ($favorito) {
            $favorito->delete();
            $totalFavoritos = Favorito::where('usuario_id', $usuarioId)->count();

            return response()->json([
                'status' => 'success',
                'action' => 'removed',
                'is_favorite' => false,
                'message' => 'Producto eliminado de tus favoritos.',
                'total_favoritos' => $totalFavoritos
            ]);
        } else {
            Favorito::create([
                'usuario_id' => $usuarioId,
                'producto_id' => $productoId
            ]);
            $totalFavoritos = Favorito::where('usuario_id', $usuarioId)->count();

            return response()->json([
                'status' => 'success',
                'action' => 'added',
                'is_favorite' => true,
                'message' => '¡Producto guardado en tus favoritos!',
                'total_favoritos' => $totalFavoritos
            ]);
        }
    }

    /**
     * Mostrar la lista de productos favoritos del usuario autenticado.
     */
    public function index()
    {
        $usuario = Auth::user();
        $tasaDolar = TasaCambio::obtenerValorUSD() ?? 1;

        $productos = $usuario->productosFavoritos()
            ->whereNull('productos.eliminado_at')
            ->with(['categoria', 'marca', 'imagenes' => function($q) {
                $q->where('es_principal', 1);
            }])
            ->paginate(12);

        $favoritosIds = $usuario->productosFavoritos()->pluck('productos.id')->toArray();

        return view('perfil.favoritos', compact('productos', 'tasaDolar', 'favoritosIds'));
    }
}
