@extends('layouts.app')

@section('title', 'Iniciar Sesión - Agrosuministros Rodríguez')

@section('content')
<div class="min-h-screen flex items-center justify-center relative py-12 px-4 sm:px-6 lg:px-8 bg-slate-900">
    
    {{-- FONDO DE IMAGEN CON DEGRADADO CORPORATIVO --}}
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=2070&auto=format&fit=crop" 
             alt="Fondo agrícola" 
             class="w-full h-full object-cover object-center opacity-40">
        <div class="absolute inset-0 bg-gradient-to-br from-[#0b1d38]/95 via-[#1e549e]/80 to-[#3492cf]/40 backdrop-blur-[2px]"></div>
    </div>

    {{-- CONTENIDO PRINCIPAL --}}
    <div class="w-full max-w-md relative z-10 animate-fade-in-up">

        {{-- TARJETA GLASSMORPHISM --}}
        <div class="bg-white/95 backdrop-blur-xl rounded-[2.5rem] shadow-[0_25px_50px_-12px_rgba(11,29,56,0.45)] border border-white/60 p-8 sm:p-10 relative overflow-hidden">
            
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-block group mb-4">
                    <img src="{{ asset('img/logo.png') }}" alt="Agrosuministros Rodríguez" class="h-16 mx-auto object-contain transition-transform group-hover:scale-105 duration-300">
                </a>
                <h1 class="text-2xl sm:text-3xl font-black text-agro-dark tracking-tight">¡Bienvenido!</h1>
                <p class="text-sm text-gray-500 font-medium mt-1.5">Ingresa tus credenciales para continuar.</p>
            </div>

            @if (session('success_reset'))
                <div class="mb-5 bg-emerald-50 border-l-4 border-emerald-500 p-3.5 rounded-r-xl shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
                        <p class="text-xs text-emerald-800 font-bold leading-relaxed">{{ session('success_reset') }}</p>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Input: Correo --}}
                <div class="group">
                    <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-primary">Correo Electrónico</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-gray-400 text-[20px] group-focus-within:text-primary transition-colors">mail</span>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" 
                               class="w-full h-12 rounded-xl bg-slate-50 border @error('email') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus:border-secondary focus:bg-white focus:ring-2 focus:ring-secondary/20 transition-all duration-300 pl-12 pr-4 text-sm font-bold text-agro-dark placeholder:text-gray-400 shadow-inner" 
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
                        <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest transition-colors group-focus-within:text-primary">Contraseña</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[11px] font-bold text-secondary hover:text-primary transition-colors">¿Olvidaste tu contraseña?</a>
                        @endif
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-gray-400 text-[20px] group-focus-within:text-primary transition-colors">key</span>
                        </div>
                        <input type="password" name="password" 
                               class="w-full h-12 rounded-xl bg-slate-50 border border-gray-200 focus:border-secondary focus:bg-white focus:ring-2 focus:ring-secondary/20 transition-all duration-300 pl-12 pr-4 text-sm font-bold text-agro-dark placeholder:text-gray-400 tracking-widest shadow-inner" 
                               placeholder="••••••••" required>
                    </div>
                </div>

                {{-- Checkbox: Recordar --}}
                <div class="flex items-center pt-1">
                    <input id="remember" name="remember" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-secondary focus:ring-offset-0 cursor-pointer shadow-sm">
                    <label for="remember" class="ml-2 block text-xs font-bold text-gray-600 cursor-pointer select-none hover:text-agro-dark transition-colors">
                        Mantener sesión iniciada
                    </label>
                </div>

                {{-- Botón Ingresar --}}
                <button type="submit" class="btn-primary w-full h-12 mt-2 rounded-xl text-sm uppercase tracking-wider justify-center shadow-lg shadow-primary/25 group">
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
                    <span class="bg-white px-3 text-[10px] font-black text-gray-500 uppercase tracking-widest">
                        ¿No tienes cuenta?
                    </span>
                </div>
            </div>

            {{-- Botón Registrarse --}}
            <a href="{{ route('register') }}" class="btn-outline w-full h-12 rounded-xl text-sm font-bold justify-center">
                Crear cuenta nueva
            </a>

        </div>
        
        {{-- Footer flotante --}}
        <p class="text-center mt-8 text-[11px] text-white/75 font-black uppercase tracking-[0.2em] drop-shadow-sm">
            Agrosuministros Rodríguez &copy; {{ date('Y') }}
        </p>

    </div>
</div>
@endsection