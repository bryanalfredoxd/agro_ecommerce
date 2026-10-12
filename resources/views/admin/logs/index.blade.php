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
                        Logs y Auditoría Forense
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Trazabilidad exhaustiva de cualquier cambio en la base de datos, eventos de seguridad y acciones operativas.
                    </p>
                </div>
                
                {{-- Botones de Acción --}}
                <div class="flex items-center gap-2.5 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold shadow-2xs">
                        <span class="material-symbols-outlined text-[16px] text-emerald-600">verified_user</span>
                        <span>Auditoría Inmutable</span>
                    </span>

                    {{-- Exportar CSV --}}
                    <button type="button" 
                            onclick="exportarLogsCSV()" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white hover:bg-gray-50 text-gray-700 hover:text-indigo-600 font-bold text-xs border border-gray-200 transition-all shadow-2xs hover:scale-105 active:scale-95">
                        <span class="material-symbols-outlined text-[18px] text-indigo-600">download</span>
                        <span>Exportar CSV</span>
                    </button>
                </div>
            </div>

            {{-- Tarjetas KPI de Auditoría --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                
                {{-- KPI 1: Total Eventos --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Histórico Total</span>
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
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Eventos de Hoy</span>
                        <div class="size-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">today</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-black text-emerald-600 leading-none">{{ number_format($logsHoy) }}</span>
                        <span class="text-[10px] text-emerald-700 font-bold uppercase">En 24h</span>
                    </div>
                </div>

                {{-- KPI 3: Usuarios Activos Auditados --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Personal Activo Hoy</span>
                        <div class="size-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">group</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-black text-gray-900 leading-none">{{ $usuariosAuditadosHoy }}</span>
                        <span class="text-[10px] text-sky-600 font-bold uppercase">Operadores</span>
                    </div>
                </div>

                {{-- KPI 4: Acciones Críticas / Seguridad --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Acciones Críticas</span>
                        <div class="size-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">security</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-black text-rose-600 leading-none">{{ number_format($accionesCriticas) }}</span>
                        <span class="text-[10px] text-rose-700 font-bold uppercase">Auditadas</span>
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
                               placeholder="Buscar por descripción, tabla, ID de registro, IP, usuario o evento...">
                        
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

                    {{-- 2. Selector Módulo / Tabla --}}
                    <div class="w-full sm:w-auto lg:w-48 flex items-center gap-2 px-3 py-2 bg-gray-50/60 border border-gray-200 rounded-xl transition-all focus-within:bg-white focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500">
                        <span class="material-symbols-outlined text-gray-400 text-[18px] shrink-0 pointer-events-none">folder_open</span>
                        <select id="filtroModulo" class="w-full bg-transparent border-none p-0 outline-none focus:ring-0 text-xs font-bold text-gray-700 cursor-pointer">
                            <option value="todos" {{ request('modulo') === 'todos' ? 'selected' : '' }}>Todos los Módulos</option>
                            @foreach($modulosDisponibles as $mod)
                                <option value="{{ $mod }}" {{ request('modulo') === $mod ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $mod)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 3. Selector Tipo de Evento --}}
                    <div class="w-full sm:w-auto lg:w-48 flex items-center gap-2 px-3 py-2 bg-gray-50/60 border border-gray-200 rounded-xl transition-all focus-within:bg-white focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500">
                        <span class="material-symbols-outlined text-gray-400 text-[18px] shrink-0 pointer-events-none">tune</span>
                        <select id="filtroEvento" class="w-full bg-transparent border-none p-0 outline-none focus:ring-0 text-xs font-bold text-gray-700 cursor-pointer">
                            <option value="todos" {{ request('evento') === 'todos' ? 'selected' : '' }}>Todas las Acciones</option>
                            <option value="CREAR" {{ request('evento') === 'CREAR' ? 'selected' : '' }}>Creaciones / Inserción</option>
                            <option value="ACTUALIZAR" {{ request('evento') === 'ACTUALIZAR' ? 'selected' : '' }}>Modificaciones / Edición</option>
                            <option value="ELIMINAR" {{ request('evento') === 'ELIMINAR' ? 'selected' : '' }}>Eliminaciones / Purga</option>
                            <option value="SEGURIDAD" {{ request('evento') === 'SEGURIDAD' ? 'selected' : '' }}>Seguridad, Roles & Login</option>
                            <option value="ESTADO" {{ request('evento') === 'ESTADO' ? 'selected' : '' }}>Cambios de Estado</option>
                        </select>
                    </div>

                    {{-- 4. Selector Usuario Responsable --}}
                    <div class="w-full sm:w-auto lg:w-48 flex items-center gap-2 px-3 py-2 bg-gray-50/60 border border-gray-200 rounded-xl transition-all focus-within:bg-white focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500">
                        <span class="material-symbols-outlined text-gray-400 text-[18px] shrink-0 pointer-events-none">person</span>
                        <select id="filtroUsuario" class="w-full bg-transparent border-none p-0 outline-none focus:ring-0 text-xs font-bold text-gray-700 cursor-pointer">
                            <option value="todos" {{ request('usuario_id') === 'todos' ? 'selected' : '' }}>Cualquier Usuario</option>
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
                            <span class="material-symbols-outlined text-[16px]">calendar_month</span> Fecha:
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
                        <span>Limpiar Filtros</span>
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
{{-- MODAL DE INSPECCIÓN FORENSE DETALLADA (SIDE-BY-SIDE DIFF)                --}}
{{-- ========================================================================= --}}
<div id="modalDetalleLog" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="backdropLog" onclick="cerrarModalLog()"></div>

    <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-4xl opacity-0 scale-95 flex flex-col max-h-[92vh]" id="panelLog">
            
            {{-- Encabezado Modal --}}
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-white shrink-0">
                <div class="flex items-center gap-3">
                    <div id="modalLogIconWrap" class="size-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <span id="modalLogIcon" class="material-symbols-outlined text-[22px]">manage_search</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-black text-gray-900" id="modalLogTitle">Inspección de Auditoría</h3>
                            <span id="modalLogBadge" class="text-[10px] font-black uppercase px-2 py-0.5 rounded-md border">EVENTO</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5" id="modalLogSubtitle">Registro #0 &bull; Fecha</p>
                    </div>
                </div>

                <button type="button" onclick="cerrarModalLog()" class="size-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-800 flex items-center justify-center transition-colors">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            {{-- Cuerpo Modal Scrollable --}}
            <div class="px-6 py-5 overflow-y-auto custom-scrollbar flex-1 space-y-5">
                
                {{-- Banner Descripción --}}
                <div class="p-3.5 bg-gray-50 rounded-2xl border border-gray-200/80">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Descripción del Cambio</span>
                    <p class="text-sm font-semibold text-gray-900" id="modalLogDesc">-</p>
                </div>

                {{-- Metadatos del Usuario y Contexto de Petición --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    {{-- Tarjeta 1: Usuario Responsable --}}
                    <div class="p-4 bg-slate-50/70 rounded-2xl border border-slate-200/70 space-y-2">
                        <span class="text-[10px] font-black text-indigo-700 uppercase tracking-wider flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">person</span> Autor de la Acción
                        </span>
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-xl bg-slate-900 text-white font-black text-sm flex items-center justify-center shrink-0" id="modalLogUserAvatar">
                                U
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="text-xs font-black text-gray-900 truncate" id="modalLogUserName">Usuario Anónimo</span>
                                <span class="text-[11px] text-gray-500 truncate" id="modalLogUserEmail">-</span>
                                <span class="text-[10px] font-bold text-indigo-600 mt-0.5" id="modalLogUserRole">Rol</span>
                            </div>
                        </div>
                    </div>

                    {{-- Tarjeta 2: Red y Dispositivo --}}
                    <div class="p-4 bg-slate-50/70 rounded-2xl border border-slate-200/70 space-y-2 text-xs">
                        <span class="text-[10px] font-black text-indigo-700 uppercase tracking-wider flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">public</span> Contexto de Conexión
                        </span>
                        <div class="space-y-1 font-mono text-[11px]">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Dirección IP:</span>
                                <span class="font-bold text-gray-800" id="modalLogIp">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Método HTTP:</span>
                                <span class="font-bold text-indigo-600" id="modalLogMethod">-</span>
                            </div>
                            <div class="flex justify-between gap-2">
                                <span class="text-gray-400 shrink-0">Ruta URL:</span>
                                <span class="font-medium text-gray-700 truncate text-right max-w-[220px]" id="modalLogUrl" title="">-</span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Visor Side-by-Side: Valores Anteriores vs Nuevos --}}
                <div class="space-y-2">
                    <span class="text-[11px] font-black text-gray-600 uppercase tracking-wider flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px] text-indigo-600">compare</span> Comparativa de Valores en Base de Datos
                    </span>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        
                        {{-- Columna Izquierda: Valores Anteriores --}}
                        <div class="bg-rose-50/40 rounded-2xl border border-rose-200/80 p-3.5 flex flex-col">
                            <div class="flex items-center justify-between pb-2 mb-2 border-b border-rose-200/60">
                                <span class="text-xs font-black text-rose-800 flex items-center gap-1">
                                    <span class="size-2 rounded-full bg-rose-500"></span> Valores Anteriores (Pre-modificación)
                                </span>
                                <span class="text-[10px] font-mono text-rose-600 font-bold" id="modalLogPrevCount">0 campos</span>
                            </div>
                            <pre id="modalLogPrevJson" class="text-[11px] font-mono bg-white p-3 rounded-xl border border-rose-100 overflow-x-auto text-rose-950 flex-1 max-h-64 custom-scrollbar">Sin valores previos</pre>
                        </div>

                        {{-- Columna Derecha: Valores Nuevos --}}
                        <div class="bg-emerald-50/40 rounded-2xl border border-emerald-200/80 p-3.5 flex flex-col">
                            <div class="flex items-center justify-between pb-2 mb-2 border-b border-emerald-200/60">
                                <span class="text-xs font-black text-emerald-800 flex items-center gap-1">
                                    <span class="size-2 rounded-full bg-emerald-500"></span> Valores Nuevos (Post-modificación)
                                </span>
                                <span class="text-[10px] font-mono text-emerald-600 font-bold" id="modalLogNewCount">0 campos</span>
                            </div>
                            <pre id="modalLogNewJson" class="text-[11px] font-mono bg-white p-3 rounded-xl border border-emerald-100 overflow-x-auto text-emerald-950 flex-1 max-h-64 custom-scrollbar">Sin valores nuevos</pre>
                        </div>

                    </div>
                </div>

                {{-- User Agent --}}
                <div class="p-3 bg-gray-50 rounded-xl border border-gray-200/60 text-[10px] text-gray-500 font-mono">
                    <span class="font-bold text-gray-700">Dispositivo / User-Agent:</span>
                    <span id="modalLogUserAgent" class="block mt-0.5 break-all">-</span>
                </div>

            </div>

            {{-- Footer Modal --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end shrink-0">
                <button type="button" onclick="cerrarModalLog()" class="px-5 py-2.5 rounded-xl font-bold text-gray-700 bg-white border border-gray-200 hover:bg-gray-100 transition-colors text-xs">
                    Cerrar Detalle
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

    // INSPECCIONAR DETALLE FORENSE (MODAL)
    function inspeccionarLog(id) {
        fetch(`{{ url('admin/logs') }}/${id}`)
        .then(res => res.json())
        .then(data => {
            if (!data.success) throw new Error('No se pudo cargar el log');

            // Asignar textos
            document.getElementById('modalLogTitle').innerText = data.modulo;
            document.getElementById('modalLogSubtitle').innerText = `ID Registro #${data.id} • Tabla '${data.tabla}' • ${data.fecha} (${data.fecha_relativa})`;
            document.getElementById('modalLogBadge').innerText = data.evento;
            document.getElementById('modalLogBadge').className = `text-[10px] font-black uppercase px-2 py-0.5 rounded-md border ${data.badge_color}`;
            document.getElementById('modalLogIcon').innerText = data.icono;
            document.getElementById('modalLogDesc').innerText = data.descripcion || 'Acción en base de datos';

            // Usuario
            if (data.usuario) {
                document.getElementById('modalLogUserAvatar').innerText = data.usuario.nombre.charAt(0).toUpperCase();
                document.getElementById('modalLogUserName').innerText = data.usuario.nombre;
                document.getElementById('modalLogUserEmail').innerText = data.usuario.email;
                document.getElementById('modalLogUserRole').innerText = `Rol: ${data.usuario.rol}`;
            } else {
                document.getElementById('modalLogUserAvatar').innerText = 'S';
                document.getElementById('modalLogUserName').innerText = 'Sistema / Anónimo';
                document.getElementById('modalLogUserEmail').innerText = 'Proceso Interno / CLI';
                document.getElementById('modalLogUserRole').innerText = 'Sin usuario de sesión';
            }

            // Red
            document.getElementById('modalLogIp').innerText = data.ip || '127.0.0.1';
            document.getElementById('modalLogMethod').innerText = `[${data.metodo_http || 'GET'}]`;
            document.getElementById('modalLogUrl').innerText = data.url || '-';
            document.getElementById('modalLogUrl').title = data.url || '';
            document.getElementById('modalLogUserAgent').innerText = data.user_agent || '-';

            // Diff Formateado JSON
            const prev = data.valores_anteriores;
            const nuevo = data.valores_nuevos;

            const prevElem = document.getElementById('modalLogPrevJson');
            const newElem = document.getElementById('modalLogNewJson');

            if (prev && Object.keys(prev).length > 0) {
                document.getElementById('modalLogPrevCount').innerText = `${Object.keys(prev).length} campos`;
                prevElem.innerText = JSON.stringify(prev, null, 2);
            } else {
                document.getElementById('modalLogPrevCount').innerText = `0 campos`;
                prevElem.innerText = 'No aplica (Creación nueva o sin valores previos)';
            }

            if (nuevo && Object.keys(nuevo).length > 0) {
                document.getElementById('modalLogNewCount').innerText = `${Object.keys(nuevo).length} campos`;
                newElem.innerText = JSON.stringify(nuevo, null, 2);
            } else {
                document.getElementById('modalLogNewCount').innerText = `0 campos`;
                newElem.innerText = 'No aplica (Eliminación o sin cambios nuevos)';
            }

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
                text: 'No fue posible consultar el registro de auditoría solicitado.'
            });
        });
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
