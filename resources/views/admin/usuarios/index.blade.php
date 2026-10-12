@extends('layouts.admin')

@section('title', 'Listado de Usuarios - Agrosuministros Rodríguez')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col">
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up pb-20">
            
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-2xl font-black text-agro-dark flex items-center gap-2">
                        <span class="material-symbols-outlined text-indigo-600 text-[32px]">group</span>
                        Directorio de Usuarios
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Busca clientes, administra tu equipo y asigna permisos excepcionales.</p>
                </div>
            </div>

            {{-- CONTROLES Y FILTROS --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-2 mb-6">
                
                {{-- Tabs de Roles --}}
                <div class="flex overflow-x-auto custom-scrollbar border-b border-gray-100 px-4 pt-2 gap-6">
                    <button class="role-tab active pb-3 text-sm font-black border-b-2 border-primary text-agro-dark whitespace-nowrap" data-rol="all">Todos</button>
                    @foreach($roles as $rol)
                        <button class="role-tab pb-3 text-sm font-bold border-b-2 border-transparent text-gray-700 hover:text-gray-700 whitespace-nowrap" data-rol="{{ $rol->id }}">{{ $rol->nombre }}</button>
                    @endforeach
                </div>

                {{-- Buscador y Selects --}}
                <div class="p-4 flex flex-col sm:flex-row gap-4 bg-gray-50/50 rounded-b-2xl">
                    <div class="flex-1 relative">
                        <span class="material-symbols-outlined absolute left-3 top-3 text-gray-700">search</span>
                        <input type="text" id="searchInput" class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-medium text-agro-dark" placeholder="Buscar por correo, CI, nombre...">
                    </div>
                    <div class="w-full sm:w-48 relative">
                        <select id="sortSelect" class="w-full pl-4 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-bold text-gray-600 appearance-none cursor-pointer">
                            <option value="newest">Más Recientes</option>
                            <option value="oldest">Más Antiguos</option>
                            <option value="az">Alfabético (A-Z)</option>
                            <option value="za">Alfabético (Z-A)</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-3 text-gray-700 pointer-events-none">sort</span>
                    </div>
                </div>
            </div>

            {{-- CONTENEDOR DE LA TABLA (Aquí se inyecta el AJAX) --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 relative" id="tableContainer">
                <div id="loadingOverlay" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-20 hidden items-center justify-center rounded-3xl">
                    <span class="material-symbols-outlined animate-spin text-primary text-4xl">autorenew</span>
                </div>
                
                <div id="tableContent">
                    @include('admin.usuarios.partials._table')
                </div>
            </div>

        </div>
    </main>
</div>

{{-- MODAL DE PERMISOS EXTRA --}}
<div id="permisosModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    {{-- Fondo Oscuro --}}
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="permisosBackdrop" onclick="closePermisosModal()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center sm:items-center p-0 sm:p-4">
            {{-- Panel del Modal --}}
            <div class="relative transform overflow-hidden rounded-t-3xl sm:rounded-3xl bg-white text-left shadow-2xl transition-all w-full sm:max-w-4xl opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95 flex flex-col max-h-[90vh]" id="permisosPanel">
                
                {{-- Header del Modal --}}
                <div class="bg-white px-6 py-5 border-b border-gray-100 flex justify-between items-center z-10 flex-shrink-0">
                    <div>
                        <h3 class="text-xl font-black text-agro-dark" id="modalTitle">Configuración de Accesos</h3>
                        <p class="text-xs text-indigo-600 font-bold mt-1" id="modalUserName">Cargando...</p>
                    </div>
                    <button type="button" onclick="closePermisosModal()" class="text-gray-700 hover:text-red-500 transition-colors p-1 bg-gray-50 rounded-lg border border-gray-200">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                {{-- Formulario --}}
                <form id="permisosForm" onsubmit="savePermisos(event)" class="flex flex-col flex-1 overflow-hidden min-h-0">
                    <input type="hidden" id="usuario_id">
                    
                    <div class="px-6 py-6 overflow-y-auto custom-scrollbar flex-1 space-y-6">
                        
                        {{-- SECCIÓN 1: ROL Y HERENCIA --}}
                        <div class="bg-gray-50/80 p-5 rounded-2xl border border-gray-100 flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
                            <div class="flex-1 w-full">
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-0.5">Rol Principal del Usuario</label>
                                    <span id="roleInheritanceHint" class="text-[11px] font-bold text-indigo-600">Hereda los permisos estándar</span>
                                </div>
                                <select name="rol_id" id="modalRolSelect" onchange="onUserRoleChanged()" class="w-full h-11 rounded-xl bg-white border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all px-4 font-bold text-agro-dark outline-none cursor-pointer text-sm">
                                    <option value="">Sin Rol Asignado</option>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id }}">{{ $rol->nombre }} ({{ $rol->permisos->count() }} permisos predeterminados)</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Botón para Restablecer al Rol 100% --}}
                            <div class="shrink-0 md:self-end">
                                <button type="button" onclick="restablecerPermisosAlRol()" class="px-4 py-2.5 bg-white hover:bg-indigo-50 text-indigo-700 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 border border-indigo-200 shadow-2xs hover:scale-102 active:scale-98" title="Elimina cualquier excepción personalizada y sincroniza exactamente con el rol">
                                    <span class="material-symbols-outlined text-[17px]">restart_alt</span> Restablecer al Rol
                                </button>
                            </div>
                        </div>

                        {{-- Mensaje de Explicación Intuitiva --}}
                        <div class="p-3.5 rounded-2xl bg-indigo-50/60 border border-indigo-100 text-xs text-indigo-900 flex items-start gap-2.5">
                            <span class="material-symbols-outlined text-[20px] text-indigo-600 shrink-0 mt-0.5">info</span>
                            <div class="flex-1 leading-relaxed">
                                <p class="font-bold">¿Cómo funcionan los permisos de este usuario?</p>
                                <div class="flex flex-wrap gap-2 mt-1.5 text-[10px] font-semibold">
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 border border-emerald-200 inline-flex items-center gap-1">
                                        <span class="size-1.5 rounded-full bg-emerald-500"></span> Heredado del Rol
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 border border-blue-200 inline-flex items-center gap-1">
                                        <span class="size-1.5 rounded-full bg-blue-500"></span> Excepción: Concedido Extra
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md bg-rose-100 text-rose-800 border border-rose-200 inline-flex items-center gap-1">
                                        <span class="size-1.5 rounded-full bg-rose-500"></span> Excepción: Revocado / Bloqueado
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN 2: BUSCADOR Y ESTADÍSTICAS --}}
                        <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between pt-2 border-t border-gray-100">
                            {{-- Buscador en Vivo --}}
                            <div class="relative flex-1">
                                <span class="material-symbols-outlined absolute left-3.5 top-2.5 text-gray-400 text-[18px]">search</span>
                                <input type="text" id="userPermissionSearch" oninput="filtrarPermisosUsuario(this.value)" placeholder="Buscar permiso por nombre, módulo o función..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
                            </div>

                            {{-- Contador en tiempo real --}}
                            <div class="shrink-0">
                                <span id="userPermCounter" class="text-xs font-black text-indigo-700 font-mono bg-indigo-50 px-3 py-1.5 rounded-xl border border-indigo-100 inline-block">
                                    0 permisos activos
                                </span>
                            </div>
                        </div>

                        {{-- SECCIÓN 3: CUADRÍCULA DE PERMISOS POR MÓDULOS --}}
                        <div class="space-y-4" id="userModulesContainer">
                            @foreach($modulos as $modulo)
                            <div class="user-module-card bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-2xs transition-all hover:border-gray-300" data-module-id="{{ $modulo['id'] }}">
                                {{-- Cabecera del Módulo --}}
                                <div class="px-5 py-3.5 bg-gray-50/80 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3 select-none">
                                    <div class="flex items-center gap-3">
                                        <div class="size-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-[18px]">{{ $modulo['icono'] }}</span>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h4 class="text-xs font-black text-gray-900 tracking-tight">{{ $modulo['nombre'] }}</h4>
                                                <span class="user-module-counter text-[10px] font-bold px-2 py-0.5 rounded-full bg-white border border-gray-200 text-gray-600 font-mono">
                                                    0/{{ count($modulo['permisos']) }}
                                                </span>
                                            </div>
                                            <p class="text-[11px] text-gray-500 font-normal leading-tight">{{ $modulo['descripcion'] }}</p>
                                        </div>
                                    </div>

                                    {{-- Botones de Acción por Módulo --}}
                                    <div class="flex items-center gap-1.5 ml-auto">
                                        <button type="button" onclick="marcarModuloUsuario('{{ $modulo['id'] }}', true)" class="px-2 py-1 bg-white hover:bg-indigo-50 text-[10px] font-bold text-gray-600 hover:text-indigo-600 rounded-lg border border-gray-200 transition-colors">
                                            Marcar módulo
                                        </button>
                                        <button type="button" onclick="marcarModuloUsuario('{{ $modulo['id'] }}', false)" class="px-2 py-1 bg-white hover:bg-gray-100 text-[10px] font-bold text-gray-600 rounded-lg border border-gray-200 transition-colors">
                                            Desmarcar
                                        </button>
                                    </div>
                                </div>

                                {{-- Grilla de Permisos del Módulo --}}
                                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-3 bg-white">
                                    @foreach($modulo['permisos'] as $key => $pInfo)
                                        @php
                                            $permisoObj = $permisos[$key] ?? null;
                                        @endphp
                                        @if($permisoObj)
                                        <label class="user-permiso-item relative flex items-start p-3 rounded-xl border border-gray-150 bg-gray-50/40 hover:bg-indigo-50/30 hover:border-indigo-200 transition-all cursor-pointer group" data-perm-name="{{ strtolower($pInfo['nombre'] . ' ' . $key . ' ' . $pInfo['descripcion']) }}">
                                            <div class="flex items-center h-5 mt-0.5">
                                                <input type="checkbox" name="permisos[]" value="{{ $permisoObj->id }}" onchange="onUserPermisoCheckboxChanged()" class="user-permiso-checkbox size-4 rounded text-indigo-600 focus:ring-indigo-500 border-gray-300 transition-colors cursor-pointer" data-permiso-id="{{ $permisoObj->id }}" data-module="{{ $modulo['id'] }}">
                                            </div>
                                            <div class="ml-3 flex-1 min-w-0">
                                                <div class="flex items-center justify-between gap-1.5 mb-1">
                                                    <span class="text-xs font-bold text-gray-900 truncate group-hover:text-indigo-900 transition-colors">
                                                        {{ $pInfo['nombre'] }}
                                                    </span>
                                                    <span class="text-[9px] font-black uppercase px-1.5 py-0.2 rounded border {{ $pInfo['badge_color'] }} shrink-0">
                                                        {{ $pInfo['badge'] }}
                                                    </span>
                                                </div>
                                                <p class="text-[11px] text-gray-500 leading-snug">
                                                    {{ $pInfo['descripcion'] }}
                                                </p>
                                                
                                                {{-- Badge Dinámico de Estado de Herencia / Excepción --}}
                                                <div class="mt-2 flex items-center justify-between">
                                                    <span class="text-[9px] font-mono text-gray-400">
                                                        {{ $key }}
                                                    </span>
                                                    <span id="perm_status_{{ $permisoObj->id }}" class="perm-status-indicator text-[10px] font-black px-2 py-0.5 rounded-md leading-none">
                                                        ...
                                                    </span>
                                                </div>
                                            </div>
                                        </label>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>

                    </div>

                    {{-- Footer / Botones --}}
                    <div class="bg-gray-50 px-4 sm:px-6 py-3.5 sm:py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 flex-shrink-0">
                        <span class="text-xs text-gray-500 font-medium hidden sm:inline">
                            Los cambios se aplican de inmediato en la sesión del usuario.
                        </span>
                        <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto sm:ml-auto">
                            <button type="button" onclick="closePermisosModal()" class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 transition-colors text-sm text-center">Cancelar</button>
                            <button type="submit" id="btnSave" class="w-full sm:w-auto px-6 py-2.5 rounded-xl font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-sm text-sm flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">save</span> Guardar Configuración
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- CONTENEDOR PARA NOTIFICACIONES TOAST (Generadas por JS) --}}
<div id="toast-container" class="fixed bottom-6 right-6 z-[999] flex flex-col gap-2"></div>

@push('scripts')
<script>
    // ESTADO PARA AJAX Y TABLA
    let currentRole = 'all';
    let currentSearch = '';
    let currentSort = 'newest';
    let searchTimeout;

    // ESTADO PARA EL MODAL DE PERMISOS DEL USUARIO
    let currentUserData = {
        usuario_id: null,
        rol_id: null,
        roles: [],
        permisos_del_rol: [],
        excepciones: {}
    };

    // EVENTOS DE LOS CONTROLES DE TABLA
    document.querySelectorAll('.role-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.role-tab').forEach(t => {
                t.classList.remove('border-primary', 'text-agro-dark', 'active');
                t.classList.add('border-transparent', 'text-gray-700');
            });
            this.classList.remove('border-transparent', 'text-gray-700');
            this.classList.add('border-primary', 'text-agro-dark', 'active');
            
            currentRole = this.getAttribute('data-rol');
            fetchData(1);
        });
    });

    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        currentSearch = this.value;
        searchTimeout = setTimeout(() => fetchData(1), 400);
    });

    document.getElementById('sortSelect').addEventListener('change', function() {
        currentSort = this.value;
        fetchData(1);
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.ajax-pagination a')) {
            e.preventDefault();
            const url = new URL(e.target.closest('a').href);
            const page = url.searchParams.get('page');
            fetchData(page);
        }
    });

    function fetchData(page = 1) {
        const loading = document.getElementById('loadingOverlay');
        loading.classList.remove('hidden');
        loading.classList.add('flex');

        const params = new URLSearchParams({
            rol_id: currentRole,
            buscar: currentSearch,
            orden: currentSort,
            page: page
        });

        fetch(`{{ route('admin.usuarios.index') }}?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            document.getElementById('tableContent').innerHTML = html;
        })
        .finally(() => {
            loading.classList.add('hidden');
            loading.classList.remove('flex');
        });
    }

    // ==========================================
    // LÓGICA INTELIGENTE DEL MODAL DE PERMISOS
    // ==========================================
    function openPermisosModal(id, nombre) {
        document.getElementById('usuario_id').value = id;
        document.getElementById('modalUserName').innerText = 'Personal / Usuario: ' + nombre;
        
        // Resetear buscador
        const searchInput = document.getElementById('userPermissionSearch');
        if (searchInput) {
            searchInput.value = '';
            filtrarPermisosUsuario('');
        }

        fetch(`/admin/usuarios/${id}/permisos-extra`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            currentUserData = data;
            
            // Asignar el rol en el selector
            const rolSelect = document.getElementById('modalRolSelect');
            rolSelect.value = data.rol_id ? String(data.rol_id) : "";

            // Renderizar el estado de cada casilla y badge
            actualizarEstadoPermisosUsuario();

            // Animación de apertura
            const modal = document.getElementById('permisosModal');
            const backdrop = document.getElementById('permisosBackdrop');
            const panel = document.getElementById('permisosPanel');
            modal.classList.remove('hidden');
            setTimeout(() => {
                backdrop.classList.remove('opacity-0');
                panel.classList.remove('opacity-0', 'scale-95', 'translate-y-8', 'sm:translate-y-0');
                panel.classList.add('opacity-100', 'scale-100');
            }, 10);
        });
    }

    function closePermisosModal() {
        const modal = document.getElementById('permisosModal');
        const backdrop = document.getElementById('permisosBackdrop');
        const panel = document.getElementById('permisosPanel');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95', 'translate-y-8', 'sm:translate-y-0');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    // Al cambiar de rol en el select: recalculamos los permisos base del rol
    function onUserRoleChanged() {
        const rolSelect = document.getElementById('modalRolSelect');
        const nuevoRolId = parseInt(rolSelect.value);
        
        currentUserData.rol_id = nuevoRolId;
        const rolEncontrado = currentUserData.roles.find(r => r.id === nuevoRolId);
        currentUserData.permisos_del_rol = rolEncontrado ? rolEncontrado.permiso_ids : [];

        // Al cambiar de rol, restablecemos las casillas a las del nuevo rol
        currentUserData.excepciones = {};
        actualizarEstadoPermisosUsuario(true);
        showToast('Permisos sincronizados con el nuevo rol seleccionado.', 'info');
    }

    // Botón: Restablecer al Rol 100%
    function restablecerPermisosAlRol() {
        currentUserData.excepciones = {};
        actualizarEstadoPermisosUsuario(true);
        showToast('Restablecido a los permisos predeterminados del rol.', 'success');
    }

    // Actualiza visualmente el estado de cada casilla y su badge explicativo
    function actualizarEstadoPermisosUsuario(sincronizarConRol = false) {
        const checkboxes = document.querySelectorAll('.user-permiso-checkbox');
        const rolPerms = currentUserData.permisos_del_rol || [];
        const excepciones = currentUserData.excepciones || {};

        let totalActivos = 0;
        let totalHeredados = 0;
        let totalExtras = 0;
        let totalBloqueados = 0;

        checkboxes.forEach(cb => {
            const pId = parseInt(cb.getAttribute('data-permiso-id'));
            const perteneceAlRol = rolPerms.includes(pId);
            const excepcion = excepciones[pId];

            if (sincronizarConRol) {
                cb.checked = perteneceAlRol;
            } else if (excepcion) {
                cb.checked = (excepcion === 'permitir');
            } else {
                cb.checked = perteneceAlRol;
            }

            const estaActivo = cb.checked;
            const badge = document.getElementById(`perm_status_${pId}`);

            if (estaActivo) {
                totalActivos++;
                if (perteneceAlRol) {
                    totalHeredados++;
                    if (badge) {
                        badge.className = 'perm-status-indicator text-[9px] font-black px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200';
                        badge.innerText = 'Heredado del Rol';
                    }
                } else {
                    totalExtras++;
                    if (badge) {
                        badge.className = 'perm-status-indicator text-[9px] font-black px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200';
                        badge.innerText = '+ Excepción Concedida';
                    }
                }
            } else {
                if (perteneceAlRol) {
                    totalBloqueados++;
                    if (badge) {
                        badge.className = 'perm-status-indicator text-[9px] font-black px-1.5 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-200';
                        badge.innerText = '✕ Revocado (Excepción)';
                    }
                } else {
                    if (badge) {
                        badge.className = 'perm-status-indicator text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-400 border border-gray-200';
                        badge.innerText = 'Sin Acceso';
                    }
                }
            }
        });

        // Actualizar contador global
        const counter = document.getElementById('userPermCounter');
        if (counter) {
            counter.innerText = `${totalActivos} activos (${totalHeredados} del rol, ${totalExtras} extra, ${totalBloqueados} revocados)`;
        }

        // Actualizar contadores por módulo
        document.querySelectorAll('.user-module-card').forEach(card => {
            const modCheckboxes = card.querySelectorAll('.user-permiso-checkbox');
            const modTotal = modCheckboxes.length;
            const modChecked = Array.from(modCheckboxes).filter(cb => cb.checked).length;
            const modBadge = card.querySelector('.user-module-counter');
            if (modBadge) {
                modBadge.innerText = `${modChecked}/${modTotal}`;
                if (modChecked === modTotal && modTotal > 0) {
                    modBadge.classList.remove('bg-white', 'text-gray-600');
                    modBadge.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                } else {
                    modBadge.classList.add('bg-white', 'text-gray-600');
                    modBadge.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
                }
            }
        });
    }

    // Al interactuar manualmente con una casilla
    function onUserPermisoCheckboxChanged() {
        // Al alterar casillas manualmente, recalculamos las excepciones relativas al rol
        const rolPerms = currentUserData.permisos_del_rol || [];
        const nuevasExcepciones = {};

        document.querySelectorAll('.user-permiso-checkbox').forEach(cb => {
            const pId = parseInt(cb.getAttribute('data-permiso-id'));
            const perteneceAlRol = rolPerms.includes(pId);
            const estaActivo = cb.checked;

            if (estaActivo && !perteneceAlRol) {
                nuevasExcepciones[pId] = 'permitir';
            } else if (!estaActivo && perteneceAlRol) {
                nuevasExcepciones[pId] = 'denegar';
            }
        });

        currentUserData.excepciones = nuevasExcepciones;
        actualizarEstadoPermisosUsuario(false);
    }

    // Marcar / Desmarcar todas las casillas de un módulo
    function marcarModuloUsuario(moduleId, estado) {
        const card = document.querySelector(`.user-module-card[data-module-id="${moduleId}"]`);
        if (card) {
            card.querySelectorAll('.user-permiso-checkbox').forEach(cb => {
                cb.checked = estado;
            });
            onUserPermisoCheckboxChanged();
        }
    }

    // Buscador en vivo de permisos en el modal de usuario
    function filtrarPermisosUsuario(busqueda) {
        const term = busqueda.toLowerCase().trim();
        document.querySelectorAll('.user-module-card').forEach(card => {
            let tieneCoincidencias = false;
            card.querySelectorAll('.user-permiso-item').forEach(item => {
                const texto = item.getAttribute('data-perm-name') || '';
                if (!term || texto.includes(term)) {
                    item.classList.remove('hidden');
                    tieneCoincidencias = true;
                } else {
                    item.classList.add('hidden');
                }
            });

            if (tieneCoincidencias) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }

    // ==========================================
    // GUARDADO AJAX Y NOTIFICACIONES TOAST
    // ==========================================
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        
        let bgColor = 'bg-emerald-600';
        let icon = 'check_circle';
        if (type === 'error') { bgColor = 'bg-rose-600'; icon = 'error'; }
        if (type === 'info') { bgColor = 'bg-indigo-600'; icon = 'info'; }

        toast.className = `flex items-center gap-3 px-5 py-4 rounded-xl shadow-2xl text-white transform translate-y-10 opacity-0 transition-all duration-300 ${bgColor}`;
        toast.innerHTML = `
            <span class="material-symbols-outlined">${icon}</span>
            <p class="font-bold text-sm">${message}</p>
        `;

        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.remove('translate-y-10', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
        }, 10);

        setTimeout(() => {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('translate-y-10', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    function savePermisos(e) {
        e.preventDefault();
        const id = document.getElementById('usuario_id').value;
        const btn = document.getElementById('btnSave');
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[18px]">autorenew</span> Guardando...';
        btn.disabled = true;

        const formData = new FormData(e.target);
        
        fetch(`/admin/usuarios/${id}/permisos-extra`, {
            method: 'POST',
            body: formData,
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                closePermisosModal();
                showToast(data.message, 'success');
                fetchData(1); // Recargamos la tabla
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(() => {
            showToast('Ocurrió un error en el servidor al guardar permisos.', 'error');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }

    // Cerrar con Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") closePermisosModal();
    });
</script>
@endpush
@endsection