<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica si el usuario está logueado y si su rol_id es 1 (Admin)
        if (Auth::check() && Auth::user()->rol_id == 1) {
            return $next($request);
        }

        // Si no es admin, lo devolvemos al home con un error
        return redirect('/')->with('error', 'No tienes permisos de administrador para acceder a esta área.');
    }
}