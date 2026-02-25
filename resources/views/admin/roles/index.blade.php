@extends('layouts.admin')

@section('title', 'Roles y Permisos - Corpo Agrícola')

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
                                <span class="text-xs text-gray-400 italic">Sin permisos asignados</span>
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
                                <button type="submit" class="w-10 h-10 flex items-center justify-center bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-600 rounded-xl transition-colors border border-gray-200">
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
                    <button type="button" onclick="closeRoleModal()" class="text-gray-400 hover:text-red-500 transition-colors p-1 bg-gray-50 rounded-lg">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                {{-- Formulario --}}
                <form id="roleForm" method="POST" action="{{ route('admin.roles.store') }}" class="flex flex-col flex-1 overflow-hidden min-h-0">
                    @csrf
                    <div id="methodContainer"></div> {{-- Aquí inyectaremos @method('PUT') si es edición --}}
                    
                    <div class="px-6 py-6 overflow-y-auto custom-scrollbar flex-1">
                        
                        {{-- Input Nombre --}}
                        <div class="mb-6">
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Nombre del Rol</label>
                            <input type="text" name="nombre" id="roleName" required class="w-full h-12 rounded-xl bg-gray-50 border border-gray-200 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all px-4 font-bold text-agro-dark outline-none" placeholder="Ej: Gerente de Ventas">
                        </div>

                        {{-- Cuadrícula de Permisos --}}
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3 ml-1 flex items-center justify-between">
                                Asignación de Permisos
                                <button type="button" onclick="marcarTodos()" class="text-indigo-600 hover:underline cursor-pointer">Marcar todos</button>
                            </label>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="permissionsGrid">
                                @foreach($permisos as $permiso)
                                <label class="relative flex items-start p-4 bg-gray-50 rounded-xl border border-gray-200 cursor-pointer hover:bg-indigo-50/50 hover:border-indigo-200 transition-all group">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="permisos[]" value="{{ $permiso->id }}" class="permiso-checkbox h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer transition-colors">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <span class="font-bold text-gray-800 block capitalize">{{ str_replace('_', ' ', $permiso->nombre) }}</span>
                                        <span class="text-gray-500 text-[11px] leading-tight block mt-0.5">{{ $permiso->descripcion }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                    </div>

                    {{-- Footer / Botones --}}
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end gap-3 flex-shrink-0">
                        <button type="button" onclick="closeRoleModal()" class="px-5 py-2.5 rounded-xl font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 transition-colors text-sm">Cancelar</button>
                        <button type="submit" id="btnSubmitModal" class="px-6 py-2.5 rounded-xl font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-sm text-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">save</span> Guardar Configuración
                        </button>
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
        
        // Resetear formulario
        form.reset();
        methodContainer.innerHTML = '';
        checkboxes.forEach(cb => cb.checked = false);
        nameInput.readOnly = false;
        btnSubmit.style.display = 'flex';

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

    function marcarTodos() {
        const checkboxes = document.querySelectorAll('.permiso-checkbox');
        // Revisar si están todos marcados, para desmarcarlos, o viceversa
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => {
            if(!cb.disabled) cb.checked = !allChecked;
        });
    }

    // Cerrar con Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") closeRoleModal();
    });
</script>
@endpush
@endsection