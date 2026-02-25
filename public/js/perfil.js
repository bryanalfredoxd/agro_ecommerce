// public/js/perfil.js

window.PerfilConfig = (function() {
    // --- VARIABLES GLOBALES ---
    let map = null;
    let marker = null;
    let deleteTargetId = null;
    let isFromModal = false;
    let toastTimeout;

    // --- INICIALIZACIÓN ---
    document.addEventListener("DOMContentLoaded", () => {
        // Inicializar Teléfono (intlTelInput)
        const inputPhone = document.querySelector("#phone");
        if (inputPhone && window.intlTelInput) {
            window.intlTelInput(inputPhone, {
                initialCountry: "ve",
                separateDialCode: true,
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.6.0/build/js/utils.js",
            });
        }

        // Mostrar Toasts desde Sesión (Blade)
        if (window.PerfilData.sessionSuccess) {
            showToast(window.PerfilData.sessionSuccess, 'success');
        }
        if (window.PerfilData.sessionError) {
            showToast(window.PerfilData.sessionError, 'error');
        }
        if (window.PerfilData.hasErrors) {
            showToast("Hay errores en el formulario. Verifica los campos.", 'error');
        }
    });

    // --- LÓGICA DE TOASTS ---
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
        } else {
            iconContainer.classList.add('bg-red-50', 'text-red-500');
            icon.innerText = 'error';
            title.innerText = 'Error';
            title.className = 'font-bold text-red-700 text-sm';
        }

        msg.innerText = message;
        toast.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');

        if (toastTimeout) clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => hideToast(), 4000);
    }

    function hideToast() {
        const toast = document.getElementById('toast-notification');
        if(toast) toast.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
    }

    // --- LÓGICA DE MODALES GENÉRICA ---
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        if(!modal) return;
        
        const backdrop = document.getElementById(modalId + '-backdrop');
        const panel = document.getElementById(modalId + '-panel');

        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                backdrop.classList.remove('opacity-0');
                panel.classList.remove('opacity-0', 'translate-y-8', 'sm:translate-y-0', 'sm:scale-95');
            }, 10);
            
            // Si es el modal del mapa, lo inicializamos
            if(modalId === 'modal-direccion') {
                setTimeout(() => {
                    initMap();
                }, 300); // 300ms permite que el modal termine su animación antes de renderizar el mapa
            }
        } else {
            backdrop.classList.add('opacity-0');
            panel.classList.add('opacity-0', 'translate-y-8', 'sm:translate-y-0', 'sm:scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }
    }

    // --- LÓGICA DEL MODAL ELIMINAR DIRECCIÓN ---
    function openDeleteModal(id, fromModal = false) {
        deleteTargetId = id;
        isFromModal = fromModal;
        toggleModal('delete-modal');
    }

    function closeDeleteModal() {
        deleteTargetId = null;
        toggleModal('delete-modal');
    }

    function confirmDelete() {
        if (!deleteTargetId) return;
        // Determinar qué formulario enviar (desde la lista principal o el modal de gestión)
        const formId = isFromModal ? `delete-form-modal-${deleteTargetId}` : `delete-form-${deleteTargetId}`;
        const form = document.getElementById(formId);
        
        if (form) {
            form.submit();
        }
        closeDeleteModal();
    }

    // --- LÓGICA DEL MAPA LEAFLET ---
    function initMap() {
        if(map) {
            map.invalidateSize(); 
            return; 
        }

        const lat = window.PerfilData.defaultLat;
        const lng = window.PerfilData.defaultLng;

        map = L.map('map-canvas', {
            center: [lat, lng],
            zoom: 15,
            minZoom: 12,
            maxZoom: 18,
            zoomControl: false // Ocultamos el nativo para evitar estilos rotos, agregamos abajo
        });
        
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        marker = L.marker([lat, lng], { draggable: true }).addTo(map);

        marker.on('dragend', function() {
            var position = marker.getLatLng();
            updateCoordinates(position.lat, position.lng);
        });

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateCoordinates(e.latlng.lat, e.latlng.lng);
        });

        updateCoordinates(lat, lng);
    }

    function updateCoordinates(lat, lng) {
        document.getElementById('input_lat').value = lat;
        document.getElementById('input_lng').value = lng;
    }

    function getCurrentLocation() {
        if (navigator.geolocation) {
            showToast("Buscando tu ubicación...", "success");
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    if(map && marker) {
                        map.setView([lat, lng], 16);
                        marker.setLatLng([lat, lng]);
                        updateCoordinates(lat, lng);
                        showToast("Ubicación actualizada correctamente.", "success");
                    }
                },
                () => { showToast("No pudimos obtener tu ubicación GPS.", "error"); }
            );
        } else {
            showToast("Tu navegador no soporta GPS.", "error");
        }
    }

    // Retornamos los métodos que necesitamos usar en el HTML (onclick)
    return {
        showToast,
        hideToast,
        toggleModal,
        openDeleteModal,
        closeDeleteModal,
        confirmDelete,
        getCurrentLocation
    };

})();