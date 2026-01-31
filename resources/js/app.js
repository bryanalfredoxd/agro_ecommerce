import './bootstrap';

/**
 * ==========================================
 * SISTEMA DE MENÚ MÓVIL (OFF-CANVAS)
 * ==========================================
 */
const getMenuElements = () => ({
    overlay: document.getElementById('mobile-menu-overlay'),
    backdrop: document.getElementById('mobile-menu-backdrop'),
    panel: document.getElementById('mobile-menu-panel'),
    body: document.body
});

const openMenu = () => {
    const { overlay, backdrop, panel, body } = getMenuElements();
    if (!overlay) return;
    overlay.classList.remove('hidden');
    body.style.overflow = 'hidden';
    requestAnimationFrame(() => {
        backdrop.classList.remove('opacity-0');
        panel.classList.remove('translate-x-full');
    });
};

const closeMenu = () => {
    const { overlay, backdrop, panel, body } = getMenuElements();
    if (!overlay) return;
    backdrop.classList.add('opacity-0');
    panel.classList.add('translate-x-full');
    body.style.overflow = '';
    setTimeout(() => {
        overlay.classList.add('hidden');
    }, 300);
};

window.toggleMobileMenu = function() {
    const { overlay } = getMenuElements();
    if (!overlay) return;
    if (overlay.classList.contains('hidden')) openMenu();
    else closeMenu();
};

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const { overlay } = getMenuElements();
        if (overlay && !overlay.classList.contains('hidden')) closeMenu();
    }
});

window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024) {
        const { overlay } = getMenuElements();
        if (overlay && !overlay.classList.contains('hidden')) closeMenu();
    }
});

/**
 * ==========================================
 * LÓGICA DE REGISTRO (GLIDER + VALIDACIONES)
 * ==========================================
 */

// Función Global para el Glider (Accesible desde HTML)
window.moveGlider = function(index, tipo) {
    const glider = document.getElementById('tabGlider');
    if (!glider) return; // Si no estamos en la página de registro, salir

    // Mover el fondo blanco
    glider.style.transform = `translateX(${index * 100}%)`;

    // Obtener referencias
    const labelNombre = document.getElementById('label_nombre');
    const labelDoc = document.getElementById('label_documento');
    const containerApellido = document.getElementById('field_apellido_container');
    const inputApellido = document.getElementById('input_apellido');
    
    if (tipo === 'juridico') {
        labelNombre.innerText = "Razón Social";
        labelDoc.innerText = "RIF";
        
        // Colapsar Apellido
        containerApellido.classList.remove('w-full', 'sm:w-1/3', 'ml-4');
        containerApellido.classList.add('w-0', 'opacity-0', 'p-0', 'm-0');
        
        // Desactivar input para validaciones
        setTimeout(() => inputApellido.disabled = true, 300);

    } else {
        labelNombre.innerText = (tipo === 'finca') ? "Nombre Productor" : "Nombre";
        labelDoc.innerText = (tipo === 'finca') ? "Cédula / RIF" : "Cédula";
        
        // Expandir Apellido
        inputApellido.disabled = false;
        containerApellido.classList.remove('w-0', 'opacity-0', 'p-0', 'm-0');
        containerApellido.classList.add('w-full', 'sm:w-1/3');
    }
};

// INICIALIZACIÓN Y VALIDACIÓN DEL FORMULARIO
document.addEventListener("DOMContentLoaded", () => {
    
    // 1. Restaurar Estado del Glider (Si Laravel devolvió old input)
    const oldTipoInput = document.getElementById('old_tipo_cliente');
    if (oldTipoInput) {
        const oldTipo = oldTipoInput.value;
        if(oldTipo === 'juridico') window.moveGlider(1, 'juridico');
        else if(oldTipo === 'finca_productor') window.moveGlider(2, 'finca');
        else window.moveGlider(0, 'natural');
    }

    // 2. Lógica de Restricción de Inputs (Evitar escribir números en nombres)
    const inputsTexto = ['input_nombre', 'input_apellido'];
    inputsTexto.forEach(id => {
        const input = document.getElementById(id);
        if(input) {
            input.addEventListener('input', function(e) {
                // Reemplaza cualquier cosa que NO sea letra o espacio con vacío
                this.value = this.value.replace(/[^a-zA-ZÀ-ÿ\u00f1\u00d1\s]/g, '');
            });

            // Validación al salir del campo (Blur)
            input.addEventListener('blur', function() {
                const errorMsg = document.getElementById(id === 'input_nombre' ? 'error_nombre' : 'error_apellido');
                if (this.value.trim().length < 2 && !this.disabled) {
                    this.classList.add('border-red-300', 'bg-red-50');
                    errorMsg.classList.remove('hidden');
                } else {
                    this.classList.remove('border-red-300', 'bg-red-50');
                    errorMsg.classList.add('hidden');
                }
            });
        }
    });

    // 3. Lógica de Teléfono (Solo números y longitud)
    const inputTel = document.getElementById('input_telefono');
    if (inputTel) {
        inputTel.addEventListener('input', function() {
            // Solo permitir números
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        inputTel.addEventListener('blur', function() {
            const errorMsg = document.getElementById('error_telefono');
            if (this.value.length < 7) { // Validación básica de longitud
                this.parentElement.classList.add('border-red-300', 'ring-2', 'ring-red-100');
                errorMsg.classList.remove('hidden');
            } else {
                this.parentElement.classList.remove('border-red-300', 'ring-2', 'ring-red-100');
                errorMsg.classList.add('hidden');
            }
        });
    }

    // 4. Lógica de Cédula (Solo números)
    const inputDoc = document.getElementById('input_documento');
    if(inputDoc) {
        inputDoc.addEventListener('input', function() {
             // Permitir numeros y guiones si quieres, aqui solo numeros
             this.value = this.value.replace(/[^0-9]/g, '');
        });
    }

    // 5. Validación de Contraseñas
    const pass = document.getElementById('input_password');
    const confirm = document.getElementById('input_confirm');
    
    if(pass && confirm) {
        confirm.addEventListener('input', validatePasswordMatch);
        pass.addEventListener('input', validatePasswordMatch); // También validar si cambia la original
    }

    function validatePasswordMatch() {
        const errorMsg = document.getElementById('error_confirm');
        const errorPass = document.getElementById('error_password');

        // Validar longitud min
        if(pass.value.length > 0 && pass.value.length < 8) {
            errorPass.classList.remove('hidden');
        } else {
            errorPass.classList.add('hidden');
        }

        // Validar coincidencia
        if (confirm.value.length > 0 && pass.value !== confirm.value) {
            confirm.classList.add('border-red-300', 'bg-red-50');
            errorMsg.classList.remove('hidden');
        } else {
            confirm.classList.remove('border-red-300', 'bg-red-50');
            errorMsg.classList.add('hidden');
        }
    }
});