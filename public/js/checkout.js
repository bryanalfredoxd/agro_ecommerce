// public/js/checkout.js - Lógica interactiva del proceso de Checkout

// Helper para escapar HTML en cadenas dinámicas
function escapeHtmlCheckout(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

window.CheckoutConfig = {

    // --- Lógica de Selección de Entrega ---
    toggleDirecciones: function (mostrar) {
        const div = document.getElementById('seccion-direcciones');
        const inputs = div ? div.querySelectorAll('input[type="radio"][name="direccion_id"]') : [];
        const filaDelivery = document.getElementById('fila-delivery');
        const totalUsdDisplay = document.getElementById('total-usd-display');
        const totalVesDisplay = document.getElementById('total-ves-display');
        const vars = window.CheckoutVars || { subtotalBase: 0, tasaCambio: 1, precioDelivery: 0, deliveryActivo: true };

        // 1. Si delivery está pausado por el admin y se intenta activar
        if (mostrar && vars.deliveryActivo === false) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'Delivery Pausado',
                    text: 'El servicio de delivery ha sido pausado temporalmente por la administración. Solo está disponible la opción de Retiro en Sede.',
                    confirmButtonColor: '#1e549e'
                });
            } else {
                alert('El servicio de delivery ha sido pausado temporalmente. Solo retiro en sede disponible.');
            }
            const pickupRadio = document.querySelector('input[name="metodo_entrega"][value="pickup"]');
            if (pickupRadio) pickupRadio.checked = true;
            this.toggleDirecciones(false);
            return;
        }

        const formatterVES = new Intl.NumberFormat('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        if (mostrar) {
            if (div) div.classList.remove('opacity-40', 'pointer-events-none', 'grayscale');
            inputs.forEach(input => input.disabled = false);

            // Obtener la dirección seleccionada
            let dirSeleccionada = document.querySelector('input[name="direccion_id"]:checked');
            if (!dirSeleccionada && inputs.length > 0) {
                // Seleccionar la primera con cobertura si existe
                let conCobertura = Array.from(inputs).find(i => i.dataset.cobertura === "1");
                dirSeleccionada = conCobertura || inputs[0];
                dirSeleccionada.checked = true;
            }

            if (dirSeleccionada) {
                const tieneCobertura = dirSeleccionada.dataset.cobertura === "1";
                if (!tieneCobertura) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Dirección fuera de cobertura',
                            text: 'La dirección seleccionada está fuera de nuestro radio de entrega. No es posible despachar a este destino, por lo que se ha asignado automáticamente Retiro en Sede.',
                            confirmButtonColor: '#1e549e'
                        });
                    } else {
                        alert('Esta dirección está fuera de cobertura. Se aplicará Retiro en Sede.');
                    }
                    const pickupRadio = document.querySelector('input[name="metodo_entrega"][value="pickup"]');
                    if (pickupRadio) pickupRadio.checked = true;
                    this.toggleDirecciones(false);
                    return;
                }

                const flete = parseFloat(dirSeleccionada.dataset.flete || 0);
                const zonaNombre = dirSeleccionada.dataset.zonaNombre || 'Delivery';
                vars.precioDelivery = flete;

                if (filaDelivery) {
                    filaDelivery.classList.remove('hidden');
                    filaDelivery.innerHTML = `<span>Delivery (${escapeHtmlCheckout(zonaNombre)})</span><span class="font-bold text-[#1e549e]">+ $${flete.toFixed(2)}</span>`;
                }

                let nuevoTotalUsd = vars.subtotalBase + flete;
                if (totalUsdDisplay) totalUsdDisplay.innerText = '$' + nuevoTotalUsd.toFixed(2);
                if (totalVesDisplay) totalVesDisplay.innerText = 'Bs. ' + formatterVES.format(nuevoTotalUsd * vars.tasaCambio);
            }
        } else {
            if (div) div.classList.add('opacity-40', 'pointer-events-none', 'grayscale');
            inputs.forEach(input => { input.disabled = true; });
            vars.precioDelivery = 0;
            if (filaDelivery) {
                filaDelivery.classList.remove('hidden');
                filaDelivery.innerHTML = `<span>Retiro en Sede</span><span class="text-emerald-600 font-bold">Gratis</span>`;
            }
            let nuevoTotalUsd = vars.subtotalBase;
            if (totalUsdDisplay) totalUsdDisplay.innerText = '$' + nuevoTotalUsd.toFixed(2);
            if (totalVesDisplay) totalVesDisplay.innerText = 'Bs. ' + formatterVES.format(nuevoTotalUsd * vars.tasaCambio);
        }

        // Ajustar disponibilidad de métodos de pago físicos según el tipo de entrega
        this.actualizarMetodosParaEntrega(mostrar);
    },

    // --- Control de Disponibilidad de Métodos de Pago según Entrega ---
    actualizarMetodosParaEntrega: function (esDelivery) {
        let metodoDeshabilitadoSeleccionado = false;

        document.querySelectorAll('input[name="metodo_pago_id"], input[name="metodo_pago_id_modal"]').forEach(radio => {
            let tipo = radio.dataset.tipo;
            if (!tipo && radio.dataset.cuenta) {
                try { tipo = JSON.parse(radio.dataset.cuenta).tipo; } catch (e) { }
            }

            if (['punto_venta', 'biopago'].includes(tipo)) {
                const label = radio.closest('label');
                const badge = label ? label.querySelector('.badge-solo-sede') : null;

                if (esDelivery) {
                    radio.disabled = true;
                    if (label) {
                        label.classList.add('opacity-40', 'cursor-not-allowed', 'pointer-events-none');
                    }
                    if (badge) badge.classList.remove('hidden');

                    if (radio.checked) {
                        radio.checked = false;
                        metodoDeshabilitadoSeleccionado = true;
                    }
                } else {
                    radio.disabled = false;
                    if (label) {
                        label.classList.remove('opacity-40', 'cursor-not-allowed', 'pointer-events-none');
                    }
                    if (badge) badge.classList.add('hidden');
                }
            }
        });

        if (metodoDeshabilitadoSeleccionado) {
            // Seleccionar automáticamente el primer método de pago habilitado
            const disponible = document.querySelector('input[name="metodo_pago_id"]:not([disabled])');
            if (disponible) {
                disponible.checked = true;
                this.manejarCambioPago(disponible);
            }
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'Punto de Venta / Biopago no disponible para Delivery',
                    text: 'Las terminales de punto de venta y Biopago solo se encuentran en nuestra sede física. Para delivery puedes seleccionar Efectivo, Pago Móvil o Transferencia bancaria.',
                    confirmButtonColor: '#1e549e'
                });
            }
        } else if (window.currentCuentaSeleccionada) {
            // Actualizar explicaciones presenciales según pickup vs delivery
            this.renderDatosPago(window.currentCuentaSeleccionada);
        }
    },

    // --- Validación en Tiempo Real para Referencias de Pago ---
    validarInputReferencia: function (input) {
        if (!input) return;
        const cuenta = window.currentCuentaSeleccionada;
        const tipo = cuenta ? cuenta.tipo : (document.querySelector('input[name="metodo_pago_id"]:checked')?.dataset?.tipo || '');
        const errorMsg = document.getElementById('error-referencia-msg');

        if (tipo === 'pago_movil') {
            // Solo dígitos numéricos (0-9), longitud hasta 15 dígitos
            const valorLimpio = input.value.replace(/\D/g, '').slice(0, 15);
            if (input.value !== valorLimpio) {
                input.value = valorLimpio;
            }
            if (errorMsg) {
                if (valorLimpio.length > 0 && valorLimpio.length < 4) {
                    errorMsg.innerHTML = '<span class="material-symbols-outlined text-[14px]">warning</span> La referencia de Pago Móvil debe tener al menos 4 dígitos numéricos.';
                    errorMsg.classList.remove('hidden');
                    input.classList.add('border-rose-400', 'bg-rose-50/20');
                } else {
                    errorMsg.classList.add('hidden');
                    errorMsg.innerHTML = '';
                    input.classList.remove('border-rose-400', 'bg-rose-50/20');
                }
            }
        } else if (tipo === 'transferencia') {
            // Solo dígitos numéricos (0-9), longitud hasta 25 dígitos
            const valorLimpio = input.value.replace(/\D/g, '').slice(0, 25);
            if (input.value !== valorLimpio) {
                input.value = valorLimpio;
            }
            if (errorMsg) {
                if (valorLimpio.length > 0 && valorLimpio.length < 4) {
                    errorMsg.innerHTML = '<span class="material-symbols-outlined text-[14px]">warning</span> La referencia de transferencia debe contener al menos 4 dígitos numéricos.';
                    errorMsg.classList.remove('hidden');
                    input.classList.add('border-rose-400', 'bg-rose-50/20');
                } else {
                    errorMsg.classList.add('hidden');
                    errorMsg.innerHTML = '';
                    input.classList.remove('border-rose-400', 'bg-rose-50/20');
                }
            }
        } else if (tipo === 'zelle' || tipo === 'binance') {
            if (errorMsg) {
                if (input.value.trim().length > 0 && input.value.trim().length < 3) {
                    errorMsg.innerHTML = '<span class="material-symbols-outlined text-[14px]">warning</span> Ingrese al menos 3 caracteres identificadores del pago.';
                    errorMsg.classList.remove('hidden');
                    input.classList.add('border-rose-400', 'bg-rose-50/20');
                } else {
                    errorMsg.classList.add('hidden');
                    errorMsg.innerHTML = '';
                    input.classList.remove('border-rose-400', 'bg-rose-50/20');
                }
            }
        } else {
            if (errorMsg) {
                errorMsg.classList.add('hidden');
                errorMsg.innerHTML = '';
                input.classList.remove('border-rose-400', 'bg-rose-50/20');
            }
        }
    },

    // --- Cambio directo de dirección seleccionada ---
    alCambiarDireccion: function (input) {
        if (!input) return;
        const deliveryRadio = document.querySelector('input[name="metodo_entrega"][value="delivery"]');
        const esDelivery = deliveryRadio && deliveryRadio.checked;

        if (esDelivery) {
            const tieneCobertura = input.dataset.cobertura === "1";
            if (!tieneCobertura) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Ubicación fuera de radio de entrega',
                        text: 'Esta dirección supera el radio de despacho de nuestra tienda. Solo puedes solicitar Retiro en Sede con esta ubicación.',
                        confirmButtonColor: '#1e549e'
                    });
                } else {
                    alert('Esta dirección está fuera de cobertura. Se aplicará Retiro en Sede.');
                }
                const pickupRadio = document.querySelector('input[name="metodo_entrega"][value="pickup"]');
                if (pickupRadio) pickupRadio.checked = true;
                this.toggleDirecciones(false);
                return;
            }

            // Si tiene cobertura, recalculamos flete y totales
            this.toggleDirecciones(true);
        }
    },

    // --- Copiar valor individual al portapapeles con feedback instantáneo ---
    copiarTexto: function (texto, btn) {
        if (!texto) return;
        const textoLimpio = String(texto).trim();

        const animarBoton = () => {
            if (!btn) return;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = `<span class="material-symbols-outlined text-[14px] text-emerald-600 font-bold">check</span> <span class="text-emerald-700 font-bold">¡Copiado!</span>`;
            btn.classList.add('bg-emerald-50', 'border-emerald-300', 'text-emerald-700');
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.classList.remove('bg-emerald-50', 'border-emerald-300', 'text-emerald-700');
            }, 1800);
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(textoLimpio).then(animarBoton).catch(() => {
                this.fallbackCopiar(textoLimpio);
                animarBoton();
            });
        } else {
            this.fallbackCopiar(textoLimpio);
            animarBoton();
        }
    },

    // Fallback para navegadores antiguos o contextos no seguros
    fallbackCopiar: function (texto) {
        const textArea = document.createElement("textarea");
        textArea.value = texto;
        textArea.style.position = "fixed";
        textArea.style.left = "-999999px";
        textArea.style.top = "-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
        } catch (err) {
            console.error('Fallback error:', err);
        }
        textArea.remove();
    },

    // --- Copiar todos los datos de la cuenta seleccionada en bloque ---
    copiarTodosLosDatos: function () {
        const c = window.currentCuentaSeleccionada;
        if (!c) return;

        let lineas = [];
        lineas.push(`DATOS DE PAGO - ${c.nombre || 'Agrosuministros Rodríguez'}`);
        if (c.banco) lineas.push(`• Banco: ${c.banco}`);
        if (c.cuenta) lineas.push(`• Nro. de Cuenta: ${c.cuenta}`);
        if (c.telefono) lineas.push(`• Teléfono: ${c.telefono}`);
        if (c.identificacion) lineas.push(`• Cédula/RIF: ${c.identificacion}`);
        if (c.email) lineas.push(`• Correo: ${c.email}`);
        if (c.titular) lineas.push(`• Titular: ${c.titular}`);
        if (c.instrucciones) lineas.push(`• Indicación: ${c.instrucciones}`);

        const textoCompleto = lineas.join('\n');
        const btn = document.getElementById('btn-copiar-todo');
        this.copiarTexto(textoCompleto, btn);
    },

    // --- Manejo del cambio de método de pago (Event listener de radio) ---
    manejarCambioPago: function (input) {
        if (!input) return;
        let cuenta = null;
        if (input.dataset.cuenta) {
            try {
                cuenta = JSON.parse(input.dataset.cuenta);
            } catch (e) {
                console.error("Error al parsear cuenta:", e);
            }
        }
        if (cuenta) {
            this.renderDatosPago(cuenta);
        }
    },

    // --- Renderizado Ejecutivo de Datos Bancarios y Formulario ---
    renderDatosPago: function (cuenta) {
        if (!cuenta) return;
        window.currentCuentaSeleccionada = cuenta;

        const container = document.getElementById('info-pago-container');
        if (!container) return;

        // Elementos visuales del encabezado
        const titulo = document.getElementById('info-pago-titulo');
        const subtitulo = document.getElementById('info-pago-subtitulo');
        const iconSpan = document.getElementById('info-pago-icon-span');
        const iconImg = document.getElementById('info-pago-icon-img');
        const btnCopiarTodo = document.getElementById('btn-copiar-todo');

        // Contenedores dinámicos
        const grid = document.getElementById('info-pago-grid');
        const presencial = document.getElementById('info-pago-presencial');
        const presencialTitulo = document.getElementById('info-presencial-titulo');
        const presencialDesc = document.getElementById('info-presencial-desc');
        const notaContainer = document.getElementById('info-pago-nota-container');
        const notaTexto = document.getElementById('info-pago-nota-texto');

        // Campos de referencia y comprobante
        const camposRef = document.getElementById('campos-referencia');
        const inputRef = document.getElementById('input-referencia');
        const labelRef = document.getElementById('label-referencia');
        const helperRef = document.getElementById('helper-referencia');

        // 1. Mostrar contenedor principal
        container.classList.remove('hidden');

        // 2. Encabezado e Ícono
        if (titulo) titulo.innerText = `Datos para ${cuenta.nombre || 'Pago'}`;
        if (subtitulo) {
            if (cuenta.banco) {
                subtitulo.innerText = `Cuenta oficial en ${cuenta.banco}`;
            } else if (['efectivo_usd', 'efectivo_bs'].includes(cuenta.tipo)) {
                subtitulo.innerText = 'Pago en efectivo directo sin transferencia bancaria previa';
            } else if (['punto_venta', 'biopago'].includes(cuenta.tipo)) {
                subtitulo.innerText = 'Cobro presencial mediante terminal en tienda';
            } else {
                subtitulo.innerText = 'Realiza tu pago con los siguientes datos oficiales';
            }
        }

        if (iconSpan && iconImg) {
            if (cuenta.icono_url) {
                iconSpan.classList.add('hidden');
                iconImg.src = cuenta.icono_url;
                iconImg.classList.remove('hidden');
            } else {
                iconImg.classList.add('hidden');
                iconSpan.classList.remove('hidden');
                const iconName = (cuenta.icono && !cuenta.icono.includes('_svg')) ? cuenta.icono : 'account_balance';
                iconSpan.innerText = iconName;
            }
        }

        // 3. Evaluar si es Presencial (Efectivo / Punto de Venta) o Digital
        const esPresencial = ['efectivo_usd', 'efectivo_bs', 'punto_venta', 'biopago'].includes(cuenta.tipo);
        const deliveryRadio = document.querySelector('input[name="metodo_entrega"][value="delivery"]');
        const esDelivery = deliveryRadio && deliveryRadio.checked;

        if (esPresencial) {
            if (grid) {
                grid.classList.add('hidden');
                grid.innerHTML = '';
            }
            if (btnCopiarTodo) btnCopiarTodo.classList.add('hidden');
            if (presencial) presencial.classList.remove('hidden');

            if (presencialTitulo) {
                if (cuenta.tipo === 'efectivo_usd') presencialTitulo.innerText = 'Pago en Efectivo (Dólares USD)';
                else if (cuenta.tipo === 'efectivo_bs') presencialTitulo.innerText = 'Pago en Efectivo (Bolívares en Efectivo)';
                else if (cuenta.tipo === 'punto_venta') presencialTitulo.innerText = 'Punto de Venta / Tarjeta de Débito (En Sede)';
                else if (cuenta.tipo === 'biopago') presencialTitulo.innerText = 'Biopago BDV (Huella en Sede)';
                else presencialTitulo.innerText = 'Pago Presencial Directo';
            }

            if (presencialDesc) {
                if (cuenta.tipo === 'punto_venta' || cuenta.tipo === 'biopago') {
                    presencialDesc.innerText = cuenta.instrucciones ||
                        'Tu pedido quedará reservado inmediatamente en nuestro inventario. Podrás procesar el pago directamente con tu tarjeta de débito o huella en nuestra caja al momento de retirar tus insumos.';
                } else if (cuenta.tipo === 'efectivo_usd' || cuenta.tipo === 'efectivo_bs') {
                    if (esDelivery) {
                        presencialDesc.innerText = cuenta.instrucciones ||
                            'Realizarás el pago en efectivo contra entrega directamente al repartidor al momento de recibir tus insumos en tu destino. Te recomendamos disponer del monto exacto o especificar con cuánto pagarás en las notas del pedido.';
                    } else {
                        presencialDesc.innerText = cuenta.instrucciones ||
                            'Tu pedido quedará reservado inmediatamente en nuestro inventario. Podrás pagar en efectivo directamente en nuestro mostrador al retirar tus insumos presentando tu número de pedido.';
                    }
                } else {
                    presencialDesc.innerText = cuenta.instrucciones ||
                        'Tu mercancía queda reservada en inventario. Realizarás el pago presencial al momento de la entrega o retiro.';
                }
            }

            // Ocultar campos de referencia bancaria y limpiar validación previa
            const errorRefMsg = document.getElementById('error-referencia-msg');
            if (errorRefMsg) {
                errorRefMsg.classList.add('hidden');
                errorRefMsg.innerHTML = '';
            }
            if (camposRef) camposRef.classList.add('hidden');
            if (inputRef) {
                inputRef.required = false;
                inputRef.value = '';
                inputRef.classList.remove('border-rose-400', 'bg-rose-50/20');
            }

        } else {
            // Es Pago Digital / Bancario (Pago Móvil, Transferencia, Zelle, Binance, etc.)
            if (presencial) presencial.classList.add('hidden');
            if (btnCopiarTodo) btnCopiarTodo.classList.remove('hidden');
            if (grid) {
                grid.classList.remove('hidden');
                let cardsHtml = '';

                // Banco / Entidad
                if (cuenta.banco) {
                    cardsHtml += `
                    <div class="p-3.5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
                        <div class="min-w-0">
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-0.5">Banco / Entidad</span>
                            <div class="flex items-center gap-1.5 font-bold text-sm text-slate-800 truncate">
                                <span class="material-symbols-outlined text-[18px] text-[#1e549e]">account_balance</span>
                                <span class="truncate">${escapeHtmlCheckout(cuenta.banco)}</span>
                            </div>
                        </div>
                    </div>`;
                }

                // Teléfono (Pago Móvil)
                if (cuenta.telefono) {
                    cardsHtml += `
                    <div class="p-3.5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-0.5">Teléfono Pago Móvil</span>
                            <span class="font-mono font-bold text-sm text-slate-900 tracking-wide block truncate select-all">${escapeHtmlCheckout(cuenta.telefono)}</span>
                        </div>
                        <button type="button" 
                                onclick="window.CheckoutConfig.copiarTexto('${escapeHtmlCheckout(cuenta.telefono)}', this)" 
                                class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-slate-50 hover:bg-[#e5f2fb] hover:text-[#1e549e] text-slate-600 border border-slate-200 text-xs font-bold transition-all active:scale-95 cursor-pointer">
                            <span class="material-symbols-outlined text-[14px]">content_copy</span>
                            <span>Copiar</span>
                        </button>
                    </div>`;
                }

                // Cédula / RIF / Pay ID
                if (cuenta.identificacion) {
                    const labelDoc = cuenta.tipo === 'binance' ? 'Pay ID / Binance ID' : 'Cédula / RIF Titular';
                    cardsHtml += `
                    <div class="p-3.5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-0.5">${labelDoc}</span>
                            <span class="font-mono font-bold text-sm text-slate-900 tracking-wide block truncate select-all">${escapeHtmlCheckout(cuenta.identificacion)}</span>
                        </div>
                        <button type="button" 
                                onclick="window.CheckoutConfig.copiarTexto('${escapeHtmlCheckout(cuenta.identificacion)}', this)" 
                                class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-slate-50 hover:bg-[#e5f2fb] hover:text-[#1e549e] text-slate-600 border border-slate-200 text-xs font-bold transition-all active:scale-95 cursor-pointer">
                            <span class="material-symbols-outlined text-[14px]">content_copy</span>
                            <span>Copiar</span>
                        </button>
                    </div>`;
                }

                // Correo Electrónico (Zelle / Binance / Cripto)
                if (cuenta.email) {
                    const labelEmail = cuenta.tipo === 'zelle' ? 'Correo Zelle Oficial' : 'Correo Electrónico';
                    cardsHtml += `
                    <div class="p-3.5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-0.5">${labelEmail}</span>
                            <span class="font-mono font-bold text-sm text-slate-900 truncate block select-all">${escapeHtmlCheckout(cuenta.email)}</span>
                        </div>
                        <button type="button" 
                                onclick="window.CheckoutConfig.copiarTexto('${escapeHtmlCheckout(cuenta.email)}', this)" 
                                class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-slate-50 hover:bg-[#e5f2fb] hover:text-[#1e549e] text-slate-600 border border-slate-200 text-xs font-bold transition-all active:scale-95 cursor-pointer">
                            <span class="material-symbols-outlined text-[14px]">content_copy</span>
                            <span>Copiar</span>
                        </button>
                    </div>`;
                }

                // Titular de la Cuenta
                if (cuenta.titular) {
                    cardsHtml += `
                    <div class="p-3.5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
                        <div class="min-w-0">
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-0.5">Titular de la Cuenta</span>
                            <div class="flex items-center gap-1.5 font-bold text-sm text-slate-800 truncate">
                                <span class="material-symbols-outlined text-[18px] text-slate-400">person</span>
                                <span class="truncate">${escapeHtmlCheckout(cuenta.titular)}</span>
                            </div>
                        </div>
                    </div>`;
                }

                // Número de Cuenta (20 dígitos o completo)
                if (cuenta.cuenta) {
                    // Formatear visualmente con separación en bloques de 4 dígitos si tiene 20 dígitos
                    const ctaRaw = String(cuenta.cuenta).trim();
                    const ctaDisplay = ctaRaw.length === 20
                        ? ctaRaw.replace(/(\d{4})(\d{4})(\d{2})(\d{10})/, '$1-$2-$3-$4')
                        : ctaRaw;

                    cardsHtml += `
                    <div class="col-span-1 sm:col-span-2 p-3.5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-0.5">Número de Cuenta Bancaria</span>
                            <span class="font-mono font-bold text-xs sm:text-sm text-slate-900 tracking-wider block break-all select-all">${escapeHtmlCheckout(ctaDisplay)}</span>
                        </div>
                        <button type="button" 
                                onclick="window.CheckoutConfig.copiarTexto('${escapeHtmlCheckout(ctaRaw)}', this)" 
                                class="shrink-0 inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-[#e5f2fb] hover:text-[#1e549e] text-slate-600 border border-slate-200 text-xs font-bold transition-all active:scale-95 cursor-pointer">
                            <span class="material-symbols-outlined text-[14px]">content_copy</span>
                            <span>Copiar Cuenta</span>
                        </button>
                    </div>`;
                }

                grid.innerHTML = cardsHtml;
            }

            // Mostrar campos de referencia y requerirlo
            if (camposRef) camposRef.classList.remove('hidden');
            if (inputRef) {
                inputRef.required = true;
                inputRef.classList.remove('border-rose-400', 'bg-rose-50/20');
            }

            // Personalizar labels, placeholders, inputMode y helpers según método
            if (labelRef && inputRef) {
                if (cuenta.tipo === 'pago_movil') {
                    labelRef.innerHTML = `Nro. de Referencia Pago Móvil <span class="text-rose-500">*</span>`;
                    inputRef.placeholder = 'Ej: 12345678 (solo números)';
                    inputRef.inputMode = 'numeric';
                    inputRef.pattern = '[0-9]*';
                    if (helperRef) {
                        helperRef.innerText = '';
                        helperRef.classList.remove('hidden');
                    }
                } else if (cuenta.tipo === 'transferencia') {
                    labelRef.innerHTML = `Nro. de Referencia Bancaria <span class="text-rose-500">*</span>`;
                    inputRef.placeholder = 'Ej: 00987654 (solo números)';
                    inputRef.inputMode = 'numeric';
                    inputRef.pattern = '[0-9]*';
                    if (helperRef) {
                        helperRef.innerText = '';
                        helperRef.classList.remove('hidden');
                    }
                } else if (cuenta.tipo === 'zelle') {
                    labelRef.innerHTML = `Titular que envía / Confirmación <span class="text-rose-500">*</span>`;
                    inputRef.placeholder = 'Ej: Pedro Pérez / Ref #';
                    inputRef.inputMode = 'text';
                    inputRef.pattern = '.*';
                    if (helperRef) {
                        helperRef.innerText = '';
                        helperRef.classList.remove('hidden');
                    }
                } else if (cuenta.tipo === 'binance') {
                    labelRef.innerHTML = `TxID / Pay ID / Order ID <span class="text-rose-500">*</span>`;
                    inputRef.placeholder = 'Ej: 294810394';
                    inputRef.inputMode = 'text';
                    inputRef.pattern = '.*';
                    if (helperRef) {
                        helperRef.innerText = '';
                        helperRef.classList.remove('hidden');
                    }
                } else {
                    labelRef.innerHTML = `Nro. de Referencia <span class="text-rose-500">*</span>`;
                    inputRef.placeholder = 'Ej: 12345678';
                    inputRef.inputMode = 'text';
                    if (helperRef) {
                        helperRef.innerText = 'Código o número de comprobante';
                        helperRef.classList.remove('hidden');
                    }
                }
            }
        }

        // 4. Mostrar Instrucciones Adicionales si existen
        if (notaContainer && notaTexto) {
            if (cuenta.instrucciones && cuenta.instrucciones.trim().length > 0 && !esPresencial) {
                notaContainer.classList.remove('hidden');
                notaTexto.innerText = cuenta.instrucciones;
            } else {
                notaContainer.classList.add('hidden');
                notaTexto.innerText = '';
            }
        }
    },

    // --- Soporte para llamadas previas de mostrarDatosPago ---
    mostrarDatosPago: function (tipo, info, cuentaObj) {
        if (cuentaObj && typeof cuentaObj === 'object') {
            this.renderDatosPago(cuentaObj);
            return;
        }
        if (tipo && typeof tipo === 'object') {
            this.renderDatosPago(tipo);
            return;
        }
        // Fallback básico si solo pasaron string
        this.renderDatosPago({
            tipo: tipo,
            nombre: tipo ? tipo.toUpperCase().replace('_', ' ') : 'Pago',
            instrucciones: info,
            banco: info
        });
    },

    // --- Copiar datos bancarios tradicional (compatibilidad) ---
    copiarDatosBancarios: function () {
        this.copiarTodosLosDatos();
    },

    // --- Mostrar Nombre del Archivo Seleccionado ---
    mostrarNombreArchivo: function (input) {
        const container = document.getElementById('nombre-archivo-container');
        if (!container) return;

        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            const fileSizeKB = Math.round(input.files[0].size / 1024);
            container.innerHTML = `<span class="text-slate-900 font-bold truncate">${escapeHtmlCheckout(fileName)}</span> <span class="text-[10px] text-slate-400 font-normal">(${fileSizeKB} KB)</span>`;
        } else {
            container.innerText = 'Adjuntar captura o voucher (PNG, JPG)...';
        }
    },

    // --- Control de Modales ---
    toggleModal: function (modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        const backdrop = document.getElementById(modalId + '-backdrop');
        const panel = document.getElementById(modalId + '-panel');

        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                if (backdrop) backdrop.classList.remove('opacity-0');
                if (panel) panel.classList.remove('opacity-0', 'translate-y-6', 'sm:translate-y-0', 'sm:scale-95');
            }, 10);
        } else {
            if (backdrop) backdrop.classList.add('opacity-0');
            if (panel) panel.classList.add('opacity-0', 'translate-y-6', 'sm:translate-y-0', 'sm:scale-95');
            setTimeout(() => modal.classList.add('hidden'), 250);
        }
    },

    // --- Sincronización Modales -> Formulario Principal ---
    seleccionarDireccionDesdeModal: function (id, elem) {
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
            if (elem) {
                hiddenInput.dataset.cobertura = elem.dataset.cobertura || "0";
                hiddenInput.dataset.flete = elem.dataset.flete || "0";
                hiddenInput.dataset.zonaNombre = elem.dataset.zonaNombre || "";
            }
            document.querySelectorAll('input[name="direccion_id"]').forEach(i => i.checked = false);
            this.alCambiarDireccion(hiddenInput);
        } else {
            mainInput.checked = true;
            const hiddenInput = document.getElementById('hidden-dir-input');
            if (hiddenInput) hiddenInput.remove();
            this.alCambiarDireccion(mainInput);
        }
        this.toggleModal('modal-otras-direcciones');
    },

    seleccionarPagoDesdeModal: function (elem, tipo, info) {
        let id, cuenta = null;

        if (elem instanceof HTMLElement) {
            id = elem.value;
            if (elem.dataset.cuenta) {
                try {
                    cuenta = JSON.parse(elem.dataset.cuenta);
                } catch (e) {
                    console.error("Error al parsear cuenta:", e);
                }
            }
        } else {
            id = elem;
            cuenta = { id: id, tipo: tipo, info: info };
        }

        let mainRadio = document.querySelector(`input[name="metodo_pago_id"][value="${id}"]`);

        if (mainRadio) {
            mainRadio.checked = true;
            this.manejarCambioPago(mainRadio);
            const hiddenInput = document.getElementById('hidden-pago-input');
            if (hiddenInput) hiddenInput.remove();
        } else {
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
            if (cuenta) {
                hiddenInput.dataset.tipo = cuenta.tipo;
                this.renderDatosPago(cuenta);
            }
        }
        this.toggleModal('modal-todos-pagos');
    }
};

// --- Inicialización al cargar la página ---
document.addEventListener("DOMContentLoaded", () => {
    // 1. Inicializar tipo de entrega
    const deliveryRadio = document.querySelector('input[name="metodo_entrega"][value="delivery"]');
    if (deliveryRadio && deliveryRadio.checked) {
        window.CheckoutConfig.toggleDirecciones(true);
    } else {
        window.CheckoutConfig.toggleDirecciones(false);
    }

    // 2. Inicializar método de pago si ya hay uno seleccionado (ej: por validación previa)
    const metodoSeleccionado = document.querySelector('input[name="metodo_pago_id"]:checked');
    if (metodoSeleccionado) {
        window.CheckoutConfig.manejarCambioPago(metodoSeleccionado);
    }

    // 3. Validación estricta antes del envío del formulario
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', (e) => {
            const metodoRadio = document.querySelector('input[name="metodo_pago_id"]:checked') || document.getElementById('hidden-pago-input');
            if (!metodoRadio) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Forma de pago requerida',
                        text: 'Por favor selecciona un método de pago antes de confirmar el pedido.',
                        confirmButtonColor: '#1e549e'
                    });
                } else {
                    alert('Por favor selecciona un método de pago antes de continuar.');
                }
                return;
            }

            const tipoMetodo = metodoRadio.dataset.tipo || (window.currentCuentaSeleccionada ? window.currentCuentaSeleccionada.tipo : '');
            const delRadio = document.querySelector('input[name="metodo_entrega"][value="delivery"]');
            const esDeliv = delRadio && delRadio.checked;

            // Validación: Punto de venta / Biopago no permitido con delivery
            if (esDeliv && ['punto_venta', 'biopago'].includes(tipoMetodo)) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Método no disponible para Delivery',
                        text: 'El Punto de Venta y Biopago solo pueden utilizarse con Retiro en Sede. Cambia la forma de pago a Efectivo, Pago Móvil o Transferencia.',
                        confirmButtonColor: '#1e549e'
                    });
                } else {
                    alert('Punto de venta solo disponible para retiro en sede.');
                }
                return;
            }

            // Validación de referencias para métodos digitales
            const inputRef = document.getElementById('input-referencia');
            if (['pago_movil', 'transferencia', 'zelle', 'binance'].includes(tipoMetodo)) {
                const val = inputRef ? inputRef.value.trim() : '';

                if (!val) {
                    e.preventDefault();
                    if (inputRef) {
                        inputRef.focus();
                        inputRef.classList.add('border-rose-400', 'bg-rose-50/20');
                    }
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Referencia requerida',
                            text: 'Debes ingresar el número de referencia o confirmación de tu pago.',
                            confirmButtonColor: '#1e549e'
                        });
                    } else {
                        alert('Debes ingresar el número de referencia.');
                    }
                    return;
                }

                if (tipoMetodo === 'pago_movil') {
                    if (!/^[0-9]{4,15}$/.test(val)) {
                        e.preventDefault();
                        if (inputRef) {
                            inputRef.focus();
                            inputRef.classList.add('border-rose-400', 'bg-rose-50/20');
                        }
                        const msg = 'La referencia de Pago Móvil debe contener solo dígitos numéricos (entre 4 y 15 dígitos).';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Referencia inválida',
                                text: msg,
                                confirmButtonColor: '#1e549e'
                            });
                        } else {
                            alert(msg);
                        }
                        return;
                    }
                } else if (tipoMetodo === 'transferencia') {
                    if (!/^[0-9]{4,25}$/.test(val)) {
                        e.preventDefault();
                        if (inputRef) {
                            inputRef.focus();
                            inputRef.classList.add('border-rose-400', 'bg-rose-50/20');
                        }
                        const msg = 'La referencia bancaria debe contener solo dígitos numéricos (entre 4 y 25 dígitos).';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Referencia inválida',
                                text: msg,
                                confirmButtonColor: '#1e549e'
                            });
                        } else {
                            alert(msg);
                        }
                        return;
                    }
                } else if (tipoMetodo === 'zelle' || tipoMetodo === 'binance') {
                    if (val.length < 3) {
                        e.preventDefault();
                        if (inputRef) {
                            inputRef.focus();
                            inputRef.classList.add('border-rose-400', 'bg-rose-50/20');
                        }
                        const msg = 'Por favor ingresa al menos 3 caracteres identificadores del pago.';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Referencia muy corta',
                                text: msg,
                                confirmButtonColor: '#1e549e'
                            });
                        } else {
                            alert(msg);
                        }
                        return;
                    }
                }
            }
        });
    }
});