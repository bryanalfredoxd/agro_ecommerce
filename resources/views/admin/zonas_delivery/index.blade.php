@extends('layouts.admin')

@section('title', 'Zonas de Delivery - Agrosuministros Rodríguez')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.3s ease-out forwards;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 1rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        padding: 4px;
    }
</style>
@endpush

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    {{-- SIDEBAR LATERAL --}}
    @include('admin.partials.sidebar')

    {{-- CONTENIDO PRINCIPAL --}}
    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        
        {{-- TOPBAR ADMIN --}}
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up pb-16 space-y-6">

            {{-- CABECERA PRINCIPAL --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <a href="{{ route('admin.dashboard') }}" class="size-10 flex-shrink-0 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:text-blue-600 hover:border-blue-200 transition-colors shadow-2xs" title="Volver al Panel">
                        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[28px] text-[#1e549e]">pin_drop</span>
                            <h1 class="text-xl sm:text-2xl font-black text-[#0b1d38] tracking-tight">Gestión de Zonas de Delivery</h1>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                            Configura áreas de cobertura geográfica, tarifas en USD y tiempos de entrega mediante mapa satelital e interactivo.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @if(Auth::user()->tienePermiso('gestionar_zonas_delivery'))
                    <button onclick="abrirModalCrear()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#1e549e] hover:bg-[#164079] text-white text-xs sm:text-sm font-bold rounded-xl shadow-md shadow-[#1e549e]/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <span class="material-symbols-outlined text-[20px]">add_location_alt</span>
                        Nueva Zona de Envío
                    </button>
                    @endif
                </div>
            </div>

    {{-- INTERRUPTOR MAESTRO DEL SERVICIO DE DELIVERY --}}
    <div class="p-4 sm:p-5 rounded-2xl border transition-all {{ $deliveryActivo ? 'bg-gradient-to-r from-emerald-500/10 via-emerald-500/5 to-white border-emerald-300' : 'bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-white border-amber-300' }} shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4" id="bannerMasterDelivery">
        <div class="flex items-center gap-3.5">
            <div class="size-12 rounded-xl {{ $deliveryActivo ? 'bg-emerald-500 text-white' : 'bg-amber-500 text-white' }} flex items-center justify-center shrink-0 shadow-xs" id="iconMasterDelivery">
                <span class="material-symbols-outlined text-[26px]">{{ $deliveryActivo ? 'local_shipping' : 'pause_circle' }}</span>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-black text-slate-800 text-sm sm:text-base">Servicio de Envíos / Delivery en Tienda Web</h3>
                    <span id="badgeMasterDelivery" class="text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase tracking-wider {{ $deliveryActivo ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                        {{ $deliveryActivo ? 'Activo en Línea' : 'Pausado Temporalmente' }}
                    </span>
                </div>
                <p id="descMasterDelivery" class="text-xs text-slate-600 mt-0.5 font-medium">
                    {{ $deliveryActivo ? 'Los clientes pueden seleccionar Delivery y calcular flete según su ubicación geográfica.' : 'El servicio está pausado: en el checkout los clientes solo podrán elegir "Retiro en Sede".' }}
                </p>
            </div>
        </div>
        
        @if(Auth::user()->tienePermiso('gestionar_zonas_delivery'))
        <div>
            <button type="button" 
                    onclick="toggleMasterDelivery()" 
                    id="btnToggleMasterDelivery"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-black text-xs transition-all shadow-xs {{ $deliveryActivo ? 'bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200' : 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-emerald-600/20' }}">
                <span class="material-symbols-outlined text-[18px]">{{ $deliveryActivo ? 'pause' : 'play_arrow' }}</span>
                <span id="btnTextMasterDelivery">{{ $deliveryActivo ? 'Pausar Servicio de Delivery' : 'Activar Servicio de Delivery' }}</span>
            </button>
        </div>
        @endif
    </div>

    {{-- KPIS RESUMEN --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="size-12 rounded-xl bg-blue-50 text-[#1e549e] flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[26px]">map</span>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Zonas</p>
                <p class="text-xl sm:text-2xl font-black text-slate-800">{{ $totalZonas }}</p>
            </div>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="size-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[26px]">check_circle</span>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Zonas Activas</p>
                <p class="text-xl sm:text-2xl font-black text-slate-800" id="kpiZonasActivas">{{ $zonasActivas }}</p>
            </div>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="size-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[26px]">payments</span>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tarifa Promedio</p>
                <p class="text-xl sm:text-2xl font-black text-slate-800">${{ number_format($precioPromedio, 2) }}</p>
            </div>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="size-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[26px]">radar</span>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Radio Cobertura Máx.</p>
                <p class="text-xl sm:text-2xl font-black text-slate-800">{{ number_format($maxRadio, 1) }} km</p>
            </div>
        </div>
    </div>

    {{-- GRID PRINCIPAL: MAPA LEAFLET + TABLA DE ZONAS --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">

        {{-- COLUMNA IZQUIERDA: MAPA INTERACTIVO LEAFLET (7 cols) --}}
        <div class="xl:col-span-7 bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3 bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#1e549e] text-[22px]">explore</span>
                    <h3 class="font-black text-slate-800 text-sm sm:text-base">Mapa de Cobertura en Tiempo Real</h3>
                </div>
                <div class="flex items-center gap-2">
                    <div id="indicadorCentroTienda" class="flex items-center gap-1.5 px-3 py-1 bg-white border border-slate-200/90 rounded-xl text-xs font-bold text-slate-700 shadow-2xs">
                        <span class="inline-block size-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span id="textoCentroTienda">Centro: Sede Tienda</span>
                    </div>
                    <button type="button" onclick="detectarYFijarUbicacionGPS(true)" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#1e549e] hover:bg-[#164079] text-white text-xs font-bold rounded-xl shadow-xs transition-all hover:scale-105 active:scale-95" title="Detectar mi ubicación real por GPS y fijarla como centro de la tienda">
                        <span class="material-symbols-outlined text-[16px]">my_location</span>
                        <span>Fijar mi GPS</span>
                    </button>
                </div>
            </div>

            {{-- CONTENEDOR DEL MAPA --}}
            <div class="relative w-full h-[450px] sm:h-[520px] bg-slate-100">
                <div id="leafletMap" class="w-full h-full z-10"></div>
                
                {{-- Controles flotantes en el mapa --}}
                <div id="mapFloatingHint" class="absolute bottom-4 left-4 z-20 bg-white/95 backdrop-blur-md px-3.5 py-2 rounded-xl border border-slate-200 shadow-md text-xs font-bold text-slate-700 flex items-center gap-2 max-w-[90%]">
                    <span class="material-symbols-outlined text-[18px] text-[#1e549e]">touch_app</span>
                    <span id="mapHintText">Haz clic en el mapa para añadir una zona o pulsa "Nueva Zona de Envío"</span>
                </div>
            </div>

            {{-- LEYENDA DEL MAPA --}}
            <div class="p-4 bg-slate-50/70 border-t border-slate-100 text-xs flex flex-wrap items-center gap-4">
                <span class="font-bold text-slate-600">Leyenda:</span>
                @foreach($zonas as $z)
                    <div class="flex items-center gap-1.5 cursor-pointer hover:opacity-80 transition-opacity" onclick="enfocarZona({{ $z->id }}, {{ $z->latitud ?? 8.7622 }}, {{ $z->longitud ?? -70.4106 }})">
                        <span class="inline-block size-3 rounded-full border border-black/10" style="background-color: {{ $z->color_mapa ?? '#1e549e' }};"></span>
                        <span class="font-medium text-slate-700">{{ $z->nombre_zona }} (${{ number_format($z->precio_delivery_usd, 2) }})</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- COLUMNA DERECHA: TABLA Y DETALLE DE ZONAS (5 cols) --}}
        <div class="xl:col-span-5 bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#3492cf] text-[22px]">format_list_bulleted</span>
                    <h3 class="font-black text-slate-800 text-sm sm:text-base">Zonas Registradas ({{ $zonas->count() }})</h3>
                </div>
            </div>

            <div class="divide-y divide-slate-100 max-h-[580px] overflow-y-auto custom-scrollbar">
                @forelse($zonas as $zona)
                    <div class="p-4 sm:p-5 hover:bg-slate-50/70 transition-colors group relative" id="zona-card-{{ $zona->id }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <span class="size-3.5 rounded-full mt-1 shrink-0 shadow-xs" style="background-color: {{ $zona->color_mapa ?? '#1e549e' }};"></span>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                         <h4 class="font-black text-slate-800 text-sm sm:text-base">{{ $zona->nombre_zona }}</h4>
                                         <span id="badge-estado-{{ $zona->id }}" class="text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider {{ $zona->activa ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-300' }}">
                                             {{ $zona->activa ? 'Activa' : 'Pausada' }}
                                         </span>
                                     </div>
                                     <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                                         {{ $zona->descripcion ?: 'Sin descripción detallada de la ruta.' }}
                                     </p>
                                 </div>
                             </div>

                             <div class="text-right shrink-0">
                                 <p class="text-base sm:text-lg font-black text-[#1e549e] leading-none">${{ number_format($zona->precio_delivery_usd, 2) }}</p>
                                 <span class="text-[10px] font-bold text-slate-400 uppercase">Tarifa USD</span>
                             </div>
                         </div>

                         {{-- Metadata adicional: tiempo, vehiculo, radio --}}
                         <div class="mt-3 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500">
                             <div class="flex items-center gap-3">
                                 <span class="flex items-center gap-1 font-medium" title="Tiempo estimado">
                                     <span class="material-symbols-outlined text-[16px] text-slate-400">schedule</span>
                                     {{ $zona->tiempo_estimado_minutos ? $zona->tiempo_estimado_minutos . ' min' : '30-45 min' }}
                                 </span>
                                 <span class="flex items-center gap-1 font-medium" title="Radio de alcance">
                                     <span class="material-symbols-outlined text-[16px] text-slate-400">radar</span>
                                     {{ number_format($zona->radio_km ?? 3.0, 1) }} km
                                 </span>
                                 @if($zona->requiere_vehiculo_carga)
                                 <span class="flex items-center gap-1 font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-200" title="Requiere carga pesada">
                                     <span class="material-symbols-outlined text-[14px]">local_shipping</span> Carga
                                 </span>
                                 @endif
                             </div>

                             {{-- Acciones --}}
                             <div class="flex items-center gap-1">
                                 <button onclick="enfocarZona({{ $zona->id }}, {{ $zona->latitud ?? 8.7622 }}, {{ $zona->longitud ?? -70.4106 }})" class="p-1.5 text-slate-400 hover:text-[#1e549e] hover:bg-slate-100 rounded-lg transition-colors" title="Ver en el Mapa">
                                     <span class="material-symbols-outlined text-[18px]">visibility</span>
                                 </button>
                                 @if(Auth::user()->tienePermiso('gestionar_zonas_delivery'))
                                 <button onclick="abrirModalEditar({{ json_encode($zona) }})" class="p-1.5 text-slate-400 hover:text-[#3492cf] hover:bg-slate-100 rounded-lg transition-colors" title="Editar Zona">
                                     <span class="material-symbols-outlined text-[18px]">edit</span>
                                 </button>
                                 <button id="btn-toggle-{{ $zona->id }}" onclick="toggleEstadoZona({{ $zona->id }})" class="p-1.5 rounded-lg border transition-all shadow-2xs hover:scale-105 {{ $zona->activa ? 'text-emerald-600 bg-emerald-50 hover:bg-emerald-100 border-emerald-200' : 'text-amber-600 bg-amber-50 hover:bg-amber-100 border-amber-300' }}" title="{{ $zona->activa ? 'Zona Activa (Clic para pausar)' : 'Zona Pausada (Clic para activar)' }}">
                                     <span class="material-symbols-outlined text-[19px]">{{ $zona->activa ? 'toggle_on' : 'toggle_off' }}</span>
                                 </button>
                                 <button onclick="eliminarZona({{ $zona->id }}, '{{ addslashes($zona->nombre_zona) }}')" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Eliminar Zona">
                                     <span class="material-symbols-outlined text-[18px]">delete</span>
                                 </button>
                                 @endif
                             </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400">
                        <span class="material-symbols-outlined text-4xl mb-2">near_me_disabled</span>
                        <p class="font-bold">No hay zonas de delivery creadas.</p>
                        <p class="text-xs mt-1">Crea la primera zona para habilitar envíos a domicilio.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

        </div>
    </main>
</div>

{{-- MODAL CREAR / EDITAR ZONA --}}
<div id="modalZona" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity opacity-0" id="modalBackdrop" onclick="cerrarModal()"></div>

    <div class="fixed inset-0 z-10 flex justify-center items-center p-4">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-xl opacity-0 scale-95" id="modalPanel">
            
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[#1e549e] text-2xl" id="modalIcon">add_location</span>
                    <h3 class="text-lg font-black text-slate-800" id="modalTitle">Nueva Zona de Delivery</h3>
                </div>
                <button type="button" onclick="cerrarModal()" class="text-slate-400 hover:text-rose-500 p-1.5 rounded-lg transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form id="formZona" onsubmit="guardarZona(event)" class="p-6 space-y-4">
                <input type="hidden" id="zona_id" name="id">

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nombre de la Zona *</label>
                    <input type="text" id="nombre_zona" name="nombre_zona" required placeholder="Ej. Sector El Cacao / Alto Barinas" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-800 focus:outline-hidden focus:border-[#1e549e] focus:ring-2 focus:ring-[#1e549e]/10">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Precio Delivery (USD) *</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-2.5 text-slate-400 font-bold">$</span>
                            <input type="number" step="0.01" min="0" id="precio_delivery_usd" name="precio_delivery_usd" required placeholder="3.50" class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-slate-800 focus:outline-hidden focus:border-[#1e549e] focus:ring-2 focus:ring-[#1e549e]/10">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tiempo Estimado (Minutos)</label>
                        <div class="relative">
                            <input type="number" min="5" max="300" id="tiempo_estimado_minutos" name="tiempo_estimado_minutos" placeholder="30" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-800 focus:outline-hidden focus:border-[#1e549e] focus:ring-2 focus:ring-[#1e549e]/10">
                            <span class="absolute right-3.5 top-2.5 text-slate-400 text-xs font-bold">min</span>
                        </div>
                    </div>
                </div>

                {{-- COORDENADAS Y MAPA --}}
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-[#1e549e] uppercase tracking-wider flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px]">location_on</span>
                            Geolocalización & Cobertura
                        </span>
                        <span class="text-[11px] text-slate-500 font-medium">(Haz clic en el mapa para fijar)</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Latitud</label>
                            <input type="number" step="0.0000001" id="latitud" name="latitud" value="8.7622000" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-mono font-bold text-slate-800 focus:outline-hidden focus:border-[#1e549e]">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Longitud</label>
                            <input type="number" step="0.0000001" id="longitud" name="longitud" value="-70.4106000" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-mono font-bold text-slate-800 focus:outline-hidden focus:border-[#1e549e]">
                        </div>
                    </div>

                    {{-- Accesos rápidos de ubicación central --}}
                    <div class="flex items-center gap-2 pt-1">
                        <button type="button" onclick="usarUbicacionTiendaEnFormulario()" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:border-[#1e549e] hover:bg-blue-50/50 text-[#1e549e] text-[11px] font-bold shadow-2xs transition-colors">
                            <span class="material-symbols-outlined text-[15px]">storefront</span>
                            Centrar en mi Tienda
                        </button>
                        <button type="button" onclick="detectarYFijarUbicacionGPS(false)" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50/50 text-emerald-700 text-[11px] font-bold shadow-2xs transition-colors">
                            <span class="material-symbols-outlined text-[15px]">my_location</span>
                            Obtener GPS Actual
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Radio Cobertura (Km)</label>
                            <input type="number" step="0.1" min="0.2" max="100" id="radio_km" name="radio_km" value="3.0" oninput="actualizarRadioPreview(this.value)" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-bold text-slate-800 focus:outline-hidden focus:border-[#1e549e]">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Color del Círculo</label>
                            <div class="flex items-center gap-2">
                                <input type="color" id="color_mapa" name="color_mapa" value="#1e549e" class="size-8 rounded-lg border border-slate-200 cursor-pointer p-0.5">
                                <span id="color_hex_text" class="text-xs font-mono text-slate-500">#1e549e</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Descripción o Indicaciones de Ruta</label>
                    <textarea id="descripcion" name="descripcion" rows="2" placeholder="Detalles de sectores cubiertos, accesos o restricciones..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-hidden focus:border-[#1e549e]"></textarea>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" id="requiere_vehiculo_carga" name="requiere_vehiculo_carga" value="1" class="size-4 rounded text-[#1e549e] border-slate-300 focus:ring-[#1e549e]">
                        <span class="text-xs font-bold text-slate-700">Requiere Camioneta / Vehículo Pesado</span>
                    </label>

                    <div class="flex items-center gap-2">
                        <span id="activa_badge_preview" class="text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">Activa</span>
                        <label class="relative inline-flex items-center cursor-pointer select-none" title="Cambiar estado Activa / Pausada">
                            <input type="checkbox" id="activa" name="activa" value="1" checked onchange="actualizarToggleModal(this.checked)" class="sr-only peer">
                            <div class="w-11 h-6 bg-amber-400 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                        </label>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" onclick="cerrarModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" id="btnGuardar" class="px-5 py-2.5 rounded-xl bg-[#1e549e] hover:bg-[#164079] text-white text-xs font-bold shadow-md shadow-[#1e549e]/20 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        <span id="btnGuardarText">Guardar Zona</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
{{-- LEAFLET JS Y SWEETALERT2 --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // DATOS DE ZONAS Y CONFIGURACIÓN INYECTADOS
    const zonasIniciales = @json($zonas);
    const csrfToken = '{{ csrf_token() }}';
    const TIENDA_LAT_CONFIG = {{ $tiendaLat ?? 8.7622 }};
    const TIENDA_LNG_CONFIG = {{ $tiendaLng ?? -70.4106 }};

    // Ubicación central de la tienda (persistida en localStorage o provista por el backend)
    let tiendaCoords = {
        lat: parseFloat(localStorage.getItem('agro_tienda_lat')) || TIENDA_LAT_CONFIG,
        lng: parseFloat(localStorage.getItem('agro_tienda_lng')) || TIENDA_LNG_CONFIG
    };

    let map = null;
    let markers = {};
    let circles = {};
    let activePin = null;
    let previewCircle = null;
    let storeMarker = null;
    let isModalOpen = false;

    // Inicializar mapa de Leaflet
    document.addEventListener("DOMContentLoaded", () => {
        inicializarMapa();

        // Color picker sync
        document.getElementById('color_mapa').addEventListener('input', (e) => {
            document.getElementById('color_hex_text').innerText = e.target.value;
            if (previewCircle) {
                previewCircle.setStyle({ color: e.target.value, fillColor: e.target.value });
            }
        });
    });

    function inicializarMapa() {
        map = L.map('leafletMap', {
            center: [tiendaCoords.lat, tiendaCoords.lng],
            zoom: 13,
            zoomControl: true
        });

        // Capa de OpenStreetMap con estilo limpio
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap | Agrosuministros Rodríguez',
            maxZoom: 19
        }).addTo(map);

        setTimeout(() => {
            if (map) {
                map.invalidateSize();
            }
        }, 250);

        // Pin de la Tienda Principal (Centro de referencia)
        actualizarMarcadorTienda();

        // Renderizar zonas existentes
        zonasIniciales.forEach(zona => {
            dibujarZonaEnMapa(zona);
        });

        // Evento de clic en el mapa
        map.on('click', (e) => {
            const { lat, lng } = e.latlng;
            if (isModalOpen) {
                // Modo activo: ajusta las coordenadas del formulario
                fijarCoordenadasFormulario(lat, lng);
            } else {
                // Modo pasivo: NO colocar marcador huérfano; mostrar popup contextual
                mostrarPopupCrearZona(lat, lng);
            }
        });

        // Si no hay coordenadas personalizadas en localStorage, consultar GPS pasivamente
        if (!localStorage.getItem('agro_tienda_lat') && navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    tiendaCoords = { lat: pos.coords.latitude, lng: pos.coords.longitude };
                    localStorage.setItem('agro_tienda_lat', pos.coords.latitude);
                    localStorage.setItem('agro_tienda_lng', pos.coords.longitude);
                    actualizarMarcadorTienda();
                },
                () => {},
                { timeout: 7000 }
            );
        }
    }

    function actualizarMarcadorTienda() {
        const storeIcon = L.divIcon({
            html: '<div style="background-color: #0b1d38; color: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(11,29,56,0.4); border: 2.5px solid white;"><span class="material-symbols-outlined" style="font-size: 20px;">storefront</span></div>',
            className: '',
            iconSize: [36, 36],
            iconAnchor: [18, 18]
        });

        const popupHtml = `
            <div style="font-family: system-ui; padding: 4px; text-align: center; min-width: 190px;">
                <strong style="color: #0b1d38; font-size: 13px;">Agrosuministros Rodríguez C.A.</strong><br>
                <span style="font-size: 11px; color: #10b981; font-weight: 700;">📍 Sede Principal (Centro de Envíos)</span><br>
                <span style="font-size: 10px; color: #64748b; font-family: monospace;">${tiendaCoords.lat.toFixed(5)}, ${tiendaCoords.lng.toFixed(5)}</span>
                <div style="margin-top: 8px;">
                    <button onclick="abrirModalCrearConCoordenadas(${tiendaCoords.lat}, ${tiendaCoords.lng})" style="background: #1e549e; color: white; border: none; padding: 5px 10px; border-radius: 8px; font-size: 11px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 2px 5px rgba(30,84,158,0.25);">
                        <span class="material-symbols-outlined" style="font-size: 14px;">add_location_alt</span> Nueva Zona desde Aquí
                    </button>
                </div>
            </div>
        `;

        if (storeMarker) {
            storeMarker.setLatLng([tiendaCoords.lat, tiendaCoords.lng]);
            storeMarker.setPopupContent(popupHtml);
        } else {
            storeMarker = L.marker([tiendaCoords.lat, tiendaCoords.lng], { icon: storeIcon, zIndexOffset: 500 })
                .addTo(map)
                .bindPopup(popupHtml);
        }

        const textoCentro = document.getElementById('textoCentroTienda');
        if (textoCentro) {
            textoCentro.innerText = `Centro: Sede (${tiendaCoords.lat.toFixed(3)}, ${tiendaCoords.lng.toFixed(3)})`;
        }
    }

    function detectarYFijarUbicacionGPS(guardarEnServidor = false) {
        if (!navigator.geolocation) {
            Swal.fire({
                icon: 'warning',
                title: 'GPS no disponible',
                text: 'Tu navegador o dispositivo no soporta geolocalización por GPS.',
                confirmButtonColor: '#1e549e'
            });
            return;
        }

        const toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true
        });

        toast.fire({
            icon: 'info',
            title: 'Detectando tu ubicación GPS...'
        });

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                tiendaCoords = { lat, lng };
                localStorage.setItem('agro_tienda_lat', lat);
                localStorage.setItem('agro_tienda_lng', lng);

                actualizarMarcadorTienda();

                if (map) {
                    map.flyTo([lat, lng], 14, { duration: 1.2 });
                }

                if (isModalOpen) {
                    fijarCoordenadasFormulario(lat, lng);
                }

                if (guardarEnServidor) {
                    fetch('{{ route("admin.zonas_delivery.tienda_ubicacion") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ latitud: lat, longitud: lng })
                    })
                    .then(res => res.json())
                    .then(data => {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Ubicación de Tienda Actualizada!',
                            text: `Centro de envíos fijado en tu ubicación actual: (${lat.toFixed(5)}, ${lng.toFixed(5)})`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    })
                    .catch(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Ubicación Detectada',
                            text: `GPS fijado: (${lat.toFixed(5)}, ${lng.toFixed(5)})`,
                            timer: 1800,
                            showConfirmButton: false
                        });
                    });
                } else {
                    toast.fire({
                        icon: 'success',
                        title: 'Coordenadas GPS aplicadas al formulario.'
                    });
                }
            },
            (error) => {
                let errorMsg = 'No pudimos obtener la señal GPS.';
                if (error.code === error.PERMISSION_DENIED) {
                    errorMsg = 'Debes permitir el acceso a tu ubicación en los permisos del navegador.';
                } else if (error.code === error.TIMEOUT) {
                    errorMsg = 'El tiempo de espera para detectar tu GPS se agotó.';
                }
                Swal.fire({
                    icon: 'warning',
                    title: 'GPS no detectado',
                    text: errorMsg,
                    confirmButtonColor: '#1e549e'
                });
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }

    function mostrarPopupCrearZona(lat, lng) {
        const popupContent = `
            <div style="font-family: system-ui; padding: 4px; text-align: center; max-width: 210px;">
                <div style="display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 50%; background: #e5f2fb; color: #1e549e; margin-bottom: 5px;">
                    <span class="material-symbols-outlined" style="font-size: 18px;">add_location_alt</span>
                </div>
                <h4 style="font-weight: 800; font-size: 13px; color: #0b1d38; margin: 0 0 3px 0;">Nueva Zona de Envío</h4>
                <p style="font-size: 11px; color: #64748b; margin: 0 0 8px 0; line-height: 1.25;">
                    ¿Configurar una zona de delivery con centro en este punto?
                </p>
                <button onclick="abrirModalCrearConCoordenadas(${lat}, ${lng})" style="width: 100%; background: #1e549e; color: white; border: none; padding: 6px 10px; border-radius: 8px; font-size: 11px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 4px; box-shadow: 0 2px 6px rgba(30,84,158,0.25);">
                    <span class="material-symbols-outlined" style="font-size: 15px;">add_circle</span> Configurar Zona
                </button>
            </div>
        `;

        L.popup({ offset: [0, -10] })
            .setLatLng([lat, lng])
            .setContent(popupContent)
            .openOn(map);
    }

    function dibujarZonaEnMapa(zona) {
        const lat = parseFloat(zona.latitud) || tiendaCoords.lat;
        const lng = parseFloat(zona.longitud) || tiendaCoords.lng;
        const radioMetros = (parseFloat(zona.radio_km) || 3.0) * 1000;
        const color = zona.color_mapa || '#1e549e';

        // Círculo de cobertura
        const circle = L.circle([lat, lng], {
            radius: radioMetros,
            color: color,
            weight: 2,
            fillColor: color,
            fillOpacity: zona.activa ? 0.18 : 0.05
        }).addTo(map);

        // Marcador con ícono
        const pinIcon = L.divIcon({
            html: `<div style="background-color: ${color}; color: white; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 3px 8px rgba(0,0,0,0.25); border: 2px solid white;"><span class="material-symbols-outlined" style="font-size: 16px;">local_shipping</span></div>`,
            className: '',
            iconSize: [28, 28],
            iconAnchor: [14, 14]
        });

        const marker = L.marker([lat, lng], { icon: pinIcon }).addTo(map);
        
        const popupContent = `
            <div style="font-family: system-ui; padding: 2px;">
                <h4 style="font-weight: 800; font-size: 13px; color: #0b1d38; margin: 0 0 4px 0;">${zona.nombre_zona}</h4>
                <p style="font-size: 11px; margin: 0 0 2px 0;"><strong>Tarifa:</strong> $${parseFloat(zona.precio_delivery_usd).toFixed(2)}</p>
                <p style="font-size: 11px; margin: 0 0 2px 0;"><strong>Radio:</strong> ${parseFloat(zona.radio_km || 3).toFixed(1)} km</p>
                <p style="font-size: 11px; margin: 0 0 6px 0;"><strong>Estado:</strong> ${zona.activa ? '<span style="color: #10b981; font-weight: bold;">Activa</span>' : '<span style="color: #f59e0b; font-weight: bold;">Pausada</span>'}</p>
                <button onclick="abrirModalEditar(${JSON.stringify(zona).replace(/"/g, '&quot;')})" style="background: #1e549e; color: white; border: none; padding: 4px 8px; border-radius: 6px; font-size: 10.5px; font-weight: bold; cursor: pointer;">Editar Zona</button>
            </div>
        `;
        marker.bindPopup(popupContent);

        markers[zona.id] = marker;
        circles[zona.id] = circle;
    }

    function enfocarZona(id, lat, lng) {
        if (map) {
            map.flyTo([lat, lng], 13, { duration: 1 });
            if (markers[id]) {
                markers[id].openPopup();
            }
        }
    }

    function fijarCoordenadasFormulario(lat, lng) {
        document.getElementById('latitud').value = lat.toFixed(7);
        document.getElementById('longitud').value = lng.toFixed(7);

        // Mover o crear pin activo
        if (activePin) {
            activePin.setLatLng([lat, lng]);
        } else {
            const tempIcon = L.divIcon({
                html: '<div style="background-color: #ef4444; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(239,68,68,0.4); border: 2px solid white; animation: bounce 1s infinite;"><span class="material-symbols-outlined" style="font-size: 18px;">pin_drop</span></div>',
                className: '',
                iconSize: [30, 30],
                iconAnchor: [15, 30]
            });
            activePin = L.marker([lat, lng], { icon: tempIcon, draggable: true }).addTo(map);
            activePin.on('dragend', (e) => {
                const pos = e.target.getLatLng();
                document.getElementById('latitud').value = pos.lat.toFixed(7);
                document.getElementById('longitud').value = pos.lng.toFixed(7);
                actualizarRadioPreview(document.getElementById('radio_km').value);
            });
        }

        actualizarRadioPreview(document.getElementById('radio_km').value);
    }

    function actualizarRadioPreview(radioKm) {
        const lat = parseFloat(document.getElementById('latitud').value) || tiendaCoords.lat;
        const lng = parseFloat(document.getElementById('longitud').value) || tiendaCoords.lng;
        const radioMetros = (parseFloat(radioKm) || 3.0) * 1000;
        const color = document.getElementById('color_mapa').value || '#1e549e';

        if (previewCircle) {
            previewCircle.setLatLng([lat, lng]);
            previewCircle.setRadius(radioMetros);
            previewCircle.setStyle({ color: color, fillColor: color });
        } else {
            previewCircle = L.circle([lat, lng], {
                radius: radioMetros,
                color: color,
                weight: 2,
                dashArray: '6, 6',
                fillColor: color,
                fillOpacity: 0.15
            }).addTo(map);
        }
    }

    // ==========================================
    // MODALES Y CRUD AJAX
    // ==========================================
    function abrirModalCrear() {
        abrirModalCrearConCoordenadas(tiendaCoords.lat, tiendaCoords.lng);
    }

    function abrirModalCrearConCoordenadas(lat, lng) {
        if (map) {
            map.closePopup();
        }

        document.getElementById('formZona').reset();
        document.getElementById('zona_id').value = '';
        document.getElementById('modalTitle').innerText = 'Nueva Zona de Delivery';
        document.getElementById('btnGuardarText').innerText = 'Guardar Zona';
        
        const checkActiva = document.getElementById('activa');
        if (checkActiva) {
            checkActiva.checked = true;
            actualizarToggleModal(true);
        }

        document.getElementById('latitud').value = lat.toFixed(7);
        document.getElementById('longitud').value = lng.toFixed(7);
        document.getElementById('radio_km').value = '3.0';
        document.getElementById('color_mapa').value = '#1e549e';
        document.getElementById('color_hex_text').innerText = '#1e549e';

        fijarCoordenadasFormulario(lat, lng);
        if (map) {
            map.flyTo([lat, lng], 13, { duration: 0.8 });
        }
        mostrarModal();
    }

    function usarUbicacionTiendaEnFormulario() {
        fijarCoordenadasFormulario(tiendaCoords.lat, tiendaCoords.lng);
        if (map) {
            map.flyTo([tiendaCoords.lat, tiendaCoords.lng], 13, { duration: 0.8 });
        }
        const toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 1500
        });
        toast.fire({ icon: 'info', title: 'Centro fijado en la ubicación de la tienda.' });
    }

    function abrirModalEditar(zona) {
        if (map) {
            map.closePopup();
        }

        document.getElementById('zona_id').value = zona.id;
        document.getElementById('nombre_zona').value = zona.nombre_zona;
        document.getElementById('precio_delivery_usd').value = zona.precio_delivery_usd;
        document.getElementById('tiempo_estimado_minutos').value = zona.tiempo_estimado_minutos || '';
        document.getElementById('requiere_vehiculo_carga').checked = !!zona.requiere_vehiculo_carga;
        
        const checkActiva = document.getElementById('activa');
        if (checkActiva) {
            checkActiva.checked = !!zona.activa;
            actualizarToggleModal(!!zona.activa);
        }

        const lat = parseFloat(zona.latitud || tiendaCoords.lat);
        const lng = parseFloat(zona.longitud || tiendaCoords.lng);
        document.getElementById('latitud').value = lat.toFixed(7);
        document.getElementById('longitud').value = lng.toFixed(7);
        document.getElementById('radio_km').value = zona.radio_km || 3.0;
        document.getElementById('color_mapa').value = zona.color_mapa || '#1e549e';
        document.getElementById('color_hex_text').innerText = zona.color_mapa || '#1e549e';
        document.getElementById('descripcion').value = zona.descripcion || '';

        document.getElementById('modalTitle').innerText = `Editar Zona: ${zona.nombre_zona}`;
        document.getElementById('btnGuardarText').innerText = 'Actualizar Zona';

        fijarCoordenadasFormulario(lat, lng);
        enfocarZona(zona.id, lat, lng);
        mostrarModal();
    }

    function mostrarModal() {
        isModalOpen = true;
        const modal = document.getElementById('modalZona');
        const backdrop = document.getElementById('modalBackdrop');
        const panel = document.getElementById('modalPanel');

        const hintText = document.getElementById('mapHintText');
        if (hintText) {
            hintText.innerHTML = '<span class="text-emerald-700 font-bold">Modo activo:</span> Haz clic en el mapa o arrastra el marcador para fijar el centro';
        }

        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    function cerrarModal() {
        isModalOpen = false;
        const modal = document.getElementById('modalZona');
        const backdrop = document.getElementById('modalBackdrop');
        const panel = document.getElementById('modalPanel');

        const hintText = document.getElementById('mapHintText');
        if (hintText) {
            hintText.innerText = 'Haz clic en el mapa para añadir una zona o pulsa "Nueva Zona de Envío"';
        }

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 250);

        if (previewCircle) {
            map.removeLayer(previewCircle);
            previewCircle = null;
        }
        if (activePin) {
            map.removeLayer(activePin);
            activePin = null;
        }
        if (map) {
            map.closePopup();
        }
    }

    function guardarZona(e) {
        e.preventDefault();
        const id = document.getElementById('zona_id').value;
        const formData = new FormData(document.getElementById('formZona'));

        const url = id ? `/admin/zonas-delivery/${id}` : '{{ route("admin.zonas_delivery.store") }}';
        if (id) {
            formData.append('_method', 'PUT');
        }

        const btn = document.getElementById('btnGuardar');
        btn.disabled = true;

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Operación Exitosa!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'No se pudo guardar la zona.' });
            }
        })
        .catch(err => {
            btn.disabled = false;
            Swal.fire({ icon: 'error', title: 'Error de Red', text: 'Ocurrió un error al procesar la solicitud.' });
        });
    }

    function toggleEstadoZona(id) {
        fetch(`/admin/zonas-delivery/${id}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const badge = document.getElementById(`badge-estado-${id}`);
                const btnToggle = document.getElementById(`btn-toggle-${id}`);

                if (data.activa) {
                    if (badge) {
                        badge.innerText = 'Activa';
                        badge.className = 'text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200';
                    }
                    if (btnToggle) {
                        btnToggle.className = 'p-1.5 rounded-lg border transition-all text-emerald-600 bg-emerald-50 hover:bg-emerald-100 border-emerald-200 shadow-2xs hover:scale-105';
                        btnToggle.title = 'Zona Activa (Clic para pausar)';
                        btnToggle.innerHTML = '<span class="material-symbols-outlined text-[19px]">toggle_on</span>';
                    }
                } else {
                    if (badge) {
                        badge.innerText = 'Pausada';
                        badge.className = 'text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-300';
                    }
                    if (btnToggle) {
                        btnToggle.className = 'p-1.5 rounded-lg border transition-all text-amber-600 bg-amber-50 hover:bg-amber-100 border-amber-300 shadow-2xs hover:scale-105';
                        btnToggle.title = 'Zona Pausada (Clic para activar)';
                        btnToggle.innerHTML = '<span class="material-symbols-outlined text-[19px]">toggle_off</span>';
                    }
                }

                if (circles[id]) {
                    circles[id].setStyle({ fillOpacity: data.activa ? 0.18 : 0.05 });
                }

                actualizarKpiZonasActivas(data.activa);

                Swal.fire({
                    icon: 'success',
                    title: data.message,
                    timer: 1200,
                    showConfirmButton: false
                });
            }
        });
    }

    function actualizarKpiZonasActivas(fueActivada) {
        const kpi = document.getElementById('kpiZonasActivas');
        if (kpi) {
            let val = parseInt(kpi.innerText) || 0;
            kpi.innerText = fueActivada ? val + 1 : Math.max(0, val - 1);
        }
    }

    function actualizarToggleModal(estaActiva) {
        const badge = document.getElementById('activa_badge_preview');
        if (badge) {
            if (estaActiva) {
                badge.innerText = 'Activa';
                badge.className = 'text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200';
            } else {
                badge.innerText = 'Pausada';
                badge.className = 'text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-300';
            }
        }
    }

    function eliminarZona(id, nombre) {
        Swal.fire({
            title: `¿Eliminar "${nombre}"?`,
            text: 'Si la zona posee pedidos históricos, el sistema la pausará automáticamente para proteger la integridad.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, Eliminar',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed) {
                fetch(`/admin/zonas-delivery/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Completado',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => window.location.reload());
                    }
                });
            }
        });
    }

    function toggleMasterDelivery() {
        Swal.fire({
            title: '¿Modificar disponibilidad de Delivery?',
            text: 'Este cambio afectará en tiempo real la opción de envíos para todos los clientes en la tienda web.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1e549e',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed) {
                fetch('{{ route("admin.zonas_delivery.toggle_master") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (typeof window.enviarNotificacionNavegadorDelivery === 'function') {
                            window.enviarNotificacionNavegadorDelivery(data.delivery_activo);
                        }
                        Swal.fire({
                            icon: 'success',
                            title: '¡Actualizado!',
                            text: data.message,
                            timer: 1600,
                            showConfirmButton: false
                        }).then(() => window.location.reload());
                    } else {
                        Swal.fire('Error', data.message || 'No se pudo actualizar.', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error', 'No se pudo comunicar con el servidor.', 'error');
                });
            }
        });
    }
</script>
@endpush
@endsection
