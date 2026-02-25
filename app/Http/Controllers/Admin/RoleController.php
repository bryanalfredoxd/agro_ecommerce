<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permiso;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        // Traemos todos los roles con la cuenta de usuarios y sus permisos
        $roles = Role::withCount('usuarios')->with('permisos')->get();
        // Traemos todos los permisos para dibujarlos en el modal
        $permisos = Permiso::all();

        return view('admin.roles.index', compact('roles', 'permisos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:roles,nombre'
        ]);

        $role = Role::create(['nombre' => $request->nombre]);

        // Si se enviaron permisos, los sincronizamos en la tabla rol_permisos
        if ($request->has('permisos')) {
            $role->permisos()->sync($request->permisos);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Rol creado exitosamente.');
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        // Bloqueo de seguridad: Nadie puede quitarle los permisos ni cambiar el nombre al Admin (ID 1)
        if ($role->id == 1) {
            return redirect()->route('admin.roles.index')->with('error', 'El Rol de Administrador Principal no puede ser modificado.');
        }

        $request->validate([
            'nombre' => 'required|string|max:50|unique:roles,nombre,' . $id
        ]);

        $role->update(['nombre' => $request->nombre]);

        // Sincronizar permisos (agrega los nuevos y elimina los que se desmarcaron)
        if ($request->has('permisos')) {
            $role->permisos()->sync($request->permisos);
        } else {
            // Si no mandó ningún permiso, vaciamos sus relaciones
            $role->permisos()->detach();
        }

        return redirect()->route('admin.roles.index')->with('success', 'Rol y permisos actualizados.');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Proteger los roles base del sistema para evitar errores críticos
        if (in_array($role->id, [1, 2, 3, 4, 5])) {
            return redirect()->route('admin.roles.index')->with('error', 'Los roles predeterminados del sistema no pueden ser eliminados.');
        }

        if ($role->usuarios()->count() > 0) {
            return redirect()->route('admin.roles.index')->with('error', 'No puedes eliminar este rol porque tiene usuarios asignados.');
        }

        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Rol eliminado correctamente.');
    }
}