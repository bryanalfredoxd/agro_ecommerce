<header class="bg-white/90 backdrop-blur-md border-b border-slate-200/80 h-16 sm:h-20 px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30 shadow-xs relative">
    <div class="flex items-center gap-4">
        <button onclick="toggleAdminSidebar()" class="lg:hidden text-slate-600 hover:text-[#1e549e] transition-colors bg-slate-100 hover:bg-slate-200 p-2 rounded-xl focus:outline-hidden">
            <span class="material-symbols-outlined text-[24px]">menu</span>
        </button>
        <div>
            <h1 class="text-base sm:text-lg font-black text-[#1e549e] leading-tight">Agrosuministros Rodríguez</h1>
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">{{ date('d M Y, h:i A') }}</p>
        </div>
    </div>

    <div class="flex items-center gap-2 sm:gap-4 relative">
        
        {{-- CONTENEDOR NOTIFICACIONES --}}
        <div class="relative">
            <button id="btnNotificaciones" onclick="toggleNotificaciones()" class="relative p-2 text-slate-600 hover:text-[#1e549e] transition-colors rounded-xl hover:bg-slate-100 focus:outline-hidden">
                <span class="material-symbols-outlined text-[22px]">notifications</span>
                {{-- Punto indicador --}}
                <span id="notificaciones-badge" class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5 items-center justify-center rounded-full bg-rose-500 ring-2 ring-white hidden">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                </span>
            </button>

            {{-- Panel Desplegable de Notificaciones --}}
            <div id="panelNotificaciones" class="absolute right-0 top-full mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-xl border border-slate-100 hidden opacity-0 translate-y-3 transition-all duration-200 z-50 overflow-hidden">
                
                <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                    <h3 class="font-bold text-[#1e549e] text-sm">Notificaciones del Sistema</h3>
                    <span id="notificaciones-count-text" class="text-[10px] font-bold bg-[#e5f2fb] text-[#1e549e] px-2 py-0.5 rounded-full">0 Nuevas</span>
                </div>

                {{-- Banner de Notificaciones de Escritorio (Windows / Navegador) --}}
                <div id="bannerNotificacionesEscritorio" class="px-4 py-2.5 bg-gradient-to-r from-blue-50 to-indigo-50/60 border-b border-blue-100 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-[#1e549e]" id="desktop-notif-icon">desktop_windows</span>
                        <div class="text-[11px] leading-tight font-medium text-slate-700" id="desktop-notif-status">
                            Alertas de Windows
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5" id="desktop-notif-action">
                        <button type="button" onclick="solicitarPermisoEscritorio()" class="px-2.5 py-1 bg-[#1e549e] hover:bg-[#164079] text-white text-[10px] font-bold rounded-lg shadow-xs transition-colors">
                            Activar
                        </button>
                    </div>
                </div>

                <div id="listaNotificaciones" class="max-h-[60vh] overflow-y-auto custom-scrollbar divide-y divide-slate-100">
                    <div class="p-8 text-center text-slate-400">
                        <span class="material-symbols-outlined animate-spin text-2xl">sync</span>
                    </div>
                </div>

                <div class="p-2.5 border-t border-slate-100 text-center bg-slate-50 hidden" id="pieNotificaciones">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Sincronizado en tiempo real</p>
                </div>
            </div>
        </div>
        
        {{-- CONTENEDOR PERFIL --}}
        <div class="relative">
            <button id="btnPerfil" onclick="togglePerfil()" class="flex items-center gap-3 pl-2 sm:pl-4 border-l border-slate-200 focus:outline-hidden hover:bg-slate-50 p-1.5 rounded-xl transition-colors">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold text-slate-800 leading-tight">{{ Auth::user()->nombre ?? 'Administrador' }}</p>
                    <p class="text-[10px] text-[#3492cf] font-bold uppercase tracking-wider">Administrador</p>
                </div>
                <div class="size-9 rounded-xl bg-[#1e549e] text-white flex items-center justify-center font-bold text-xs shadow-xs">
                    {{ strtoupper(substr(Auth::user()->nombre ?? 'A', 0, 1)) }}
                </div>
            </button>
            
            {{-- Menú de sesión --}}
            <div id="panelPerfil" class="absolute right-0 top-full mt-2 w-52 bg-white rounded-2xl shadow-xl border border-slate-100 hidden opacity-0 translate-y-2 transition-all duration-200 z-50 overflow-hidden">
                <div class="p-2 space-y-1">
                    <a href="{{ route('perfil') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 rounded-xl transition-colors">
                        <span class="material-symbols-outlined text-[18px] text-slate-400">person</span> Mi Perfil
                    </a>
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 rounded-xl transition-colors">
                        <span class="material-symbols-outlined text-[18px] text-slate-400">storefront</span> Ver Tienda
                    </a>
                    <hr class="my-1 border-slate-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 rounded-xl transition-colors text-left">
                            <span class="material-symbols-outlined text-[18px] text-rose-400">logout</span> Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    // ==========================================
    // LÓGICA DE MENÚS DESPLEGABLES (DROPDOWNS)
    // ==========================================
    const panelNotificaciones = document.getElementById('panelNotificaciones');
    const panelPerfil = document.getElementById('panelPerfil');

    function toggleNotificaciones() {
        if(panelPerfil.classList.contains('block')) togglePerfil(); // Cerrar el otro

        if(panelNotificaciones.classList.contains('hidden')) {
            panelNotificaciones.classList.remove('hidden');
            setTimeout(() => { panelNotificaciones.classList.remove('opacity-0', 'translate-y-4'); }, 10);
            cargarNotificaciones(); // Cargar al instante al abrir
        } else {
            panelNotificaciones.classList.add('opacity-0', 'translate-y-4');
            setTimeout(() => { panelNotificaciones.classList.add('hidden'); }, 300);
        }
    }

    function togglePerfil() {
        if(panelNotificaciones.classList.contains('block') || !panelNotificaciones.classList.contains('hidden')) toggleNotificaciones();

        if(panelPerfil.classList.contains('hidden')) {
            panelPerfil.classList.remove('hidden');
            setTimeout(() => { panelPerfil.classList.remove('opacity-0', 'translate-y-2'); }, 10);
        } else {
            panelPerfil.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => { panelPerfil.classList.add('hidden'); }, 200);
        }
    }

    // Cerrar al hacer clic fuera
    document.addEventListener('click', (event) => {
        if (!event.target.closest('#btnNotificaciones') && !event.target.closest('#panelNotificaciones')) {
            if (!panelNotificaciones.classList.contains('hidden')) toggleNotificaciones();
        }
        if (!event.target.closest('#btnPerfil') && !event.target.closest('#panelPerfil')) {
            if (!panelPerfil.classList.contains('hidden')) togglePerfil();
        }
    });

    // ==========================================
    // SISTEMA DE ALERTAS Y NOTIFICACIONES NATIVAS
    // ==========================================
    let lastSeenOrderId = localStorage.getItem('agro_last_order_id') ? parseInt(localStorage.getItem('agro_last_order_id')) : null;
    let lastSeenPagoId = localStorage.getItem('agro_last_pago_id') ? parseInt(localStorage.getItem('agro_last_pago_id')) : null;
    let isInitialFetch = true;

    // Chime sintético con Web Audio API (No requiere archivos externos y suena impecable)
    function reproducirChime() {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            const audioCtx = new AudioContext();

            // Nota 1 (D5)
            const osc1 = audioCtx.createOscillator();
            const gain1 = audioCtx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(587.33, audioCtx.currentTime);
            gain1.gain.setValueAtTime(0.2, audioCtx.currentTime);
            gain1.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.3);
            osc1.connect(gain1);
            gain1.connect(audioCtx.destination);
            osc1.start(audioCtx.currentTime);
            osc1.stop(audioCtx.currentTime + 0.3);

            // Nota 2 (A5)
            const osc2 = audioCtx.createOscillator();
            const gain2 = audioCtx.createGain();
            osc2.type = 'triangle';
            osc2.frequency.setValueAtTime(880.00, audioCtx.currentTime + 0.12);
            gain2.gain.setValueAtTime(0.25, audioCtx.currentTime + 0.12);
            gain2.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.55);
            osc2.connect(gain2);
            gain2.connect(audioCtx.destination);
            osc2.start(audioCtx.currentTime + 0.12);
            osc2.stop(audioCtx.currentTime + 0.55);
        } catch(e) {
            console.log('Audio alert blocked or unsupported:', e);
        }
    }

    // Actualizar estado del banner de permisos
    function actualizarEstadoPermisoEscritorio() {
        const icon = document.getElementById('desktop-notif-icon');
        const status = document.getElementById('desktop-notif-status');
        const action = document.getElementById('desktop-notif-action');

        if (!("Notification" in window)) {
            status.innerHTML = '<span class="text-slate-400">Navegador no compatible</span>';
            action.innerHTML = '';
            return;
        }

        if (Notification.permission === 'granted') {
            icon.className = 'material-symbols-outlined text-[18px] text-emerald-600';
            status.innerHTML = '<span class="text-emerald-700 font-bold">Alertas Windows Activas</span>';
            action.innerHTML = `
                <button type="button" onclick="probarAlertaNativa()" class="px-2 py-0.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-[10px] font-bold rounded-lg transition-colors flex items-center gap-1" title="Probar sonido y notificación">
                    <span class="material-symbols-outlined text-[14px]">volume_up</span> Probar
                </button>
            `;
        } else if (Notification.permission === 'denied') {
            icon.className = 'material-symbols-outlined text-[18px] text-rose-500';
            status.innerHTML = '<span class="text-rose-600 font-bold">Bloqueadas en Navegador</span>';
            action.innerHTML = '<span class="text-[9px] text-slate-400">Ver permisos</span>';
        } else {
            icon.className = 'material-symbols-outlined text-[18px] text-[#1e549e]';
            status.innerHTML = '<span class="text-slate-700 font-bold">Activar Alertas de Windows</span>';
            action.innerHTML = `
                <button type="button" onclick="solicitarPermisoEscritorio()" class="px-2.5 py-1 bg-[#1e549e] hover:bg-[#164079] text-white text-[10px] font-bold rounded-lg shadow-xs transition-colors">
                    Activar
                </button>
            `;
        }
    }

    function solicitarPermisoEscritorio() {
        if (!("Notification" in window)) {
            alert('Tu navegador actual no soporta la API de notificaciones.');
            return;
        }

        Notification.requestPermission().then(permission => {
            actualizarEstadoPermisoEscritorio();
            if (permission === 'granted') {
                reproducirChime();
                const testNotif = new Notification('🛒 Agrosuministros Rodríguez', {
                    body: '¡Alertas activadas con éxito! Recibirás avisos en Windows cuando entren nuevos pedidos.',
                    icon: '{{ asset("favicon.ico") }}'
                });
            }
        });
    }

    function probarAlertaNativa() {
        reproducirChime();
        if ("Notification" in window && Notification.permission === 'granted') {
            const notif = new Notification('🔔 Prueba de Alerta - Agrosuministros', {
                body: 'El sistema de notificaciones de escritorio está funcionando perfectamente.',
                icon: '{{ asset("favicon.ico") }}'
            });
            notif.onclick = () => window.focus();
        }
    }

    // Notificación en el Navegador para activación / desactivación de Delivery
    window.enviarNotificacionNavegadorDelivery = function(activo) {
        const titulo = activo 
            ? '🚚 ¡Servicio de Delivery HABILITADO!' 
            : '⚠️ ¡Servicio de Delivery PAUSADO!';
        const cuerpo = activo 
            ? 'Los clientes ya pueden solicitar envíos a domicilio en la tienda web.' 
            : 'El servicio de envíos ha sido pausado. Solo retiro en tienda física disponible.';
        const icono = '{{ asset("favicon.ico") }}';

        // 1. Chime sonoro corporativo
        reproducirChime();

        // 2. Notificación nativa del Navegador / Sistema Operativo
        if ("Notification" in window) {
            if (Notification.permission === 'granted') {
                try {
                    const notif = new Notification(titulo, {
                        body: cuerpo,
                        icon: icono,
                        tag: 'delivery-status-alert',
                        badge: icono
                    });
                    notif.onclick = () => window.focus();
                } catch (e) {
                    console.log('Error al emitir notificación nativa:', e);
                }
            } else if (Notification.permission === 'default') {
                Notification.requestPermission().then(permission => {
                    actualizarEstadoPermisoEscritorio();
                    if (permission === 'granted') {
                        try {
                            new Notification(titulo, { body: cuerpo, icon: icono });
                        } catch(e) {}
                    }
                });
            }
        }

        // 3. Notificación Toast visual en la pantalla
        if (typeof showToast === 'function') {
            showToast(cuerpo, activo ? 'success' : 'error');
        } else if (typeof Swal !== 'undefined') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true
            });
            Toast.fire({
                icon: activo ? 'success' : 'warning',
                title: titulo,
                text: cuerpo
            });
        }
    };

    // ==========================================
    // MOTOR DE NOTIFICACIONES (AJAX POLLING)
    // ==========================================
    function cargarNotificaciones() {
        fetch(`{{ route('admin.notificaciones.fetch') }}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notificaciones-badge');
            const lista = document.getElementById('listaNotificaciones');
            const tituloCount = document.getElementById('notificaciones-count-text');
            const pie = document.getElementById('pieNotificaciones');

            // 1. Actualizar el "Punto Rojo" de alerta
            if (data.total > 0) {
                badge.classList.remove('hidden');
                tituloCount.innerText = `${data.cantidad_eventos} Pendientes`;
            } else {
                badge.classList.add('hidden');
                tituloCount.innerText = 'Al día';
            }

            // 2. DISPARADOR DE NOTIFICACIÓN NATIVA DE ESCRITORIO PARA NUEVOS PEDIDOS
            if (data.ultimo_pedido && data.ultimo_pedido.id) {
                if (!isInitialFetch && lastSeenOrderId !== null && data.ultimo_pedido.id > lastSeenOrderId) {
                    // ¡NUEVA COMPRA DETECTADA!
                    reproducirChime();
                    if ("Notification" in window && Notification.permission === 'granted') {
                        const notifPedido = new Notification(`🛒 ¡Nuevo Pedido en Línea #${data.ultimo_pedido.id}!`, {
                            body: `${data.ultimo_pedido.cliente} realizó una compra por $${data.ultimo_pedido.total_usd}.\nHaz clic para despachar.`,
                            icon: '{{ asset("favicon.ico") }}',
                            tag: `orden-${data.ultimo_pedido.id}`,
                            requireInteraction: true
                        });
                        notifPedido.onclick = () => {
                            window.focus();
                            window.location.href = data.ultimo_pedido.url;
                        };
                    }
                }
                lastSeenOrderId = data.ultimo_pedido.id;
                localStorage.setItem('agro_last_order_id', lastSeenOrderId);
            }

            // 3. DISPARADOR DE NOTIFICACIÓN PARA NUEVOS PAGOS POR VERIFICAR
            if (data.ultimo_pago && data.ultimo_pago.id) {
                if (!isInitialFetch && lastSeenPagoId !== null && data.ultimo_pago.id > lastSeenPagoId) {
                    // ¡NUEVO PAGO DETECTADO!
                    reproducirChime();
                    if ("Notification" in window && Notification.permission === 'granted') {
                        const notifPago = new Notification(`💳 ¡Nuevo Pago por Verificar #${data.ultimo_pago.id}!`, {
                            body: `${data.ultimo_pago.cliente} reportó $${data.ultimo_pago.monto_usd} (${data.ultimo_pago.metodo}). Ref: ${data.ultimo_pago.referencia}`,
                            icon: '{{ asset("favicon.ico") }}',
                            tag: `pago-${data.ultimo_pago.id}`
                        });
                        notifPago.onclick = () => {
                            window.focus();
                            window.location.href = data.ultimo_pago.url;
                        };
                    }
                }
                lastSeenPagoId = data.ultimo_pago.id;
                localStorage.setItem('agro_last_pago_id', lastSeenPagoId);
            }

            // 4. DISPARADOR DE NOTIFICACIÓN DE ESTADO DE DELIVERY (ACTIVADO / PAUSADO)
            if (data.delivery_activo !== undefined) {
                const currentDeliveryStr = data.delivery_activo ? '1' : '0';
                const lastDeliveryState = localStorage.getItem('agro_last_delivery_estado');
                if (!isInitialFetch && lastDeliveryState !== null && lastDeliveryState !== currentDeliveryStr) {
                    window.enviarNotificacionNavegadorDelivery(data.delivery_activo);
                }
                localStorage.setItem('agro_last_delivery_estado', currentDeliveryStr);
            }

            isInitialFetch = false;

            // 5. Inyectar HTML en la lista
            lista.innerHTML = '';
            
            if (data.alertas.length === 0) {
                lista.innerHTML = `
                    <div class="p-8 flex flex-col items-center justify-center text-center">
                        <div class="w-14 h-14 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                            <span class="material-symbols-outlined text-slate-300 text-3xl">done_all</span>
                        </div>
                        <p class="text-sm font-black text-slate-700">¡Todo al día!</p>
                        <p class="text-xs text-slate-400 mt-1">No hay tareas pendientes por ahora.</p>
                    </div>`;
                pie.classList.add('hidden');
            } else {
                pie.classList.remove('hidden');
                data.alertas.forEach(alerta => {
                    lista.innerHTML += `
                        <a href="${alerta.url}" class="p-3.5 sm:p-4 flex gap-3 hover:bg-slate-50 transition-colors group">
                            <div class="w-9 h-9 rounded-xl ${alerta.bg} ${alerta.color} flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined text-[18px]">${alerta.icono}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start mb-0.5">
                                    <h4 class="text-xs font-black text-slate-800 truncate">${alerta.titulo}</h4>
                                    <span class="text-[9px] font-bold text-slate-500 uppercase bg-slate-100 px-1.5 py-0.5 rounded">${alerta.tiempo}</span>
                                </div>
                                <p class="text-[11px] text-slate-500 leading-snug pr-2">${alerta.mensaje}</p>
                            </div>
                        </a>
                    `;
                });
            }
        })
        .catch(error => console.error('Error cargando notificaciones:', error));
    }

    // Inicializar al cargar
    document.addEventListener("DOMContentLoaded", () => {
        actualizarEstadoPermisoEscritorio();
        cargarNotificaciones();
        
        // Polling cada 20 segundos (20000 ms) para respuesta ágil sin saturar el servidor
        setInterval(cargarNotificaciones, 20000); 
    });
</script>