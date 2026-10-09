<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermiso
{
    /**
     * Valida que el usuario autenticado cuente con los permisos necesarios.
     * Admite uno o varios permisos. Si el usuario cuenta con AL MENOS UNO de ellos, se le permite el acceso.
     */
    public function handle(Request $request, Closure $next, ...$permisos): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->activo) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Tu cuenta se encuentra inactiva. Contacta al administrador.');
        }

        // El Administrador principal siempre tiene acceso irrestricto
        if ($user->esAdmin()) {
            return $next($request);
        }

        // Normalizar la lista de permisos requeridos (acepta comas, pipes o múltiples parámetros)
        $permisosRequeridos = [];
        foreach ($permisos as $param) {
            $separados = preg_split('/[,|]/', $param);
            foreach ($separados as $p) {
                $limpio = trim($p);
                if (!empty($limpio)) {
                    $permisosRequeridos[] = $limpio;
                }
            }
        }

        // Verificar si el usuario cuenta con al menos uno de los permisos requeridos
        foreach ($permisosRequeridos as $permiso) {
            if ($user->tienePermiso($permiso)) {
                return $next($request);
            }
        }

        // Si la petición es AJAX o espera respuesta JSON (por ejemplo, botones de acción)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado: No cuentas con permisos suficientes para realizar esta acción.'
            ], 403);
        }

        // Redirección amigable si es navegación web estándar
        $rutaSegura = $this->obtenerRutaSegura($user);

        // Si la ruta segura es la misma a la que intentaba acceder, evitamos redirección infinita
        if ($rutaSegura && $request->url() !== $rutaSegura) {
            return redirect($rutaSegura)->with('error', 'No tienes permisos para acceder a esa sección del sistema.');
        }

        return redirect('/')->with('error', 'No tienes permisos para acceder a los módulos administrativos.');
    }

    /**
     * Determina la mejor ruta administrativa a la que el usuario sí tiene acceso.
     */
    protected function obtenerRutaSegura($user): ?string
    {
        if ($user->tienePermiso('ver_dashboard')) {
            return route('admin.dashboard');
        }
        if ($user->tienePermiso('crear_venta_manual')) {
            return route('admin.pos.index');
        }
        if ($user->tienePermiso('ver_pedidos') || $user->tienePermiso('gestionar_pagos')) {
            return route('admin.pedidos.index');
        }
        if ($user->tienePermiso('ver_productos')) {
            return route('admin.productos.index');
        }
        if ($user->tienePermiso('gestionar_caja_diaria')) {
            return route('admin.caja_diaria.index');
        }
        if ($user->tienePermiso('ver_facturas')) {
            return route('admin.facturas.index');
        }
        if ($user->tienePermiso('gestionar_zonas_delivery') || $user->tienePermiso('ver_logistica')) {
            return route('admin.zonas_delivery.index');
        }
        if ($user->tienePermiso('ver_usuarios')) {
            return route('admin.usuarios.index');
        }

        return null;
    }
}
