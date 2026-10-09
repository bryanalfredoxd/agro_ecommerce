@extends('layouts.admin')

@section('title', 'Roles y Permisos - Agrosuministros Rodríguez')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    {{-- SIDEBAR LATERAL --}}
    @include('admin.partials.sidebar')

    {{-- CONTENIDO PRINCIPAL --}}
    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        
        {{-- TOPBAR ADMIN --}}
        @include('admin.partials.topbar')

        {{-- Área de Contenido --}}
        <div class="p-4 sm:p-8 animate-fade-in-up pb-20">
            
            {{-- Encabezado de la Sección --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-2xl font-black text-agro-dark flex items-center gap-2">
                        <span class="material-symbols-outlined text-indigo-600 text-[32px]">admin_panel_settings</span>
                        Roles y Permisos
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Gestiona los niveles de acceso y la seguridad del sistema.</p>
                </div>
                
                {{-- Botón Crear Rol --}}
                <button onclick="openRoleModal()" class="inline-flex items-center justify-center gap-2 bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-500/30 transition-all transform hover:-translate-y-0.5">
                    <span class="material-symbols-outlined text-[20px]">add_circle</span>
                    Nuevo Rol
                </button>
            </div>

            {{-- Cuadrícula de Roles --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 items-start">
                
                @foreach($roles as $rol)
                <div class="bg-white border border-gray-200 rounded-3xl p-6 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
                    
                    {{-- Decoración Visual --}}
                    <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-gradient-to-br from-indigo-50 to-transparent z-0"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-xl font-black text-agro-dark capitalize">{{ $rol->nombre }}</h3>
                            <span class="bg-gray-100 text-gray-600 text-[10px] font-bold px-2.5 py-1 rounded-lg flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">group</span> {{ $rol->usuarios_count }} Usuarios
                            </span>
                        </div>

                        <p class="text-xs font-bold text-indigo-600 mb-2">Permisos asignados ({{ $rol->permisos->count() }})</p>
                        
                        {{-- Muestra los primeros 3 permisos y un contador del resto --}}
                        <div class="flex flex-wrap gap-1.5 mb-6 min-h-[60px]">
                            @if($rol->id == 1)
                                <span class="bg-indigo-50 text-indigo-700 text-[10px] font-bold px-2 py-1 rounded-md border border-indigo-100">Acceso Total al Sistema</span>
                            @elseif($rol->permisos->count() > 0)
                                @foreach($rol->permisos->take(3) as $permiso)
                                    <span class="bg-gray-50 text-gray-600 text-[10px] font-bold px-2 py-1 rounded-md border border-gray-200">
                                        {{ str_replace('_', ' ', ucfirst($permiso->nombre)) }}
                                    </span>
                                @endforeach
                                @if($rol->permisos->count() > 3)
                                    <span class="bg-gray-100 text-gray-500 text-[10px] font-bold px-2 py-1 rounded-md">
                                        +{{ $rol->permisos->count() - 3 }} más
                                    </span>
                                @endif
                            @else
                                <span class="text-xs text-gray-700 italic">Sin permisos asignados</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 border-t border-gray-100 pt-4">
                            <button onclick="openRoleModal({{ $rol->toJson() }})" class="flex-1 bg-gray-50 hover:bg-indigo-50 text-gray-700 hover:text-indigo-700 text-xs font-bold py-2 rounded-xl transition-colors border border-gray-200 flex items-center justify-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">edit</span> {{ $rol->id == 1 ? 'Ver Permisos' : 'Editar Rol' }}
                            </button>
                            
                            {{-- Solo mostrar eliminar si no es rol base y no tiene usuarios --}}
                            @if(!in_array($rol->id, [1,2,3,4,5]) && $rol->usuarios_count == 0)
                            <form action="{{ route('admin.roles.destroy', $rol->id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de eliminar este rol?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-10 h-10 flex items-center justify-center bg-gray-50 hover:bg-red-50 text-gray-700 hover:text-red-600 rounded-xl transition-colors border border-gray-200">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach

            </div>

        </div>
    </main>
</div>

{{-- ========================================== --}}
{{-- MODAL CREAR / EDITAR ROL Y PERMISOS        --}}
{{-- ========================================== --}}
<div id="roleModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    {{-- Fondo Oscuro --}}
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="roleModalBackdrop" onclick="closeRoleModal()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center sm:items-center p-0 sm:p-4">
            {{-- Panel del Modal --}}
            <div class="relative transform overflow-hidden rounded-t-3xl sm:rounded-3xl bg-white text-left shadow-2xl transition-all w-full sm:max-w-4xl opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95 flex flex-col max-h-[90vh]" id="roleModalPanel">
                
                {{-- Header del Modal --}}
                <div class="bg-white px-6 py-5 border-b border-gray-100 flex justify-between items-center z-10 flex-shrink-0">
                    <h3 class="text-xl font-black text-agro-dark" id="modalTitle">Crear Nuevo Rol</h3>
                    <button type="button" onclick="closeRoleModal()" class="text-gray-700 hover:text-red-500 transition-colors p-1 bg-gray-50 rounded-lg">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                {{-- Formulario --}}
                <form id="roleForm" method="POST" action="{{ route('admin.roles.store') }}" class="flex flex-col flex-1 overflow-hidden min-h-0">
                                      <div id="methodContainer"></div> {{-- Aquí inyectaremos @method('PUT') si es edición --}}
                    
                    <div class="px-6 py-6 overflow-y-auto custom-scrollbar flex-1 space-y-6">
                        
                        {{-- Input Nombre y Estadísticas Rápidas --}}
                        <div class="bg-gray-50/70 p-4 rounded-2xl border border-gray-100 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                            <div class="flex-1 w-full">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5 ml-1">Nombre del Rol</label>
                                <input type="text" name="nombre" id="roleName" required class="w-full h-11 rounded-xl bg-white border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all px-4 font-bold text-agro-dark outline-none text-sm" placeholder="Ej: Vendedor de Mostrador">
                            </div>
                            <div class="flex flex-col items-end shrink-0 sm:self-end">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Permisos Asignados</span>
                                <span id="rolePermissionsCounter" class="text-sm font-black text-indigo-600 font-mono bg-indigo-50 px-3 py-1 rounded-lg border border-indigo-100 mt-1">
                                    0 / 31 seleccionados
                                </span>
                            </div>
                        </div>

                        {{-- Barra de Herramientas de Permisos (Buscador + Acciones Rápidas) --}}
                        <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between pt-2 border-t border-gray-100">
                            {{-- Buscador en Vivo --}}
                            <div class="relative flex-1">
                                <span class="material-symbols-outlined absolute left-3.5 top-2.5 text-gray-400 text-[18px]">search</span>
                                <input type="text" id="rolePermissionSearch" oninput="filtrarPermisosRol(this.value)" placeholder="Buscar permiso por nombre, módulo o función..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
                            </div>

                            {{-- Botones Globales --}}
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" onclick="marcarTodosRol(true)" class="px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-xs font-bold transition-colors flex items-center gap-1 border border-indigo-200/60">
                                    <span class="material-symbols-outlined text-[16px]">select_all</span> Marcar Todos
                                </button>
                                <button type="button" onclick="marcarTodosRol(false)" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition-colors flex items-center gap-1 border border-gray-200">
                                    <span class="material-symbols-outlined text-[16px]">deselect</span> Desmarcar
                                </button>
                            </div>
                        </div>

                        {{-- Módulos de Permisos Estructurados --}}
                        <div class="space-y-4" id="modulesAccordionContainer">
                            @foreach($modulos as $modIdx => $modulo)
                            <div class="module-card bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-2xs transition-all hover:border-gray-300" data-module-id="{{ $modulo['id'] }}">
                                {{-- Cabecera del Módulo --}}
                                <div class="px-5 py-3.5 bg-gray-50/80 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3 select-none">
                                    <div class="flex items-center gap-3">
                                        <div class="size-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-[18px]">{{ $modulo['icono'] }}</span>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h4 class="text-xs font-black text-gray-900 tracking-tight">{{ $modulo['nombre'] }}</h4>
                                                <span class="module-counter text-[10px] font-bold px-2 py-0.5 rounded-full bg-white border border-gray-200 text-gray-600 font-mono">
                                                    0/{{ count($modulo['permisos']) }}
                                                </span>
                                            </div>
                                            <p class="text-[11px] text-gray-500 font-normal leading-tight">{{ $modulo['descripcion'] }}</p>
                                        </div>
                                    </div>

                                    {{-- Botones de Acción por Módulo --}}
                                    <div class="flex items-center gap-1.5 ml-auto">
                                        <button type="button" onclick="marcarModulo('{{ $modulo['id'] }}', true)" class="px-2 py-1 bg-white hover:bg-indigo-50 text-[10px] font-bold text-gray-600 hover:text-indigo-600 rounded-lg border border-gray-200 transition-colors">
                                            Todos
                                        </button>
                                        <button type="button" onclick="marcarModulo('{{ $modulo['id'] }}', false)" class="px-2 py-1 bg-white hover:bg-gray-100 text-[10px] font-bold text-gray-600 rounded-lg border border-gray-200 transition-colors">
                                            Ninguno
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
                                        <label class="permiso-item relative flex items-start p-3 rounded-xl border border-gray-150 bg-gray-50/40 hover:bg-indigo-50/30 hover:border-indigo-200 transition-all cursor-pointer group" data-perm-name="{{ strtolower($pInfo['nombre'] . ' ' . $key . ' ' . $pInfo['descripcion']) }}">
                                            <div class="flex items-center h-5 mt-0.5">
                                                <input type="checkbox" name="permisos[]" value="{{ $permisoObj->id }}" onchange="actualizarContadoresRol()" class="permiso-checkbox size-4 rounded text-indigo-600 focus:ring-indigo-500 border-gray-300 transition-colors cursor-pointer" data-module="{{ $modulo['id'] }}">
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
                                                <span class="text-[9px] font-mono text-gray-400 mt-1 block">
                                                    Clave: {{ $key }}
                                                </span>
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
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex items-center justify-between gap-3 flex-shrink-0">
                        <span class="text-xs text-gray-500 font-medium hidden sm:inline">
                            Asegúrate de revisar los permisos de acciones sensibles antes de guardar.
                        </span>
                        <div class="flex items-center gap-2 ml-auto">
                            <button type="button" onclick="closeRoleModal()" class="px-5 py-2.5 rounded-xl font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 transition-colors text-sm">Cancelar</button>
                            <button type="submit" id="btnSubmitModal" class="px-6 py-2.5 rounded-xl font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-sm text-sm flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">save</span> Guardar Configuración
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Lógica para abrir/cerrar modal
    function openRoleModal(role = null) {
        const modal = document.getElementById('roleModal');
        const backdrop = document.getElementById('roleModalBackdrop');
        const panel = document.getElementById('roleModalPanel');
        
        const form = document.getElementById('roleForm');
        const title = document.getElementById('modalTitle');
        const nameInput = document.getElementById('roleName');
        const methodContainer = document.getElementById('methodContainer');
        const btnSubmit = document.getElementById('btnSubmitModal');
        const checkboxes = document.querySelectorAll('.permiso-checkbox');
        const searchInput = document.getElementById('rolePermissionSearch');
        
        // Resetear formulario
        form.reset();
        methodContainer.innerHTML = '';
        checkboxes.forEach(cb => cb.checked = false);
        nameInput.readOnly = false;
        btnSubmit.style.display = 'flex';
        if (searchInput) {
            searchInput.value = '';
            filtrarPermisosRol('');
        }

        if (role) {
            // Modo Edición
            title.innerText = 'Editar Rol: ' + role.nombre;
            form.action = `/admin/roles/${role.id}`;
            methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            nameInput.value = role.nombre;
            
            // Marcar permisos que ya tiene el rol
            const rolePerms = role.permisos.map(p => p.id);
            checkboxes.forEach(cb => {
                if(rolePerms.includes(parseInt(cb.value))) {
                    cb.checked = true;
                }
            });

            // Si es el Administrador principal (ID 1), bloqueamos la edición por seguridad
            if(role.id === 1) {
                title.innerText = 'Permisos del Administrador';
                nameInput.readOnly = true;
                btnSubmit.style.display = 'none';
                checkboxes.forEach(cb => {
                    cb.checked = true; // El admin tiene todos
                    cb.disabled = true;
                });
            } else {
                checkboxes.forEach(cb => cb.disabled = false);
            }

        } else {
            // Modo Creación
            title.innerText = 'Crear Nuevo Rol';
            form.action = `{{ route('admin.roles.store') }}`;
            checkboxes.forEach(cb => cb.disabled = false);
        }

        actualizarContadoresRol();

        // Animación de entrada
        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95', 'translate-y-8', 'sm:translate-y-0');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    function closeRoleModal() {
        const modal = document.getElementById('roleModal');
        const backdrop = document.getElementById('roleModalBackdrop');
        const panel = document.getElementById('roleModalPanel');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95', 'translate-y-8', 'sm:translate-y-0');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Actualiza los contadores globales y por módulo
    function actualizarContadoresRol() {
        const checkboxes = document.querySelectorAll('.permiso-checkbox');
        const total = checkboxes.length;
        const checked = Array.from(checkboxes).filter(cb => cb.checked).length;

        const globalCounter = document.getElementById('rolePermissionsCounter');
        if (globalCounter) {
            globalCounter.innerText = `${checked} / ${total} seleccionados`;
        }

        // Contadores por módulo
        document.querySelectorAll('.module-card').forEach(card => {
            const moduleId = card.getAttribute('data-module-id');
            const modCheckboxes = card.querySelectorAll('.permiso-checkbox');
            const modTotal = modCheckboxes.length;
            const modChecked = Array.from(modCheckboxes).filter(cb => cb.checked).length;
            const badge = card.querySelector('.module-counter');
            if (badge) {
                badge.innerText = `${modChecked}/${modTotal}`;
                if (modChecked === modTotal && modTotal > 0) {
                    badge.classList.remove('bg-white', 'text-gray-600');
                    badge.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                } else {
                    badge.classList.add('bg-white', 'text-gray-600');
                    badge.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
                }
            }
        });
    }

    // Marcar / Desmarcar todos globalmente
    function marcarTodosRol(estado) {
        document.querySelectorAll('.permiso-checkbox').forEach(cb => {
            if(!cb.disabled) cb.checked = estado;
        });
        actualizarContadoresRol();
    }

    // Marcar / Desmarcar por módulo
    function marcarModulo(moduleId, estado) {
        const card = document.querySelector(`.module-card[data-module-id="${moduleId}"]`);
        if (card) {
            card.querySelectorAll('.permiso-checkbox').forEach(cb => {
                if(!cb.disabled) cb.checked = estado;
            });
            actualizarContadoresRol();
        }
    }

    // Filtro en vivo de permisos por texto
    function filtrarPermisosRol(busqueda) {
        const term = busqueda.toLowerCase().trim();
        document.querySelectorAll('.module-card').forEach(card => {
            let tieneCoincidencias = false;
            card.querySelectorAll('.permiso-item').forEach(item => {
                const texto = item.getAttribute('data-perm-name') || '';
                if (!term || texto.includes(term)) {
                    item.classList.remove('hidden');
                    tieneCoincidencias = true;
                } else {
                    item.classList.add('hidden');
                }
            });

            // Si ningún permiso del módulo coincide, ocultar el módulo entero
            if (tieneCoincidencias) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }

    // Cerrar con Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") closeRoleModal();
    });
</script>
@endpush
@endsection