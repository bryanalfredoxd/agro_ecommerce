<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Permiso;
use App\Models\PermisoExtraUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $roles = Role::with('permisos')->get();
        $permisos = Permiso::all()->keyBy('nombre');
        $modulos = config('permisos_modulos', []);

        return view('admin.usuarios.index', compact('usuarios', 'roles', 'permisos', 'modulos'));
    }

    // Obtener los permisos del rol y excepciones extra de un usuario (Para el Modal)
    public function getPermisosExtra($id)
    {
        $usuario = User::with('rol')->findOrFail($id);

        // Traer todos los roles con sus IDs de permisos asignados para sincronización en tiempo real
        $rolesData = Role::with('permisos:id')->get()->map(function($r) {
            return [
                'id' => $r->id,
                'nombre' => $r->nombre,
                'permiso_ids' => $r->permisos->pluck('id')->map(fn($v) => (int)$v)->values()->toArray(),
            ];
        });

        // Obtener las excepciones guardadas para este usuario
        $excepciones = PermisoExtraUsuario::where('usuario_id', $id)
            ->get(['permiso_id', 'accion'])
            ->mapWithKeys(function($item) {
                return [(int)$item->permiso_id => $item->accion];
            })
            ->toArray(); // [ permiso_id => 'permitir' | 'denegar' ]

        // Permisos naturales del rol actual
        $permisosDelRol = [];
        if ($usuario->rol_id) {
            $permisosDelRol = DB::table('rol_permisos')
                ->where('rol_id', $usuario->rol_id)
                ->pluck('permiso_id')
                ->map(fn($v) => (int)$v)
                ->values()
                ->toArray();
        }

        return response()->json([
            'usuario_id' => $usuario->id,
            'nombre' => $usuario->nombre . ' ' . $usuario->apellido,
            'email' => $usuario->email,
            'rol_id' => $usuario->rol_id,
            'roles' => $rolesData,
            'permisos_del_rol' => $permisosDelRol,
            'excepciones' => $excepciones
        ]);
    }

    // Guardar los permisos extra / excepciones de un usuario
    public function updatePermisosExtra(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        // Bloqueo de seguridad: No modificar al Super Admin
        if ($usuario->id == 1) {
            return response()->json(['success' => false, 'message' => 'No puedes modificar la configuración del Super Admin.'], 403);
        }

        // 1. Actualizar el Rol del Usuario
        if ($request->filled('rol_id')) {
            $usuario->rol_id = $request->rol_id;
            $usuario->save();
        }

        // 2. Si el usuario solicita restablecer al rol (restablecer = true), borramos excepciones y salimos
        if ($request->boolean('restablecer')) {
            PermisoExtraUsuario::where('usuario_id', $id)->delete();
            return response()->json([
                'success' => true,
                'message' => 'Permisos restablecidos con éxito a la configuración estándar del rol.'
            ]);
        }

        // 3. Borramos las excepciones anteriores
        PermisoExtraUsuario::where('usuario_id', $id)->delete();

        // 4. Obtener los permisos del rol asignado
        $permisosDelRol = [];
        if ($usuario->rol_id) {
            $permisosDelRol = DB::table('rol_permisos')
                ->where('rol_id', $usuario->rol_id)
                ->pluck('permiso_id')
                ->map(fn($v) => (int)$v)
                ->toArray();
        }

        $todosLosPermisos = Permiso::pluck('id')->map(fn($v) => (int)$v)->toArray();
        $permisosMarcados = array_map('intval', (array)$request->input('permisos', []));

        $inserts = [];
        foreach ($todosLosPermisos as $permisoId) {
            $estaMarcado = in_array($permisoId, $permisosMarcados);
            $estaEnRol = in_array($permisoId, $permisosDelRol);

            if ($estaMarcado && !$estaEnRol) {
                // Concedido extra (Excepción)
                $inserts[] = [
                    'usuario_id' => $id,
                    'permiso_id' => $permisoId,
                    'accion'     => 'permitir'
                ];
            } elseif (!$estaMarcado && $estaEnRol) {
                // Denegado extra (Excepción)
                $inserts[] = [
                    'usuario_id' => $id,
                    'permiso_id' => $permisoId,
                    'accion'     => 'denegar'
                ];
            }
        }

        if (count($inserts) > 0) {
            PermisoExtraUsuario::insert($inserts);
        }

        return response()->json([
            'success' => true,
            'message' => 'Permisos del usuario actualizados correctamente.'
        ]);
    }
}