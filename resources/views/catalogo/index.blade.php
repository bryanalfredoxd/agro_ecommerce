@extends('layouts.app')

@section('title', 'Catálogo de Insumos - Corpo Agrícola')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/catalogo.css') }}">
@endpush

@section('content')
<div class="bg-gray-50 min-h-screen font-sans animate-fade-in-up pb-12">

    {{-- 1. CABECERA COMPACTA Y PROFESIONAL --}}
    <div class="bg-agro-dark text-white border-b border-gray-200 py-6 mb-8">
        <div class="layout-container px-4 sm:px-0 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[28px] sm:text-[32px]">inventory_2</span>
                    Catálogo de <span class="text-primary">Insumos</span>
                </h1>
                <p class="text-sm text-gray-500 mt-1">Explora nuestra selección de productos agrícolas y veterinarios.</p>
            </div>
        </div>
    </div>

    {{-- CONTENEDOR PRINCIPAL --}}
    <div id="contenido-catalogo" class="layout-container relative z-20 px-4 sm:px-0">
        
        <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">

            {{-- 2. SIDEBAR DE FILTROS --}}
            <aside class="w-full lg:w-64 xl:w-72 flex-shrink-0">
                
                <button id="mobile-filter-btn" onclick="toggleFilters()" 
                        class="lg:hidden w-full flex items-center justify-center gap-2 bg-white text-agro-dark px-4 py-3.5 rounded-xl shadow-sm font-bold mb-4 border border-gray-200 active:scale-95 transition-all">
                    <span class="material-symbols-outlined text-primary">tune</span>
                    Filtros y Búsqueda
                </button>

                <div id="filters-panel" class="hidden lg:block bg-white rounded-2xl border border-gray-200 p-5 sticky top-24 transition-all duration-300 z-30 shadow-sm">
                    
                    {{-- Buscador --}}
                    <form id="search-form" method="GET" class="mb-6 relative group" onsubmit="event.preventDefault();">
                        <input type="text" name="buscar" value="{{ request('buscar') }}" 
                               id="search-input"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-sm font-medium text-agro-dark placeholder-gray-400" 
                               placeholder="Buscar producto...">
                        <span class="material-symbols-outlined absolute left-3 top-2.5 text-gray-400 group-focus-within:text-primary transition-colors text-[20px]">search</span>
                    </form>

                    <hr class="border-gray-100 mb-6">

                    {{-- Categorías --}}
                    <div class="mb-6">
                        <h3 class="font-bold text-agro-dark text-sm uppercase tracking-wider mb-3">Departamentos</h3>
                        <nav class="space-y-1 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                            <a href="#" data-filter="categoria" data-value="" 
                               class="filter-link flex items-center gap-3 px-2 py-2 rounded-lg text-sm font-medium transition-all duration-200 group {{ !request('categoria') ? 'text-primary bg-primary/5' : 'text-gray-600 hover:text-agro-dark hover:bg-gray-50' }}">
                                <div class="w-1.5 h-1.5 rounded-full transition-colors {{ !request('categoria') ? 'bg-primary' : 'bg-gray-300 group-hover:bg-agro-dark' }}"></div>
                                Todas las categorías
                            </a>

                            @foreach($categorias as $cat)
                                <a href="#" data-filter="categoria" data-value="{{ $cat->id }}" 
                                   class="filter-link flex items-center gap-3 px-2 py-2 rounded-lg text-sm font-medium transition-all duration-200 group {{ request('categoria') == $cat->id ? 'text-primary bg-primary/5' : 'text-gray-600 hover:text-agro-dark hover:bg-gray-50' }}">
                                    <div class="w-1.5 h-1.5 rounded-full transition-colors {{ request('categoria') == $cat->id ? 'bg-primary' : 'bg-gray-300 group-hover:bg-agro-dark' }}"></div>
                                    <span class="line-clamp-1">{{ $cat->nombre }}</span>
                                </a>
                            @endforeach
                        </nav>
                    </div>

                    <hr class="border-gray-100 mb-6">

                    {{-- Marcas --}}
                    <div class="mb-4">
                        <h3 class="font-bold text-agro-dark text-sm uppercase tracking-wider mb-3">Marcas</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($marcas as $marca)
                                <a href="#" data-filter="marca" data-value="{{ $marca->id }}" 
                                   class="filter-link inline-flex items-center justify-center text-center px-4 py-1 sm:py-1.5 min-h-[44px] sm:min-h-[32px] rounded-full text-xs font-bold border transition-all duration-200 {{ request('marca') == $marca->id ? 'bg-agro-dark text-white border-agro-dark shadow-sm' : 'bg-white text-gray-500 border-gray-200 hover:border-primary hover:text-primary hover:bg-primary/5' }}">
                                    <span class="truncate max-w-[130px] leading-none select-none">{{ $marca->nombre }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Botón Limpiar --}}
                    <div id="clear-filters-container" class="{{ request()->hasAny(['categoria', 'marca', 'buscar']) ? 'block' : 'hidden' }}">
                        <a href="#" onclick="catalogoClearFilters(event)" 
                           class="flex items-center justify-center gap-1.5 w-full py-2.5 mt-6 text-xs text-red-500 font-bold hover:bg-red-50 rounded-lg transition-all duration-300 group border border-transparent hover:border-red-100">
                            <span class="material-symbols-outlined text-[16px] group-hover:-rotate-180 transition-transform duration-500">restart_alt</span>
                            Borrar Filtros
                        </a>
                    </div>
                </div>
            </aside>

            {{-- 3. CONTENEDOR DE PRODUCTOS (Inyecta el parcial) --}}
            <main class="flex-1 relative">
                <div id="loading-overlay" class="absolute inset-0 bg-gray-50/80 backdrop-blur-[2px] z-40 flex items-start justify-center pt-20 hidden rounded-2xl">
                    <div class="bg-white p-5 rounded-2xl shadow-lg border border-gray-100 flex items-center gap-3">
                        <div class="w-6 h-6 border-2 border-gray-200 border-t-primary rounded-full animate-spin"></div>
                        <p class="text-agro-dark font-bold text-sm">Actualizando...</p>
                    </div>
                </div>
                
                <div id="products-container">
                    @include('catalogo.partials.products', ['productos' => $productos])
                </div>
            </main>

        </div>
    </div>
</div>

{{-- TOAST NOTIFICATION --}}
<div id="toast-notification" class="fixed bottom-6 right-6 z-[100] transform transition-all duration-500 translate-y-24 opacity-0 pointer-events-none">
    <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-gray-100 p-4 flex items-center gap-4 min-w-[320px] max-w-md">
        <div id="toast-icon-container" class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 transition-colors">
            <span id="toast-icon" class="material-symbols-outlined text-2xl">check_circle</span>
        </div>
        <div class="flex-1">
            <h4 id="toast-title" class="font-bold text-gray-900 text-sm">Notificación</h4>
            <p id="toast-message" class="text-xs font-medium text-gray-500 mt-0.5">Mensaje</p>
        </div>
        <button onclick="hideToast()" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100">
            <span class="material-symbols-outlined text-xl">close</span>
        </button>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        // Configuración Global del Catálogo
        window.CatalogoConfig = {
            routes: {
                addCart: "{{ route('carrito.add') }}",
                login: "{{ route('login') }}",
                catalogo: "{{ route('catalogo') }}"
            },
            filters: {
                buscar: "{{ request('buscar') }}",
                categoria: "{{ request('categoria') }}",
                marca: "{{ request('marca') }}",
                orden: "{{ request('orden', 'reciente') }}"
            },
            isAuth: {{ Auth::check() ? 'true' : 'false' }},
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        };

        // Lógica AJAX para Añadir al Carrito
        function agregarAlCarrito(productoId, btnElement) {
            const iconNormal = btnElement.querySelector('.btn-icon');
            const iconSpinner = btnElement.querySelector('.btn-spinner');
            const iconSuccess = btnElement.querySelector('.btn-success');

            // Estado de carga
            btnElement.disabled = true;
            btnElement.classList.add('bg-primary', 'text-white', 'pointer-events-none');
            iconNormal.classList.add('hidden');
            iconSpinner.classList.remove('hidden');

            fetch(window.CatalogoConfig.routes.addCart, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': window.CatalogoConfig.csrfToken
                },
                body: JSON.stringify({
                    producto_id: productoId,
                    cantidad: 1 
                })
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Error al añadir al carrito');
                return data;
            })
            .then(data => {
                // Éxito
                iconSpinner.classList.add('hidden');
                iconSuccess.classList.remove('hidden');
                btnElement.classList.replace('bg-primary', 'bg-green-500'); 
                
                // Disparar evento personalizado por si el Header necesita actualizar el número del carrito
                window.dispatchEvent(new CustomEvent('carrito-actualizado', { detail: { total: data.total_items } }));

                if (typeof showToast === 'function') {
                    showToast('Producto añadido al carrito', 'success');
                }
            })
            .catch(error => {
                // Error
                iconSpinner.classList.add('hidden');
                iconNormal.classList.remove('hidden');
                if (typeof showToast === 'function') {
                    showToast(error.message, 'error');
                } else {
                    alert(error.message);
                }
            })
            .finally(() => {
                // Restaurar botón
                setTimeout(() => {
                    iconSuccess.classList.add('hidden');
                    iconNormal.classList.remove('hidden');
                    btnElement.classList.remove('bg-green-500', 'bg-primary', 'text-white', 'pointer-events-none');
                    btnElement.disabled = false;
                }, 2000);
            });
        }
    </script>
    <script src="{{ asset('js/catalogo.js') }}"></script>
@endpush