@extends('layouts.app')

@section('title', 'Iniciar Sesión - Corpo Agrícola')

@section('content')
<div class="min-h-[calc(100vh-80px)] flex items-center justify-center bg-gray-50/50 py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="w-full max-w-md animate-fade-in-up">
        
        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/60 border border-gray-100 p-8 sm:p-10 relative overflow-hidden">
            
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center size-14 bg-primary/10 rounded-2xl mb-4 text-primary">
                    <span class="material-symbols-outlined text-[32px]">lock</span>
                </div>
                <h1 class="text-3xl font-black text-agro-dark tracking-tight">Bienvenido de nuevo</h1>
                <p class="text-sm text-gray-500 font-medium mt-2">Ingresa tus credenciales para continuar.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div class="group">
                    <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-primary">Correo Electrónico</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-gray-400 text-[20px] group-focus-within:text-primary transition-colors">mail</span>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-xl bg-gray-50 border @error('email') border-red-300 ring-2 ring-red-100 @else border-transparent @enderror focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 transition-all duration-300 py-3.5 pl-11 text-sm font-semibold placeholder:text-gray-400" placeholder="usuario@mail.com" required autofocus>
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-red-500 font-bold ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="group">
                    <div class="flex items-center justify-between mb-1.5 ml-1">
                        <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest transition-colors group-focus-within:text-primary">Contraseña</label>
                        <a href="#" class="text-[10px] font-bold text-primary hover:text-agro-dark transition-colors">¿Olvidaste tu contraseña?</a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-gray-400 text-[20px] group-focus-within:text-primary transition-colors">key</span>
                        </div>
                        <input type="password" name="password" class="w-full rounded-xl bg-gray-50 border border-transparent focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 transition-all duration-300 py-3.5 pl-11 text-sm font-semibold placeholder:text-gray-400 tracking-widest" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="size-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer">
                    <label for="remember" class="ml-2 block text-xs font-medium text-gray-600 cursor-pointer select-none">
                        Mantener sesión iniciada
                    </label>
                </div>

                <button type="submit" class="w-full py-4 px-6 rounded-xl bg-primary text-agro-dark font-black text-sm uppercase tracking-wide hover:bg-primary/90 hover:shadow-lg hover:shadow-primary/30 hover:-translate-y-1 active:scale-[0.98] transition-all duration-300 flex items-center justify-center gap-2 group shadow-md">
                    <span>Ingresar al Sistema</span>
                    <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">login</span>
                </button>

            </form>

            <div class="relative mt-8 mb-6">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-100"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-white px-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        ¿No tienes cuenta?
                    </span>
                </div>
            </div>

            <a href="{{ route('register') }}" class="w-full py-3.5 px-6 rounded-xl border-2 border-gray-100 text-gray-600 font-bold text-sm text-center block hover:border-primary hover:text-primary hover:bg-primary/5 transition-all duration-300">
                Crear cuenta nueva
            </a>

        </div>
        
        <p class="text-center mt-6 text-[10px] text-gray-300 font-bold uppercase tracking-[0.2em]">
            Agropecuaria Venezuela
        </p>

    </div>
</div>
@endsection