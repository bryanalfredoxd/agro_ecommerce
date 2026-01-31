<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    // 1. Mostrar el formulario
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // 2. Procesar el Login
    public function login(Request $request)
    {
        // Validar datos básicos
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Buscar al usuario por su email
        $user = User::where('email', $request->email)->first();

        // Verificar si el usuario existe Y si la contraseña coincide
        if ($user && Hash::check($request->password, $user->password_hash)) {
            
            // Verificar si el usuario está activo
            if (!$user->activo) {
                return back()->withErrors([
                    'email' => 'Tu cuenta ha sido desactivada. Contacta soporte.',
                ]);
            }

            // ¡Login Exitoso!
            // El 'true' en el segundo parámetro es para "Recordarme" (Remember Me)
            Auth::login($user, $request->filled('remember'));
            
            $request->session()->regenerate();

            return redirect()->intended(route('home'));
        }

        // Si falla (Contraseña incorrecta o usuario no encontrado)
        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }
}