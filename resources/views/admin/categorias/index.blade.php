@extends('layouts.admin')

@section('title', 'Jerarquía de Categorías - Agrosuministros Rodríguez')

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
                            <span class="material-symbols-outlined text-[#1e549e] text-[32px]">category</span>
                            Estructura de Categorías
                        </h2>
                        <p class="text-xs sm:text-sm text-gray-500 mt-0.5 font-medium">Organiza el catálogo de la tienda en departamentos y subcategorías con fotos e insignias.</p>
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
                        <span>Nueva Categoría</span>
                    </button>
                    @endif
                </div>
            </div>

            {{-- KPI METRICS CARDS --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
                
                {{-- KPI 1: Total Categorías --}}
                <div onclick="filtrarPorEstado('todas')" class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all cursor-pointer group">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Registradas</span>
                        <div class="size-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">folder_copy</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span id="kpi_total_categorias" class="text-2xl font-black text-gray-900 leading-none">{{ $stats['total'] }}</span>
                        <span class="text-[10px] text-gray-400 font-bold uppercase">Categorías</span>
                    </div>
                </div>

                {{-- KPI 2: Principales (Raíz) --}}
                <div onclick="filtrarPorTipo('principales')" class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all cursor-pointer group">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wider">Departamentos Raíz</span>
                        <div class="size-8 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">folder</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span id="kpi_principales_categorias" class="text-2xl font-black text-blue-700 leading-none">{{ $stats['principales'] }}</span>
                        <span class="text-[10px] text-blue-600 font-bold uppercase">Principales</span>
                    </div>
                </div>

                {{-- KPI 3: Subcategorías --}}
                <div onclick="filtrarPorTipo('subcategorias')" class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all cursor-pointer group">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-indigo-700 uppercase tracking-wider">Subcategorías</span>
                        <div class="size-8 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">account_tree</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span id="kpi_subcategorias_categorias" class="text-2xl font-black text-indigo-700 leading-none">{{ $stats['subcategorias'] }}</span>
                        <span class="text-[10px] text-indigo-600 font-bold uppercase">Ramas</span>
                    </div>
                </div>

                {{-- KPI 4: Deshabilitadas --}}
                <div onclick="filtrarPorEstado('deshabilitadas')" class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all cursor-pointer group">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Deshabilitadas</span>
                        <div class="size-8 rounded-xl bg-gray-100 text-gray-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">delete_sweep</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span id="kpi_deshabilitadas_categorias" class="text-2xl font-black text-gray-600 leading-none">{{ $stats['deshabilitadas'] }}</span>
                        <span class="text-[10px] text-gray-400 font-bold uppercase">Ocultas</span>
                    </div>
                </div>

            </div>

            {{-- BARRA DE FILTROS ELEVADA --}}
            <div class="bg-white rounded-2xl sm:rounded-3xl shadow-xs border border-gray-100 mb-6 overflow-hidden">
                <div class="p-3.5 sm:p-4 flex flex-col gap-3">
                    <div class="flex flex-col md:flex-row gap-2.5 sm:gap-3 items-stretch md:items-center">
                        
                        {{-- 1. Buscador en Vivo Avanzado --}}
                        <div class="flex-1 relative group">
                            <span class="material-symbols-outlined absolute left-3.5 top-2.5 text-gray-400 group-focus-within:text-[#1e549e] text-[20px] transition-colors pointer-events-none">search</span>
                            
                            <input type="text" 
                                   id="searchInput" 
                                   value="{{ request('buscar', '') }}"
                                   autocomplete="off"
                                   class="w-full pl-10 pr-20 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#1e549e]/15 focus:border-[#1e549e] transition-all outline-none font-medium text-gray-800 placeholder-gray-400 shadow-2xs" 
                                   placeholder="Buscar categoría por nombre...">
                            
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

                        {{-- 2. Filtro Jerarquía / Tipo (Flex Input Group sin solapamientos) --}}
                        <div id="typeWrapper" class="w-full sm:w-auto md:w-56 flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-xl shadow-2xs transition-all focus-within:ring-2 focus-within:ring-[#1e549e]/15 focus-within:border-[#1e549e]">
                            <span id="typeIcon" class="material-symbols-outlined text-gray-400 text-[20px] shrink-0 pointer-events-none transition-colors">account_tree</span>
                            <select id="typeSelect" class="w-full bg-transparent border-none p-0 outline-none focus:ring-0 text-xs sm:text-sm font-bold text-gray-700 cursor-pointer">
                                <option value="todas" {{ request('tipo', 'todas') === 'todas' ? 'selected' : '' }}>Cualquier Jerarquía</option>
                                <option value="principales" {{ request('tipo') === 'principales' ? 'selected' : '' }}>Solo Principales (Raíz)</option>
                                <option value="subcategorias" {{ request('tipo') === 'subcategorias' ? 'selected' : '' }}>Solo Subcategorías</option>
                            </select>
                        </div>

                        {{-- 3. Filtro Estado (Flex Input Group sin solapamientos) --}}
                        <div id="statusWrapper" class="w-full sm:w-auto md:w-52 flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-xl shadow-2xs transition-all focus-within:ring-2 focus-within:ring-[#1e549e]/15 focus-within:border-[#1e549e]">
                            <span id="statusIcon" class="material-symbols-outlined text-gray-400 text-[20px] shrink-0 pointer-events-none transition-colors">tune</span>
                            <select id="statusSelect" class="w-full bg-transparent border-none p-0 outline-none focus:ring-0 text-xs sm:text-sm font-bold text-gray-700 cursor-pointer">
                                <option value="activas" {{ request('estado', 'activas') === 'activas' ? 'selected' : '' }}>Activas (Visibles)</option>
                                <option value="deshabilitadas" {{ request('estado') === 'deshabilitadas' ? 'selected' : '' }}>Deshabilitadas (Papelera)</option>
                                <option value="todas" {{ request('estado') === 'todas' ? 'selected' : '' }}>Todas las categorías</option>
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
                        <span class="text-xs font-bold text-gray-600">Actualizando categorías...</span>
                    </div>
                </div>
                
                <div id="tableContent">
                    @include('admin.categorias.partials._table')
                </div>
            </div>

        </div>
    </main>
</div>

{{-- ========================================== --}}
{{-- MODAL CREAR / EDITAR CATEGORÍA             --}}
{{-- ========================================== --}}
<div id="categoriaModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity opacity-0" id="catModalBackdrop" onclick="closeModal()"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-center p-4">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-md opacity-0 scale-95 flex flex-col border border-gray-100" id="catModalPanel">
            
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/80">
                <div class="flex items-center gap-2.5">
                    <div class="size-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">category</span>
                    </div>
                    <h3 class="text-base font-black text-gray-900 leading-none" id="modalTitle">Nueva Categoría</h3>
                </div>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-red-500 bg-white p-1 rounded-lg border border-gray-200 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            <form id="categoriaForm" onsubmit="saveCategoria(event)" enctype="multipart/form-data">
                <input type="hidden" id="categoria_id">
                
                <div class="p-6 space-y-4">
                    
                    <div>
                        <label class="block text-[11px] font-black text-gray-500 uppercase tracking-wider mb-1.5 ml-0.5">
                            Nombre de la Categoría <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nombre" name="nombre" required class="w-full h-11 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all font-bold text-gray-900 outline-none text-sm" placeholder="Ej: Fertilizantes, Semillas, Maquinaria">
                    </div>

                    <div>
                        <label class="block text-[11px] font-black text-gray-500 uppercase tracking-wider mb-1.5 ml-0.5">
                            Categoría Padre (Jerarquía)
                        </label>
                        <select id="categoria_padre_id" name="categoria_padre_id" class="w-full h-11 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all font-bold text-gray-800 outline-none cursor-pointer text-sm">
                            <option value="">-- Ninguna (Es Categoría Principal / Raíz) --</option>
                            @foreach($categoriasPadre as $padre)
                                <option value="{{ $padre->id }}">{{ $padre->nombre }}</option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-gray-400 mt-1 ml-0.5">Déjalo vacío para que sea un departamento principal en el menú.</p>
                    </div>

                    {{-- Previsualizador de Imagen --}}
                    <div>
                        <label class="block text-[11px] font-black text-gray-500 uppercase tracking-wider mb-1.5 ml-0.5">
                            Icono o Foto Representativa
                        </label>
                        
                        <div class="flex items-center gap-3">
                            <div id="imagePreviewContainer" class="size-16 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden flex items-center justify-center shrink-0">
                                <img id="imagePreview" src="" alt="Preview" class="size-full object-cover hidden">
                                <span id="imagePlaceholder" class="material-symbols-outlined text-gray-400 text-2xl">image</span>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <input type="file" id="imagen" name="imagen" accept="image/*" onchange="previewImage(event)" class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all border border-gray-200 rounded-xl bg-gray-50 cursor-pointer">
                                <p class="text-[10px] text-gray-400 mt-1">PNG, WEBP o JPG. Max 2MB.</p>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/80 flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-xl font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 text-xs">Cancelar</button>
                    <button type="submit" id="btnSave" class="px-5 py-2 rounded-xl font-black text-white bg-blue-600 hover:bg-blue-700 shadow-sm text-xs flex items-center gap-1.5 transition-all">
                        <span class="material-symbols-outlined text-[16px]">save</span>
                        <span id="btnSaveText">Guardar Categoría</span>
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
    let currentType = @json(request('tipo', 'todas'));
    let currentStatus = @json(request('estado', 'activas'));
    let currentPage = {{ request('page', 1) }};
    let searchTimeout;

    const TYPE_LABELS = {
        'principales': 'Solo Principales (Raíz)',
        'subcategorias': 'Solo Subcategorías'
    };

    const STATUS_LABELS = {
        'deshabilitadas': 'Deshabilitadas (Papelera)',
        'todas': 'Todas las categorías'
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

        // Selector Tipo / Jerarquía
        const typeWrapper = document.getElementById('typeWrapper');
        const typeIcon = document.getElementById('typeIcon');
        if (currentType && currentType !== 'todas') {
            activeCount++;
            if (typeWrapper) {
                typeWrapper.classList.add('border-[#1e549e]', 'bg-blue-50/40', 'text-[#1e549e]', 'ring-1', 'ring-[#1e549e]/30');
                typeWrapper.classList.remove('border-gray-200', 'bg-white');
            }
            if (typeIcon) {
                typeIcon.classList.add('text-[#1e549e]');
                typeIcon.classList.remove('text-gray-400');
            }
        } else {
            if (typeWrapper) {
                typeWrapper.classList.remove('border-[#1e549e]', 'bg-blue-50/40', 'text-[#1e549e]', 'ring-1', 'ring-[#1e549e]/30');
                typeWrapper.classList.add('border-gray-200', 'bg-white');
            }
            if (typeIcon) {
                typeIcon.classList.remove('text-[#1e549e]');
                typeIcon.classList.add('text-gray-400');
            }
        }

        // Selector Estado
        const statusWrapper = document.getElementById('statusWrapper');
        const statusIcon = document.getElementById('statusIcon');
        if (currentStatus && currentStatus !== 'activas') {
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

        if (currentType && currentType !== 'todas') {
            const typeLabel = TYPE_LABELS[currentType] || 'Jerarquía';
            chipsHtml += `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-white text-gray-700 border border-gray-200 shadow-2xs">
                    <span class="material-symbols-outlined text-[14px] text-[#1e549e]">account_tree</span>
                    <span>Jerarquía: <strong class="text-[#1e549e] font-bold">${escapeHtml(typeLabel)}</strong></span>
                    <button type="button" onclick="quitarFiltroTipo()" class="size-4 rounded-full flex items-center justify-center hover:bg-gray-200 text-gray-400 hover:text-gray-800 transition-colors" title="Quitar filtro de jerarquía">
                        <span class="material-symbols-outlined text-[12px]">close</span>
                    </button>
                </span>
            `;
        }

        if (currentStatus && currentStatus !== 'activas') {
            const statusLabel = STATUS_LABELS[currentStatus] || 'Estado';
            chipsHtml += `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-white text-gray-700 border border-gray-200 shadow-2xs">
                    <span class="material-symbols-outlined text-[14px] text-[#1e549e]">tune</span>
                    <span>Estado: <strong class="text-[#1e549e] font-bold">${escapeHtml(statusLabel)}</strong></span>
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

    function quitarFiltroTipo() {
        const typeSel = document.getElementById('typeSelect');
        if (typeSel) typeSel.value = 'todas';
        currentType = 'todas';
        actualizarFiltrosUI();
        fetchData(1);
    }

    function quitarFiltroEstado() {
        const statusSel = document.getElementById('statusSelect');
        if (statusSel) statusSel.value = 'activas';
        currentStatus = 'activas';
        actualizarFiltrosUI();
        fetchData(1);
    }

    function resetearTodosFiltros() {
        const input = document.getElementById('searchInput');
        if (input) input.value = '';
        currentSearch = '';

        const typeSel = document.getElementById('typeSelect');
        if (typeSel) typeSel.value = 'todas';
        currentType = 'todas';

        const statusSel = document.getElementById('statusSelect');
        if (statusSel) statusSel.value = 'activas';
        currentStatus = 'activas';

        actualizarFiltrosUI();
        fetchData(1);
    }

    function filtrarPorTipo(tipo) {
        const typeSel = document.getElementById('typeSelect');
        if (typeSel) typeSel.value = tipo;
        currentType = tipo;
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

            if (currentType && currentType !== 'todas') {
                url.searchParams.set('tipo', currentType);
            } else {
                url.searchParams.delete('tipo');
            }

            if (currentStatus && currentStatus !== 'activas') {
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

    // SELECTORES
    const typeSelectEl = document.getElementById('typeSelect');
    if (typeSelectEl) {
        typeSelectEl.addEventListener('change', function() {
            currentType = this.value;
            actualizarFiltrosUI();
            fetchData(1);
        });
    }

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
            tipo: currentType,
            estado: currentStatus,
            page: page
        });

        fetch(`{{ route('admin.categorias.index') }}?${params.toString()}`, {
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
            console.error('Error al actualizar categorías:', err);
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
        const totalEl = document.getElementById('kpi_total_categorias');
        if (totalEl && stats.total !== undefined) totalEl.textContent = stats.total;

        const princEl = document.getElementById('kpi_principales_categorias');
        if (princEl && stats.principales !== undefined) princEl.textContent = stats.principales;

        const subEl = document.getElementById('kpi_subcategorias_categorias');
        if (subEl && stats.subcategorias !== undefined) subEl.textContent = stats.subcategorias;

        const deshabEl = document.getElementById('kpi_deshabilitadas_categorias');
        if (deshabEl && stats.deshabilitadas !== undefined) deshabEl.textContent = stats.deshabilitadas;
    }

    // ==========================================
    // MODAL CREAR / EDITAR
    // ==========================================
    function openModal(categoria = null) {
        const modal = document.getElementById('categoriaModal');
        const backdrop = document.getElementById('catModalBackdrop');
        const panel = document.getElementById('catModalPanel');
        const form = document.getElementById('categoriaForm');
        const preview = document.getElementById('imagePreview');
        const placeholder = document.getElementById('imagePlaceholder');
        
        form.reset();
        preview.src = '';
        preview.classList.add('hidden');
        placeholder.classList.remove('hidden');

        if (categoria) {
            document.getElementById('modalTitle').innerText = 'Editar Categoría';
            document.getElementById('btnSaveText').innerText = 'Actualizar Cambios';
            document.getElementById('categoria_id').value = categoria.id;
            document.getElementById('nombre').value = categoria.nombre;
            document.getElementById('categoria_padre_id').value = categoria.categoria_padre_id || '';
            
            if (categoria.imagen_url) {
                preview.src = '/' + categoria.imagen_url.replace(/^\/+/, '');
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            }
        } else {
            document.getElementById('modalTitle').innerText = 'Nueva Categoría';
            document.getElementById('btnSaveText').innerText = 'Guardar Categoría';
            document.getElementById('categoria_id').value = '';
        }

        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    function closeModal() {
        const modal = document.getElementById('categoriaModal');
        const backdrop = document.getElementById('catModalBackdrop');
        const panel = document.getElementById('catModalPanel');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 250);
    }

    function previewImage(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('imagePreview');
        const placeholder = document.getElementById('imagePlaceholder');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    function saveCategoria(e) {
        e.preventDefault();
        const id = document.getElementById('categoria_id').value;
        const btn = document.getElementById('btnSave');
        const originalHtml = btn.innerHTML;
        
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[16px]">autorenew</span> Guardando...';
        btn.disabled = true;

        const url = id ? `/admin/categorias/${id}` : `{{ route('admin.categorias.store') }}`;
        const formData = new FormData(document.getElementById('categoriaForm'));

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
            if (!res.ok) throw new Error(data.message || 'Error al guardar la categoría');
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
    // DESHABILITAR (SOFT DELETE)
    // ==========================================
    function deleteCategoria(id, nombre, subcategoriasCount) {
        if (subcategoriasCount > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No se puede deshabilitar',
                html: `La categoría <b>${nombre}</b> tiene <b>${subcategoriasCount} subcategoría(s)</b> activa(s).<br><br><span class="text-xs text-gray-500">Debes reasignar o eliminar sus ramas secundarias antes de deshabilitar la categoría principal.</span>`,
                confirmButtonColor: '#1e549e',
                confirmButtonText: 'Entendido',
                customClass: { popup: 'rounded-2xl' }
            });
            return;
        }

        Swal.fire({
            title: '¿Deshabilitar Categoría?',
            text: `La categoría "${nombre}" dejará de ser visible en la tienda para los compradores. Podrás reactivarla en cualquier momento.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Sí, Deshabilitar',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/categorias/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Error al deshabilitar');
                    return data;
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deshabilitada',
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
    // REACTIVAR CATEGORÍA
    // ==========================================
    function restoreCategoria(id, nombre) {
        Swal.fire({
            title: '¿Reactivar Categoría?',
            text: `La categoría "${nombre}" volverá a estar disponible y visible en el catálogo de la tienda.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Sí, Reactivar',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/categorias/${id}/restore`, {
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
                        title: '¡Reactivada!',
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
                        title: 'No permitido',
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
            const modal = document.getElementById('categoriaModal');
            if (modal && !modal.classList.contains('hidden')) {
                closeModal();
            }
        }
    });
</script>
@endpush
@endsection