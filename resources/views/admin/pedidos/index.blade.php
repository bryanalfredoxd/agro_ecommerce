@extends('layouts.admin')

@section('title', 'Gestión de Pedidos y Pagos - Agrosuministros Rodríguez')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up pb-12">
            
            {{-- ENCABEZADO PRINCIPAL --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-agro-dark flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[#1e549e] text-[34px]">receipt_long</span>
                        Gestión de Pedidos y Pagos
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1 font-medium">
                        Monitorea ventas en vivo, audita transferencias bancarias y coordina el despacho logístico en una sola pantalla.
                    </p>
                </div>
                
                <div class="flex items-center gap-2">
                    <button type="button" onclick="fetchData(1)" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 shadow-2xs transition-all active:scale-95" title="Actualizar datos">
                        <span class="material-symbols-outlined text-[17px] text-blue-600">sync</span>
                        Refrescar
                    </button>
                    @if(Auth::user()->tienePermiso('crear_venta_manual'))
                        <a href="{{ route('admin.pos.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-black text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all hover:scale-105 active:scale-95">
                            <span class="material-symbols-outlined text-[17px]">point_of_sale</span>
                            Terminal POS
                        </a>
                    @endif
                </div>
            </div>

            {{-- KPI METRICS CARDS --}}
            @php
                $visibleKpisCount = count(array_intersect(['todos', 'por_verificar', 'por_despachar', 'en_ruta'], $allowedTabs));
            @endphp
            @if($visibleKpisCount > 0)
            <div class="grid grid-cols-2 {{ $visibleKpisCount == 1 ? 'lg:grid-cols-1' : ($visibleKpisCount == 2 ? 'lg:grid-cols-2' : ($visibleKpisCount == 3 ? 'lg:grid-cols-3' : 'lg:grid-cols-4')) }} gap-3 sm:gap-4 mb-6">
                
                {{-- KPI 1: Total Pedidos --}}
                @if(in_array('todos', $allowedTabs))
                <div onclick="filtrarPorTab('todos')" class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all cursor-pointer group">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Pedidos</span>
                        <div class="size-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span id="kpi_total" class="text-2xl font-black text-gray-900 leading-none">{{ $stats['total'] }}</span>
                        <span class="text-[10px] text-gray-400 font-bold uppercase">Registrados</span>
                    </div>
                </div>
                @endif

                {{-- KPI 2: Pagos por Verificar (PRIORITARIO) --}}
                @if(in_array('por_verificar', $allowedTabs))
                <div id="kpi_card_por_verificar" onclick="filtrarPorTab('por_verificar')" class="bg-white p-4 sm:p-5 rounded-2xl border {{ $stats['por_verificar'] > 0 ? 'border-amber-300 ring-2 ring-amber-400/20 bg-amber-50/20' : 'border-gray-100' }} shadow-2xs hover:shadow-md transition-all cursor-pointer group">
                    <div class="flex items-center justify-between">
                        <span id="kpi_label_por_verificar" class="text-[11px] font-bold {{ $stats['por_verificar'] > 0 ? 'text-amber-800' : 'text-gray-400' }} uppercase tracking-wider">
                            Por Verificar Pago
                        </span>
                        <div id="kpi_icon_por_verificar" class="size-8 rounded-xl {{ $stats['por_verificar'] > 0 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">pending_actions</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span id="kpi_por_verificar" class="text-2xl font-black {{ $stats['por_verificar'] > 0 ? 'text-amber-700' : 'text-gray-900' }} leading-none">
                            {{ $stats['por_verificar'] }}
                        </span>
                        @if($stats['por_verificar'] > 0)
                            <span id="kpi_badge_por_verificar" class="text-[10px] text-amber-700 font-black uppercase bg-amber-100 px-1.5 py-0.5 rounded animate-pulse">¡Atención!</span>
                        @else
                            <span id="kpi_badge_por_verificar" class="text-[10px] text-emerald-600 font-bold uppercase">Al día</span>
                        @endif
                    </div>
                </div>
                @endif

                {{-- KPI 3: Por Despachar --}}
                @if(in_array('por_despachar', $allowedTabs))
                <div onclick="filtrarPorTab('por_despachar')" class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all cursor-pointer group">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Listos para Despacho</span>
                        <div class="size-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span id="kpi_por_despachar" class="text-2xl font-black text-gray-900 leading-none">{{ $stats['por_despachar'] }}</span>
                        <span class="text-[10px] text-indigo-600 font-bold uppercase">Pagados</span>
                    </div>
                </div>
                @endif

                {{-- KPI 4: En Ruta --}}
                @if(in_array('en_ruta', $allowedTabs))
                <div onclick="filtrarPorTab('en_ruta')" class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all cursor-pointer group">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">En Ruta (Delivery)</span>
                        <div class="size-8 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">local_shipping</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span id="kpi_en_ruta" class="text-2xl font-black text-gray-900 leading-none">{{ $stats['en_ruta'] }}</span>
                        <span class="text-[10px] text-orange-600 font-bold uppercase">En entrega</span>
                    </div>
                </div>
                @endif

            </div>
            @endif

            {{-- PANEL DE FILTROS Y CONTROLES --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-2 mb-6">
                
                {{-- Tabs de Estados Inteligentes --}}
                <div class="flex overflow-x-auto custom-scrollbar border-b border-gray-100 px-4 pt-2 gap-2 sm:gap-4">
                    @if(in_array('todos', $allowedTabs))
                    <button class="status-tab {{ $filtroEstado === 'todos' ? 'active border-primary text-agro-dark font-black' : 'border-transparent text-gray-500 font-bold' }} pb-3 px-2 text-xs sm:text-sm border-b-2 whitespace-nowrap flex items-center gap-1.5 transition-colors" data-estado="todos">
                        <span>Todos los Pedidos</span>
                        <span id="tab_badge_todos" class="px-1.5 py-0.2 rounded-full text-[10px] bg-gray-100 text-gray-600">{{ $stats['total'] }}</span>
                    </button>
                    @endif
                    
                    @if(in_array('por_verificar', $allowedTabs))
                    <button class="status-tab {{ $filtroEstado === 'por_verificar' ? 'active border-primary text-agro-dark font-black' : 'border-transparent text-gray-500 font-bold' }} pb-3 px-2 text-xs sm:text-sm border-b-2 whitespace-nowrap flex items-center gap-1.5 transition-colors" data-estado="por_verificar">
                        <span class="material-symbols-outlined text-[16px] text-amber-500">pending_actions</span>
                        <span>Por Verificar Pago</span>
                        @if($stats['por_verificar'] > 0)
                            <span id="tab_badge_por_verificar" class="px-1.5 py-0.2 rounded-full text-[10px] font-black bg-amber-500 text-slate-900 animate-pulse">{{ $stats['por_verificar'] }}</span>
                        @else
                            <span id="tab_badge_por_verificar" class="px-1.5 py-0.2 rounded-full text-[10px] bg-gray-100 text-gray-600">0</span>
                        @endif
                    </button>
                    @endif
                    
                    @if(in_array('por_despachar', $allowedTabs))
                    <button class="status-tab {{ $filtroEstado === 'por_despachar' ? 'active border-primary text-agro-dark font-black' : 'border-transparent text-gray-500 font-bold' }} pb-3 px-2 text-xs sm:text-sm border-b-2 whitespace-nowrap flex items-center gap-1.5 transition-colors" data-estado="por_despachar">
                        <span>Por Despachar</span>
                        <span id="tab_badge_por_despachar" class="px-1.5 py-0.2 rounded-full text-[10px] bg-blue-100 text-blue-700 font-bold">{{ $stats['por_despachar'] }}</span>
                    </button>
                    @endif
                    
                    @if(in_array('en_ruta', $allowedTabs))
                    <button class="status-tab {{ $filtroEstado === 'en_ruta' ? 'active border-primary text-agro-dark font-black' : 'border-transparent text-gray-500 font-bold' }} pb-3 px-2 text-xs sm:text-sm border-b-2 whitespace-nowrap flex items-center gap-1.5 transition-colors" data-estado="en_ruta">
                        <span>En Ruta</span>
                        <span id="tab_badge_en_ruta" class="px-1.5 py-0.2 rounded-full text-[10px] bg-orange-100 text-orange-700 font-bold">{{ $stats['en_ruta'] }}</span>
                    </button>
                    @endif
                    
                    @if(in_array('completados', $allowedTabs))
                    <button class="status-tab {{ $filtroEstado === 'completados' ? 'active border-primary text-agro-dark font-black' : 'border-transparent text-gray-500 font-bold' }} pb-3 px-2 text-xs sm:text-sm border-b-2 whitespace-nowrap flex items-center gap-1.5 transition-colors" data-estado="completados">
                        <span>Completados</span>
                        <span id="tab_badge_completados" class="px-1.5 py-0.2 rounded-full text-[10px] bg-emerald-100 text-emerald-700 font-bold">{{ $stats['completados'] }}</span>
                    </button>
                    @endif
                    
                    @if(in_array('cancelados', $allowedTabs))
                    <button class="status-tab {{ $filtroEstado === 'cancelados' ? 'active border-primary text-agro-dark font-black' : 'border-transparent text-gray-500 font-bold' }} pb-3 px-2 text-xs sm:text-sm border-b-2 whitespace-nowrap flex items-center gap-1.5 transition-colors" data-estado="cancelados">
                        <span>Cancelados / Devueltos</span>
                        <span id="tab_badge_cancelados" class="px-1.5 py-0.2 rounded-full text-[10px] bg-rose-100 text-rose-700 font-bold">{{ $stats['cancelados'] }}</span>
                    </button>
                    @endif
                </div>

                {{-- Filtros secundarios: Buscador, Canal y Método de Pago --}}
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
                                   placeholder="Buscar por # Pedido, Factura, Cédula/RIF, Cliente o Ref. Bancaria...">
                            
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

                        {{-- 2. Filtro Canal de Venta --}}
                        <div id="canalWrapper" class="w-full sm:w-auto lg:w-56 flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-xl shadow-2xs transition-all focus-within:ring-2 focus-within:ring-[#1e549e]/15 focus-within:border-[#1e549e]">
                            <span id="canalIcon" class="material-symbols-outlined text-gray-400 text-[20px] shrink-0 pointer-events-none transition-colors">storefront</span>
                            <select id="canalSelect" class="w-full bg-transparent border-none p-0 outline-none focus:ring-0 text-xs sm:text-sm font-bold text-gray-700 cursor-pointer">
                                <option value="todos" {{ request('filtro_canal') === 'todos' ? 'selected' : '' }}>Todos los Canales</option>
                                <option value="web" {{ request('filtro_canal') === 'web' ? 'selected' : '' }}>Tienda Web (Online)</option>
                                <option value="whatsapp" {{ request('filtro_canal') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                <option value="tienda_fisica" {{ request('filtro_canal') === 'tienda_fisica' ? 'selected' : '' }}>Mostrador (POS)</option>
                            </select>
                        </div>

                        {{-- 3. Filtro Método de Pago --}}
                        <div id="metodoWrapper" class="w-full sm:w-auto lg:w-60 flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-xl shadow-2xs transition-all focus-within:ring-2 focus-within:ring-[#1e549e]/15 focus-within:border-[#1e549e]">
                            <span id="metodoIcon" class="material-symbols-outlined text-gray-400 text-[20px] shrink-0 pointer-events-none transition-colors">payments</span>
                            <select id="metodoSelect" class="w-full bg-transparent border-none p-0 outline-none focus:ring-0 text-xs sm:text-sm font-bold text-gray-700 cursor-pointer">
                                <option value="todos" {{ request('filtro_metodo') === 'todos' ? 'selected' : '' }}>Todos los Métodos</option>
                                <option value="pago_movil" {{ request('filtro_metodo') === 'pago_movil' ? 'selected' : '' }}>Pago Móvil</option>
                                <option value="zelle" {{ request('filtro_metodo') === 'zelle' ? 'selected' : '' }}>Zelle (USD)</option>
                                <option value="binance" {{ request('filtro_metodo') === 'binance' ? 'selected' : '' }}>Binance Pay</option>
                                <option value="transferencia" {{ request('filtro_metodo') === 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                <option value="punto_venta" {{ request('filtro_metodo') === 'punto_venta' ? 'selected' : '' }}>Punto de Venta</option>
                                <option value="efectivo_usd" {{ request('filtro_metodo') === 'efectivo_usd' ? 'selected' : '' }}>Efectivo USD ($)</option>
                                <option value="efectivo_bs" {{ request('filtro_metodo') === 'efectivo_bs' ? 'selected' : '' }}>Efectivo Bolívares</option>
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
                        <span class="text-xs font-bold text-gray-600">Actualizando pedidos...</span>
                    </div>
                </div>
                
                <div id="tableContent">
                    @include('admin.pedidos.partials._table')
                </div>
            </div>

        </div>
    </main>
</div>

{{-- ========================================== --}}
{{-- MODAL VISOR DE COMPROBANTES DE PAGO        --}}
{{-- ========================================== --}}
<div id="comprobanteModal" class="fixed inset-0 z-[120] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity opacity-0" id="comprobanteBackdrop" onclick="closeComprobante()"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-center p-4">
        <div class="relative transform overflow-hidden rounded-3xl bg-slate-950 text-left shadow-2xl transition-all w-full max-w-xl opacity-0 scale-95 flex flex-col border border-white/10" id="comprobantePanel">
            
            <div class="px-6 py-4 border-b border-white/10 flex justify-between items-center bg-slate-900">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-400 text-[20px]">receipt_long</span>
                    <h3 class="text-xs font-black text-white tracking-wider uppercase">Comprobante - Ref: <span id="visor_referencia" class="text-emerald-400 font-mono"></span></h3>
                </div>
                <button type="button" onclick="closeComprobante()" class="text-slate-400 hover:text-white bg-white/5 p-1.5 rounded-lg border border-white/10 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            <div class="p-4 flex justify-center items-center bg-slate-900/60 max-h-[70vh] overflow-y-auto custom-scrollbar">
                <img id="visor_imagen" src="" alt="Comprobante de Pago" class="max-w-full max-h-[65vh] object-contain rounded-xl shadow-lg border border-white/5">
            </div>

            {{-- Botones de acción directa en el comprobante si está en revisión --}}
            <div id="visor_acciones" class="px-6 py-3.5 border-t border-white/10 bg-slate-900 flex justify-between items-center gap-3">
                <a id="visor_descargar" href="" target="_blank" class="text-xs text-slate-400 hover:text-white inline-flex items-center gap-1 font-bold">
                    <span class="material-symbols-outlined text-[15px]">open_in_new</span> Abrir original
                </a>
                <div id="visor_botones_resolucion" class="flex gap-2"></div>
            </div>
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{-- MODAL CAMBIO DE ESTADO LOGÍSTICO           --}}
{{-- ========================================== --}}
<div id="statusModal" class="fixed inset-0 z-[110] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity opacity-0" id="statusBackdrop" onclick="closeStatusModal()"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-center p-4">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-sm opacity-0 scale-95 flex flex-col" id="statusPanel">
            
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/80">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-600 text-[22px]" id="statusModalIcon">local_shipping</span>
                    <h3 class="text-base font-black text-agro-dark" id="statusModalTitle">Despacho Logístico</h3>
                </div>
                <button type="button" onclick="closeStatusModal()" class="text-gray-400 hover:text-red-500 bg-white p-1 rounded-lg border border-gray-200 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            <form id="statusForm" onsubmit="saveStatus(event)">
                <input type="hidden" id="pedido_id">
                
                <div class="p-6">
                    <label class="block text-[11px] font-black text-gray-500 uppercase tracking-wider mb-2">Fase Operativa del Pedido</label>
                    <select id="estado_select" name="estado" required class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all font-bold text-gray-800 outline-none cursor-pointer">
                        <option value="preparacion">En Preparación / Empaque</option>
                        <option value="en_ruta">En Ruta (Con Repartidor)</option>
                        <option value="entregado">Entregado al Cliente ✅</option>
                        <option value="cancelado" class="text-red-600 font-bold">Cancelar Pedido</option>
                    </select>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/80 flex justify-end gap-3">
                    <button type="button" onclick="closeStatusModal()" class="px-4 py-2 rounded-xl font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 text-xs">Cerrar</button>
                    <button type="submit" id="btnSaveStatus" class="px-5 py-2 rounded-xl font-black text-white bg-blue-600 hover:bg-blue-700 shadow-sm text-xs flex items-center gap-1.5 transition-all">
                        <span class="material-symbols-outlined text-[16px]">save</span> Guardar Estado
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{-- MODAL DETALLE 360° DEL PEDIDO Y AUDITORÍA  --}}
{{-- ========================================== --}}
<div id="detalleModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity opacity-0" id="detalleBackdrop" onclick="closeDetalleModal()"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-center p-3 sm:p-6">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-3xl opacity-0 scale-95 border border-gray-100" 
             id="detallePanel"
             style="height: 86vh; max-height: 86vh; display: flex; flex-direction: column; min-height: 0; overflow: hidden;">
            
            {{-- Header --}}
            <div class="px-4 sm:px-6 py-3.5 sm:py-4 border-b border-gray-100 flex justify-between items-center bg-white shrink-0">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-slate-900 text-white flex items-center justify-center shadow-xs">
                        <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-900 leading-none">Detalle Completo del Pedido</h3>
                        <p class="text-xs text-blue-600 font-bold mt-1" id="detalle_pedido_titulo">Pedido #000000</p>
                    </div>
                </div>
                <button type="button" onclick="closeDetalleModal()" class="text-gray-400 hover:text-red-500 bg-gray-50 hover:bg-red-50 p-2 rounded-xl transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            {{-- Contenedor donde se inyecta el HTML de _detalles.blade.php --}}
            <div id="detalleContent" 
                 class="custom-scrollbar relative p-0"
                 style="flex: 1 1 0%; min-height: 0; overflow-y: auto !important; -webkit-overflow-scrolling: touch; overscroll-behavior: contain;">
                <div id="detalleLoader" class="absolute inset-0 bg-white z-10 flex flex-col items-center justify-center p-12">
                    <span class="material-symbols-outlined animate-spin text-blue-600 text-4xl mb-3">autorenew</span>
                    <p class="font-bold text-gray-500 text-xs">Cargando orden y auditoría...</p>
                </div>
                
                <div id="detalleInyectado"></div>
            </div>
            
            {{-- Footer --}}
            <div class="px-6 py-3.5 border-t border-gray-100 bg-gray-50/80 flex justify-end shrink-0">
                <button type="button" onclick="closeDetalleModal()" class="px-5 py-2 rounded-xl font-black text-white bg-slate-900 hover:bg-black shadow-xs text-xs transition-all">
                    Cerrar Detalle
                </button>
            </div>
        </div>
    </div>
</div>

{{-- SweetAlert2 para Confirmaciones Seguras --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@push('scripts')
<script>
    // PERMISOS DEL USUARIO
    const puedeGestionarPagos = {{ Auth::user()->tienePermiso('gestionar_pagos') ? 'true' : 'false' }};
    const puedeActualizarEstado = {{ Auth::user()->tienePermiso('actualizar_estado_pedido') ? 'true' : 'false' }};
    const puedeCancelar = {{ (Auth::user()->esAdmin() || Auth::user()->tienePermiso('ver_pedidos_cancelados')) ? 'true' : 'false' }};
    const esSoloReparto = {{ (!Auth::user()->esAdmin() && Auth::user()->tienePermiso('ver_pedidos_en_ruta') && !Auth::user()->tienePermiso('ver_pedidos_despacho') && !Auth::user()->tienePermiso('ver_pedidos_todos')) ? 'true' : 'false' }};
    const allowedTabs = @json($allowedTabs);

    // ESTADO GLOBAL DE FILTROS AJAX
    let currentFiltroEstado = '{{ $filtroEstado }}';
    let currentSearch = @json(request('buscar', ''));
    let currentCanal = @json(request('filtro_canal', 'todos'));
    let currentMetodo = @json(request('filtro_metodo', 'todos'));
    let searchTimeout;
    let currentPage = 1;

    // Diccionarios de etiquetas legibles para Chips
    const CANAL_LABELS = {
        'web': 'Tienda Web',
        'whatsapp': 'WhatsApp',
        'tienda_fisica': 'Mostrador (POS)'
    };

    const METODO_LABELS = {
        'pago_movil': 'Pago Móvil',
        'zelle': 'Zelle (USD)',
        'binance': 'Binance Pay',
        'transferencia': 'Transferencia',
        'punto_venta': 'Punto de Venta',
        'efectivo_usd': 'Efectivo USD ($)',
        'efectivo_bs': 'Efectivo Bolívares'
    };

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Inicializar Tab Activa y Filtros UI al cargar la página
    document.addEventListener('DOMContentLoaded', () => {
        if (currentFiltroEstado) {
            const activeTab = document.querySelector(`.status-tab[data-estado="${currentFiltroEstado}"]`);
            if (activeTab) {
                document.querySelectorAll('.status-tab').forEach(t => {
                    t.classList.remove('border-primary', 'text-agro-dark', 'active');
                    t.classList.add('border-transparent', 'text-gray-500');
                });
                activeTab.classList.remove('border-transparent', 'text-gray-500');
                activeTab.classList.add('border-primary', 'text-agro-dark', 'active');
            }
        }
        actualizarFiltrosUI();
    });

    // CLIC EN TABS DE ESTADO
    document.querySelectorAll('.status-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            filtrarPorTab(this.getAttribute('data-estado'));
        });
    });

    function filtrarPorTab(estado) {
        if (!allowedTabs.includes(estado)) {
            return;
        }

        document.querySelectorAll('.status-tab').forEach(t => {
            t.classList.remove('border-primary', 'text-agro-dark', 'active');
            t.classList.add('border-transparent', 'text-gray-500');
        });

        const targetTab = document.querySelector(`.status-tab[data-estado="${estado}"]`);
        if (targetTab) {
            targetTab.classList.remove('border-transparent', 'text-gray-500');
            targetTab.classList.add('border-primary', 'text-agro-dark', 'active');
        }

        currentFiltroEstado = estado;
        fetchData(1);
    }

    // ACTUALIZAR ESTADO VISUAL DE FILTROS, CHIPS Y BOTONES
    function actualizarFiltrosUI() {
        const searchInput = document.getElementById('searchInput');
        const btnClearSearch = document.getElementById('btn_clear_search');
        const searchSpinner = document.getElementById('search_spinner');
        const canalWrapper = document.getElementById('canalWrapper');
        const canalIcon = document.getElementById('canalIcon');
        const metodoWrapper = document.getElementById('metodoWrapper');
        const metodoIcon = document.getElementById('metodoIcon');
        const btnResetFiltros = document.getElementById('btn_reset_filtros');
        const badgeFiltrosCount = document.getElementById('badge_filtros_count');
        const chipsWrapper = document.getElementById('chips_filtros_activos');
        const chipsContainer = document.getElementById('chips_container');

        // 1. Botón limpiar búsqueda
        const hasSearch = currentSearch && currentSearch.trim().length > 0;
        if (btnClearSearch) {
            btnClearSearch.style.display = hasSearch ? 'flex' : 'none';
        }

        // 2. Estilos dinámicos para Canal de Venta
        if (canalWrapper) {
            if (currentCanal && currentCanal !== 'todos') {
                canalWrapper.classList.add('border-[#1e549e]', 'bg-blue-50/40', 'ring-1', 'ring-[#1e549e]/30');
                canalWrapper.classList.remove('border-gray-200', 'bg-white');
                if (canalIcon) {
                    canalIcon.classList.add('text-[#1e549e]');
                    canalIcon.classList.remove('text-gray-400');
                }
            } else {
                canalWrapper.classList.remove('border-[#1e549e]', 'bg-blue-50/40', 'ring-1', 'ring-[#1e549e]/30');
                canalWrapper.classList.add('border-gray-200', 'bg-white');
                if (canalIcon) {
                    canalIcon.classList.remove('text-[#1e549e]');
                    canalIcon.classList.add('text-gray-400');
                }
            }
        }

        // 3. Estilos dinámicos para Método de Pago
        if (metodoWrapper) {
            if (currentMetodo && currentMetodo !== 'todos') {
                metodoWrapper.classList.add('border-[#1e549e]', 'bg-blue-50/40', 'ring-1', 'ring-[#1e549e]/30');
                metodoWrapper.classList.remove('border-gray-200', 'bg-white');
                if (metodoIcon) {
                    metodoIcon.classList.add('text-[#1e549e]');
                    metodoIcon.classList.remove('text-gray-400');
                }
            } else {
                metodoWrapper.classList.remove('border-[#1e549e]', 'bg-blue-50/40', 'ring-1', 'ring-[#1e549e]/30');
                metodoWrapper.classList.add('border-gray-200', 'bg-white');
                if (metodoIcon) {
                    metodoIcon.classList.remove('text-[#1e549e]');
                    metodoIcon.classList.add('text-gray-400');
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

        if (currentCanal && currentCanal !== 'todos') {
            activeCount++;
            const labelCanal = CANAL_LABELS[currentCanal] || currentCanal;
            chipsHtml += `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-white border border-[#1e549e]/30 text-gray-800 shadow-2xs">
                    <span class="material-symbols-outlined text-[14px] text-[#1e549e]">storefront</span>
                    <span>Canal: <strong class="text-[#1e549e] font-bold">${escapeHtml(labelCanal)}</strong></span>
                    <button type="button" onclick="quitarFiltroCanal()" class="size-4 rounded-full flex items-center justify-center hover:bg-gray-200 text-gray-400 hover:text-gray-800 transition-colors" title="Quitar filtro de canal">
                        <span class="material-symbols-outlined text-[12px]">close</span>
                    </button>
                </span>
            `;
        }

        if (currentMetodo && currentMetodo !== 'todos') {
            activeCount++;
            const labelMetodo = METODO_LABELS[currentMetodo] || currentMetodo;
            chipsHtml += `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-white border border-[#1e549e]/30 text-gray-800 shadow-2xs">
                    <span class="material-symbols-outlined text-[14px] text-[#1e549e]">payments</span>
                    <span>Método: <strong class="text-[#1e549e] font-bold">${escapeHtml(labelMetodo)}</strong></span>
                    <button type="button" onclick="quitarFiltroMetodo()" class="size-4 rounded-full flex items-center justify-center hover:bg-gray-200 text-gray-400 hover:text-gray-800 transition-colors" title="Quitar filtro de método">
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

    function quitarFiltroCanal() {
        const canalSel = document.getElementById('canalSelect');
        if (canalSel) canalSel.value = 'todos';
        currentCanal = 'todos';
        actualizarFiltrosUI();
        fetchData(1);
    }

    function quitarFiltroMetodo() {
        const metodoSel = document.getElementById('metodoSelect');
        if (metodoSel) metodoSel.value = 'todos';
        currentMetodo = 'todos';
        actualizarFiltrosUI();
        fetchData(1);
    }

    function resetearTodosFiltros() {
        const input = document.getElementById('searchInput');
        if (input) input.value = '';
        currentSearch = '';

        const canalSel = document.getElementById('canalSelect');
        if (canalSel) canalSel.value = 'todos';
        currentCanal = 'todos';

        const metodoSel = document.getElementById('metodoSelect');
        if (metodoSel) metodoSel.value = 'todos';
        currentMetodo = 'todos';

        actualizarFiltrosUI();
        fetchData(1);
    }

    // SINCRONIZACIÓN DE PARÁMETROS EN LA URL
    function sincronizarURL(page = 1) {
        try {
            const url = new URL(window.location.href);
            if (currentFiltroEstado && currentFiltroEstado !== 'todos') {
                url.searchParams.set('filtro_estado', currentFiltroEstado);
            } else {
                url.searchParams.delete('filtro_estado');
            }

            if (currentSearch && currentSearch.trim() !== '') {
                url.searchParams.set('buscar', currentSearch.trim());
            } else {
                url.searchParams.delete('buscar');
            }

            if (currentCanal && currentCanal !== 'todos') {
                url.searchParams.set('filtro_canal', currentCanal);
            } else {
                url.searchParams.delete('filtro_canal');
            }

            if (currentMetodo && currentMetodo !== 'todos') {
                url.searchParams.set('filtro_metodo', currentMetodo);
            } else {
                url.searchParams.delete('filtro_metodo');
            }

            if (page > 1) {
                url.searchParams.set('page', page);
            } else {
                url.searchParams.delete('page');
            }

            window.history.replaceState({}, '', url.toString());
        } catch (e) {
            // No-op si no se soporta history API
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

    // SELECTORES DE CANAL Y MÉTODO
    const canalSelectEl = document.getElementById('canalSelect');
    if (canalSelectEl) {
        canalSelectEl.addEventListener('change', function() {
            currentCanal = this.value;
            actualizarFiltrosUI();
            fetchData(1);
        });
    }

    const metodoSelectEl = document.getElementById('metodoSelect');
    if (metodoSelectEl) {
        metodoSelectEl.addEventListener('change', function() {
            currentMetodo = this.value;
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
            filtro_estado: currentFiltroEstado, 
            buscar: currentSearch, 
            filtro_canal: currentCanal,
            filtro_metodo: currentMetodo,
            page: page 
        });

        fetch(`{{ route('admin.pedidos.index') }}?${params.toString()}`, { 
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
            console.error('Error al actualizar datos en vivo:', err);
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

        // KPI 1: Total Pedidos
        const kpiTotal = document.getElementById('kpi_total');
        if (kpiTotal) kpiTotal.innerText = stats.total ?? 0;

        // KPI 2: Por Verificar Pago (con cambio de alertas y colores)
        const kpiVerificar = document.getElementById('kpi_por_verificar');
        const kpiCardVerificar = document.getElementById('kpi_card_por_verificar');
        const kpiLabelVerificar = document.getElementById('kpi_label_por_verificar');
        const kpiIconVerificar = document.getElementById('kpi_icon_por_verificar');
        const kpiBadgeVerificar = document.getElementById('kpi_badge_por_verificar');

        if (kpiVerificar) kpiVerificar.innerText = stats.por_verificar ?? 0;

        if (kpiCardVerificar) {
            if (stats.por_verificar > 0) {
                kpiCardVerificar.className = "bg-white p-4 sm:p-5 rounded-2xl border border-amber-300 ring-2 ring-amber-400/20 bg-amber-50/20 shadow-2xs hover:shadow-md transition-all cursor-pointer group";
                if (kpiLabelVerificar) kpiLabelVerificar.className = "text-[11px] font-bold text-amber-800 uppercase tracking-wider";
                if (kpiIconVerificar) kpiIconVerificar.className = "size-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center group-hover:scale-110 transition-transform";
                if (kpiVerificar) kpiVerificar.className = "text-2xl font-black text-amber-700 leading-none";
                if (kpiBadgeVerificar) {
                    kpiBadgeVerificar.className = "text-[10px] text-amber-700 font-black uppercase bg-amber-100 px-1.5 py-0.5 rounded animate-pulse";
                    kpiBadgeVerificar.innerText = "¡Atención!";
                }
            } else {
                kpiCardVerificar.className = "bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all cursor-pointer group";
                if (kpiLabelVerificar) kpiLabelVerificar.className = "text-[11px] font-bold text-gray-400 uppercase tracking-wider";
                if (kpiIconVerificar) kpiIconVerificar.className = "size-8 rounded-xl bg-gray-100 text-gray-500 flex items-center justify-center group-hover:scale-110 transition-transform";
                if (kpiVerificar) kpiVerificar.className = "text-2xl font-black text-gray-900 leading-none";
                if (kpiBadgeVerificar) {
                    kpiBadgeVerificar.className = "text-[10px] text-emerald-600 font-bold uppercase";
                    kpiBadgeVerificar.innerText = "Al día";
                }
            }
        }

        // KPI 3: Por Despachar
        const kpiDespachar = document.getElementById('kpi_por_despachar');
        if (kpiDespachar) kpiDespachar.innerText = stats.por_despachar ?? 0;

        // KPI 4: En Ruta
        const kpiRuta = document.getElementById('kpi_en_ruta');
        if (kpiRuta) kpiRuta.innerText = stats.en_ruta ?? 0;

        // CONTADORES EN LOS TABS DE FILTRO
        const tabTodos = document.getElementById('tab_badge_todos');
        if (tabTodos) tabTodos.innerText = stats.total ?? 0;

        const tabVerificar = document.getElementById('tab_badge_por_verificar');
        if (tabVerificar) {
            tabVerificar.innerText = stats.por_verificar ?? 0;
            if (stats.por_verificar > 0) {
                tabVerificar.className = "px-1.5 py-0.2 rounded-full text-[10px] font-black bg-amber-500 text-slate-900 animate-pulse";
            } else {
                tabVerificar.className = "px-1.5 py-0.2 rounded-full text-[10px] bg-gray-100 text-gray-600";
            }
        }

        const tabDespachar = document.getElementById('tab_badge_por_despachar');
        if (tabDespachar) tabDespachar.innerText = stats.por_despachar ?? 0;

        const tabRuta = document.getElementById('tab_badge_en_ruta');
        if (tabRuta) tabRuta.innerText = stats.en_ruta ?? 0;

        const tabCompletados = document.getElementById('tab_badge_completados');
        if (tabCompletados) tabCompletados.innerText = stats.completados ?? 0;

        const tabCancelados = document.getElementById('tab_badge_cancelados');
        if (tabCancelados) tabCancelados.innerText = stats.cancelados ?? 0;
    }

    // AUTO-REFRESCO INTELIGENTE EN SEGUNDO PLANO (CADA 15 SEGUNDOS)
    setInterval(() => {
        const modalDetalle = document.getElementById('detalleModal');
        const modalComprobante = document.getElementById('comprobanteModal');
        const modalStatus = document.getElementById('statusModal');
        const swalAbierto = document.querySelector('.swal2-container') !== null;
        const escribiendo = document.activeElement === document.getElementById('searchInput');

        const algunModalAbierto = (modalDetalle && !modalDetalle.classList.contains('hidden')) ||
                                  (modalComprobante && !modalComprobante.classList.contains('hidden')) ||
                                  (modalStatus && !modalStatus.classList.contains('hidden'));

        if (!algunModalAbierto && !swalAbierto && !escribiendo) {
            fetchData(currentPage, false);
        }
    }, 15000);

    // ==========================================
    // VISOR DE COMPROBANTES DE PAGO
    // ==========================================
    function verComprobante(imgUrl, referencia, pagoId = null, estadoPago = null, pedidoId = null) {
        const imgElem = document.getElementById('visor_imagen');
        imgElem.src = imgUrl;
        imgElem.onerror = function() {
            this.onerror = null;
            this.src = 'data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22200%22%20height%3D%22200%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%2394a3b8%22%20stroke-width%3D%221.5%22%3E%3Crect%20x%3D%223%22%20y%3D%223%22%20width%3D%2218%22%20height%3D%2218%22%20rx%3D%222%22%2F%3E%3Ccircle%20cx%3D%228.5%22%20cy%3D%228.5%22%20r%3D%221.5%22%2F%3E%3Cpolyline%20points%3D%2221%2015%2016%2010%205%2021%22%2F%3E%3C%2Fsvg%3E';
        };

        document.getElementById('visor_referencia').innerText = referencia || 'SIN-REF';
        document.getElementById('visor_descargar').href = imgUrl;

        const contenedorBotones = document.getElementById('visor_botones_resolucion');
        contenedorBotones.innerHTML = '';

        // Si el pago está en revisión y el usuario tiene permisos, agregamos botones para aprobar/rechazar directamente
        if (pagoId && estadoPago === 'revision' && puedeGestionarPagos) {
            contenedorBotones.innerHTML = `
                <button type="button" onclick="aprobarPagoDirecto(${pagoId}, '${referencia}', ${pedidoId}); closeComprobante();" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs flex items-center gap-1 shadow-sm transition-all hover:scale-105 active:scale-95">
                    <span class="material-symbols-outlined text-[16px]">check_circle</span> Aprobar Pago
                </button>
                <button type="button" onclick="rechazarPagoDirecto(${pagoId}, '${referencia}', ${pedidoId}); closeComprobante();" class="px-3.5 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs flex items-center gap-1 shadow-sm transition-all hover:scale-105 active:scale-95">
                    <span class="material-symbols-outlined text-[16px]">cancel</span> Rechazar
                </button>
            `;
        }
        
        const modal = document.getElementById('comprobanteModal');
        const backdrop = document.getElementById('comprobanteBackdrop');
        const panel = document.getElementById('comprobantePanel');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    function closeComprobante() {
        const modal = document.getElementById('comprobanteModal');
        const backdrop = document.getElementById('comprobanteBackdrop');
        const panel = document.getElementById('comprobantePanel');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.getElementById('visor_imagen').src = '';
        }, 250);
    }

    // ==========================================
    // AUDITORÍA DE PAGO: APROBAR Y RECHAZAR
    // ==========================================
    function aprobarPagoDirecto(pagoId, referencia, pedidoId = null, esPresencial = false) {
        const esCobroPresencial = esPresencial === true || ['COBRO-CONTRA-ENTREGA', 'PAGO-EN-LOCAL', 'PAGO-EN-SITIO', 'EN-SITIO'].includes(String(referencia).toUpperCase());
        const titulo = esCobroPresencial ? '¿Confirmar Cobro y Entrega en Tienda?' : '¿Aprobar Pago Reportado?';
        const htmlMsg = esCobroPresencial
            ? `Confirmas que se ha recibido el pago en caja y entregado los productos al cliente.<br><br><span class="text-xs text-gray-500">Esto marcará el pedido como <b>ENTREGADO</b> en mostrador, descontará el inventario del almacén, emitirá la factura digital y notificará al cliente por correo.</span>`
            : `Confirmas que los fondos con referencia <b>${referencia}</b> se encuentran acreditados en cuenta bancaria.<br><br><span class="text-xs text-gray-500">Esto marcará el pedido como <b>PAGADO</b>, descontará inventario, emitirá la factura y notificará al cliente por correo.</span>`;
        const btnText = esCobroPresencial
            ? '<span class="material-symbols-outlined align-middle mr-1 text-[18px]">payments</span> Sí, Cobrar y Entregar'
            : '<span class="material-symbols-outlined align-middle mr-1 text-[18px]">verified</span> Sí, Aprobar y Liberar';

        Swal.fire({
            title: titulo,
            html: htmlMsg,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a', 
            cancelButtonColor: '#9ca3af',
            confirmButtonText: btnText,
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                enviarResolucionPago(`/admin/pagos/${pagoId}/aprobar`, pedidoId);
            }
        });
    }

    function rechazarPagoDirecto(pagoId, referencia, pedidoId = null) {
        Swal.fire({
            title: '¿Rechazar Pago?',
            text: `El comprobante con referencia ${referencia} será marcado como inválido. El pedido se mantendrá en espera de un nuevo pago válido.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', 
            cancelButtonColor: '#9ca3af',
            confirmButtonText: '<span class="material-symbols-outlined align-middle mr-1 text-[18px]">cancel</span> Rechazar Pago',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                enviarResolucionPago(`/admin/pagos/${pagoId}/rechazar`, pedidoId);
            }
        });
    }

    function enviarResolucionPago(url, pedidoId = null) {
        Swal.fire({
            title: 'Auditando Pago...',
            text: 'Actualizando base de datos, inventario y comprobante fiscal.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Error en el servidor al auditar');
            return data;
        })
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: '¡Operación Exitosa!',
                text: data.message,
                timer: 2200,
                showConfirmButton: false,
                customClass: { popup: 'rounded-2xl' }
            });

            // REFRESCAR TABLA Y CONTADORES AL INSTANTE SIN RECARGAR PÁGINA
            fetchData(currentPage, false);

            // Si el modal de detalle 360° está abierto, refrescarlo de inmediato
            const modalDetalle = document.getElementById('detalleModal');
            if (pedidoId && modalDetalle && !modalDetalle.classList.contains('hidden')) {
                verDetallePedido(pedidoId);
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message,
                customClass: { popup: 'rounded-2xl' }
            });
        });
    }

    // ==========================================
    // MODAL: ESTADO LOGÍSTICO OPERATIVO
    // ==========================================
    function openStatusModal(id, estadoActual, esDelivery = true) {
        document.getElementById('pedido_id').value = id;
        
        const titleEl = document.getElementById('statusModalTitle');
        const iconEl = document.getElementById('statusModalIcon');
        const select = document.getElementById('estado_select');

        if (esSoloReparto) {
            if (titleEl) titleEl.innerText = 'Entrega Logística (Reparto)';
            if (iconEl) iconEl.innerText = 'local_shipping';
            select.innerHTML = `
                <option value="en_ruta">En Ruta (Con Repartidor)</option>
                <option value="entregado">Entregado al Cliente ✅</option>
            `;
        } else if (esDelivery) {
            if (titleEl) titleEl.innerText = 'Despacho Logístico (Delivery)';
            if (iconEl) iconEl.innerText = 'local_shipping';
            let opts = `
                <option value="preparacion">En Preparación / Empaque</option>
                <option value="en_ruta">En Ruta (Con Repartidor)</option>
                <option value="entregado">Entregado al Cliente ✅</option>
            `;
            if (puedeCancelar) {
                opts += `<option value="cancelado" class="text-red-600 font-bold">Cancelar Pedido</option>`;
            }
            select.innerHTML = opts;
        } else {
            if (titleEl) titleEl.innerText = 'Entrega en Sede Física';
            if (iconEl) iconEl.innerText = 'storefront';
            let opts = `
                <option value="preparacion">En Preparación / Listo para Retiro</option>
                <option value="entregado">Retirado / Entregado en Tienda ✅</option>
            `;
            if (puedeCancelar) {
                opts += `<option value="cancelado" class="text-red-600 font-bold">Cancelar Pedido</option>`;
            }
            select.innerHTML = opts;
        }

        let optionExists = Array.from(select.options).some(opt => opt.value === estadoActual);
        select.value = optionExists ? estadoActual : 'preparacion';

        const modal = document.getElementById('statusModal');
        const backdrop = document.getElementById('statusBackdrop');
        const panel = document.getElementById('statusPanel');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    function closeStatusModal() {
        const modal = document.getElementById('statusModal');
        const backdrop = document.getElementById('statusBackdrop');
        const panel = document.getElementById('statusPanel');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 250);
    }

    function saveStatus(e) {
        e.preventDefault();
        const id = document.getElementById('pedido_id').value;
        const estado = document.getElementById('estado_select').value;
        const btn = document.getElementById('btnSaveStatus');
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[16px]">autorenew</span> Guardando...';
        btn.disabled = true;

        fetch(`/admin/pedidos/${id}/estado`, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ estado: estado })
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Error al cambiar estado logístico');
            return data;
        })
        .then(data => {
            closeStatusModal();
            Swal.fire({
                icon: 'success',
                title: 'Logística Actualizada',
                text: data.message,
                timer: 2000,
                showConfirmButton: false,
                customClass: { popup: 'rounded-2xl' }
            });
            fetchData(currentPage, false); 
        })
        .catch(err => { 
            Swal.fire({
                icon: 'error',
                title: 'No permitido',
                text: err.message,
                customClass: { popup: 'rounded-2xl' }
            });
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }

    function guardarEstadoDesdeModal(id) {
        const select = document.getElementById(`modal_estado_select_${id}`);
        if (!select) return;
        
        const nuevoEstado = select.value;
        fetch(`/admin/pedidos/${id}/estado`, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ estado: nuevoEstado })
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Error al actualizar');
            return data;
        })
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'Actualizado',
                text: data.message,
                timer: 1800,
                showConfirmButton: false,
                customClass: { popup: 'rounded-2xl' }
            });
            verDetallePedido(id);
            fetchData(currentPage, false);
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
    // MODAL: VER DETALLE 360° DEL PEDIDO
    // ==========================================
    function verDetallePedido(id) {
        const modal = document.getElementById('detalleModal');
        const backdrop = document.getElementById('detalleBackdrop');
        const panel = document.getElementById('detallePanel');
        const loader = document.getElementById('detalleLoader');
        const content = document.getElementById('detalleInyectado');
        
        document.getElementById('detalle_pedido_titulo').innerText = `Pedido #${String(id).padStart(6, '0')}`;
        
        content.innerHTML = '';
        loader.classList.remove('hidden');
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);

        fetch(`/admin/pedidos/${id}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            content.innerHTML = html;
            loader.classList.add('hidden');
        })
        .catch(err => {
            content.innerHTML = `<div class="p-8 text-center text-red-500 font-bold"><span class="material-symbols-outlined text-4xl mb-2">error</span><br>Error al cargar el detalle del pedido.</div>`;
            loader.classList.add('hidden');
        });
    }

    function closeDetalleModal() {
        const modal = document.getElementById('detalleModal');
        const backdrop = document.getElementById('detalleBackdrop');
        const panel = document.getElementById('detallePanel');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        document.body.classList.remove('overflow-hidden');
        setTimeout(() => modal.classList.add('hidden'), 250);
    }

    // Cerrar modales con tecla Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeComprobante();
            closeStatusModal();
            closeDetalleModal();
        }
    });

    // ==========================================
    // SELECCIÓN DE FORMATO DE IMPRESIÓN (TICKET 80mm vs FACTURA A4)
    // ==========================================
    function abrirOpcionesImpresion(pedidoId, numeroFactura = '') {
        const subtitulo = numeroFactura 
            ? `Factura N° <b>${numeroFactura}</b> &bull; Pedido #<b>${String(pedidoId).padStart(6, '0')}</b>`
            : `Pedido #<b>${String(pedidoId).padStart(6, '0')}</b>`;

        Swal.fire({
            title: '<div class="flex items-center justify-center gap-2 text-slate-800 text-lg font-black"><span class="material-symbols-outlined text-[#1e549e] text-2xl">print</span> Seleccionar Formato de Impresión</div>',
            html: `
                <p class="text-xs text-gray-500 mb-5 font-medium">${subtitulo}</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-left">
                    <!-- Opción 1: Ticket 80mm -->
                    <button type="button" 
                            onclick="abrirDocumentoImpresion(${pedidoId}, 'ticket')" 
                            class="p-4 rounded-2xl border-2 border-gray-100 hover:border-[#1e549e] bg-slate-50/70 hover:bg-blue-50/40 transition-all text-left flex flex-col justify-between group cursor-pointer shadow-2xs hover:shadow-md">
                        <div>
                            <div class="flex items-center justify-between mb-2.5">
                                <div class="size-9 rounded-xl bg-blue-100 text-[#1e549e] flex items-center justify-center group-hover:scale-105 transition-transform">
                                    <span class="material-symbols-outlined text-[20px]">receipt_long</span>
                                </div>
                                <span class="text-[9.5px] font-black uppercase px-2 py-0.5 rounded-full bg-blue-100 text-[#1e549e]">80mm</span>
                            </div>
                            <h4 class="font-black text-gray-900 text-sm group-hover:text-[#1e549e] transition-colors">Ticket Térmico</h4>
                            <p class="text-[11px] text-gray-500 mt-1 leading-snug">Formato para impresoras de punto de venta (POS) y térmicas de 80mm.</p>
                        </div>
                        <div class="mt-3.5 pt-2 border-t border-gray-200/60 flex items-center gap-1 text-[11px] font-bold text-[#1e549e]">
                            <span>Abrir Ticket</span>
                            <span class="material-symbols-outlined text-[14px] group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                        </div>
                    </button>

                    <!-- Opción 2: Factura A4 / PDF -->
                    <button type="button" 
                            onclick="abrirDocumentoImpresion(${pedidoId}, 'factura')" 
                            class="p-4 rounded-2xl border-2 border-gray-100 hover:border-emerald-600 bg-slate-50/70 hover:bg-emerald-50/40 transition-all text-left flex flex-col justify-between group cursor-pointer shadow-2xs hover:shadow-md">
                        <div>
                            <div class="flex items-center justify-between mb-2.5">
                                <div class="size-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center group-hover:scale-105 transition-transform">
                                    <span class="material-symbols-outlined text-[20px]">description</span>
                                </div>
                                <span class="text-[9.5px] font-black uppercase px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">A4 / PDF</span>
                            </div>
                            <h4 class="font-black text-gray-900 text-sm group-hover:text-emerald-700 transition-colors">Factura Fiscal</h4>
                            <p class="text-[11px] text-gray-500 mt-1 leading-snug">Documento formal completo en hoja estándar carta/A4, listo para guardar como PDF.</p>
                        </div>
                        <div class="mt-3.5 pt-2 border-t border-gray-200/60 flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                            <span>Abrir Factura A4</span>
                            <span class="material-symbols-outlined text-[14px] group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                        </div>
                    </button>
                </div>
            `,
            showConfirmButton: false,
            showCloseButton: true,
            customClass: {
                popup: 'rounded-3xl max-w-lg p-6 sm:p-7',
            }
        });
    }

    function abrirDocumentoImpresion(pedidoId, tipo) {
        Swal.close();
        if (tipo === 'ticket') {
            window.open(`/admin/pedidos/${pedidoId}/ticket`, '_blank');
        } else {
            window.open(`/admin/pedidos/${pedidoId}/factura`, '_blank');
        }
    }

    window.abrirOpcionesImpresion = abrirOpcionesImpresion;
    window.abrirDocumentoImpresion = abrirDocumentoImpresion;
</script>
@endpush
@endsection