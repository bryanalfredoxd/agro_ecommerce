@extends('layouts.admin')

@section('title', 'Estadísticas y Analítica - Agrosuministros Rodríguez')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    {{-- SIDEBAR LATERAL --}}
    @include('admin.partials.sidebar')

    {{-- CONTENIDO PRINCIPAL --}}
    <main class="flex-1 min-w-0 flex flex-col">
        
        {{-- TOPBAR ADMIN --}}
        @include('admin.partials.topbar')

        {{-- Área de Contenido --}}
        <div class="p-4 sm:p-6 lg:p-8 animate-fade-in-up pb-24 space-y-6 max-w-7xl mx-auto w-full">

            {{-- CABECERA Y FILTROS INTERACTIVOS --}}
            <div class="bg-white rounded-3xl p-5 sm:p-7 border border-gray-100 shadow-sm relative overflow-hidden">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#0b1d38] to-[#1e549e] text-white flex items-center justify-center shadow-md shadow-[#1e549e]/20">
                                <span class="material-symbols-outlined text-[26px]">query_stats</span>
                            </div>
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-black text-agro-dark tracking-tight">Centro de Analítica y Estadísticas</h1>
                                <p class="text-xs sm:text-sm text-gray-500 font-medium">Monitoreo comercial en vivo, productos más vendidos, métodos de pago y logística</p>
                            </div>
                        </div>
                    </div>

                    {{-- Formulario y Selectores de Filtro --}}
                    <form method="GET" action="{{ route('admin.estadisticas.index') }}" id="formFiltros" class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                        {{-- Selector de Período --}}
                        <div class="relative w-full sm:w-auto sm:min-w-[170px]">
                            <select name="periodo" id="filtroPeriodo" onchange="cambiarPeriodoFiltro(this.value)" class="w-full bg-gray-50 hover:bg-gray-100/80 text-agro-dark text-xs font-bold rounded-2xl border border-gray-200/80 px-3.5 py-2.5 outline-none focus:ring-2 focus:ring-primary/20 transition-all cursor-pointer">
                                <option value="hoy" {{ $periodo === 'hoy' ? 'selected' : '' }}>📅 Hoy</option>
                                <option value="semana" {{ $periodo === 'semana' ? 'selected' : '' }}>📊 Esta Semana</option>
                                <option value="mes" {{ $periodo === 'mes' ? 'selected' : '' }}>📈 Este Mes</option>
                                <option value="mes_anterior" {{ $periodo === 'mes_anterior' ? 'selected' : '' }}>⏪ Mes Anterior</option>
                                <option value="ultimos_30" {{ $periodo === 'ultimos_30' ? 'selected' : '' }}>🗓️ Últimos 30 Días</option>
                                <option value="anio" {{ $periodo === 'anio' ? 'selected' : '' }}>🏆 Año Actual</option>
                                <option value="historico" {{ $periodo === 'historico' ? 'selected' : '' }}>🌐 Histórico Completo</option>
                                <option value="personalizado" {{ $periodo === 'personalizado' ? 'selected' : '' }}>⚙️ Rango Personalizado</option>
                            </select>
                        </div>

                        {{-- Selector de Canal --}}
                        <div class="relative w-full sm:w-auto sm:min-w-[160px]">
                            <select name="canal" id="filtroCanal" onchange="document.getElementById('formFiltros').submit()" class="w-full bg-gray-50 hover:bg-gray-100/80 text-agro-dark text-xs font-bold rounded-2xl border border-gray-200/80 px-3.5 py-2.5 outline-none focus:ring-2 focus:ring-primary/20 transition-all cursor-pointer">
                                <option value="todos" {{ $canal === 'todos' ? 'selected' : '' }}>🏪 Todos los Canales</option>
                                <option value="web" {{ $canal === 'web' ? 'selected' : '' }}>🌐 Solo Tienda Web</option>
                                <option value="tienda_fisica" {{ $canal === 'tienda_fisica' ? 'selected' : '' }}>🛒 Solo POS (Mostrador)</option>
                            </select>
                        </div>

                        {{-- Campos de Fechas Personalizadas (Condicional) --}}
                        <div id="cajaFechasPersonalizadas" class="{{ $periodo === 'personalizado' ? 'flex' : 'hidden' }} items-center gap-1.5 flex-wrap">
                            <input type="date" name="fecha_desde" value="{{ $fecha_desde }}" class="bg-gray-50 text-xs font-bold text-gray-700 rounded-xl border border-gray-200 px-2.5 py-2 outline-none">
                            <span class="text-xs text-gray-400 font-bold">-</span>
                            <input type="date" name="fecha_hasta" value="{{ $fecha_hasta }}" class="bg-gray-50 text-xs font-bold text-gray-700 rounded-xl border border-gray-200 px-2.5 py-2 outline-none">
                            <button type="submit" class="px-3 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:bg-secondary transition-colors">
                                Filtrar
                            </button>
                        </div>

                        {{-- Botón de recarga / reset --}}
                        <a href="{{ route('admin.estadisticas.index') }}" class="p-2.5 rounded-2xl bg-gray-100 hover:bg-gray-200/80 text-gray-600 transition-colors flex items-center justify-center shadow-2xs" title="Restablecer filtros">
                            <span class="material-symbols-outlined text-[20px]">refresh</span>
                        </a>
                    </form>
                </div>

                {{-- Badges de Resumen del Filtro Activo --}}
                <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap items-center justify-between text-xs gap-2.5">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 font-bold text-teal-800 bg-teal-50/90 px-3 py-1 rounded-xl border border-teal-200/60 shadow-2xs">
                            <span class="material-symbols-outlined text-[16px] text-teal-600">event_available</span>
                            <span>{{ $etiqueta_periodo }}</span>
                        </span>

                        <span class="inline-flex items-center gap-1.5 font-bold text-blue-800 bg-blue-50/90 px-3 py-1 rounded-xl border border-blue-200/60 shadow-2xs">
                            <span class="material-symbols-outlined text-[16px] text-blue-600">storefront</span>
                            <span>{{ $canal === 'todos' ? 'Todos los canales' : ($canal === 'web' ? 'Canal Web' : 'Canal POS Mostrador') }}</span>
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="font-mono text-gray-700 bg-gray-100 px-3 py-1 rounded-xl border border-gray-200/70 text-xs shadow-2xs">
                            Tasa BCV del Día: <strong class="text-agro-dark font-black">Bs. {{ number_format($tasa_bcv, 2) }}</strong>
                        </span>
                    </div>
                </div>
            </div>

            {{-- 1. TARJETAS DE KPIS PRINCIPALES (5 COLUMNAS) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 sm:gap-5">
                
                {{-- KPI 1: Total Facturación --}}
                <div class="bg-white rounded-3xl p-5 sm:p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all flex flex-col justify-between">
                    <div class="absolute -right-3 -top-3 w-20 h-20 bg-emerald-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-wider">Facturación Total</span>
                            <div class="w-9 h-9 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-[20px]">payments</span>
                            </div>
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-black text-agro-dark tracking-tight break-words">
                            ${{ number_format($kpis['total_ingresos_usd'], 2) }}
                        </h3>
                    </div>
                    <div class="relative z-10 mt-3 pt-2.5 border-t border-gray-100 flex flex-col gap-1">
                        <p class="text-xs font-bold text-emerald-800 bg-emerald-50/80 px-2.5 py-0.5 rounded-lg border border-emerald-100 inline-block w-fit">
                            Bs. {{ number_format($kpis['total_ingresos_ves'], 2) }}
                        </p>
                        @if(($kpis['total_devoluciones_usd'] ?? 0) > 0)
                        <p class="text-[10px] font-bold text-gray-500">
                            Neto Real: <strong class="text-emerald-700">${{ number_format($kpis['ingresos_netos_usd'] ?? $kpis['total_ingresos_usd'], 2) }}</strong>
                        </p>
                        @endif
                    </div>
                </div>

                {{-- KPI 2: Total Pedidos y Unidades --}}
                <div class="bg-white rounded-3xl p-5 sm:p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all flex flex-col justify-between">
                    <div class="absolute -right-3 -top-3 w-20 h-20 bg-blue-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-wider">Órdenes y Tickets</span>
                            <div class="w-9 h-9 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-[20px]">receipt_long</span>
                            </div>
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-black text-agro-dark tracking-tight">
                            {{ number_format($kpis['total_pedidos'], 0) }}
                        </h3>
                    </div>
                    <div class="relative z-10 mt-3 pt-2.5 border-t border-gray-100">
                        <p class="text-xs font-bold text-blue-700 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[15px]">inventory_2</span>
                            <span>{{ number_format($kpis['total_unidades'], 0) }} unidades vendidas</span>
                        </p>
                    </div>
                </div>

                {{-- KPI 3: Ticket Promedio --}}
                <div class="bg-white rounded-3xl p-5 sm:p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all flex flex-col justify-between">
                    <div class="absolute -right-3 -top-3 w-20 h-20 bg-purple-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-wider">Ticket Promedio</span>
                            <div class="w-9 h-9 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-[20px]">trending_up</span>
                            </div>
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-black text-agro-dark tracking-tight">
                            ${{ number_format($kpis['ticket_promedio_usd'], 2) }}
                        </h3>
                    </div>
                    <div class="relative z-10 mt-3 pt-2.5 border-t border-gray-100">
                        <p class="text-xs font-bold text-purple-700 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">price_change</span>
                            <span>Bs. {{ number_format($kpis['ticket_promedio_ves'], 2) }} / compra</span>
                        </p>
                    </div>
                </div>

                {{-- KPI 4: Producto Estrella --}}
                <div class="bg-white rounded-3xl p-5 sm:p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all flex flex-col justify-between">
                    <div class="absolute -right-3 -top-3 w-20 h-20 bg-amber-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-wider">Top Producto #1</span>
                            <div class="w-9 h-9 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center shadow-xs">
                                <span class="material-symbols-outlined text-[20px]">military_tech</span>
                            </div>
                        </div>
                        @if($producto_estrella ?? $productoEstrella ?? null)
                            @php $estrella = $producto_estrella ?? $productoEstrella; @endphp
                            <h3 class="text-xs sm:text-sm font-black text-agro-dark line-clamp-2 leading-snug" title="{{ $estrella['nombre'] }}">
                                {{ $estrella['nombre'] }}
                            </h3>
                        @else
                            <h3 class="text-sm font-bold text-gray-400">Sin datos de venta</h3>
                        @endif
                    </div>
                    <div class="relative z-10 mt-3 pt-2.5 border-t border-gray-100">
                        @if($producto_estrella ?? $productoEstrella ?? null)
                            <div class="flex items-center justify-between gap-1 text-xs">
                                <span class="font-black text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200/60">
                                    {{ $estrella['unidades_vendidas'] }} {{ $estrella['unidad'] }}
                                </span>
                                <span class="font-black text-agro-dark">
                                    ${{ number_format($estrella['total_usd'], 2) }}
                                </span>
                            </div>
                        @else
                            <span class="text-xs text-gray-400 font-medium">Registra ventas para calcular</span>
                        @endif
                    </div>
                </div>

                {{-- KPI 5: Devoluciones y Reembolsos --}}
                <div class="bg-white rounded-3xl p-5 sm:p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all flex flex-col justify-between">
                    <div class="absolute -right-3 -top-3 w-20 h-20 bg-rose-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-wider">Devoluciones</span>
                            <div class="w-9 h-9 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center shadow-xs">
                                <span class="material-symbols-outlined text-[20px]">assignment_return</span>
                            </div>
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-black text-agro-dark tracking-tight break-words">
                            ${{ number_format($kpis['total_devoluciones_usd'] ?? 0, 2) }}
                        </h3>
                    </div>
                    <div class="relative z-10 mt-3 pt-2.5 border-t border-gray-100">
                        <p class="text-xs font-bold text-rose-700 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[15px]">history</span>
                            <span>{{ $kpis['cantidad_devoluciones'] ?? 0 }} reembolsos (Bs. {{ number_format($kpis['total_devoluciones_ves'] ?? 0, 2) }})</span>
                        </p>
                    </div>
                </div>

            </div>

            {{-- 2. FILA GRÁFICA: EVOLUCIÓN TEMPORAL & COMPARATIVA WEB vs POS --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Gráfico 1: Tendencia Temporal de Ventas (2 Columnas) --}}
                <div class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-100 shadow-sm lg:col-span-2 flex flex-col justify-between">
                    <div>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                            <div>
                                <h2 class="text-base sm:text-lg font-black text-agro-dark flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary text-[22px]">show_chart</span>
                                    <span>Tendencia de Ventas en el Tiempo</span>
                                </h2>
                                <p class="text-xs text-gray-500 font-medium mt-0.5">Evolución continua de ingresos y transacciones en el período</p>
                            </div>

                            {{-- Botones de Alternar Métrica --}}
                            <div class="flex items-center gap-1.5 p-1 bg-gray-100 rounded-xl self-start sm:self-auto border border-gray-200/60">
                                <button type="button" onclick="cambiarMetricaTimeline('ingresos')" id="btnTimelineIngresos" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-white text-agro-dark shadow-xs transition-all">
                                    $ Ingresos
                                </button>
                                <button type="button" onclick="cambiarMetricaTimeline('pedidos')" id="btnTimelinePedidos" class="px-3 py-1.5 text-xs font-bold rounded-lg text-gray-500 hover:text-agro-dark transition-all">
                                    # Pedidos
                                </button>
                            </div>
                        </div>

                        <div class="h-80 w-full relative">
                            <canvas id="chartTimelineCanvas"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Gráfico 2: Comparativa Web vs POS (1 Columna) --}}
                <div class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h2 class="text-base font-black text-agro-dark flex items-center gap-2">
                                    <span class="material-symbols-outlined text-teal-600 text-[22px]">balance</span>
                                    <span>Web vs POS (Física)</span>
                                </h2>
                                <p class="text-xs text-gray-500 font-medium mt-0.5">Distribución del volumen por canal de venta</p>
                            </div>
                        </div>

                        {{-- Gráfico Donut de Canales --}}
                        <div class="h-48 w-full relative mb-5">
                            <canvas id="chartCanalesCanvas"></canvas>
                        </div>

                        {{-- Tarjetas Comparativas Resumen (Amplias y Desplegadas) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div class="p-3.5 rounded-2xl bg-blue-50/80 border border-blue-100 flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-[10px] font-black text-blue-700 uppercase tracking-wider flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">language</span> Tienda Web
                                    </span>
                                    <span class="text-[10px] font-bold text-blue-600 bg-white px-1.5 py-0.2 rounded border border-blue-200">
                                        {{ $comparativa_canales['web']['porcentaje'] }}%
                                    </span>
                                </div>
                                <p class="text-lg font-black text-blue-900 mt-1">${{ number_format($comparativa_canales['web']['total_usd'], 2) }}</p>
                                <div class="mt-1 pt-1.5 border-t border-blue-100/70 text-[10.5px] text-gray-600 space-y-0.5">
                                    <p class="font-semibold">{{ $comparativa_canales['web']['pedidos'] }} pedidos procesados</p>
                                    <p class="text-gray-500">Ticket prom: <strong class="text-blue-900">${{ number_format($comparativa_canales['web']['ticket_promedio'], 2) }}</strong></p>
                                </div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-teal-50/80 border border-teal-100 flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-[10px] font-black text-teal-700 uppercase tracking-wider flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">point_of_sale</span> Mostrador POS
                                    </span>
                                    <span class="text-[10px] font-bold text-teal-600 bg-white px-1.5 py-0.2 rounded border border-teal-200">
                                        {{ $comparativa_canales['pos']['porcentaje'] }}%
                                    </span>
                                </div>
                                <p class="text-lg font-black text-teal-900 mt-1">${{ number_format($comparativa_canales['pos']['total_usd'], 2) }}</p>
                                <div class="mt-1 pt-1.5 border-t border-teal-100/70 text-[10.5px] text-gray-600 space-y-0.5">
                                    <p class="font-semibold">{{ $comparativa_canales['pos']['pedidos'] }} tickets en mostrador</p>
                                    <p class="text-gray-500">Ticket prom: <strong class="text-teal-900">${{ number_format($comparativa_canales['pos']['ticket_promedio'], 2) }}</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Veredicto Automático Desplegado (Fondo Claro y Elegante) --}}
                    <div class="mt-4 p-4 rounded-2xl bg-gradient-to-r from-blue-50/90 via-sky-50/60 to-indigo-50/80 border border-blue-200/80 shadow-2xs flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-blue-600 to-[#1e549e] text-white flex items-center justify-center shrink-0 shadow-sm shadow-blue-600/20 mt-0.5">
                            <span class="material-symbols-outlined text-[22px]">military_tech</span>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] text-blue-700 font-black uppercase tracking-wider">Canal Comercial Líder</span>
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-black bg-blue-100 text-blue-800 border border-blue-200/60">
                                    Mayor Facturación
                                </span>
                            </div>
                            <p class="font-black text-agro-dark text-sm mt-1">
                                {{ $comparativa_canales['canal_ganador_ingresos'] }}
                            </p>
                            <p class="text-xs text-gray-600 font-medium mt-0.5">
                                Supera al otro canal con una ventaja de <strong class="text-blue-900 font-black">${{ number_format($comparativa_canales['diferencia_usd'], 2) }}</strong> en el período seleccionado.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- 3. FILA GRÁFICA: MÉTODOS DE PAGO (COMPLETAMENTE DESPLEGADO CON COLORES 1:1) & TOP 10 PRODUCTOS --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Gráfico 3: Métodos de Pago Más Utilizados (SIN SCROLLBAR - TOTALMENTE DESPLEGADO CON COLORES) --}}
                <div class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                            <div>
                                <h2 class="text-base sm:text-lg font-black text-agro-dark flex items-center gap-2">
                                    <span class="material-symbols-outlined text-amber-500 text-[22px]">account_balance_wallet</span>
                                    <span>Métodos de Pago Preferidos</span>
                                </h2>
                                <p class="text-xs text-gray-500 font-medium mt-0.5">Distribución total de ingresos recibidos por divisa y pasarela</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-gray-100 text-gray-700 w-fit">
                                {{ count($metodos_pago) }} métodos activos
                            </span>
                        </div>

                        {{-- Gráfico Donut de Métodos --}}
                        <div class="h-56 w-full relative mb-5">
                            <canvas id="chartMetodosPagoCanvas"></canvas>
                        </div>

                        {{-- Desglose Completo en Cuadrícula Desplegada (Con colores correspondientes 1:1) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-2">
                            @forelse($metodos_pago as $m)
                                <div class="p-3 rounded-2xl border transition-all flex items-center justify-between gap-2.5 group hover:shadow-sm"
                                     style="background-color: {{ $m['color'] }}0c; border-color: {{ $m['color'] }}38; border-left: 4px solid {{ $m['color'] }};">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 shadow-2xs" 
                                             style="background-color: {{ $m['color'] }}; color: #ffffff;">
                                            <span class="material-symbols-outlined text-[18px]">{{ $m['icono'] }}</span>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="font-black text-xs text-agro-dark block truncate" title="{{ $m['nombre'] }}">
                                                {{ $m['nombre'] }}
                                            </span>
                                            <span class="text-[10.5px] text-gray-500 font-semibold block">
                                                {{ $m['transacciones'] }} transacciones
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="font-black text-xs text-agro-dark block">
                                            ${{ number_format($m['total_usd'], 2) }}
                                        </span>
                                        <span class="inline-block text-[9.5px] font-black px-1.5 py-0.2 rounded-md shadow-2xs"
                                              style="background-color: {{ $m['color'] }}20; color: {{ $m['color'] }}; border: 1px solid {{ $m['color'] }}50;">
                                            {{ $m['pct_monto'] }}%
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="sm:col-span-2 py-6 text-center text-xs text-gray-400 font-medium">
                                    No se han registrado pagos en este período.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Gráfico 4: Top 10 Productos Más Vendidos --}}
                <div class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h2 class="text-base sm:text-lg font-black text-agro-dark flex items-center gap-2">
                                    <span class="material-symbols-outlined text-rose-500 text-[22px]">bar_chart</span>
                                    <span>Top 10 Productos Más Vendidos</span>
                                </h2>
                                <p class="text-xs text-gray-500 font-medium mt-0.5">Ranking ordenado por cantidad física despachada</p>
                            </div>
                        </div>

                        <div class="h-80 w-full relative">
                            <canvas id="chartTopProductosCanvas"></canvas>
                        </div>
                    </div>

                    <div class="mt-4 p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs text-gray-500 font-medium">
                        <span class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-primary">info</span>
                            <span>Pasa el cursor sobre las barras para ver la facturación en USD</span>
                        </span>
                        <a href="#tablaDetalleProductos" class="font-bold text-primary hover:underline text-xs flex items-center gap-0.5">
                            <span>Ver tabla completa</span>
                            <span class="material-symbols-outlined text-[14px]">arrow_downward</span>
                        </a>
                    </div>
                </div>

            </div>

            {{-- 4. SECCIÓN LOGÍSTICA & DELIVERIES (REDISEÑADA Y EQUILIBRADA) --}}
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-100 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-agro-dark flex items-center gap-2">
                            <span class="material-symbols-outlined text-orange-500 text-[22px]">local_shipping</span>
                            <span>Estadísticas de Logística y Zonas de Delivery</span>
                        </h2>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">Control de despachos a domicilio, retiros en tienda y facturación de fletes</p>
                    </div>

                    <a href="{{ route('admin.zonas_delivery.index') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-orange-50 text-orange-800 hover:bg-orange-100 transition-colors border border-orange-200/80 shadow-2xs flex items-center gap-1.5 w-fit">
                        <span class="material-symbols-outlined text-[16px]">map</span>
                        <span>Configurar Zonas y Tarifas</span>
                    </a>
                </div>

                {{-- 4 Mini-KPIs Logísticos en Fila Equilibrada --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
                    {{-- KPI 1: Despachos Totales --}}
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-200 text-slate-700 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[20px]">inventory</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-wider block">Total Despachos</span>
                            <h4 class="text-xl font-black text-agro-dark leading-tight">{{ $logistica['total_envios'] }}</h4>
                            <span class="text-[10px] text-gray-500 font-semibold truncate block">Órdenes completadas</span>
                        </div>
                    </div>

                    {{-- KPI 2: Envíos a Domicilio --}}
                    <div class="p-4 rounded-2xl bg-orange-50/70 border border-orange-200/70 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <span class="material-symbols-outlined text-[20px]">moped</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] font-black text-orange-800 uppercase tracking-wider block">A Domicilio</span>
                            <h4 class="text-xl font-black text-orange-950 leading-tight">{{ $logistica['pedidos_delivery'] }}</h4>
                            <span class="text-[10px] text-orange-700 font-bold truncate block">{{ $logistica['pct_delivery'] }}% del volumen</span>
                        </div>
                    </div>

                    {{-- KPI 3: Retiros en Tienda --}}
                    <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-200/70 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <span class="material-symbols-outlined text-[20px]">store</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] font-black text-blue-800 uppercase tracking-wider block">Retiro en Sede</span>
                            <h4 class="text-xl font-black text-blue-950 leading-tight">{{ $logistica['pedidos_retiro'] }}</h4>
                            <span class="text-[10px] text-blue-700 font-bold truncate block">{{ 100 - $logistica['pct_delivery'] }}% del volumen</span>
                        </div>
                    </div>

                    {{-- KPI 4: Recaudación por Fletes --}}
                    <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/70 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <span class="material-symbols-outlined text-[20px]">payments</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] font-black text-emerald-800 uppercase tracking-wider block">Total Fletes</span>
                            <h4 class="text-xl font-black text-emerald-950 leading-tight">${{ number_format($logistica['ingresos_flete_usd'], 2) }}</h4>
                            <span class="text-[10px] text-emerald-700 font-bold truncate block">Bs. {{ number_format($logistica['ingresos_flete_usd'] * $tasa_bcv, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Fila de Detalle Operativo en 2 Columnas Balanceadas --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    {{-- Panel Izquierdo: Balance Operativo y Financiero del Flete --}}
                    <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/80 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[10.5px] font-black text-agro-dark uppercase tracking-wider flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px] text-primary">pie_chart</span>
                                    <span>Modalidad de Despacho (Delivery vs Retiro)</span>
                                </span>
                            </div>

                            {{-- Barra Proporcional --}}
                            <div class="w-full bg-gray-200 rounded-full h-3.5 overflow-hidden flex shadow-inner">
                                <div class="bg-orange-500 h-3.5 transition-all duration-500" style="width: {{ $logistica['pct_delivery'] }}%" title="Delivery: {{ $logistica['pct_delivery'] }}%"></div>
                                <div class="bg-blue-600 h-3.5 transition-all duration-500" style="width: {{ 100 - $logistica['pct_delivery'] }}%" title="Retiro: {{ 100 - $logistica['pct_delivery'] }}%"></div>
                            </div>
                            <div class="flex justify-between items-center text-xs font-bold mt-2">
                                <span class="text-orange-600 flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                                    Delivery: {{ $logistica['pedidos_delivery'] }} pedidos ({{ $logistica['pct_delivery'] }}%)
                                </span>
                                <span class="text-blue-700 flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                    Retiro: {{ $logistica['pedidos_retiro'] }} pedidos ({{ 100 - $logistica['pct_delivery'] }}%)
                                </span>
                            </div>

                            {{-- Métricas Clave de Logística --}}
                            <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-slate-200/80 text-xs">
                                <div class="p-3 rounded-xl bg-white border border-slate-200/70">
                                    <span class="text-[10px] text-gray-500 font-bold block uppercase">Ticket Promedio Flete</span>
                                    <p class="text-base font-black text-agro-dark mt-0.5">
                                        ${{ $logistica['pedidos_delivery'] > 0 ? number_format($logistica['ingresos_flete_usd'] / $logistica['pedidos_delivery'], 2) : '0.00' }}
                                    </p>
                                    <span class="text-[9.5px] text-gray-400 font-medium">Por envío a domicilio</span>
                                </div>
                                <div class="p-3 rounded-xl bg-white border border-slate-200/70">
                                    <span class="text-[10px] text-gray-500 font-bold block uppercase">Incidencia en Ventas</span>
                                    <p class="text-base font-black text-agro-dark mt-0.5">
                                        {{ $kpis['total_ingresos_usd'] > 0 ? number_format(($logistica['ingresos_flete_usd'] / $kpis['total_ingresos_usd']) * 100, 1) : '0.0' }}%
                                    </p>
                                    <span class="text-[9.5px] text-gray-400 font-medium">Del total de ingresos</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 p-3 rounded-xl bg-blue-50/60 border border-blue-100 text-[11px] text-blue-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-blue-600 shrink-0">info</span>
                            <span>El flete es abonado por el comprador al seleccionar su zona de entrega y no afecta el margen neto de los productos.</span>
                        </div>
                    </div>

                    {{-- Panel Derecho: Desglose Completo por Zonas de Cobertura --}}
                    <div class="p-5 rounded-2xl bg-white border border-gray-200 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[10.5px] font-black text-gray-500 uppercase tracking-wider flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px] text-orange-500">pin_drop</span>
                                    <span>Demanda por Zona Geográfica</span>
                                </span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase">Tarifa y Despachos</span>
                            </div>

                            <div class="space-y-2.5">
                                @php 
                                    $zonasArray = is_array($logistica['zonas']) ? $logistica['zonas'] : (method_exists($logistica['zonas'], 'toArray') ? $logistica['zonas']->toArray() : (array)$logistica['zonas']);
                                    $maxPedidosZona = count($zonasArray) > 0 ? max(array_column($zonasArray, 'pedidos')) : 1;
                                    if ($maxPedidosZona == 0) $maxPedidosZona = 1;
                                @endphp
                                @forelse($logistica['zonas'] as $z)
                                    @php 
                                        $porcentajeBarra = round(($z['pedidos'] / $maxPedidosZona) * 100);
                                    @endphp
                                    <div class="p-3 rounded-xl bg-gray-50 hover:bg-gray-100/80 transition-colors border border-gray-100">
                                        <div class="flex justify-between items-center text-xs mb-1.5">
                                            <div class="flex items-center gap-2 min-w-0 pr-2">
                                                <span class="font-bold text-agro-dark truncate text-xs" title="{{ $z['zona'] }}">{{ $z['zona'] }}</span>
                                                <span class="text-[10px] font-bold text-gray-400 font-mono bg-white px-1.5 py-0.2 rounded border border-gray-200">
                                                    Tarifa: ${{ number_format($z['tarifa_base'], 2) }}
                                                </span>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <span class="font-mono font-black text-orange-600 text-xs">
                                                    {{ $z['pedidos'] }} envíos
                                                </span>
                                                <span class="text-[10px] font-bold text-gray-500 block">
                                                    (${{ number_format($z['flete_usd'], 2) }})
                                                </span>
                                            </div>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-orange-500 h-1.5 rounded-full" style="width: {{ $porcentajeBarra }}%"></div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-gray-400 text-center py-6">No hay zonas con despachos en este rango.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                            <span class="text-gray-400 font-medium">6 zonas configuradas en el sistema</span>
                            <a href="{{ route('admin.zonas_delivery.index') }}" class="font-bold text-primary hover:underline flex items-center gap-0.5">
                                <span>Ver mapa de zonas</span>
                                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 5. TABLA COMPLETA DETALLADA: PRODUCTOS MÁS VENDIDOS --}}
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden" id="tablaDetalleProductos">
                <div class="p-5 sm:p-7 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-agro-dark flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[22px]">inventory_2</span>
                            <span>Ranking Detallado de Ventas por Producto</span>
                        </h2>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">Información minuciosa de volumen despachado, ingresos en divisas, porcentaje de ventas y stock</p>
                    </div>

                    <div class="relative w-full sm:w-72">
                        <span class="material-symbols-outlined absolute left-3.5 top-2.5 text-gray-400 text-[18px]">search</span>
                        <input type="text" id="busquedaTablaProductos" oninput="filtrarTablaProductos(this.value)" placeholder="Buscar producto, SKU o categoría..." class="w-full h-10 pl-10 pr-3 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-bold text-agro-dark outline-none focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs min-w-[760px]">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100 text-[10px] font-black text-gray-500 uppercase tracking-wider">
                                <th class="py-3.5 px-4 text-center w-16">Puesto</th>
                                <th class="py-3.5 px-4">Producto e Identificación</th>
                                <th class="py-3.5 px-4">Categoría</th>
                                <th class="py-3.5 px-4 text-center">Unidades</th>
                                <th class="py-3.5 px-4 text-right">Facturación USD</th>
                                <th class="py-3.5 px-4 text-right">Precio Prom.</th>
                                <th class="py-3.5 px-4 text-center">% Ventas</th>
                                <th class="py-3.5 px-4 text-center">Inventario</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-medium text-agro-dark">
                            @forelse($top_productos as $p)
                                <tr class="hover:bg-slate-50/80 transition-colors fila-producto" data-busqueda="{{ strtolower($p['nombre'] . ' ' . $p['sku'] . ' ' . $p['categoria']) }}">
                                    {{-- Ranking con Medallas --}}
                                    <td class="py-3.5 px-4 text-center font-black">
                                        @if($p['ranking'] === 1)
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-100 text-amber-800 text-sm font-black shadow-xs">🥇</span>
                                        @elseif($p['ranking'] === 2)
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-200 text-slate-800 text-sm font-black shadow-xs">🥈</span>
                                        @elseif($p['ranking'] === 3)
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-700/20 text-amber-900 text-sm font-black shadow-xs">🥉</span>
                                        @else
                                            <span class="text-gray-400 font-bold text-xs">#{{ $p['ranking'] }}</span>
                                        @endif
                                    </td>

                                    {{-- Producto con Miniatura y Nombre en 2 líneas sin recortar abruptamente --}}
                                    <td class="py-3.5 px-4 max-w-xs">
                                        <div class="flex items-start gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden shrink-0 flex items-center justify-center mt-0.5">
                                                @if($p['foto'])
                                                    <img src="{{ $p['foto'] }}" alt="{{ $p['nombre'] }}" class="w-full h-full object-cover">
                                                @else
                                                    <span class="material-symbols-outlined text-gray-300 text-[20px]">inventory_2</span>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <a href="{{ route('admin.productos.index', ['buscar' => $p['nombre']]) }}" class="font-bold text-agro-dark hover:text-primary transition-colors text-xs leading-snug block" title="{{ $p['nombre'] }}">
                                                    {{ $p['nombre'] }}
                                                </a>
                                                <span class="text-[10px] text-gray-400 font-mono block mt-0.5">SKU: {{ $p['sku'] }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Categoría --}}
                                    <td class="py-3.5 px-4 text-gray-600 whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-lg bg-gray-100 text-[10.5px] font-bold text-gray-700 border border-gray-200/60">
                                            {{ $p['categoria'] }}
                                        </span>
                                    </td>

                                    {{-- Cantidad --}}
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="font-black text-sm text-agro-dark">
                                            {{ number_format($p['unidades_vendidas'], ($p['unidades_vendidas'] == (int)$p['unidades_vendidas'] ? 0 : 2)) }}
                                        </span>
                                        <span class="text-[10px] font-bold text-gray-400 block">{{ $p['unidad'] }}</span>
                                    </td>

                                    {{-- Ingresos USD & VES --}}
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <span class="font-black text-agro-dark block text-sm">${{ number_format($p['total_usd'], 2) }}</span>
                                        <span class="text-[10px] font-bold text-gray-400 block">Bs. {{ number_format($p['total_ves'], 2) }}</span>
                                    </td>

                                    {{-- Precio Promedio --}}
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-gray-600 whitespace-nowrap">
                                        ${{ number_format($p['precio_promedio'], 2) }}
                                    </td>

                                    {{-- % Participación con Barra --}}
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-2">
                                            <div class="w-16 bg-gray-100 rounded-full h-2 overflow-hidden">
                                                <div class="bg-primary h-2 rounded-full" style="width: {{ min(100, $p['pct_participacion'] * 2.5) }}%"></div>
                                            </div>
                                            <span class="font-bold text-[10px] text-gray-600">{{ $p['pct_participacion'] }}%</span>
                                        </div>
                                    </td>

                                    {{-- Stock Actual con Badges Claros --}}
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if($p['estado_stock'] === 'agotado')
                                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-rose-100 text-rose-700 border border-rose-200">
                                                Agotado
                                            </span>
                                        @elseif($p['estado_stock'] === 'critico')
                                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-200" title="Alerta de stock bajo">
                                                {{ $p['stock_actual'] }} {{ $p['unidad'] }} ⚠️
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                {{ $p['stock_actual'] }} {{ $p['unidad'] }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-gray-400 font-bold">
                                        No se encontraron ventas para los filtros seleccionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 bg-gray-50/60 border-t border-gray-100 flex flex-wrap items-center justify-between text-xs text-gray-500">
                    <span>Mostrando <strong>{{ count($top_productos) }}</strong> productos clasificados</span>
                    <span>Los porcentajes de ventas se calculan sobre la facturación neta del período</span>
                </div>
            </div>

        </div>
    </main>
</div>

@push('scripts')
{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
    // ==========================================
    // DATOS DESDE EL BACKEND
    // ==========================================
    const timelineData = @json($chart_timeline);
    const canalesData = @json($comparativa_canales);
    const metodosPagoData = @json($chart_metodos_pago);
    const topProductosData = @json($chart_top_productos);

    let chartTimelineInstance = null;
    let chartCanalesInstance = null;
    let chartMetodosInstance = null;
    let chartTopProdInstance = null;

    let metricaTimelineActual = 'ingresos'; // 'ingresos' | 'pedidos'

    // ==========================================
    // 1. INICIALIZACIÓN DE GRÁFICOS CHART.JS
    // ==========================================
    document.addEventListener('DOMContentLoaded', function() {
        initChartTimeline();
        initChartCanales();
        initChartMetodos();
        initChartTopProductos();
    });

    // 1.1 Gráfico Timeline (Evolución Temporal)
    function initChartTimeline() {
        const ctx = document.getElementById('chartTimelineCanvas');
        if (!ctx) return;

        const dataValues = metricaTimelineActual === 'ingresos' ? timelineData.ingresos : timelineData.pedidos;
        const labelText = metricaTimelineActual === 'ingresos' ? 'Ingresos ($ USD)' : 'Cantidad de Pedidos';

        chartTimelineInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: timelineData.labels,
                datasets: [{
                    label: labelText,
                    data: dataValues,
                    borderColor: '#1e549e',
                    backgroundColor: 'rgba(52, 146, 207, 0.12)',
                    borderWidth: 2.8,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#3492cf',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4.5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0b1d38',
                        titleFont: { family: 'Work Sans', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Work Sans', size: 12 },
                        padding: 12,
                        cornerRadius: 12,
                        callbacks: {
                            label: function(context) {
                                let v = context.parsed.y;
                                return metricaTimelineActual === 'ingresos' ? ` Facturación: $${v.toFixed(2)}` : ` Órdenes: ${v} pedidos`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: {
                            font: { family: 'Work Sans', size: 11 },
                            callback: function(value) {
                                return metricaTimelineActual === 'ingresos' ? '$' + value : value;
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Work Sans', size: 11 } }
                    }
                }
            }
        });
    }

    function cambiarMetricaTimeline(metrica) {
        metricaTimelineActual = metrica;
        const btnIngresos = document.getElementById('btnTimelineIngresos');
        const btnPedidos = document.getElementById('btnTimelinePedidos');

        if (metrica === 'ingresos') {
            btnIngresos.className = 'px-3 py-1.5 text-xs font-bold rounded-lg bg-white text-agro-dark shadow-xs transition-all';
            btnPedidos.className = 'px-3 py-1.5 text-xs font-bold rounded-lg text-gray-500 hover:text-agro-dark transition-all';
        } else {
            btnPedidos.className = 'px-3 py-1.5 text-xs font-bold rounded-lg bg-white text-agro-dark shadow-xs transition-all';
            btnIngresos.className = 'px-3 py-1.5 text-xs font-bold rounded-lg text-gray-500 hover:text-agro-dark transition-all';
        }

        if (chartTimelineInstance) {
            chartTimelineInstance.data.datasets[0].label = metrica === 'ingresos' ? 'Ingresos ($ USD)' : 'Cantidad de Pedidos';
            chartTimelineInstance.data.datasets[0].data = metrica === 'ingresos' ? timelineData.ingresos : timelineData.pedidos;
            chartTimelineInstance.update();
        }
    }

    // 1.2 Gráfico Canales (Web vs POS)
    function initChartCanales() {
        const ctx = document.getElementById('chartCanalesCanvas');
        if (!ctx) return;

        const webUsd = parseFloat(canalesData.web.total_usd) || 0;
        const posUsd = parseFloat(canalesData.pos.total_usd) || 0;

        chartCanalesInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Tienda Web', 'Mostrador POS'],
                datasets: [{
                    data: [webUsd, posUsd],
                    backgroundColor: ['#3b82f6', '#0d9488'],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { family: 'Work Sans', size: 11, weight: 'bold' }, padding: 14 }
                    },
                    tooltip: {
                        backgroundColor: '#0b1d38',
                        padding: 12,
                        cornerRadius: 12,
                        callbacks: {
                            label: function(context) {
                                let total = webUsd + posUsd;
                                let pct = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return ` $${context.parsed.toFixed(2)} (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // 1.3 Gráfico Métodos de Pago
    function initChartMetodos() {
        const ctx = document.getElementById('chartMetodosPagoCanvas');
        if (!ctx) return;

        chartMetodosInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: metodosPagoData.labels,
                datasets: [{
                    data: metodosPagoData.data_usd,
                    backgroundColor: metodosPagoData.colores,
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0b1d38',
                        padding: 12,
                        cornerRadius: 12,
                        callbacks: {
                            label: function(context) {
                                let v = context.parsed;
                                let total = metodosPagoData.data_usd.reduce((a, b) => a + b, 0);
                                let pct = total > 0 ? ((v / total) * 100).toFixed(1) : 0;
                                return ` Recaudado: $${v.toFixed(2)} (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // 1.4 Gráfico Top 10 Productos Más Vendidos con Formateo Inteligente de Ejes
    function initChartTopProductos() {
        const ctx = document.getElementById('chartTopProductosCanvas');
        if (!ctx) return;

        chartTopProdInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: topProductosData.labels,
                datasets: [{
                    label: 'Unidades Vendidas',
                    data: topProductosData.unidades,
                    backgroundColor: 'rgba(30, 84, 158, 0.88)',
                    hoverBackgroundColor: '#1e549e',
                    borderRadius: 8,
                    maxBarThickness: 24
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0b1d38',
                        padding: 12,
                        cornerRadius: 12,
                        callbacks: {
                            title: function(context) {
                                let idx = context[0].dataIndex;
                                return topProductosData.labels[idx] || '';
                            },
                            label: function(context) {
                                let idx = context.dataIndex;
                                let usd = topProductosData.ingresos[idx] || 0;
                                return ` Despachadas: ${context.parsed.x} unidades | Facturación: $${usd.toFixed(2)}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: { font: { family: 'Work Sans', size: 10 } }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { 
                            font: { family: 'Work Sans', size: 10, weight: 'bold' },
                            // Truncar etiquetas largas en el eje para no invadir el canvas
                            callback: function(val, index) {
                                let label = this.getLabelForValue(val) || '';
                                return label.length > 22 ? label.substring(0, 20) + '…' : label;
                            }
                        }
                    }
                }
            }
        });
    }

    // ==========================================
    // 2. FILTRADO RÁPIDO EN VIVO
    // ==========================================
    function cambiarPeriodoFiltro(periodo) {
        const cajaFechas = document.getElementById('cajaFechasPersonalizadas');
        if (periodo === 'personalizado') {
            cajaFechas.classList.remove('hidden');
            cajaFechas.classList.add('flex');
        } else {
            cajaFechas.classList.add('hidden');
            cajaFechas.classList.remove('flex');
            document.getElementById('formFiltros').submit();
        }
    }

    function filtrarTablaProductos(texto) {
        const query = texto.toLowerCase().trim();
        document.querySelectorAll('.fila-producto').forEach(row => {
            const data = row.getAttribute('data-busqueda') || '';
            row.style.display = (query === '' || data.includes(query)) ? '' : 'none';
        });
    }
</script>
@endpush
@endsection
