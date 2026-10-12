@extends('layouts.admin')

@section('title', 'Gestión de Cajas & Auditoría POS - Agrosuministros Rodríguez')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up pb-16">
            
            {{-- ENCABEZADO PRINCIPAL --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-agro-dark flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[#1e549e] text-[34px]">point_of_sale</span>
                        Gestión de Cajas & Auditoría POS
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1 font-medium">
                        Control de terminales físicos, turnos de venta y arqueos de caja en tiempo real.
                    </p>
                </div>
                
                <div class="flex flex-wrap items-center gap-2">
                    @if(Auth::user()->tienePermiso('gestionar_caja_diaria'))
                    <button type="button" onclick="abrirModalCrearCaja()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 shadow-2xs transition-all active:scale-95">
                        <span class="material-symbols-outlined text-[17px] text-[#1e549e]">add_circle</span>
                        Nueva Caja Física
                    </button>
                    @endif
                    @if(Auth::user()->tienePermiso('crear_venta_manual'))
                        <a href="{{ route('admin.pos.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-[#1e549e] hover:bg-[#153e77] shadow-sm transition-all hover:scale-105 active:scale-95">
                            <span class="material-symbols-outlined text-[17px]">storefront</span>
                            Abrir POS (Ventas)
                        </a>
                    @endif
                </div>
            </div>

            {{-- KPI METRICS CARDS --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
                
                {{-- KPI 1: Ventas Hoy --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all group">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Ventas POS Hoy</span>
                        <div class="size-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">payments</span>
                        </div>
                    </div>
                    <div class="mt-2 flex flex-col">
                        <span class="text-2xl font-black text-gray-900 leading-none">${{ number_format($kpi_ventas_hoy_usd, 2) }}</span>
                        <span class="text-xs text-gray-500 font-medium mt-1">Bs. {{ number_format($kpi_ventas_hoy_ves, 2) }}</span>
                    </div>
                </div>

                {{-- KPI 2: Turnos Activos --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border {{ $kpi_turnos_activos > 0 ? 'border-emerald-300 ring-2 ring-emerald-400/20 bg-emerald-50/20' : 'border-gray-100' }} shadow-2xs hover:shadow-md transition-all group">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold {{ $kpi_turnos_activos > 0 ? 'text-emerald-800' : 'text-gray-400' }} uppercase tracking-wider">Turnos en Operación</span>
                        <div class="size-8 rounded-xl {{ $kpi_turnos_activos > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">badge</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-black {{ $kpi_turnos_activos > 0 ? 'text-emerald-700' : 'text-gray-900' }} leading-none">{{ $kpi_turnos_activos }}</span>
                        @if($kpi_turnos_activos > 0)
                            <span class="inline-flex items-center gap-1 text-[10px] text-emerald-700 font-bold uppercase bg-emerald-100 px-2 py-0.5 rounded-full">
                                <span class="size-1.5 rounded-full bg-emerald-500 animate-ping"></span> En Mostrador
                            </span>
                        @else
                            <span class="text-[10px] text-gray-400 font-bold uppercase">Sin turnos</span>
                        @endif
                    </div>
                </div>

                {{-- KPI 3: Terminales Físicos --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all group">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Terminales Físicos</span>
                        <div class="size-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">point_of_sale</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-black text-gray-900 leading-none">{{ $kpi_cajas_activas }}</span>
                        <span class="text-xs text-gray-400 font-medium">de {{ $kpi_total_cajas }} habilitadas</span>
                    </div>
                </div>

                {{-- KPI 4: Cuadre de Turnos --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border {{ $kpi_descuadres_hoy > 0 ? 'border-rose-300 ring-2 ring-rose-400/20 bg-rose-50/20' : 'border-gray-100' }} shadow-2xs hover:shadow-md transition-all group">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold {{ $kpi_descuadres_hoy > 0 ? 'text-rose-700' : 'text-gray-400' }} uppercase tracking-wider">Cuadre de Turnos Hoy</span>
                        <div class="size-8 rounded-xl {{ $kpi_descuadres_hoy > 0 ? 'bg-rose-100 text-rose-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">{{ $kpi_descuadres_hoy > 0 ? 'warning' : 'verified' }}</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-black {{ $kpi_descuadres_hoy > 0 ? 'text-rose-600' : 'text-emerald-700' }} leading-none">
                            {{ $kpi_descuadres_hoy > 0 ? $kpi_descuadres_hoy : '100%' }}
                        </span>
                        <span class="text-[10px] {{ $kpi_descuadres_hoy > 0 ? 'text-rose-600 font-bold' : 'text-emerald-700 font-bold' }} uppercase">
                            {{ $kpi_descuadres_hoy > 0 ? 'con diferencia' : 'Cuadradas' }}
                        </span>
                    </div>
                </div>

            </div>

            {{-- CONTENEDOR INTEGRADO: TABS + FILTROS + TABLAS --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                
                {{-- Cabecera con Tabs --}}
                <div class="flex items-center justify-between border-b border-gray-100 px-4 sm:px-5 pt-3 bg-white overflow-x-auto custom-scrollbar">
                    <div class="flex gap-3 sm:gap-6 shrink-0">
                        <button type="button" onclick="cambiarPestanaPrincipal('turnos')" id="tab_btn_turnos" class="pb-3 px-1 text-xs sm:text-sm font-bold border-b-2 border-[#1e549e] text-[#1e549e] whitespace-nowrap flex items-center gap-2 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                            <span>Historial y Turnos</span>
                            <span id="tab_badge_turnos" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700">{{ $sesiones->total() }}</span>
                        </button>
                        <button type="button" onclick="cambiarPestanaPrincipal('cajas')" id="tab_btn_cajas" class="pb-3 px-1 text-xs sm:text-sm font-semibold border-b-2 border-transparent text-gray-400 hover:text-gray-700 whitespace-nowrap flex items-center gap-2 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">point_of_sale</span>
                            <span>Cajas Registradoras</span>
                            <span id="tab_badge_cajas" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600">{{ $cajasFisicas->count() }}</span>
                        </button>
                    </div>

                    <div class="pb-2">
                        @if(Auth::user()->tienePermiso('gestionar_caja_diaria'))
                        <button type="button" onclick="abrirModalCrearCaja()" id="btn_nueva_caja_tab" class="hidden inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-[#1e549e] hover:bg-[#153e77] transition-all shadow-2xs">
                            <span class="material-symbols-outlined text-[16px]">add_circle</span>
                            Nueva Caja
                        </button>
                        @endif
                    </div>
                </div>

                {{-- Barra de Filtros (Pestaña Turnos) --}}
                <div id="filtros_turnos_bar" class="p-3 sm:p-4 bg-gray-50/60 border-b border-gray-100 flex flex-col md:flex-row gap-2.5 sm:gap-3 items-stretch md:items-center">
                    
                    {{-- Buscador en Vivo --}}
                    <div class="flex-1 relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-2.5 text-gray-400 text-[18px] pointer-events-none">search</span>
                        <input type="text" id="filtro_buscar" oninput="debounceBuscar()" placeholder="Buscar por cajero, cédula o # de turno..." class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none font-medium text-gray-800 shadow-2xs">
                    </div>

                    {{-- Filtro por Estado --}}
                    <div class="w-full md:w-44 relative">
                        <select id="filtro_estado" onchange="aplicarFiltrosTurnos(1)" class="w-full pl-3.5 pr-8 py-2 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none font-semibold text-gray-700 appearance-none cursor-pointer shadow-2xs">
                            <option value="todas">Todos los turnos</option>
                            <option value="abierta">Turnos en curso</option>
                            <option value="cerrada">Turnos cerrados</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-2.5 text-gray-400 text-[18px] pointer-events-none">expand_more</span>
                    </div>

                    {{-- Filtro por Caja Registradora --}}
                    <div class="w-full md:w-48 relative">
                        <select id="filtro_caja" onchange="aplicarFiltrosTurnos(1)" class="w-full pl-3.5 pr-8 py-2 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none font-semibold text-gray-700 appearance-none cursor-pointer shadow-2xs">
                            <option value="">Todas las cajas</option>
                            @foreach($cajasFisicas as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-2.5 text-gray-400 text-[18px] pointer-events-none">expand_more</span>
                    </div>

                    {{-- Filtro por Fecha --}}
                    <div class="w-full md:w-40 relative">
                        <input type="date" id="filtro_fecha" onchange="aplicarFiltrosTurnos(1)" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none font-medium text-gray-700 cursor-pointer shadow-2xs">
                    </div>

                    {{-- Botón Reset Filtros --}}
                    <button type="button" onclick="limpiarFiltrosTurnos()" class="p-2 rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition-all shadow-2xs flex items-center justify-center shrink-0" title="Limpiar filtros">
                        <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                    </button>
                </div>

                {{-- CONTENIDO SECCIÓN TURNOS --}}
                <div id="seccion_turnos" class="relative">
                    <div id="loadingOverlay" class="absolute inset-0 bg-white/80 backdrop-blur-xs z-20 hidden items-center justify-center">
                        <span class="material-symbols-outlined animate-spin text-[#1e549e] text-4xl">autorenew</span>
                    </div>
                    <div id="tableContent">
                        @include('admin.caja_diaria.partials._table')
                    </div>
                </div>

                {{-- CONTENIDO SECCIÓN CAJAS REGISTRADORAS --}}
                <div id="seccion_cajas" class="hidden relative">
                    <div id="cajasLoadingOverlay" class="absolute inset-0 bg-white/80 backdrop-blur-xs z-20 hidden items-center justify-center">
                        <span class="material-symbols-outlined animate-spin text-[#1e549e] text-4xl">autorenew</span>
                    </div>
                    <div id="cajasContent">
                        @include('admin.caja_diaria.partials._cajas_table')
                    </div>
                </div>

            </div>

        </div>
    </main>
</div>

{{-- ========================================== --}}
{{-- MODAL CREAR / EDITAR CAJA FÍSICA          --}}
{{-- ========================================== --}}
<div id="cajaModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity opacity-0" id="cajaModalBackdrop" onclick="cerrarModalCaja()"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-center p-4">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-md opacity-0 scale-95 flex flex-col" id="cajaModalPanel">
            
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/75">
                <div class="flex items-center gap-2.5">
                    <div class="size-8 rounded-xl bg-blue-50 text-[#1e549e] flex items-center justify-center">
                        <span class="material-symbols-outlined text-[19px]">point_of_sale</span>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900" id="cajaModalTitle">Nueva Caja Registradora</h3>
                </div>
                <button type="button" onclick="cerrarModalCaja()" class="text-gray-400 hover:text-red-500 bg-white p-1 rounded-lg border border-gray-200 transition-colors">
                    <span class="material-symbols-outlined text-[17px]">close</span>
                </button>
            </div>

            <form id="formCaja" onsubmit="guardarCaja(event)">
                <input type="hidden" id="caja_id">
                
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Nombre de la Caja <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="caja_nombre" required maxlength="50" placeholder="Ej: Caja 01 - Principal" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 focus:border-[#1e549e] focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all font-medium text-gray-800 text-sm outline-none">
                        <p class="text-[11px] text-gray-400 mt-1">Identificador visible para los cajeros y en comprobantes.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Cajero Asignado (Predeterminado)
                        </label>
                        <select id="caja_cajero_asignado_id" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 focus:border-[#1e549e] focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all font-medium text-gray-800 text-sm outline-none cursor-pointer">
                            <option value="">-- Sin cajero fijo (Cualquier cajero autorizado) --</option>
                            @foreach($cajeros as $u)
                                <option value="{{ $u->id }}">{{ $u->nombre }} {{ $u->apellido }} ({{ $u->documento_identidad ?: 'DNI N/A' }})</option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-gray-400 mt-1">Si seleccionas un cajero, la caja se le pre-asignará al abrir turno.</p>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/75 flex justify-end gap-2.5">
                    <button type="button" onclick="cerrarModalCaja()" class="px-4 py-2 rounded-xl font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 text-xs transition-all">Cancelar</button>
                    <button type="submit" id="btnGuardarCaja" class="px-5 py-2 rounded-xl font-bold text-white bg-[#1e549e] hover:bg-[#153e77] shadow-sm text-xs flex items-center gap-1.5 transition-all">
                        <span class="material-symbols-outlined text-[17px]">save</span> Guardar Caja
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{-- MODAL AUDITORÍA 360° DE TURNO DE CAJA     --}}
{{-- ========================================== --}}
<div id="detalleModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity opacity-0" id="detalleBackdrop" onclick="cerrarModalDetalle()"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-center p-3 sm:p-4">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-2xl max-h-[92vh] flex flex-col opacity-0 scale-95" id="detallePanel">
            
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white sticky top-0 z-20">
                <div class="flex items-center gap-2.5">
                    <div class="size-9 bg-blue-50 text-[#1e549e] rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">manage_search</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 leading-tight">Auditoría 360° de Turno</h3>
                        <p class="text-[11px] text-gray-400 font-medium">Detalle financiero, métodos de pago y arqueo de gaveta</p>
                    </div>
                </div>
                <button type="button" onclick="cerrarModalDetalle()" class="text-gray-400 hover:text-red-500 bg-gray-50 border border-gray-200 p-1.5 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[17px]">close</span>
                </button>
            </div>

            <div id="detalleContentContainer" class="overflow-y-auto custom-scrollbar flex-1 relative">
                <div id="detalleLoader" class="absolute inset-0 bg-white z-10 flex flex-col items-center justify-center p-12">
                    <span class="material-symbols-outlined animate-spin text-[#1e549e] text-4xl mb-3">autorenew</span>
                    <p class="font-medium text-xs text-gray-500">Cargando desglose del turno...</p>
                </div>
                <div id="detalleInyectado"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@push('scripts')
<script>
    let pestanaActual = 'turnos';
    let debounceTimer = null;

    function cambiarPestanaPrincipal(tab) {
        pestanaActual = tab;
        const btnTurnos = document.getElementById('tab_btn_turnos');
        const btnCajas = document.getElementById('tab_btn_cajas');
        const secTurnos = document.getElementById('seccion_turnos');
        const secCajas = document.getElementById('seccion_cajas');
        const filtrosBar = document.getElementById('filtros_turnos_bar');
        const btnNuevaCaja = document.getElementById('btn_nueva_caja_tab');

        if (tab === 'turnos') {
            btnTurnos.className = 'pb-3 px-1 text-xs sm:text-sm font-bold border-b-2 border-[#1e549e] text-[#1e549e] whitespace-nowrap flex items-center gap-2 transition-colors';
            btnCajas.className = 'pb-3 px-1 text-xs sm:text-sm font-semibold border-b-2 border-transparent text-gray-400 hover:text-gray-700 whitespace-nowrap flex items-center gap-2 transition-colors';
            secTurnos.classList.remove('hidden');
            secCajas.classList.add('hidden');
            filtrosBar.classList.remove('hidden');
            btnNuevaCaja.classList.add('hidden');
        } else {
            btnCajas.className = 'pb-3 px-1 text-xs sm:text-sm font-bold border-b-2 border-[#1e549e] text-[#1e549e] whitespace-nowrap flex items-center gap-2 transition-colors';
            btnTurnos.className = 'pb-3 px-1 text-xs sm:text-sm font-semibold border-b-2 border-transparent text-gray-400 hover:text-gray-700 whitespace-nowrap flex items-center gap-2 transition-colors';
            secCajas.classList.remove('hidden');
            secTurnos.classList.add('hidden');
            filtrosBar.classList.add('hidden');
            btnNuevaCaja.classList.remove('hidden');
            recargarTablaCajas();
        }
    }

    function debounceBuscar() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            aplicarFiltrosTurnos(1);
        }, 300);
    }

    // ==========================================
    // 1. FILTRADO Y PAGINACIÓN DE TURNOS
    // ==========================================
    function aplicarFiltrosTurnos(page = 1) {
        const loading = document.getElementById('loadingOverlay');
        loading.classList.remove('hidden'); loading.classList.add('flex');

        const params = new URLSearchParams({
            filtro_estado: document.getElementById('filtro_estado').value,
            caja_id: document.getElementById('filtro_caja').value,
            fecha: document.getElementById('filtro_fecha').value,
            buscar: document.getElementById('filtro_buscar').value.trim(),
            page: page
        });

        fetch(`{{ route('admin.caja_diaria.index') }}?${params.toString()}`, { 
            headers: { 'X-Requested-With': 'XMLHttpRequest' } 
        })
        .then(response => response.text())
        .then(html => {
            document.getElementById('tableContent').innerHTML = html;
        })
        .finally(() => { 
            loading.classList.add('hidden'); loading.classList.remove('flex'); 
        });
    }

    function limpiarFiltrosTurnos() {
        document.getElementById('filtro_buscar').value = '';
        document.getElementById('filtro_estado').value = 'todas';
        document.getElementById('filtro_caja').value = '';
        document.getElementById('filtro_fecha').value = '';
        aplicarFiltrosTurnos(1);
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('#seccion_turnos .ajax-pagination a')) {
            e.preventDefault();
            const page = new URL(e.target.closest('a').href).searchParams.get('page');
            aplicarFiltrosTurnos(page);
        }
    });

    // ==========================================
    // 2. AUDITORÍA 360° DE TURNO (MODAL DETALLE)
    // ==========================================
    function verDetalleTurno(id) {
        const modal = document.getElementById('detalleModal');
        const backdrop = document.getElementById('detalleBackdrop');
        const panel = document.getElementById('detallePanel');
        const loader = document.getElementById('detalleLoader');
        const content = document.getElementById('detalleInyectado');

        content.innerHTML = '';
        loader.classList.remove('hidden');
        modal.classList.remove('hidden');

        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);

        fetch(`/admin/caja-diaria/${id}/detalle`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.text())
        .then(html => {
            content.innerHTML = html;
            loader.classList.add('hidden');
        })
        .catch(err => {
            content.innerHTML = `<div class="p-8 text-center text-rose-600 font-medium">Error al cargar información del turno.</div>`;
            loader.classList.add('hidden');
        });
    }

    function cerrarModalDetalle() {
        const modal = document.getElementById('detalleModal');
        const backdrop = document.getElementById('detalleBackdrop');
        const panel = document.getElementById('detallePanel');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 200);
    }

    // ==========================================
    // 3. GESTIÓN DE CAJAS FÍSICAS (TERMINALES)
    // ==========================================
    function abrirModalCrearCaja() {
        document.getElementById('caja_id').value = '';
        document.getElementById('caja_nombre').value = '';
        document.getElementById('caja_cajero_asignado_id').value = '';
        document.getElementById('cajaModalTitle').innerText = 'Nueva Caja Registradora';
        abrirModalCaja();
    }

    function editarCajaModal(caja) {
        document.getElementById('caja_id').value = caja.id;
        document.getElementById('caja_nombre').value = caja.nombre;
        document.getElementById('caja_cajero_asignado_id').value = caja.cajero_asignado_id || '';
        document.getElementById('cajaModalTitle').innerText = 'Editar Caja Registradora';
        abrirModalCaja();
    }

    function abrirModalCaja() {
        const modal = document.getElementById('cajaModal');
        const backdrop = document.getElementById('cajaModalBackdrop');
        const panel = document.getElementById('cajaModalPanel');
        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
            document.getElementById('caja_nombre').focus();
        }, 10);
    }

    function cerrarModalCaja() {
        const modal = document.getElementById('cajaModal');
        const backdrop = document.getElementById('cajaModalBackdrop');
        const panel = document.getElementById('cajaModalPanel');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 200);
    }

    function guardarCaja(e) {
        e.preventDefault();
        const id = document.getElementById('caja_id').value;
        const nombre = document.getElementById('caja_nombre').value.trim();
        const cajeroId = document.getElementById('caja_cajero_asignado_id').value;

        const btn = document.getElementById('btnGuardarCaja');
        btn.disabled = true;

        const url = id ? `/admin/caja-diaria/cajas/${id}` : `{{ route('admin.caja_diaria.cajas.store') }}`;
        const method = id ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                nombre: nombre,
                cajero_asignado_id: cajeroId || null
            })
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Error al guardar la caja');
            return data;
        })
        .then(data => {
            cerrarModalCaja();
            Swal.fire({
                icon: 'success',
                title: '¡Operación Exitosa!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
            recargarTablaCajas();
        })
        .catch(err => {
            Swal.fire('Atención', err.message, 'warning');
        })
        .finally(() => {
            btn.disabled = false;
        });
    }

    function toggleCaja(id) {
        fetch(`/admin/caja-diaria/cajas/${id}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Error al cambiar estado');
            return data;
        })
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'Actualizado',
                text: data.message,
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
            recargarTablaCajas();
        })
        .catch(err => {
            Swal.fire('No permitido', err.message, 'warning');
            recargarTablaCajas();
        });
    }

    function eliminarCaja(id, nombre) {
        Swal.fire({
            title: `¿Eliminar ${nombre}?`,
            text: 'Solo podrá eliminarse si nunca ha tenido historial de ventas o turnos.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed) {
                fetch(`/admin/caja-diaria/cajas/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'No se pudo eliminar');
                    return data;
                })
                .then(data => {
                    Swal.fire('Eliminada', data.message, 'success');
                    recargarTablaCajas();
                })
                .catch(err => {
                    Swal.fire('No permitido', err.message, 'warning');
                });
            }
        });
    }

    function recargarTablaCajas() {
        const overlay = document.getElementById('cajasLoadingOverlay');
        overlay.classList.remove('hidden'); overlay.classList.add('flex');

        fetch(`{{ route('admin.caja_diaria.index') }}?tab=cajas`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById('cajasContent').innerHTML = html;
        })
        .finally(() => {
            overlay.classList.add('hidden'); overlay.classList.remove('flex');
        });
    }
</script>
@endpush
@endsection