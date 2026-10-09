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
        if (Auth::check()) {
            $user = Auth::user();

            if (!$user->activo) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Tu cuenta se encuentra inactiva. Contacta al administrador.');
            }

            if ($user->esAdmin() || $user->esPersonal()) {
                return $next($request);
            }
        }

        return redirect('/')->with('error', 'No tienes permisos para acceder al panel administrativo.');
    }
}