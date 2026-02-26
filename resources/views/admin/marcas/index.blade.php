@extends('layouts.admin')

@section('title', 'Marcas - Corpo Agrícola')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up pb-20">
            
            {{-- Encabezado --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-2xl font-black text-agro-dark flex items-center gap-2">
                        <span class="material-symbols-outlined text-green-600 text-[32px]">sell</span>
                        Directorio de Marcas
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Registra y administra los fabricantes de tus productos.</p>
                </div>
                <button onclick="openModal()" class="inline-flex items-center justify-center gap-2 bg-green-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-green-700 hover:shadow-lg hover:shadow-green-500/30 transition-all transform hover:-translate-y-0.5">
                    <span class="material-symbols-outlined text-[20px]">add_circle</span>
                    Nueva Marca
                </button>
            </div>

            {{-- Filtros y Buscador --}}
            <div class="bg-white rounded-t-3xl shadow-sm border border-gray-100 p-4 flex flex-col sm:flex-row gap-4 relative z-10">
                <div class="flex-1 relative">
                    <span class="material-symbols-outlined absolute left-3 top-3 text-gray-400">search</span>
                    <input type="text" id="searchInput" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all outline-none font-medium text-agro-dark" placeholder="Buscar por nombre o país...">
                </div>
                <div class="w-full sm:w-48 relative">
                    <select id="statusSelect" class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all outline-none font-bold text-gray-600 appearance-none cursor-pointer">
                        <option value="all">Todos los estados</option>
                        <option value="1">Activas</option>
                        <option value="0">Inactivas</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-3 text-gray-400 pointer-events-none">filter_list</span>
                </div>
            </div>

            {{-- Contenedor de la Tabla --}}
            <div class="bg-white rounded-b-3xl shadow-sm border border-t-0 border-gray-100 relative" id="tableContainer">
                <div id="loadingOverlay" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-20 hidden items-center justify-center rounded-b-3xl">
                    <span class="material-symbols-outlined animate-spin text-green-600 text-4xl">autorenew</span>
                </div>
                
                <div id="tableContent">
                    @include('admin.marcas.partials._table')
                </div>
            </div>

        </div>
    </main>
</div>

{{-- MODAL CREAR / EDITAR MARCA --}}
<div id="marcaModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="marcaModalBackdrop" onclick="closeModal()"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-center p-4">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-md opacity-0 scale-95 flex flex-col" id="marcaModalPanel">
            
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-black text-agro-dark leading-none" id="modalTitle">Nueva Marca</h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-red-500 bg-white p-1 rounded-lg border border-gray-200 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form id="marcaForm" onsubmit="saveMarca(event)">
                <input type="hidden" id="marca_id">
                <div id="methodContainer"></div>
                
                <div class="p-6 space-y-5">
                    
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nombre Comercial <span class="text-red-500">*</span></label>
                        <input type="text" id="nombre" name="nombre" required class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white focus:ring-2 focus:ring-green-500/20 transition-all font-bold text-agro-dark outline-none uppercase placeholder:normal-case" placeholder="Ej: Bayer">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">País de Origen (Opcional)</label>
                        <input type="text" id="pais_origen" name="pais_origen" class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white focus:ring-2 focus:ring-green-500/20 transition-all font-bold text-agro-dark outline-none" placeholder="Ej: Alemania">
                    </div>

                    <div class="pt-2">
                        <label class="relative flex items-center p-3 bg-gray-50 rounded-xl border border-gray-200 cursor-pointer hover:border-green-200 transition-all">
                            <input type="checkbox" id="activo" name="activo" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[14px] after:left-[14px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                            <span class="ml-3 text-sm font-bold text-gray-700">Marca Activa (Visible)</span>
                        </label>
                    </div>

                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-5 py-2.5 rounded-xl font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 text-sm">Cancelar</button>
                    <button type="submit" id="btnSave" class="px-6 py-2.5 rounded-xl font-bold text-white bg-green-600 hover:bg-green-700 shadow-sm text-sm flex items-center gap-2 transition-all">
                        <span class="material-symbols-outlined text-[18px]">save</span> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="toast-container" class="fixed bottom-6 right-6 z-[999] flex flex-col gap-2"></div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@push('scripts')
<script>
    let currentSearch = '';
    let currentStatus = 'all';
    let searchTimeout;

    // Buscador y Filtros AJAX
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        currentSearch = this.value;
        searchTimeout = setTimeout(() => fetchData(1), 400);
    });

    document.getElementById('statusSelect').addEventListener('change', function() {
        currentStatus = this.value;
        fetchData(1);
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.ajax-pagination a')) {
            e.preventDefault();
            fetchData(new URL(e.target.closest('a').href).searchParams.get('page'));
        }
    });

    function fetchData(page = 1) {
        const loading = document.getElementById('loadingOverlay');
        loading.classList.remove('hidden');
        loading.classList.add('flex');

        fetch(`{{ route('admin.marcas.index') }}?buscar=${currentSearch}&estado=${currentStatus}&page=${page}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => document.getElementById('tableContent').innerHTML = html)
        .finally(() => {
            loading.classList.add('hidden');
            loading.classList.remove('flex');
        });
    }

    // ==========================================
    // LÓGICA DEL MODAL
    // ==========================================
    function openModal(marca = null) {
        const form = document.getElementById('marcaForm');
        form.reset();
        document.getElementById('marca_id').value = '';
        document.getElementById('methodContainer').innerHTML = '';
        document.getElementById('modalTitle').innerText = 'Nueva Marca';
        document.getElementById('activo').checked = true;

        if (marca) {
            document.getElementById('modalTitle').innerText = 'Editar Marca';
            document.getElementById('marca_id').value = marca.id;
            document.getElementById('methodContainer').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('nombre').value = marca.nombre;
            document.getElementById('pais_origen').value = marca.pais_origen || '';
            document.getElementById('activo').checked = marca.activo == 1;
        }

        const modal = document.getElementById('marcaModal');
        const backdrop = document.getElementById('marcaModalBackdrop');
        const panel = document.getElementById('marcaModalPanel');
        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    function closeModal() {
        const modal = document.getElementById('marcaModal');
        const backdrop = document.getElementById('marcaModalBackdrop');
        const panel = document.getElementById('marcaModalPanel');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    // Toast Genérico
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-600' : 'bg-red-600';
        const icon = type === 'success' ? 'check_circle' : 'error';

        toast.className = `flex items-center gap-3 px-5 py-4 rounded-xl shadow-2xl text-white transform translate-y-10 opacity-0 transition-all duration-300 ${bgColor}`;
        toast.innerHTML = `<span class="material-symbols-outlined">${icon}</span><p class="font-bold text-sm">${message}</p>`;

        container.appendChild(toast);
        setTimeout(() => toast.classList.remove('translate-y-10', 'opacity-0'), 10);
        setTimeout(() => {
            toast.classList.add('translate-y-10', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Guardar (Crear/Editar)
    function saveMarca(e) {
        e.preventDefault();
        const id = document.getElementById('marca_id').value;
        const btn = document.getElementById('btnSave');
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[18px]">autorenew</span> Guardando...';
        btn.disabled = true;

        const formData = new FormData(e.target);
        const url = id ? `/admin/marcas/${id}` : `{{ route('admin.marcas.store') }}`;

        fetch(url, {
            method: 'POST', // En Laravel Form Data + _method=PUT se manda por POST
            body: formData,
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'El nombre de la marca ya existe o hay un error.');
            return data;
        })
        .then(data => {
            closeModal();
            showToast(data.message, 'success');
            fetchData(1);
        })
        .catch(err => {
            showToast(err.message, 'error');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }

    // Eliminar
    function deleteMarca(id) {
        Swal.fire({
            title: '¿Eliminar marca?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', // Color rojo Tailwind (red-500)
            cancelButtonColor: '#9ca3af', // Color gris Tailwind (gray-400)
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'rounded-3xl' // Mismo redondeo que usas en tu modal de creación
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Tu lógica fetch intacta
                fetch(`/admin/marcas/${id}`, {
                    method: 'DELETE',
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        showToast(data.message, 'success');
                        fetchData(1);
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(() => showToast('Error en el servidor', 'error'));
            }
        });
    }

    // Toggle Rápido de Estado en la Tabla
    function toggleMarcaStatus(id, checkbox) {
        // Deshabilitar momentáneamente para evitar doble clic
        checkbox.disabled = true;

        fetch(`/admin/marcas/${id}/toggle`, {
            method: 'POST',
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                showToast(data.message, 'success');
                // Opcional: Podrías hacer fetchData(1) aquí, pero visualmente el botón ya cambió así que no es estrictamente necesario.
            }
        })
        .catch(() => {
            showToast('Error al cambiar estado.', 'error');
            checkbox.checked = !checkbox.checked; // Revertir visualmente si falló
        })
        .finally(() => {
            checkbox.disabled = false;
        });
    }
</script>
@endpush
@endsection