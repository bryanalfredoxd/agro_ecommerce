@extends('layouts.app')

@section('title', 'Mi Perfil - Agropecuaria Venezuela')

@section('styles')
    <style>
        /* Ajuste para que el mapa se vea bien */
        #map-canvas { z-index: 1; width: 100%; height: 100%; }
        .leaflet-container { font-family: inherit; }
        .modal-scroll { -webkit-overflow-scrolling: touch; }
        
        /* Animaciones personalizadas */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
        @keyframes pulseSlow { 0%, 100% { opacity: 0.3; transform: scale(1); } 50% { opacity: 0.15; transform: scale(1.1); } }
        .animate-pulse-slow { animation: pulseSlow 8s infinite ease-in-out; }
    </style>
@endsection

@section('content')
<div class="bg-gray-50 min-h-screen font-sans pb-20 relative">

    {{-- Encabezado decorativo --}}
    <div class="relative bg-agro-dark h-40 lg:h-48 overflow-hidden transition-all">
        <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute -bottom-10 -right-10 w-40 h-40 lg:w-64 lg:h-64 rounded-full bg-primary/20 blur-3xl animate-pulse-slow"></div>
    </div>

    {{-- Contenedor Principal --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 -mt-16 lg:-mt-24 relative z-10">
        
        <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">

            {{-- ASIDE: Menú Lateral --}}
            <aside class="w-full lg:w-80 flex-shrink-0">
                <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden lg:sticky lg:top-6 transition-all">
                    
                    {{-- Perfil Header --}}
                    <div class="p-6 lg:p-8 text-center bg-gradient-to-b from-white to-gray-50">
                        <div class="w-20 h-20 lg:w-28 lg:h-28 mx-auto bg-primary/10 text-primary rounded-full flex items-center justify-center text-3xl lg:text-4xl font-black mb-3 lg:mb-4 border-4 border-white shadow-sm transition-all">
                            {{ substr(auth()->user()->nombre, 0, 1) }}{{ substr(auth()->user()->apellido, 0, 1) }}
                        </div>
                        
                        <h2 class="text-xl lg:text-2xl font-black text-agro-dark mb-1 truncate">
                            {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}
                        </h2>
                        <p class="text-xs lg:text-sm text-gray-500 font-medium mb-4 truncate">{{ auth()->user()->email }}</p>
                        
                        <div class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-primary/10 text-primary text-[10px] lg:text-xs font-bold uppercase tracking-wider">
                            @if(auth()->user()->tipo_cliente === 'juridico')
                                <span class="material-symbols-outlined text-sm">domain</span> Empresa
                            @elseif(auth()->user()->tipo_cliente === 'finca_productor')
                                <span class="material-symbols-outlined text-sm">agriculture</span> Productor
                            @else
                                <span class="material-symbols-outlined text-sm">person</span> Particular
                            @endif
                        </div>
                    </div>

                    {{-- Menú de Navegación --}}
                    <div class="border-t border-gray-100 bg-white">
                    <nav class="flex flex-col p-2">
                        <a href="{{ route('perfil') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-primary transition-all font-medium group">
                            <span class="material-symbols-outlined text-xl">person</span>
                            Mi Perfil
                        </a>
                        
                        <a href="{{ route('perfil') }}#direcciones" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-primary transition-all font-medium group">
                            <span class="material-symbols-outlined text-xl">location_on</span>
                            Direcciones
                        </a>

                        <a href="{{ route('perfil') }}#seguridad" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-primary transition-all font-medium group">
                            <span class="material-symbols-outlined text-xl">lock</span>
                            Seguridad
                        </a>

                        {{-- MODIFICADO: Ahora es un link normal, sin selección verde fija --}}
                        <a href="{{ route('perfil.pedidos') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-primary transition-all font-medium group">
                            <span class="material-symbols-outlined text-xl">receipt_long</span>
                            Mis Pedidos
                        </a>
                        
                        <div class="h-px bg-gray-100 my-2"></div>
                        
                        <a href="{{ route('catalogo') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-primary transition-all font-medium group">
                            <span class="material-symbols-outlined text-xl">store</span>
                            Ir al Catálogo
                        </a>
                    </nav>
                    </div>
                </div>
            </aside>

            {{-- MAIN: Contenido --}}
            <main class="flex-1 space-y-6 lg:space-y-8 min-w-0">

                {{-- SECCIÓN 1: DATOS PERSONALES --}}
                <div id="datos-personales" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-5 lg:p-8 animate-fade-in-up">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-2">
                        <h3 class="text-lg lg:text-xl font-black text-agro-dark flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-2xl">feed</span>
                            Información Personal
                        </h3>
                        <span class="self-start sm:self-auto bg-green-100 text-green-700 text-[10px] font-bold px-2 py-1 rounded-lg uppercase tracking-wider flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">verified_user</span> Verificado
                        </span>
                    </div>

                    <form action="{{ route('perfil.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
                            
                            @php
                                $labelNombre = auth()->user()->tipo_cliente === 'juridico' ? 'Razón Social' : 'Nombre';
                                $labelDoc = auth()->user()->tipo_cliente === 'juridico' ? 'RIF' : 'Cédula / Documento';
                            @endphp

                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ $labelNombre }}</label>
                                <input type="text" name="nombre" value="{{ old('nombre', auth()->user()->nombre) }}" 
                                       class="w-full rounded-xl px-4 py-3 text-sm lg:text-base font-medium transition-all focus:ring-4 focus:ring-primary/10 
                                       @error('nombre') border-red-500 bg-red-50 text-red-900 focus:border-red-500 @else border-gray-200 bg-gray-50 text-gray-700 focus:bg-white focus:border-primary @enderror">
                                @error('nombre') <span class="text-xs text-red-500 font-bold flex items-center gap-1 mt-1"><span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}</span> @enderror
                            </div>

                            @if(auth()->user()->tipo_cliente !== 'juridico')
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Apellido</label>
                                    <input type="text" name="apellido" value="{{ old('apellido', auth()->user()->apellido) }}" 
                                           class="w-full rounded-xl px-4 py-3 text-sm lg:text-base font-medium transition-all focus:ring-4 focus:ring-primary/10 
                                           @error('apellido') border-red-500 bg-red-50 text-red-900 focus:border-red-500 @else border-gray-200 bg-gray-50 text-gray-700 focus:bg-white focus:border-primary @enderror">
                                    @error('apellido') <span class="text-xs text-red-500 font-bold flex items-center gap-1 mt-1"><span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}</span> @enderror
                                </div>
                            @endif

                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ $labelDoc }}</label>
                                <div class="relative">
                                    <input type="text" value="{{ auth()->user()->documento_identidad }}" disabled
                                           class="w-full bg-gray-100 border border-gray-200 rounded-xl px-4 py-3 text-sm lg:text-base font-medium text-gray-500 cursor-not-allowed opacity-70">
                                    <span class="material-symbols-outlined absolute right-3 top-3 text-gray-400 text-lg">lock</span>
                                </div>
                                <p class="text-[10px] text-gray-400 flex items-center gap-1 mt-1">
                                    <span class="material-symbols-outlined text-[12px]">info</span>
                                    Dato inmutable por seguridad.
                                </p>
                            </div>

                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Teléfono</label>
                                <input type="tel" name="telefono" value="{{ old('telefono', auth()->user()->telefono) }}" id="phone"
                                       class="w-full rounded-xl px-4 py-3 text-sm lg:text-base font-medium transition-all focus:ring-4 focus:ring-primary/10 
                                       @error('telefono') border-red-500 bg-red-50 text-red-900 focus:border-red-500 @else border-gray-200 bg-gray-50 text-gray-700 focus:bg-white focus:border-primary @enderror">
                                @error('telefono') <span class="text-xs text-red-500 font-bold flex items-center gap-1 mt-1"><span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1 md:col-span-2">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Correo Electrónico</label>
                                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" 
                                       class="w-full rounded-xl px-4 py-3 text-sm lg:text-base font-medium transition-all focus:ring-4 focus:ring-primary/10 
                                       @error('email') border-red-500 bg-red-50 text-red-900 focus:border-red-500 @else border-gray-200 bg-gray-50 text-gray-700 focus:bg-white focus:border-primary @enderror">
                                @error('email') <span class="text-xs text-red-500 font-bold flex items-center gap-1 mt-1"><span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- ZONA DE SEGURIDAD --}}
                        <div class="mt-8 bg-orange-50/50 border border-orange-100 rounded-2xl p-4 lg:p-5">
                            <div class="flex flex-col lg:flex-row items-center gap-4">
                                <div class="w-full flex-1">
                                    <label class="text-xs font-black text-orange-800 uppercase tracking-wide mb-1 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">lock_person</span>
                                        Contraseña Actual
                                    </label>
                                    <p class="text-[11px] text-orange-600/80 mb-2 leading-tight">
                                        Para evitar robos de cuenta, ingresa tu contraseña para autorizar cambios.
                                    </p>
                                    <input type="password" name="password_actual_auth" placeholder="Ingresa tu contraseña aquí..." autocomplete="new-password"
                                           class="w-full bg-white border border-orange-200 rounded-xl px-4 py-2.5 text-sm lg:text-base font-medium text-gray-700 focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all placeholder:text-gray-300">
                                    @error('password_actual_auth') 
                                        <p class="text-xs text-red-500 font-bold mt-1 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-sm">error</span> {{ $message }}
                                        </p> 
                                    @enderror
                                </div>
                                
                                <button type="submit" class="w-full lg:w-auto whitespace-nowrap px-6 py-3.5 bg-agro-dark text-white font-bold rounded-xl hover:bg-primary shadow-lg shadow-agro-dark/20 hover:shadow-primary/30 transition-all transform hover:-translate-y-1 flex items-center justify-center gap-2 text-sm lg:text-base">
                                    <span class="material-symbols-outlined">save</span>
                                    Autorizar y Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- SECCIÓN 2: DIRECCIONES --}}
                <div id="direcciones" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-5 lg:p-8 animate-fade-in-up" style="animation-delay: 100ms;">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
                        <h3 class="text-lg lg:text-xl font-black text-agro-dark flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-2xl">map</span>
                            Mis Direcciones
                        </h3>
                        <div class="flex gap-2 w-full sm:w-auto">
                            @if(auth()->user()->direcciones->count() > 4)
                                <button onclick="toggleModalGestionar()" class="flex-1 sm:flex-none justify-center text-sm font-bold text-gray-500 hover:text-agro-dark flex items-center gap-1 bg-gray-100 px-3 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                                    <span class="material-symbols-outlined text-lg">list</span>
                                    Ver todas
                                </button>
                            @endif
                            <button onclick="toggleModalDireccion()" class="flex-1 sm:flex-none justify-center text-sm font-bold text-primary hover:text-green-700 flex items-center gap-1 bg-primary/5 px-3 py-2 rounded-lg hover:bg-primary/10 transition-colors">
                                <span class="material-symbols-outlined text-lg">add_location</span>
                                Nueva
                            </button>
                        </div>
                    </div>

                    @if(auth()->user()->direcciones && auth()->user()->direcciones->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach(auth()->user()->direcciones->sortByDesc('es_principal')->take(4) as $direccion)
                                <div class="relative group border {{ $direccion->es_principal ? 'border-primary bg-green-50/30' : 'border-gray-200 bg-white' }} rounded-2xl p-4 lg:p-5 hover:shadow-md transition-all">
                                    
                                    @if($direccion->es_principal)
                                        <span class="absolute top-0 right-0 bg-primary text-white text-[10px] font-bold px-2 py-1 rounded-bl-xl rounded-tr-xl shadow-sm">
                                            PRINCIPAL
                                        </span>
                                    @endif

                                    <div class="flex items-start gap-3">
                                        <div class="bg-white p-2 rounded-full shadow-sm text-gray-400 group-hover:text-primary transition-colors flex-shrink-0">
                                            <span class="material-symbols-outlined">home_pin</span>
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="font-bold text-agro-dark text-sm mb-1 truncate">{{ $direccion->alias ?? 'Ubicación' }}</h4>
                                            <p class="text-xs text-gray-500 leading-relaxed mb-2 line-clamp-2">{{ $direccion->direccion_texto }}</p>
                                            @if($direccion->referencia_punto)
                                                <p class="text-[10px] text-gray-400 italic truncate">Ref: {{ $direccion->referencia_punto }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-2 mt-4 pt-3 border-t {{ $direccion->es_principal ? 'border-primary/20' : 'border-gray-100' }}">
                                        @if(!$direccion->es_principal)
                                            <form action="{{ route('direccion.principal', $direccion->id) }}" method="POST" class="flex-1 min-w-[100px]">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="text-[10px] lg:text-xs font-bold text-primary hover:underline w-full text-left">
                                                    Hacer principal
                                                </button>
                                            </form>
                                        @else
                                            <span class="flex-1 text-[10px] lg:text-xs font-bold text-gray-400">Dirección por defecto</span>
                                        @endif
                                        
                                        @if(!$direccion->es_principal)
                                            <form id="delete-form-{{ $direccion->id }}" action="{{ route('direccion.destroy', $direccion->id) }}" method="POST" class="flex-shrink-0">
                                                @csrf @method('DELETE')
                                                <button type="button" onclick="openDeleteModal({{ $direccion->id }})" class="text-[10px] lg:text-xs font-bold text-red-400 hover:text-red-600">Eliminar</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-gray-50 rounded-2xl border border-dashed border-gray-300">
                            <span class="material-symbols-outlined text-4xl text-gray-300 mb-2">wrong_location</span>
                            <p class="text-gray-500 font-medium text-sm">No tienes direcciones registradas.</p>
                        </div>
                    @endif
                </div>

                {{-- SECCIÓN 3: SEGURIDAD (PASS) --}}
                <div id="seguridad" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-5 lg:p-8 animate-fade-in-up" style="animation-delay: 200ms;">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg lg:text-xl font-black text-agro-dark flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-2xl">security</span>
                            Cambiar Contraseña
                        </h3>
                    </div>
                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Contraseña Actual</label>
                                <input type="password" name="current_password" 
                                       class="w-full rounded-xl px-4 py-3 text-sm lg:text-base font-medium transition-all focus:ring-4 focus:ring-primary/10 
                                       @error('current_password') border-red-500 bg-red-50 text-red-900 focus:border-red-500 @else border-gray-200 bg-gray-50 text-gray-700 focus:bg-white focus:border-primary @enderror">
                                @error('current_password') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Nueva</label>
                                <input type="password" name="password" 
                                       class="w-full rounded-xl px-4 py-3 text-sm lg:text-base font-medium transition-all focus:ring-4 focus:ring-primary/10 
                                       @error('password') border-red-500 bg-red-50 text-red-900 focus:border-red-500 @else border-gray-200 bg-gray-50 text-gray-700 focus:bg-white focus:border-primary @enderror">
                                @error('password') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Confirmar</label>
                                <input type="password" name="password_confirmation" 
                                       class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm lg:text-base font-medium text-gray-700 focus:bg-white focus:border-primary">
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="w-full lg:w-auto px-6 py-3 bg-gray-800 text-white font-bold rounded-xl hover:bg-black shadow-lg shadow-gray-400/20 transition-all text-sm lg:text-base">
                                Actualizar Contraseña
                            </button>
                        </div>
                    </form>
                </div>

            </main>
        </div>
    </div>
</div>

{{-- MODAL NUEVA DIRECCIÓN --}}
<div id="modal-direccion" class="fixed inset-0 z-50 hidden" style="z-index: 100;">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="toggleModalDireccion()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center sm:items-center p-0 sm:p-4">
            <div class="relative transform overflow-hidden rounded-t-3xl sm:rounded-3xl bg-white text-left shadow-2xl transition-all w-full sm:max-w-2xl border border-gray-100 modal-scroll h-[90vh] sm:h-auto flex flex-col">
                
                <div class="bg-gradient-to-r from-agro-dark to-primary/90 px-6 py-4 flex justify-between items-center flex-shrink-0">
                    <h3 class="text-lg font-black leading-6 text-white flex items-center gap-2">
                        <span class="material-symbols-outlined">add_location_alt</span>
                        Nueva Dirección
                    </h3>
                    <button type="button" onclick="toggleModalDireccion()" class="text-white/80 hover:text-white transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form action="{{ route('direccion.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden">
                    @csrf
                    <input type="hidden" id="input_lat" name="geo_latitud">
                    <input type="hidden" id="input_lng" name="geo_longitud">

                    <div class="px-6 py-6 space-y-4 overflow-y-auto flex-1">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Alias</label>
                                <input type="text" name="alias" required placeholder="Ej: Casa, Oficina..."
                                       class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Referencia</label>
                                <input type="text" name="referencia_punto" placeholder="Ej: Portón azul..."
                                       class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Dirección Escrita</label>
                            <textarea name="direccion_texto" rows="2" required placeholder="Calle, Avenida..."
                                      class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all resize-none"></textarea>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide flex justify-between">
                                <span>Ubicación Exacta</span>
                                <span class="text-primary cursor-pointer hover:underline flex items-center gap-1" onclick="getCurrentLocation()">
                                    <span class="material-symbols-outlined text-[14px]">my_location</span> Usar GPS
                                </span>
                            </label>
                            <div class="w-full h-56 lg:h-72 rounded-xl border-2 border-gray-200 overflow-hidden relative z-0">
                                <div id="map-canvas" class="w-full h-full"></div>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1">Mueve el pin azul a tu puerta.</p>
                        </div>

                        <div class="relative flex items-start p-4 bg-gray-50 rounded-xl border border-gray-200 cursor-pointer hover:bg-white hover:border-primary/50 transition-all group" 
                             onclick="toggleCheckbox(this)">
                            <div class="flex h-6 items-center">
                                <input id="check_principal" name="es_principal" value="1" type="checkbox" 
                                       class="h-5 w-5 rounded border-gray-300 text-primary focus:ring-primary pointer-events-none">
                            </div>
                            <div class="ml-3 text-sm leading-6">
                                <label for="check_principal" class="font-bold text-gray-900 cursor-pointer select-none group-hover:text-primary">
                                    Dirección Principal
                                </label>
                                <p class="text-gray-500 text-xs select-none">Usar por defecto para envíos.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex flex-col-reverse sm:flex-row-reverse gap-2 flex-shrink-0">
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-xl bg-agro-dark px-6 py-3 text-sm font-bold text-white shadow-sm hover:bg-primary transition-all">
                            <span class="material-symbols-outlined text-lg">save</span> Guardar
                        </button>
                        <button type="button" onclick="toggleModalDireccion()" class="w-full sm:w-auto inline-flex justify-center rounded-xl bg-white px-6 py-3 text-sm font-bold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-all">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- MODAL GESTIONAR TODAS LAS DIRECCIONES --}}
<div id="modal-gestionar-direcciones" class="fixed inset-0 z-50 hidden" style="z-index: 100;">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="toggleModalGestionar()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-0 sm:p-4 text-center sm:items-center">
            <div class="relative transform overflow-hidden rounded-t-3xl sm:rounded-3xl bg-white text-left shadow-2xl transition-all w-full sm:max-w-lg border border-gray-100 max-h-[85vh] flex flex-col">
                
                <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
                    <h3 class="text-lg font-black text-agro-dark">Gestionar Direcciones</h3>
                    <button type="button" onclick="toggleModalGestionar()" class="text-gray-400 hover:text-gray-600">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="px-6 py-6 overflow-y-auto space-y-4 flex-1">
                    @foreach(auth()->user()->direcciones->sortByDesc('es_principal') as $direccion)
                        <div class="flex items-center justify-between p-4 border rounded-xl {{ $direccion->es_principal ? 'border-primary bg-green-50/30' : 'border-gray-200' }}">
                            <div class="flex items-center gap-3 overflow-hidden">
                                <div class="text-gray-400 flex-shrink-0">
                                    <span class="material-symbols-outlined">home_pin</span>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-bold text-sm text-gray-800 truncate">{{ $direccion->alias }}</h4>
                                    <p class="text-xs text-gray-500 truncate">{{ $direccion->direccion_texto }}</p>
                                </div>
                            </div>
                            <div class="flex-shrink-0 ml-2">
                                @if($direccion->es_principal)
                                    <span class="text-xs font-bold text-primary bg-primary/10 px-2 py-1 rounded">Principal</span>
                                @else
                                    <div class="flex gap-1">
                                        <form action="{{ route('direccion.principal', $direccion->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-xs font-bold text-gray-500 hover:text-primary border border-gray-300 hover:border-primary px-3 py-1.5 rounded-lg transition-all">
                                                Usar
                                            </button>
                                        </form>
                                        
                                        <form id="delete-form-modal-{{ $direccion->id }}" action="{{ route('direccion.destroy', $direccion->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="openDeleteModal({{ $direccion->id }}, true)" class="text-xs font-bold text-red-400 hover:text-red-600 border border-gray-200 hover:border-red-200 px-2 py-1.5 rounded-lg transition-all">
                                                <span class="material-symbols-outlined text-sm">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DE CONFIRMACIÓN (ELIMINAR) - ESTILO TAILWIND --}}
<div id="delete-modal" class="fixed inset-0 z-[150] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="delete-modal-backdrop"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" id="delete-modal-panel">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-symbols-outlined text-red-600">warning</span>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-bold leading-6 text-gray-900">¿Eliminar dirección?</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">¿Estás seguro de que deseas eliminar esta dirección? Esta acción no se puede deshacer.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" onclick="confirmDelete()" class="inline-flex w-full justify-center rounded-xl bg-red-600 px-3 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors">
                        Sí, eliminar
                    </button>
                    <button type="button" onclick="closeDeleteModal()" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-3 py-2.5 text-sm font-bold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- TOAST NOTIFICATION --}}
<div id="toast-notification" class="fixed bottom-6 right-6 z-[200] transform transition-all duration-500 translate-y-24 opacity-0 pointer-events-none">
    <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-gray-100 p-4 flex items-center gap-4 min-w-[320px] max-w-md">
        <div id="toast-icon-container" class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 transition-colors">
            <span id="toast-icon" class="material-symbols-outlined text-2xl">check_circle</span>
        </div>
        <div class="flex-1">
            <h4 id="toast-title" class="font-bold text-gray-900 text-sm">Notificación</h4>
            <p id="toast-message" class="text-xs font-medium text-gray-500 mt-0.5">Mensaje...</p>
        </div>
        <button onclick="hideToast()" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100">
            <span class="material-symbols-outlined text-xl">close</span>
        </button>
    </div>
</div>

<script>
    // --- VARIABLES MAPA ---
    let map;
    let marker;
    const defaultLat = 8.6226; 
    const defaultLng = -70.2075;
    
    // --- VARIABLES PARA ELIMINAR ---
    let deleteTargetId = null;
    let isFromModal = false;

    document.addEventListener("DOMContentLoaded", () => {
        // Inicializar Teléfono
        const inputPhone = document.querySelector("#phone");
        if (inputPhone && window.intlTelInput) {
            window.intlTelInput(inputPhone, {
                initialCountry: "ve",
                separateDialCode: true,
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.6.0/build/js/utils.js",
            });
        }

        // MOSTRAR TOASTS DESDE SESIÓN (ÉXITO O ERROR)
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if($errors->any())
            showToast("Hay errores en el formulario. Verifica los campos.", 'error');
        @endif
    });

    // --- SISTEMA DE TOASTS ---
    let toastTimeout;
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast-notification');
        const iconContainer = document.getElementById('toast-icon-container');
        const icon = document.getElementById('toast-icon');
        const title = document.getElementById('toast-title');
        const msg = document.getElementById('toast-message');

        iconContainer.className = 'w-12 h-12 rounded-xl flex items-center justify-center shrink-0 transition-colors';
        
        if (type === 'success') {
            iconContainer.classList.add('bg-green-50', 'text-green-600');
            icon.innerText = 'check_circle';
            title.innerText = '¡Éxito!';
            title.className = 'font-bold text-green-700 text-sm';
        } else {
            iconContainer.classList.add('bg-red-50', 'text-red-500');
            icon.innerText = 'error';
            title.innerText = 'Error';
            title.className = 'font-bold text-red-700 text-sm';
        }

        msg.innerText = message;
        toast.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');

        if (toastTimeout) clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => hideToast(), 4000);
    }

    function hideToast() {
        document.getElementById('toast-notification').classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
    }

    // --- SISTEMA DE MODAL ELIMINAR ---
    function openDeleteModal(id, fromModal = false) {
        deleteTargetId = id;
        isFromModal = fromModal;
        const modal = document.getElementById('delete-modal');
        const backdrop = document.getElementById('delete-modal-backdrop');
        const panel = document.getElementById('delete-modal-panel');

        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
        }, 10);
    }

    function closeDeleteModal() {
        deleteTargetId = null;
        const modal = document.getElementById('delete-modal');
        const backdrop = document.getElementById('delete-modal-backdrop');
        const panel = document.getElementById('delete-modal-panel');

        backdrop.classList.add('opacity-0');
        panel.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function confirmDelete() {
        if (!deleteTargetId) return;
        
        // Determinar qué formulario enviar (desde la lista principal o el modal de gestión)
        const formId = isFromModal ? `delete-form-modal-${deleteTargetId}` : `delete-form-${deleteTargetId}`;
        const form = document.getElementById(formId);
        
        if (form) {
            form.submit();
        }
        closeDeleteModal();
    }

    // --- LÓGICA DEL MAPA ---
    function initMap() {
        if(map) {
            setTimeout(() => { map.invalidateSize(); }, 200);
            return; 
        }

        map = L.map('map-canvas', {
            center: [defaultLat, defaultLng],
            zoom: 15,
            minZoom: 12,
            maxZoom: 18
        });

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

        marker.on('dragend', function(event) {
            var position = marker.getLatLng();
            updateCoordinates(position.lat, position.lng);
        });

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateCoordinates(e.latlng.lat, e.latlng.lng);
        });

        updateCoordinates(defaultLat, defaultLng);
    }

    function updateCoordinates(lat, lng) {
        document.getElementById('input_lat').value = lat;
        document.getElementById('input_lng').value = lng;
    }

    window.getCurrentLocation = function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    if(map && marker) {
                        map.setView([lat, lng], 16);
                        marker.setLatLng([lat, lng]);
                        updateCoordinates(lat, lng);
                    }
                },
                () => { showToast("Error obteniendo ubicación GPS", "error"); }
            );
        }
    };

    // --- UTILIDADES ---
    window.toggleCheckbox = function(element) {
        const checkbox = element.querySelector('input[type="checkbox"]');
        if (event.target !== checkbox) {
            checkbox.checked = !checkbox.checked;
        }
        if(checkbox.checked) {
            element.classList.add('bg-green-50', 'border-primary');
        } else {
            element.classList.remove('bg-green-50', 'border-primary');
        }
    };

    window.toggleModalDireccion = function() {
        const modal = document.getElementById('modal-direccion');
        modal.classList.toggle('hidden');
        if (!modal.classList.contains('hidden')) {
            setTimeout(() => {
                initMap();
                if(map) map.invalidateSize(); 
            }, 300);
        }
    };

    window.toggleModalGestionar = function() {
        const modal = document.getElementById('modal-gestionar-direcciones');
        modal.classList.toggle('hidden');
    }
</script>
@endsection