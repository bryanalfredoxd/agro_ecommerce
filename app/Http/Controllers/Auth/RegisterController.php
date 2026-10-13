<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

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
        // CONSTRUIR DATOS COMPUESTOS PREVIAMENTE
        $documentoCompleto = $request->tipo_doc . '-' . $request->documento_identidad;
        $numeroLimpio = preg_replace('/[^0-9]/', '', $request->telefono);
        $codigo = $request->codigo_pais ?? '';
        $telefonoCompleto = $codigo . $numeroLimpio;

        // Buscar si existe un registro previo con ese documento (ej: registro parcial desde el POS)
        $usuarioExistente = User::where(function($q) use ($documentoCompleto, $request) {
            $q->where('documento_identidad', $documentoCompleto)
              ->orWhere('documento_identidad', $request->documento_identidad);
        })->first();

        // 1. Validaciones Básicas de Formato
        $emailRule = ['required', 'string', 'email', 'max:150'];
        if ($usuarioExistente) {
            $emailRule[] = Rule::unique('usuarios', 'email')->ignore($usuarioExistente->id);
        } else {
            $emailRule[] = 'unique:usuarios,email';
        }

        $request->validate([
            'nombre' => ['required', 'string', 'min:2', 'max:100'],
            'apellido' => ['required', 'string', 'min:2', 'max:100'], 
            'email' => $emailRule,
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'codigo_pais' => ['required', 'string', 'max:10'], 
            'telefono' => ['required', 'string', 'min:7', 'max:20'],
            'tipo_doc' => ['required', 'in:V,E,J,G'],
            'documento_identidad' => ['required', 'string', 'min:5', 'max:20'],
            'terms' => ['required', 'accepted'],
        ], [
            'nombre.required' => 'El campo Nombre es obligatorio.',
            'nombre.min' => 'El campo Nombre debe tener al menos :min caracteres.',
            'nombre.max' => 'El campo Nombre no puede exceder los :max caracteres.',
            
            'apellido.required' => 'El campo Apellido es obligatorio.',
            'apellido.min' => 'El campo Apellido debe tener al menos :min caracteres.',
            'apellido.max' => 'El campo Apellido no puede exceder los :max caracteres.',
            
            'email.required' => 'El campo Correo Electrónico es obligatorio.',
            'email.email' => 'El campo Correo Electrónico debe ser una dirección válida (ej: usuario@correo.com).',
            'email.max' => 'El campo Correo Electrónico no puede exceder los :max caracteres.',
            'email.unique' => 'El correo electrónico ingresado ya se encuentra registrado con otra cuenta.',
            
            'password.required' => 'El campo Contraseña es obligatorio.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide con la contraseña ingresada.',
            'password.min' => 'La contraseña debe contener al menos :min caracteres.',
            
            'codigo_pais.required' => 'El código de país es obligatorio.',
            
            'telefono.required' => 'El campo Teléfono / WhatsApp es obligatorio.',
            'telefono.min' => 'El número de teléfono debe tener al menos :min dígitos.',
            'telefono.max' => 'El número de teléfono no puede exceder los :max caracteres.',
            
            'tipo_doc.required' => 'Debes seleccionar el tipo de documento (V, E, J o G).',
            'tipo_doc.in' => 'El tipo de documento seleccionado no es válido (debe ser V, E, J o G).',
            
            'documento_identidad.required' => 'El campo Cédula / Documento es obligatorio.',
            'documento_identidad.min' => 'El número de cédula o documento debe tener al menos :min dígitos.',
            'documento_identidad.max' => 'El número de cédula o documento no puede exceder los :max caracteres.',
            
            'terms.required' => 'Debes aceptar los términos y condiciones para continuar.',
            'terms.accepted' => 'Debes aceptar los términos y condiciones para continuar.',
        ]);

        $tipoCliente = 'natural';

        // 2. EVALUAR REGISTRO O ACTUALIZACIÓN DE USUARIO PARCIAL
        if ($usuarioExistente) {
            // Si ya tiene contraseña, significa que ya tiene una cuenta web activa y formal
            if (!empty($usuarioExistente->password_hash)) {
                return back()
                    ->withErrors(['documento_identidad' => 'Este documento ya se encuentra registrado con una cuenta activa. Por favor, inicia sesión.'])
                    ->withInput();
            }

            // Verificar si el teléfono ya está tomado por otra cuenta distinta
            $telDuplicado = User::where('telefono', $telefonoCompleto)
                                ->where('id', '!=', $usuarioExistente->id)
                                ->exists();
            if ($telDuplicado) {
                return back()
                    ->withErrors(['telefono' => 'Este número de teléfono ya está asociado a otra cuenta.'])
                    ->withInput();
            }

            // ACTUALIZAR REGISTRO PARCIAL:
            // Al conservar el ID existente en la tabla `usuarios`, se mantienen automáticamente
            // vinculados todos los pedidos anteriores que realizó en el POS / tienda física.
            $usuarioExistente->update([
                'rol_id' => 4, // ID 4 = Cliente
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->email,
                'password_hash' => Hash::make($request->password), 
                'telefono' => $telefonoCompleto, 
                'documento_identidad' => $documentoCompleto,
                'tipo_cliente' => $tipoCliente,
                'activo' => true,
            ]);

            Auth::login($usuarioExistente);

            return redirect('/')->with('success_register', '¡Bienvenido! Tu cuenta ha sido activada y vinculada con tus compras anteriores realizadas en tienda.');
        }

        // Si no existía el usuario, verificar que el teléfono no esté repetido
        if (User::where('telefono', $telefonoCompleto)->exists()) {
            return back()
                ->withErrors(['telefono' => 'Este número de teléfono ya está asociado a otra cuenta.'])
                ->withInput();
        }

        // Si es el primer usuario registrado en toda la plataforma, se le asigna rol Administrador (ID 1)
        $esPrimerUsuario = User::count() === 0;
        $rolAsignado = $esPrimerUsuario ? 1 : 4;

        // 3. Crear nuevo usuario en la base de datos
        $user = User::create([
            'rol_id' => $rolAsignado,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password), 
            'telefono' => $telefonoCompleto, 
            'documento_identidad' => $documentoCompleto,
            'tipo_cliente' => $tipoCliente,
            'activo' => true,
        ]);

        // 4. Iniciar sesión y redirigir
        Auth::login($user);

        return redirect('/')->with('success_register', '¡Bienvenido a la familia! Tu cuenta ha sido creada correctamente.');
    }
}