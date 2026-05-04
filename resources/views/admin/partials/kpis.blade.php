<style>
    /* ==========================================================================
       TRANSICIONES GLOBALES
       Permite que el cambio entre los distintos tamaños se vea fluido y elegante
       ========================================================================== */
    .kpi-card, .kpi-value, .kpi-icon-box, .kpi-icon-box span, .kpi-footer, .kpi-bg-circle, .kpi-title, .kpi-value-symbol, .kpi-header {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ==========================================================================
       TAMAÑO COMPACTO (-20%)
       ========================================================================== */
    #kpi-grid[data-size="compacto"] { gap: 1rem; }
    #kpi-grid[data-size="compacto"] .kpi-card { padding: 1.25rem; border-radius: 1.5rem; }
    #kpi-grid[data-size="compacto"] .kpi-header { margin-bottom: 0.75rem; }
    #kpi-grid[data-size="compacto"] .kpi-value { font-size: 1.8rem; }
    #kpi-grid[data-size="compacto"] .kpi-icon-box { width: 2.2rem; height: 2.2rem; border-radius: 0.6rem; }
    #kpi-grid[data-size="compacto"] .kpi-icon-box span { font-size: 1.2rem; }
    #kpi-grid[data-size="compacto"] .kpi-bg-circle { width: 5rem; height: 5rem; right: -1rem; top: -1rem; }

    /* ==========================================================================
       TAMAÑO PEQUEÑO (-40%)
       ========================================================================== */
    #kpi-grid[data-size="pequeno"] { gap: 0.75rem; }
    #kpi-grid[data-size="pequeno"] .kpi-card { padding: 1rem; border-radius: 1.25rem; }
    #kpi-grid[data-size="pequeno"] .kpi-header { margin-bottom: 0.5rem; }
    #kpi-grid[data-size="pequeno"] .kpi-title { font-size: 10px; }
    #kpi-grid[data-size="pequeno"] .kpi-value { font-size: 1.4rem; }
    #kpi-grid[data-size="pequeno"] .kpi-value-symbol { font-size: 1rem; }
    #kpi-grid[data-size="pequeno"] .kpi-icon-box { width: 1.8rem; height: 1.8rem; border-radius: 0.5rem; }
    #kpi-grid[data-size="pequeno"] .kpi-icon-box span { font-size: 1rem; }
    #kpi-grid[data-size="pequeno"] .kpi-bg-circle { width: 4.25rem; height: 4.25rem; right: -0.75rem; top: -0.75rem; }
    #kpi-grid[data-size="pequeno"] .kpi-footer { font-size: 10px; margin-top: 0.5rem; padding: 0.2rem 0.4rem; }
    #kpi-grid[data-size="pequeno"] .kpi-icon-sm { font-size: 12px; }

    /* ==========================================================================
       TAMAÑO MINI (-60%)
       Oculta el footer, ajusta a 8 columnas en desktop y evita el desbordamiento
       ========================================================================== */
    #kpi-grid[data-size="mini"] { gap: 0.5rem; }
    
    /* Media Queries exclusivas para sobreescribir el grid de Tailwind en tamaño mini */
    @media (min-width: 1024px) { /* Escritorio (lg y xl): 8 columnas */
        #kpi-grid[data-size="mini"] { grid-template-columns: repeat(8, minmax(0, 1fr)); }
    }
    @media (min-width: 640px) and (max-width: 1023px) { /* Tablets (sm y md): 4 columnas */
        #kpi-grid[data-size="mini"] { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    @media (max-width: 639px) { /* Teléfonos (xs): 2 columnas */
        #kpi-grid[data-size="mini"] { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    #kpi-grid[data-size="mini"] .kpi-card { 
        padding: 0.75rem 0.6rem; 
        border-radius: 1rem; 
        display: flex; 
        flex-direction: column; 
        justify-content: center;
    }
    
    #kpi-grid[data-size="mini"] .relative.z-10 { 
        width: 100%; 
        min-width: 0; 
    }
    
    #kpi-grid[data-size="mini"] .kpi-header { 
        margin-bottom: 0.25rem; 
        gap: 0.25rem; 
    } 
    
    /* Prevención de desbordamiento de texto */
    #kpi-grid[data-size="mini"] .kpi-title { 
        font-size: 8px; 
        white-space: nowrap; 
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    #kpi-grid[data-size="mini"] .kpi-value { 
        font-size: 1.05rem; 
        line-height: 1; 
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    #kpi-grid[data-size="mini"] .kpi-value-symbol { font-size: 0.75rem; }
    
    #kpi-grid[data-size="mini"] .kpi-icon-box { 
        width: 1.5rem; height: 1.5rem; 
        border-radius: 0.35rem; 
        flex-shrink: 0; /* Evita que el ícono se aplaste */
    }
    
    #kpi-grid[data-size="mini"] .kpi-icon-box span { font-size: 0.85rem; }
    #kpi-grid[data-size="mini"] .kpi-bg-circle { width: 2.8rem; height: 2.8rem; right: -0.4rem; top: -0.4rem; }
    #kpi-grid[data-size="mini"] .kpi-footer { display: none; }
</style>
{{-- CONTROLES DEL DASHBOARD --}}
<div class="flex justify-between items-end mb-4 animate-fade-in-up">
    <h2 class="text-xl font-black text-agro-dark">Indicadores de Rendimiento</h2>
    
    {{-- Selector de Tamaño de KPIs --}}
    <div class="flex flex-col items-end gap-1">
        <label for="kpiSize" class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tamaño de Tarjetas</label>
        <select id="kpiSize" onchange="window.DashboardConfig.changeKpiSize(this.value)" class="bg-white border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-primary focus:border-primary px-3 py-1.5 shadow-sm font-medium cursor-pointer transition-all hover:bg-gray-50 outline-none">
            <option value="original">Original (100%)</option>
            <option value="compacto">Compacto (-20%)</option>
            <option value="pequeno">Pequeño (-40%)</option>
            <option value="mini">Mini (-60%)</option>
        </select>
    </div>
</div>

{{-- GRID DE KPIs (Nota el id "kpi-grid" y data-size) --}}
<div id="kpi-grid" data-size="original" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6 mb-8 lg:mb-10 transition-all duration-300">
    
    {{-- 1. KPI: Ventas del Día --}}
    @if(Auth::user()->tienePermiso('ver_kpis_financieros'))
    <div class="kpi-card bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="kpi-bg-circle absolute -right-4 -top-4 w-24 h-24 bg-green-50 rounded-full group-hover:scale-[1.8] transition-transform duration-500 ease-out z-0"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4 kpi-header">
                <span class="kpi-title text-[11px] font-black text-gray-700 uppercase tracking-wider">Ventas de Hoy</span>
                <div class="kpi-icon-box w-10 h-10 bg-green-100 text-green-600 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined">payments</span></div>
            </div>
            <h3 class="kpi-value text-3xl font-black text-agro-dark">${{ number_format($ventasHoyUsd ?? 0, 2) }}</h3>
            <p class="kpi-footer text-[11px] font-bold text-green-600 flex items-center gap-1 mt-2 bg-green-50 w-fit px-2 py-1 rounded-md border border-green-100">
                <span class="material-symbols-outlined text-[14px] kpi-icon-sm">account_balance_wallet</span> 
                Bs. {{ number_format($ventasHoyVes ?? 0, 2) }}
            </p>
        </div>
    </div>
    @endif

    {{-- 2. KPI: Pedidos Pendientes --}}
    @if(Auth::user()->tienePermiso('ver_kpis_operativos') || Auth::user()->tienePermiso('ver_pedidos'))
    <div class="kpi-card bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="kpi-bg-circle absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full group-hover:scale-[1.8] transition-transform duration-500 ease-out z-0"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4 kpi-header">
                <span class="kpi-title text-[11px] font-black text-gray-700 uppercase tracking-wider">Pedidos Pendientes</span>
                <div class="kpi-icon-box w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined">shopping_bag</span></div>
            </div>
            <h3 class="kpi-value text-3xl font-black text-agro-dark">{{ $pedidosPendientes ?? 0 }}</h3>
            <p class="kpi-footer text-xs font-bold text-blue-500 flex items-center gap-1 mt-2">
                <span class="material-symbols-outlined text-[14px] kpi-icon-sm">info</span> {{ $pedidosNuevosHoy ?? 0 }} nuevos el día de hoy
            </p>
        </div>
    </div>
    @endif

    {{-- 3. KPI: Pagos en Revisión --}}
    @if(Auth::user()->tienePermiso('ver_kpis_financieros') || Auth::user()->tienePermiso('gestionar_pagos'))
    <div class="kpi-card bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="kpi-bg-circle absolute -right-4 -top-4 w-24 h-24 bg-amber-50 rounded-full group-hover:scale-[1.8] transition-transform duration-500 ease-out z-0"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4 kpi-header">
                <span class="kpi-title text-[11px] font-black text-gray-700 uppercase tracking-wider">Pagos en Revisión</span>
                <div class="kpi-icon-box w-10 h-10 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined">fact_check</span></div>
            </div>
            <h3 class="kpi-value text-3xl font-black text-agro-dark">{{ $pagosPendientes ?? 0 }}</h3>
            <p class="kpi-footer text-xs font-bold text-amber-600 flex items-center gap-1 mt-2">
                <span class="material-symbols-outlined text-[14px] kpi-icon-sm">hourglass_empty</span> Esperando validación
            </p>
        </div>
    </div>
    @endif

    {{-- 4. KPI: Tasa de Cambio Actual --}}
    @if(Auth::user()->tienePermiso('ver_kpis_financieros') || Auth::user()->tienePermiso('gestionar_tasas_cambio'))
    <div class="kpi-card bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="kpi-bg-circle absolute -right-4 -top-4 w-24 h-24 bg-teal-50 rounded-full group-hover:scale-[1.8] transition-transform duration-500 ease-out z-0"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4 kpi-header">
                <span class="kpi-title text-[11px] font-black text-gray-700 uppercase tracking-wider">Tasa de Cambio</span>
                <div class="kpi-icon-box w-10 h-10 bg-teal-100 text-teal-600 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined">currency_exchange</span></div>
            </div>
            <h3 class="kpi-value text-3xl font-black text-agro-dark"><span class="text-xl text-gray-700 font-bold kpi-value-symbol">Bs</span> {{ number_format($tasaCambioActual ?? 0, 2) }}</h3>
            <p class="kpi-footer text-xs font-bold text-teal-600 flex items-center gap-1 mt-2">
                <span class="material-symbols-outlined text-[14px] kpi-icon-sm">update</span> {{ $fuenteTasa ?? 'Sin Tasa' }}
            </p>
        </div>
    </div>
    @endif

    {{-- 5. KPI: Alertas de Stock Crítico --}}
    @if(Auth::user()->tienePermiso('ver_kpis_operativos') || Auth::user()->tienePermiso('ver_productos'))
    <div class="kpi-card bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="kpi-bg-circle absolute -right-4 -top-4 w-24 h-24 bg-orange-50 rounded-full group-hover:scale-[1.8] transition-transform duration-500 ease-out z-0"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4 kpi-header">
                <span class="kpi-title text-[11px] font-black text-gray-700 uppercase tracking-wider">Stock Crítico</span>
                <div class="kpi-icon-box w-10 h-10 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined">inventory_2</span></div>
            </div>
            <h3 class="kpi-value text-3xl font-black text-agro-dark">{{ $alertasStock ?? 0 }}</h3>
            <p class="kpi-footer text-xs font-bold text-orange-500 flex items-center gap-1 mt-2">
                <span class="material-symbols-outlined text-[14px] kpi-icon-sm">warning</span> Productos por agotarse
            </p>
        </div>
    </div>
    @endif

    {{-- 6. KPI: Lotes por Vencer --}}
    @if(Auth::user()->tienePermiso('ver_kpis_operativos') || Auth::user()->tienePermiso('gestionar_inventario_lotes'))
    <div class="kpi-card bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="kpi-bg-circle absolute -right-4 -top-4 w-24 h-24 bg-rose-50 rounded-full group-hover:scale-[1.8] transition-transform duration-500 ease-out z-0"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4 kpi-header">
                <span class="kpi-title text-[11px] font-black text-gray-700 uppercase tracking-wider">Lotes por Vencer</span>
                <div class="kpi-icon-box w-10 h-10 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined">event_busy</span></div>
            </div>
            <h3 class="kpi-value text-3xl font-black text-agro-dark">{{ $lotesPorVencer ?? 0 }}</h3>
            <p class="kpi-footer text-xs font-bold text-rose-500 flex items-center gap-1 mt-2">
                <span class="material-symbols-outlined text-[14px] kpi-icon-sm">error</span> Vencen en &le; 30 días
            </p>
        </div>
    </div>
    @endif

    {{-- 7. KPI: Recetas Pendientes --}}
    @if(Auth::user()->tienePermiso('ver_kpis_operativos') || Auth::user()->tienePermiso('ver_recetas'))
    <div class="kpi-card bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="kpi-bg-circle absolute -right-4 -top-4 w-24 h-24 bg-purple-50 rounded-full group-hover:scale-[1.8] transition-transform duration-500 ease-out z-0"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4 kpi-header">
                <span class="kpi-title text-[11px] font-black text-gray-700 uppercase tracking-wider">Récipes por Aprobar</span>
                <div class="kpi-icon-box w-10 h-10 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined">stethoscope</span></div>
            </div>
            <h3 class="kpi-value text-3xl font-black text-agro-dark">{{ $recetasPendientes ?? 0 }}</h3>
            <p class="kpi-footer text-xs font-bold text-purple-500 flex items-center gap-1 mt-2">
                <span class="material-symbols-outlined text-[14px] kpi-icon-sm">verified_user</span> Requieren validación
            </p>
        </div>
    </div>
    @endif

    {{-- 8. KPI: Usuarios y Clientes --}}
    @if(Auth::user()->tienePermiso('ver_usuarios'))
    <div class="kpi-card bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="kpi-bg-circle absolute -right-4 -top-4 w-24 h-24 bg-indigo-50 rounded-full group-hover:scale-[1.8] transition-transform duration-500 ease-out z-0"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4 kpi-header">
                <span class="kpi-title text-[11px] font-black text-gray-700 uppercase tracking-wider">Nuevos Clientes</span>
                <div class="kpi-icon-box w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined">group_add</span></div>
            </div>
            <h3 class="kpi-value text-3xl font-black text-agro-dark">{{ $nuevosUsuariosMes ?? 0 }}</h3>
            <p class="kpi-footer text-xs font-bold text-indigo-500 flex items-center gap-1 mt-2">
                <span class="material-symbols-outlined text-[14px] kpi-icon-sm">calendar_month</span> Registros este mes
            </p>
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script>
    window.DashboardConfig = {
        changeKpiSize: function(size) {
            const grid = document.getElementById('kpi-grid');
            if(grid) {
                // Actualiza el atributo en el DOM
                grid.setAttribute('data-size', size);
                // Guarda la preferencia en el navegador del usuario
                localStorage.setItem('preferencia_tamano_kpi', size);
            }
        },
        init: function() {
            // Busca si el usuario ya había elegido un tamaño antes, por defecto "original"
            const savedSize = localStorage.getItem('preferencia_tamano_kpi') || 'original';
            const selector = document.getElementById('kpiSize');
            
            if(selector) {
                selector.value = savedSize;
            }
            // Aplica el tamaño al cargar la página
            this.changeKpiSize(savedSize);
        }
    };

    // Inicializar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', () => {
        window.DashboardConfig.init();
    });
</script>
@endpush