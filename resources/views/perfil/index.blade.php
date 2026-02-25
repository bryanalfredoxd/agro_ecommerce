@extends('layouts.app')

@section('title', 'Mi Perfil - Corpo Agrícola')

@push('styles')
    {{-- Estilos necesarios para el mapa de Leaflet --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        #map-canvas { z-index: 1; width: 100%; height: 100%; }
        .leaflet-container { font-family: "Work Sans", sans-serif; }
    </style>
@endpush

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

            {{-- ASIDE: Menú Lateral (Estilo Tarjeta) --}}
            <aside class="w-full lg:w-80 xl:w-80 flex-shrink-0">
                <div class="bg-white rounded-3xl shadow-lg shadow-black/5 border border-gray-100 overflow-hidden lg:sticky lg:top-24 transition-all">
                    
                    {{-- Perfil Header --}}
                    <div class="p-6 lg:p-8 text-center bg-white relative">
                        {{-- Avatar que sobresale ligeramente --}}
                        <div class="relative w-24 h-24 lg:w-28 lg:h-28 mx-auto bg-primary/10 text-primary rounded-full flex items-center justify-center text-3xl lg:text-4xl font-black mb-4 border-4 border-white shadow-sm">
                            {{ substr(auth()->user()->nombre, 0, 1) }}{{ substr(auth()->user()->apellido, 0, 1) }}
                        </div>
                        
                        <h2 class="text-xl lg:text-2xl font-black text-agro-dark mb-1 truncate leading-tight">
                            {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}
                        </h2>
                        <p class="text-sm text-gray-500 font-medium mb-4 truncate">{{ auth()->user()->email }}</p>
                        
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-50 border border-gray-100 text-gray-600 text-[11px] font-bold uppercase tracking-wider shadow-sm">
                            @if(auth()->user()->tipo_cliente === 'juridico')
                                <span class="material-symbols-outlined text-[16px] text-primary">domain</span> Empresa
                            @elseif(auth()->user()->tipo_cliente === 'finca_productor')
                                <span class="material-symbols-outlined text-[16px] text-primary">agriculture</span> Productor
                            @else
                                <span class="material-symbols-outlined text-[16px] text-primary">person</span> Particular
                            @endif
                        </div>
                    </div>

                    {{-- Menú de Navegación --}}
                    <div class="border-t border-gray-100 bg-white p-3">
                        <nav class="flex flex-col space-y-1">
                            <a href="#datos-personales" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-primary/5 text-primary font-bold transition-all group">
                                <span class="material-symbols-outlined text-[22px]">person</span>
                                Información Personal
                            </a>
                            
                            <a href="#direcciones" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-agro-dark font-medium transition-all group">
                                <span class="material-symbols-outlined text-[22px] text-gray-400 group-hover:text-agro-dark transition-colors">location_on</span>
                                Direcciones de Envío
                            </a>

                            <a href="#seguridad" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-agro-dark font-medium transition-all group">
                                <span class="material-symbols-outlined text-[22px] text-gray-400 group-hover:text-agro-dark transition-colors">lock</span>
                                Seguridad y Contraseña
                            </a>

                            <div class="h-px bg-gray-100 my-2 mx-4"></div>
                            
                            <a href="{{ route('perfil.pedidos') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-agro-dark font-medium transition-all group">
                                <span class="material-symbols-outlined text-[22px] text-gray-400 group-hover:text-agro-dark transition-colors">receipt_long</span>
                                Historial de Pedidos
                            </a>
                        </nav>
                    </div>
                </div>
            </aside>

            {{-- MAIN: Contenido --}}
            <main class="flex-1 space-y-8 min-w-0 pb-10">

                {{-- SECCIÓN 1: DATOS PERSONALES --}}
                <div id="datos-personales" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 lg:p-8 animate-fade-in-up scroll-mt-28">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-3 border-b border-gray-100 pb-5">
                        <h3 class="text-xl font-black text-agro-dark flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[28px]">feed</span>
                            Información Personal
                        </h3>
                        <span class="self-start sm:self-auto bg-green-50 border border-green-100 text-green-700 text-[10px] font-bold px-3 py-1.5 rounded-lg uppercase tracking-wider flex items-center gap-1 shadow-sm">
                            <span class="material-symbols-outlined text-[14px]">verified_user</span> Verificado
                        </span>
                    </div>

                    <form action="{{ route('perfil.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 lg:gap-6">
                            @php
                                $labelNombre = auth()->user()->tipo_cliente === 'juridico' ? 'Razón Social' : 'Nombre';
                                $labelDoc = auth()->user()->tipo_cliente === 'juridico' ? 'RIF' : 'Cédula / Documento';
                            @endphp

                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ $labelNombre }}</label>
                                <input type="text" name="nombre" value="{{ old('nombre', auth()->user()->nombre) }}" 
                                       class="w-full h-12 rounded-xl px-4 text-sm font-medium transition-all focus:ring-2 focus:ring-primary/20 
                                       @error('nombre') border-red-500 bg-red-50 text-red-900 @else border-gray-200 bg-gray-50 text-gray-800 focus:bg-white focus:border-primary @enderror">
                                @error('nombre') <span class="text-xs text-red-500 font-bold flex items-center gap-1 mt-1"><span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}</span> @enderror
                            </div>

                            @if(auth()->user()->tipo_cliente !== 'juridico')
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Apellido</label>
                                    <input type="text" name="apellido" value="{{ old('apellido', auth()->user()->apellido) }}" 
                                           class="w-full h-12 rounded-xl px-4 text-sm font-medium transition-all focus:ring-2 focus:ring-primary/20 
                                           @error('apellido') border-red-500 bg-red-50 text-red-900 @else border-gray-200 bg-gray-50 text-gray-800 focus:bg-white focus:border-primary @enderror">
                                    @error('apellido') <span class="text-xs text-red-500 font-bold flex items-center gap-1 mt-1"><span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}</span> @enderror
                                </div>
                            @endif

                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ $labelDoc }}</label>
                                <div class="relative">
                                    <input type="text" value="{{ auth()->user()->documento_identidad }}" disabled
                                           class="w-full h-12 bg-gray-100 border border-gray-200 rounded-xl px-4 text-sm font-medium text-gray-500 cursor-not-allowed opacity-70">
                                    <span class="material-symbols-outlined absolute right-3 top-3 text-gray-400 text-[20px]">lock</span>
                                </div>
                                <p class="text-[10px] text-gray-400 font-medium flex items-center gap-1 mt-1">
                                    <span class="material-symbols-outlined text-[14px]">info</span> Dato inmutable por seguridad.
                                </p>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Teléfono</label>
                                <input type="tel" name="telefono" value="{{ old('telefono', auth()->user()->telefono) }}" id="phone"
                                       class="w-full h-12 rounded-xl px-4 text-sm font-medium transition-all focus:ring-2 focus:ring-primary/20 
                                       @error('telefono') border-red-500 bg-red-50 text-red-900 @else border-gray-200 bg-gray-50 text-gray-800 focus:bg-white focus:border-primary @enderror">
                                @error('telefono') <span class="text-xs text-red-500 font-bold flex items-center gap-1 mt-1"><span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1.5 md:col-span-2">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Correo Electrónico</label>
                                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" 
                                       class="w-full h-12 rounded-xl px-4 text-sm font-medium transition-all focus:ring-2 focus:ring-primary/20 
                                       @error('email') border-red-500 bg-red-50 text-red-900 @else border-gray-200 bg-gray-50 text-gray-800 focus:bg-white focus:border-primary @enderror">
                                @error('email') <span class="text-xs text-red-500 font-bold flex items-center gap-1 mt-1"><span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- ZONA DE SEGURIDAD PARA GUARDAR --}}
                        <div class="mt-8 bg-orange-50 border border-orange-100 rounded-2xl p-5 lg:p-6">
                            <div class="flex flex-col lg:flex-row lg:items-end gap-4">
                                <div class="w-full flex-1">
                                    <label class="text-xs font-black text-orange-800 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[18px]">lock_person</span>
                                        Autorizar Cambios
                                    </label>
                                    <p class="text-xs text-orange-700/80 mb-3 font-medium">
                                        Para mantener tu cuenta segura, ingresa tu contraseña actual antes de guardar los cambios.
                                    </p>
                                    <input type="password" name="password_actual_auth" placeholder="Tu contraseña actual..." autocomplete="new-password"
                                           class="w-full h-11 bg-white border border-orange-200 rounded-xl px-4 text-sm font-medium text-gray-800 focus:border-orange-400 focus:ring-2 focus:ring-orange-200 transition-all placeholder:text-gray-400 shadow-sm">
                                    @error('password_actual_auth') 
                                        <p class="text-xs text-red-500 font-bold mt-2 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[16px]">error</span> {{ $message }}
                                        </p> 
                                    @enderror
                                </div>
                                
                                <button type="submit" class="w-full lg:w-auto h-11 px-8 bg-agro-dark text-white font-bold text-sm rounded-xl hover:bg-primary shadow-lg shadow-agro-dark/20 hover:shadow-primary/30 transition-all flex items-center justify-center gap-2 flex-shrink-0">
                                    <span class="material-symbols-outlined text-[20px]">save</span>
                                    Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- SECCIÓN 2: DIRECCIONES --}}
                <div id="direcciones" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 lg:p-8 animate-fade-in-up scroll-mt-28" style="animation-delay: 100ms;">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4 border-b border-gray-100 pb-5">
                        <h3 class="text-xl font-black text-agro-dark flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[28px]">map</span>
                            Mis Direcciones
                        </h3>
                        <div class="flex gap-2 w-full sm:w-auto">
                            @if(auth()->user()->direcciones->count() > 4)
                                <button onclick="window.PerfilConfig.toggleModal('modal-gestionar-direcciones')" class="flex-1 sm:flex-none justify-center text-sm font-bold text-gray-600 hover:text-agro-dark flex items-center gap-1.5 bg-gray-50 border border-gray-200 px-4 py-2.5 rounded-xl hover:bg-gray-100 transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">list</span>
                                    Ver todas
                                </button>
                            @endif
                            <button onclick="window.PerfilConfig.toggleModal('modal-direccion')" class="flex-1 sm:flex-none justify-center text-sm font-bold text-agro-dark hover:text-white flex items-center gap-1.5 bg-primary/10 border border-primary/20 px-4 py-2.5 rounded-xl hover:bg-primary hover:border-primary transition-all shadow-sm">
                                <span class="material-symbols-outlined text-[18px]">add_location</span>
                                Nueva Dirección
                            </button>
                        </div>
                    </div>

                    @if(auth()->user()->direcciones && auth()->user()->direcciones->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-5">
                            @foreach(auth()->user()->direcciones->sortByDesc('es_principal')->take(4) as $direccion)
                                <div class="relative group border {{ $direccion->es_principal ? 'border-primary bg-primary/5' : 'border-gray-200 bg-white' }} rounded-2xl p-5 hover:border-primary/50 transition-all flex flex-col h-full shadow-sm">
                                    
                                    @if($direccion->es_principal)
                                        <span class="absolute top-0 right-0 bg-primary text-agro-dark text-[10px] font-black tracking-wider px-3 py-1 rounded-bl-xl rounded-tr-xl shadow-sm uppercase">
                                            Principal
                                        </span>
                                    @endif

                                    <div class="flex items-start gap-4 flex-1">
                                        <div class="bg-white p-2.5 rounded-full shadow-sm text-gray-400 group-hover:text-primary transition-colors flex-shrink-0 border border-gray-100">
                                            <span class="material-symbols-outlined text-[24px]">home_pin</span>
                                        </div>
                                        <div class="min-w-0 pr-4">
                                            <h4 class="font-black text-agro-dark text-base mb-1 truncate">{{ $direccion->alias ?? 'Ubicación' }}</h4>
                                            <p class="text-sm text-gray-600 leading-snug mb-2">{{ $direccion->direccion_texto }}</p>
                                            @if($direccion->referencia_punto)
                                                <p class="text-xs text-gray-400 font-medium flex items-start gap-1">
                                                    <span class="material-symbols-outlined text-[16px]">info</span> {{ $direccion->referencia_punto }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t {{ $direccion->es_principal ? 'border-primary/10' : 'border-gray-100' }} justify-between items-center">
                                        @if(!$direccion->es_principal)
                                            <form action="{{ route('direccion.principal', $direccion->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="text-xs font-bold text-gray-500 hover:text-primary transition-colors flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[16px]">check_circle</span> Usar por defecto
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs font-bold text-gray-400 flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[16px]">verified</span> Predeterminada
                                            </span>
                                        @endif
                                        
                                        @if(!$direccion->es_principal)
                                            <form id="delete-form-{{ $direccion->id }}" action="{{ route('direccion.destroy', $direccion->id) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="button" onclick="window.PerfilConfig.openDeleteModal({{ $direccion->id }})" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center transition-colors shadow-sm">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10 bg-white rounded-2xl border border-dashed border-gray-300">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                <span class="material-symbols-outlined text-[32px] text-gray-400">wrong_location</span>
                            </div>
                            <p class="text-gray-800 font-bold text-base mb-1">Aún no tienes direcciones</p>
                            <p class="text-gray-500 text-sm">Registra una dirección para agilizar tus compras.</p>
                        </div>
                    @endif
                </div>

                {{-- SECCIÓN 3: SEGURIDAD (PASS) --}}
                <div id="seguridad" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 lg:p-8 animate-fade-in-up scroll-mt-28" style="animation-delay: 200ms;">
                    <div class="flex items-center justify-between mb-8 border-b border-gray-100 pb-5">
                        <h3 class="text-xl font-black text-agro-dark flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[28px]">security</span>
                            Seguridad de la Cuenta
                        </h3>
                    </div>

                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 lg:gap-6">
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Contraseña Actual</label>
                                <input type="password" name="current_password" 
                                       class="w-full h-12 rounded-xl px-4 text-sm font-medium transition-all focus:ring-2 focus:ring-primary/20 
                                       @error('current_password') border-red-500 bg-red-50 text-red-900 @else border-gray-200 bg-gray-50 text-gray-800 focus:bg-white focus:border-primary @enderror">
                                @error('current_password') <span class="text-xs text-red-500 font-bold mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Nueva Contraseña</label>
                                <input type="password" name="password" 
                                       class="w-full h-12 rounded-xl px-4 text-sm font-medium transition-all focus:ring-2 focus:ring-primary/20 
                                       @error('password') border-red-500 bg-red-50 text-red-900 @else border-gray-200 bg-gray-50 text-gray-800 focus:bg-white focus:border-primary @enderror">
                                @error('password') <span class="text-xs text-red-500 font-bold mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Confirmar Nueva</label>
                                <input type="password" name="password_confirmation" 
                                       class="w-full h-12 rounded-xl px-4 text-sm font-medium transition-all border border-gray-200 bg-gray-50 text-gray-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20">
                            </div>
                        </div>
                        
                        {{-- 2. BOTÓN DE ACTUALIZAR CONTRASEÑA CENTRADO --}}
                        <div class="mt-8 flex justify-center">
                            <button type="submit" class="w-full sm:w-auto h-11 px-10 bg-gray-800 text-white font-bold text-sm rounded-xl hover:bg-black shadow-lg shadow-gray-400/20 transition-all flex items-center justify-center gap-2 transform hover:-translate-y-1">
                                <span class="material-symbols-outlined text-[20px]">key</span>
                                Actualizar Contraseña
                            </button>
                        </div>
                    </form>
                </div>

            </main>
        </div>
    </div>
</div>

{{-- ================= MODALES ================= --}}

{{-- MODAL NUEVA DIRECCIÓN --}}
<div id="modal-direccion" class="fixed inset-0 z-[120] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-agro-dark/60 backdrop-blur-sm transition-opacity opacity-0" id="modal-direccion-backdrop" onclick="window.PerfilConfig.toggleModal('modal-direccion')"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-end sm:items-center p-0 sm:p-4">
        {{-- PANEL DEL MODAL: Limitado al 95vh en PC para evitar que se salga de la pantalla, con Flexbox estricto --}}
        <div class="relative transform overflow-hidden rounded-t-3xl sm:rounded-3xl bg-white text-left shadow-2xl transition-all w-full sm:max-w-2xl opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95 flex flex-col max-h-[90vh] sm:max-h-[95vh]" id="modal-direccion-panel">
            
            {{-- CABECERA: Fija --}}
            <div class="bg-white px-5 py-4 border-b border-gray-100 flex justify-between items-center flex-shrink-0 z-10">
                <h3 class="text-lg font-black text-agro-dark flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[24px]">add_location_alt</span>
                    Nueva Dirección
                </h3>
                <button type="button" onclick="window.PerfilConfig.toggleModal('modal-direccion')" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form action="{{ route('direccion.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden min-h-0">
                @csrf
                <input type="hidden" id="input_lat" name="geo_latitud">
                <input type="hidden" id="input_lng" name="geo_longitud">

                {{-- CUERPO: Área scrollable si es necesario --}}
                <div class="px-5 py-5 space-y-4 overflow-y-auto flex-1 custom-scrollbar">
                    
                    {{-- Fila 1: Alias y Referencia (Compactos) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Alias</label>
                            <input type="text" name="alias" required placeholder="Ej: Casa, Oficina, Finca..."
                                   class="w-full h-10 bg-gray-50 border border-gray-200 rounded-xl px-4 text-sm font-medium text-gray-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all shadow-sm">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Punto de Referencia</label>
                            <input type="text" name="referencia_punto" placeholder="Ej: Portón azul..."
                                   class="w-full h-10 bg-gray-50 border border-gray-200 rounded-xl px-4 text-sm font-medium text-gray-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all shadow-sm">
                        </div>
                    </div>

                    {{-- Fila 2: Dirección de texto --}}
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Dirección Detallada</label>
                        <textarea name="direccion_texto" rows="2" required placeholder="Calle, Avenida, Sector, Número de casa..."
                                  class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm font-medium text-gray-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all resize-none shadow-sm"></textarea>
                    </div>

                    {{-- Fila 3: Mapa y GPS --}}
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Ubicación GPS Exacta</label>
                        
                        {{-- Botón GPS MUY DESTACADO --}}
                        <button type="button" onclick="window.PerfilConfig.getCurrentLocation()" class="w-full h-10 flex items-center justify-center gap-2 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-sm rounded-xl border border-blue-200 transition-colors shadow-sm mb-2 group">
                            <span class="material-symbols-outlined text-[18px] group-hover:animate-pulse">my_location</span> 
                            Usar mi ubicación actual
                        </button>

                        {{-- Mapa (Altura reducida para que quepa en laptops) --}}
                        <div class="w-full h-40 sm:h-48 rounded-xl border border-gray-200 overflow-hidden relative z-0 shadow-inner">
                            <div id="map-canvas" class="w-full h-full bg-gray-100"></div>
                        </div>
                        <p class="text-[10px] font-medium text-gray-400 mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">info</span> Mueve el mapa para posicionar el pin en tu puerta.
                        </p>
                    </div>

                    {{-- Fila 4: Checkbox Principal --}}
                    <label class="relative flex items-center p-3 sm:p-4 bg-gray-50 rounded-xl border border-gray-200 cursor-pointer hover:bg-white hover:border-primary/50 transition-all group select-none mt-2">
                        <input id="check_principal" name="es_principal" value="1" type="checkbox" 
                               class="h-5 w-5 rounded border-gray-300 text-primary focus:ring-primary bg-white shadow-sm cursor-pointer"
                               onchange="this.closest('label').classList.toggle('bg-primary/5'); this.closest('label').classList.toggle('border-primary');">
                        <div class="ml-3 text-sm flex flex-col">
                            <span class="font-bold text-agro-dark group-hover:text-primary transition-colors">Guardar como Principal</span>
                            <span class="text-gray-500 text-[11px]">Se usará por defecto para tus próximos pedidos.</span>
                        </div>
                    </label>
                </div>

                {{-- PIE DE PÁGINA (BOTONES): Fijo --}}
                <div class="bg-gray-50 px-5 py-4 flex flex-col sm:flex-row gap-3 border-t border-gray-100 flex-shrink-0">
                    <button type="submit" class="w-full sm:w-1/2 h-11 inline-flex justify-center items-center gap-2 rounded-xl bg-agro-dark text-sm font-bold text-white shadow-lg shadow-agro-dark/20 hover:bg-primary hover:shadow-primary/30 transition-all">
                        <span class="material-symbols-outlined text-[20px]">save</span> Guardar Dirección
                    </button>
                    <button type="button" onclick="window.PerfilConfig.toggleModal('modal-direccion')" class="w-full sm:w-1/2 h-11 inline-flex justify-center items-center rounded-xl bg-white text-sm font-bold text-gray-700 shadow-sm border border-gray-200 hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL GESTIONAR TODAS LAS DIRECCIONES --}}
<div id="modal-gestionar-direcciones" class="fixed inset-0 z-[120] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-agro-dark/60 backdrop-blur-sm transition-opacity opacity-0" id="modal-gestionar-direcciones-backdrop" onclick="window.PerfilConfig.toggleModal('modal-gestionar-direcciones')"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-3xl bg-gray-50 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95 flex flex-col max-h-[85vh]" id="modal-gestionar-direcciones-panel">
                
                <div class="bg-white px-6 py-5 border-b border-gray-100 flex items-center justify-between sticky top-0 z-10">
                    <h3 class="text-lg font-black text-agro-dark flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[24px]">list_alt</span> Gestionar Direcciones
                    </h3>
                    <button type="button" onclick="window.PerfilConfig.toggleModal('modal-gestionar-direcciones')" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto flex-1 custom-scrollbar">
                    <div class="space-y-4">
                        @foreach(auth()->user()->direcciones->sortByDesc('es_principal') as $direccion)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-white border rounded-2xl {{ $direccion->es_principal ? 'border-primary shadow-sm shadow-primary/10' : 'border-gray-200 shadow-sm' }} gap-4">
                                <div class="flex items-start gap-3 overflow-hidden">
                                    <div class="text-gray-400 flex-shrink-0 mt-0.5">
                                        <span class="material-symbols-outlined {{ $direccion->es_principal ? 'text-primary' : '' }}">home_pin</span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="font-bold text-sm text-agro-dark truncate">{{ $direccion->alias }}</h4>
                                            @if($direccion->es_principal)
                                                <span class="text-[9px] font-black uppercase tracking-wider text-primary bg-primary/10 px-1.5 py-0.5 rounded">Principal</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 truncate">{{ $direccion->direccion_texto }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 justify-end sm:flex-shrink-0 border-t sm:border-t-0 border-gray-100 pt-3 sm:pt-0">
                                    @if(!$direccion->es_principal)
                                        <form action="{{ route('direccion.principal', $direccion->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-xs font-bold text-gray-600 hover:text-agro-dark bg-gray-50 border border-gray-200 hover:border-gray-300 px-3 py-2 rounded-xl transition-all shadow-sm">
                                                Usar
                                            </button>
                                        </form>
                                        <form id="delete-form-modal-{{ $direccion->id }}" action="{{ route('direccion.destroy', $direccion->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="window.PerfilConfig.openDeleteModal({{ $direccion->id }}, true)" class="w-9 h-9 flex items-center justify-center text-red-500 bg-red-50 hover:bg-red-500 hover:text-white rounded-xl transition-colors shadow-sm">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DE CONFIRMACIÓN (ELIMINAR) --}}
<div id="delete-modal" class="fixed inset-0 z-[150] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-agro-dark/60 backdrop-blur-sm transition-opacity opacity-0" id="delete-modal-backdrop"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95" id="delete-modal-panel">
                <div class="px-6 py-8 sm:p-8">
                    <div class="flex flex-col items-center text-center">
                        <div class="mx-auto flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full bg-red-50 mb-6">
                            <span class="material-symbols-outlined text-4xl text-red-500">delete_forever</span>
                        </div>
                        <h3 class="text-xl font-black text-agro-dark mb-2" id="modal-title">¿Eliminar dirección?</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">
                            Esta dirección será eliminada de tu libreta. ¿Estás seguro de que deseas continuar?
                        </p>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row gap-3">
                    <button type="button" onclick="window.PerfilConfig.confirmDelete()" class="inline-flex w-full justify-center items-center rounded-xl bg-red-500 px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-red-600 sm:w-1/2 transition-colors">
                        Sí, Eliminar
                    </button>
                    <button type="button" onclick="window.PerfilConfig.closeDeleteModal()" class="inline-flex w-full justify-center items-center rounded-xl bg-white px-4 py-3 text-sm font-bold text-gray-700 shadow-sm border border-gray-200 hover:bg-gray-50 sm:w-1/2 transition-colors">
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
        <button onclick="window.PerfilConfig.hideToast()" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100">
            <span class="material-symbols-outlined text-xl">close</span>
        </button>
    </div>
</div>
@endsection

@push('scripts')
    {{-- Scripts para Leaflet y Teléfonos Internacionales --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@24.6.0/build/js/intlTelInput.min.js"></script>
    
    <script>
        // Inyectamos configuraciones de Blade antes de cargar el JS
        window.PerfilData = {
            sessionSuccess: "{{ session('success') ?? '' }}",
            sessionError: "{{ session('error') ?? '' }}",
            hasErrors: {{ $errors->any() ? 'true' : 'false' }},
            defaultLat: 8.6226,
            defaultLng: -70.2075
        };
    </script>
    <script src="{{ asset('js/perfil.js') }}"></script>
@endpush