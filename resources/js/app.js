import './bootstrap';

/**
 * ==========================================
 * SISTEMA DE MENÚ MÓVIL (OFF-CANVAS)
 * ==========================================
 */

// 1. Referencias al DOM (Dinámicas para evitar errores si la página cambia)
const getMenuElements = () => ({
    overlay: document.getElementById('mobile-menu-overlay'),
    backdrop: document.getElementById('mobile-menu-backdrop'),
    panel: document.getElementById('mobile-menu-panel'),
    body: document.body
});

// 2. Función Privada: ABRIR el menú
const openMenu = () => {
    const { overlay, backdrop, panel, body } = getMenuElements();
    
    // Si no existen los elementos en esta página, salimos
    if (!overlay || !backdrop || !panel) return;

    overlay.classList.remove('hidden');
    body.style.overflow = 'hidden'; // Bloquear scroll del body

    // requestAnimationFrame asegura una animación fluida a 60fps
    requestAnimationFrame(() => {
        backdrop.classList.remove('opacity-0');
        panel.classList.remove('translate-x-full');
    });
};

// 3. Función Privada: CERRAR el menú
const closeMenu = () => {
    const { overlay, backdrop, panel, body } = getMenuElements();
    
    if (!overlay || !backdrop || !panel) return;

    backdrop.classList.add('opacity-0');
    panel.classList.add('translate-x-full');
    body.style.overflow = ''; // Restaurar scroll

    // Esperar a que termine la transición CSS (300ms) antes de ocultar el div
    setTimeout(() => {
        overlay.classList.add('hidden');
    }, 300);
};

// 4. API PÚBLICA (Accesible desde el HTML onclick="toggleMobileMenu()")
window.toggleMobileMenu = function() {
    const { overlay } = getMenuElements();
    
    // Protección por si el script corre en una página sin menú
    if (!overlay) return;

    if (overlay.classList.contains('hidden')) {
        openMenu();
    } else {
        closeMenu();
    }
};

// 5. EVENT LISTENERS (Mejoras de UX)

// Cerrar con la tecla ESC (Accesibilidad)
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const { overlay } = getMenuElements();
        if (overlay && !overlay.classList.contains('hidden')) {
            closeMenu();
        }
    }
});

// Cerrar automáticamente si se agranda la pantalla (Evita bugs visuales)
window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024) { // 1024px es el breakpoint 'lg' de Tailwind
        const { overlay } = getMenuElements();
        if (overlay && !overlay.classList.contains('hidden')) {
            closeMenu();
        }
    }
});