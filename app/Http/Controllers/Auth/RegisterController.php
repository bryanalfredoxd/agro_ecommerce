<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    // Mostrar el formulario
    public function create()
    {
        return view('auth.register');
    }

    // Guardar el usuario
    public function store(Request $request)
    {
        // 1. Validaciones
        $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['nullable', 'string', 'max:100'], 
            'email' => ['required', 'string', 'email', 'max:150', 'unique:usuarios'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'telefono' => ['required', 'string', 'max:20'],
            'tipo_cliente' => ['required', 'in:natural,juridico,finca_productor'],
            'tipo_doc' => ['required', 'in:V,E,J,G'],
            'documento_identidad' => ['required', 'string', 'max:20'],
            'terms' => ['required', 'accepted'],
        ]);

        // 2. Concatenar documento (Ej: V-12345678)
        $documentoCompleto = $request->tipo_doc . '-' . $request->documento_identidad;

        // 3. Crear en la base de datos
        $user = User::create([
            'rol_id' => 4, // ID 4 = Cliente (Asegúrate de tener este ID en tu tabla 'roles')
            'nombre' => $request->nombre,
            'apellido' => ($request->tipo_cliente === 'juridico') ? null : $request->apellido,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password), // Encriptar
            'telefono' => $request->telefono,
            'documento_identidad' => $documentoCompleto,
            'tipo_cliente' => $request->tipo_cliente,
            'activo' => true,
        ]);

        // 4. Iniciar sesión y redirigir
        Auth::login($user);

        return redirect('/'); 
    }
}