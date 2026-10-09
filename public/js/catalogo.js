// public/js/catalogo.js - Lógica interactiva del catálogo Agrosuministros Rodríguez

let currentPage = 1;
let currentFilters = {};
let toastTimeout;

document.addEventListener('DOMContentLoaded', function() {
    // Cargar variables inyectadas desde Blade
    if (window.CatalogoConfig) {
        currentFilters = Object.assign({}, window.CatalogoConfig.filters);
    }

    // Event Delegation para clics en la página
    document.addEventListener('click', function(e) {
        // Clic en filtros de categoría o marca
        const filterLink = e.target.closest('.filter-link');
        if (filterLink) {
            e.preventDefault();
            const filter = filterLink.getAttribute('data-filter');
            const value = filterLink.getAttribute('data-value') || '';
            
            // Si el valor ya está activo y no es vacío, lo desactivamos; de lo contrario, lo activamos
            if (currentFilters[filter] === value && value !== '') {
                currentFilters[filter] = '';
            } else {
                currentFilters[filter] = value;
            }
            loadProducts(1, currentFilters);
            return;
        }
        
        // Clic en paginación
        const paginationLink = e.target.closest('.pagination a');
        if (paginationLink) {
            e.preventDefault();
            try {
                const url = new URL(paginationLink.href);
                const page = url.searchParams.get('page') || 1;
                loadProducts(page, currentFilters);
            } catch (err) {
                console.error('Error parseando link de paginación:', err);
            }
            return;
        }
    });

    // Escuchar cambios de ordenamiento
    window.addEventListener('catalogo:orden-change', function(e) {
        if (e.detail && e.detail.value) {
            currentFilters.orden = e.detail.value;
            loadProducts(1, currentFilters);
        }
    });
    
    // Búsqueda en vivo (Debounce)
    let searchTimeout;
    const searchInput = document.getElementById('search-input');
    const searchClearBtn = document.getElementById('search-clear-btn');

    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const val = e.target.value;
            if (searchClearBtn) {
                searchClearBtn.classList.toggle('hidden', val.trim() === '');
            }
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentFilters.buscar = val.trim();
                loadProducts(1, currentFilters);
            }, 400);
        });
    }

    if (searchClearBtn) {
        searchClearBtn.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            searchClearBtn.classList.add('hidden');
            currentFilters.buscar = '';
            loadProducts(1, currentFilters);
        });
    }
    
    // Navegación con los botones Atrás/Adelante del navegador
    window.addEventListener('popstate', function(event) {
        if (event.state) {
            currentFilters = event.state;
            loadProducts(1, currentFilters);
        }
    });

    // Inicializar estados de los filtros visuales
    updateFilterStates(currentFilters);
});

// ==========================================
// 🛒 LÓGICA DEL CARRITO (AJAX)
// ==========================================
function addToCart(productoId) {
    if (!window.CatalogoConfig || !window.CatalogoConfig.isAuth) {
        showToast('Debes iniciar sesión para comprar', 'info');
        setTimeout(() => {
            window.location.href = (window.CatalogoConfig && window.CatalogoConfig.routes.login) ? window.CatalogoConfig.routes.login : '/login';
        }, 1200);
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
                badgeContainer.classList.remove('hidden');
                
                badge.classList.add('animate-bounce');
                setTimeout(() => badge.classList.remove('animate-bounce'), 1000);
            }
            showToast(data.message || 'Producto agregado al carrito con éxito.', 'success');
        } else {
            showToast(data ? data.message : 'No se pudo agregar el producto.', 'error');
        }
    })
    .catch(error => {
        console.error('Error al agregar al carrito:', error);
        showToast('Error de conexión con el servidor.', 'error');
    });
}

// ==========================================
// 🔍 LÓGICA DE FILTROS Y AJAX
// ==========================================
function toggleFilters() {
    const panel = document.getElementById('filters-panel');
    if (panel) {
        panel.classList.toggle('hidden');
    }
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
    
    if (loader) loader.classList.remove('hidden');
    if (productsContainer) productsContainer.style.opacity = '0.4';
    
    currentPage = page;
    currentFilters = Object.assign({}, currentFilters, filters);
    
    updateBrowserURL(currentFilters);
    updateFilterStates(currentFilters);
    
    const params = new URLSearchParams();
    Object.entries(currentFilters).forEach(([key, val]) => {
        if (val) params.set(key, val);
    });
    if (page > 1) params.set('page', page);
    
    const url = `${window.CatalogoConfig.routes.catalogo}?${params.toString()}`;

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Error al consultar productos');
        return response.text();
    })
    .then(html => {
        if (productsContainer) {
            productsContainer.innerHTML = html;
            setTimeout(() => { productsContainer.style.opacity = '1'; }, 50);
        }
        const contenido = document.getElementById('contenido-catalogo');
        if (contenido) {
            contenido.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (productsContainer) {
            productsContainer.innerHTML = `
                <div class="bg-white border border-rose-200 rounded-2xl p-10 text-center shadow-xs">
                    <div class="size-14 bg-rose-50 rounded-2xl flex items-center justify-center mx-auto mb-3 text-rose-500">
                        <span class="material-symbols-outlined text-3xl">error_outline</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-800 mb-1">Ocurrió un problema al cargar los insumos</h3>
                    <p class="text-xs text-slate-500 mb-5">Por favor intenta de nuevo o recarga la página.</p>
                    <button onclick="loadProducts(1, currentFilters)" class="px-5 py-2.5 bg-[#1e549e] text-white font-bold text-xs rounded-xl hover:bg-[#3492cf] transition-colors shadow-xs">
                        Reintentar
                    </button>
                </div>
            `;
        }
    })
    .finally(() => {
        setTimeout(() => { 
            if (loader) loader.classList.add('hidden'); 
            if (productsContainer) productsContainer.style.opacity = '1';
        }, 200);
    });
}

function updateFilterStates(filters) {
    // 1. Categorías
    document.querySelectorAll('[data-filter="categoria"]').forEach(link => {
        const value = link.getAttribute('data-value') || "";
        const filterValue = filters.categoria || "";
        const isActive = (filterValue.toString() === value.toString());
        
        // Clases del enlace
        link.classList.toggle('bg-[#1e549e]', isActive);
        link.classList.toggle('text-white', isActive);
        link.classList.toggle('font-bold', isActive);
        link.classList.toggle('shadow-2xs', isActive);
        
        link.classList.toggle('text-slate-700', !isActive);
        link.classList.toggle('hover:text-[#1e549e]', !isActive);
        link.classList.toggle('hover:bg-[#e5f2fb]/60', !isActive);
        link.classList.toggle('font-medium', !isActive);
        
        // Badge de cantidad
        const countBadge = link.querySelector('.category-count-badge');
        if (countBadge) {
            countBadge.classList.toggle('bg-white/20', isActive);
            countBadge.classList.toggle('text-white', isActive);
            countBadge.classList.toggle('bg-slate-100', !isActive);
            countBadge.classList.toggle('text-slate-500', !isActive);
        }
    });
    
    // 2. Marcas
    document.querySelectorAll('[data-filter="marca"]').forEach(link => {
        const value = link.getAttribute('data-value') || "";
        const filterValue = filters.marca || "";
        const isActive = (filterValue.toString() === value.toString());

        link.classList.toggle('bg-[#1e549e]', isActive);
        link.classList.toggle('text-white', isActive);
        link.classList.toggle('border-[#1e549e]', isActive);
        link.classList.toggle('shadow-2xs', isActive);
        
        link.classList.toggle('bg-slate-50', !isActive);
        link.classList.toggle('text-slate-600', !isActive);
        link.classList.toggle('border-slate-200', !isActive);
        link.classList.toggle('hover:border-[#3492cf]', !isActive);
        link.classList.toggle('hover:text-[#1e549e]', !isActive);
        link.classList.toggle('hover:bg-white', !isActive);

        const checkIcon = link.querySelector('.brand-check-icon');
        if (checkIcon) {
            checkIcon.classList.toggle('hidden', !isActive);
        }
    });
    
    // 3. Sincronizar input de búsqueda
    const searchInput = document.getElementById('search-input');
    if (searchInput) searchInput.value = filters.buscar || '';
    
    const searchClearBtn = document.getElementById('search-clear-btn');
    if (searchClearBtn) {
        searchClearBtn.classList.toggle('hidden', !filters.buscar);
    }
    
    // 4. Sincronizar select de orden
    document.querySelectorAll('select[name="orden"]').forEach(select => {
        select.value = filters.orden || 'reciente';
    });

    // 5. Botón de limpiar filtros en sidebar
    const clearContainer = document.getElementById('clear-filters-container');
    const hasActiveFilters = Boolean(filters.buscar || filters.categoria || filters.marca);
    if (clearContainer) {
        if (hasActiveFilters) {
            clearContainer.classList.remove('hidden');
        } else {
            clearContainer.classList.add('hidden');
        }
    }
    
    // 6. Contador en botón móvil
    const mobileFilterBadge = document.getElementById('mobile-filter-count');
    if (mobileFilterBadge) {
        let count = 0;
        if (filters.buscar) count++;
        if (filters.categoria) count++;
        if (filters.marca) count++;
        if (count > 0) {
            mobileFilterBadge.textContent = count;
            mobileFilterBadge.classList.remove('hidden');
        } else {
            mobileFilterBadge.classList.add('hidden');
        }
    }
}

function removeFilter(filterName) {
    if (filterName === 'buscar') {
        currentFilters.buscar = '';
        const searchInput = document.getElementById('search-input');
        if (searchInput) searchInput.value = '';
        const searchClearBtn = document.getElementById('search-clear-btn');
        if (searchClearBtn) searchClearBtn.classList.add('hidden');
    } else {
        currentFilters[filterName] = '';
    }
    loadProducts(1, currentFilters);
}

function clearFilters(e) {
    if(e) e.preventDefault();
    currentFilters = { orden: currentFilters.orden || 'reciente' };
    const searchInput = document.getElementById('search-input');
    if (searchInput) searchInput.value = '';
    const searchClearBtn = document.getElementById('search-clear-btn');
    if (searchClearBtn) searchClearBtn.classList.add('hidden');
    loadProducts(1, currentFilters);
}

// ==========================================
// 🔔 LÓGICA DE NOTIFICACIONES (TOAST)
// ==========================================
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast-notification');
    const iconContainer = document.getElementById('toast-icon-container');
    const icon = document.getElementById('toast-icon');
    const title = document.getElementById('toast-title');
    const msg = document.getElementById('toast-message');

    if (!toast || !iconContainer || !icon || !title || !msg) return;

    iconContainer.className = 'w-11 h-11 rounded-xl flex items-center justify-center shrink-0 transition-colors';
    
    if (type === 'success') {
        iconContainer.className += ' bg-emerald-50 text-emerald-600 border border-emerald-100';
        icon.innerText = 'check_circle';
        title.innerText = '¡Operación Exitosa!';
        title.className = 'font-bold text-slate-800 text-sm';
    } else if (type === 'error') {
        iconContainer.className += ' bg-rose-50 text-rose-500 border border-rose-100';
        icon.innerText = 'error';
        title.innerText = 'Atención';
        title.className = 'font-bold text-slate-800 text-sm';
    } else {
        iconContainer.className += ' bg-[#e5f2fb] text-[#1e549e] border border-[#aad6f3]/40';
        icon.innerText = 'info';
        title.innerText = 'Información';
        title.className = 'font-bold text-slate-800 text-sm';
    }

    msg.innerText = message;
    toast.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');

    if (toastTimeout) clearTimeout(toastTimeout);
    toastTimeout = setTimeout(() => { hideToast(); }, 3500);
}

function hideToast() {
    const toast = document.getElementById('toast-notification');
    if (toast) {
        toast.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
    }
}

// ==========================================
// ❤️ LÓGICA DE FAVORITOS
// ==========================================
function toggleCatalogoFavorito(productoId, botonElement, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    if (!window.CatalogoConfig || !window.CatalogoConfig.isAuth) {
        showToast('Debes iniciar sesión para guardar favoritos', 'info');
        setTimeout(() => {
            window.location.href = (window.CatalogoConfig && window.CatalogoConfig.routes.login) ? window.CatalogoConfig.routes.login : '/login';
        }, 1200);
        return;
    }

    const icono = botonElement.querySelector('.material-symbols-outlined');
    if (icono) {
        icono.classList.add('scale-125');
        setTimeout(() => icono.classList.remove('scale-125'), 200);
    }
    botonElement.disabled = true;

    fetch("/favoritos/toggle", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": window.CatalogoConfig.csrfToken,
            "Accept": "application/json"
        },
        body: JSON.stringify({ producto_id: productoId })
    })
    .then(async response => {
        if (response.status === 401) {
            showToast('Debes iniciar sesión para guardar favoritos', 'info');
            setTimeout(() => {
                window.location.href = window.CatalogoConfig.routes.login;
            }, 1200);
            return Promise.reject('Requiere autenticación');
        }
        return response.json();
    })
    .then(data => {
        if (data && data.status === 'success') {
            if (data.is_favorite) {
                botonElement.className = 'btn-favorito-catalogo flex items-center justify-center size-8.5 rounded-full transition-all duration-300 shadow-xs bg-red-50 text-red-500 shadow-md ring-1 ring-red-200';
                if (icono) {
                    icono.className = 'material-symbols-outlined text-[19px] transition-transform duration-200 pointer-events-none fill-1 text-red-500';
                }
                botonElement.setAttribute('title', 'Quitar de favoritos');
                showToast(data.message || '¡Guardado en tus favoritos!', 'success');
            } else {
                botonElement.className = 'btn-favorito-catalogo flex items-center justify-center size-8.5 rounded-full transition-all duration-300 shadow-xs bg-white/95 text-slate-400 hover:text-red-500 hover:bg-white hover:shadow-md';
                if (icono) {
                    icono.className = 'material-symbols-outlined text-[19px] transition-transform duration-200 pointer-events-none text-slate-400';
                }
                botonElement.setAttribute('title', 'Añadir a favoritos');
                showToast(data.message || 'Eliminado de tus favoritos', 'info');
            }
        } else {
            showToast(data ? data.message : 'Error al actualizar favoritos', 'error');
        }
    })
    .catch(error => {
        if (error !== 'Requiere autenticación') {
            console.error('Error al alternar favorito:', error);
            showToast('Error de conexión con el servidor', 'error');
        }
    })
    .finally(() => {
        botonElement.disabled = false;
    });
}

// Hacer funciones accesibles globalmente
window.addToCart = addToCart;
window.toggleFilters = toggleFilters;
window.hideToast = hideToast;
window.showToast = showToast;
window.catalogoClearFilters = clearFilters;
window.removeFilter = removeFilter;
window.toggleCatalogoFavorito = toggleCatalogoFavorito;
window.loadProducts = loadProducts;