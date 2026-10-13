@extends('layouts.admin')

@section('title', 'Logs y Auditoría del Sistema - Agrosuministros Rodríguez')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    {{-- SIDEBAR LATERAL --}}
    @include('admin.partials.sidebar')

    {{-- CONTENIDO PRINCIPAL --}}
    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        
        {{-- TOPBAR ADMIN --}}
        @include('admin.partials.topbar')

        {{-- Área de Contenido --}}
        <div class="p-4 sm:p-8 animate-fade-in-up pb-24">
                      {{-- Encabezado de la Sección con Botones de Acción --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-2xl font-black text-agro-dark flex items-center gap-2">
                        <span class="material-symbols-outlined text-indigo-600 text-[32px]">manage_search</span>
                        Historial de Actividades y Movimientos
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Aquí puedes ver de forma clara y sencilla todo lo que pasa en el negocio: quién cambió un precio, quién cobró un pedido, quién borró algo y qué valor tenía antes.
                    </p>
                </div>
                
                {{-- Botones de Acción --}}
                <div class="flex items-center gap-2.5 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold shadow-2xs">
                        <span class="material-symbols-outlined text-[16px] text-emerald-600">verified_user</span>
                        <span>Registro Protegido (Inmutable)</span>
                    </span>

                    {{-- Exportar CSV --}}
                    <button type="button" 
                            onclick="exportarLogsCSV()" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white hover:bg-gray-50 text-gray-700 hover:text-indigo-600 font-bold text-xs border border-gray-200 transition-all shadow-2xs hover:scale-105 active:scale-95">
                        <span class="material-symbols-outlined text-[18px] text-indigo-600">download</span>
                        <span>Descargar Reporte Excel</span>
                    </button>
                </div>
            </div>

            {{-- Tarjetas KPI de Auditoría --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                
                {{-- KPI 1: Total Eventos --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total de Movimientos</span>
                        <div class="size-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">database</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-black text-gray-900 leading-none">{{ number_format($totalLogs) }}</span>
                        <span class="text-[10px] text-indigo-600 font-bold uppercase">Registros</span>
                    </div>
                </div>

                {{-- KPI 2: Modificaciones Hoy --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Movimientos de Hoy</span>
                        <div class="size-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">today</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-black text-emerald-600 leading-none">{{ number_format($logsHoy) }}</span>
                        <span class="text-[10px] text-emerald-700 font-bold uppercase">En 24 horas</span>
                    </div>
                </div>

                {{-- KPI 3: Usuarios Activos Auditados --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Personas Trabajando Hoy</span>
                        <div class="size-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">group</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-black text-gray-900 leading-none">{{ $usuariosAuditadosHoy }}</span>
                        <span class="text-[10px] text-sky-600 font-bold uppercase">Colaboradores</span>
                    </div>
                </div>

                {{-- KPI 4: Acciones Críticas / Seguridad --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Acciones Delicadas</span>
                        <div class="size-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">security</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-black text-rose-600 leading-none">{{ number_format($accionesCriticas) }}</span>
                        <span class="text-[10px] text-rose-700 font-bold uppercase">Borrados / Roles</span>
                    </div>
                </div>

            </div>

            {{-- Panel de Filtros Avanzados y Buscador en Vivo --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-4 sm:p-5 mb-6 space-y-4">
                
                <div class="flex flex-col lg:flex-row gap-3 items-stretch lg:items-center">
                    
                    {{-- 1. Buscador en Vivo --}}
                    <div class="flex-1 relative group">
                        <span class="material-symbols-outlined absolute left-3.5 top-2.5 text-gray-400 group-focus-within:text-indigo-600 text-[20px] transition-colors pointer-events-none">search</span>
                        <input type="text" 
                                id="buscarLogInput" 
                                value="{{ request('buscar', '') }}"
                                autocomplete="off"
                                class="w-full pl-10 pr-20 py-2.5 bg-gray-50/60 border border-gray-200 rounded-xl text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none font-medium text-gray-800 placeholder-gray-400" 
                                placeholder="Escribe el nombre de alguien, un producto, pedido o lo que pasó...">
                        
                        <div class="absolute right-2.5 top-2.5 flex items-center gap-1.5">
                            <span id="log_search_spinner" style="display: none;" class="material-symbols-outlined text-indigo-600 text-[18px] animate-spin">progress_activity</span>
                            <button type="button" 
                                    id="btn_clear_log_search" 
                                    onclick="limpiarBuscadorLogs()" 
                                    style="{{ request('buscar') ? 'display: flex;' : 'display: none;' }}"
                                    class="size-6 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-800 items-center justify-center transition-colors" 
                                    title="Limpiar búsqueda (Escape)">
                                <span class="material-symbols-outlined text-[15px]">close</span>
                            </button>
                            <kbd class="hidden sm:inline-block px-1.5 py-0.5 text-[9px] font-mono font-bold text-gray-400 bg-gray-50 border border-gray-200 rounded">ESC</kbd>
                        </div>
                    </div>

                    {{-- 2. Selector Módulo / Pantalla --}}
                    <div class="w-full sm:w-auto lg:w-52 flex items-center gap-2 px-3 py-2 bg-gray-50/60 border border-gray-200 rounded-xl transition-all focus-within:bg-white focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500">
                        <span class="material-symbols-outlined text-gray-400 text-[18px] shrink-0 pointer-events-none">folder_open</span>
                        <select id="filtroModulo" class="w-full bg-transparent border-none p-0 outline-none focus:ring-0 text-xs font-bold text-gray-700 cursor-pointer">
                            <option value="todos" {{ request('modulo') === 'todos' ? 'selected' : '' }}>En cualquier pantalla</option>
                            @foreach($modulosDisponibles as $mod)
                                <option value="{{ $mod }}" {{ request('modulo') === $mod ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $mod)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 3. Selector Tipo de Acción --}}
                    <div class="w-full sm:w-auto lg:w-52 flex items-center gap-2 px-3 py-2 bg-gray-50/60 border border-gray-200 rounded-xl transition-all focus-within:bg-white focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500">
                        <span class="material-symbols-outlined text-gray-400 text-[18px] shrink-0 pointer-events-none">tune</span>
                        <select id="filtroEvento" class="w-full bg-transparent border-none p-0 outline-none focus:ring-0 text-xs font-bold text-gray-700 cursor-pointer">
                            <option value="todos" {{ request('evento') === 'todos' ? 'selected' : '' }}>Cualquier tipo de acción</option>
                            <option value="CREAR" {{ request('evento') === 'CREAR' ? 'selected' : '' }}>Crearon algo nuevo</option>
                            <option value="ACTUALIZAR" {{ request('evento') === 'ACTUALIZAR' ? 'selected' : '' }}>Modificaron datos</option>
                            <option value="ELIMINAR" {{ request('evento') === 'ELIMINAR' ? 'selected' : '' }}>Borraron un registro</option>
                            <option value="SEGURIDAD" {{ request('evento') === 'SEGURIDAD' ? 'selected' : '' }}>Seguridad e Inicios de Sesión</option>
                            <option value="ESTADO" {{ request('evento') === 'ESTADO' ? 'selected' : '' }}>Cambiaron un estado</option>
                        </select>
                    </div>

                    {{-- 4. Selector Persona Responsable --}}
                    <div class="w-full sm:w-auto lg:w-48 flex items-center gap-2 px-3 py-2 bg-gray-50/60 border border-gray-200 rounded-xl transition-all focus-within:bg-white focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500">
                        <span class="material-symbols-outlined text-gray-400 text-[18px] shrink-0 pointer-events-none">person</span>
                        <select id="filtroUsuario" class="w-full bg-transparent border-none p-0 outline-none focus:ring-0 text-xs font-bold text-gray-700 cursor-pointer">
                            <option value="todos" {{ request('usuario_id') === 'todos' ? 'selected' : '' }}>Cualquier persona</option>
                            @foreach($usuariosAuditados as $u)
                                <option value="{{ $u->id }}" {{ (string)request('usuario_id') === (string)$u->id ? 'selected' : '' }}>
                                    {{ $u->nombre }} {{ $u->apellido }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                {{-- Segunda Línea de Filtros: Fechas y Botón Limpiar --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-3 border-t border-gray-100">
                    <div class="flex items-center gap-2 flex-wrap text-xs font-bold text-gray-500">
                        <span class="flex items-center gap-1 text-[11px] font-black uppercase text-gray-400">
                            <span class="material-symbols-outlined text-[16px]">calendar_month</span> Fechas:
                        </span>
                        <input type="date" id="filtroFechaDesde" value="{{ request('fecha_desde', '') }}" class="px-2.5 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold focus:bg-white outline-none">
                        <span>a</span>
                        <input type="date" id="filtroFechaHasta" value="{{ request('fecha_hasta', '') }}" class="px-2.5 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold focus:bg-white outline-none">
                    </div>

                    <button type="button" 
                            id="btnResetFiltrosLogs" 
                            onclick="resetearTodosFiltros()" 
                            class="inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-all self-end sm:self-auto">
                        <span class="material-symbols-outlined text-[16px]">filter_alt_off</span>
                        <span>Quitar filtros</span>
                    </button>
                </div>

            </div>

            {{-- Contenedor de la Tabla con Overlay de Carga --}}
            <div class="relative bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div id="loadingOverlayLogs" class="absolute inset-0 bg-white/70 backdrop-blur-xs flex items-center justify-center z-20 hidden transition-opacity">
                    <div class="flex flex-col items-center gap-2">
                        <div class="size-9 border-3 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                        <span class="text-xs font-bold text-indigo-900">Consultando registros de auditoría...</span>
                    </div>
                </div>

                <div id="logsTableContainer">
                    @include('admin.logs.partials._table', ['logs' => $logs])
                </div>
            </div>

        </div>

    </main>

</div>

{{-- ========================================================================= --}}
{{-- MODAL AMIGABLE DE DETALLE DEL MOVIMIENTO (EXPLICACIÓN FÁCIL Y VISUAL)     --}}
{{-- ========================================================================= --}}
<div id="modalDetalleLog" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="backdropLog" onclick="cerrarModalLog()"></div>

    <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-3xl opacity-0 scale-95 flex flex-col max-h-[92vh]" id="panelLog">
            
            {{-- Encabezado Modal --}}
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-white shrink-0">
                <div class="flex items-center gap-3">
                    <div id="modalLogIconWrap" class="size-11 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <span id="modalLogIcon" class="material-symbols-outlined text-[24px]">manage_search</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-lg font-black text-gray-900" id="modalLogTitle">Detalle de la Actividad</h3>
                            <span id="modalLogBadge" class="text-[11px] font-black uppercase px-2.5 py-0.5 rounded-lg border">Acción</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5" id="modalLogSubtitle">Fecha y hora del movimiento</p>
                    </div>
                </div>

                <button type="button" onclick="cerrarModalLog()" class="size-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-800 flex items-center justify-center transition-colors">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            {{-- Cuerpo Modal Scrollable --}}
            <div class="px-6 py-5 overflow-y-auto custom-scrollbar flex-1 space-y-5">
                
                {{-- Banner: Explicación de lo que pasó en criollo --}}
                <div class="p-4 bg-blue-50/70 rounded-2xl border border-blue-100 flex items-start gap-3">
                    <div class="size-9 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
                        <span class="material-symbols-outlined text-[20px]">info</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-black text-blue-900 uppercase tracking-widest block mb-0.5">¿Qué ocurrió en el sistema?</span>
                        <p class="text-sm font-bold text-blue-950 leading-snug" id="modalLogDesc">-</p>
                    </div>
                </div>

                {{-- Tarjetas: ¿Quién lo hizo? y ¿Desde dónde? --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                    
                    {{-- Tarjeta 1: Usuario Responsable --}}
                    <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/70 space-y-2.5">
                        <span class="text-[10px] font-black text-indigo-800 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-indigo-600">person</span> ¿Quién lo hizo?
                        </span>
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-xl bg-slate-900 text-white font-black text-sm flex items-center justify-center shrink-0 shadow-2xs" id="modalLogUserAvatar">
                                U
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="text-xs font-black text-gray-900 truncate" id="modalLogUserName">Usuario</span>
                                <span class="text-[11px] text-gray-500 truncate" id="modalLogUserEmail">-</span>
                                <div class="mt-1">
                                    <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100" id="modalLogUserRole">Rol</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tarjeta 2: Dónde y desde qué equipo --}}
                    <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/70 space-y-2 text-xs">
                        <span class="text-[10px] font-black text-indigo-800 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-indigo-600">devices</span> ¿Desde dónde se hizo?
                        </span>
                        <div class="space-y-1.5 text-[11px]">
                            <div class="flex items-start gap-1.5">
                                <span class="text-gray-400 font-medium shrink-0">Pantalla:</span>
                                <span class="font-bold text-gray-800" id="modalLogPantalla">-</span>
                            </div>
                            <div class="flex items-start gap-1.5">
                                <span class="text-gray-400 font-medium shrink-0">Equipo:</span>
                                <span class="font-semibold text-gray-700" id="modalLogDispositivo">-</span>
                            </div>
                            <div class="flex items-start gap-1.5">
                                <span class="text-gray-400 font-medium shrink-0">Conexión / IP:</span>
                                <span class="font-mono text-gray-600 font-medium" id="modalLogIp">-</span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- SECCIÓN PRINCIPAL: ¿QUÉ VALORES CAMBIARON? (Visual y Criollo) --}}
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-black text-gray-800 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[17px] text-indigo-600">checklist</span> 
                            ¿Qué datos cambiaron exactamente?
                        </span>
                        <span class="text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md" id="modalLogCambiosConteo">0 cambios</span>
                    </div>

                    {{-- Lista Dinámica de Cambios Amigables --}}
                    <div id="modalLogCambiosLista" class="space-y-2.5">
                        {{-- Generado por JavaScript --}}
                    </div>
                </div>

                {{-- SECCIÓN COLAPSABLE: Datos Técnicos para Desarrolladores (Oculto por defecto) --}}
                <div class="pt-2 border-t border-gray-100">
                    <button type="button" 
                            onclick="toggleDetallesTecnicos()" 
                            class="flex items-center gap-1.5 text-xs font-bold text-gray-500 hover:text-indigo-600 transition-colors py-1 group/tech">
                        <span class="material-symbols-outlined text-[18px] text-gray-400 group-hover/tech:text-indigo-600 transition-transform" id="iconDetallesTecnicos">expand_more</span>
                        <span>Ver detalles técnicos avanzados (JSON / Programadores)</span>
                    </button>

                    <div id="bloqueDetallesTecnicos" class="hidden mt-3 p-3.5 bg-gray-900 text-gray-100 rounded-2xl text-[11px] font-mono space-y-3">
                        <div class="flex items-center justify-between text-gray-400 text-[10px] pb-2 border-b border-gray-800 flex-wrap gap-2">
                            <span>Método: <strong class="text-sky-400" id="modalLogMethod">-</strong></span>
                            <span>URL: <span class="text-gray-300 break-all" id="modalLogUrl">-</span></span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-[10px]">
                            <div>
                                <span class="text-rose-400 font-bold block mb-1">JSON Valores Anteriores:</span>
                                <pre id="modalLogPrevJson" class="bg-black/50 p-2.5 rounded-xl border border-gray-800 overflow-x-auto text-rose-300 max-h-48 custom-scrollbar">{}</pre>
                            </div>
                            <div>
                                <span class="text-emerald-400 font-bold block mb-1">JSON Valores Nuevos:</span>
                                <pre id="modalLogNewJson" class="bg-black/50 p-2.5 rounded-xl border border-gray-800 overflow-x-auto text-emerald-300 max-h-48 custom-scrollbar">{}</pre>
                            </div>
                        </div>

                        <div class="text-[10px] text-gray-500 pt-1 border-t border-gray-800">
                            <span>User-Agent: </span>
                            <span id="modalLogUserAgent" class="text-gray-400 break-all">-</span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Footer Modal --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end shrink-0">
                <button type="button" onclick="cerrarModalLog()" class="px-5 py-2.5 rounded-xl font-bold text-gray-700 bg-white border border-gray-200 hover:bg-gray-100 transition-colors text-xs shadow-2xs">
                    Entendido, Cerrar
                </button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let searchTimeoutLogs = null;
    let currentLogsPage = 1;

    // EVENT LISTENERS DE FILTROS EN TIEMPO REAL
    const searchInput = document.getElementById('buscarLogInput');
    const filtroModulo = document.getElementById('filtroModulo');
    const filtroEvento = document.getElementById('filtroEvento');
    const filtroUsuario = document.getElementById('filtroUsuario');
    const filtroFechaDesde = document.getElementById('filtroFechaDesde');
    const filtroFechaHasta = document.getElementById('filtroFechaHasta');
    const spinner = document.getElementById('log_search_spinner');
    const btnClear = document.getElementById('btn_clear_log_search');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeoutLogs);
            if (spinner) spinner.style.display = 'inline-block';
            if (btnClear) btnClear.style.display = this.value ? 'flex' : 'none';

            searchTimeoutLogs = setTimeout(() => {
                fetchLogs(1);
            }, 300);
        });

        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                limpiarBuscadorLogs();
            }
        });
    }

    [filtroModulo, filtroEvento, filtroUsuario, filtroFechaDesde, filtroFechaHasta].forEach(el => {
        if (el) {
            el.addEventListener('change', () => fetchLogs(1));
        }
    });

    function limpiarBuscadorLogs() {
        if (searchInput) {
            searchInput.value = '';
            if (btnClear) btnClear.style.display = 'none';
            fetchLogs(1);
        }
    }

    function resetearTodosFiltros() {
        if (searchInput) searchInput.value = '';
        if (filtroModulo) filtroModulo.value = 'todos';
        if (filtroEvento) filtroEvento.value = 'todos';
        if (filtroUsuario) filtroUsuario.value = 'todos';
        if (filtroFechaDesde) filtroFechaDesde.value = '';
        if (filtroFechaHasta) filtroFechaHasta.value = '';
        if (btnClear) btnClear.style.display = 'none';
        fetchLogs(1);
    }

    // PETICIÓN AJAX PRINCIPAL CON ACTUALIZACIÓN DEL CONTENIDO
    function fetchLogs(page = 1) {
        currentLogsPage = page;
        const overlay = document.getElementById('loadingOverlayLogs');
        if (overlay) overlay.classList.remove('hidden');

        const params = new URLSearchParams();
        if (searchInput && searchInput.value.trim()) params.append('buscar', searchInput.value.trim());
        if (filtroModulo && filtroModulo.value !== 'todos') params.append('modulo', filtroModulo.value);
        if (filtroEvento && filtroEvento.value !== 'todos') params.append('evento', filtroEvento.value);
        if (filtroUsuario && filtroUsuario.value !== 'todos') params.append('usuario_id', filtroUsuario.value);
        if (filtroFechaDesde && filtroFechaDesde.value) params.append('fecha_desde', filtroFechaDesde.value);
        if (filtroFechaHasta && filtroFechaHasta.value) params.append('fecha_hasta', filtroFechaHasta.value);
        params.append('page', page);

        fetch(`{{ route('admin.logs.index') }}?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById('logsTableContainer').innerHTML = html;
        })
        .catch(err => {
            console.error('Error cargando logs:', err);
        })
        .finally(() => {
            if (overlay) overlay.classList.add('hidden');
            if (spinner) spinner.style.display = 'none';
        });
    }

    // PAGINACIÓN AJAX
    document.addEventListener('click', function(e) {
        const link = e.target.closest('#logsTableContainer .ajax-pagination a');
        if (link) {
            e.preventDefault();
            const page = new URL(link.href).searchParams.get('page') || 1;
            fetchLogs(page);
        }
    });

    // INSPECCIONAR DETALLE DE LA ACTIVIDAD (MODAL AMIGABLE)
    function inspeccionarLog(id) {
        fetch(`{{ url('admin/logs') }}/${id}`)
        .then(res => res.json())
        .then(data => {
            if (!data.success) throw new Error('No se pudo cargar el log');

            // Asignar textos amigables
            document.getElementById('modalLogTitle').innerText = data.modulo || 'Actividad';
            document.getElementById('modalLogSubtitle').innerText = `${data.fecha} • Hace ${data.fecha_relativa}`;
            document.getElementById('modalLogBadge').innerText = data.evento_amigable || data.evento;
            document.getElementById('modalLogBadge').className = `text-[11px] font-black uppercase px-2.5 py-0.5 rounded-lg border ${data.badge_color}`;
            document.getElementById('modalLogIcon').innerText = data.icono;
            document.getElementById('modalLogDesc').innerText = data.descripcion || 'Acción registrada en el sistema.';

            // Usuario
            if (data.usuario) {
                document.getElementById('modalLogUserAvatar').innerText = data.usuario.nombre.charAt(0).toUpperCase();
                document.getElementById('modalLogUserName').innerText = data.usuario.nombre;
                document.getElementById('modalLogUserEmail').innerText = data.usuario.email;
                document.getElementById('modalLogUserRole').innerText = `Rol: ${data.usuario.rol}`;
            } else {
                document.getElementById('modalLogUserAvatar').innerText = 'S';
                document.getElementById('modalLogUserName').innerText = 'Acción Automática del Sistema';
                document.getElementById('modalLogUserEmail').innerText = 'Proceso Interno';
                document.getElementById('modalLogUserRole').innerText = 'Sin operador humano';
            }

            // Dónde y desde qué equipo
            document.getElementById('modalLogPantalla').innerText = data.pantalla_amigable || data.modulo;
            document.getElementById('modalLogDispositivo').innerText = data.dispositivo_amigable || 'Dispositivo no identificado';
            document.getElementById('modalLogIp').innerText = data.ip ? `${data.ip} (Conexión registrada)` : '127.0.0.1';

            // Datos técnicos para programadores
            document.getElementById('modalLogMethod').innerText = data.metodo_http || 'GET';
            document.getElementById('modalLogUrl').innerText = data.url || '-';
            document.getElementById('modalLogUserAgent').innerText = data.user_agent || '-';

            // LISTA AMIGABLE DE CAMBIOS (Visual y Criolla)
            const cambiosLista = document.getElementById('modalLogCambiosLista');
            cambiosLista.innerHTML = '';
            
            const cambios = data.cambios_amigables || [];
            document.getElementById('modalLogCambiosConteo').innerText = `${cambios.length} ${cambios.length === 1 ? 'dato modificado' : 'datos modificados'}`;

            if (cambios.length > 0) {
                cambios.forEach(item => {
                    const row = document.createElement('div');
                    row.className = 'p-3 rounded-2xl border border-gray-100 bg-gray-50/70 transition-all hover:bg-white hover:shadow-xs';

                    if (item.tipo === 'modificado') {
                        row.innerHTML = `
                            <div class="flex items-center justify-between mb-1.5 flex-wrap gap-1">
                                <span class="text-xs font-black text-gray-900 flex items-center gap-1.5">
                                    <span class="size-2 rounded-full bg-indigo-500"></span>
                                    ${item.nombre}
                                </span>
                                <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100">Modificado</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-1">
                                <div class="p-2 rounded-xl bg-rose-50 border border-rose-100 text-xs">
                                    <span class="text-[9px] font-black text-rose-800 uppercase tracking-wider block mb-0.5">Estaba en:</span>
                                    <span class="font-bold text-rose-950 break-words">${item.antes ?? '(Vacío)'}</span>
                                </div>
                                <div class="p-2 rounded-xl bg-emerald-50 border border-emerald-100 text-xs">
                                    <span class="text-[9px] font-black text-emerald-800 uppercase tracking-wider block mb-0.5">Ahora quedó en:</span>
                                    <span class="font-bold text-emerald-950 break-words">${item.despues ?? '(Vacío)'}</span>
                                </div>
                            </div>
                        `;
                    } else if (item.tipo === 'nuevo') {
                        row.innerHTML = `
                            <div class="flex items-center justify-between mb-1.5 flex-wrap gap-1">
                                <span class="text-xs font-black text-gray-900 flex items-center gap-1.5">
                                    <span class="size-2 rounded-full bg-emerald-500"></span>
                                    ${item.nombre}
                                </span>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">Dato Registrado</span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-emerald-50/80 border border-emerald-100 text-xs">
                                <span class="text-[9px] font-black text-emerald-800 uppercase tracking-wider block mb-0.5">Valor asignado:</span>
                                <span class="font-bold text-emerald-950 break-words">${item.despues ?? '(Vacío)'}</span>
                            </div>
                        `;
                    } else { // eliminado
                        row.innerHTML = `
                            <div class="flex items-center justify-between mb-1.5 flex-wrap gap-1">
                                <span class="text-xs font-black text-gray-900 flex items-center gap-1.5">
                                    <span class="size-2 rounded-full bg-rose-500"></span>
                                    ${item.nombre}
                                </span>
                                <span class="text-[10px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-100">Dato Retirado</span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-rose-50/80 border border-rose-100 text-xs">
                                <span class="text-[9px] font-black text-rose-800 uppercase tracking-wider block mb-0.5">Valor que tenía:</span>
                                <span class="font-bold text-rose-950 break-words">${item.antes ?? '(Vacío)'}</span>
                            </div>
                        `;
                    }
                    cambiosLista.appendChild(row);
                });
            } else {
                cambiosLista.innerHTML = `
                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 text-center">
                        <span class="material-symbols-outlined text-2xl text-gray-400 block mb-1">done_all</span>
                        <p class="text-xs font-bold text-gray-700">Esta acción no cambió datos numéricos ni textos individuales.</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">Se trató de un evento general (como un inicio de sesión o consulta en pantalla).</p>
                    </div>
                `;
            }

            // JSON Raw para sección colapsable técnica
            const prev = data.valores_anteriores;
            const nuevo = data.valores_nuevos;
            document.getElementById('modalLogPrevJson').innerText = prev ? JSON.stringify(prev, null, 2) : 'No aplica (Sin datos previos)';
            document.getElementById('modalLogNewJson').innerText = nuevo ? JSON.stringify(nuevo, null, 2) : 'No aplica (Sin datos nuevos)';

            // Mostrar Modal con animación
            const modal = document.getElementById('modalDetalleLog');
            const backdrop = document.getElementById('backdropLog');
            const panel = document.getElementById('panelLog');

            modal.classList.remove('hidden');
            setTimeout(() => {
                backdrop.classList.remove('opacity-0');
                panel.classList.remove('opacity-0', 'scale-95');
                panel.classList.add('opacity-100', 'scale-100');
            }, 10);
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No fue posible consultar el registro solicitado.'
            });
        });
    }

    function toggleDetallesTecnicos() {
        const bloque = document.getElementById('bloqueDetallesTecnicos');
        const icono = document.getElementById('iconDetallesTecnicos');
        if (bloque) {
            const estaOculto = bloque.classList.contains('hidden');
            if (estaOculto) {
                bloque.classList.remove('hidden');
                icono.classList.add('rotate-180');
            } else {
                bloque.classList.add('hidden');
                icono.classList.remove('rotate-180');
            }
        }
    }

    function cerrarModalLog() {
        const modal = document.getElementById('modalDetalleLog');
        const backdrop = document.getElementById('backdropLog');
        const panel = document.getElementById('panelLog');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 250);
    }

    // EXPORTAR CSV
    function exportarLogsCSV() {
        const params = new URLSearchParams();
        if (searchInput && searchInput.value.trim()) params.append('buscar', searchInput.value.trim());
        if (filtroModulo && filtroModulo.value !== 'todos') params.append('modulo', filtroModulo.value);
        if (filtroEvento && filtroEvento.value !== 'todos') params.append('evento', filtroEvento.value);
        if (filtroUsuario && filtroUsuario.value !== 'todos') params.append('usuario_id', filtroUsuario.value);
        if (filtroFechaDesde && filtroFechaDesde.value) params.append('fecha_desde', filtroFechaDesde.value);
        if (filtroFechaHasta && filtroFechaHasta.value) params.append('fecha_hasta', filtroFechaHasta.value);

        window.location.href = `{{ route('admin.logs.exportar') }}?${params.toString()}`;
    }
</script>
@endpush
