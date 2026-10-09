// public/js/carrito.js - Lógica y cálculos interactivos del Carrito Agrosuministros Rodríguez

document.addEventListener("DOMContentLoaded", () => {
    const pendingToast = sessionStorage.getItem('toastMessage');
    const pendingToastType = sessionStorage.getItem('toastType');
    if (pendingToast) {
        showToast(pendingToast, pendingToastType);
        sessionStorage.removeItem('toastMessage');
        sessionStorage.removeItem('toastType');
    }

    if (window.CarritoConfig && window.CarritoConfig.sessionError) showToast(window.CarritoConfig.sessionError, 'error');
    if (window.CarritoConfig && window.CarritoConfig.sessionSuccess) showToast(window.CarritoConfig.sessionSuccess, 'success');
});

let itemToDeleteId = null;
let toastTimeout;

// ========================
// 🔔 Toasts Notificaciones
// ========================
window.showToast = function(message, type = 'success') {
    const toast = document.getElementById('toast-notification');
    if (!toast) return;
    
    const iconContainer = document.getElementById('toast-icon-container');
    const icon = document.getElementById('toast-icon');
    const title = document.getElementById('toast-title');
    const msg = document.getElementById('toast-message');

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
    toastTimeout = setTimeout(() => {
        toast.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
    }, 3500);
};

window.hideToast = function() {
    const toast = document.getElementById('toast-notification');
    if (toast) toast.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
};

// ========================
// 🗑️ Modal y Eliminación por AJAX
// ========================
window.openDeleteModal = function(itemId) {
    itemToDeleteId = itemId;
    const modal = document.getElementById('delete-modal');
    const backdrop = document.getElementById('delete-modal-backdrop');
    const panel = document.getElementById('delete-modal-panel');

    if (modal) modal.classList.remove('hidden');
    setTimeout(() => {
        if (backdrop) backdrop.classList.remove('opacity-0');
        if (panel) panel.classList.remove('opacity-0', 'translate-y-8', 'sm:translate-y-0', 'sm:scale-95');
    }, 10);
};

window.closeDeleteModal = function() {
    itemToDeleteId = null;
    const modal = document.getElementById('delete-modal');
    const backdrop = document.getElementById('delete-modal-backdrop');
    const panel = document.getElementById('delete-modal-panel');

    if (backdrop) backdrop.classList.add('opacity-0');
    if (panel) panel.classList.add('opacity-0', 'translate-y-8', 'sm:translate-y-0', 'sm:scale-95');

    setTimeout(() => {
        if (modal) modal.classList.add('hidden');
    }, 250);
};

window.confirmDelete = function() {
    if (!itemToDeleteId) return;

    fetch(window.CarritoConfig.routes.remove, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": window.CarritoConfig.csrfToken,
            "Accept": "application/json"
        },
        body: JSON.stringify({ id: itemToDeleteId })
    })
    .then(res => res.json())
    .then(data => {
        closeDeleteModal();
        if (data.status === 'success') {
            sessionStorage.setItem('toastMessage', 'Insumo eliminado del carrito');
            sessionStorage.setItem('toastType', 'info');
            location.reload();
        } else {
            showToast(data.message || 'Error al eliminar el producto', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        closeDeleteModal();
        showToast('Error de conexión con el servidor', 'error');
    });
};

// ========================
// 🛒 Sumar/Restar Cantidad en Vivo
// ========================
window.updateQty = function(itemId, action) {
    const row = document.getElementById(`item-${itemId}`);
    const input = document.getElementById(`qty-${itemId}`);
    if (!input || !row) return;

    const step = parseFloat(row.getAttribute('data-step')) || 1;
    const min = parseFloat(row.getAttribute('data-min')) || 1;
    const currentQty = parseFloat(input.value) || min;

    let newQty = currentQty;

    if (action === 'add') {
        newQty += step;
    } else if (action === 'sub') {
        newQty -= step;
    }

    // Resolver decimales flotantes en Javascript
    newQty = Math.round(newQty * 1000) / 1000;

    // Si baja de la venta mínima permitida, confirmamos con el usuario si desea eliminarlo
    if (newQty < min) {
        openDeleteModal(itemId);
        return;
    }

    // 1. Optimistic UI update
    input.value = newQty;
    recalculateTotals();

    // 2. Envío silencioso al backend
    fetch(window.CarritoConfig.routes.update, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": window.CarritoConfig.csrfToken,
            "Accept": "application/json"
        },
        body: JSON.stringify({ id: itemId, cantidad: newQty })
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok || data.status !== 'success') {
            throw new Error(data.message || 'No hay suficiente stock disponible');
        }
    })
    .catch(err => {
        console.error('Error al actualizar cantidad:', err);
        input.value = currentQty;
        recalculateTotals();
        showToast(err.message, 'error');
    });
};

// ========================
// 🧮 Motor de Cálculo de Totales (USD y Bs.)
// ========================
function recalculateTotals() {
    let subtotal = 0;
    let uniqueItems = 0;
    
    const formatterUSD = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const formatterVES = new Intl.NumberFormat('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const tasaDolar = parseFloat(window.CarritoConfig.tasaDolar) || 1;

    // 1. Actualizar cada fila individual
    document.querySelectorAll('.cart-item-row').forEach(row => {
        const price = parseFloat(row.getAttribute('data-price')) || 0;
        const qtyInput = row.querySelector('.qty-input');
        const qty = parseFloat(qtyInput ? qtyInput.value : 0) || 0;
        
        const itemTotal = price * qty;
        const itemTotalBs = itemTotal * tasaDolar;

        const displayTotal = row.querySelector('.item-total-display');
        if (displayTotal) displayTotal.innerText = '$' + formatterUSD.format(itemTotal);

        const displayTotalBs = row.querySelector('.item-total-bs-display');
        if (displayTotalBs) displayTotalBs.innerText = 'Bs. ' + formatterVES.format(itemTotalBs);
        
        subtotal += itemTotal;
        uniqueItems += 1;
    });
    
    // 2. Actualizar Panel de Resumen
    const ivaPercentage = parseFloat(window.CarritoConfig.ivaPercentage) || 16;
    const ivaAmount = subtotal * (ivaPercentage / 100);
    const total = subtotal + ivaAmount;
    const totalBs = total * tasaDolar;
    
    const subtotalEl = document.getElementById('summary-subtotal');
    if (subtotalEl) subtotalEl.innerText = '$' + formatterUSD.format(subtotal);
    
    const ivaEl = document.getElementById('summary-iva');
    if (ivaEl) ivaEl.innerText = '$' + formatterUSD.format(ivaAmount);
    
    const totalEl = document.getElementById('summary-total');
    if (totalEl) totalEl.innerText = '$' + formatterUSD.format(total);

    const totalBsEl = document.getElementById('summary-total-bs');
    if (totalBsEl) totalBsEl.innerText = 'Bs. ' + formatterVES.format(totalBs);
    
    const titleText = document.getElementById('cart-items-count-text');
    if (titleText) titleText.innerHTML = `Tienes <span class="text-[#1e549e] font-black">${uniqueItems}</span> ${uniqueItems === 1 ? 'insumo' : 'insumos'} en tu lista`;
    
    const summaryText = document.getElementById('summary-items-count');
    if (summaryText) summaryText.innerText = `Subtotal (${uniqueItems} ${uniqueItems === 1 ? 'insumo' : 'insumos'})`;

    // 3. ACTUALIZAR ÍCONO DEL HEADER
    const headerBadge = document.getElementById('cart-count-badge');
    const badgeContainer = document.getElementById('cart-badge-container');
    
    if (headerBadge) {
        headerBadge.innerText = uniqueItems;
        
        if (uniqueItems > 0) {
            headerBadge.classList.remove('hidden');
            if (badgeContainer) badgeContainer.classList.remove('hidden');
        } else {
            headerBadge.classList.add('hidden');
            if (badgeContainer) badgeContainer.classList.add('hidden');
        }
        
        headerBadge.classList.remove('animate-bounce');
        void headerBadge.offsetWidth;
        headerBadge.classList.add('animate-bounce');
        
        setTimeout(() => {
            headerBadge.classList.remove('animate-bounce');
        }, 1000);
    }
}