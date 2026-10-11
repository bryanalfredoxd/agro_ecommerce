@extends('layouts.admin')

@section('title', 'Solicitudes de Insumos - Agrosuministros Rodríguez')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    {{-- SIDEBAR --}}
    @include('admin.partials.sidebar')

    {{-- CONTENIDO PRINCIPAL --}}
    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up pb-16">
            
            {{-- ENCABEZADO DE PÁGINA --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-3.5">
                    <a href="{{ route('admin.dashboard') }}" class="size-10 flex-shrink-0 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:text-blue-600 hover:border-blue-200 transition-colors shadow-2xs">
                        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#1e549e] text-[28px]">box_add</span>
                            <h2 class="text-2xl sm:text-3xl font-black text-agro-dark">Solicitudes de Insumos</h2>
                        </div>
                        <p class="text-xs sm:text-sm text-gray-500 mt-0.5 font-medium">
                            Requerimientos especiales, medicinas y productos pedidos por clientes o visitantes web.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 flex-wrap">
                    <button type="button" 
                            id="btn-refrescar" 
                            onclick="cargarSolicitudes()" 
                            class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs font-bold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 shadow-2xs transition-all active:scale-95 cursor-pointer">
                        <span id="btn-refrescar-icon" class="material-symbols-outlined text-[18px] text-[#1e549e]">sync</span>
                        <span>Refrescar</span>
                    </button>

                    @if(Auth::user()?->tienePermiso('gestionar_productos'))
                        <a href="{{ route('admin.productos.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-black text-white bg-[#1e549e] hover:bg-[#164079] shadow-sm transition-all hover:scale-105 active:scale-95">
                            <span class="material-symbols-outlined text-[18px]">add_circle</span>
                            <span>Nuevo en Catálogo</span>
                        </a>
                    @endif
                </div>
            </div>

            {{-- KPI METRICS CARDS --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 mb-6">
                
                {{-- KPI 1: Total --}}
                <div onclick="filtrarPorEstado('todos')" 
                     class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all cursor-pointer group hover:border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total</span>
                        <div class="size-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">inventory</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span id="kpi-total" class="text-2xl font-black text-gray-900 leading-none">{{ $stats['total'] }}</span>
                        <span class="text-[10px] text-gray-400 font-bold uppercase">Peticiones</span>
                    </div>
                </div>

                {{-- KPI 2: Por Revisar (Pendientes) --}}
                <div onclick="filtrarPorEstado('pendiente')" 
                     class="bg-white p-4 sm:p-5 rounded-2xl border {{ $stats['pendientes'] > 0 ? 'border-amber-300 ring-2 ring-amber-400/20 bg-amber-50/20' : 'border-gray-100' }} shadow-2xs hover:shadow-md transition-all cursor-pointer group">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold {{ $stats['pendientes'] > 0 ? 'text-amber-800' : 'text-gray-400' }} uppercase tracking-wider">
                            Por Revisar
                        </span>
                        <div class="size-8 rounded-xl {{ $stats['pendientes'] > 0 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">pending_actions</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span id="kpi-pendientes" class="text-2xl font-black {{ $stats['pendientes'] > 0 ? 'text-amber-800' : 'text-gray-900' }} leading-none">
                            {{ $stats['pendientes'] }}
                        </span>
                        @if($stats['pendientes'] > 0)
                            <span class="text-[10px] text-amber-700 font-black uppercase bg-amber-100 px-1.5 py-0.5 rounded animate-pulse">¡Nuevas!</span>
                        @else
                            <span class="text-[10px] text-emerald-600 font-bold uppercase">Al día</span>
                        @endif
                    </div>
                </div>

                {{-- KPI 3: En Revisión --}}
                <div onclick="filtrarPorEstado('revisado')" 
                     class="bg-white p-4 sm:p-5 rounded-2xl border border-blue-100 shadow-2xs hover:shadow-md transition-all cursor-pointer group hover:border-blue-200">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">En Revisión</span>
                        <div class="size-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">search_check</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span id="kpi-revisados" class="text-2xl font-black text-blue-900 leading-none">{{ $stats['revisados'] }}</span>
                        <span class="text-[10px] text-blue-500 font-bold uppercase">Cotizando</span>
                    </div>
                </div>

                {{-- KPI 4: Agregados al Catálogo --}}
                <div onclick="filtrarPorEstado('agregado')" 
                     class="bg-white p-4 sm:p-5 rounded-2xl border border-emerald-100 shadow-2xs hover:shadow-md transition-all cursor-pointer group hover:border-emerald-200">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Agregados</span>
                        <div class="size-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span id="kpi-agregados" class="text-2xl font-black text-emerald-900 leading-none">{{ $stats['agregados'] }}</span>
                        <span class="text-[10px] text-emerald-500 font-bold uppercase">En Tienda</span>
                    </div>
                </div>

                {{-- KPI 5: Descartadas --}}
                <div onclick="filtrarPorEstado('rechazado')" 
                     class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all cursor-pointer group hover:border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Descartadas</span>
                        <div class="size-8 rounded-xl bg-gray-50 text-gray-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">cancel</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span id="kpi-rechazados" class="text-2xl font-black text-gray-700 leading-none">{{ $stats['rechazados'] }}</span>
                        <span class="text-[10px] text-gray-400 font-bold uppercase">Archivadas</span>
                    </div>
                </div>
            </div>

            {{-- PANEL UNIFICADO DE FILTROS Y CONTROLES --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-2 mb-6">
                
                {{-- 1. TABS SUPERIORES DE ESTADOS --}}
                <div class="flex overflow-x-auto custom-scrollbar border-b border-gray-100 px-4 pt-2 gap-2 sm:gap-4" id="tabs-estados">
                    
                    {{-- Tab: Todas --}}
                    <button type="button" 
                            onclick="filtrarPorEstado('todos')" 
                            data-tab="todos"
                            class="status-tab active pb-3 px-3 text-xs sm:text-sm font-black border-b-2 border-[#1e549e] text-[#1e549e] whitespace-nowrap flex items-center gap-2 transition-colors cursor-pointer">
                        <span>Todas</span>
                        <span id="tab-badge-todos" class="px-2 py-0.5 rounded-full text-[10px] bg-blue-100 text-blue-800 font-black">{{ $stats['total'] }}</span>
                    </button>
                    
                    {{-- Tab: Pendientes --}}
                    <button type="button" 
                            onclick="filtrarPorEstado('pendiente')" 
                            data-tab="pendiente"
                            class="status-tab pb-3 px-3 text-xs sm:text-sm font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-800 whitespace-nowrap flex items-center gap-2 transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-[16px] text-amber-500">pending_actions</span>
                        <span>Por Revisar</span>
                        <span id="tab-badge-pendientes" class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $stats['pendientes'] > 0 ? 'bg-amber-500 text-slate-900 animate-pulse' : 'bg-gray-100 text-gray-600' }}">{{ $stats['pendientes'] }}</span>
                    </button>
                    
                    {{-- Tab: En Revisión --}}
                    <button type="button" 
                            onclick="filtrarPorEstado('revisado')" 
                            data-tab="revisado"
                            class="status-tab pb-3 px-3 text-xs sm:text-sm font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-800 whitespace-nowrap flex items-center gap-2 transition-colors cursor-pointer">
                        <span>En Revisión</span>
                        <span id="tab-badge-revisados" class="px-2 py-0.5 rounded-full text-[10px] bg-blue-50 text-blue-700 font-bold border border-blue-200/60">{{ $stats['revisados'] }}</span>
                    </button>
                    
                    {{-- Tab: Agregadas --}}
                    <button type="button" 
                            onclick="filtrarPorEstado('agregado')" 
                            data-tab="agregado"
                            class="status-tab pb-3 px-3 text-xs sm:text-sm font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-800 whitespace-nowrap flex items-center gap-2 transition-colors cursor-pointer">
                        <span>Agregadas al Catálogo</span>
                        <span id="tab-badge-agregados" class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-100 text-emerald-800 font-bold">{{ $stats['agregados'] }}</span>
                    </button>
                    
                    {{-- Tab: Descartadas --}}
                    <button type="button" 
                            onclick="filtrarPorEstado('rechazado')" 
                            data-tab="rechazado"
                            class="status-tab pb-3 px-3 text-xs sm:text-sm font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-800 whitespace-nowrap flex items-center gap-2 transition-colors cursor-pointer">
                        <span>Descartadas</span>
                        <span id="tab-badge-rechazados" class="px-2 py-0.5 rounded-full text-[10px] bg-gray-100 text-gray-600 font-bold">{{ $stats['rechazados'] }}</span>
                    </button>
                </div>

                {{-- 2. TOOLBAR INFERIOR: BUSCADOR EN VIVO Y SELECTORES --}}
                <div class="p-3.5 sm:p-4 bg-gray-50/70 rounded-b-2xl border-t border-gray-100 flex flex-col lg:flex-row gap-3 items-stretch lg:items-center justify-between">
                    
                    {{-- Buscador en Vivo --}}
                    <div class="flex-1 relative group">
                        <span class="material-symbols-outlined absolute left-3.5 top-2.5 text-gray-400 group-focus-within:text-[#1e549e] text-[20px] transition-colors pointer-events-none">search</span>
                        
                        <input type="text" 
                               id="searchInput" 
                               autocomplete="off"
                               oninput="debounceBuscar()" 
                               class="w-full pl-10 pr-20 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#1e549e]/15 focus:border-[#1e549e] transition-all outline-none font-medium text-gray-800 placeholder-gray-400 shadow-2xs" 
                               placeholder="Buscar por insumo, cliente, teléfono, notas o email...">
                        
                        {{-- Acciones dentro del buscador --}}
                        <div class="absolute right-2.5 top-2.5 flex items-center gap-1.5">
                            <span id="search_spinner" style="display: none;" class="material-symbols-outlined text-[#1e549e] text-[18px] animate-spin">progress_activity</span>
                            
                            <button type="button" 
                                    id="btn_clear_search" 
                                    onclick="limpiarBuscador()" 
                                    style="display: none;"
                                    class="size-6 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-800 flex items-center justify-center transition-colors cursor-pointer" 
                                    title="Limpiar búsqueda (Escape)">
                                <span class="material-symbols-outlined text-[15px]">close</span>
                            </button>
                            
                            <kbd class="hidden sm:inline-block px-1.5 py-0.5 text-[9px] font-mono font-bold text-gray-400 bg-gray-50 border border-gray-200 rounded shadow-2xs">ESC</kbd>
                        </div>
                    </div>

                    {{-- Filtros Secundarios --}}
                    <div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap">
                        
                        {{-- Filtro: Tipo de Solicitante --}}
                        <div class="w-full sm:w-auto flex items-center gap-1.5 px-3 py-2 bg-white border border-gray-200 rounded-xl shadow-2xs focus-within:border-[#1e549e] focus-within:ring-2 focus-within:ring-[#1e549e]/15 transition-all">
                            <span class="material-symbols-outlined text-gray-400 text-[18px] shrink-0 pointer-events-none">group</span>
                            <select id="tipoClienteSelect" 
                                    onchange="cambiarTipoCliente(this.value)" 
                                    class="w-full sm:w-auto bg-transparent border-none p-0 outline-none focus:ring-0 text-xs sm:text-sm font-bold text-gray-700 cursor-pointer">
                                <option value="">Todos los Solicitantes</option>
                                <option value="registrado">Solo Clientes Registrados</option>
                                <option value="visitante">Solo Visitantes Web</option>
                            </select>
                        </div>

                        {{-- Filtro: Orden --}}
                        <div class="w-full sm:w-auto flex items-center gap-1.5 px-3 py-2 bg-white border border-gray-200 rounded-xl shadow-2xs focus-within:border-[#1e549e] focus-within:ring-2 focus-within:ring-[#1e549e]/15 transition-all">
                            <span class="material-symbols-outlined text-gray-400 text-[18px] shrink-0 pointer-events-none">swap_vert</span>
                            <select id="ordenSelect" 
                                    onchange="cambiarOrden(this.value)" 
                                    class="w-full sm:w-auto bg-transparent border-none p-0 outline-none focus:ring-0 text-xs sm:text-sm font-bold text-gray-700 cursor-pointer">
                                <option value="reciente">Más recientes</option>
                                <option value="antiguo">Más antiguos</option>
                            </select>
                        </div>

                        {{-- Botón Limpiar Todo --}}
                        <button type="button" 
                                id="btn-limpiar-filtros" 
                                onclick="limpiarTodosFiltros()" 
                                style="display: none;" 
                                class="px-3 py-2 rounded-xl text-xs font-bold bg-gray-200 hover:bg-gray-300 text-gray-700 transition-colors flex items-center gap-1 shrink-0 cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">filter_alt_off</span>
                            <span class="hidden sm:inline">Restablecer</span>
                        </button>
                    </div>

                </div>
            </div>

            {{-- CONTENEDOR DE LA TABLA --}}
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden relative" id="contenedor-tabla">
                {{-- Spinner Overlay durante recarga --}}
                <div id="tabla-loading" class="hidden absolute inset-0 bg-white/75 backdrop-blur-2xs z-20 flex flex-col items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-3xl text-[#1e549e] animate-spin">progress_activity</span>
                    <span class="text-xs font-bold text-gray-600">Cargando solicitudes...</span>
                </div>

                {{-- Tabla Parcial Inyectable --}}
                <div id="tabla-solicitudes-wrapper">
                    @include('admin.solicitudes.partials._table', ['solicitudes' => $solicitudes])
                </div>
            </div>

        </div>
    </main>
</div>

{{-- Contenedor de Toasts Flotantes --}}
<div id="toast-container" class="fixed bottom-6 right-6 z-[999] flex flex-col gap-2"></div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let currentEstado = 'todos';
    let currentBuscar = '';
    let currentTipoCliente = '';
    let currentOrden = 'reciente';
    let debounceTimer = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // ==========================================
    // 🔔 NOTIFICACIONES TOAST FLOTANTES
    // ==========================================
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if(!container) return;
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-emerald-600' : (type === 'error' ? 'bg-red-600' : 'bg-blue-600');
        const icon = type === 'success' ? 'check_circle' : (type === 'error' ? 'error' : 'info');

        toast.className = `flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-2xl text-white transform translate-y-10 opacity-0 transition-all duration-300 ${bgColor}`;
        toast.innerHTML = `<span class="material-symbols-outlined text-[20px] shrink-0">${icon}</span><p class="font-bold text-xs">${message}</p>`;

        container.appendChild(toast);
        setTimeout(() => toast.classList.remove('translate-y-10', 'opacity-0'), 10);
        setTimeout(() => {
            toast.classList.add('translate-y-10', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }

    // ==========================================
    // 🔍 BUSCADOR CON DEBOUNCE Y TECLADO
    // ==========================================
    function debounceBuscar() {
        const input = document.getElementById('searchInput');
        const clearBtn = document.getElementById('btn_clear_search');
        const spinner = document.getElementById('search_spinner');
        
        currentBuscar = input.value.trim();

        if (clearBtn) {
            clearBtn.style.display = currentBuscar.length > 0 ? 'flex' : 'none';
        }
        actualizarBotonLimpiarFiltros();

        if (spinner) spinner.style.display = 'inline-block';
        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(() => {
            cargarSolicitudes();
        }, 350);
    }

    function limpiarBuscador() {
        const input = document.getElementById('searchInput');
        const clearBtn = document.getElementById('btn_clear_search');
        if (input) input.value = '';
        if (clearBtn) clearBtn.style.display = 'none';
        currentBuscar = '';
        actualizarBotonLimpiarFiltros();
        cargarSolicitudes();
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const input = document.getElementById('searchInput');
            if (input && input.value !== '') {
                limpiarBuscador();
            }
        }
    });

    // ==========================================
    // 📑 GESTIÓN DE TABS Y FILTROS
    // ==========================================
    function filtrarPorEstado(estado) {
        currentEstado = estado;
        
        // Actualizar clases de los tabs
        document.querySelectorAll('.status-tab').forEach(tab => {
            const tabEstado = tab.getAttribute('data-tab');
            if (tabEstado === estado) {
                tab.className = 'status-tab active pb-3 px-3 text-xs sm:text-sm font-black border-b-2 border-[#1e549e] text-[#1e549e] whitespace-nowrap flex items-center gap-2 transition-colors cursor-pointer';
            } else {
                tab.className = 'status-tab pb-3 px-3 text-xs sm:text-sm font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-800 whitespace-nowrap flex items-center gap-2 transition-colors cursor-pointer';
            }
        });

        actualizarBotonLimpiarFiltros();
        cargarSolicitudes();
    }

    function cambiarTipoCliente(tipo) {
        currentTipoCliente = tipo;
        actualizarBotonLimpiarFiltros();
        cargarSolicitudes();
    }

    function cambiarOrden(orden) {
        currentOrden = orden;
        cargarSolicitudes();
    }

    function actualizarBotonLimpiarFiltros() {
        const btn = document.getElementById('btn-limpiar-filtros');
        if (!btn) return;
        const hayFiltros = currentEstado !== 'todos' || currentBuscar !== '' || currentTipoCliente !== '' || currentOrden !== 'reciente';
        btn.style.display = hayFiltros ? 'flex' : 'none';
    }

    function limpiarTodosFiltros() {
        currentEstado = 'todos';
        currentBuscar = '';
        currentTipoCliente = '';
        currentOrden = 'reciente';

        const input = document.getElementById('searchInput');
        if (input) input.value = '';
        const clearBtn = document.getElementById('btn_clear_search');
        if (clearBtn) clearBtn.style.display = 'none';

        const tipoSelect = document.getElementById('tipoClienteSelect');
        if (tipoSelect) tipoSelect.value = '';

        const ordenSelect = document.getElementById('ordenSelect');
        if (ordenSelect) ordenSelect.value = 'reciente';

        filtrarPorEstado('todos');
    }

    // ==========================================
    // 🔄 CARGA ASÍNCRONA DE DATOS (AJAX)
    // ==========================================
    function cargarSolicitudes(url = null) {
        const loading = document.getElementById('tabla-loading');
        const spinner = document.getElementById('search_spinner');
        const btnRefrescarIcon = document.getElementById('btn-refrescar-icon');

        if (loading) loading.classList.remove('hidden');
        if (btnRefrescarIcon) btnRefrescarIcon.classList.add('animate-spin');

        let fetchUrl = url;
        if (!fetchUrl) {
            const params = new URLSearchParams({
                estado: currentEstado,
                buscar: currentBuscar,
                tipo_cliente: currentTipoCliente,
                orden: currentOrden
            });
            fetchUrl = `{{ route('admin.solicitudes_productos.index') }}?${params.toString()}`;
        }

        fetch(fetchUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (loading) loading.classList.add('hidden');
            if (spinner) spinner.style.display = 'none';
            if (btnRefrescarIcon) btnRefrescarIcon.classList.remove('animate-spin');

            if (data.success) {
                document.getElementById('tabla-solicitudes-wrapper').innerHTML = data.html;
                
                // Actualizar KPIs
                if (data.stats) {
                    if (document.getElementById('kpi-total')) document.getElementById('kpi-total').textContent = data.stats.total;
                    if (document.getElementById('kpi-pendientes')) document.getElementById('kpi-pendientes').textContent = data.stats.pendientes;
                    if (document.getElementById('kpi-revisados')) document.getElementById('kpi-revisados').textContent = data.stats.revisados;
                    if (document.getElementById('kpi-agregados')) document.getElementById('kpi-agregados').textContent = data.stats.agregados;
                    if (document.getElementById('kpi-rechazados')) document.getElementById('kpi-rechazados').textContent = data.stats.rechazados;

                    if (document.getElementById('tab-badge-todos')) document.getElementById('tab-badge-todos').textContent = data.stats.total;
                    if (document.getElementById('tab-badge-pendientes')) document.getElementById('tab-badge-pendientes').textContent = data.stats.pendientes;
                    if (document.getElementById('tab-badge-revisados')) document.getElementById('tab-badge-revisados').textContent = data.stats.revisados;
                    if (document.getElementById('tab-badge-agregados')) document.getElementById('tab-badge-agregados').textContent = data.stats.agregados;
                    if (document.getElementById('tab-badge-rechazados')) document.getElementById('tab-badge-rechazados').textContent = data.stats.rechazados;
                }

                vincularPaginacionAjax();
            }
        })
        .catch(err => {
            console.error('Error cargando solicitudes:', err);
            if (loading) loading.classList.add('hidden');
            if (spinner) spinner.style.display = 'none';
            if (btnRefrescarIcon) btnRefrescarIcon.classList.remove('animate-spin');
            showToast('Error al conectar con el servidor', 'error');
        });
    }

    function vincularPaginacionAjax() {
        const paginacionLinks = document.querySelectorAll('#tabla-solicitudes-wrapper a.page-link, #tabla-solicitudes-wrapper nav a');
        paginacionLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                cargarSolicitudes(this.href);
            });
        });
    }

    // ==========================================
    // ⚙️ CAMBIO RÁPIDO DE ESTADO (SELECT)
    // ==========================================
    window.cambiarEstadoSolicitud = function(id, nuevoEstado) {
        const badge = document.getElementById(`badge-estado-${id}`);

        fetch(`{{ url('admin/solicitudes-insumos') }}/${id}/estado`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ estado: nuevoEstado })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                if (badge && data.badge) {
                    badge.className = `inline-flex items-center justify-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black border uppercase tracking-wider shadow-2xs w-full max-w-[170px] ${data.badge.bg}`;
                    badge.innerHTML = `<span class="material-symbols-outlined text-[15px]">${data.badge.icono}</span><span class="truncate">${data.badge.texto}</span>`;
                }
                
                // Si estamos filtrando por un estado específico y el nuevo estado no coincide, recargamos
                if (currentEstado !== 'todos' && currentEstado !== nuevoEstado) {
                    setTimeout(() => cargarSolicitudes(), 400);
                }
            } else {
                showToast(data.message || 'No se pudo actualizar el estado', 'error');
            }
        })
        .catch(err => {
            console.error('Error al actualizar estado:', err);
            showToast('Error de conexión al actualizar estado', 'error');
        });
    };

    // ==========================================
    // 🗑️ ELIMINAR SOLICITUD CON SWEETALERT
    // ==========================================
    window.eliminarSolicitud = function(id, nombreProducto) {
        Swal.fire({
            title: '¿Eliminar solicitud?',
            html: `¿Estás seguro de eliminar la petición de <b class="text-[#1e549e] font-black">"${nombreProducto}"</b>? Esta acción no se puede revertir.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-3xl',
                confirmButton: 'rounded-xl text-xs font-bold px-4 py-2.5',
                cancelButton: 'rounded-xl text-xs font-bold px-4 py-2.5'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ url('admin/solicitudes-insumos') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        const row = document.getElementById(`row-solicitud-${id}`);
                        if (row) {
                            row.classList.add('opacity-0', 'transition-opacity', 'duration-300');
                            setTimeout(() => {
                                cargarSolicitudes();
                            }, 300);
                        } else {
                            cargarSolicitudes();
                        }
                    } else {
                        showToast(data.message || 'Error al eliminar la solicitud', 'error');
                    }
                })
                .catch(err => {
                    console.error('Error al eliminar:', err);
                    showToast('Error de conexión al eliminar', 'error');
                });
            }
        });
    };

    document.addEventListener('DOMContentLoaded', function() {
        vincularPaginacionAjax();
    });
</script>
@endpush
