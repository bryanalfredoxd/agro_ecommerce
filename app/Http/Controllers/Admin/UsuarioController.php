<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Permiso;
use App\Models\PermisoExtraUsuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('rol');

        // 1. Buscador (Por email, documento, nombre o apellido)
        if ($request->filled('buscar')) {
            $busqueda = $request->buscar;
            $query->where(function ($q) use ($busqueda) {
                $q->where('email', 'LIKE', "%{$busqueda}%")
                  ->orWhere('documento_identidad', 'LIKE', "%{$busqueda}%")
                  ->orWhere('nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('apellido', 'LIKE', "%{$busqueda}%");
            });
        }

        // 2. Filtro por Rol (Tabs)
        if ($request->filled('rol_id') && $request->rol_id !== 'all') {
            $query->where('rol_id', $request->rol_id);
        }

        // 3. Ordenamiento
        switch ($request->orden) {
            case 'az': $query->orderBy('nombre', 'asc'); break;
            case 'za': $query->orderBy('nombre', 'desc'); break;
            case 'oldest': $query->orderBy('creado_at', 'asc'); break;
            case 'newest':
            default: $query->orderBy('creado_at', 'desc'); break;
        }

        // Paginación
        $usuarios = $query->paginate(10);

        // Si la petición es AJAX, solo devolvemos la tabla y la paginación en HTML
        if ($request->ajax()) {
            return view('admin.usuarios.partials._table', compact('usuarios'))->render();
        }

        // Si es carga normal, enviamos todo a la vista principal
        $roles = Role::all();
        $permisos = Permiso::all();

        return view('admin.usuarios.index', compact('usuarios', 'roles', 'permisos'));
    }

    // Obtener los permisos extra de un usuario (Para el Modal)
    public function getPermisosExtra($id)
    {
        $usuario = User::findOrFail($id);
        $permisosExtra = PermisoExtraUsuario::where('usuario_id', $id)->get();
        
        return response()->json([
            'rol_id' => $usuario->rol_id,
            'permisosExtra' => $permisosExtra
        ]);
    }

    // Guardar los permisos extra de un usuario
    public function updatePermisosExtra(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        // Bloqueo de seguridad: No modificar al Super Admin
        if ($usuario->id == 1) {
            return response()->json(['success' => false, 'message' => 'No puedes modificar la configuración del Super Admin.']);
        }

        // 1. Actualizar el Rol del Usuario
        if ($request->has('rol_id')) {
            $usuario->rol_id = $request->rol_id;
            $usuario->save();
        }

        // 2. Borramos todas las excepciones anteriores de este usuario
        PermisoExtraUsuario::where('usuario_id', $id)->delete();

        // 3. Insertamos las nuevas (solo las que sean 'permitir' o 'denegar')
        if ($request->has('permisos')) {
            $inserts = [];
            foreach ($request->permisos as $permiso_id => $accion) {
                if (in_array($accion, ['permitir', 'denegar'])) {
                    $inserts[] = [
                        'usuario_id' => $id,
                        'permiso_id' => $permiso_id,
                        'accion'     => $accion
                    ];
                }
            }
            if (count($inserts) > 0) {
                PermisoExtraUsuario::insert($inserts);
            }
        }

        return response()->json(['success' => true, 'message' => 'Configuración actualizada correctamente.']);
    }
}