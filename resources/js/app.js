import './bootstrap';

/**
 * ==========================================
 * CONFIGURACIÓN LEAFLET (MAPA)
 * ==========================================
 */
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

// Importación manual de imágenes para evitar errores 404 en producción
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

// Fix para que Leaflet encuentre las imágenes correctamente con Vite
delete L.Icon.Default.prototype._getIconUrl;

L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

// Hacemos 'L' global para usarlo en los scripts de Blade (perfil, contacto, etc.)
window.L = L;

/**
 * ==========================================
 * OTRAS LIBRERÍAS
 * ==========================================
 */
import intlTelInput from 'intl-tel-input';
import Swal from 'sweetalert2';

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
 * LÓGICA DEL CATÁLOGO (FILTROS)
 * ==========================================
 */
window.toggleFilters = function() {
    const panel = document.getElementById('filters-panel');
    if (!panel) return;
    
    panel.classList.toggle('hidden');
    if (!panel.classList.contains('hidden')) {
        panel.classList.add('animate-fade-in-up');
        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        panel.classList.remove('animate-fade-in-up');
    }
};

/**
 * ==========================================
 * LÓGICA DE REGISTRO (GLIDER + LIBRERÍA TEL)
 * ==========================================
 */

// 1. ANIMACIÓN DE TABS (Glider)
window.moveGlider = function(index, tipo) {
    const glider = document.getElementById('tabGlider');
    
    // --- CORRECCIÓN VITAL: Si no estamos en la página de registro, SALIR ---
    if (!glider) return; 

    const labelNombre = document.getElementById('label_nombre');
    const labelDoc = document.getElementById('label_documento');
    const containerApellido = document.getElementById('field_apellido_container');
    const inputApellido = document.getElementById('input_apellido');
    
    // Doble chequeo de seguridad
    if (!labelNombre || !labelDoc || !containerApellido || !inputApellido) return;

    glider.style.transform = `translateX(${index * 100}%)`;
    
    if (tipo === 'juridico') {
        labelNombre.innerText = "Razón Social";
        labelDoc.innerText = "RIF";
        containerApellido.classList.remove('w-full', 'sm:w-1/3', 'ml-4');
        containerApellido.classList.add('w-0', 'opacity-0', 'p-0', 'm-0');
        setTimeout(() => inputApellido.disabled = true, 300);
    } else {
        labelNombre.innerText = (tipo === 'finca') ? "Nombre Productor" : "Nombre";
        labelDoc.innerText = (tipo === 'finca') ? "Cédula / RIF" : "Cédula";
        inputApellido.disabled = false;
        containerApellido.classList.remove('w-0', 'opacity-0', 'p-0', 'm-0');
        containerApellido.classList.add('w-full', 'sm:w-1/3');
    }
};

document.addEventListener("DOMContentLoaded", () => {
    
    // --- A. CONFIGURACIÓN DE LA LIBRERÍA DE TELÉFONO ---
    const inputPhone = document.querySelector("#phone");
    const errorMsg = document.querySelector("#error_telefono");
    const hiddenPhone = document.querySelector("#hidden_telefono");
    const hiddenCode = document.querySelector("#hidden_codigo_pais");

    if (inputPhone) {
        const iti = intlTelInput(inputPhone, {
            initialCountry: "ve",
            preferredCountries: [
                "ve", "co", "br", "us", 
                "ar", "bo", "cl", "ec", "pe", "py", "uy", 
                "cr", "cu", "gt", "hn", "mx", "ni", "pa", "do", "sv", 
                "de", "es", "fr", "it", "pt", "gb", 
                "cn", "tr"
            ],
            separateDialCode: true, 
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.6.0/build/js/utils.js",
        });

        const updateHiddenInputs = () => {
            hiddenCode.value = "+" + iti.getSelectedCountryData().dialCode;
            hiddenPhone.value = inputPhone.value.replace(/[^0-9]/g, ''); 
        };

        const validatePhone = () => {
            updateHiddenInputs();
            const cleanNumber = inputPhone.value.replace(/[^0-9]/g, '');

            if (!cleanNumber) {
                inputPhone.parentElement.classList.remove('border-red-300', 'ring-2', 'ring-red-100');
                if(errorMsg) errorMsg.classList.add('hidden');
                return;
            }

            const isStrictlyValid = iti.isValidNumber();
            const isLooselyValid = cleanNumber.length > 6; 

            if (isStrictlyValid || isLooselyValid) {
                inputPhone.parentElement.classList.remove('border-red-300', 'ring-2', 'ring-red-100');
                if(errorMsg) errorMsg.classList.add('hidden');
            } else {
                inputPhone.parentElement.classList.add('border-red-300', 'ring-2', 'ring-red-100');
                if(errorMsg) {
                    errorMsg.classList.remove('hidden');
                    errorMsg.innerHTML = "Número muy corto";
                }
            }
        };

        inputPhone.addEventListener('blur', validatePhone);
        
        inputPhone.addEventListener('input', function() {
             const dialCode = iti.getSelectedCountryData().dialCode;
             const val = this.value.replace(/\D/g, ''); 
             
             if (val.startsWith(dialCode) && val.length > dialCode.length + 4) {
             }
             updateHiddenInputs();
        });
        
        inputPhone.addEventListener('countrychange', updateHiddenInputs); 
    }

    // --- B. RESTAURAR GLIDER ---
    const oldTipoInput = document.getElementById('old_tipo_cliente');
    
    // CORRECCIÓN: Solo intentar restaurar si el input existe
    if (oldTipoInput) {
        const oldTipo = oldTipoInput.value;
        if(oldTipo === 'juridico') window.moveGlider(1, 'juridico');
        else if(oldTipo === 'finca_productor') window.moveGlider(2, 'finca');
        else window.moveGlider(0, 'natural');
    }

    // --- C. VALIDACIÓN DE TEXTO ---
    const inputsTexto = ['input_nombre', 'input_apellido'];
    inputsTexto.forEach(id => {
        const input = document.getElementById(id);
        if(input) {
            input.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^a-zA-ZÀ-ÿ\u00f1\u00d1\s]/g, '');
            });

            input.addEventListener('blur', function() {
                const errorMsg = document.getElementById(id === 'input_nombre' ? 'error_nombre' : 'error_apellido');
                if (this.value.trim().length < 2 && !this.disabled) {
                    this.classList.add('border-red-300', 'bg-red-50');
                    if(errorMsg) errorMsg.classList.remove('hidden');
                } else {
                    this.classList.remove('border-red-300', 'bg-red-50');
                    if(errorMsg) errorMsg.classList.add('hidden');
                }
            });
        }
    });

    // --- D. VALIDACIÓN CÉDULA ---
    const inputDoc = document.getElementById('input_documento');
    if(inputDoc) {
        inputDoc.addEventListener('input', function() {
             this.value = this.value.replace(/[^0-9]/g, '');
        });
    }

    // --- E. VALIDACIÓN CONTRASEÑAS ---
    const pass = document.getElementById('input_password');
    const confirm = document.getElementById('input_confirm');
    
    if(pass && confirm) {
        const validatePass = () => {
            const errorMsg = document.getElementById('error_confirm');
            const errorPass = document.getElementById('error_password');

            if(pass.value.length > 0 && pass.value.length < 8) {
                if(errorPass) errorPass.classList.remove('hidden');
            } else {
                if(errorPass) errorPass.classList.add('hidden');
            }

            if (confirm.value.length > 0 && pass.value !== confirm.value) {
                confirm.classList.add('border-red-300', 'bg-red-50');
                if(errorMsg) errorMsg.classList.remove('hidden');
            } else {
                confirm.classList.remove('border-red-300', 'bg-red-50');
                if(errorMsg) errorMsg.classList.add('hidden');
            }
        };

        confirm.addEventListener('input', validatePass);
        pass.addEventListener('input', validatePass);
    }

    /**
     * ==========================================
     * GESTOR CENTRALIZADO DE ALERTAS
     * ==========================================
     */

    // --- F. ALERTA DE ERROR (TOAST ROJO) ---
    const hasErrors = document.querySelector('.text-red-800'); 
    if (hasErrors) {
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
        Toast.fire({
            icon: "error",
            title: "Revisa los campos marcados en rojo"
        });
    }

    // --- G. ALERTA DE ÉXITO (MODAL VERDE) ---
    // Buscamos la etiqueta meta que puso Laravel
    const successMeta = document.querySelector('meta[name="success-message"]');
    
    if (successMeta) {
        const message = successMeta.getAttribute('content');
        
        Swal.fire({
            title: '¡Bienvenido!',
            text: message,
            icon: 'success',
            confirmButtonText: 'Comenzar a comprar',
            confirmButtonColor: '#13ec13',
            color: '#1B4332',
            background: '#fff',
            backdrop: `rgba(0,0,0,0.4)`
        });
    }

    // --- H. SCROLL AUTOMÁTICO EN CATÁLOGO (SI HAY FILTROS) ---
    if (window.location.search.length > 1) {
        const catalogoSection = document.getElementById('contenido-catalogo');
        if(catalogoSection) {
            catalogoSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
});