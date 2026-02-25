@extends('layouts.app')

@section('title', 'Iniciar Sesión - Corpo Agrícola')

@section('content')
{{-- El contenedor ahora ocupa toda la pantalla (h-screen) sin márgenes --}}
<div class="min-h-screen flex items-center justify-center relative py-12 px-4 sm:px-6 lg:px-8">
    
    {{-- 1. FONDO DE IMAGEN (Campo de trigo al atardecer) --}}
    <div class="absolute inset-0 z-0">
        {{-- Usamos la URL de Unsplash temporalmente. Si descargaste la imagen, cámbiala por: src="{{ asset('img/photo-1500382017468-9049fed747ef.jpg') }}" --}}
        <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=2070&auto=format&fit=crop" 
             alt="Fondo de campo de trigo" 
             class="w-full h-full object-cover object-center">
        
        {{-- DEGRADADO OVERLAY (Mezcla el verde oscuro corporativo con el atardecer y desenfoca ligeramente) --}}
        <div class="absolute inset-0 bg-gradient-to-br from-agro-dark/95 via-agro-dark/20 to-primary/40 backdrop-blur-[2px]"></div>
    </div>

    {{-- CONTENIDO PRINCIPAL --}}
    <div class="w-full max-w-md relative z-10 animate-fade-in-up">

        {{-- 2. TARJETA EFECTO CRISTAL (Glassmorphism) --}}
        <div class="bg-white/95 backdrop-blur-xl rounded-[2.5rem] shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)] border border-white/50 p-8 sm:p-10 relative overflow-hidden">
            
            <div class="text-center mb-8">
                <h1 class="text-2xl sm:text-3xl font-black text-agro-dark tracking-tight">¡Bienvenido!</h1>
                <p class="text-sm text-gray-500 font-medium mt-2">Ingresa tus credenciales para continuar.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Input: Correo --}}
                <div class="group">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-primary">Correo Electrónico</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-gray-400 text-[20px] group-focus-within:text-primary transition-colors">mail</span>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" 
                               class="w-full h-12 rounded-xl bg-gray-50 border @error('email') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-all duration-300 pl-12 pr-4 text-sm font-bold text-agro-dark placeholder:text-gray-400 placeholder:font-medium shadow-inner" 
                               placeholder="usuario@correo.com" required autofocus>
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-red-500 font-bold ml-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Input: Contraseña --}}
                <div class="group">
                    <div class="flex items-center justify-between mb-1.5 ml-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest transition-colors group-focus-within:text-primary">Contraseña</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[10px] font-bold text-primary hover:text-agro-dark transition-colors">¿Olvidaste tu contraseña?</a>
                        @endif
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-gray-400 text-[20px] group-focus-within:text-primary transition-colors">key</span>
                        </div>
                        <input type="password" name="password" 
                               class="w-full h-12 rounded-xl bg-gray-50 border border-gray-200 focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-all duration-300 pl-12 pr-4 text-sm font-bold text-agro-dark placeholder:text-gray-400 tracking-widest shadow-inner" 
                               placeholder="••••••••" required>
                    </div>
                </div>

                {{-- Checkbox: Recordar --}}
                <div class="flex items-center pt-1">
                    <input id="remember" name="remember" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary focus:ring-offset-0 cursor-pointer shadow-sm">
                    <label for="remember" class="ml-2 block text-xs font-bold text-gray-500 cursor-pointer select-none hover:text-agro-dark transition-colors">
                        Mantener sesión iniciada
                    </label>
                </div>

                {{-- Botón Ingresar --}}
                <button type="submit" class="w-full h-12 mt-2 rounded-xl bg-primary text-agro-dark font-black text-sm uppercase tracking-wide hover:bg-green-500 hover:text-white shadow-lg shadow-primary/30 transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-2 group">
                    <span>Ingresar al Sistema</span>
                    <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">login</span>
                </button>

            </form>

            {{-- Separador --}}
            <div class="relative mt-8 mb-6">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-white px-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        ¿No tienes cuenta?
                    </span>
                </div>
            </div>

            {{-- Botón Registrarse --}}
            <a href="{{ route('register') }}" class="w-full h-12 rounded-xl border-2 border-gray-100 text-gray-500 font-bold text-sm flex items-center justify-center hover:border-primary hover:text-primary hover:bg-primary/5 transition-all duration-300">
                Crear cuenta nueva
            </a>

        </div>
        
        {{-- Footer flotante --}}
        <p class="text-center mt-8 text-[10px] text-white/60 font-black uppercase tracking-[0.2em] drop-shadow-sm">
            Corpo Agrícola &copy; {{ date('Y') }}
        </p>

    </div>
</div>
@endsection