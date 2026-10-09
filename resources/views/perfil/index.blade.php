@extends('layouts.app')

@section('title', 'Mi Perfil y Preferencias - Agrosuministros Rodríguez')

@push('styles')
    {{-- Estilos necesarios para el mapa de Leaflet --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        #map-canvas { z-index: 1; width: 100%; height: 100%; }
        .leaflet-container { font-family: "Work Sans", sans-serif; }
    </style>
@endpush

@section('content')
<div class="bg-slate-50 min-h-screen font-sans pb-24 relative">

    {{-- Encabezado decorativo corporativo --}}
    <div class="relative bg-gradient-to-r from-[#0b1d38] via-[#1e549e] to-[#3492cf] h-48 lg:h-56 overflow-hidden">
        <div class="absolute inset-0 opacity-15 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute -bottom-12 -right-12 size-64 rounded-full bg-[#3492cf]/25 blur-3xl pointer-events-none"></div>
        <div class="absolute top-0 left-1/3 size-48 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>

        <div class="container mx-auto px-4 sm:px-6 lg:px-8 pt-8 relative z-10">
            <div class="flex items-center gap-2 text-xs font-bold text-white/70 uppercase tracking-wider mb-2">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors">Inicio</a>
                <span>/</span>
                <span class="text-white">Mi Cuenta</span>
                <span>/</span>
                <span class="text-[#aad6f3]">Perfil</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Panel de Usuario</h1>
            <p class="text-xs sm:text-sm text-white/80 font-medium mt-1 max-w-xl">
                Administra tus datos personales, direcciones de despacho y credenciales de acceso.
            </p>
        </div>
    </div>

    {{-- Contenedor Principal --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 -mt-14 lg:-mt-16 relative z-20">
        
        <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 items-start">

            {{-- ASIDE: Menú Lateral Corporativo --}}
            @include('perfil.partials.sidebar', ['active' => 'perfil'])

            {{-- MAIN: Contenido y Secciones --}}
            <main class="flex-1 space-y-7 min-w-0 w-full pb-8">

                {{-- ALERTA GLOBAL DE ÉXITO O MENSAJE --}}
                @if(session('success-message') || session('success'))
                    <div class="p-4 sm:p-5 rounded-2xl bg-emerald-50 border border-emerald-200/90 text-emerald-800 shadow-2xs flex items-center justify-between gap-3 animate-fade-in-up">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="size-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                            </div>
                            <p class="text-xs sm:text-sm font-bold truncate">{{ session('success-message') ?? session('success') }}</p>
                        </div>
                        <button type="button" onclick="this.closest('div').remove()" class="text-emerald-500 hover:text-emerald-700 p-1">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>
                @endif

                {{-- SECCIÓN 1: DATOS PERSONALES --}}
                <div id="datos-personales" class="bg-white rounded-3xl shadow-xs border border-slate-200/90 p-6 sm:p-8 scroll-mt-24">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-5 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="size-11 rounded-2xl bg-[#e5f2fb] text-[#1e549e] flex items-center justify-center shrink-0 shadow-2xs">
                                <span class="material-symbols-outlined text-[22px]">badge</span>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-black text-slate-900 leading-tight">
                                    Información Personal
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">Datos asociados a tus comprobantes de compra y facturación.</p>
                            </div>
                        </div>

                        <span class="self-start sm:self-auto inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200/70">
                            <span class="material-symbols-outlined text-[14px]">verified_user</span> Cuenta Verificada
                        </span>
                    </div>

                    <form action="{{ route('perfil.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            @php
                                $labelNombre = auth()->user()->tipo_cliente === 'juridico' ? 'Razón Social' : 'Nombre';
                                $labelDoc = auth()->user()->tipo_cliente === 'juridico' ? 'RIF Fiscal' : 'Cédula / Documento';
                            @endphp

                            {{-- Nombre / Razón Social --}}
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ $labelNombre }} <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       name="nombre" 
                                       value="{{ old('nombre', auth()->user()->nombre) }}" 
                                       required
                                       class="w-full h-11 rounded-xl px-4 text-sm font-semibold transition-all shadow-2xs border @error('nombre') border-rose-300 bg-rose-50/50 text-rose-900 @else border-slate-200 bg-white text-slate-800 focus:border-[#3492cf] focus:ring-2 focus:ring-[#3492cf]/20 @enderror">
                                @error('nombre') 
                                    <span class="text-xs text-rose-500 font-bold flex items-center gap-1 mt-1.5">
                                        <span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}
                                    </span> 
                                @enderror
                            </div>

                            {{-- Apellido (si no es jurídico) --}}
                            @if(auth()->user()->tipo_cliente !== 'juridico')
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        Apellido <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="apellido" 
                                           value="{{ old('apellido', auth()->user()->apellido) }}" 
                                           required
                                           class="w-full h-11 rounded-xl px-4 text-sm font-semibold transition-all shadow-2xs border @error('apellido') border-rose-300 bg-rose-50/50 text-rose-900 @else border-slate-200 bg-white text-slate-800 focus:border-[#3492cf] focus:ring-2 focus:ring-[#3492cf]/20 @enderror">
                                    @error('apellido') 
                                        <span class="text-xs text-rose-500 font-bold flex items-center gap-1 mt-1.5">
                                            <span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}
                                        </span> 
                                    @enderror
                                </div>
                            @endif

                            {{-- Cédula / Documento (Bloqueado) --}}
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ $labelDoc }}
                                </label>
                                <div class="relative">
                                    <input type="text" 
                                           value="{{ auth()->user()->documento_identidad }}" 
                                           disabled
                                           class="w-full h-11 bg-slate-100 border border-slate-200 rounded-xl px-4 text-sm font-mono font-bold text-slate-500 cursor-not-allowed shadow-inner pr-10">
                                    <span class="material-symbols-outlined absolute right-3 top-3 text-slate-400 text-[18px]">lock</span>
                                </div>
                                <p class="text-[10px] text-slate-400 font-medium flex items-center gap-1 mt-1.5">
                                    <span class="material-symbols-outlined text-[13px]">info</span> Inmutable por normativa contable.
                                </p>
                            </div>

                            {{-- Teléfono --}}
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Teléfono Móvil <span class="text-rose-500">*</span>
                                </label>
                                <input type="tel" 
                                       name="telefono" 
                                       value="{{ old('telefono', auth()->user()->telefono) }}" 
                                       id="phone" 
                                       required
                                       class="w-full h-11 rounded-xl px-4 text-sm font-semibold transition-all shadow-2xs border @error('telefono') border-rose-300 bg-rose-50/50 text-rose-900 @else border-slate-200 bg-white text-slate-800 focus:border-[#3492cf] focus:ring-2 focus:ring-[#3492cf]/20 @enderror">
                                @error('telefono') 
                                    <span class="text-xs text-rose-500 font-bold flex items-center gap-1 mt-1.5">
                                        <span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}
                                    </span> 
                                @enderror
                            </div>

                            {{-- Correo Electrónico --}}
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Correo Electrónico <span class="text-rose-500">*</span>
                                </label>
                                <input type="email" 
                                       name="email" 
                                       value="{{ old('email', auth()->user()->email) }}" 
                                       required
                                       class="w-full h-11 rounded-xl px-4 text-sm font-semibold transition-all shadow-2xs border @error('email') border-rose-300 bg-rose-50/50 text-rose-900 @else border-slate-200 bg-white text-slate-800 focus:border-[#3492cf] focus:ring-2 focus:ring-[#3492cf]/20 @enderror">
                                @error('email') 
                                    <span class="text-xs text-rose-500 font-bold flex items-center gap-1 mt-1.5">
                                        <span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}
                                    </span> 
                                @enderror
                            </div>
                        </div>

                        {{-- ZONA DE SEGURIDAD PARA AUTORIZAR CAMBIOS --}}
                        <div class="mt-8 bg-gradient-to-br from-amber-50/80 via-white to-amber-50/30 border border-amber-200/80 rounded-2xl p-5 sm:p-6 shadow-2xs">
                            <div class="flex flex-col lg:flex-row lg:items-end gap-4">
                                <div class="w-full flex-1">
                                    <label class="text-xs font-black text-amber-900 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[18px] text-amber-600">lock_person</span>
                                        Confirmación de Seguridad
                                    </label>
                                    <p class="text-xs text-amber-800/80 mb-3 font-medium">
                                        Para autorizar y guardar modificaciones en tu cuenta, ingresa tu contraseña actual.
                                    </p>
                                    <input type="password" 
                                           name="password_actual_auth" 
                                           placeholder="Ingresa tu contraseña actual..." 
                                           autocomplete="current-password"
                                           required
                                           class="w-full h-11 bg-white border border-amber-300/80 rounded-xl px-4 text-sm font-medium text-slate-800 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all placeholder:text-slate-400 shadow-2xs">
                                    @error('password_actual_auth') 
                                        <p class="text-xs text-rose-600 font-bold mt-2 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[16px]">error</span> {{ $message }}
                                        </p> 
                                    @enderror
                                </div>
                                
                                <button type="submit" 
                                        class="w-full lg:w-auto h-11 px-7 rounded-xl bg-[#1e549e] hover:bg-[#3492cf] text-white font-black text-xs sm:text-sm shadow-xs transition-all flex items-center justify-center gap-2 shrink-0 active:scale-95 cursor-pointer">
                                    <span class="material-symbols-outlined text-[18px]">save</span>
                                    <span>Guardar Datos</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- SECCIÓN 2: DIRECCIONES DE ENVÍO --}}
                <div id="direcciones" class="bg-white rounded-3xl shadow-xs border border-slate-200/90 p-6 sm:p-8 scroll-mt-24">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="size-11 rounded-2xl bg-[#e5f2fb] text-[#1e549e] flex items-center justify-center shrink-0 shadow-2xs">
                                <span class="material-symbols-outlined text-[22px]">location_on</span>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-black text-slate-900 leading-tight">
                                    Libreta de Direcciones
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">Ubicaciones registradas para el despacho de tus compras.</p>
                            </div>
                        </div>

                        <div class="flex gap-2 w-full sm:w-auto">
                            @if(auth()->user()->direcciones->count() > 4)
                                <button type="button" 
                                        onclick="window.PerfilConfig.toggleModal('modal-gestionar-direcciones')" 
                                        class="flex-1 sm:flex-none justify-center text-xs font-bold text-slate-600 hover:text-[#1e549e] flex items-center gap-1.5 bg-slate-50 border border-slate-200 px-3.5 py-2.5 rounded-xl hover:bg-slate-100 transition-colors shadow-2xs cursor-pointer">
                                    <span class="material-symbols-outlined text-[18px]">list</span>
                                    <span>Ver todas ({{ auth()->user()->direcciones->count() }})</span>
                                </button>
                            @endif
                            <button type="button" 
                                    onclick="window.PerfilConfig.toggleModal('modal-direccion')" 
                                    class="flex-1 sm:flex-none justify-center text-xs font-bold text-white bg-[#1e549e] hover:bg-[#3492cf] px-4 py-2.5 rounded-xl transition-all shadow-xs flex items-center gap-1.5 active:scale-95 cursor-pointer">
                                <span class="material-symbols-outlined text-[18px]">add_location</span>
                                <span>Nueva Dirección</span>
                            </button>
                        </div>
                    </div>

                    @if(auth()->user()->direcciones && auth()->user()->direcciones->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach(auth()->user()->direcciones->sortByDesc('es_principal')->take(4) as $direccion)
                                <div class="relative rounded-2xl p-5 transition-all flex flex-col justify-between border {{ $direccion->es_principal ? 'border-[#3492cf] bg-[#e5f2fb]/25 shadow-2xs' : 'border-slate-200 bg-white hover:border-slate-300 shadow-2xs' }}">
                                    
                                    @if($direccion->es_principal)
                                        <span class="absolute top-3 right-3 bg-[#1e549e] text-white text-[10px] font-black tracking-wider px-2.5 py-0.5 rounded-full shadow-2xs uppercase">
                                            Principal
                                        </span>
                                    @endif

                                    <div>
                                        <div class="flex items-center gap-2 mb-2 pr-16">
                                            <div class="size-8 rounded-xl {{ $direccion->es_principal ? 'bg-[#1e549e] text-white' : 'bg-slate-100 text-slate-600' }} flex items-center justify-center shrink-0">
                                                <span class="material-symbols-outlined text-[18px]">home_pin</span>
                                            </div>
                                            <h4 class="font-black text-slate-900 text-sm truncate">{{ $direccion->alias ?? 'Ubicación' }}</h4>
                                        </div>

                                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-3">{{ $direccion->direccion_texto }}</p>

                                        @if($direccion->referencia_punto)
                                            <div class="inline-flex items-start gap-1 text-[11px] font-medium text-slate-500 bg-white/80 border border-slate-200/80 px-2.5 py-1 rounded-lg mb-2">
                                                <span class="material-symbols-outlined text-[14px] text-amber-600 shrink-0 mt-0.5">flag</span>
                                                <span class="truncate">{{ $direccion->referencia_punto }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex items-center justify-between gap-2 mt-4 pt-3 border-t {{ $direccion->es_principal ? 'border-[#aad6f3]/60' : 'border-slate-100' }}">
                                        @if(!$direccion->es_principal)
                                            <form action="{{ route('direccion.principal', $direccion->id) }}" method="POST">
                                                @csrf 
                                                @method('PATCH')
                                                <button type="submit" class="text-xs font-bold text-slate-500 hover:text-[#1e549e] transition-colors flex items-center gap-1 cursor-pointer">
                                                    <span class="material-symbols-outlined text-[16px]">check_circle</span> 
                                                    <span>Usar como principal</span>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs font-bold text-[#1e549e] flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[16px] text-emerald-600">verified</span> 
                                                Predeterminada
                                            </span>
                                        @endif
                                        
                                        @if(!$direccion->es_principal)
                                            <form id="delete-form-{{ $direccion->id }}" action="{{ route('direccion.destroy', $direccion->id) }}" method="POST">
                                                @csrf 
                                                @method('DELETE')
                                                <button type="button" 
                                                        onclick="window.PerfilConfig.openDeleteModal({{ $direccion->id }})" 
                                                        class="size-8 rounded-xl bg-slate-50 text-slate-400 hover:bg-rose-50 hover:text-rose-600 flex items-center justify-center transition-colors border border-slate-200/80 cursor-pointer" 
                                                        title="Eliminar dirección">
                                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10 bg-slate-50/70 rounded-2xl border-2 border-dashed border-slate-200">
                            <div class="size-14 bg-white rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-2xs border border-slate-200 text-slate-400">
                                <span class="material-symbols-outlined text-[28px]">add_location_alt</span>
                            </div>
                            <h4 class="text-sm font-black text-slate-800 mb-1">Aún no tienes direcciones registradas</h4>
                            <p class="text-xs text-slate-500 max-w-sm mx-auto mb-4">Agrega tus lugares de entrega frecuentes para agilizar el proceso de compra.</p>
                            <button type="button" 
                                    onclick="window.PerfilConfig.toggleModal('modal-direccion')" 
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#1e549e] text-white font-bold text-xs rounded-xl hover:bg-[#3492cf] transition-colors shadow-2xs">
                                <span class="material-symbols-outlined text-[16px]">add</span>
                                Registrar primera dirección
                            </button>
                        </div>
                    @endif
                </div>

                {{-- SECCIÓN 3: SEGURIDAD Y CONTRASEÑA --}}
                <div id="seguridad" class="bg-white rounded-3xl shadow-xs border border-slate-200/90 p-6 sm:p-8 scroll-mt-24">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-5 mb-6">
                        <div class="size-11 rounded-2xl bg-[#e5f2fb] text-[#1e549e] flex items-center justify-center shrink-0 shadow-2xs">
                            <span class="material-symbols-outlined text-[22px]">shield</span>
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-black text-slate-900 leading-tight">
                                Seguridad de la Cuenta
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Modifica tu contraseña de acceso para mantener protegida tu información.</p>
                        </div>
                    </div>

                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Contraseña Actual <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" 
                                       name="current_password" 
                                       required
                                       placeholder="••••••••"
                                       class="w-full h-11 rounded-xl px-4 text-sm font-semibold transition-all shadow-2xs border @error('current_password') border-rose-300 bg-rose-50/50 text-rose-900 @else border-slate-200 bg-white text-slate-800 focus:border-[#3492cf] focus:ring-2 focus:ring-[#3492cf]/20 @enderror">
                                @error('current_password') 
                                    <span class="text-xs text-rose-500 font-bold mt-1.5 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}
                                    </span> 
                                @enderror
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nueva Contraseña <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" 
                                       name="password" 
                                       required
                                       placeholder="Mínimo 8 caracteres"
                                       class="w-full h-11 rounded-xl px-4 text-sm font-semibold transition-all shadow-2xs border @error('password') border-rose-300 bg-rose-50/50 text-rose-900 @else border-slate-200 bg-white text-slate-800 focus:border-[#3492cf] focus:ring-2 focus:ring-[#3492cf]/20 @enderror">
                                @error('password') 
                                    <span class="text-xs text-rose-500 font-bold mt-1.5 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}
                                    </span> 
                                @enderror
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Confirmar Nueva Contraseña <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" 
                                       name="password_confirmation" 
                                       required
                                       placeholder="Repite la contraseña"
                                       class="w-full h-11 rounded-xl px-4 text-sm font-semibold transition-all shadow-2xs border border-slate-200 bg-white text-slate-800 focus:border-[#3492cf] focus:ring-2 focus:ring-[#3492cf]/20">
                            </div>
                        </div>
                        
                        <div class="mt-7 flex justify-end">
                            <button type="submit" 
                                    class="w-full sm:w-auto h-11 px-7 rounded-xl bg-[#1e549e] hover:bg-[#3492cf] text-white font-black text-xs sm:text-sm shadow-xs transition-all flex items-center justify-center gap-2 active:scale-95 cursor-pointer">
                                <span class="material-symbols-outlined text-[18px]">key</span>
                                <span>Actualizar Contraseña</span>
                            </button>
                        </div>
                    </form>
                </div>

            </main>
        </div>
    </div>
</div>

{{-- ================= MODALES ================= --}}

{{-- MODAL NUEVA DIRECCIÓN CON LEAFLET --}}
<div id="modal-direccion" class="fixed inset-0 z-[120] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-[#0b1d38]/60 backdrop-blur-xs transition-opacity opacity-0" id="modal-direccion-backdrop" onclick="window.PerfilConfig.toggleModal('modal-direccion')"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-end sm:items-center p-0 sm:p-4">
        <div class="relative transform overflow-hidden rounded-t-3xl sm:rounded-3xl bg-white text-left shadow-2xl transition-all w-full sm:max-w-2xl opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95 flex flex-col max-h-[90vh] sm:max-h-[95vh] border border-slate-100" id="modal-direccion-panel">
            
            {{-- Cabecera del Modal --}}
            <div class="bg-white px-6 py-4.5 border-b border-slate-100 flex justify-between items-center shrink-0 z-10">
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2.5">
                    <span class="material-symbols-outlined text-[#1e549e] text-[22px]">add_location_alt</span>
                    Registrar Nueva Dirección
                </h3>
                <button type="button" onclick="window.PerfilConfig.toggleModal('modal-direccion')" class="size-8 flex items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            <form action="{{ route('direccion.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden min-h-0">
                @csrf
                <input type="hidden" id="input_lat" name="geo_latitud">
                <input type="hidden" id="input_lng" name="geo_longitud">

                {{-- Cuerpo del Formulario --}}
                <div class="px-6 py-5 space-y-4 overflow-y-auto flex-1 custom-scrollbar">
                    
                    {{-- Alias y Punto de Referencia --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Alias de la Ubicación <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="alias" 
                                   required 
                                   placeholder="Ej: Finca El Roble, Oficina Central..."
                                   class="w-full h-10 bg-slate-50 border border-slate-200 rounded-xl px-3.5 text-sm font-medium text-slate-800 focus:bg-white focus:border-[#3492cf] focus:ring-2 focus:ring-[#3492cf]/20 transition-all shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Punto de Referencia
                            </label>
                            <input type="text" 
                                   name="referencia_punto" 
                                   placeholder="Ej: Portón blanco, frente al silo..."
                                   class="w-full h-10 bg-slate-50 border border-slate-200 rounded-xl px-3.5 text-sm font-medium text-slate-800 focus:bg-white focus:border-[#3492cf] focus:ring-2 focus:ring-[#3492cf]/20 transition-all shadow-2xs">
                        </div>
                    </div>

                    {{-- Dirección Detallada --}}
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Dirección Detallada <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="direccion_texto" 
                                  rows="2" 
                                  required 
                                  placeholder="Sector, Carretera, Kilómetro, Parroquia..."
                                  class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm font-medium text-slate-800 focus:bg-white focus:border-[#3492cf] focus:ring-2 focus:ring-[#3492cf]/20 transition-all resize-none shadow-2xs"></textarea>
                    </div>

                    {{-- Ubicación en Mapa --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                                Posición Exacta en Mapa (GPS)
                            </label>
                            <span class="text-[10px] text-slate-400 font-medium">Opcional</span>
                        </div>
                        
                        {{-- Botón GPS --}}
                        <button type="button" 
                                onclick="window.PerfilConfig.getCurrentLocation()" 
                                class="w-full h-10 flex items-center justify-center gap-2 bg-[#e5f2fb] hover:bg-[#3492cf] hover:text-white text-[#1e549e] font-bold text-xs rounded-xl border border-[#aad6f3]/80 transition-all shadow-2xs mb-2 group">
                            <span class="material-symbols-outlined text-[18px] group-hover:scale-110 transition-transform">my_location</span> 
                            <span>Usar mi ubicación GPS actual</span>
                        </button>

                        {{-- Contenedor del Mapa --}}
                        <div class="w-full h-44 rounded-xl border border-slate-200 overflow-hidden relative shadow-inner">
                            <div id="map-canvas" class="size-full bg-slate-100"></div>
                        </div>
                        <p class="text-[10px] font-medium text-slate-400 mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[13px]">info</span> Arrastra el marcador para ubicar tu entrada exacta.
                        </p>
                    </div>

                    {{-- Checkbox Guardar como Principal --}}
                    <label class="relative flex items-center p-3.5 bg-slate-50 rounded-xl border border-slate-200 cursor-pointer hover:bg-[#e5f2fb]/30 hover:border-[#3492cf] transition-all group select-none">
                        <input id="check_principal" 
                               name="es_principal" 
                               value="1" 
                               type="checkbox" 
                               class="size-4 rounded border-slate-300 text-[#1e549e] focus:ring-[#3492cf] bg-white cursor-pointer">
                        <div class="ml-3 text-xs flex flex-col">
                            <span class="font-bold text-slate-900 group-hover:text-[#1e549e] transition-colors">Guardar como dirección principal</span>
                            <span class="text-slate-400 text-[11px]">Se seleccionará automáticamente para tus próximos envíos.</span>
                        </div>
                    </label>
                </div>

                {{-- Pie del Modal --}}
                <div class="bg-slate-50 px-6 py-4 flex flex-col sm:flex-row gap-3 border-t border-slate-100 shrink-0">
                    <button type="submit" 
                            class="w-full sm:w-1/2 h-11 inline-flex justify-center items-center gap-2 rounded-xl bg-[#1e549e] hover:bg-[#3492cf] text-xs font-bold text-white shadow-xs transition-all active:scale-95 cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">save</span> 
                        <span>Guardar Dirección</span>
                    </button>
                    <button type="button" 
                            onclick="window.PerfilConfig.toggleModal('modal-direccion')" 
                            class="w-full sm:w-1/2 h-11 inline-flex justify-center items-center rounded-xl bg-white text-xs font-bold text-slate-700 shadow-2xs border border-slate-200 hover:bg-slate-100 transition-colors cursor-pointer">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL GESTIONAR TODAS LAS DIRECCIONES --}}
<div id="modal-gestionar-direcciones" class="fixed inset-0 z-[120] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-[#0b1d38]/60 backdrop-blur-xs transition-opacity opacity-0" id="modal-gestionar-direcciones-backdrop" onclick="window.PerfilConfig.toggleModal('modal-gestionar-direcciones')"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95 flex flex-col max-h-[85vh] border border-slate-100" id="modal-gestionar-direcciones-panel">
                
                <div class="bg-white px-6 py-4.5 border-b border-slate-100 flex items-center justify-between sticky top-0 z-10">
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#1e549e]">list_alt</span> 
                        Todas mis Direcciones Registradas
                    </h3>
                    <button type="button" onclick="window.PerfilConfig.toggleModal('modal-gestionar-direcciones')" class="size-8 flex items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto flex-1 custom-scrollbar space-y-3">
                    @foreach(auth()->user()->direcciones->sortByDesc('es_principal') as $direccion)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-slate-50 rounded-2xl border {{ $direccion->es_principal ? 'border-[#3492cf] bg-[#e5f2fb]/20' : 'border-slate-200' }} gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="font-bold text-sm text-slate-900 truncate">{{ $direccion->alias }}</h4>
                                    @if($direccion->es_principal)
                                        <span class="text-[9px] font-black uppercase tracking-wider text-[#1e549e] bg-[#e5f2fb] border border-[#aad6f3]/60 px-2 py-0.5 rounded-full">Principal</span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">{{ $direccion->direccion_texto }}</p>
                            </div>
                            <div class="flex items-center gap-2 justify-end shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-200/60">
                                @if(!$direccion->es_principal)
                                    <form action="{{ route('direccion.principal', $direccion->id) }}" method="POST">
                                        @csrf 
                                        @method('PATCH')
                                        <button type="submit" class="text-xs font-bold text-slate-600 hover:text-[#1e549e] bg-white border border-slate-200 hover:border-slate-300 px-3 py-1.5 rounded-xl transition-all shadow-2xs">
                                            Usar
                                        </button>
                                    </form>
                                    <form id="delete-form-modal-{{ $direccion->id }}" action="{{ route('direccion.destroy', $direccion->id) }}" method="POST">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="button" onclick="window.PerfilConfig.openDeleteModal({{ $direccion->id }}, true)" class="size-8 flex items-center justify-center text-rose-500 bg-white hover:bg-rose-500 hover:text-white rounded-xl transition-colors border border-slate-200 shadow-2xs">
                                            <span class="material-symbols-outlined text-[16px]">delete</span>
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

{{-- MODAL DE CONFIRMACIÓN (ELIMINAR) --}}
<div id="delete-modal" class="fixed inset-0 z-[150] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-[#0b1d38]/60 backdrop-blur-xs transition-opacity opacity-0" id="delete-modal-backdrop"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95 border border-slate-100" id="delete-modal-panel">
                <div class="p-6 sm:p-7 text-center">
                    <div class="size-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-4 border border-rose-100">
                        <span class="material-symbols-outlined text-[30px]">delete_forever</span>
                    </div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900 mb-1.5" id="modal-title">¿Eliminar esta dirección?</h3>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Esta ubicación será removida de tu libreta de direcciones. Esta acción no se puede deshacer.
                    </p>
                </div>
                <div class="bg-slate-50 px-6 py-4 flex flex-col sm:flex-row gap-3 border-t border-slate-100">
                    <button type="button" onclick="window.PerfilConfig.confirmDelete()" class="inline-flex w-full justify-center items-center rounded-xl bg-rose-600 hover:bg-rose-700 px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-xs transition-colors cursor-pointer">
                        Sí, Eliminar
                    </button>
                    <button type="button" onclick="window.PerfilConfig.closeDeleteModal()" class="inline-flex w-full justify-center items-center rounded-xl bg-white px-4 py-2.5 text-xs sm:text-sm font-bold text-slate-700 shadow-2xs border border-slate-200 hover:bg-slate-100 transition-colors cursor-pointer">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- TOAST NOTIFICATION --}}
<div id="toast-notification" class="fixed bottom-6 right-6 z-[200] transform transition-all duration-500 translate-y-24 opacity-0 pointer-events-none">
    <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-slate-200/90 p-4 flex items-center gap-3.5 min-w-[320px] max-w-md">
        <div id="toast-icon-container" class="size-11 rounded-xl flex items-center justify-center shrink-0 transition-colors">
            <span id="toast-icon" class="material-symbols-outlined text-2xl">check_circle</span>
        </div>
        <div class="flex-1 min-w-0">
            <h4 id="toast-title" class="font-bold text-slate-900 text-xs">Notificación</h4>
            <p id="toast-message" class="text-xs font-medium text-slate-500 mt-0.5 truncate">Mensaje...</p>
        </div>
        <button onclick="window.PerfilConfig.hideToast()" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg">
            <span class="material-symbols-outlined text-[18px]">close</span>
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