@extends('layouts.app')

@section('title', 'Registro - Agropecuaria Venezuela')

@section('content')
<div class="min-h-[calc(100vh-80px)] flex items-center justify-center bg-gray-50/50 py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="w-full max-w-[680px] animate-fade-in-up">
        

        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/60 border border-gray-100 p-8 sm:p-12 relative overflow-hidden transition-all duration-500 ease-out" id="mainCard">
            
            <div class="flex items-end justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-black text-agro-dark tracking-tight leading-none">Crear Cuenta</h1>
                    <p class="text-sm text-gray-500 font-medium mt-2">Únete al ecosistema líder del agro.</p>
                </div>
                <a href="{{ route('login') }}" class="group flex items-center gap-2 text-xs font-bold text-gray-500 bg-gray-50 hover:bg-gray-100 px-4 py-2.5 rounded-xl transition-all duration-300">
                    <span>Iniciar Sesión</span>
                    <span class="material-symbols-outlined text-[16px] group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                </a>
            </div>

            <form method="POST" action="{{ route('register') }}" id="registerForm" class="space-y-6 relative z-10" novalidate>
                @csrf

                <div class="relative bg-gray-100/80 p-1.5 rounded-2xl border border-gray-200/50">
                    <div id="tabGlider" class="absolute top-1.5 left-1.5 h-[calc(100%-12px)] w-[calc(33.33%-4px)] bg-white rounded-xl shadow-sm ring-1 ring-black/5 transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] z-0"></div>

                    <div class="relative z-10 grid grid-cols-3 gap-1">
                        <label class="cursor-pointer text-center">
                            <input type="radio" name="tipo_cliente" value="natural" class="peer sr-only" {{ old('tipo_cliente', 'natural') == 'natural' ? 'checked' : '' }} onclick="window.moveGlider(0, 'natural')">
                            <div class="py-2.5 rounded-xl text-xs font-bold text-gray-500 transition-colors duration-300 peer-checked:text-agro-dark flex flex-col items-center gap-1">
                                <span class="material-symbols-outlined text-[20px]">person</span> Natural
                            </div>
                        </label>
                        <label class="cursor-pointer text-center">
                            <input type="radio" name="tipo_cliente" value="juridico" class="peer sr-only" {{ old('tipo_cliente') == 'juridico' ? 'checked' : '' }} onclick="window.moveGlider(1, 'juridico')">
                            <div class="py-2.5 rounded-xl text-xs font-bold text-gray-500 transition-colors duration-300 peer-checked:text-agro-dark flex flex-col items-center gap-1">
                                <span class="material-symbols-outlined text-[20px]">domain</span> Empresa
                            </div>
                        </label>
                        <label class="cursor-pointer text-center">
                            <input type="radio" name="tipo_cliente" value="finca_productor" class="peer sr-only" {{ old('tipo_cliente') == 'finca_productor' ? 'checked' : '' }} onclick="window.moveGlider(2, 'finca')">
                            <div class="py-2.5 rounded-xl text-xs font-bold text-gray-500 transition-colors duration-300 peer-checked:text-agro-dark flex flex-col items-center gap-1">
                                <span class="material-symbols-outlined text-[20px]">potted_plant</span> Productor
                            </div>
                        </label>
                    </div>
                </div>
                
                <input type="hidden" id="old_tipo_cliente" value="{{ old('tipo_cliente', 'natural') }}">

                <div class="space-y-5">
                    
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="w-full sm:w-[140px] flex-shrink-0 group">
                            <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-primary">Cédula</label>
                            <div class="flex rounded-xl bg-gray-50 border @error('documento_identidad') border-red-300 ring-2 ring-red-100 bg-red-50 @else border-transparent @enderror focus-within:border-primary focus-within:bg-white focus-within:ring-4 focus-within:ring-primary/10 transition-all duration-300" id="container_doc">
                                <select name="tipo_doc" class="flex-none bg-transparent border-0 py-3.5 pl-3 pr-1 text-gray-600 font-bold focus:ring-0 text-sm cursor-pointer hover:bg-gray-100 rounded-l-xl transition-colors outline-none">
                                    <option {{ old('tipo_doc') == 'V' ? 'selected' : '' }}>V</option>
                                    <option {{ old('tipo_doc') == 'E' ? 'selected' : '' }}>E</option>
                                    <option {{ old('tipo_doc') == 'J' ? 'selected' : '' }}>J</option>
                                    <option {{ old('tipo_doc') == 'G' ? 'selected' : '' }}>G</option>
                                </select>
                                <div class="w-px bg-gray-200 my-2.5"></div>
                                <input type="text" name="documento_identidad" id="input_documento" value="{{ old('documento_identidad') }}" class="w-full bg-transparent border-0 py-3.5 px-2 text-gray-900 placeholder:text-gray-400 focus:ring-0 text-sm font-semibold rounded-r-xl outline-none" placeholder="123456" inputmode="numeric">
                            </div>
                            @error('documento_identidad')
                                <p class="mt-1 text-xs text-red-500 font-bold ml-1 animate-pulse">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex-1 group transition-all duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]" id="field_nombre_container">
                            <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-primary" id="label_nombre">Nombre</label>
                            <input type="text" name="nombre" id="input_nombre" value="{{ old('nombre') }}" class="w-full rounded-xl bg-gray-50 border @error('nombre') border-red-300 ring-2 ring-red-100 bg-red-50 @else border-transparent @enderror focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 transition-all duration-300 py-3.5 text-sm font-semibold placeholder:text-gray-400 outline-none" placeholder="Ej: Juan">
                            @error('nombre')
                                <p class="mt-1 text-xs text-red-500 font-bold ml-1 animate-pulse">{{ $message }}</p>
                            @else
                                <p class="mt-1 text-xs text-red-500 font-bold ml-1 hidden" id="error_nombre">Nombre inválido</p>
                            @enderror
                        </div>

                        <div class="w-full sm:w-1/3 group transition-all duration-500 ease-[cubic-bezier(0.4,0,0.2,1)] overflow-hidden" id="field_apellido_container">
                            <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-primary">Apellido</label>
                            <input type="text" name="apellido" id="input_apellido" value="{{ old('apellido') }}" class="w-full rounded-xl bg-gray-50 border @error('apellido') border-red-300 ring-2 ring-red-100 bg-red-50 @else border-transparent @enderror focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 transition-all duration-300 py-3.5 text-sm font-semibold placeholder:text-gray-400 outline-none" placeholder="Ej: Pérez">
                            @error('apellido')
                                <p class="mt-1 text-xs text-red-500 font-bold ml-1 animate-pulse">{{ $message }}</p>
                            @else
                                <p class="mt-1 text-xs text-red-500 font-bold ml-1 hidden" id="error_apellido">Apellido inválido</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="group transition-all duration-300 hover:-translate-y-0.5">
                            <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-primary">Email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <span class="material-symbols-outlined text-gray-400 text-[20px] group-focus-within:text-primary transition-colors">mail</span>
                                </div>
                                <input type="email" name="email" id="input_email" value="{{ old('email') }}" class="w-full rounded-xl bg-gray-50 border @error('email') border-red-300 ring-2 ring-red-100 bg-red-50 @else border-transparent @enderror focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 transition-all duration-300 py-3.5 pl-11 text-sm font-semibold placeholder:text-gray-400 outline-none" placeholder="usuario@mail.com">
                            </div>
                            @error('email')
                                <p class="mt-1 text-xs text-red-500 font-bold ml-1 animate-pulse">{{ $message }}</p>
                            @else
                                <p class="mt-1 text-xs text-red-500 font-bold ml-1 hidden" id="error_email">Correo inválido</p>
                            @enderror
                        </div>

                        <div class="group transition-all duration-300 hover:-translate-y-0.5">
                            <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-primary">WhatsApp / Teléfono</label>
                            
                            <div class="flex rounded-xl bg-gray-50 border @error('telefono') border-red-300 ring-2 ring-red-100 bg-red-50 @else border-transparent @enderror focus-within:border-primary focus-within:bg-white focus-within:ring-4 focus-within:ring-primary/10 transition-all duration-300 relative">
                                <input type="tel" id="phone" class="w-full bg-transparent border-0 py-3.5 px-3 text-gray-900 placeholder:text-gray-400 focus:ring-0 text-sm font-semibold rounded-xl outline-none" placeholder="412 1234567">
                                <input type="hidden" name="telefono" id="hidden_telefono">
                                <input type="hidden" name="codigo_pais" id="hidden_codigo_pais">
                            </div>
                            @error('telefono')
                                <p class="mt-1 text-xs text-red-500 font-bold ml-1 animate-pulse">{{ $message }}</p>
                            @else
                                <p class="mt-1 text-xs text-red-500 font-bold ml-1 hidden" id="error_telefono">Número inválido</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="group transition-all duration-300 hover:-translate-y-0.5">
                            <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-primary">Contraseña</label>
                            <input type="password" name="password" id="input_password" class="w-full rounded-xl bg-gray-50 border @error('password') border-red-300 ring-2 ring-red-100 bg-red-50 @else border-transparent @enderror focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 transition-all duration-300 py-3.5 text-sm font-semibold placeholder:text-gray-400 tracking-widest outline-none" placeholder="••••••••">
                            @error('password')
                                <p class="mt-1 text-xs text-red-500 font-bold ml-1 animate-pulse">{{ $message }}</p>
                            @else
                                <p class="mt-1 text-xs text-red-500 font-bold ml-1 hidden" id="error_password">Mínimo 8 caracteres</p>
                            @enderror
                        </div>
                        <div class="group transition-all duration-300 hover:-translate-y-0.5">
                            <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-primary">Confirmar</label>
                            <input type="password" name="password_confirmation" id="input_confirm" class="w-full rounded-xl bg-gray-50 border-transparent focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 transition-all duration-300 py-3.5 text-sm font-semibold placeholder:text-gray-400 tracking-widest outline-none" placeholder="••••••••">
                            <p class="mt-1 text-xs text-red-500 font-bold ml-1 hidden" id="error_confirm">Las contraseñas no coinciden</p>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-50">
                    <button type="submit" id="btn_submit" class="w-full py-4 px-6 rounded-xl bg-primary text-agro-dark font-black text-sm uppercase tracking-wide hover:bg-primary/90 hover:shadow-lg hover:shadow-primary/30 hover:-translate-y-1 active:scale-[0.98] transition-all duration-300 flex items-center justify-center gap-2 group">
                        <span>Crear mi Cuenta</span>
                        <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">rocket_launch</span>
                    </button>
                    
                    <div class="mt-4 flex flex-col items-center justify-center gap-1">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="terms" id="terms" class="size-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer" @error('terms') checked @enderror>
                            <label for="terms" class="text-xs text-gray-400 cursor-pointer select-none">
                                Acepto los <a href="#" class="font-bold text-gray-600 hover:text-primary transition-colors">términos y condiciones</a>.
                            </label>
                        </div>
                        @error('terms')
                             <p class="mt-1 text-xs text-red-500 font-bold animate-pulse">Debes aceptar los términos.</p>
                        @enderror
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection