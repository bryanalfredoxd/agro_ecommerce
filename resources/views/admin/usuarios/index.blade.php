@extends('layouts.admin')

@section('title', 'Listado de Usuarios - Corpo Agrícola')

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
                        <button class="role-tab pb-3 text-sm font-bold border-b-2 border-transparent text-gray-400 hover:text-gray-700 whitespace-nowrap" data-rol="{{ $rol->id }}">{{ $rol->nombre }}</button>
                    @endforeach
                </div>

                {{-- Buscador y Selects --}}
                <div class="p-4 flex flex-col sm:flex-row gap-4 bg-gray-50/50 rounded-b-2xl">
                    <div class="flex-1 relative">
                        <span class="material-symbols-outlined absolute left-3 top-3 text-gray-400">search</span>
                        <input type="text" id="searchInput" class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-medium text-agro-dark" placeholder="Buscar por correo, CI, nombre...">
                    </div>
                    <div class="w-full sm:w-48 relative">
                        <select id="sortSelect" class="w-full pl-4 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-bold text-gray-600 appearance-none cursor-pointer">
                            <option value="newest">Más Recientes</option>
                            <option value="oldest">Más Antiguos</option>
                            <option value="az">Alfabético (A-Z)</option>
                            <option value="za">Alfabético (Z-A)</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-3 text-gray-400 pointer-events-none">sort</span>
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
                    <button type="button" onclick="closePermisosModal()" class="text-gray-400 hover:text-red-500 transition-colors p-1 bg-gray-50 rounded-lg border border-gray-200">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                {{-- Formulario --}}
                <form id="permisosForm" onsubmit="savePermisos(event)" class="flex flex-col flex-1 overflow-hidden min-h-0">
                    <input type="hidden" id="usuario_id">
                    
                    <div class="px-6 py-6 overflow-y-auto custom-scrollbar flex-1">
                        
                        {{-- SECCIÓN 1: ROL --}}
                        <div class="mb-6">
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Rol Base del Usuario</label>
                            <select name="rol_id" id="modalRolSelect" class="w-full h-12 rounded-xl bg-gray-50 border border-gray-200 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all px-4 font-bold text-agro-dark outline-none cursor-pointer appearance-none">
                                <option value="">Sin Rol</option>
                                @foreach($roles as $rol)
                                    <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- SECCIÓN 2: CUADRÍCULA DE PERMISOS --}}
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3 ml-1 flex items-center justify-between">
                                Asignación de Permisos
                                <button type="button" onclick="marcarTodosPermisos()" class="text-indigo-600 hover:underline cursor-pointer">Marcar / Desmarcar todos</button>
                            </label>
                            
                            <p class="text-xs text-gray-500 mb-4 bg-blue-50 text-blue-800 p-3 rounded-xl border border-blue-100 leading-relaxed">
                                <span class="font-bold">Info:</span> Marca las casillas para conceder acceso explícito a un módulo. Si la dejas desmarcada, el acceso será denegado.
                            </p>
                            
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
                        <button type="button" onclick="closePermisosModal()" class="px-5 py-2.5 rounded-xl font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 transition-colors text-sm">Cancelar</button>
                        <button type="submit" id="btnSave" class="px-6 py-2.5 rounded-xl font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-sm text-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">save</span> Guardar Configuración
                        </button>
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
    // ESTADO PARA AJAX
    let currentRole = 'all';
    let currentSearch = '';
    let currentSort = 'newest';
    let searchTimeout;

    // EVENTOS DE LOS CONTROLES
    document.querySelectorAll('.role-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.role-tab').forEach(t => {
                t.classList.remove('border-primary', 'text-agro-dark', 'active');
                t.classList.add('border-transparent', 'text-gray-400');
            });
            this.classList.remove('border-transparent', 'text-gray-400');
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
    // LÓGICA DEL MODAL DE PERMISOS
    // ==========================================
    // Función para el botón "Marcar / Desmarcar todos"
    function marcarTodosPermisos() {
        const checkboxes = document.querySelectorAll('.permiso-checkbox');
        const todosMarcados = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !todosMarcados);
    }

    function openPermisosModal(id, nombre) {
        document.getElementById('usuario_id').value = id;
        document.getElementById('modalUserName').innerText = 'Usuario: ' + nombre;
        
        // Limpiamos todo al abrir
        document.querySelectorAll('.permiso-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('modalRolSelect').value = ""; 

        fetch(`/admin/usuarios/${id}/permisos-extra`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if(data.rol_id) {
                document.getElementById('modalRolSelect').value = data.rol_id;
            }

            // Marcamos solo los que tienen acción "permitir"
            data.permisosExtra.forEach(permiso => {
                if (permiso.accion === 'permitir') {
                    const cb = document.querySelector(`.permiso-checkbox[value="${permiso.permiso_id}"]`);
                    if(cb) cb.checked = true;
                }
            });

            // Animación de entrada (adaptada a la nueva estructura)
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

    function closePermisosModal() {
        const modal = document.getElementById('permisosModal');
        const backdrop = document.getElementById('permisosBackdrop');
        const panel = document.getElementById('permisosPanel');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    // ==========================================
    // GUARDADO AJAX Y NOTIFICACIONES TOAST
    // ==========================================
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        
        const bgColor = type === 'success' ? 'bg-green-600' : 'bg-red-600';
        const icon = type === 'success' ? 'check_circle' : 'error';

        toast.className = `flex items-center gap-3 px-5 py-4 rounded-xl shadow-2xl text-white transform translate-y-10 opacity-0 transition-all duration-300 ${bgColor}`;
        toast.innerHTML = `
            <span class="material-symbols-outlined">${icon}</span>
            <p class="font-bold text-sm">${message}</p>
        `;

        container.appendChild(toast);

        // Animar entrada
        setTimeout(() => {
            toast.classList.remove('translate-y-10', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
        }, 10);

        // Animar salida y eliminar
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
                fetchData(1); // RECARGAMOS LA TABLA AUTOMÁTICAMENTE PARA VER EL NUEVO ROL
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(() => {
            showToast('Ocurrió un error en el servidor.', 'error');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }
</script>
@endpush
@endsection