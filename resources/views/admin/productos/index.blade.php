@extends('layouts.admin')

@section('title', 'Catálogo de Productos - Agrosuministros Rodríguez')

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
                            <span class="material-symbols-outlined text-[#1e549e] text-[32px]">inventory_2</span>
                            Catálogo de Productos
                        </h2>
                        <p class="text-xs sm:text-sm text-gray-500 mt-0.5 font-medium">Control integral de inventario físico, precios, lotes, marcas y alertas de reposición.</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="fetchData(1)" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 shadow-2xs transition-all active:scale-95" title="Actualizar lista">
                        <span class="material-symbols-outlined text-[17px] text-blue-600">sync</span>
                        Refrescar
                    </button>
                    @if(Auth::user()->tienePermiso('gestionar_productos'))
                    <a href="{{ route('admin.productos.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-black text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all hover:scale-105 active:scale-95">
                        <span class="material-symbols-outlined text-[18px]">add_circle</span>
                        <span>Nuevo Producto</span>
                    </a>
                    @endif
                </div>
            </div>

            {{-- KPI METRICS CARDS --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
                
                {{-- KPI 1: Total Productos --}}
                <div onclick="filtrarPorTab('')" class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all cursor-pointer group">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Artículos</span>
                        <div class="size-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span id="kpi_total" class="text-2xl font-black text-gray-900 leading-none">{{ $stats['total'] }}</span>
                        <span class="text-[10px] text-gray-400 font-bold uppercase">En Catálogo</span>
                    </div>
                </div>

                {{-- KPI 2: Stock Crítico (Alerta) --}}
                <div id="kpi_card_critico" onclick="filtrarPorTab('critico')" class="bg-white p-4 sm:p-5 rounded-2xl border {{ $stats['critico'] > 0 ? 'border-amber-300 ring-2 ring-amber-400/20 bg-amber-50/20' : 'border-gray-100' }} shadow-2xs hover:shadow-md transition-all cursor-pointer group">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold {{ $stats['critico'] > 0 ? 'text-amber-800' : 'text-gray-400' }} uppercase tracking-wider">Stock Crítico / Agotado</span>
                        <div class="size-8 rounded-xl {{ $stats['critico'] > 0 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">warning</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span id="kpi_critico" class="text-2xl font-black {{ $stats['critico'] > 0 ? 'text-amber-700' : 'text-gray-900' }} leading-none">{{ $stats['critico'] }}</span>
                        <span id="kpi_badge_critico" class="text-[10px] font-bold uppercase {{ $stats['critico'] > 0 ? 'text-amber-700 font-black bg-amber-100 px-1.5 py-0.5 rounded animate-pulse' : 'text-emerald-600' }}">{{ $stats['critico'] > 0 ? '¡Por agotarse!' : 'Suficiente' }}</span>
                    </div>
                </div>

                {{-- KPI 3: Destacados --}}
                <div onclick="filtrarPorTab('destacados')" class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all cursor-pointer group">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Destacados</span>
                        <div class="size-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">star</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span id="kpi_destacados" class="text-2xl font-black text-gray-900 leading-none">{{ $stats['destacados'] }}</span>
                        <span class="text-[10px] text-amber-600 font-bold uppercase">En Portada</span>
                    </div>
                </div>

                {{-- KPI 4: Valoración de Inventario --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all group">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Valor Inventario</span>
                        <div class="size-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">attach_money</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-1.5">
                        <span id="kpi_valor_inventario" class="text-xl sm:text-2xl font-black text-emerald-700 leading-none">${{ number_format($stats['valor_inventario_usd'], 2) }}</span>
                    </div>
                </div>

            </div>

            {{-- PANEL DE FILTROS Y SMART TABS --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-2 mb-6">
                
                {{-- Smart Tabs --}}
                <div class="flex overflow-x-auto custom-scrollbar border-b border-gray-100 px-4 pt-2 gap-2 sm:gap-4">
                    <button class="prod-tab active pb-3 px-2 text-xs sm:text-sm font-black border-b-2 border-primary text-agro-dark whitespace-nowrap flex items-center gap-1.5 transition-colors" data-filtro="">
                        <span>Todos los Productos</span>
                        <span id="tab_badge_total" class="px-1.5 py-0.2 rounded-full text-[10px] bg-gray-100 text-gray-600">{{ $stats['total'] }}</span>
                    </button>
                    
                    <button class="prod-tab pb-3 px-2 text-xs sm:text-sm font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-800 whitespace-nowrap flex items-center gap-1.5 transition-colors" data-filtro="critico">
                        <span class="material-symbols-outlined text-[16px] text-amber-500">warning</span>
                        <span>Stock Crítico</span>
                        <span id="tab_badge_critico" class="px-1.5 py-0.2 rounded-full text-[10px] {{ $stats['critico'] > 0 ? 'font-black bg-amber-500 text-slate-900 animate-pulse' : 'bg-gray-100 text-gray-600' }}">{{ $stats['critico'] }}</span>
                    </button>

                    <button class="prod-tab pb-3 px-2 text-xs sm:text-sm font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-800 whitespace-nowrap flex items-center gap-1.5 transition-colors" data-filtro="destacados">
                        <span class="material-symbols-outlined text-[16px] text-amber-400">star</span>
                        <span>Destacados</span>
                        <span id="tab_badge_destacados" class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-100 text-amber-800 font-bold">{{ $stats['destacados'] }}</span>
                    </button>

                    <button class="prod-tab pb-3 px-2 text-xs sm:text-sm font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-800 whitespace-nowrap flex items-center gap-1.5 transition-colors" data-filtro="combos">
                        <span>Combos / Paquetes</span>
                        <span id="tab_badge_combos" class="px-1.5 py-0.2 rounded-full text-[10px] bg-blue-100 text-blue-700 font-bold">{{ $stats['combos'] }}</span>
                    </button>

                    {{-- Tab Venta Controlada / Récipes (Deshabilitado a solicitud del cliente - reactivable a futuro) --}}
                    {{-- <button class="prod-tab pb-3 px-2 text-xs sm:text-sm font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-800 whitespace-nowrap flex items-center gap-1.5 transition-colors" data-filtro="recetados">
                        <span>Venta Controlada</span>
                        <span id="tab_badge_recetados" class="px-1.5 py-0.2 rounded-full text-[10px] bg-purple-100 text-purple-700 font-bold">{{ $stats['recetados'] }}</span>
                    </button> --}}

                    <button class="prod-tab pb-3 px-2 text-xs sm:text-sm font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-800 whitespace-nowrap flex items-center gap-1.5 transition-colors" data-filtro="suspendidos">
                        <span>Pausados / Ocultos</span>
                        <span id="tab_badge_suspendidos" class="px-1.5 py-0.2 rounded-full text-[10px] bg-rose-100 text-rose-700 font-bold">{{ $stats['suspendidos'] }}</span>
                    </button>
                </div>

                {{-- Filtros secundarios: Buscador, Categoría, Marca y Limpiar --}}
                <div class="p-3.5 sm:p-4 bg-gray-50/70 rounded-b-2xl border-t border-gray-100 flex flex-col gap-3">
                    <div class="flex flex-col lg:flex-row gap-2.5 sm:gap-3 items-stretch lg:items-center">
                        
                        {{-- 1. Buscador en Vivo Avanzado --}}
                        <div class="flex-1 relative group">
                            <span class="material-symbols-outlined absolute left-3.5 top-2.5 text-gray-400 group-focus-within:text-[#1e549e] text-[20px] transition-colors pointer-events-none">search</span>
                            
                            <input type="text" 
                                   id="searchInput" 
                                   value="{{ request('buscar', '') }}"
                                   autocomplete="off"
                                   class="w-full pl-10 pr-20 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#1e549e]/15 focus:border-[#1e549e] transition-all outline-none font-medium text-gray-800 placeholder-gray-400 shadow-2xs" 
                                   placeholder="Buscar por Nombre del producto, SKU, Código de barras, Categoría...">
                            
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

                        {{-- 2. Filtro por Categoría (Flex Input Group sin solapamientos) --}}
                        <div id="categoriaWrapper" class="w-full sm:w-auto lg:w-56 flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-xl shadow-2xs transition-all focus-within:ring-2 focus-within:ring-[#1e549e]/15 focus-within:border-[#1e549e]">
                            <span id="categoriaIcon" class="material-symbols-outlined text-gray-400 text-[20px] shrink-0 pointer-events-none transition-colors">category</span>
                            <select id="categoriaSelect" class="w-full bg-transparent border-none p-0 outline-none focus:ring-0 text-xs sm:text-sm font-bold text-gray-700 cursor-pointer">
                                <option value="" {{ !request('categoria_id') ? 'selected' : '' }}>Todas las Categorías</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 3. Filtro por Marca (Flex Input Group sin solapamientos) --}}
                        <div id="marcaWrapper" class="w-full sm:w-auto lg:w-52 flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-xl shadow-2xs transition-all focus-within:ring-2 focus-within:ring-[#1e549e]/15 focus-within:border-[#1e549e]">
                            <span id="marcaIcon" class="material-symbols-outlined text-gray-400 text-[20px] shrink-0 pointer-events-none transition-colors">sell</span>
                            <select id="marcaSelect" class="w-full bg-transparent border-none p-0 outline-none focus:ring-0 text-xs sm:text-sm font-bold text-gray-700 cursor-pointer">
                                <option value="" {{ !request('marca_id') ? 'selected' : '' }}>Todas las Marcas</option>
                                @foreach($marcas as $marca)
                                    <option value="{{ $marca->id }}" {{ request('marca_id') == $marca->id ? 'selected' : '' }}>{{ $marca->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 4. Botón Limpiar Todos los Filtros --}}
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
                    <div id="chips_filtros_activos" style="display: none;" class="flex-wrap items-center gap-2 pt-1 border-t border-gray-200/50">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px] text-[#1e549e]">tune</span> Filtros activos:
                        </span>
                        <div id="chips_container" class="flex flex-wrap items-center gap-1.5"></div>
                    </div>
                </div>
            </div>

            {{-- CONTENEDOR DE LA TABLA AJAX --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden" id="tableContainer">
                <div id="loadingOverlay" class="absolute inset-0 bg-white/80 backdrop-blur-xs z-20 hidden items-center justify-center">
                    <div class="flex flex-col items-center gap-2">
                        <span class="material-symbols-outlined animate-spin text-[#1e549e] text-4xl">autorenew</span>
                        <span class="text-xs font-bold text-gray-600">Actualizando catálogo...</span>
                    </div>
                </div>
                
                <div id="tableContent">
                    @include('admin.productos.partials._table')
                </div>
            </div>

        </div>
    </main>
</div>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@push('scripts')
<script>
    // ESTADO GLOBAL DE FILTROS AJAX
    let currentSearch = @json(request('buscar', ''));
    let currentCategoria = @json(request('categoria_id', ''));
    let currentMarca = @json(request('marca_id', ''));
    let currentFiltroRapido = @json(request('filtro_rapido', ''));
    let currentPage = {{ request('page', 1) }};
    let searchTimeout;

    const FILTRO_RAPIDO_LABELS = {
        'critico': 'Stock Crítico',
        'destacados': 'Destacados',
        'combos': 'Combos / Paquetes',
        'recetados': 'Venta Controlada',
        'suspendidos': 'Pausados / Ocultos'
    };

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Inicializar Tab Activa y Filtros UI al cargar la página
    document.addEventListener('DOMContentLoaded', () => {
        if (currentFiltroRapido) {
            const activeTab = document.querySelector(`.prod-tab[data-filtro="${currentFiltroRapido}"]`);
            if (activeTab) {
                document.querySelectorAll('.prod-tab').forEach(t => {
                    t.classList.remove('border-primary', 'text-agro-dark', 'active');
                    t.classList.add('border-transparent', 'text-gray-500');
                });
                activeTab.classList.remove('border-transparent', 'text-gray-500');
                activeTab.classList.add('border-primary', 'text-agro-dark', 'active');
            }
        }
        actualizarFiltrosUI();
    });

    // LISTENERS DE TABS INTELIGENTES
    document.querySelectorAll('.prod-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            filtrarPorTab(this.getAttribute('data-filtro'));
        });
    });

    function filtrarPorTab(filtro) {
        document.querySelectorAll('.prod-tab').forEach(t => {
            t.classList.remove('border-primary', 'text-agro-dark', 'active');
            t.classList.add('border-transparent', 'text-gray-500');
        });

        const targetTab = document.querySelector(`.prod-tab[data-filtro="${filtro}"]`);
        if (targetTab) {
            targetTab.classList.remove('border-transparent', 'text-gray-500');
            targetTab.classList.add('border-primary', 'text-agro-dark', 'active');
        }

        currentFiltroRapido = filtro;
        actualizarFiltrosUI();
        fetchData(1);
    }

    // ACTUALIZAR ESTADO VISUAL DE FILTROS, CHIPS Y BOTONES
    function actualizarFiltrosUI() {
        const searchInput = document.getElementById('searchInput');
        const btnClearSearch = document.getElementById('btn_clear_search');
        const searchSpinner = document.getElementById('search_spinner');
        const categoriaWrapper = document.getElementById('categoriaWrapper');
        const categoriaSelect = document.getElementById('categoriaSelect');
        const categoriaIcon = document.getElementById('categoriaIcon');
        const marcaWrapper = document.getElementById('marcaWrapper');
        const marcaSelect = document.getElementById('marcaSelect');
        const marcaIcon = document.getElementById('marcaIcon');
        const btnResetFiltros = document.getElementById('btn_reset_filtros');
        const badgeFiltrosCount = document.getElementById('badge_filtros_count');
        const chipsWrapper = document.getElementById('chips_filtros_activos');
        const chipsContainer = document.getElementById('chips_container');

        // 1. Botón limpiar búsqueda
        const hasSearch = currentSearch && currentSearch.trim().length > 0;
        if (btnClearSearch) {
            btnClearSearch.style.display = hasSearch ? 'flex' : 'none';
        }

        // 2. Estilos dinámicos para Categoría
        if (categoriaWrapper && categoriaSelect) {
            if (currentCategoria && currentCategoria !== '') {
                categoriaWrapper.classList.add('border-[#1e549e]', 'bg-blue-50/40', 'ring-1', 'ring-[#1e549e]/30');
                categoriaWrapper.classList.remove('border-gray-200', 'bg-white');
                if (categoriaIcon) {
                    categoriaIcon.classList.add('text-[#1e549e]');
                    categoriaIcon.classList.remove('text-gray-400');
                }
            } else {
                categoriaWrapper.classList.remove('border-[#1e549e]', 'bg-blue-50/40', 'ring-1', 'ring-[#1e549e]/30');
                categoriaWrapper.classList.add('border-gray-200', 'bg-white');
                if (categoriaIcon) {
                    categoriaIcon.classList.remove('text-[#1e549e]');
                    categoriaIcon.classList.add('text-gray-400');
                }
            }
        }

        // 3. Estilos dinámicos para Marca
        if (marcaWrapper && marcaSelect) {
            if (currentMarca && currentMarca !== '') {
                marcaWrapper.classList.add('border-[#1e549e]', 'bg-blue-50/40', 'ring-1', 'ring-[#1e549e]/30');
                marcaWrapper.classList.remove('border-gray-200', 'bg-white');
                if (marcaIcon) {
                    marcaIcon.classList.add('text-[#1e549e]');
                    marcaIcon.classList.remove('text-gray-400');
                }
            } else {
                marcaWrapper.classList.remove('border-[#1e549e]', 'bg-blue-50/40', 'ring-1', 'ring-[#1e549e]/30');
                marcaWrapper.classList.add('border-gray-200', 'bg-white');
                if (marcaIcon) {
                    marcaIcon.classList.remove('text-[#1e549e]');
                    marcaIcon.classList.add('text-gray-400');
                }
            }
        }

        // 4. Contador de filtros activos y Chips
        let activeCount = 0;
        let chipsHtml = '';

        if (hasSearch) {
            activeCount++;
            chipsHtml += `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-white border border-[#1e549e]/30 text-gray-800 shadow-2xs">
                    <span class="material-symbols-outlined text-[14px] text-[#1e549e]">search</span>
                    <span>Buscar: <strong class="text-[#1e549e] font-bold">"${escapeHtml(currentSearch)}"</strong></span>
                    <button type="button" onclick="limpiarBuscador()" class="size-4 rounded-full flex items-center justify-center hover:bg-gray-200 text-gray-400 hover:text-gray-800 transition-colors" title="Quitar búsqueda">
                        <span class="material-symbols-outlined text-[12px]">close</span>
                    </button>
                </span>
            `;
        }

        if (currentCategoria && currentCategoria !== '') {
            activeCount++;
            const catText = (categoriaSelect && categoriaSelect.selectedIndex >= 0) 
                ? categoriaSelect.options[categoriaSelect.selectedIndex].text 
                : 'Categoría';
            chipsHtml += `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-white border border-[#1e549e]/30 text-gray-800 shadow-2xs">
                    <span class="material-symbols-outlined text-[14px] text-[#1e549e]">category</span>
                    <span>Categoría: <strong class="text-[#1e549e] font-bold">${escapeHtml(catText)}</strong></span>
                    <button type="button" onclick="quitarFiltroCategoria()" class="size-4 rounded-full flex items-center justify-center hover:bg-gray-200 text-gray-400 hover:text-gray-800 transition-colors" title="Quitar filtro de categoría">
                        <span class="material-symbols-outlined text-[12px]">close</span>
                    </button>
                </span>
            `;
        }

        if (currentMarca && currentMarca !== '') {
            activeCount++;
            const marcaText = (marcaSelect && marcaSelect.selectedIndex >= 0) 
                ? marcaSelect.options[marcaSelect.selectedIndex].text 
                : 'Marca';
            chipsHtml += `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-white border border-[#1e549e]/30 text-gray-800 shadow-2xs">
                    <span class="material-symbols-outlined text-[14px] text-[#1e549e]">sell</span>
                    <span>Marca: <strong class="text-[#1e549e] font-bold">${escapeHtml(marcaText)}</strong></span>
                    <button type="button" onclick="quitarFiltroMarca()" class="size-4 rounded-full flex items-center justify-center hover:bg-gray-200 text-gray-400 hover:text-gray-800 transition-colors" title="Quitar filtro de marca">
                        <span class="material-symbols-outlined text-[12px]">close</span>
                    </button>
                </span>
            `;
        }

        if (currentFiltroRapido && currentFiltroRapido !== '') {
            activeCount++;
            const tabLabel = FILTRO_RAPIDO_LABELS[currentFiltroRapido] || currentFiltroRapido;
            chipsHtml += `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-white border border-[#1e549e]/30 text-gray-800 shadow-2xs">
                    <span class="material-symbols-outlined text-[14px] text-[#1e549e]">tune</span>
                    <span>Estado: <strong class="text-[#1e549e] font-bold">${escapeHtml(tabLabel)}</strong></span>
                    <button type="button" onclick="quitarFiltroRapido()" class="size-4 rounded-full flex items-center justify-center hover:bg-gray-200 text-gray-400 hover:text-gray-800 transition-colors" title="Ver todos los productos">
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

    function quitarFiltroCategoria() {
        const catSel = document.getElementById('categoriaSelect');
        if (catSel) catSel.value = '';
        currentCategoria = '';
        actualizarFiltrosUI();
        fetchData(1);
    }

    function quitarFiltroMarca() {
        const marcaSel = document.getElementById('marcaSelect');
        if (marcaSel) marcaSel.value = '';
        currentMarca = '';
        actualizarFiltrosUI();
        fetchData(1);
    }

    function quitarFiltroRapido() {
        filtrarPorTab('');
    }

    function resetearTodosFiltros() {
        const input = document.getElementById('searchInput');
        if (input) input.value = '';
        currentSearch = '';

        const catSel = document.getElementById('categoriaSelect');
        if (catSel) catSel.value = '';
        currentCategoria = '';

        const marcaSel = document.getElementById('marcaSelect');
        if (marcaSel) marcaSel.value = '';
        currentMarca = '';

        document.querySelectorAll('.prod-tab').forEach(t => {
            t.classList.remove('border-primary', 'text-agro-dark', 'active');
            t.classList.add('border-transparent', 'text-gray-500');
        });
        const defaultTab = document.querySelector('.prod-tab[data-filtro=""]');
        if (defaultTab) {
            defaultTab.classList.remove('border-transparent', 'text-gray-500');
            defaultTab.classList.add('border-primary', 'text-agro-dark', 'active');
        }
        currentFiltroRapido = '';

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

            if (currentCategoria && currentCategoria !== '') {
                url.searchParams.set('categoria_id', currentCategoria);
            } else {
                url.searchParams.delete('categoria_id');
            }

            if (currentMarca && currentMarca !== '') {
                url.searchParams.set('marca_id', currentMarca);
            } else {
                url.searchParams.delete('marca_id');
            }

            if (currentFiltroRapido && currentFiltroRapido !== '') {
                url.searchParams.set('filtro_rapido', currentFiltroRapido);
            } else {
                url.searchParams.delete('filtro_rapido');
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

            // Indicar visualmente que se está esperando la respuesta
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

    // SELECTORES DE CATEGORÍA Y MARCA
    const categoriaSelectEl = document.getElementById('categoriaSelect');
    if (categoriaSelectEl) {
        categoriaSelectEl.addEventListener('change', function() {
            currentCategoria = this.value;
            actualizarFiltrosUI();
            fetchData(1);
        });
    }

    const marcaSelectEl = document.getElementById('marcaSelect');
    if (marcaSelectEl) {
        marcaSelectEl.addEventListener('change', function() {
            currentMarca = this.value;
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

    // FUNCIÓN PRINCIPAL FETCH DATA CON SINCRONIZACIÓN DE KPIs Y TABS
    function fetchData(page = 1, showLoading = true) {
        currentPage = page;
        const loading = document.getElementById('loadingOverlay');
        if (showLoading && loading) {
            loading.classList.remove('hidden');
            loading.classList.add('flex');
        }

        const params = new URLSearchParams({
            buscar: currentSearch,
            categoria_id: currentCategoria,
            marca_id: currentMarca,
            filtro_rapido: currentFiltroRapido,
            page: page
        });

        fetch(`{{ route('admin.productos.index') }}?${params.toString()}`, {
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.includes("application/json")) {
                return response.json();
            }
            return response.text().then(html => ({ html: html, stats: null }));
        })
        .then(data => {
            if (data.html) {
                document.getElementById('tableContent').innerHTML = data.html;
            }
            if (data.stats) {
                updateStatsUI(data.stats);
            }
        })
        .catch(err => {
            console.error('Error al actualizar productos:', err);
        })
        .finally(() => {
            if (showLoading && loading) {
                loading.classList.add('hidden');
                loading.classList.remove('flex');
            }
            if (searchSpinnerEl) searchSpinnerEl.style.display = 'none';
            actualizarFiltrosUI();
            sincronizarURL(page);
        });
    }

    // SINCRONIZADOR DINÁMICO DE MÉTRICAS (KPIs) Y CONTADORES DE TABS
    function updateStatsUI(stats) {
        if (!stats) return;

        // KPI 1: Total
        const kpiTotal = document.getElementById('kpi_total');
        if (kpiTotal) kpiTotal.innerText = stats.total ?? 0;

        // KPI 2: Crítico
        const kpiCritico = document.getElementById('kpi_critico');
        const kpiCardCritico = document.getElementById('kpi_card_critico');
        const kpiBadgeCritico = document.getElementById('kpi_badge_critico');
        if (kpiCritico) kpiCritico.innerText = stats.critico ?? 0;
        if (kpiCardCritico) {
            if (stats.critico > 0) {
                kpiCardCritico.className = "bg-white p-4 sm:p-5 rounded-2xl border border-amber-300 ring-2 ring-amber-400/20 bg-amber-50/20 shadow-2xs hover:shadow-md transition-all cursor-pointer group";
                if (kpiBadgeCritico) {
                    kpiBadgeCritico.className = "text-[10px] text-amber-700 font-black uppercase bg-amber-100 px-1.5 py-0.5 rounded animate-pulse";
                    kpiBadgeCritico.innerText = "¡Por agotarse!";
                }
            } else {
                kpiCardCritico.className = "bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all cursor-pointer group";
                if (kpiBadgeCritico) {
                    kpiBadgeCritico.className = "text-[10px] text-emerald-600 font-bold uppercase";
                    kpiBadgeCritico.innerText = "Suficiente";
                }
            }
        }

        // KPI 3: Destacados
        const kpiDestacados = document.getElementById('kpi_destacados');
        if (kpiDestacados) kpiDestacados.innerText = stats.destacados ?? 0;

        // KPI 4: Valor Inventario
        const kpiValor = document.getElementById('kpi_valor_inventario');
        if (kpiValor && stats.valor_inventario_usd !== undefined) {
            kpiValor.innerText = '$' + Number(stats.valor_inventario_usd).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // Badges en Tabs
        const tabTotal = document.getElementById('tab_badge_total');
        if (tabTotal) tabTotal.innerText = stats.total ?? 0;

        const tabCritico = document.getElementById('tab_badge_critico');
        if (tabCritico) {
            tabCritico.innerText = stats.critico ?? 0;
            tabCritico.className = `px-1.5 py-0.2 rounded-full text-[10px] ${stats.critico > 0 ? 'font-black bg-amber-500 text-slate-900 animate-pulse' : 'bg-gray-100 text-gray-600'}`;
        }

        const tabDestacados = document.getElementById('tab_badge_destacados');
        if (tabDestacados) tabDestacados.innerText = stats.destacados ?? 0;

        const tabCombos = document.getElementById('tab_badge_combos');
        if (tabCombos) tabCombos.innerText = stats.combos ?? 0;

        const tabRecetados = document.getElementById('tab_badge_recetados');
        if (tabRecetados) tabRecetados.innerText = stats.recetados ?? 0;

        const tabSuspendidos = document.getElementById('tab_badge_suspendidos');
        if (tabSuspendidos) tabSuspendidos.innerText = stats.suspendidos ?? 0;
    }

    // ==========================================
    // TOGGLE DESTACADO INSTANTÁNEO (AJAX)
    // ==========================================
    function toggleDestacadoProducto(id, btn) {
        fetch(`/admin/productos/${id}/destacado`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Error al cambiar destacado');
            return data;
        })
        .then(data => {
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

    // ==========================================
    // SUSPENDER / PAUSAR PRODUCTO (SOFT DELETE)
    // ==========================================
    function deleteProducto(id, nombre) {
        Swal.fire({
            title: '¿Suspender Producto?',
            html: `El producto <b>${nombre}</b> será pausado y ocultado temporalmente del catálogo web.<br><br><span class="text-xs text-gray-500">No se perderá su historial ni sus ventas anteriores, y podrás reactivarlo en cualquier momento.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d97706',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Sí, Suspender',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/productos/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Error al suspender');
                    return data;
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Producto Suspendido',
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

    // ==========================================
    // REACTIVAR PRODUCTO
    // ==========================================
    function restoreProducto(id, nombre) {
        Swal.fire({
            title: '¿Reactivar Producto?',
            text: `El producto "${nombre}" volverá a estar visible y disponible para la venta en la tienda.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Sí, Reactivar',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/productos/${id}/restore`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Error al reactivar');
                    return data;
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Reactivado!',
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
</script>
@endpush
@endsection