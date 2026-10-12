@extends('layouts.app')

@section('title', 'Crear Cuenta - ' . ($configTienda->nombre_empresa ?? \App\Models\ConfiguracionTienda::nombre()))

@section('content')
<div class="min-h-[calc(100vh-80px)] flex items-center justify-center relative overflow-hidden py-12 px-4 sm:px-6 lg:px-8 bg-slate-900">
    
    {{-- FONDO ABSOLUTO CORPORATIVO --}}
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=2070&auto=format&fit=crop" 
             alt="Fondo agrícola" 
             class="w-full h-full object-cover object-center opacity-40">
        <div class="absolute inset-0 bg-gradient-to-br from-[#0b1d38]/95 via-[#1e549e]/80 to-[#3492cf]/40 backdrop-blur-[2px]"></div>
    </div>

    {{-- CONTENIDO PRINCIPAL --}}
    <div class="w-full max-w-[650px] relative z-10 animate-fade-in-up">

        {{-- ALERTA DE ERRORES --}}
        @if (isset($errors) && $errors->any())
            <div class="mb-6 bg-red-50/95 backdrop-blur-md border-l-4 border-red-500 p-4 rounded-r-2xl shadow-lg">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-red-500 text-[24px]">error</span>
                    <div>
                        <h3 class="text-sm font-black text-red-800 uppercase tracking-wider">Por favor corrige los siguientes errores:</h3>
                        <p class="text-xs text-red-700 font-medium">{{ $errors->first() }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- TARJETA GLASSMORPHISM --}}
        <div class="bg-white/95 backdrop-blur-xl rounded-[2.5rem] shadow-[0_25px_50px_-12px_rgba(11,29,56,0.45)] border border-white/60 p-6 sm:p-10 relative overflow-hidden" id="mainCard">
            
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-block group mb-3">
                    <img src="{{ asset('img/logo.png') }}" alt="{{ $configTienda->nombre_empresa ?? \App\Models\ConfiguracionTienda::nombre() }}" class="h-16 mx-auto object-contain transition-transform group-hover:scale-105 duration-300">
                </a>
                <h1 class="text-2xl sm:text-3xl font-black text-agro-dark tracking-tight">Crear Cuenta</h1>
                <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1.5">
                    Regístrate para comprar en línea y gestionar tus pedidos de manera ágil.
                </p>
            </div>

            <form method="POST" action="{{ route('register') }}" id="registerForm" class="space-y-5" novalidate>
                @csrf

                {{-- Tipo de cuenta: Persona Natural únicamente --}}
                <input type="hidden" name="tipo_cliente" value="natural">

                {{-- FILA 1: NOMBRE Y APELLIDO --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Nombre --}}
                    <div class="group">
                        <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-primary">
                            Nombre <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <span class="material-symbols-outlined text-gray-400 text-[18px] group-focus-within:text-primary transition-colors">badge</span>
                            </div>
                            <input type="text" 
                                   name="nombre" 
                                   id="input_nombre" 
                                   value="{{ old('nombre') }}" 
                                   required
                                   class="w-full h-12 rounded-xl bg-gray-50 border @error('nombre') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-all duration-300 pl-10 pr-4 text-sm font-bold text-agro-dark placeholder:text-gray-400 shadow-inner" 
                                   placeholder="Ej: Juan">
                        </div>
                        @error('nombre') <p class="mt-1 text-xs text-red-500 font-bold ml-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Apellido --}}
                    <div class="group">
                        <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-primary">
                            Apellido <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <span class="material-symbols-outlined text-gray-400 text-[18px] group-focus-within:text-primary transition-colors">badge</span>
                            </div>
                            <input type="text" 
                                   name="apellido" 
                                   id="input_apellido" 
                                   value="{{ old('apellido') }}" 
                                   required
                                   class="w-full h-12 rounded-xl bg-gray-50 border @error('apellido') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-all duration-300 pl-10 pr-4 text-sm font-bold text-agro-dark placeholder:text-gray-400 shadow-inner" 
                                   placeholder="Ej: Pérez">
                        </div>
                        @error('apellido') <p class="mt-1 text-xs text-red-500 font-bold ml-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- FILA 2: CÉDULA Y TELÉFONO --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Cédula / Documento --}}
                    <div class="group">
                        <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-primary">
                            Cédula / Documento <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex h-12 rounded-xl bg-gray-50 border @error('documento_identidad') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus-within:border-primary focus-within:bg-white focus-within:ring-2 focus-within:ring-primary/20 transition-all duration-300 shadow-inner">
                            <select name="tipo_doc" class="flex-none bg-transparent border-0 py-0 pl-4 pr-1 text-gray-600 font-bold focus:ring-0 text-sm cursor-pointer hover:text-agro-dark outline-none h-full">
                                <option value="V" {{ old('tipo_doc', 'V') == 'V' ? 'selected' : '' }}>V</option>
                                <option value="E" {{ old('tipo_doc') == 'E' ? 'selected' : '' }}>E</option>
                                <option value="J" {{ old('tipo_doc') == 'J' ? 'selected' : '' }}>J</option>
                                <option value="G" {{ old('tipo_doc') == 'G' ? 'selected' : '' }}>G</option>
                            </select>
                            <div class="w-px bg-gray-200 my-2"></div>
                            <input type="text" 
                                   name="documento_identidad" 
                                   id="input_documento" 
                                   value="{{ old('documento_identidad') }}" 
                                   required
                                   class="w-full bg-transparent border-0 px-3 text-agro-dark font-bold focus:ring-0 text-sm placeholder:text-gray-400 outline-none h-full" 
                                   placeholder="12345678" 
                                   inputmode="numeric">
                        </div>
                        @error('documento_identidad') <p class="mt-1 text-xs text-red-500 font-bold ml-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Teléfono / WhatsApp --}}
                    <div class="group">
                        <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-primary">
                            WhatsApp / Teléfono <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex h-12 rounded-xl bg-gray-50 border @error('telefono') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus-within:border-primary focus-within:bg-white focus-within:ring-2 focus-within:ring-primary/20 transition-all duration-300 shadow-inner relative">
                            <input type="tel" 
                                   id="phone" 
                                   class="w-full bg-transparent border-0 px-3 text-agro-dark font-bold focus:ring-0 text-sm placeholder:text-gray-400 outline-none h-full rounded-xl" 
                                   placeholder="412 1234567"
                                   required>
                            <input type="hidden" name="telefono" id="hidden_telefono" value="{{ old('telefono') }}">
                            <input type="hidden" name="codigo_pais" id="hidden_codigo_pais" value="{{ old('codigo_pais', '+58') }}">
                        </div>
                        @error('telefono') <p class="mt-1 text-xs text-red-500 font-bold ml-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- FILA 3: EMAIL (Ocupa todo el ancho) --}}
                <div class="group">
                    <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-primary">
                        Correo Electrónico <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-gray-400 text-[18px] group-focus-within:text-primary transition-colors">mail</span>
                        </div>
                        <input type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required
                               class="w-full h-12 rounded-xl bg-gray-50 border @error('email') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-all duration-300 pl-10 pr-4 text-sm font-bold text-agro-dark placeholder:text-gray-400 shadow-inner" 
                               placeholder="usuario@correo.com">
                    </div>
                    @error('email') <p class="mt-1 text-xs text-red-500 font-bold ml-1">{{ $message }}</p> @enderror
                </div>

                {{-- FILA 4: CONTRASEÑAS --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Contraseña --}}
                    <div class="group">
                        <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-primary">
                            Contraseña <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <span class="material-symbols-outlined text-gray-400 text-[18px] group-focus-within:text-primary transition-colors">key</span>
                            </div>
                            <input type="password" 
                                   name="password" 
                                   required
                                   class="w-full h-12 rounded-xl bg-gray-50 border @error('password') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-all duration-300 pl-10 pr-4 text-sm font-bold text-agro-dark tracking-widest placeholder:text-gray-400 shadow-inner" 
                                   placeholder="••••••••">
                        </div>
                        @error('password') <p class="mt-1 text-xs text-red-500 font-bold ml-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Confirmar Contraseña --}}
                    <div class="group">
                        <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-primary">
                            Confirmar Contraseña <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <span class="material-symbols-outlined text-gray-400 text-[18px] group-focus-within:text-primary transition-colors">password</span>
                            </div>
                            <input type="password" 
                                   name="password_confirmation" 
                                   required
                                   class="w-full h-12 rounded-xl bg-gray-50 border border-gray-200 focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-all duration-300 pl-10 pr-4 text-sm font-bold text-agro-dark tracking-widest placeholder:text-gray-400 shadow-inner" 
                                   placeholder="••••••••">
                        </div>
                    </div>
                </div>

                {{-- Términos y Condiciones --}}
                <div class="pt-2 flex flex-col items-center justify-center">
                    <div class="flex items-center">
                        <input id="terms" name="terms" type="checkbox" required class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary focus:ring-offset-0 cursor-pointer shadow-sm" {{ old('terms') ? 'checked' : '' }}>
                        <label for="terms" class="ml-2 block text-xs font-bold text-gray-500 cursor-pointer select-none">
                            Acepto los <a href="#" class="text-primary hover:text-agro-dark transition-colors">términos y condiciones</a>.
                        </label>
                    </div>
                    @error('terms') <p class="text-[10px] text-red-500 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Botón Enviar --}}
                <button type="submit" class="btn-primary w-full h-12 mt-2 rounded-xl text-sm uppercase tracking-wider justify-center shadow-lg shadow-primary/25 group">
                    <span>Completar Registro</span>
                    <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">rocket_launch</span>
                </button>

            </form>

            {{-- Separador y Link a Login --}}
            <div class="relative mt-8 mb-6">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-white px-3 text-[10px] font-black text-gray-500 uppercase tracking-widest">
                        ¿Ya tienes una cuenta?
                    </span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('login') }}" class="btn-outline w-full h-12 rounded-xl text-sm font-bold justify-center flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">login</span>
                    <span>Iniciar Sesión</span>
                </a>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('password.request') }}" class="text-xs font-bold text-[#1e549e] hover:text-[#3492cf] hover:underline transition-colors inline-flex items-center gap-1">
                    <span class="material-symbols-outlined text-[15px]">lock_reset</span>
                    <span>¿Olvidaste tu contraseña? Recupérala aquí</span>
                </a>
            </div>

        </div>
        
        {{-- Footer --}}
        <p class="text-center mt-6 text-[11px] text-white/75 font-black uppercase tracking-[0.2em] drop-shadow-sm pb-6">
            {{ $configTienda->nombre_empresa ?? \App\Models\ConfiguracionTienda::nombre() }} &copy; {{ date('Y') }}
        </p>

    </div>
</div>
@endsection