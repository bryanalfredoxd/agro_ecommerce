@extends('layouts.app')

@section('title', 'Catálogo de Insumos - Agropecuaria Venezuela')

@section('content')
<div class="bg-gray-50 min-h-screen font-sans animate-fade-in-up">

    <div class="relative bg-gradient-to-br from-agro-dark via-teal-900 to-primary/90 text-white pb-32 pt-16 shadow-xl overflow-hidden">
        <div class="absolute inset-0 opacity-20 mix-blend-overlay bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-primary/30 blur-3xl animate-pulse-slow"></div>
        
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center lg:text-left">
            <h1 class="text-4xl md:text-5xl font-black tracking-tight mb-4 drop-shadow-sm">
                Catálogo de <span class="text-green-300">Insumos</span>
            </h1>
            <p class="text-green-50 text-lg max-w-2xl mx-auto lg:mx-0 font-medium leading-relaxed">
                Calidad certificada para tu producción. Encuentra las mejores marcas y productos veterinarios.
            </p>
        </div>
    </div>

    <div id="contenido-catalogo" class="container mx-auto px-4 sm:px-6 lg:px-8 -mt-20 pb-12 relative z-20 scroll-mt-24">
        
        <div class="flex flex-col lg:flex-row gap-8">

            <aside class="w-full lg:w-72 flex-shrink-0">
                
                <button id="mobile-filter-btn" onclick="toggleFilters()" 
                        class="lg:hidden w-full flex items-center justify-center gap-2 bg-white text-agro-dark px-4 py-4 rounded-xl shadow-md font-bold mb-6 border-0 ring-1 ring-gray-100 active:scale-95 transition-all">
                    <span class="material-symbols-outlined text-primary">tune</span>
                    Filtrar Productos
                </button>

                <div id="filters-panel" class="hidden lg:block bg-white/95 backdrop-blur-md rounded-2xl shadow-xl shadow-gray-200/50 border border-white/50 p-6 sticky top-6 transition-all duration-300">
                    
                    <form id="search-form" method="GET" class="mb-8 relative group">
                        <input type="text" name="buscar" value="{{ request('buscar') }}" 
                               id="search-input"
                               class="w-full pl-11 pr-4 py-3 rounded-xl bg-gray-100/80 border-transparent focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/20 transition-all text-sm font-medium text-gray-800 placeholder-gray-400" 
                               placeholder="Buscar insumo...">
                        <span class="material-symbols-outlined absolute left-3 top-3 text-gray-400 group-focus-within:text-primary transition-colors duration-300">search</span>
                    </form>

                    <div class="mb-8">
                        <h3 class="font-bold text-agro-dark text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg text-primary">category</span> Categorías
                        </h3>
                        <nav class="space-y-2 max-h-[350px] overflow-y-auto pr-2 custom-scrollbar">
                            <a href="#" data-filter="categoria" data-value="" 
                               class="filter-link flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition-all duration-300 group {{ !request('categoria') ? 'bg-primary text-white shadow-lg shadow-primary/30 scale-[1.02]' : 'text-gray-700 bg-gray-50 hover:bg-gray-100 hover:text-primary' }}">
                                <span>Todas</span>
                                @if(!request('categoria'))
                                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                                @endif
                            </a>

                            @foreach($categorias as $cat)
                                <a href="#" data-filter="categoria" data-value="{{ $cat->id }}" 
                                   class="filter-link flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition-all duration-300 group {{ request('categoria') == $cat->id ? 'bg-primary text-white shadow-lg shadow-primary/30 scale-[1.02]' : 'text-gray-700 bg-gray-50 hover:bg-gray-100 hover:text-primary' }}">
                                    <span class="line-clamp-1">{{ $cat->nombre }}</span>
                                    @if(request('categoria') == $cat->id)
                                        <span class="material-symbols-outlined text-[20px]">check_circle</span>
                                    @else
                                        <span class="material-symbols-outlined text-[20px] opacity-0 group-hover:opacity-100 text-primary/60 transition-opacity">chevron_right</span>
                                    @endif
                                </a>
                            @endforeach
                        </nav>
                    </div>

                    <div class="mb-6">
                        <h3 class="font-bold text-agro-dark text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                             <span class="material-symbols-outlined text-lg text-primary">verified</span> Marcas
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($marcas as $marca)
                                <a href="#" data-filter="marca" data-value="{{ $marca->id }}" 
                                   class="filter-link px-3 py-1.5 rounded-lg text-xs font-bold border transition-all duration-200 {{ request('marca') == $marca->id ? 'bg-agro-dark text-white border-agro-dark shadow-md' : 'bg-white text-gray-600 border-gray-200 hover:border-primary hover:text-primary hover:bg-primary/5' }}">
                                    {{ $marca->nombre }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    @if(request()->hasAny(['categoria', 'marca', 'buscar']))
                        <a href="#" id="clear-filters" 
                           class="flex items-center justify-center gap-2 w-full py-3 mt-6 text-sm text-red-500 font-bold bg-red-50 rounded-xl hover:bg-red-100 hover:shadow-sm transition-all duration-300 group">
                            <span class="material-symbols-outlined text-[20px] group-hover:rotate-180 transition-transform duration-500">restart_alt</span>
                            Limpiar Filtros
                        </a>
                    @endif
                </div>
            </aside>

            <main class="flex-1">
                <div id="loading-overlay" class="fixed inset-0 bg-white/80 backdrop-blur-sm z-50 flex items-center justify-center hidden">
                    <div class="text-center">
                        <div class="w-16 h-16 border-4 border-primary/20 border-t-primary rounded-full animate-spin mx-auto mb-4"></div>
                        <p class="text-agro-dark font-bold text-lg">Cargando productos...</p>
                    </div>
                </div>
                
                <div id="products-container">
                    @include('catalogo.partials.products', ['productos' => $productos])
                </div>
            </main>

        </div>
    </div>
</div>

<div id="toast-notification" class="fixed bottom-6 right-6 z-[100] transform transition-all duration-500 translate-y-24 opacity-0 pointer-events-none">
    <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-gray-100 p-4 flex items-center gap-4 min-w-[320px] max-w-md">
        <div id="toast-icon-container" class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 transition-colors">
            <span id="toast-icon" class="material-symbols-outlined text-2xl">check_circle</span>
        </div>
        
        <div class="flex-1">
            <h4 id="toast-title" class="font-bold text-gray-900 text-sm">Notificación</h4>
            <p id="toast-message" class="text-xs font-medium text-gray-500 mt-0.5">Mensaje de la notificación</p>
        </div>

        <button onclick="hideToast()" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100">
            <span class="material-symbols-outlined text-xl">close</span>
        </button>
    </div>
</div>

<style>
    /* Animaciones personalizadas que no cubre Tailwind por defecto */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.6s ease-out forwards;
    }
    @keyframes pulseSlow {
        0%, 100% { opacity: 0.3; transform: scale(1); }
        50% { opacity: 0.15; transform: scale(1.1); }
    }
    .animate-pulse-slow {
        animation: pulseSlow 8s infinite ease-in-out;
    }
    
    /* Scrollbar personalizada */
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #10B981; }
    
    #loading-overlay { transition: opacity 0.3s ease; }
    #loading-overlay.hidden { opacity: 0; pointer-events: none; }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .animate-spin { animation: spin 1s linear infinite; }
    #products-container { transition: opacity 0.3s ease; }
</style>

<script>
// Variables globales
let currentPage = 1;
let currentFilters = {
    buscar: "{{ request('buscar') }}",
    categoria: "{{ request('categoria') }}",
    marca: "{{ request('marca') }}",
    orden: "{{ request('orden', 'reciente') }}"
};

// ==========================================
// 🔔 LÓGICA DE NOTIFICACIONES (JS + TAILWIND)
// ==========================================
let toastTimeout;

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast-notification');
    const iconContainer = document.getElementById('toast-icon-container');
    const icon = document.getElementById('toast-icon');
    const title = document.getElementById('toast-title');
    const msg = document.getElementById('toast-message');

    // Resetear estilos base
    iconContainer.className = 'w-12 h-12 rounded-xl flex items-center justify-center shrink-0 transition-colors';
    
    // Configurar estilos según el tipo (usando clases de Tailwind)
    if (type === 'success') {
        iconContainer.classList.add('bg-green-50', 'text-green-600');
        icon.innerText = 'check_circle';
        title.innerText = '¡Éxito!';
        title.className = 'font-bold text-green-700 text-sm';
    } else if (type === 'error') {
        iconContainer.classList.add('bg-red-50', 'text-red-500');
        icon.innerText = 'error';
        title.innerText = 'Error';
        title.className = 'font-bold text-red-700 text-sm';
    } else {
        iconContainer.classList.add('bg-blue-50', 'text-blue-500');
        icon.innerText = 'info';
        title.innerText = 'Información';
        title.className = 'font-bold text-blue-700 text-sm';
    }

    msg.innerText = message;

    // Mostrar: Quitamos las clases que ocultan y trasladan
    toast.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');

    // Reiniciar temporizador si ya hay uno corriendo
    if (toastTimeout) clearTimeout(toastTimeout);

    // Ocultar automáticamente a los 4 segundos
    toastTimeout = setTimeout(() => {
        hideToast();
    }, 4000);
}

function hideToast() {
    const toast = document.getElementById('toast-notification');
    // Ocultar: Agregamos clases para trasladar hacia abajo y desvanecer
    toast.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
}

// ==========================================
// 🛒 LÓGICA DEL CARRITO (AJAX)
// ==========================================
function addToCart(productoId) {
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

    @auth
        fetch("{{ route('carrito.add') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json"
            },
            body: JSON.stringify({ 
                producto_id: productoId, 
                cantidad: 1 
            })
        })
        .then(response => {
            if (response.status === 401) {
                window.location.href = "{{ route('login') }}";
                return;
            }
            return response.json();
        })
        .then(data => {
            if (data && data.status === 'success') {
                // 1. Actualizar Badge del Header
                const badge = document.getElementById('cart-count-badge');
                if(badge) {
                    badge.innerText = data.cart_count;
                    badge.classList.remove('hidden');
                    // Efecto de rebote Tailwind
                    badge.classList.add('animate-bounce');
                    setTimeout(() => badge.classList.remove('animate-bounce'), 1000);
                }
                
                // 2. Mostrar Toast de Éxito
                showToast('Producto agregado al carrito correctamente.', 'success');
            } else {
                // Mostrar Toast de Error
                showToast(data.message || 'No se pudo agregar el producto.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error de conexión con el servidor.', 'error');
        });
    @else
        window.location.href = "{{ route('login') }}";
    @endauth
}

// ==========================================
// 🔍 LÓGICA DE FILTROS (EXISTENTE)
// ==========================================

function toggleFilters() {
    const panel = document.getElementById('filters-panel');
    panel.classList.toggle('hidden');
}

function updateBrowserURL(filters) {
    const params = new URLSearchParams();
    Object.entries(filters).forEach(([key, value]) => {
        if (value) params.set(key, value);
    });
    const newURL = params.toString() ? `${window.location.pathname}?${params}` : window.location.pathname;
    window.history.pushState(filters, '', newURL);
}

function loadProducts(page = 1, filters = {}) {
    const loader = document.getElementById('loading-overlay');
    const productsContainer = document.getElementById('products-container');
    
    loader.classList.remove('hidden');
    productsContainer.style.opacity = '0.5';
    
    currentPage = page;
    currentFilters = {...currentFilters, ...filters};
    
    updateBrowserURL(currentFilters);
    updateFilterStates(currentFilters);
    
    const params = new URLSearchParams(currentFilters);
    if (page > 1) params.set('page', page);
    
    fetch(`{{ route('catalogo') }}?${params.toString()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Error en la respuesta');
        return response.text();
    })
    .then(html => {
        productsContainer.innerHTML = html;
        setTimeout(() => { productsContainer.style.opacity = '1'; }, 100);
        document.getElementById('contenido-catalogo').scrollIntoView({ behavior: 'smooth' });
    })
    .catch(error => {
        console.error('Error:', error);
        productsContainer.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-xl p-8 text-center">
                <span class="material-symbols-outlined text-red-500 text-4xl mb-4">error</span>
                <h3 class="text-xl font-bold text-red-700 mb-2">Error al cargar productos</h3>
                <button onclick="location.reload()" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">Recargar Página</button>
            </div>
        `;
    })
    .finally(() => {
        setTimeout(() => { 
            loader.classList.add('hidden'); 
            productsContainer.style.opacity = '1';
        }, 300);
    });
}

function updateFilterStates(filters) {
    document.querySelectorAll('[data-filter="categoria"]').forEach(link => {
        const value = link.getAttribute('data-value');
        const isActive = filters.categoria === value;
        link.classList.toggle('bg-primary', isActive);
        link.classList.toggle('text-white', isActive);
        link.classList.toggle('shadow-lg', isActive);
        link.classList.toggle('scale-[1.02]', isActive);
        link.classList.toggle('bg-gray-50', !isActive);
        link.classList.toggle('text-gray-700', !isActive);
    });
    
    document.querySelectorAll('[data-filter="marca"]').forEach(link => {
        const value = link.getAttribute('data-value');
        const isActive = filters.marca === value;
        link.classList.toggle('bg-agro-dark', isActive);
        link.classList.toggle('text-white', isActive);
        link.classList.toggle('bg-white', !isActive);
        link.classList.toggle('text-gray-600', !isActive);
    });
    
    const searchInput = document.getElementById('search-input');
    if(searchInput) searchInput.value = filters.buscar || '';
    
    document.querySelectorAll('select[name="orden"]').forEach(select => {
        select.value = filters.orden;
    });
    
    const filtersPanel = document.getElementById('filters-panel');
    const existingClearBtn = document.getElementById('clear-filters');
    const hasActiveFilters = filters.buscar || filters.categoria || filters.marca;
    
    if (hasActiveFilters && !existingClearBtn) {
        const clearLink = document.createElement('a');
        clearLink.id = 'clear-filters';
        clearLink.href = '#';
        clearLink.className = 'flex items-center justify-center gap-2 w-full py-3 mt-6 text-sm text-red-500 font-bold bg-red-50 rounded-xl hover:bg-red-100 hover:shadow-sm transition-all duration-300 group';
        clearLink.innerHTML = `<span class="material-symbols-outlined text-[20px] group-hover:rotate-180 transition-transform duration-500">restart_alt</span> Limpiar Filtros`;
        clearLink.addEventListener('click', clearFilters);
        filtersPanel.appendChild(clearLink);
    } else if (!hasActiveFilters && existingClearBtn) {
        existingClearBtn.remove();
    }
}

function clearFilters(e) {
    if(e) e.preventDefault();
    currentFilters = { orden: currentFilters.orden };
    loadProducts(1, currentFilters);
}

window.catalogoClearFilters = clearFilters;

document.addEventListener('DOMContentLoaded', function() {
    
    document.addEventListener('click', function(e) {
        if (e.target.matches('.filter-link') || e.target.closest('.filter-link')) {
            e.preventDefault();
            const link = e.target.matches('.filter-link') ? e.target : e.target.closest('.filter-link');
            const filter = link.getAttribute('data-filter');
            const value = link.getAttribute('data-value');
            
            currentFilters[filter] = (currentFilters[filter] === value) ? '' : value;
            loadProducts(1, currentFilters);
        }
        
        if (e.target.matches('.pagination a') || e.target.closest('.pagination a')) {
            e.preventDefault();
            const link = e.target.matches('.pagination a') ? e.target : e.target.closest('.pagination a');
            const url = new URL(link.href);
            const page = url.searchParams.get('page') || 1;
            loadProducts(page);
        }
    });

    window.addEventListener('catalogo:orden-change', function(e) {
        if (e.detail && e.detail.value) {
            currentFilters.orden = e.detail.value;
            loadProducts(1, currentFilters);
        }
    });
    
    let searchTimeout;
    const searchInput = document.getElementById('search-input');
    if(searchInput) {
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentFilters.buscar = e.target.value;
                loadProducts(1, currentFilters);
            }, 500);
        });
    }
    
    const clearBtn = document.getElementById('clear-filters');
    if (clearBtn) {
        clearBtn.addEventListener('click', clearFilters);
    }
    
    window.addEventListener('popstate', function(event) {
        if (event.state) {
            currentFilters = event.state;
            loadProducts(1, currentFilters);
        }
    });
});
</script>

@endsection