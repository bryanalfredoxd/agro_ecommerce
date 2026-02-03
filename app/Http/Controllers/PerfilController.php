<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;

class PerfilController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load('direcciones');
        return view('perfil.index', compact('user'));
    }

    public function updateDatos(Request $request)
    {
        $user = auth()->user();

        // 1. Validaciones de formato
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => [$user->tipo_cliente == 'juridico' ? 'nullable' : 'required', 'string', 'max:100'],
            'telefono' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:150', Rule::unique('usuarios')->ignore($user->id)],
            // Campo obligatorio para autorizar
            'password_actual_auth' => ['required', 'string'], 
        ], [
            'password_actual_auth.required' => 'Por seguridad, debes ingresar tu contraseña para guardar cambios.',
            'email.unique' => 'Este correo ya está registrado.',
        ]);

        // 2. SEGURIDAD CRÍTICA: Verificar contraseña actual
        // Usamos Hash::check contra tu campo personalizado 'password_hash'
        if (!Hash::check($request->password_actual_auth, $user->password_hash)) {
            return back()
                ->withErrors(['password_actual_auth' => 'La contraseña es incorrecta. No se realizaron cambios.'])
                ->withInput(); // Mantiene los datos que el usuario escribió
        }

        // 3. Si pasó la seguridad, actualizamos
        $user->update([
            'nombre' => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'telefono' => $validated['telefono'],
            'email' => $validated['email'],
        ]);

        return back()->with('success-message', 'Datos actualizados y verificados correctamente.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'], 
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'current_password.current_password' => 'La contraseña actual no es correcta.',
            'password.confirmed' => 'Las nuevas contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.'
        ]);

        auth()->user()->update([
            'password_hash' => Hash::make($request->password)
        ]);

        return back()->with('success-message', 'Tu contraseña ha sido modificada con éxito.');
    }
}