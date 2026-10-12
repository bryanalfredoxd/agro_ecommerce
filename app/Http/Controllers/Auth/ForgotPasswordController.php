<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\RestablecerPasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Muestra el formulario para solicitar restablecimiento de contraseña.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Procesa la solicitud y envía el correo con el enlace de recuperación vía SMTP.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Por favor ingresa un correo electrónico válido.'
        ]);

        $user = User::where('email', $request->email)->first();

        // Si el usuario no existe o está desactivado
        if (!$user) {
            return back()->withErrors([
                'email' => 'No encontramos ninguna cuenta asociada a este correo electrónico.'
            ])->withInput();
        }

        if (!$user->activo) {
            return back()->withErrors([
                'email' => 'Esta cuenta se encuentra temporalmente suspendida o inactiva.'
            ])->withInput();
        }

        // Generar token criptográficamente seguro
        $token = Str::random(64);

        // Limpiar tokens previos de este correo
        DB::table('password_resets')->where('email', $user->email)->delete();

        // Guardar nuevo token en la base de datos (válido por 60 minutos)
        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
            'creado_at' => now()
        ]);

        // Construir URL firmada/directa para restablecer
        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email
        ]);

        // Enviar correo electrónico mediante SMTP
        try {
            Mail::to($user->email)->send(new RestablecerPasswordMail($user, $resetUrl));
        } catch (\Throwable $e) {
            \Log::error('Error enviando correo de recuperación de contraseña: ' . $e->getMessage());

            return back()->withErrors([
                'email' => 'No pudimos enviar el correo debido a un problema con el servidor de correos (SMTP). Por favor intenta de nuevo o comunícate con nosotros.'
            ])->withInput();
        }

        return back()->with('status', '¡Listo! Hemos enviado un enlace de recuperación a tu correo electrónico. Por favor revisa tu bandeja de entrada o carpeta de spam.');
    }
}
