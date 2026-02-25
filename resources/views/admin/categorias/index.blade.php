@extends('layouts.admin')

@section('title', 'Categorías - Corpo Agrícola')

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
                        <span class="material-symbols-outlined text-green-600 text-[32px]">category</span>
                        Jerarquía de Categorías
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Administra la estructura del catálogo, imágenes y subcategorías.</p>
                </div>
                <button onclick="openModal()" class="inline-flex items-center justify-center gap-2 bg-green-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-green-700 hover:shadow-lg hover:shadow-green-500/30 transition-all transform hover:-translate-y-0.5">
                    <span class="material-symbols-outlined text-[20px]">add_circle</span>
                    Nueva Categoría
                </button>
            </div>

            {{-- Buscador --}}
            <div class="bg-white rounded-t-3xl shadow-sm border border-gray-100 p-4 flex gap-4 relative z-10">
                <div class="flex-1 relative">
                    <span class="material-symbols-outlined absolute left-3 top-3 text-gray-400">search</span>
                    <input type="text" id="searchInput" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all outline-none font-medium text-agro-dark" placeholder="Buscar categoría por nombre...">
                </div>
            </div>

            {{-- Contenedor de la Tabla --}}
            <div class="bg-white rounded-b-3xl shadow-sm border border-t-0 border-gray-100 relative" id="tableContainer">
                <div id="loadingOverlay" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-20 hidden items-center justify-center rounded-b-3xl">
                    <span class="material-symbols-outlined animate-spin text-green-600 text-4xl">autorenew</span>
                </div>
                
                <div id="tableContent">
                    @include('admin.categorias.partials._table')
                </div>
            </div>

        </div>
    </main>
</div>

{{-- MODAL CREAR / EDITAR --}}
<div id="categoriaModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="catModalBackdrop" onclick="closeModal()"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-center p-4">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-md opacity-0 scale-95 flex flex-col" id="catModalPanel">
            
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-black text-agro-dark leading-none" id="modalTitle">Nueva Categoría</h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-red-500 bg-white p-1 rounded-lg border border-gray-200 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            {{-- El formulario usa enctype multipart/form-data porque enviamos una imagen --}}
            <form id="categoriaForm" onsubmit="saveCategoria(event)" enctype="multipart/form-data">
                <input type="hidden" id="categoria_id">
                
                <div class="p-6 space-y-5">
                    
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nombre</label>
                        <input type="text" id="nombre" name="nombre" required class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white focus:ring-2 focus:ring-green-500/20 transition-all font-bold text-agro-dark outline-none">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Categoría Padre (Opcional)</label>
                        <select id="categoria_padre_id" name="categoria_padre_id" class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white focus:ring-2 focus:ring-green-500/20 transition-all font-bold text-agro-dark outline-none cursor-pointer appearance-none">
                            <option value="">-- Ninguna (Es Categoría Principal) --</option>
                            @foreach($categoriasPadre as $padre)
                                <option value="{{ $padre->id }}">{{ $padre->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Imagen (Opcional)</label>
                        <input type="file" id="imagen" name="imagen" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 transition-all border border-gray-200 rounded-xl bg-gray-50 cursor-pointer">
                        <p class="text-[10px] text-gray-400 mt-2 ml-1">Recomendado: PNG o WEBP con fondo transparente. Max 2MB.</p>
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

@push('scripts')
<script>
    let currentSearch = '';
    let searchTimeout;

    // Buscador AJAX
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        currentSearch = this.value;
        searchTimeout = setTimeout(() => fetchData(1), 400);
    });

    // Paginación AJAX
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

        fetch(`{{ route('admin.categorias.index') }}?buscar=${currentSearch}&page=${page}`, {
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
    // LÓGICA DEL MODAL Y FORMULARIO (FormData)
    // ==========================================
    function openModal(categoria = null) {
        const form = document.getElementById('categoriaForm');
        form.reset();
        document.getElementById('categoria_id').value = '';
        document.getElementById('modalTitle').innerText = 'Nueva Categoría';

        if (categoria) {
            document.getElementById('modalTitle').innerText = 'Editar Categoría';
            document.getElementById('categoria_id').value = categoria.id;
            document.getElementById('nombre').value = categoria.nombre;
            document.getElementById('categoria_padre_id').value = categoria.categoria_padre_id || '';
        }

        const modal = document.getElementById('categoriaModal');
        const backdrop = document.getElementById('catModalBackdrop');
        const panel = document.getElementById('catModalPanel');
        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    function closeModal() {
        const modal = document.getElementById('categoriaModal');
        const backdrop = document.getElementById('catModalBackdrop');
        const panel = document.getElementById('catModalPanel');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

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

    function saveCategoria(e) {
        e.preventDefault();
        const id = document.getElementById('categoria_id').value;
        const btn = document.getElementById('btnSave');
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[18px]">autorenew</span> Guardando...';
        btn.disabled = true;

        // IMPORTANTE: Usamos FormData porque hay un campo <input type="file">
        const formData = new FormData(e.target);
        
        let url = `{{ route('admin.categorias.store') }}`;
        
        // Si es edición, apuntamos a la URL de update
        if (id) {
            url = `/admin/categorias/${id}`;
        }

        fetch(url, {
            method: 'POST', // Siempre enviamos por POST con FormData
            body: formData,
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(async res => {
            if (!res.ok) {
                const data = await res.json();
                throw new Error(data.message || 'Error en validación');
            }
            return res.json();
        })
        .then(data => {
            closeModal();
            showToast(data.message, 'success');
            fetchData(1); // Recargar la tabla
        })
        .catch(err => {
            showToast(err.message, 'error');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }

    function deleteCategoria(id) {
        if(!confirm('¿Estás seguro de eliminar esta categoría?')) return;

        fetch(`/admin/categorias/${id}`, {
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
</script>
@endpush
@endsection