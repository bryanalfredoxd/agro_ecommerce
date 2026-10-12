@extends('layouts.app')

@section('title', 'Recuperar Contraseña - Agrosuministros Rodríguez')

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
            
            <div class="text-center mb-7">
                <a href="{{ route('home') }}" class="inline-block group mb-3">
                    <img src="{{ asset('img/logo.png') }}" alt="Agrosuministros Rodríguez" class="h-16 mx-auto object-contain transition-transform group-hover:scale-105 duration-300">
                </a>
                <h1 class="text-2xl sm:text-3xl font-black text-agro-dark tracking-tight">Recuperar Contraseña</h1>
                <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1.5 leading-relaxed">
                    Ingresa tu correo registrado y te enviaremos las instrucciones para restablecer tu acceso.
                </p>
            </div>

            {{-- Mensaje de Éxito al Enviar Correo --}}
            @if (session('status'))
                <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-2xl shadow-sm">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-emerald-600 text-[22px] shrink-0">mark_email_read</span>
                        <div>
                            <h4 class="text-xs font-black text-emerald-900 uppercase tracking-wider">Correo Enviado</h4>
                            <p class="text-xs text-emerald-700 font-medium mt-0.5 leading-relaxed">{{ session('status') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Mensaje de Error General --}}
            @if (isset($errors) && $errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-2xl shadow-sm">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-red-500 text-[22px] shrink-0">error</span>
                        <div>
                            <h4 class="text-xs font-black text-red-900 uppercase tracking-wider">No se pudo procesar</h4>
                            <p class="text-xs text-red-700 font-medium mt-0.5">{{ $errors->first() }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                {{-- Input: Correo Electrónico --}}
                <div class="group">
                    <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-primary">
                        Correo Electrónico de tu Cuenta
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-gray-400 text-[20px] group-focus-within:text-primary transition-colors">mail</span>
                        </div>
                        <input type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               class="w-full h-12 rounded-xl bg-slate-50 border @error('email') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus:border-secondary focus:bg-white focus:ring-2 focus:ring-secondary/20 transition-all duration-300 pl-12 pr-4 text-sm font-bold text-agro-dark placeholder:text-gray-400 shadow-inner" 
                               placeholder="usuario@correo.com" 
                               required 
                               autofocus>
                    </div>
                </div>

                {{-- Botón Enviar --}}
                <button type="submit" class="btn-primary w-full h-12 mt-2 rounded-xl text-sm uppercase tracking-wider justify-center shadow-lg shadow-primary/25 group">
                    <span>Enviar Enlace de Recuperación</span>
                    <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">send</span>
                </button>

            </form>

            {{-- Separador --}}
            <div class="relative mt-8 mb-6">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-white px-3 text-[10px] font-black text-gray-500 uppercase tracking-widest">
                        O
                    </span>
                </div>
            </div>

            {{-- Enlace Volver a Login --}}
            <a href="{{ route('login') }}" class="btn-outline w-full h-12 rounded-xl text-sm font-bold justify-center flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                <span>Volver al Inicio de Sesión</span>
            </a>

        </div>
        
        {{-- Footer flotante --}}
        <p class="text-center mt-8 text-[11px] text-white/75 font-black uppercase tracking-[0.2em] drop-shadow-sm">
            Agrosuministros Rodríguez &copy; {{ date('Y') }}
        </p>

    </div>
</div>
@endsection
