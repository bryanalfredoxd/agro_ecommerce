// public/js/checkout.js

window.CheckoutConfig = {
    
    // --- Lógica de UI Principal ---
    toggleDirecciones: function(mostrar) {
        const div = document.getElementById('seccion-direcciones');
        const inputs = div.querySelectorAll('input[type="radio"][name="direccion_id"]');
        const filaDelivery = document.getElementById('fila-delivery');
        const totalUsdDisplay = document.getElementById('total-usd-display');
        const totalVesDisplay = document.getElementById('total-ves-display');
        
        // Leemos las variables inyectadas desde Blade
        const vars = window.CheckoutVars;
        let nuevoTotalUsd = vars.subtotalBase;

        if (mostrar) {
            div.classList.remove('opacity-40', 'pointer-events-none', 'grayscale');
            inputs.forEach(input => input.disabled = false);
            filaDelivery.classList.remove('hidden');
            nuevoTotalUsd += vars.precioDelivery;
        } else {
            div.classList.add('opacity-40', 'pointer-events-none', 'grayscale');
            inputs.forEach(input => { input.disabled = true; input.checked = false; });
            filaDelivery.classList.add('hidden');
        }

        totalUsdDisplay.innerText = '$' + nuevoTotalUsd.toFixed(2);
        totalVesDisplay.innerText = (nuevoTotalUsd * vars.tasaCambio).toFixed(2) + ' Bs';
    },

    mostrarDatosPago: function(tipo, info) {
        const container = document.getElementById('info-pago-container');
        const texto = document.getElementById('datos-bancarios-texto');
        const camposRef = document.getElementById('campos-referencia');
        const inputRef = document.getElementById('input-referencia');
        
        container.classList.remove('hidden');
        texto.innerText = info;
        
        // Ocultar campos de referencia si es efectivo o punto de venta
        if (['efectivo_usd', 'efectivo_bs', 'punto_venta', 'biopago'].includes(tipo)) {
            camposRef.classList.add('hidden');
            inputRef.required = false;
        } else {
            camposRef.classList.remove('hidden');
            inputRef.required = true;
        }
    },

    mostrarNombreArchivo: function(input) {
        const container = document.getElementById('nombre-archivo-container');
        if (input.files && input.files[0]) {
            container.innerText = input.files[0].name;
            container.classList.add('text-agro-dark');
            container.classList.remove('text-gray-500');
        } else {
            container.innerText = 'Adjuntar imagen...';
            container.classList.add('text-gray-500');
            container.classList.remove('text-agro-dark');
        }
    },

    // --- Lógica de Modales ---
    toggleModal: function(modalId) {
        const modal = document.getElementById(modalId);
        const backdrop = document.getElementById(modalId + '-backdrop');
        const panel = document.getElementById(modalId + '-panel');

        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                backdrop.classList.remove('opacity-0');
                panel.classList.remove('opacity-0', 'translate-y-8', 'sm:translate-y-0', 'sm:scale-95');
            }, 10);
        } else {
            backdrop.classList.add('opacity-0');
            panel.classList.add('opacity-0', 'translate-y-8', 'sm:translate-y-0', 'sm:scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }
    },

    // --- Sincronización Modales -> Formulario Principal ---
    seleccionarDireccionDesdeModal: function(id) {
        let mainInput = document.querySelector(`input[name="direccion_id"][value="${id}"]`);
        
        if (!mainInput) {
             const form = document.getElementById('checkout-form');
             let hiddenInput = document.getElementById('hidden-dir-input');
             if (!hiddenInput) {
                 hiddenInput = document.createElement('input');
                 hiddenInput.type = 'hidden';
                 hiddenInput.name = 'direccion_id';
                 hiddenInput.id = 'hidden-dir-input';
                 form.appendChild(hiddenInput);
             }
             hiddenInput.value = id;
             // Desmarcar las principales para que no haya conflictos
             document.querySelectorAll('input[name="direccion_id"]').forEach(i => i.checked = false);
        } else {
            mainInput.checked = true;
            const hiddenInput = document.getElementById('hidden-dir-input');
            if (hiddenInput) hiddenInput.remove();
        }
        this.toggleModal('modal-otras-direcciones');
    },

    seleccionarPagoDesdeModal: function(id, tipo, info) {
        let mainInput = document.querySelector(`input[name="metodo_pago_id"][value="${id}"]`);
        
        if (!mainInput) {
            document.querySelectorAll('input[name="metodo_pago_id"]').forEach(i => i.checked = false);

            const form = document.getElementById('checkout-form');
            let hiddenInput = document.getElementById('hidden-pago-input');
            if (!hiddenInput) {
                hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'metodo_pago_id';
                hiddenInput.id = 'hidden-pago-input';
                form.appendChild(hiddenInput);
            }
            hiddenInput.value = id;
            this.mostrarDatosPago(tipo, info);
        } else {
             mainInput.click(); 
             const hiddenInput = document.getElementById('hidden-pago-input');
             if (hiddenInput) hiddenInput.remove();
        }
        this.toggleModal('modal-todos-pagos');
    }
};

// --- Inicialización al cargar la página ---
document.addEventListener("DOMContentLoaded", () => {
    // Verificar si "Delivery" está chequeado por defecto al cargar la página
    const deliveryRadio = document.querySelector('input[name="metodo_entrega"][value="delivery"]');
    if (deliveryRadio && deliveryRadio.checked) {
        window.CheckoutConfig.toggleDirecciones(true);
    } else {
        window.CheckoutConfig.toggleDirecciones(false);
    }
});