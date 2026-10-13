@extends('layouts.admin')

@section('title', 'Directorio de Marcas - Agrosuministros Rodríguez')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up pb-12">
            
            {{-- ENCABEZADO --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-3.5">
                    <a href="{{ route('admin.dashboard') }}" class="size-10 flex-shrink-0 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:text-blue-600 hover:border-blue-200 transition-colors shadow-2xs">
                        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                    </a>
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-black text-agro-dark flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#1e549e] text-[32px]">sell</span>
                            Directorio de Marcas
                        </h2>
                        <p class="text-xs sm:text-sm text-gray-500 mt-0.5 font-medium">Registra, administra y audita los fabricantes y marcas comerciales de tus productos.</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="fetchData(1)" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 shadow-2xs transition-all active:scale-95" title="Actualizar lista">
                        <span class="material-symbols-outlined text-[17px] text-blue-600">sync</span>
                        Refrescar
                    </button>
                    @if(Auth::user()->tienePermiso('gestionar_categorias_marcas'))
                    <button onclick="openModal()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-black text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all hover:scale-105 active:scale-95">
                        <span class="material-symbols-outlined text-[18px]">add_circle</span>
                        <span>Nueva Marca</span>
                    </button>
                    @endif
                </div>
            </div>

            {{-- KPI METRICS CARDS --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
                
                {{-- KPI 1: Total Marcas --}}
                <div onclick="filtrarPorEstado('all')" class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all cursor-pointer group">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Marcas</span>
                        <div class="size-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">sell</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span id="kpi_total_marcas" class="text-2xl font-black text-gray-900 leading-none">{{ $stats['total'] }}</span>
                        <span class="text-[10px] text-gray-400 font-bold uppercase">Fabricantes</span>
                    </div>
                </div>

                {{-- KPI 2: Marcas Activas --}}
                <div onclick="filtrarPorEstado('1')" class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all cursor-pointer group">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Activas en Tienda</span>
                        <div class="size-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span id="kpi_activas_marcas" class="text-2xl font-black text-emerald-600 leading-none">{{ $stats['activas'] }}</span>
                        <span class="text-[10px] text-emerald-700 font-bold uppercase">Habilitadas</span>
                    </div>
                </div>

                {{-- KPI 3: Con Productos Asociados --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all group">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Con Catálogo</span>
                        <div class="size-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span id="kpi_con_productos_marcas" class="text-2xl font-black text-gray-900 leading-none">{{ $stats['con_productos'] }}</span>
                        <span class="text-[10px] text-indigo-600 font-bold uppercase">Con Productos</span>
                    </div>
                </div>

                {{-- KPI 4: Inactivas / Pausadas --}}
                <div onclick="filtrarPorEstado('0')" class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all cursor-pointer group">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Inactivas</span>
                        <div class="size-8 rounded-xl bg-gray-100 text-gray-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">pause_circle</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span id="kpi_inactivas_marcas" class="text-2xl font-black text-gray-600 leading-none">{{ $stats['inactivas'] }}</span>
                        <span class="text-[10px] text-gray-400 font-bold uppercase">Ocultas</span>
                    </div>
                </div>

            </div>

            {{-- BARRA DE FILTROS ELEVADA --}}
            <div class="bg-white rounded-2xl sm:rounded-3xl shadow-xs border border-gray-100 mb-6 overflow-hidden">
                <div class="p-3.5 sm:p-4 flex flex-col gap-3">
                    <div class="flex flex-col sm:flex-row gap-2.5 sm:gap-3 items-stretch sm:items-center">
                        
                        {{-- 1. Buscador en Vivo Avanzado --}}
                        <div class="flex-1 relative group">
                            <span class="material-symbols-outlined absolute left-3.5 top-2.5 text-gray-400 group-focus-within:text-[#1e549e] text-[20px] transition-colors pointer-events-none">search</span>
                            
                            <input type="text" 
                                   id="searchInput" 
                                   value="{{ request('buscar', '') }}"
                                   autocomplete="off"
                                   class="w-full pl-10 pr-20 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#1e549e]/15 focus:border-[#1e549e] transition-all outline-none font-medium text-gray-800 placeholder-gray-400 shadow-2xs" 
                                   placeholder="Buscar por nombre de marca o país de origen...">
                            
                            {{-- Controles auxiliares en el buscador --}}
                            <div class="absolute right-2.5 top-2.5 flex items-center gap-1.5">
                                <span id="search_spinner" style="display: none;" class="material-symbols-outlined text-[#1e549e] text-[18px] animate-spin">progress_activity</span>
                                
                                <button type="button" 
                                        id="btn_clear_search" 
                                        onclick="limpiarBuscador()" 
                                        style="{{ request('buscar') ? 'display: flex;' : 'display: none;' }}"
                                        class="size-6 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-800 items-center justify-center transition-colors" 
                                        title="Limpiar búsqueda (Escape)">
                                    <span class="material-symbols-outlined text-[15px]">close</span>
                                </button>
                                
                                <kbd class="hidden sm:inline-block px-1.5 py-0.5 text-[9px] font-mono font-bold text-gray-400 bg-gray-50 border border-gray-200 rounded shadow-2xs">ESC</kbd>
                            </div>
                        </div>

                        {{-- 2. Filtro por Estado (Flex Input Group sin solapamientos) --}}
                        <div id="statusWrapper" class="w-full sm:w-56 flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-xl shadow-2xs transition-all focus-within:ring-2 focus-within:ring-[#1e549e]/15 focus-within:border-[#1e549e]">
                            <span id="statusIcon" class="material-symbols-outlined text-gray-400 text-[20px] shrink-0 pointer-events-none transition-colors">tune</span>
                            <select id="statusSelect" class="w-full bg-transparent border-none p-0 outline-none focus:ring-0 text-xs sm:text-sm font-bold text-gray-700 cursor-pointer">
                                <option value="all" {{ request('estado', 'all') === 'all' ? 'selected' : '' }}>Todos los estados</option>
                                <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Solo Activas</option>
                                <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Solo Inactivas</option>
                            </select>
                        </div>

                        {{-- 3. Botón Limpiar Todos los Filtros --}}
                        <button type="button" 
                                id="btn_reset_filtros" 
                                onclick="resetearTodosFiltros()" 
                                style="display: none;"
                                class="items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200/80 transition-all shadow-2xs whitespace-nowrap active:scale-95" 
                                title="Restablecer todos los filtros aplicados">
                            <span class="material-symbols-outlined text-[16px]">filter_alt_off</span>
                            <span>Limpiar</span>
                            <span id="badge_filtros_count" class="size-4.5 rounded-full bg-rose-600 text-white text-[10px] flex items-center justify-center font-black leading-none">0</span>
                        </button>
                    </div>

                    {{-- Chips / Indicadores de filtros activos en tiempo real --}}
                    <div id="chips_filtros_activos" style="display: none;" class="flex-wrap items-center gap-2 pt-2 border-t border-gray-200/50">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px] text-[#1e549e]">tune</span> Filtros activos:
                        </span>
                        <div id="chips_container" class="flex flex-wrap items-center gap-1.5"></div>
                    </div>
                </div>
            </div>

            {{-- CONTENEDOR DE LA TABLA --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden" id="tableContainer">
                <div id="loadingOverlay" class="absolute inset-0 bg-white/80 backdrop-blur-xs z-20 hidden items-center justify-center">
                    <div class="flex flex-col items-center gap-2">
                        <span class="material-symbols-outlined animate-spin text-[#1e549e] text-4xl">autorenew</span>
                        <span class="text-xs font-bold text-gray-600">Actualizando marcas...</span>
                    </div>
                </div>
                
                <div id="tableContent">
                    @include('admin.marcas.partials._table')
                </div>
            </div>

        </div>
    </main>
</div>

{{-- ========================================== --}}
{{-- MODAL CREAR / EDITAR MARCA                 --}}
{{-- ========================================== --}}
<div id="marcaModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity opacity-0" id="marcaModalBackdrop" onclick="closeModal()"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-center p-4">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-md opacity-0 scale-95 flex flex-col border border-gray-100" id="marcaModalPanel">
            
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/80">
                <div class="flex items-center gap-2.5">
                    <div class="size-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">sell</span>
                    </div>
                    <h3 class="text-base font-black text-gray-900 leading-none" id="modalTitle">Nueva Marca</h3>
                </div>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-red-500 bg-white p-1 rounded-lg border border-gray-200 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            <form id="marcaForm" onsubmit="saveMarca(event)">
                <input type="hidden" id="marca_id">
                
                <div class="p-6 space-y-4">
                    
                    <div>
                        <label class="block text-[11px] font-black text-gray-500 uppercase tracking-wider mb-1.5 ml-0.5">
                            Nombre Comercial <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nombre" name="nombre" required class="w-full h-11 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all font-bold text-gray-900 outline-none uppercase placeholder:normal-case text-sm" placeholder="Ej: Bayer, Syngenta, FertiAgro">
                    </div>

                    <div>
                        <label class="block text-[11px] font-black text-gray-500 uppercase tracking-wider mb-1.5 ml-0.5">
                            País de Origen (Opcional)
                        </label>
                        <input type="text" id="pais_origen" name="pais_origen" class="w-full h-11 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all font-bold text-gray-900 outline-none text-sm" placeholder="Ej: Venezuela, Alemania, EE. UU.">
                    </div>

                    <div class="pt-1">
                        <label for="activo" class="flex items-center gap-3.5 p-3.5 bg-gray-50/80 rounded-xl border border-gray-200 cursor-pointer hover:border-blue-300 hover:bg-blue-50/20 transition-all select-none">
                            <div class="relative inline-flex items-center shrink-0">
                                <input type="checkbox" id="activo" name="activo" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                            </div>
                            <div>
                                <span class="block text-xs font-black text-gray-800">Marca Activa</span>
                                <span class="block text-[11px] text-gray-500">Visible en filtros y ficha de productos de la tienda</span>
                            </div>
                        </label>
                    </div>

                </div>

                <div class="px-4 sm:px-6 py-3.5 sm:py-4 border-t border-gray-100 bg-gray-50/80 flex flex-col sm:flex-row justify-end gap-2 sm:gap-3">
                    <button type="button" onclick="closeModal()" class="w-full sm:w-auto px-4 py-2 rounded-xl font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 text-xs text-center">Cancelar</button>
                    <button type="submit" id="btnSave" class="w-full sm:w-auto px-5 py-2 rounded-xl font-black text-white bg-blue-600 hover:bg-blue-700 shadow-sm text-xs flex items-center justify-center gap-1.5 transition-all">
                        <span class="material-symbols-outlined text-[16px]">save</span>
                        <span id="btnSaveText">Guardar Marca</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@push('scripts')
<script>
    // ESTADO GLOBAL DE FILTROS AJAX
    let currentSearch = @json(request('buscar', ''));
    let currentStatus = @json(request('estado', 'all'));
    let currentPage = {{ request('page', 1) }};
    let searchTimeout;

    const ESTADO_LABELS = {
        '1': 'Solo Activas',
        '0': 'Solo Inactivas'
    };

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Inicializar filtros UI al cargar la página
    document.addEventListener('DOMContentLoaded', () => {
        actualizarFiltrosUI();
    });

    // ACTUALIZAR ESTILOS DE SELECTORES Y CHIPS ACTIVOS
    function actualizarFiltrosUI() {
        let activeCount = 0;

        // Buscador
        const btnClear = document.getElementById('btn_clear_search');
        if (btnClear) {
            btnClear.style.display = (currentSearch && currentSearch.trim() !== '') ? 'flex' : 'none';
        }

        // Selector de Estado
        const statusWrapper = document.getElementById('statusWrapper');
        const statusIcon = document.getElementById('statusIcon');
        if (currentStatus && currentStatus !== 'all') {
            activeCount++;
            if (statusWrapper) {
                statusWrapper.classList.add('border-[#1e549e]', 'bg-blue-50/40', 'text-[#1e549e]', 'ring-1', 'ring-[#1e549e]/30');
                statusWrapper.classList.remove('border-gray-200', 'bg-white');
            }
            if (statusIcon) {
                statusIcon.classList.add('text-[#1e549e]');
                statusIcon.classList.remove('text-gray-400');
            }
        } else {
            if (statusWrapper) {
                statusWrapper.classList.remove('border-[#1e549e]', 'bg-blue-50/40', 'text-[#1e549e]', 'ring-1', 'ring-[#1e549e]/30');
                statusWrapper.classList.add('border-gray-200', 'bg-white');
            }
            if (statusIcon) {
                statusIcon.classList.remove('text-[#1e549e]');
                statusIcon.classList.add('text-gray-400');
            }
        }

        // Búsqueda activa cuenta como filtro
        if (currentSearch && currentSearch.trim() !== '') {
            activeCount++;
        }

        // Renderizar Chips
        const chipsWrapper = document.getElementById('chips_filtros_activos');
        const chipsContainer = document.getElementById('chips_container');
        const btnResetFiltros = document.getElementById('btn_reset_filtros');
        const badgeFiltrosCount = document.getElementById('badge_filtros_count');

        let chipsHtml = '';

        if (currentSearch && currentSearch.trim() !== '') {
            chipsHtml += `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-white text-gray-700 border border-gray-200 shadow-2xs">
                    <span class="material-symbols-outlined text-[14px] text-[#1e549e]">search</span>
                    <span>Búsqueda: <strong class="text-gray-900 font-bold">"${escapeHtml(currentSearch)}"</strong></span>
                    <button type="button" onclick="limpiarBuscador()" class="size-4 rounded-full flex items-center justify-center hover:bg-gray-200 text-gray-400 hover:text-gray-800 transition-colors" title="Quitar filtro de búsqueda">
                        <span class="material-symbols-outlined text-[12px]">close</span>
                    </button>
                </span>
            `;
        }

        if (currentStatus && currentStatus !== 'all') {
            const estadoLabel = ESTADO_LABELS[currentStatus] || 'Estado';
            chipsHtml += `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-white text-gray-700 border border-gray-200 shadow-2xs">
                    <span class="material-symbols-outlined text-[14px] text-[#1e549e]">tune</span>
                    <span>Estado: <strong class="text-[#1e549e] font-bold">${escapeHtml(estadoLabel)}</strong></span>
                    <button type="button" onclick="quitarFiltroEstado()" class="size-4 rounded-full flex items-center justify-center hover:bg-gray-200 text-gray-400 hover:text-gray-800 transition-colors" title="Quitar filtro de estado">
                        <span class="material-symbols-outlined text-[12px]">close</span>
                    </button>
                </span>
            `;
        }

        if (chipsContainer) chipsContainer.innerHTML = chipsHtml;

        if (chipsWrapper) {
            chipsWrapper.style.display = activeCount > 0 ? 'flex' : 'none';
        }

        if (btnResetFiltros && badgeFiltrosCount) {
            btnResetFiltros.style.display = activeCount > 0 ? 'inline-flex' : 'none';
            badgeFiltrosCount.textContent = activeCount;
        }
    }

    // ACCIONES DE LIMPIEZA
    function limpiarBuscador() {
        const input = document.getElementById('searchInput');
        if (input) {
            input.value = '';
            input.focus();
        }
        currentSearch = '';
        actualizarFiltrosUI();
        fetchData(1);
    }

    function quitarFiltroEstado() {
        const statusSel = document.getElementById('statusSelect');
        if (statusSel) statusSel.value = 'all';
        currentStatus = 'all';
        actualizarFiltrosUI();
        fetchData(1);
    }

    function resetearTodosFiltros() {
        const input = document.getElementById('searchInput');
        if (input) input.value = '';
        currentSearch = '';

        const statusSel = document.getElementById('statusSelect');
        if (statusSel) statusSel.value = 'all';
        currentStatus = 'all';

        actualizarFiltrosUI();
        fetchData(1);
    }

    function filtrarPorEstado(estado) {
        const statusSel = document.getElementById('statusSelect');
        if (statusSel) statusSel.value = estado;
        currentStatus = estado;
        actualizarFiltrosUI();
        fetchData(1);
    }

    // SINCRONIZACIÓN DE PARÁMETROS EN LA URL
    function sincronizarURL(page = 1) {
        try {
            const url = new URL(window.location.href);
            if (currentSearch && currentSearch.trim() !== '') {
                url.searchParams.set('buscar', currentSearch.trim());
            } else {
                url.searchParams.delete('buscar');
            }

            if (currentStatus && currentStatus !== 'all') {
                url.searchParams.set('estado', currentStatus);
            } else {
                url.searchParams.delete('estado');
            }

            if (page > 1) {
                url.searchParams.set('page', page);
            } else {
                url.searchParams.delete('page');
            }

            window.history.replaceState({}, '', url.toString());
        } catch (e) {
            // No-op
        }
    }

    // EVENT LISTENERS DEL BUSCADOR EN VIVO
    const searchInputEl = document.getElementById('searchInput');
    const searchSpinnerEl = document.getElementById('search_spinner');
    const btnClearSearchEl = document.getElementById('btn_clear_search');

    if (searchInputEl) {
        searchInputEl.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            currentSearch = this.value;

            if (searchSpinnerEl) searchSpinnerEl.style.display = 'inline-block';
            if (btnClearSearchEl) btnClearSearchEl.style.display = 'none';

            searchTimeout = setTimeout(() => {
                fetchData(1, false);
            }, 300);
        });

        // Soporte tecla Escape y Enter
        searchInputEl.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (this.value !== '') {
                    limpiarBuscador();
                } else {
                    this.blur();
                }
            } else if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                currentSearch = this.value;
                actualizarFiltrosUI();
                fetchData(1);
            }
        });
    }

    // SELECTOR DE ESTADO
    const statusSelectEl = document.getElementById('statusSelect');
    if (statusSelectEl) {
        statusSelectEl.addEventListener('change', function() {
            currentStatus = this.value;
            actualizarFiltrosUI();
            fetchData(1);
        });
    }

    // PAGINACIÓN AJAX
    document.addEventListener('click', function(e) {
        const pagLink = e.target.closest('.ajax-pagination a');
        if (pagLink) {
            e.preventDefault();
            const pageUrl = pagLink.href;
            const targetPage = new URL(pageUrl).searchParams.get('page') || 1;
            fetchData(targetPage, true);
        }
    });

    // FUNCIÓN PRINCIPAL FETCH DATA CON ACTUALIZACIÓN DE KPIs
    function fetchData(page = 1, showLoading = true) {
        currentPage = page;
        const loading = document.getElementById('loadingOverlay');
        if (showLoading && loading) {
            loading.classList.remove('hidden');
            loading.classList.add('flex');
        }

        const params = new URLSearchParams({
            buscar: currentSearch,
            estado: currentStatus,
            page: page
        });

        fetch(`{{ route('admin.marcas.index') }}?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(async res => {
            const contentType = res.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return await res.json();
            }
            return { html: await res.text() };
        })
        .then(data => {
            if (data.html) {
                document.getElementById('tableContent').innerHTML = data.html;
            }
            if (data.stats) {
                actualizarKPIs(data.stats);
            }
            sincronizarURL(page);
        })
        .catch(err => {
            console.error('Error al actualizar marcas:', err);
        })
        .finally(() => {
            if (loading) {
                loading.classList.add('hidden');
                loading.classList.remove('flex');
            }
            if (searchSpinnerEl) searchSpinnerEl.style.display = 'none';
            actualizarFiltrosUI();
        });
    }

    function actualizarKPIs(stats) {
        if (!stats) return;
        const totalEl = document.getElementById('kpi_total_marcas');
        if (totalEl && stats.total !== undefined) totalEl.textContent = stats.total;

        const activasEl = document.getElementById('kpi_activas_marcas');
        if (activasEl && stats.activas !== undefined) activasEl.textContent = stats.activas;

        const conProdEl = document.getElementById('kpi_con_productos_marcas');
        if (conProdEl && stats.con_productos !== undefined) conProdEl.textContent = stats.con_productos;

        const inactivasEl = document.getElementById('kpi_inactivas_marcas');
        if (inactivasEl && stats.inactivas !== undefined) inactivasEl.textContent = stats.inactivas;
    }

    // ==========================================
    // MODAL CREAR / EDITAR
    // ==========================================
    function openModal(marca = null) {
        const modal = document.getElementById('marcaModal');
        const backdrop = document.getElementById('marcaModalBackdrop');
        const panel = document.getElementById('marcaModalPanel');
        const form = document.getElementById('marcaForm');
        
        form.reset();
        
        if (marca) {
            document.getElementById('modalTitle').innerText = 'Editar Marca';
            document.getElementById('btnSaveText').innerText = 'Actualizar Cambios';
            document.getElementById('marca_id').value = marca.id;
            document.getElementById('nombre').value = marca.nombre;
            document.getElementById('pais_origen').value = marca.pais_origen || '';
            document.getElementById('activo').checked = Boolean(marca.activo);
        } else {
            document.getElementById('modalTitle').innerText = 'Nueva Marca';
            document.getElementById('btnSaveText').innerText = 'Guardar Marca';
            document.getElementById('marca_id').value = '';
            document.getElementById('activo').checked = true;
        }

        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    function closeModal() {
        const modal = document.getElementById('marcaModal');
        const backdrop = document.getElementById('marcaModalBackdrop');
        const panel = document.getElementById('marcaModalPanel');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 250);
    }

    function saveMarca(e) {
        e.preventDefault();
        const id = document.getElementById('marca_id').value;
        const btn = document.getElementById('btnSave');
        const originalHtml = btn.innerHTML;
        
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[16px]">autorenew</span> Guardando...';
        btn.disabled = true;

        const url = id ? `/admin/marcas/${id}` : `{{ route('admin.marcas.store') }}`;
        const formData = new FormData(document.getElementById('marcaForm'));
        
        if (id) {
            formData.append('_method', 'PUT');
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Error al guardar la marca');
            return data;
        })
        .then(data => {
            closeModal();
            Swal.fire({
                icon: 'success',
                title: '¡Operación Exitosa!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false,
                customClass: { popup: 'rounded-2xl' }
            });
            fetchData(1);
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Error de Validación',
                text: err.message,
                customClass: { popup: 'rounded-2xl' }
            });
        })
        .finally(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    }

    // ==========================================
    // TOGGLE STATUS DIRECTO (AJAX)
    // ==========================================
    function toggleStatusMarca(id, checkbox) {
        fetch(`/admin/marcas/${id}/toggle`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Error al cambiar estado');
            return data;
        })
        .then(data => {
            checkbox.checked = data.activo;
            fetchData(1);
        })
        .catch(err => {
            checkbox.checked = !checkbox.checked;
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: err.message,
                customClass: { popup: 'rounded-2xl' }
            });
        });
    }

    // ==========================================
    // ELIMINAR MARCA CON VALIDACIÓN
    // ==========================================
    function deleteMarca(id, nombre, productosCount) {
        if (productosCount > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No se puede eliminar',
                html: `La marca <b>${nombre}</b> tiene <b>${productosCount} producto(s)</b> asignado(s) en el inventario.<br><br><span class="text-xs text-gray-500">Debes reasignar los productos a otra marca o simplemente desactivarla con el interruptor.</span>`,
                confirmButtonColor: '#1e549e',
                confirmButtonText: 'Entendido',
                customClass: { popup: 'rounded-2xl' }
            });
            return;
        }

        Swal.fire({
            title: '¿Eliminar Marca?',
            text: `La marca "${nombre}" será removida del sistema.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Sí, Eliminar',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/marcas/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Error al eliminar');
                    return data;
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminada',
                        text: data.message,
                        timer: 1800,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-2xl' }
                    });
                    fetchData(1);
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: err.message,
                        customClass: { popup: 'rounded-2xl' }
                    });
                });
            }
        });
    }

    // Cerrar modal con Escape solo si está abierto
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modal = document.getElementById('marcaModal');
            if (modal && !modal.classList.contains('hidden')) {
                closeModal();
            }
        }
    });
</script>
@endpush
@endsection