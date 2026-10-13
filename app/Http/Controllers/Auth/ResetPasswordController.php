<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ResetPasswordController extends Controller
{
    /**
     * Muestra el formulario para ingresar la nueva contraseña.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Valida el token y actualiza la contraseña del usuario en la base de datos.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'token.required' => 'El código de seguridad del enlace no es válido.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min' => 'La nueva contraseña debe tener al menos :min caracteres.',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors([
                'email' => 'No encontramos ninguna cuenta asociada a este correo electrónico.'
            ])->withInput();
        }

        // Buscar los registros de recuperación de este correo
        $records = DB::table('password_resets')
                    ->where('email', $request->email)
                    ->get();

        $tokenValido = false;
        foreach ($records as $record) {
            // Verificar si el token coincide con el hash
            if (Hash::check($request->token, $record->token)) {
                // Verificar si no ha expirado (60 minutos)
                $fechaCreacion = Carbon::parse($record->created_at ?? $record->creado_at);
                if ($fechaCreacion->addMinutes(60)->isFuture()) {
                    $tokenValido = true;
                    break;
                }
            }
        }

        if (!$tokenValido) {
            return back()->withErrors([
                'email' => 'Este enlace de recuperación ha expirado o ya fue utilizado. Por favor solicita uno nuevo.'
            ])->withInput();
        }

        // Actualizar la contraseña en la columna personalizada `password_hash`
        $user->password_hash = Hash::make($request->password);
        $user->save();

        // Eliminar los tokens usados para este correo
        DB::table('password_resets')->where('email', $user->email)->delete();

        return redirect()->route('login')->with('success_reset', '¡Tu contraseña ha sido restablecida con éxito! Ya puedes iniciar sesión con tu nueva contraseña.');
    }
}
