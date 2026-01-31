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
        // 1. Validaciones Básicas de Formato
        $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['nullable', 'string', 'max:100'], 
            'email' => ['required', 'string', 'email', 'max:150', 'unique:usuarios,email'], // El correo sí se valida directo aquí
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'codigo_pais' => ['required', 'string', 'max:10'], 
            'telefono' => ['required', 'string', 'max:20'],
            'tipo_cliente' => ['required', 'in:natural,juridico,finca_productor'],
            'tipo_doc' => ['required', 'in:V,E,J,G'],
            'documento_identidad' => ['required', 'string', 'max:20'],
            'terms' => ['required', 'accepted'],
        ]);

        // 2. CONSTRUIR DATOS COMPUESTOS (Para verificar duplicados)

        // A. Documento (Ej: V-12345678)
        $documentoCompleto = $request->tipo_doc . '-' . $request->documento_identidad;

        // B. Teléfono (Ej: +584121234567)
        // Eliminamos cualquier símbolo no numérico del teléfono antes de unirlo
        $numeroLimpio = preg_replace('/[^0-9]/', '', $request->telefono);
        $codigo = $request->codigo_pais ?? '';
        $telefonoCompleto = $codigo . $numeroLimpio;

        // 3. VALIDACIÓN MANUAL DE DUPLICADOS (La parte importante)
        
        // Verificar si la Cédula/RIF ya existe
        if (User::where('documento_identidad', $documentoCompleto)->exists()) {
            return back()
                ->withErrors(['documento_identidad' => 'Este documento ya se encuentra registrado.'])
                ->withInput(); // Devuelve los datos para no borrar el formulario
        }

        // Verificar si el Teléfono ya existe
        if (User::where('telefono', $telefonoCompleto)->exists()) {
            return back()
                ->withErrors(['telefono' => 'Este número de teléfono ya está asociado a otra cuenta.'])
                ->withInput();
        }

        // 4. Crear en la base de datos (Si pasó las validaciones)
        $user = User::create([
            'rol_id' => 4, // ID 4 = Cliente
            'nombre' => $request->nombre,
            'apellido' => ($request->tipo_cliente === 'juridico') ? null : $request->apellido,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password), 
            'telefono' => $telefonoCompleto, 
            'documento_identidad' => $documentoCompleto,
            'tipo_doc' => $request->tipo_doc,
            'tipo_cliente' => $request->tipo_cliente,
            'activo' => true,
        ]);

        // 5. Iniciar sesión y redirigir
        Auth::login($user);

return redirect('/')->with('success_register', '¡Bienvenido a la familia! Tu cuenta ha sido creada correctamente.');
    }
}