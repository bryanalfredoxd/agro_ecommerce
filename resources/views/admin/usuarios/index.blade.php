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
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="permisosBackdrop" onclick="closePermisosModal()"></div>
    <div class="fixed inset-0 z-10 flex justify-center items-center p-4 sm:p-0">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-2xl opacity-0 scale-95 flex flex-col max-h-[90vh]" id="permisosPanel">
            
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50 flex-shrink-0">
                <div>
                    <h3 class="text-lg font-black text-agro-dark leading-none">Configuración de Accesos</h3>
                    <p class="text-xs text-indigo-600 font-bold mt-1" id="modalUserName">Cargando...</p>
                </div>
                <button type="button" onclick="closePermisosModal()" class="text-gray-400 hover:text-red-500 bg-white p-1 rounded-lg border border-gray-200 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form id="permisosForm" onsubmit="savePermisos(event)" class="flex flex-col flex-1 overflow-hidden min-h-0">
                <input type="hidden" id="usuario_id">
                
                <div class="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-6">
                    
                    {{-- SECCIÓN 1: CAMBIO DE ROL --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Rol del Usuario</label>
                        <select name="rol_id" id="modalRolSelect" class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all font-bold text-agro-dark outline-none cursor-pointer appearance-none">
                            <option value="">Sin Rol</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="h-px w-full bg-gray-100"></div>

                    {{-- SECCIÓN 2: PERMISOS EXTRA --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Permisos Excepcionales</label>
                        <p class="text-xs text-gray-500 mb-4 bg-blue-50 text-blue-800 p-3 rounded-xl border border-blue-100 leading-relaxed">
                            <span class="font-bold">Info:</span> Por defecto el usuario hereda los permisos del rol seleccionado arriba. Usa estas opciones para <b>concederle acceso extra</b> o <b>denegarle acceso</b> a un módulo ignorando su rol.
                        </p>

                        <div class="space-y-2">
                            @foreach($permisos as $permiso)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3 sm:p-4 bg-gray-50 border border-gray-200 rounded-xl hover:bg-white hover:border-indigo-200 transition-colors">
                                <div class="mb-3 sm:mb-0">
                                    <p class="text-sm font-bold text-gray-800 capitalize">{{ str_replace('_', ' ', $permiso->nombre) }}</p>
                                    <p class="text-[10px] text-gray-500 mt-0.5">{{ $permiso->descripcion }}</p>
                                </div>
                                
                                <div class="flex items-center gap-1 bg-white border border-gray-200 rounded-lg p-1 flex-shrink-0">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="permisos[{{ $permiso->id }}]" value="heredar" class="peer sr-only" checked>
                                        <div class="px-3 py-1.5 text-[10px] font-bold text-gray-400 rounded-md peer-checked:bg-gray-100 peer-checked:text-gray-700 transition-colors">Heredar</div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="permisos[{{ $permiso->id }}]" value="permitir" class="peer sr-only">
                                        <div class="px-3 py-1.5 text-[10px] font-bold text-gray-400 rounded-md peer-checked:bg-green-100 peer-checked:text-green-700 transition-colors">Permitir</div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="permisos[{{ $permiso->id }}]" value="denegar" class="peer sr-only">
                                        <div class="px-3 py-1.5 text-[10px] font-bold text-gray-400 rounded-md peer-checked:bg-red-100 peer-checked:text-red-700 transition-colors">Denegar</div>
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3 flex-shrink-0">
                    <button type="button" onclick="closePermisosModal()" class="px-5 py-2.5 rounded-xl font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 text-sm">Cancelar</button>
                    <button type="submit" id="btnSave" class="px-6 py-2.5 rounded-xl font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm text-sm flex items-center gap-2 transition-all">
                        <span class="material-symbols-outlined text-[18px]">save</span> Guardar Cambios
                    </button>
                </div>
            </form>
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
    function openPermisosModal(id, nombre) {
        document.getElementById('usuario_id').value = id;
        document.getElementById('modalUserName').innerText = 'Usuario: ' + nombre;
        
        // Reset a "Heredar"
        document.querySelectorAll('input[value="heredar"]').forEach(radio => radio.checked = true);
        document.getElementById('modalRolSelect').value = ""; // Reset select

        fetch(`/admin/usuarios/${id}/permisos-extra`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            // 1. Asignar el ROL actual en el Select
            if(data.rol_id) {
                document.getElementById('modalRolSelect').value = data.rol_id;
            }

            // 2. Marcar los permisos extra
            data.permisosExtra.forEach(permiso => {
                const radio = document.querySelector(`input[name="permisos[${permiso.permiso_id}]"][value="${permiso.accion}"]`);
                if(radio) radio.checked = true;
            });

            // Animación
            const modal = document.getElementById('permisosModal');
            const backdrop = document.getElementById('permisosBackdrop');
            const panel = document.getElementById('permisosPanel');
            modal.classList.remove('hidden');
            setTimeout(() => {
                backdrop.classList.remove('opacity-0');
                panel.classList.remove('opacity-0', 'scale-95');
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