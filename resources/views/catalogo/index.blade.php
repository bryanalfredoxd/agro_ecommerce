@extends('layouts.app')

@section('title', 'Catálogo de Insumos - Agrosuministros Rodríguez')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/catalogo.css') }}">
@endpush

@section('content')
<div class="bg-slate-50 min-h-screen font-sans animate-fade-in-up pb-16">

    {{-- 1. CABECERA CORPORATIVA Y ELEGANTE DEL CATÁLOGO --}}
    <div class="relative bg-gradient-to-r from-[#0a192f] via-[#112d4e] to-[#1e549e] text-white border-b border-[#3492cf]/20 py-8 md:py-10 mb-8 overflow-hidden">
        {{-- Efecto de fondo sutil --}}
        <div class="absolute -right-20 -bottom-20 w-96 h-96 rounded-full bg-[#3492cf]/10 blur-3xl pointer-events-none"></div>
        <div class="absolute left-1/4 -top-20 w-80 h-80 rounded-full bg-[#1e549e]/20 blur-3xl pointer-events-none"></div>

        <div class="layout-container px-4 sm:px-6 relative z-10">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-300 mb-3">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-[15px]">home</span> Inicio
                </a>
                <span class="text-slate-400">/</span>
                <span class="text-[#aad6f3] font-bold">Catálogo Oficial</span>
            </nav>

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-sm border border-white/15 text-[11px] font-bold text-[#aad6f3] uppercase tracking-wider mb-2.5">
                        <span class="size-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Inventario Oficial Disponible
                    </div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#3492cf] text-[32px] sm:text-[38px]">inventory_2</span>
                        Catálogo de <span class="text-[#aad6f3]">Insumos y Soluciones</span>
                    </h1>
                    <p class="text-sm sm:text-base text-slate-200 mt-2 max-w-2xl leading-relaxed">
                        Semillas certificadas, fertilizantes, medicina veterinaria, nutrición y herramientas con garantía de calidad y despacho nacional.
                    </p>
                </div>

                {{-- Badges de confianza en cabecera --}}
                <div class="flex flex-wrap sm:flex-nowrap items-center gap-3 shrink-0">
                    <div class="bg-white/10 backdrop-blur-md border border-white/15 px-4 py-3 rounded-2xl flex items-center gap-3 shadow-xs">
                        <div class="size-10 rounded-xl bg-[#3492cf]/20 flex items-center justify-center text-[#aad6f3]">
                            <span class="material-symbols-outlined text-[22px]">currency_exchange</span>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-300">Tasa Oficial BCV</div>
                            <div class="text-sm font-black text-white">1 USD = Bs. {{ number_format($tasaDolar, 2, ',', '.') }}</div>
                        </div>
                    </div>

                    @php
                        $deliveryActivoCatalogo = \App\Models\ConfiguracionTienda::deliveryHabilitado();
                    @endphp
                    <div class="hidden sm:flex bg-white/10 backdrop-blur-md border border-white/15 px-4 py-3 rounded-2xl items-center gap-3 shadow-xs">
                        <div class="size-10 rounded-xl {{ $deliveryActivoCatalogo ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300' }} flex items-center justify-center">
                            <span class="material-symbols-outlined text-[22px]">local_shipping</span>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-300">Despacho</div>
                            <div class="text-sm font-black text-white">{{ $deliveryActivoCatalogo ? 'Disponible' : 'No Disponible' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CONTENEDOR PRINCIPAL DEL CATÁLOGO --}}
    <div id="contenido-catalogo" class="layout-container px-4 sm:px-6 relative z-20">
        
        <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 items-start">

            {{-- 2. SIDEBAR DE FILTROS (PROFESIONAL Y ADAPTABLE) --}}
            <aside class="w-full lg:w-72 xl:w-80 shrink-0">
                
                {{-- Botón Móvil para abrir filtros --}}
                <button id="mobile-filter-btn" 
                        onclick="toggleFilters()" 
                        type="button"
                        class="lg:hidden w-full flex items-center justify-between bg-white text-slate-800 px-5 py-3.5 rounded-2xl shadow-xs font-bold mb-4 border border-slate-200 active:scale-98 transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[#1e549e] text-[22px]">tune</span>
                        <span class="text-sm font-bold">Filtros y Departamentos</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span id="mobile-filter-count" class="hidden px-2 py-0.5 rounded-full bg-[#1e549e] text-white text-xs font-black">0</span>
                        <span class="material-symbols-outlined text-slate-400 text-[20px]">expand_more</span>
                    </div>
                </button>

                {{-- Panel de Filtros --}}
                <div id="filters-panel" class="hidden lg:block bg-white rounded-2xl border border-slate-200/90 p-5 lg:sticky lg:top-24 transition-all duration-300 z-30 shadow-xs space-y-6">
                    
                    {{-- Cabecera del Panel --}}
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2 text-slate-800">
                            <span class="material-symbols-outlined text-[#1e549e] text-[20px]">filter_alt</span>
                            <h2 class="font-bold text-sm tracking-tight">Filtrar Productos</h2>
                        </div>
                        
                        <div id="clear-filters-container" class="{{ request()->hasAny(['categoria', 'marca', 'buscar']) ? '' : 'hidden' }}">
                            <button onclick="catalogoClearFilters(event)" 
                                    type="button"
                                    class="text-xs font-bold text-rose-600 hover:text-rose-700 hover:underline flex items-center gap-1 transition-colors">
                                <span class="material-symbols-outlined text-[15px]">restart_alt</span> Limpiar
                            </button>
                        </div>
                    </div>

                    {{-- Buscador Interno del Catálogo --}}
                    <div>
                        <label for="search-input" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Buscar en el catálogo</label>
                        <div class="relative group">
                            <input type="text" 
                                   name="buscar" 
                                   id="search-input"
                                   value="{{ request('buscar') }}" 
                                   autocomplete="off"
                                   class="w-full pl-10 pr-9 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-[#3492cf] focus:ring-2 focus:ring-[#3492cf]/20 transition-all text-sm font-medium text-slate-800 placeholder:text-slate-400 shadow-2xs" 
                                   placeholder="Nombre, principio o marca...">
                            <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 group-focus-within:text-[#1e549e] transition-colors text-[20px] pointer-events-none">search</span>
                            
                            {{-- Botón de limpiar texto de búsqueda --}}
                            <button type="button" 
                                    id="search-clear-btn" 
                                    class="{{ request('buscar') ? '' : 'hidden' }} absolute right-2.5 top-2.5 size-6 rounded-md flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 transition-colors"
                                    title="Borrar búsqueda">
                                <span class="material-symbols-outlined text-[16px]">close</span>
                            </button>
                        </div>
                    </div>

                    {{-- Departamentos / Categorías --}}
                    <div>
                        <div class="flex items-center justify-between mb-2.5">
                            <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px] text-[#3492cf]">category</span> Departamentos
                            </h3>
                        </div>

                        <nav class="space-y-1 max-h-[290px] overflow-y-auto pr-1.5 custom-scrollbar">
                            {{-- Opción: Todas las categorías --}}
                            <a href="#" 
                               data-filter="categoria" 
                               data-value="" 
                               class="filter-link group flex items-center justify-between px-3 py-2 rounded-xl text-xs transition-all duration-200 {{ !request('categoria') ? 'bg-[#1e549e] text-white font-bold shadow-2xs' : 'text-slate-700 hover:text-[#1e549e] hover:bg-[#e5f2fb]/60 font-medium' }}">
                                <span class="flex items-center gap-2 truncate">
                                    <span class="material-symbols-outlined text-[16px] shrink-0 opacity-80">grid_view</span>
                                    <span>Todas las categorías</span>
                                </span>
                                <span class="category-count-badge text-[10px] px-2 py-0.5 rounded-full font-bold {{ !request('categoria') ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-[#e5f2fb] group-hover:text-[#1e549e]' }}">
                                    {{ $totalProductosDisponibles ?? $productos->total() }}
                                </span>
                            </a>

                            {{-- Categorías dinámicas --}}
                            @foreach($categorias as $cat)
                                @php
                                    $esCatActiva = request('categoria') == $cat->id;
                                @endphp
                                <a href="#" 
                                   data-filter="categoria" 
                                   data-value="{{ $cat->id }}" 
                                   class="filter-link group flex items-center justify-between px-3 py-2 rounded-xl text-xs transition-all duration-200 {{ $esCatActiva ? 'bg-[#1e549e] text-white font-bold shadow-2xs' : 'text-slate-700 hover:text-[#1e549e] hover:bg-[#e5f2fb]/60 font-medium' }}">
                                    <span class="flex items-center gap-2 truncate">
                                        <span class="size-1.5 rounded-full shrink-0 {{ $esCatActiva ? 'bg-white' : 'bg-slate-300 group-hover:bg-[#3492cf]' }}"></span>
                                        <span class="truncate">{{ $cat->nombre }}</span>
                                    </span>
                                    <span class="category-count-badge text-[10px] px-2 py-0.5 rounded-full font-bold {{ $esCatActiva ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-[#e5f2fb] group-hover:text-[#1e549e]' }}">
                                        {{ $cat->productos_count ?? 0 }}
                                    </span>
                                </a>
                            @endforeach
                        </nav>
                    </div>

                    {{-- Marcas Principales --}}
                    <div>
                        <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-[#3492cf]">verified</span> Marcas
                        </h3>
                        
                        <div class="flex flex-wrap gap-1.5 max-h-[190px] overflow-y-auto pr-1 custom-scrollbar">
                            @foreach($marcas as $marca)
                                @php
                                    $esMarcaActiva = request('marca') == $marca->id;
                                @endphp
                                <a href="#" 
                                   data-filter="marca" 
                                   data-value="{{ $marca->id }}" 
                                   class="filter-link inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold border transition-all duration-200 {{ $esMarcaActiva ? 'bg-[#1e549e] text-white border-[#1e549e] shadow-2xs' : 'bg-slate-50 text-slate-600 border-slate-200 hover:border-[#3492cf] hover:text-[#1e549e] hover:bg-white' }}">
                                    <span class="brand-check-icon material-symbols-outlined text-[13px] {{ $esMarcaActiva ? '' : 'hidden' }}">check</span>
                                    <span class="truncate max-w-[120px]">{{ $marca->nombre }}</span>
                                    <span class="text-[10px] opacity-75 font-semibold">({{ $marca->productos_count ?? 0 }})</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Banner de Ayuda / Solicitar Producto en Sidebar --}}
                    <div class="pt-2 border-t border-slate-100">
                        <div class="bg-gradient-to-br from-[#e5f2fb] to-white p-4 rounded-xl border border-[#aad6f3]/50">
                            <div class="flex items-center gap-2 text-[#1e549e] font-bold text-xs mb-1">
                                <span class="material-symbols-outlined text-[18px]">support_agent</span>
                                ¿No encuentras tu producto?
                            </div>
                            <p class="text-[11px] text-slate-500 leading-relaxed mb-3">
                                Si necesitas un insumo o medicamento específico, solicítalo directamente a nuestro equipo.
                            </p>
                            <button type="button" 
                                    onclick="abrirModalSolicitarProducto()"
                                    class="w-full py-2.5 px-3 bg-white hover:bg-[#1e549e] text-[#1e549e] hover:text-white border border-[#3492cf]/30 hover:border-[#1e549e] rounded-xl text-xs font-black transition-all shadow-2xs hover:shadow-md flex items-center justify-center gap-1.5 cursor-pointer active:scale-95 group">
                                <span class="material-symbols-outlined text-[17px] group-hover:scale-110 transition-transform">box_add</span>
                                Solicitar Insumo
                            </button>
                        </div>
                    </div>

                </div>
            </aside>

            {{-- 3. CONTENEDOR PRINCIPAL DE PRODUCTOS --}}
            <main class="flex-1 w-full min-w-0 relative">
                
                {{-- Overlay de Carga Ajax --}}
                <div id="loading-overlay" class="absolute inset-0 bg-slate-50/80 backdrop-blur-xs z-40 flex items-start justify-center pt-24 hidden rounded-2xl">
                    <div class="bg-white p-5 rounded-2xl shadow-xl border border-slate-200/80 flex items-center gap-3">
                        <div class="size-6 border-3 border-slate-200 border-t-[#1e549e] rounded-full animate-spin"></div>
                        <p class="text-slate-800 font-bold text-sm">Actualizando catálogo...</p>
                    </div>
                </div>
                
                {{-- Contenedor donde se inyecta la vista parcial --}}
                <div id="products-container" class="transition-opacity duration-200">
                    @include('catalogo.partials.products', [
                        'productos' => $productos,
                        'tasaDolar' => $tasaDolar,
                        'favoritosIds' => $favoritosIds,
                        'categorias' => $categorias,
                        'marcas' => $marcas,
                        'totalProductosDisponibles' => $totalProductosDisponibles ?? $productos->total()
                    ])
                </div>
            </main>

        </div>
    </div>
</div>

{{-- ================= TOAST NOTIFICATION ================= --}}
<div id="toast-notification" class="fixed bottom-6 right-6 z-[100] transform transition-all duration-300 translate-y-24 opacity-0 pointer-events-none">
    <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-slate-200/80 p-4 flex items-center gap-3.5 min-w-[320px] max-w-md">
        <div id="toast-icon-container" class="size-11 rounded-xl flex items-center justify-center shrink-0 transition-colors">
            <span id="toast-icon" class="material-symbols-outlined text-2xl">check_circle</span>
        </div>
        <div class="flex-1 min-w-0">
            <h4 id="toast-title" class="font-bold text-slate-900 text-sm">Notificación</h4>
            <p id="toast-message" class="text-xs font-medium text-slate-500 mt-0.5 truncate">Mensaje</p>
        </div>
        <button onclick="hideToast()" class="text-slate-400 hover:text-slate-700 transition-colors p-1 rounded-lg hover:bg-slate-100">
            <span class="material-symbols-outlined text-lg">close</span>
        </button>
    </div>
</div>

{{-- ================= MODAL: SOLICITAR PRODUCTO ================= --}}
<div id="modal-solicitar-producto" class="fixed inset-0 z-[120] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-[#0f2347]/60 backdrop-blur-xs transition-opacity" onclick="cerrarModalSolicitarProducto()"></div>
    
    {{-- Dialog --}}
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end sm:items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg animate-fade-in-up border border-slate-100">
                
                <form id="form-solicitar-producto" action="{{ route('catalogo.solicitar') }}" method="POST" onsubmit="enviarSolicitudProducto(event)">
                    @csrf
                    <div class="bg-white px-6 pb-6 pt-7">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2.5">
                                <div class="size-10 rounded-xl bg-[#e5f2fb] text-[#1e549e] flex items-center justify-center shadow-2xs">
                                    <span class="material-symbols-outlined text-[24px]">box_add</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-slate-800" id="modal-title">
                                        Solicitar Insumo o Producto
                                    </h3>
                                    <p class="text-[11px] text-slate-400 font-semibold">Atención y abastecimiento a medida</p>
                                </div>
                            </div>
                            <button type="button" 
                                    onclick="cerrarModalSolicitarProducto()" 
                                    class="text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 rounded-full size-8 flex items-center justify-center transition-colors">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                            </button>
                        </div>
                        
                        <p class="text-xs text-slate-500 font-medium leading-relaxed mb-4">
                            ¿No encuentras el insumo, medicamento o herramienta que buscas? Déjanos tu requerimiento y nuestro equipo de compras se encargará de conseguirlo para ti.
                        </p>

                        {{-- Contenedor de Errores AJAX --}}
                        <div id="modal-solicitud-errores" class="hidden mb-4 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold space-y-1"></div>

                        @auth
                            {{-- Info del Usuario Autenticado --}}
                            <div class="mb-4 p-3 rounded-xl bg-[#f8fafc] border border-slate-200/80 flex items-center gap-3">
                                <div class="size-9 rounded-full bg-[#1e549e]/10 text-[#1e549e] flex items-center justify-center font-black text-xs shrink-0">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()->name }}</div>
                                    <div class="text-[11px] text-slate-500 truncate">{{ Auth::user()->email }} {{ Auth::user()->telefono ? '• ' . Auth::user()->telefono : '' }}</div>
                                </div>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                                    <span class="size-1.5 rounded-full bg-emerald-500"></span> Conectado
                                </span>
                            </div>
                        @else
                            {{-- Campos para Visitante --}}
                            <div class="mb-4 p-3.5 rounded-2xl bg-amber-50/70 border border-amber-200/70">
                                <div class="flex items-center gap-1.5 text-amber-800 text-xs font-bold mb-2">
                                    <span class="material-symbols-outlined text-[17px]">contact_phone</span>
                                    Datos de Contacto (Solicitud como Visitante)
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label for="nombre_contacto" class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                                            Tu Nombre / Empresa <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="text" 
                                               name="nombre_contacto" 
                                               id="nombre_contacto" 
                                               placeholder="Ej: Carlos Mendoza" 
                                               class="block w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:border-[#3492cf] focus:ring-2 focus:ring-[#3492cf]/20 text-slate-800 placeholder:text-slate-400 transition-all shadow-2xs">
                                    </div>
                                    <div>
                                        <label for="telefono_contacto" class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                                            Teléfono / WhatsApp <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="text" 
                                               name="telefono_contacto" 
                                               id="telefono_contacto" 
                                               placeholder="Ej: 0414-1234567" 
                                               class="block w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:border-[#3492cf] focus:ring-2 focus:ring-[#3492cf]/20 text-slate-800 placeholder:text-slate-400 transition-all shadow-2xs">
                                    </div>
                                </div>
                            </div>
                        @endauth
                        
                        <div class="space-y-4">
                            <div>
                                <label for="nombre_producto" class="block text-[11px] font-black text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nombre del Insumo o Producto <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       name="nombre_producto" 
                                       id="nombre_producto" 
                                       required 
                                       placeholder="Ej: Urea perlada 46%, Ivermectina 1%, Alimento iniciador..." 
                                       class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-[#3492cf] focus:ring-2 focus:ring-[#3492cf]/20 text-slate-800 placeholder:text-slate-400 transition-all shadow-2xs focus:bg-white">
                            </div>
                            
                            <div>
                                <label for="descripcion_adicional" class="block text-[11px] font-black text-slate-700 uppercase tracking-wider mb-1.5">
                                    Detalles o Presentación (Opcional)
                                </label>
                                <textarea name="descripcion_adicional" 
                                          id="descripcion_adicional" 
                                          rows="3" 
                                          placeholder="Presentación preferida (saco 50kg, frasco 100ml), marca requerida o cantidad estimada..." 
                                          class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-[#3492cf] focus:ring-2 focus:ring-[#3492cf]/20 text-slate-800 placeholder:text-slate-400 transition-all shadow-2xs resize-none focus:bg-white"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex flex-col-reverse sm:flex-row justify-end gap-2.5">
                        <button type="button" 
                                onclick="cerrarModalSolicitarProducto()" 
                                class="w-full sm:w-auto inline-flex justify-center items-center rounded-xl bg-white px-5 py-2.5 text-xs font-bold text-slate-600 border border-slate-200 hover:bg-slate-100 transition-colors cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit" 
                                id="btn-submit-solicitud"
                                class="w-full sm:w-auto inline-flex justify-center items-center gap-1.5 rounded-xl bg-[#1e549e] hover:bg-[#3492cf] text-white px-5 py-2.5 text-xs font-black shadow-xs transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                            <span id="btn-submit-solicitud-icon" class="material-symbols-outlined text-[17px]">send</span>
                            <span id="btn-submit-solicitud-text">Enviar Solicitud</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        window.CatalogoConfig = {
            routes: {
                addCart: "{{ route('carrito.add') }}",
                login: "{{ route('login') }}",
                catalogo: "{{ route('catalogo') }}"
            },
            filters: {
                buscar: "{{ request('buscar') }}",
                categoria: "{{ request('categoria') }}",
                marca: "{{ request('marca') }}",
                orden: "{{ request('orden', 'reciente') }}"
            },
            isAuth: {{ Auth::check() ? 'true' : 'false' }},
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        };

        // ==============================================================
        // 📦 GESTIÓN DEL MODAL DE SOLICITAR PRODUCTO
        // ==============================================================
        window.abrirModalSolicitarProducto = function(nombre = '') {
            const modal = document.getElementById('modal-solicitar-producto');
            const inputNombre = document.getElementById('nombre_producto');
            const errorDiv = document.getElementById('modal-solicitud-errores');
            
            if (errorDiv) {
                errorDiv.classList.add('hidden');
                errorDiv.innerHTML = '';
            }

            if (nombre && inputNombre) {
                inputNombre.value = nombre;
            }

            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                
                setTimeout(() => {
                    if (inputNombre && !nombre) {
                        inputNombre.focus();
                    } else {
                        const inputContacto = document.getElementById('nombre_contacto');
                        if (inputContacto) inputContacto.focus();
                    }
                }, 100);
            }
        };

        window.cerrarModalSolicitarProducto = function() {
            const modal = document.getElementById('modal-solicitar-producto');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        };

        // Escuchar tecla Escape para cerrar
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.cerrarModalSolicitarProducto();
            }
        });

        // Envío interactivo por AJAX
        window.enviarSolicitudProducto = function(event) {
            event.preventDefault();
            const form = event.target;
            const submitBtn = document.getElementById('btn-submit-solicitud');
            const submitIcon = document.getElementById('btn-submit-solicitud-icon');
            const submitText = document.getElementById('btn-submit-solicitud-text');
            const errorDiv = document.getElementById('modal-solicitud-errores');

            if (errorDiv) {
                errorDiv.classList.add('hidden');
                errorDiv.innerHTML = '';
            }

            // Estado de carga
            if (submitBtn) submitBtn.disabled = true;
            if (submitIcon) submitIcon.textContent = 'progress_activity';
            if (submitIcon) submitIcon.classList.add('animate-spin');
            if (submitText) submitText.textContent = 'Enviando...';

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.CatalogoConfig.csrfToken
                },
                body: formData
            })
            .then(async (response) => {
                const data = await response.json();
                if (!response.ok) {
                    throw data;
                }
                return data;
            })
            .then((data) => {
                // Éxito
                form.reset();
                window.cerrarModalSolicitarProducto();
                if (typeof showToast === 'function') {
                    showToast(data.message || '¡Solicitud enviada correctamente!', 'success');
                } else {
                    alert(data.message || '¡Solicitud enviada correctamente!');
                }
            })
            .catch((err) => {
                console.error('Error al enviar solicitud de producto:', err);
                let mensaje = 'No fue posible registrar la solicitud.';
                if (err && err.errors) {
                    const list = Object.values(err.errors).flat();
                    if (errorDiv) {
                        errorDiv.innerHTML = list.map(m => `<div>• ${m}</div>`).join('');
                        errorDiv.classList.remove('hidden');
                    }
                    if (typeof showToast === 'function') {
                        showToast(list[0] || 'Por favor verifica los campos', 'error');
                    }
                } else if (err && err.message) {
                    if (errorDiv) {
                        errorDiv.innerHTML = `<div>• ${err.message}</div>`;
                        errorDiv.classList.remove('hidden');
                    }
                    if (typeof showToast === 'function') {
                        showToast(err.message, 'error');
                    }
                } else {
                    if (typeof showToast === 'function') {
                        showToast('Error de conexión al enviar la solicitud', 'error');
                    }
                }
            })
            .finally(() => {
                if (submitBtn) submitBtn.disabled = false;
                if (submitIcon) {
                    submitIcon.textContent = 'send';
                    submitIcon.classList.remove('animate-spin');
                }
                if (submitText) submitText.textContent = 'Enviar Solicitud';
            });
        };

        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(() => {
                    if (typeof showToast === 'function') {
                        showToast("{{ session('success') }}", 'success');
                    }
                }, 300);
            });
        @endif
    </script>
    <script src="{{ asset('js/catalogo.js') }}"></script>
@endpush