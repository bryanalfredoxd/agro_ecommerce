<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6 mb-8 lg:mb-10">
    
    {{-- 1. KPI: Ventas del Día (Muestra USD y VES según requerimiento) --}}
    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-green-50 rounded-full group-hover:scale-[1.8] transition-transform duration-500 ease-out z-0"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Ventas de Hoy</span>
                <div class="w-10 h-10 bg-green-100 text-green-600 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined">payments</span></div>
            </div>
            <h3 class="text-3xl font-black text-agro-dark">${{ number_format($ventasHoyUsd ?? 1245.50, 2) }}</h3>
            <p class="text-[11px] font-bold text-green-600 flex items-center gap-1 mt-2 bg-green-50 w-fit px-2 py-1 rounded-md border border-green-100">
                <span class="material-symbols-outlined text-[14px]">account_balance_wallet</span> 
                Bs. {{ number_format($ventasHoyVes ?? 45460.75, 2) }}
            </p>
        </div>
    </div>

    {{-- 2. KPI: Pedidos Pendientes --}}
    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full group-hover:scale-[1.8] transition-transform duration-500 ease-out z-0"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Pedidos Activos</span>
                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined">shopping_bag</span></div>
            </div>
            <h3 class="text-3xl font-black text-agro-dark">{{ $pedidosPendientes ?? 18 }}</h3>
            <p class="text-xs font-bold text-blue-500 flex items-center gap-1 mt-2">
                <span class="material-symbols-outlined text-[14px]">info</span> {{ $pedidosNuevosHoy ?? 5 }} nuevos el día de hoy
            </p>
        </div>
    </div>

    {{-- 3. KPI: Pagos en Revisión (Nuevo) --}}
    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 rounded-full group-hover:scale-[1.8] transition-transform duration-500 ease-out z-0"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Pagos en Revisión</span>
                <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined">fact_check</span></div>
            </div>
            <h3 class="text-3xl font-black text-agro-dark">{{ $pagosPendientes ?? 7 }}</h3>
            <p class="text-xs font-bold text-amber-600 flex items-center gap-1 mt-2">
                <span class="material-symbols-outlined text-[14px]">hourglass_empty</span> Esperando validación
            </p>
        </div>
    </div>

    {{-- 4. KPI: Tasa de Cambio Actual (Nuevo) --}}
    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-teal-50 rounded-full group-hover:scale-[1.8] transition-transform duration-500 ease-out z-0"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Tasa de Cambio</span>
                <div class="w-10 h-10 bg-teal-100 text-teal-600 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined">currency_exchange</span></div>
            </div>
            <h3 class="text-3xl font-black text-agro-dark"><span class="text-xl text-gray-400 font-bold">Bs</span> {{ number_format($tasaCambioActual ?? 36.50, 2) }}</h3>
            <p class="text-xs font-bold text-teal-600 flex items-center gap-1 mt-2">
                <span class="material-symbols-outlined text-[14px]">update</span> {{ $fuenteTasa ?? 'Oficial BCV' }}
            </p>
        </div>
    </div>

    {{-- 5. KPI: Alertas de Stock Crítico --}}
    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-orange-50 rounded-full group-hover:scale-[1.8] transition-transform duration-500 ease-out z-0"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Stock Crítico</span>
                <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined">inventory_2</span></div>
            </div>
            <h3 class="text-3xl font-black text-agro-dark">{{ $alertasStock ?? 12 }}</h3>
            <p class="text-xs font-bold text-orange-500 flex items-center gap-1 mt-2">
                <span class="material-symbols-outlined text-[14px]">warning</span> Productos por agotarse
            </p>
        </div>
    </div>

    {{-- 6. KPI: Lotes por Vencer (Nuevo e Importante para Agro) --}}
    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-50 rounded-full group-hover:scale-[1.8] transition-transform duration-500 ease-out z-0"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Lotes por Vencer</span>
                <div class="w-10 h-10 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined">event_busy</span></div>
            </div>
            <h3 class="text-3xl font-black text-agro-dark">{{ $lotesPorVencer ?? 4 }}</h3>
            <p class="text-xs font-bold text-rose-500 flex items-center gap-1 mt-2">
                <span class="material-symbols-outlined text-[14px]">error</span> Vencen en &le; 30 días
            </p>
        </div>
    </div>

    {{-- 7. KPI: Recetas Pendientes --}}
    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-50 rounded-full group-hover:scale-[1.8] transition-transform duration-500 ease-out z-0"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Récipes por Aprobar</span>
                <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined">stethoscope</span></div>
            </div>
            <h3 class="text-3xl font-black text-agro-dark">{{ $recetasPendientes ?? 3 }}</h3>
            <p class="text-xs font-bold text-purple-500 flex items-center gap-1 mt-2">
                <span class="material-symbols-outlined text-[14px]">verified_user</span> Requieren validación
            </p>
        </div>
    </div>

    {{-- 8. KPI: Usuarios y Clientes (Nuevo) --}}
    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-50 rounded-full group-hover:scale-[1.8] transition-transform duration-500 ease-out z-0"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Nuevos Clientes</span>
                <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined">group_add</span></div>
            </div>
            <h3 class="text-3xl font-black text-agro-dark">{{ $nuevosUsuariosMes ?? 24 }}</h3>
            <p class="text-xs font-bold text-indigo-500 flex items-center gap-1 mt-2">
                <span class="material-symbols-outlined text-[14px]">calendar_month</span> Registros este mes
            </p>
        </div>
    </div>

</div>