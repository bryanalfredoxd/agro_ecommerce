import './bootstrap';

/**
 * ==========================================
 * DATA: LISTADO MUNDIAL DE PAÍSES
 * ==========================================
 * Prioridad: Venezuela y LatAm al inicio.
 * Resto: Alfabético.
 */
const COUNTRY_CODES = [
    // --- PRIORITARIOS ---
    { code: "+58", flag: "🇻🇪", name: "Venezuela" },
    { code: "+57", flag: "🇨🇴", name: "Colombia" },
    { code: "+1",  flag: "🇺🇸", name: "Estados Unidos" },
    { code: "+34", flag: "🇪🇸", name: "España" },
    { code: "+55", flag: "🇧🇷", name: "Brasil" },
    { code: "+54", flag: "🇦🇷", name: "Argentina" },
    { code: "+56", flag: "🇨🇱", name: "Chile" },
    { code: "+51", flag: "🇵🇪", name: "Perú" },
    { code: "+52", flag: "🇲🇽", name: "México" },
    { code: "+507", flag: "🇵🇦", name: "Panamá" },
    { code: "+593", flag: "🇪🇨", name: "Ecuador" },
    { code: "+1-809", flag: "🇩🇴", name: "Rep. Dominicana" },
    
    // --- RESTO DEL MUNDO (A-Z) ---
    { code: "+93", flag: "🇦🇫", name: "Afganistán" },
    { code: "+355", flag: "🇦🇱", name: "Albania" },
    { code: "+49", flag: "🇩🇪", name: "Alemania" },
    { code: "+376", flag: "🇦🇩", name: "Andorra" },
    { code: "+244", flag: "🇦🇴", name: "Angola" },
    { code: "+966", flag: "🇸🇦", name: "Arabia Saudita" },
    { code: "+213", flag: "🇩🇿", name: "Argelia" },
    { code: "+374", flag: "🇦🇲", name: "Armenia" },
    { code: "+297", flag: "🇦🇼", name: "Aruba" },
    { code: "+61", flag: "🇦🇺", name: "Australia" },
    { code: "+43", flag: "🇦🇹", name: "Austria" },
    { code: "+994", flag: "🇦🇿", name: "Azerbaiyán" },
    { code: "+1-242", flag: "🇧🇸", name: "Bahamas" },
    { code: "+880", flag: "🇧🇩", name: "Bangladesh" },
    { code: "+1-246", flag: "🇧🇧", name: "Barbados" },
    { code: "+973", flag: "🇧🇭", name: "Bahrein" },
    { code: "+32", flag: "🇧🇪", name: "Bélgica" },
    { code: "+501", flag: "🇧🇿", name: "Belice" },
    { code: "+229", flag: "🇧🇯", name: "Benín" },
    { code: "+375", flag: "🇧🇾", name: "Bielorrusia" },
    { code: "+591", flag: "🇧🇴", name: "Bolivia" },
    { code: "+387", flag: "🇧🇦", name: "Bosnia y Herz." },
    { code: "+267", flag: "🇧🇼", name: "Botsuana" },
    { code: "+673", flag: "🇧🇳", name: "Brunéi" },
    { code: "+359", flag: "🇧🇬", name: "Bulgaria" },
    { code: "+226", flag: "🇧🇫", name: "Burkina Faso" },
    { code: "+257", flag: "🇧🇮", name: "Burundi" },
    { code: "+975", flag: "🇧🇹", name: "Bután" },
    { code: "+238", flag: "🇨🇻", name: "Cabo Verde" },
    { code: "+855", flag: "🇰🇭", name: "Camboya" },
    { code: "+237", flag: "🇨🇲", name: "Camerún" },
    { code: "+1", flag: "🇨🇦", name: "Canadá" },
    { code: "+974", flag: "🇶🇦", name: "Catar" },
    { code: "+235", flag: "🇹🇩", name: "Chad" },
    { code: "+86", flag: "🇨🇳", name: "China" },
    { code: "+357", flag: "🇨🇾", name: "Chipre" },
    { code: "+39", flag: "🇻🇦", name: "Ciudad del Vaticano" },
    { code: "+269", flag: "🇰🇲", name: "Comoras" },
    { code: "+850", flag: "🇰🇵", name: "Corea del Norte" },
    { code: "+82", flag: "🇰🇷", name: "Corea del Sur" },
    { code: "+225", flag: "🇨🇮", name: "Costa de Marfil" },
    { code: "+506", flag: "🇨🇷", name: "Costa Rica" },
    { code: "+385", flag: "🇭🇷", name: "Croacia" },
    { code: "+53", flag: "🇨🇺", name: "Cuba" },
    { code: "+45", flag: "🇩🇰", name: "Dinamarca" },
    { code: "+1-767", flag: "🇩🇲", name: "Dominica" },
    { code: "+20", flag: "🇪🇬", name: "Egipto" },
    { code: "+503", flag: "🇸🇻", name: "El Salvador" },
    { code: "+971", flag: "🇦🇪", name: "Emiratos Árabes" },
    { code: "+291", flag: "🇪🇷", name: "Eritrea" },
    { code: "+421", flag: "🇸🇰", name: "Eslovaquia" },
    { code: "+386", flag: "🇸🇮", name: "Eslovenia" },
    { code: "+372", flag: "🇪🇪", name: "Estonia" },
    { code: "+251", flag: "🇪🇹", name: "Etiopía" },
    { code: "+63", flag: "🇵🇭", name: "Filipinas" },
    { code: "+358", flag: "🇫🇮", name: "Finlandia" },
    { code: "+679", flag: "🇫🇯", name: "Fiyi" },
    { code: "+33", flag: "🇫🇷", name: "Francia" },
    { code: "+241", flag: "🇬🇦", name: "Gabón" },
    { code: "+220", flag: "🇬🇲", name: "Gambia" },
    { code: "+995", flag: "🇬🇪", name: "Georgia" },
    { code: "+233", flag: "🇬🇭", name: "Ghana" },
    { code: "+1-473", flag: "🇬🇩", name: "Granada" },
    { code: "+30", flag: "🇬🇷", name: "Grecia" },
    { code: "+502", flag: "🇬🇹", name: "Guatemala" },
    { code: "+240", flag: "🇬🇶", name: "Guinea Ecuatorial" },
    { code: "+224", flag: "🇬🇳", name: "Guinea" },
    { code: "+245", flag: "🇬🇼", name: "Guinea-Bisáu" },
    { code: "+592", flag: "🇬🇾", name: "Guyana" },
    { code: "+509", flag: "🇭🇹", name: "Haití" },
    { code: "+504", flag: "🇭🇳", name: "Honduras" },
    { code: "+36", flag: "🇭🇺", name: "Hungría" },
    { code: "+91", flag: "🇮🇳", name: "India" },
    { code: "+62", flag: "🇮🇩", name: "Indonesia" },
    { code: "+964", flag: "🇮🇶", name: "Irak" },
    { code: "+98", flag: "🇮🇷", name: "Irán" },
    { code: "+353", flag: "🇮🇪", name: "Irlanda" },
    { code: "+354", flag: "🇮🇸", name: "Islandia" },
    { code: "+972", flag: "🇮🇱", name: "Israel" },
    { code: "+39", flag: "🇮🇹", name: "Italia" },
    { code: "+1-876", flag: "🇯🇲", name: "Jamaica" },
    { code: "+81", flag: "🇯🇵", name: "Japón" },
    { code: "+962", flag: "🇯🇴", name: "Jordania" },
    { code: "+7", flag: "🇰🇿", name: "Kazajistán" },
    { code: "+254", flag: "🇰🇪", name: "Kenia" },
    { code: "+996", flag: "🇰🇬", name: "Kirguistán" },
    { code: "+686", flag: "🇰🇮", name: "Kiribati" },
    { code: "+965", flag: "🇰🇼", name: "Kuwait" },
    { code: "+856", flag: "🇱🇦", name: "Laos" },
    { code: "+266", flag: "🇱🇸", name: "Lesoto" },
    { code: "+371", flag: "🇱🇻", name: "Letonia" },
    { code: "+961", flag: "🇱🇧", name: "Líbano" },
    { code: "+231", flag: "🇱🇷", name: "Liberia" },
    { code: "+218", flag: "🇱🇾", name: "Libia" },
    { code: "+423", flag: "🇱🇮", name: "Liechtenstein" },
    { code: "+370", flag: "🇱🇹", name: "Lituania" },
    { code: "+352", flag: "🇱🇺", name: "Luxemburgo" },
    { code: "+389", flag: "🇲🇰", name: "Macedonia del Norte" },
    { code: "+261", flag: "🇲🇬", name: "Madagascar" },
    { code: "+60", flag: "🇲🇾", name: "Malasia" },
    { code: "+265", flag: "🇲🇼", name: "Malaui" },
    { code: "+960", flag: "🇲🇻", name: "Maldivas" },
    { code: "+223", flag: "🇲🇱", name: "Malí" },
    { code: "+356", flag: "🇲🇹", name: "Malta" },
    { code: "+212", flag: "🇲🇦", name: "Marruecos" },
    { code: "+230", flag: "🇲🇺", name: "Mauricio" },
    { code: "+222", flag: "🇲🇷", name: "Mauritania" },
    { code: "+691", flag: "🇫🇲", name: "Micronesia" },
    { code: "+373", flag: "🇲🇩", name: "Moldavia" },
    { code: "+377", flag: "🇲🇨", name: "Mónaco" },
    { code: "+976", flag: "🇲🇳", name: "Mongolia" },
    { code: "+382", flag: "🇲🇪", name: "Montenegro" },
    { code: "+258", flag: "🇲🇿", name: "Mozambique" },
    { code: "+95", flag: "🇲🇲", name: "Myanmar" },
    { code: "+264", flag: "🇳🇦", name: "Namibia" },
    { code: "+674", flag: "🇳🇷", name: "Nauru" },
    { code: "+977", flag: "🇳🇵", name: "Nepal" },
    { code: "+505", flag: "🇳🇮", name: "Nicaragua" },
    { code: "+227", flag: "🇳🇪", name: "Níger" },
    { code: "+234", flag: "🇳🇬", name: "Nigeria" },
    { code: "+47", flag: "🇳🇴", name: "Noruega" },
    { code: "+64", flag: "🇳🇿", name: "Nueva Zelanda" },
    { code: "+968", flag: "🇴🇲", name: "Omán" },
    { code: "+31", flag: "🇳🇱", name: "Países Bajos" },
    { code: "+92", flag: "🇵🇰", name: "Pakistán" },
    { code: "+680", flag: "🇵🇼", name: "Palaos" },
    { code: "+970", flag: "🇵🇸", name: "Palestina" },
    { code: "+675", flag: "🇵🇬", name: "Papúa Nueva Guinea" },
    { code: "+595", flag: "🇵🇾", name: "Paraguay" },
    { code: "+48", flag: "🇵🇱", name: "Polonia" },
    { code: "+351", flag: "🇵🇹", name: "Portugal" },
    { code: "+44", flag: "🇬🇧", name: "Reino Unido" },
    { code: "+236", flag: "🇨🇫", name: "Rep. Centroafricana" },
    { code: "+420", flag: "🇨🇿", name: "República Checa" },
    { code: "+242", flag: "🇨🇬", name: "República del Congo" },
    { code: "+243", flag: "🇨🇩", name: "R.D. del Congo" },
    { code: "+250", flag: "🇷🇼", name: "Ruanda" },
    { code: "+40", flag: "🇷🇴", name: "Rumania" },
    { code: "+7", flag: "🇷🇺", name: "Rusia" },
    { code: "+677", flag: "🇸🇧", name: "Islas Salomón" },
    { code: "+685", flag: "🇼🇸", name: "Samoa" },
    { code: "+1-869", flag: "🇰🇳", name: "San Cristóbal y Nieves" },
    { code: "+378", flag: "🇸🇲", name: "San Marino" },
    { code: "+1-784", flag: "🇻🇨", name: "San Vicente y Granadinas" },
    { code: "+1-758", flag: "🇱🇨", name: "Santa Lucía" },
    { code: "+239", flag: "🇸🇹", name: "Santo Tomé y Príncipe" },
    { code: "+221", flag: "🇸🇳", name: "Senegal" },
    { code: "+381", flag: "🇷🇸", name: "Serbia" },
    { code: "+248", flag: "🇸🇨", name: "Seychelles" },
    { code: "+232", flag: "🇸🇱", name: "Sierra Leona" },
    { code: "+65", flag: "🇸🇬", name: "Singapur" },
    { code: "+963", flag: "🇸🇾", name: "Siria" },
    { code: "+252", flag: "🇸🇴", name: "Somalia" },
    { code: "+94", flag: "🇱🇰", name: "Sri Lanka" },
    { code: "+268", flag: "🇸🇿", name: "Suazilandia" },
    { code: "+27", flag: "🇿🇦", name: "Sudáfrica" },
    { code: "+249", flag: "🇸🇩", name: "Sudán" },
    { code: "+211", flag: "🇸🇸", name: "Sudán del Sur" },
    { code: "+46", flag: "🇸🇪", name: "Suecia" },
    { code: "+41", flag: "🇨🇭", name: "Suiza" },
    { code: "+597", flag: "🇸🇷", name: "Surinam" },
    { code: "+66", flag: "🇹🇭", name: "Tailandia" },
    { code: "+886", flag: "🇹🇼", name: "Taiwán" },
    { code: "+255", flag: "🇹🇿", name: "Tanzania" },
    { code: "+992", flag: "🇹🇯", name: "Tayikistán" },
    { code: "+670", flag: "🇹🇱", name: "Timor Oriental" },
    { code: "+228", flag: "🇹🇬", name: "Togo" },
    { code: "+676", flag: "🇹🇴", name: "Tonga" },
    { code: "+1-868", flag: "🇹🇹", name: "Trinidad y Tobago" },
    { code: "+216", flag: "🇹🇳", name: "Túnez" },
    { code: "+993", flag: "🇹🇲", name: "Turkmenistán" },
    { code: "+90", flag: "🇹🇷", name: "Turquía" },
    { code: "+688", flag: "🇹🇻", name: "Tuvalu" },
    { code: "+380", flag: "🇺🇦", name: "Ucrania" },
    { code: "+256", flag: "🇺🇬", name: "Uganda" },
    { code: "+598", flag: "🇺🇾", name: "Uruguay" },
    { code: "+998", flag: "🇺🇿", name: "Uzbekistán" },
    { code: "+678", flag: "🇻🇺", name: "Vanuatu" },
    { code: "+84", flag: "🇻🇳", name: "Vietnam" },
    { code: "+967", flag: "🇾🇪", name: "Yemen" },
    { code: "+253", flag: "🇩🇯", name: "Yibuti" },
    { code: "+260", flag: "🇿🇲", name: "Zambia" },
    { code: "+263", flag: "🇿🇼", name: "Zimbabue" }
];

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
 * LÓGICA DE REGISTRO
 * ==========================================
 */

window.moveGlider = function(index, tipo) {
    const glider = document.getElementById('tabGlider');
    if (!glider) return;

    glider.style.transform = `translateX(${index * 100}%)`;

    const labelNombre = document.getElementById('label_nombre');
    const labelDoc = document.getElementById('label_documento');
    const containerApellido = document.getElementById('field_apellido_container');
    const inputApellido = document.getElementById('input_apellido');
    
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
    
    // 1. Cargar Países Dinámicamente
    const selectPais = document.getElementById('select_pais');
    const hiddenPais = document.getElementById('codigo_pais_hidden');

    if (selectPais) {
        // Generar opciones
        COUNTRY_CODES.forEach(country => {
            const option = document.createElement('option');
            option.value = country.code;
            option.text = `${country.flag} ${country.code}`; // Ej: 🇻🇪 +58
            option.title = country.name; // Tooltip con el nombre al pasar el mouse
            
            // Pre-seleccionar si hay old value
            if (hiddenPais && hiddenPais.value === country.code) {
                option.selected = true;
            }
            selectPais.appendChild(option);
        });

        selectPais.addEventListener('change', function() {
            if(hiddenPais) hiddenPais.value = this.value;
        });
    }

    // 2. Restaurar Glider
    const oldTipoInput = document.getElementById('old_tipo_cliente');
    if (oldTipoInput) {
        const oldTipo = oldTipoInput.value;
        if(oldTipo === 'juridico') window.moveGlider(1, 'juridico');
        else if(oldTipo === 'finca_productor') window.moveGlider(2, 'finca');
        else window.moveGlider(0, 'natural');
    }

    // 3. Validación de Nombre/Apellido (SOLO LETRAS, Min 2 caracteres)
    const inputsTexto = ['input_nombre', 'input_apellido'];
    inputsTexto.forEach(id => {
        const input = document.getElementById(id);
        if(input) {
            input.addEventListener('input', function(e) {
                // Elimina números y símbolos raros
                this.value = this.value.replace(/[^a-zA-ZÀ-ÿ\u00f1\u00d1\s]/g, '');
            });

            input.addEventListener('blur', function() {
                const errorMsg = document.getElementById(id === 'input_nombre' ? 'error_nombre' : 'error_apellido');
                
                // MÍNIMO 2 LETRAS (Restaurado a tu preferencia)
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

    // 4. Validación Teléfono (Solo números)
    const inputTel = document.getElementById('input_telefono');
    if (inputTel) {
        inputTel.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        inputTel.addEventListener('blur', function() {
            const errorMsg = document.getElementById('error_telefono');
            if (this.value.length < 7) { 
                this.parentElement.classList.add('border-red-300', 'ring-2', 'ring-red-100');
                if(errorMsg) errorMsg.classList.remove('hidden');
            } else {
                this.parentElement.classList.remove('border-red-300', 'ring-2', 'ring-red-100');
                if(errorMsg) errorMsg.classList.add('hidden');
            }
        });
    }

    // 5. Cédula (Solo números)
    const inputDoc = document.getElementById('input_documento');
    if(inputDoc) {
        inputDoc.addEventListener('input', function() {
             this.value = this.value.replace(/[^0-9]/g, '');
        });
    }

    // 6. Contraseñas
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
});