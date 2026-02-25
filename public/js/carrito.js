// public/js/carrito.js

document.addEventListener("DOMContentLoaded", () => {
    if (window.CarritoConfig && window.CarritoConfig.sessionError) showToast(window.CarritoConfig.sessionError, 'error');
    if (window.CarritoConfig && window.CarritoConfig.sessionSuccess) showToast(window.CarritoConfig.sessionSuccess, 'success');
});

let itemToDeleteId = null;
let toastTimeout;

// ========================
// 🔔 Toasts
// ========================
window.showToast = function(message, type = 'success') {
    const toast = document.getElementById('toast-notification');
    if (!toast) return;
    
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
    } else {
        iconContainer.classList.add('bg-red-50', 'text-red-500');
        icon.innerText = 'error';
        title.innerText = 'Error';
        title.className = 'font-bold text-red-700 text-sm';
    }

    msg.innerText = message;
    toast.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');

    if (toastTimeout) clearTimeout(toastTimeout);
    toastTimeout = setTimeout(() => {
        toast.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
    }, 4000);
};

// ========================
// 🗑️ Modal y Eliminación por AJAX
// ========================
window.openDeleteModal = function(itemId) {
    itemToDeleteId = itemId;
    const modal = document.getElementById('delete-modal');
    const backdrop = document.getElementById('delete-modal-backdrop');
    const panel = document.getElementById('delete-modal-panel');

    if(modal) modal.classList.remove('hidden');
    setTimeout(() => {
        if(backdrop) backdrop.classList.remove('opacity-0');
        if(panel) panel.classList.remove('opacity-0', 'translate-y-8', 'sm:translate-y-0', 'sm:scale-95');
    }, 10);
};

window.closeDeleteModal = function() {
    itemToDeleteId = null;
    const modal = document.getElementById('delete-modal');
    const backdrop = document.getElementById('delete-modal-backdrop');
    const panel = document.getElementById('delete-modal-panel');

    if(backdrop) backdrop.classList.add('opacity-0');
    if(panel) panel.classList.add('opacity-0', 'translate-y-8', 'sm:translate-y-0', 'sm:scale-95');

    setTimeout(() => {
        if(modal) modal.classList.add('hidden');
    }, 300);
};

window.confirmDelete = function() {
    if (!itemToDeleteId) return;

    fetch(window.CarritoConfig.routes.remove, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": window.CarritoConfig.csrfToken
        },
        body: JSON.stringify({ id: itemToDeleteId })
    })
    .then(res => res.json())
    .then(data => {
        closeDeleteModal();
        if (data.status === 'success') {
            // Guardamos el mensaje en sessionStorage para que el toast 
            // sobreviva a la recarga de la página (opcional pero recomendado)
            sessionStorage.setItem('toastMessage', 'Producto eliminado correctamente');
            sessionStorage.setItem('toastType', 'success');
            
            // Recargamos la página inmediatamente
            location.reload();
        } else {
            showToast('Error al eliminar producto', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        closeDeleteModal();
        showToast('Error de conexión', 'error');
    });
};

// Pequeño agregado al inicio del archivo JS para mostrar el Toast de eliminación tras recargar
document.addEventListener("DOMContentLoaded", () => {
    // Revisar si venimos de eliminar un producto
    const pendingToast = sessionStorage.getItem('toastMessage');
    const pendingToastType = sessionStorage.getItem('toastType');
    if(pendingToast) {
        showToast(pendingToast, pendingToastType);
        sessionStorage.removeItem('toastMessage');
        sessionStorage.removeItem('toastType');
    }

    if (window.CarritoConfig.sessionError) showToast(window.CarritoConfig.sessionError, 'error');
    if (window.CarritoConfig.sessionSuccess) showToast(window.CarritoConfig.sessionSuccess, 'success');
});

// ========================
// 🛒 Sumar/Restar Cantidad en Vivo
// ========================
window.updateQty = function(itemId, change) {
    const input = document.getElementById(`qty-${itemId}`);
    if (!input) return;

    const currentQty = parseInt(input.value, 10);
    const newQty = currentQty + change;
    
    // No permitir bajar de 1
    if (isNaN(newQty) || newQty < 1) return;

    // 1. ACTUALIZAR INTERFAZ AL INSTANTE (Optimistic UI)
    input.value = newQty;
    recalculateTotals();

    // 2. ENVIAR A SERVIDOR SILENCIOSAMENTE
    fetch(window.CarritoConfig.routes.update, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": window.CarritoConfig.csrfToken,
            "Accept": "application/json"
        },
        body: JSON.stringify({ id: itemId, cantidad: newQty })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status !== 'success') {
            // Si el servidor falla (ej. No hay stock), revertir
            input.value = currentQty;
            recalculateTotals();
            showToast(data.message || 'Error de stock', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        // Si no hay internet o error grave, revertir
        input.value = currentQty;
        recalculateTotals();
        showToast('Error de conexión al actualizar', 'error');
    });
};

// ========================
// 🧮 Motor de Cálculo en Vivo (Precios y Header)
// ========================
function recalculateTotals() {
    let subtotal = 0;
    let uniqueItems = 0;
    let totalQuantity = 0; // Suma de cantidades para el Header
    
    const formatter = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    
    // 1. Actualizar cada fila individual
    document.querySelectorAll('.cart-item-row').forEach(row => {
        const price = parseFloat(row.getAttribute('data-price'));
        const qtyInput = row.querySelector('.qty-input');
        const qty = parseInt(qtyInput.value, 10) || 0;
        
        // Precio de la fila
        const itemTotal = price * qty;
        const displayTotal = row.querySelector('.item-total-display');
        if (displayTotal) displayTotal.innerText = '$' + formatter.format(itemTotal);
        
        subtotal += itemTotal;
        uniqueItems += 1;
        totalQuantity += qty; // Acumulamos para el ícono rojo
    });
    
    // 2. Actualizar Panel de Resumen
    const ivaPercentage = window.CarritoConfig.ivaPercentage || 16;
    const ivaAmount = subtotal * (ivaPercentage / 100);
    const total = subtotal + ivaAmount;
    
    const subtotalEl = document.getElementById('summary-subtotal');
    if(subtotalEl) subtotalEl.innerText = '$' + formatter.format(subtotal);
    
    const ivaEl = document.getElementById('summary-iva');
    if(ivaEl) ivaEl.innerText = '$' + formatter.format(ivaAmount);
    
    const totalEl = document.getElementById('summary-total');
    if(totalEl) totalEl.innerText = '$' + formatter.format(total);
    
    const titleText = document.getElementById('cart-items-count-text');
    if (titleText) titleText.innerHTML = `Tienes <span class="text-primary font-bold">${uniqueItems}</span> ${uniqueItems === 1 ? 'producto' : 'productos'} en tu lista`;
    
    const summaryText = document.getElementById('summary-items-count');
    if (summaryText) summaryText.innerText = `Subtotal (${uniqueItems} productos)`;

    // 3. ACTUALIZAR ÍCONO DEL HEADER
    const headerBadge = document.getElementById('cart-count-badge');
    const badgeContainer = document.getElementById('cart-badge-container'); // Opcional, dependiendo de tu header
    
    if (headerBadge) {
        headerBadge.innerText = totalQuantity;
        
        if (totalQuantity > 0) {
            headerBadge.classList.remove('hidden');
            if (badgeContainer) badgeContainer.classList.remove('hidden');
        } else {
            headerBadge.classList.add('hidden');
            if (badgeContainer) badgeContainer.classList.add('hidden');
        }
        
        // Efecto de "salto" para avisar al usuario
        headerBadge.classList.remove('animate-bounce');
        void headerBadge.offsetWidth; // Truco mágico para reiniciar animaciones en JS
        headerBadge.classList.add('animate-bounce');
        
        setTimeout(() => {
            headerBadge.classList.remove('animate-bounce');
        }, 1000);
    }
}