@extends('layouts.admin')

@section('title', 'Catálogo de Productos - Corpo Agrícola')

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
                        <span class="material-symbols-outlined text-green-600 text-[32px]">inventory_2</span>
                        Catálogo de Productos
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Administra el inventario, precios y detalles de tus artículos.</p>
                </div>
                
                {{-- Redirigirá a una pantalla completa de creación en el futuro --}}
                <a href="{{ route('admin.productos.create') }}" class="inline-flex items-center justify-center gap-2 bg-green-600 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-green-700 hover:shadow-lg hover:shadow-green-500/30 transition-all transform hover:-translate-y-0.5">
                    <span class="material-symbols-outlined text-[20px]">add_circle</span>
                    Crear Producto
                </a>
            </div>

            {{-- Barra de Filtros Inteligente --}}
            <div class="bg-white rounded-t-3xl shadow-sm border border-gray-100 p-4 sm:p-5 flex flex-col xl:flex-row gap-4 relative z-10">
                
                {{-- Buscador Principal --}}
                <div class="flex-1 relative">
                    <span class="material-symbols-outlined absolute left-3 top-3 text-gray-400">search</span>
                    <input type="text" id="searchInput" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all outline-none font-medium text-agro-dark" placeholder="Buscar por Nombre, SKU o Código...">
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    {{-- Filtro por Categoría --}}
                    <div class="w-full sm:w-56 relative">
                        <select id="categoriaSelect" class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all outline-none font-bold text-gray-600 appearance-none cursor-pointer">
                            <option value="">Todas las Categorías</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-3 text-gray-400 pointer-events-none">folder</span>
                    </div>

                    {{-- Filtro Rápido (Chips) --}}
                    <div class="w-full sm:w-56 relative">
                        <select id="filtroRapidoSelect" class="w-full pl-4 pr-10 py-2.5 bg-red-50 border border-red-100 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all outline-none font-black text-red-700 appearance-none cursor-pointer">
                            <option value="">Vista General</option>
                            <option value="critico">🔥 Con Stock Crítico</option>
                            <option value="destacados">⭐ Destacados</option>
                            <option value="combos">📦 Solo Combos</option>
                            <option value="recetados">⚕️ Venta Controlada</option>
                            <option value="suspendidos">🚫 Suspendidos / Ocultos</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-3 text-red-400 pointer-events-none">filter_list</span>
                    </div>
                </div>

            </div>

            {{-- Contenedor de la Tabla AJAX --}}
            <div class="bg-white rounded-b-3xl shadow-sm border border-t-0 border-gray-100 relative" id="tableContainer">
                <div id="loadingOverlay" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-20 hidden items-center justify-center rounded-b-3xl">
                    <span class="material-symbols-outlined animate-spin text-green-600 text-4xl">autorenew</span>
                </div>
                
                <div id="tableContent">
                    @include('admin.productos.partials._table')
                </div>
            </div>

        </div>
    </main>
</div>

{{-- CONTENEDOR TOAST --}}
<div id="toast-container" class="fixed bottom-6 right-6 z-[999] flex flex-col gap-2"></div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@push('scripts')
<script>
    let currentSearch = '';
    let currentCategoria = '';
    let currentFiltroRapido = '';
    let searchTimeout;

    // Listeners de Filtros
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        currentSearch = this.value;
        searchTimeout = setTimeout(() => fetchData(1), 400);
    });

    document.getElementById('categoriaSelect').addEventListener('change', function() {
        currentCategoria = this.value;
        fetchData(1);
    });

    document.getElementById('filtroRapidoSelect').addEventListener('change', function() {
        currentFiltroRapido = this.value;
        // Cambiar color visual del select para indicar que hay un filtro activo
        if(this.value) {
            this.classList.replace('bg-gray-50', 'bg-red-50');
            this.classList.replace('text-gray-600', 'text-red-700');
        } else {
            this.classList.replace('bg-red-50', 'bg-gray-50');
            this.classList.replace('text-red-700', 'text-gray-600');
        }
        fetchData(1);
    });

    // Paginación AJAX
    document.addEventListener('click', function(e) {
        if (e.target.closest('.ajax-pagination a')) {
            e.preventDefault();
            fetchData(new URL(e.target.closest('a').href).searchParams.get('page'));
        }
    });

    // Petición AJAX
    function fetchData(page = 1) {
        const loading = document.getElementById('loadingOverlay');
        loading.classList.remove('hidden');
        loading.classList.add('flex');

        const params = new URLSearchParams({
            buscar: currentSearch,
            categoria_id: currentCategoria,
            filtro_rapido: currentFiltroRapido,
            page: page
        });

        fetch(`{{ route('admin.productos.index') }}?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById('tableContent').innerHTML = html;
        })
        .finally(() => {
            loading.classList.add('hidden');
            loading.classList.remove('flex');
        });
    }

    // Funciones Helper
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

    // Acciones de Producto
    function toggleDestacado(id, btn) {
        // Desactivamos el botón temporalmente
        btn.disabled = true;
        
        fetch(`/admin/productos/${id}/destacado`, {
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
                fetchData(1); // Recargamos para reflejar el cambio en la estrellita
            }
        })
        .catch(() => showToast('Error al actualizar.', 'error'))
        .finally(() => btn.disabled = false);
    }

function deleteProducto(id) {
        Swal.fire({
            title: '¿Suspender producto?',
            text: "El producto se ocultará de la tienda y el catálogo, pero su historial de ventas quedará intacto.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Sí, suspender producto',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'rounded-3xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/productos/${id}`, {
                    method: 'DELETE',
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(async res => {
                    if (!res.ok) throw new Error('Error en la respuesta del servidor');
                    return res.json();
                })
                .then(data => {
                    if(data.success) {
                        showToast('Producto suspendido correctamente.', 'success'); 
                        fetchData(1);
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error al suspender:', error);
                    showToast('Ocurrió un error al procesar la solicitud.', 'error');
                });
            }
        });
    } // <--- ¡ESTA LLAVE ES LA QUE FALTABA PARA CERRAR DELETEPRODUCTO!

    // Reactivar Producto Suspendido
    function restoreProducto(id) {
        Swal.fire({
            title: '¿Reactivar producto?',
            text: "El producto volverá a estar visible en el catálogo y disponible para la venta.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#10b981', // green-500
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Sí, reactivar',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'rounded-3xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/productos/${id}/restore`, {
                    method: 'POST',
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(async res => {
                    if (!res.ok) throw new Error('Error en la respuesta del servidor');
                    return res.json();
                })
                .then(data => {
                    if(data.success) {
                        showToast(data.message, 'success');
                        fetchData(1); // Recarga la tabla
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error al reactivar:', error);
                    showToast('Ocurrió un error al procesar la solicitud.', 'error');
                });
            }
        });
    }
</script>
@endpush
@endsection