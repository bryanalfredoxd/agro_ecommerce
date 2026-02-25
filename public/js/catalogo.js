// public/js/catalogo.js

let currentPage = 1;
let currentFilters = {};
let toastTimeout;

document.addEventListener('DOMContentLoaded', function() {
    // Cargar variables inyectadas desde Blade
    if (window.CatalogoConfig) {
        currentFilters = window.CatalogoConfig.filters;
    }

    // Event Delegation para clics en la página
    document.addEventListener('click', function(e) {
        // Clic en filtros
        if (e.target.matches('.filter-link') || e.target.closest('.filter-link')) {
            e.preventDefault();
            const link = e.target.matches('.filter-link') ? e.target : e.target.closest('.filter-link');
            const filter = link.getAttribute('data-filter');
            const value = link.getAttribute('data-value');
            
            currentFilters[filter] = (currentFilters[filter] === value) ? '' : value;
            loadProducts(1, currentFilters);
        }
        
        // Clic en paginación
        if (e.target.matches('.pagination a') || e.target.closest('.pagination a')) {
            e.preventDefault();
            const link = e.target.matches('.pagination a') ? e.target : e.target.closest('.pagination a');
            const url = new URL(link.href);
            const page = url.searchParams.get('page') || 1;
            loadProducts(page);
        }
    });

    // Escuchar cambios de orden
    window.addEventListener('catalogo:orden-change', function(e) {
        if (e.detail && e.detail.value) {
            currentFilters.orden = e.detail.value;
            loadProducts(1, currentFilters);
        }
    });
    
    // Búsqueda en vivo (Debounce)
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
    
    // Botón de limpiar filtros
    const clearBtn = document.getElementById('clear-filters');
    if (clearBtn) {
        clearBtn.addEventListener('click', clearFilters);
    }
    
    // Navegación con los botones Atrás/Adelante del navegador
    window.addEventListener('popstate', function(event) {
        if (event.state) {
            currentFilters = event.state;
            loadProducts(1, currentFilters);
        }
    });
});

// ==========================================
// 🛒 LÓGICA DEL CARRITO (AJAX)
// ==========================================
function addToCart(productoId) {
    if (!window.CatalogoConfig.isAuth) {
        window.location.href = window.CatalogoConfig.routes.login;
        return;
    }

    fetch(window.CatalogoConfig.routes.addCart, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": window.CatalogoConfig.csrfToken,
            "Accept": "application/json"
        },
        body: JSON.stringify({ producto_id: productoId, cantidad: 1 })
    })
    .then(response => {
        if (response.status === 401) {
            window.location.href = window.CatalogoConfig.routes.login;
            return;
        }
        return response.json();
    })
    .then(data => {
        if (data && data.status === 'success') {
            const badgeContainer = document.getElementById('cart-badge-container');
            const badge = document.getElementById('cart-count-badge');
            
            if(badge && badgeContainer) {
                badge.innerText = data.cart_count;
                badgeContainer.classList.remove('hidden'); // Mostramos el contenedor
                
                // Hacemos que rebote el span interno libremente sin romper la posición
                badge.classList.add('animate-bounce');
                setTimeout(() => badge.classList.remove('animate-bounce'), 1000);
            }
            showToast('Producto agregado al carrito correctamente.', 'success');
        } else {
            showToast(data.message || 'No se pudo agregar el producto.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error de conexión con el servidor.', 'error');
    });
}

// ==========================================
// 🔍 LÓGICA DE FILTROS Y AJAX
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
    
    fetch(`${window.CatalogoConfig.routes.catalogo}?${params.toString()}`, {
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
    // 1. Actualizar enlaces de categoría (Diseño sutil)
    document.querySelectorAll('[data-filter="categoria"]').forEach(link => {
        const value = link.getAttribute('data-value') || "";
        const filterValue = filters.categoria || "";
        const isActive = filterValue === value;
        
        // Clases principales del enlace
        link.classList.toggle('text-primary', isActive);
        link.classList.toggle('bg-primary/5', isActive);
        link.classList.toggle('text-gray-600', !isActive);
        link.classList.toggle('hover:text-agro-dark', !isActive);
        link.classList.toggle('hover:bg-gray-50', !isActive);
        
        // Clases del puntito indicador
        const dot = link.querySelector('div');
        if (dot) {
            dot.classList.toggle('bg-primary', isActive);
            dot.classList.toggle('bg-gray-300', !isActive);
            dot.classList.toggle('group-hover:bg-agro-dark', !isActive);
        }
    });
    
    // 2. Actualizar botones de marca (Píldoras responsive)
    document.querySelectorAll('[data-filter="marca"]').forEach(link => {
        const value = link.getAttribute('data-value') || "";
        const filterValue = filters.marca || "";
        const isActive = filterValue === value;

        // Clases cuando está activo (Seleccionado)
        link.classList.toggle('bg-agro-dark', isActive);
        link.classList.toggle('text-white', isActive);
        link.classList.toggle('border-agro-dark', isActive);
        link.classList.toggle('shadow-sm', isActive);
        
        // Clases cuando está inactivo
        link.classList.toggle('bg-white', !isActive);
        link.classList.toggle('text-gray-500', !isActive);
        link.classList.toggle('border-gray-200', !isActive);
        link.classList.toggle('hover:border-primary', !isActive);
        link.classList.toggle('hover:text-primary', !isActive);
        link.classList.toggle('hover:bg-primary/5', !isActive);
    });
    
    // 3. Sincronizar inputs de búsqueda y orden
    const searchInput = document.getElementById('search-input');
    if(searchInput) searchInput.value = filters.buscar || '';
    
    document.querySelectorAll('select[name="orden"]').forEach(select => {
        select.value = filters.orden || 'reciente';
    });

    // 4. Mostrar u ocultar el botón de limpiar filtros
    const clearContainer = document.getElementById('clear-filters-container');
    if (clearContainer) {
        const hasActiveFilters = filters.buscar || filters.categoria || filters.marca;
        if (hasActiveFilters) {
            clearContainer.classList.remove('hidden');
            clearContainer.classList.add('block');
        } else {
            clearContainer.classList.remove('block');
            clearContainer.classList.add('hidden');
        }
    }
}

function clearFilters(e) {
    if(e) e.preventDefault();
    currentFilters = { orden: currentFilters.orden };
    loadProducts(1, currentFilters);
}

// ==========================================
// 🔔 LÓGICA DE NOTIFICACIONES
// ==========================================
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast-notification');
    const iconContainer = document.getElementById('toast-icon-container');
    const icon = document.getElementById('toast-icon');
    const title = document.getElementById('toast-title');
    const msg = document.getElementById('toast-message');

    iconContainer.className = 'w-12 h-12 rounded-xl flex items-center justify-center shrink-0 transition-colors';
    
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
    toast.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');

    if (toastTimeout) clearTimeout(toastTimeout);
    toastTimeout = setTimeout(() => { hideToast(); }, 4000);
}

function hideToast() {
    const toast = document.getElementById('toast-notification');
    toast.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
}

// Hacer funciones accesibles globalmente si son llamadas directamente desde HTML (onclick)
window.addToCart = addToCart;
window.toggleFilters = toggleFilters;
window.hideToast = hideToast;
window.catalogoClearFilters = clearFilters;