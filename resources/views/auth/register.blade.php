@extends('layouts.app')

@section('title', 'Crear Cuenta - Corpo Agrícola')

@section('content')
{{-- Usamos relative y overflow-hidden para que el fondo no afecte el footer de la página --}}
<div class="min-h-[calc(100vh-80px)] flex items-center justify-center relative overflow-hidden py-12 px-4 sm:px-6 lg:px-8">
    
    {{-- FONDO ABSOLUTO (Atrapado solo en este contenedor, igual que en el login) --}}
    <div class="absolute inset-0 z-0">
        {{-- Reemplaza la URL por tu asset local: src="{{ asset('img/photo-1500382017468-9049fed747ef.jpg') }}" --}}
        <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=2070&auto=format&fit=crop" 
             alt="Fondo de campo de trigo" 
             class="w-full h-full object-cover object-center">
        
        {{-- DEGRADADO OVERLAY --}}
        <div class="absolute inset-0 bg-gradient-to-br from-agro-dark/95 via-agro-dark/20 to-primary/40 backdrop-blur-[2px]"></div>
    </div>

    {{-- CONTENIDO PRINCIPAL --}}
    <div class="w-full max-w-[700px] relative z-10 animate-fade-in-up">

        {{-- ALERTA DE ERRORES --}}
        @if ($errors->any())
            <div class="mb-6 bg-red-50/95 backdrop-blur-md border-l-4 border-red-500 p-4 rounded-r-2xl shadow-lg animate-shake">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-red-500 text-[24px]">error</span>
                    <div>
                        <h3 class="text-sm font-black text-red-800 uppercase tracking-wider">Ups, algo salió mal</h3>
                        <p class="text-xs text-red-700 font-medium">Por favor revisa los campos marcados en rojo abajo.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- TARJETA EFECTO CRISTAL --}}
        <div class="bg-white/95 backdrop-blur-xl rounded-[2.5rem] shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)] border border-white/50 p-6 sm:p-10 relative overflow-hidden" id="mainCard">
            
            <div class="text-center mb-8">
                <h1 class="text-2xl sm:text-3xl font-black text-agro-dark tracking-tight">Crear Cuenta</h1>
                <p class="text-sm text-gray-500 font-medium mt-2">Únete al ecosistema líder del agro venezolano.</p>
            </div>

            <form method="POST" action="{{ route('register') }}" id="registerForm" class="space-y-5" novalidate>
                @csrf

                {{-- TABS: TIPO DE CLIENTE --}}
                <div class="relative bg-gray-100/80 p-1.5 rounded-2xl border border-gray-200/50 shadow-inner mb-6">
                    <div id="tabGlider" class="absolute top-1.5 left-1.5 h-[calc(100%-12px)] w-[calc(33.33%-4px)] bg-white rounded-xl shadow-sm ring-1 ring-black/5 transition-transform duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] z-0"></div>

                    <div class="relative z-10 grid grid-cols-3 gap-1">
                        <label class="cursor-pointer text-center group">
                            <input type="radio" name="tipo_cliente" value="natural" class="peer sr-only" {{ old('tipo_cliente', 'natural') == 'natural' ? 'checked' : '' }} onclick="window.moveGlider(0, 'natural')">
                            <div class="py-2.5 rounded-xl text-xs font-bold text-gray-500 transition-colors duration-300 peer-checked:text-primary flex flex-col items-center gap-1 group-hover:text-agro-dark">
                                <span class="material-symbols-outlined text-[20px]">person</span> Natural
                            </div>
                        </label>
                        <label class="cursor-pointer text-center group">
                            <input type="radio" name="tipo_cliente" value="juridico" class="peer sr-only" {{ old('tipo_cliente') == 'juridico' ? 'checked' : '' }} onclick="window.moveGlider(1, 'juridico')">
                            <div class="py-2.5 rounded-xl text-xs font-bold text-gray-500 transition-colors duration-300 peer-checked:text-primary flex flex-col items-center gap-1 group-hover:text-agro-dark">
                                <span class="material-symbols-outlined text-[20px]">domain</span> Empresa
                            </div>
                        </label>
                        <label class="cursor-pointer text-center group">
                            <input type="radio" name="tipo_cliente" value="finca_productor" class="peer sr-only" {{ old('tipo_cliente') == 'finca_productor' ? 'checked' : '' }} onclick="window.moveGlider(2, 'finca')">
                            <div class="py-2.5 rounded-xl text-xs font-bold text-gray-500 transition-colors duration-300 peer-checked:text-primary flex flex-col items-center gap-1 group-hover:text-agro-dark">
                                <span class="material-symbols-outlined text-[20px]">agriculture</span> Productor
                            </div>
                        </label>
                    </div>
                </div>
                
                <input type="hidden" id="old_tipo_cliente" value="{{ old('tipo_cliente', 'natural') }}">

                {{-- FILA 1: NOMBRE Y APELLIDO --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5" id="row_nombres">
    {{-- Nombre --}}
    <div class="group" id="field_nombre_container" style="width: 100%;">
        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-primary" id="label_nombre">Nombre</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <span class="material-symbols-outlined text-gray-400 text-[18px] group-focus-within:text-primary transition-colors">badge</span>
            </div>
            <input type="text" name="nombre" id="input_nombre" value="{{ old('nombre') }}" 
                   class="w-full h-12 rounded-xl bg-gray-50 border @error('nombre') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-all duration-300 pl-10 pr-4 text-sm font-bold text-agro-dark placeholder:text-gray-400 shadow-inner" placeholder="Ej: Juan" style="width: 100%;">
        </div>
        @error('nombre') <p class="mt-1 text-xs text-red-500 font-bold ml-1">{{ $message }}</p> @enderror
    </div>

    {{-- Apellido --}}
    <div class="group" id="field_apellido_container" style="width: 100%;">
        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-primary">Apellido</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <span class="material-symbols-outlined text-gray-400 text-[18px] group-focus-within:text-primary transition-colors">badge</span>
            </div>
            <input type="text" name="apellido" id="input_apellido" value="{{ old('apellido') }}" 
                   class="w-full h-12 rounded-xl bg-gray-50 border @error('apellido') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-all duration-300 pl-10 pr-4 text-sm font-bold text-agro-dark placeholder:text-gray-400 shadow-inner" placeholder="Ej: Pérez" style="width: 100%;">
        </div>
        @error('apellido') <p class="mt-1 text-xs text-red-500 font-bold ml-1">{{ $message }}</p> @enderror
    </div>
</div>

                {{-- FILA 2: CÉDULA Y TELÉFONO --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Cédula / Documento --}}
                    <div class="group">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-primary" id="label_documento">Documento</label>
                        <div class="flex h-12 rounded-xl bg-gray-50 border @error('documento_identidad') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus-within:border-primary focus-within:bg-white focus-within:ring-2 focus-within:ring-primary/20 transition-all duration-300 shadow-inner">
                            <select name="tipo_doc" class="flex-none bg-transparent border-0 py-0 pl-4 pr-1 text-gray-500 font-bold focus:ring-0 text-sm cursor-pointer hover:text-agro-dark outline-none h-full">
                                <option {{ old('tipo_doc') == 'V' ? 'selected' : '' }}>V</option>
                                <option {{ old('tipo_doc') == 'E' ? 'selected' : '' }}>E</option>
                                <option {{ old('tipo_doc') == 'J' ? 'selected' : '' }}>J</option>
                                <option {{ old('tipo_doc') == 'G' ? 'selected' : '' }}>G</option>
                            </select>
                            <div class="w-px bg-gray-200 my-2"></div>
                            <input type="text" name="documento_identidad" id="input_documento" value="{{ old('documento_identidad') }}" 
                                   class="w-full bg-transparent border-0 px-3 text-agro-dark font-bold focus:ring-0 text-sm placeholder:text-gray-400 outline-none h-full" placeholder="12345678" inputmode="numeric">
                        </div>
                        @error('documento_identidad') <p class="mt-1 text-xs text-red-500 font-bold ml-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Teléfono --}}
                    <div class="group">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-primary">WhatsApp / Teléfono</label>
                        <div class="flex h-12 rounded-xl bg-gray-50 border @error('telefono') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus-within:border-primary focus-within:bg-white focus-within:ring-2 focus-within:ring-primary/20 transition-all duration-300 shadow-inner relative">
                            <input type="tel" id="phone" class="w-full bg-transparent border-0 px-3 text-agro-dark font-bold focus:ring-0 text-sm placeholder:text-gray-400 outline-none h-full rounded-xl" placeholder="412 1234567">
                            <input type="hidden" name="telefono" id="hidden_telefono">
                            <input type="hidden" name="codigo_pais" id="hidden_codigo_pais">
                        </div>
                        @error('telefono') <p class="mt-1 text-xs text-red-500 font-bold ml-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- FILA 3: EMAIL (Ocupa todo el ancho) --}}
                <div class="group">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-primary">Correo Electrónico</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-gray-400 text-[18px] group-focus-within:text-primary transition-colors">mail</span>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" 
                               class="w-full h-12 rounded-xl bg-gray-50 border @error('email') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-all duration-300 pl-10 pr-4 text-sm font-bold text-agro-dark placeholder:text-gray-400 shadow-inner" placeholder="usuario@correo.com">
                    </div>
                    @error('email') <p class="mt-1 text-xs text-red-500 font-bold ml-1">{{ $message }}</p> @enderror
                </div>

                {{-- FILA 4: CONTRASEÑAS --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Contraseña --}}
                    <div class="group">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-primary">Contraseña</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <span class="material-symbols-outlined text-gray-400 text-[18px] group-focus-within:text-primary transition-colors">key</span>
                            </div>
                            <input type="password" name="password" 
                                   class="w-full h-12 rounded-xl bg-gray-50 border @error('password') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-all duration-300 pl-10 pr-4 text-sm font-bold text-agro-dark tracking-widest placeholder:text-gray-400 shadow-inner" placeholder="••••••••">
                        </div>
                        @error('password') <p class="mt-1 text-xs text-red-500 font-bold ml-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Confirmar Contraseña --}}
                    <div class="group">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-primary">Confirmar Contraseña</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <span class="material-symbols-outlined text-gray-400 text-[18px] group-focus-within:text-primary transition-colors">password</span>
                            </div>
                            <input type="password" name="password_confirmation" 
                                   class="w-full h-12 rounded-xl bg-gray-50 border border-gray-200 focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-all duration-300 pl-10 pr-4 text-sm font-bold text-agro-dark tracking-widest placeholder:text-gray-400 shadow-inner" placeholder="••••••••">
                        </div>
                    </div>
                </div>

                {{-- Términos y Condiciones --}}
                <div class="pt-4 flex flex-col items-center justify-center">
                    <div class="flex items-center">
                        <input id="terms" name="terms" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary focus:ring-offset-0 cursor-pointer shadow-sm" @error('terms') checked @enderror>
                        <label for="terms" class="ml-2 block text-xs font-bold text-gray-500 cursor-pointer select-none">
                            Acepto los <a href="#" class="text-primary hover:text-agro-dark transition-colors">términos y condiciones</a>.
                        </label>
                    </div>
                    @error('terms') <p class="text-[10px] text-red-500 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Botón Enviar --}}
                <button type="submit" class="w-full h-12 mt-2 rounded-xl bg-primary text-agro-dark font-black text-sm uppercase tracking-wide hover:bg-green-500 hover:text-white shadow-lg shadow-primary/30 transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-2 group">
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
                    <span class="bg-white px-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        ¿Ya eres parte de nosotros?
                    </span>
                </div>
            </div>

            <a href="{{ route('login') }}" class="w-full h-12 rounded-xl border-2 border-gray-100 text-gray-500 font-bold text-sm flex items-center justify-center hover:border-primary hover:text-primary hover:bg-primary/5 transition-all duration-300">
                Iniciar Sesión
            </a>

        </div>
        
        {{-- Footer - Este texto se queda fuera del glass y dentro del contenedor base --}}
        <p class="text-center mt-6 text-[10px] text-white/60 font-black uppercase tracking-[0.2em] drop-shadow-sm pb-6">
            Corpo Agrícola &copy; {{ date('Y') }}
        </p>

    </div>
</div>

<script>
    // Lógica para el Slider de Tipo de Cliente y Ajuste de "Razón Social"
    window.moveGlider = function(index, type) {
        const glider = document.getElementById('tabGlider');
        if(glider) {
            glider.style.transform = `translateX(${index * 100}%)`;
        }
        
        const rowNombres = document.getElementById('row_nombres');
        const apellidoCont = document.getElementById('field_apellido_container');
        const labelNombre = document.getElementById('label_nombre');
        const labelDoc = document.getElementById('label_documento');
        
        if(type === 'juridico') {
            // Ocultar Apellido
            if(apellidoCont) {
                apellidoCont.style.display = 'none';
            }
            if(labelNombre) labelNombre.innerText = 'Razón Social';
            if(labelDoc) labelDoc.innerText = 'RIF';
        } else {
            // Mostrar Apellido
            if(apellidoCont) {
                apellidoCont.style.display = 'block';
            }
            if(labelNombre) labelNombre.innerText = 'Nombre';
            if(labelDoc) labelDoc.innerText = 'Cédula / Documento';
        }
    };
    
    document.addEventListener('DOMContentLoaded', () => {
        const oldType = document.getElementById('old_tipo_cliente').value;
        if(oldType === 'juridico') window.moveGlider(1, 'juridico');
        else if(oldType === 'finca_productor') window.moveGlider(2, 'finca');
        else window.moveGlider(0, 'natural');
    });
</script>
@endsection