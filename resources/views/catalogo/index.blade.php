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
                <!-- Contenedor para el loader -->
                <div id="loading-overlay" class="fixed inset-0 bg-white/80 backdrop-blur-sm z-50 flex items-center justify-center hidden">
                    <div class="text-center">
                        <div class="w-16 h-16 border-4 border-primary/20 border-t-primary rounded-full animate-spin mx-auto mb-4"></div>
                        <p class="text-agro-dark font-bold text-lg">Cargando productos...</p>
                    </div>
                </div>
                
                <!-- Contenedor donde se cargarán los productos -->
                <div id="products-container">
                    @include('catalogo.partials.products', ['productos' => $productos])
                </div>
            </main>

        </div>
    </div>
</div>

<style>
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
    .custom-scrollbar::-webkit-scrollbar {
        width: 5px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent; 
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #d1d5db; 
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #10B981; 
    }
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

// Función para mostrar/ocultar filtros en móvil
function toggleFilters() {
    const panel = document.getElementById('filters-panel');
    panel.classList.toggle('hidden');
}

// Función para actualizar URL en el navegador sin recargar
function updateBrowserURL(filters) {
    const params = new URLSearchParams();
    
    Object.entries(filters).forEach(([key, value]) => {
        if (value) {
            params.set(key, value);
        }
    });
    
    const newURL = params.toString() ? `${window.location.pathname}?${params}` : window.location.pathname;
    window.history.pushState(filters, '', newURL);
}

// Función para cargar productos vía AJAX
function loadProducts(page = 1, filters = {}) {
    // Mostrar loader
    const loader = document.getElementById('loading-overlay');
    const productsContainer = document.getElementById('products-container');
    
    loader.classList.remove('hidden');
    productsContainer.style.opacity = '0.5';
    productsContainer.style.transition = 'opacity 0.3s';
    
    // Actualizar página actual
    currentPage = page;
    currentFilters = {...currentFilters, ...filters};
    
    // Actualizar URL del navegador
    updateBrowserURL(currentFilters);
    
    // Actualizar estado de los filtros visualmente
    updateFilterStates(currentFilters);
    
    // Construir URL para AJAX
    const params = new URLSearchParams(currentFilters);
    if (page > 1) {
        params.set('page', page);
    }
    
    // Hacer petición AJAX
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
        // Actualizar contenedor de productos
        productsContainer.innerHTML = html;
        
        // Restaurar opacidad
        setTimeout(() => {
            productsContainer.style.opacity = '1';
        }, 100);
        
        // Restaurar scroll al inicio de los productos
        document.getElementById('contenido-catalogo').scrollIntoView({ behavior: 'smooth' });
    })
    .catch(error => {
        console.error('Error:', error);
        productsContainer.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-xl p-8 text-center">
                <span class="material-symbols-outlined text-red-500 text-4xl mb-4">error</span>
                <h3 class="text-xl font-bold text-red-700 mb-2">Error al cargar productos</h3>
                <p class="text-red-600 mb-4">Intenta recargar la página o contactar al soporte.</p>
                <button onclick="location.reload()" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Recargar Página
                </button>
            </div>
        `;
    })
    .finally(() => {
        // Ocultar loader
        setTimeout(() => {
            loader.classList.add('hidden');
            productsContainer.style.opacity = '1';
        }, 300);
    });
}

// Función para actualizar estados visuales de los filtros
function updateFilterStates(filters) {
    // Actualizar enlaces de categorías
    document.querySelectorAll('[data-filter="categoria"]').forEach(link => {
        const value = link.getAttribute('data-value');
        const isActive = filters.categoria === value;
        
        link.classList.toggle('bg-primary', isActive);
        link.classList.toggle('text-white', isActive);
        link.classList.toggle('shadow-lg', isActive);
        link.classList.toggle('shadow-primary/30', isActive);
        link.classList.toggle('scale-[1.02]', isActive);
        link.classList.toggle('bg-gray-50', !isActive);
        link.classList.toggle('text-gray-700', !isActive);
        link.classList.toggle('hover:bg-gray-100', !isActive);
        link.classList.toggle('hover:text-primary', !isActive);
    });
    
    // Actualizar enlaces de marcas
    document.querySelectorAll('[data-filter="marca"]').forEach(link => {
        const value = link.getAttribute('data-value');
        const isActive = filters.marca === value;
        
        link.classList.toggle('bg-agro-dark', isActive);
        link.classList.toggle('text-white', isActive);
        link.classList.toggle('border-agro-dark', isActive);
        link.classList.toggle('shadow-md', isActive);
        link.classList.toggle('bg-white', !isActive);
        link.classList.toggle('text-gray-600', !isActive);
        link.classList.toggle('border-gray-200', !isActive);
        link.classList.toggle('hover:border-primary', !isActive);
        link.classList.toggle('hover:text-primary', !isActive);
        link.classList.toggle('hover:bg-primary/5', !isActive);
    });
    
    // Actualizar input de búsqueda
    document.getElementById('search-input').value = filters.buscar || '';
    
    // Actualizar select de orden
    document.querySelectorAll('select[name="orden"]').forEach(select => {
        select.value = filters.orden;
    });
    
    // Mostrar/ocultar botón de limpiar filtros
    const clearBtn = document.getElementById('clear-filters');
    const hasActiveFilters = filters.buscar || filters.categoria || filters.marca;
    
    if (hasActiveFilters && !clearBtn) {
        // Crear botón de limpiar filtros si no existe
        const filtersPanel = document.getElementById('filters-panel');
        const clearLink = document.createElement('a');
        clearLink.id = 'clear-filters';
        clearLink.href = '#';
        clearLink.className = 'flex items-center justify-center gap-2 w-full py-3 mt-6 text-sm text-red-500 font-bold bg-red-50 rounded-xl hover:bg-red-100 hover:shadow-sm transition-all duration-300 group';
        clearLink.innerHTML = `
            <span class="material-symbols-outlined text-[20px] group-hover:rotate-180 transition-transform duration-500">restart_alt</span>
            Limpiar Filtros
        `;
        clearLink.addEventListener('click', clearFilters);
        filtersPanel.appendChild(clearLink);
    } else if (!hasActiveFilters && clearBtn) {
        clearBtn.remove();
    }
}

// Función para limpiar todos los filtros
function clearFilters(e) {
    e.preventDefault();
    currentFilters = { orden: currentFilters.orden }; // Mantener solo el orden
    loadProducts(1, currentFilters);
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Delegación de eventos para los enlaces de filtro
    document.addEventListener('click', function(e) {
        // Filtros por categoría y marca
        if (e.target.matches('.filter-link') || e.target.closest('.filter-link')) {
            e.preventDefault();
            const link = e.target.matches('.filter-link') ? e.target : e.target.closest('.filter-link');
            const filter = link.getAttribute('data-filter');
            const value = link.getAttribute('data-value');
            
            // Si es el mismo valor, deseleccionar
            if (currentFilters[filter] === value) {
                currentFilters[filter] = '';
            } else {
                currentFilters[filter] = value;
            }
            
            loadProducts(1, currentFilters);
        }
        
        // Paginación (delegación para enlaces dinámicos)
        if (e.target.matches('.pagination a') || e.target.closest('.pagination a')) {
            e.preventDefault();
            const link = e.target.matches('.pagination a') ? e.target : e.target.closest('.pagination a');
            const url = new URL(link.href);
            const page = url.searchParams.get('page') || 1;
            loadProducts(page);
        }
    });
    
    // Búsqueda con debounce
    let searchTimeout;
    document.getElementById('search-input').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentFilters.buscar = e.target.value;
            loadProducts(1, currentFilters);
        }, 500); // 500ms de delay
    });
    
    // Ordenamiento
    document.querySelectorAll('select[name="orden"]').forEach(select => {
        select.addEventListener('change', function(e) {
            currentFilters.orden = e.target.value;
            loadProducts(1, currentFilters);
        });
    });
    
    // Limpiar filtros
    const clearBtn = document.getElementById('clear-filters');
    if (clearBtn) {
        clearBtn.addEventListener('click', clearFilters);
    }
    
    // Manejar botón de navegación atrás/adelante
    window.addEventListener('popstate', function(event) {
        if (event.state) {
            currentFilters = event.state;
            loadProducts(1, currentFilters);
        }
    });
});
</script>

<style>
    /* Agrega esto a tu archivo CSS principal */
#loading-overlay {
    transition: opacity 0.3s ease;
}

#loading-overlay.hidden {
    opacity: 0;
    pointer-events: none;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Transiciones suaves para los productos */
#products-container {
    transition: opacity 0.3s ease;
}
</style>

@endsection