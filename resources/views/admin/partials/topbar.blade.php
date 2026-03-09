<header class="bg-white/80 backdrop-blur-md border-b border-gray-100 h-20 px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30 shadow-sm relative">
    <div class="flex items-center gap-4">
        <button onclick="toggleAdminSidebar()" class="lg:hidden text-gray-500 hover:text-agro-dark transition-colors bg-gray-100 hover:bg-gray-200 p-2 rounded-xl focus:outline-none">
            <span class="material-symbols-outlined text-[24px]">menu</span>
        </button>
        <div>
            <h1 class="text-xl font-black text-agro-dark leading-none">Corpo Agrícola</h1>
            <p class="text-[11px] font-bold text-gray-400 mt-1 uppercase tracking-wider">{{ date('d M Y, h:i A') }}</p>
        </div>
    </div>

    <div class="flex items-center gap-2 sm:gap-4 relative">
        
        {{-- CONTENEDOR NOTIFICACIONES --}}
        <div class="relative">
            <button id="btnNotificaciones" onclick="toggleNotificaciones()" class="relative p-2 text-gray-400 hover:text-primary transition-colors rounded-full hover:bg-gray-50 focus:outline-none">
                <span class="material-symbols-outlined">notifications</span>
                {{-- Punto rojo (Oculto por defecto hasta que el JS lo active) --}}
                <span id="notificaciones-badge" class="absolute top-1 right-1 flex h-3 w-3 items-center justify-center rounded-full bg-red-500 ring-2 ring-white hidden">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                </span>
            </button>

            {{-- Panel Desplegable de Notificaciones --}}
            <div id="panelNotificaciones" class="absolute right-0 top-full mt-3 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 hidden opacity-0 translate-y-4 transition-all duration-300 z-50 overflow-hidden">
                
                <div class="px-5 py-4 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="font-black text-agro-dark">Notificaciones</h3>
                    <span id="notificaciones-count-text" class="text-[10px] font-bold bg-primary/10 text-primary px-2 py-0.5 rounded-md">0 Nuevas</span>
                </div>

                <div id="listaNotificaciones" class="max-h-[60vh] overflow-y-auto custom-scrollbar divide-y divide-gray-50">
                    {{-- Loader inicial --}}
                    <div class="p-8 text-center text-gray-400">
                        <span class="material-symbols-outlined animate-spin text-3xl">autorenew</span>
                    </div>
                </div>

                <div class="p-3 border-t border-gray-50 text-center bg-gray-50/50 hidden" id="pieNotificaciones">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Actualizado en vivo</p>
                </div>
            </div>
        </div>
        
        {{-- CONTENEDOR PERFIL --}}
        <div class="relative">
            <button id="btnPerfil" onclick="togglePerfil()" class="flex items-center gap-3 pl-2 sm:pl-4 border-l border-gray-200 focus:outline-none hover:bg-gray-50 p-2 rounded-xl transition-colors">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-agro-dark leading-none">{{ Auth::user()->nombre ?? 'Administrador' }}</p>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-1">Super Admin</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-agro-dark text-white flex items-center justify-center font-bold shadow-sm transition-colors">
                    {{ substr(Auth::user()->nombre ?? 'A', 0, 1) }}
                </div>
            </button>
            
            {{-- Mini menú de sesión --}}
            <div id="panelPerfil" class="absolute right-0 top-full mt-3 w-48 bg-white rounded-xl shadow-xl border border-gray-100 hidden opacity-0 translate-y-2 transition-all duration-200 z-50">
                <div class="p-2">
                    <a href="#" class="flex items-center gap-2 px-3 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-[18px]">person</span> Mi Cuenta
                    </a>
                    <hr class="my-1 border-gray-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-sm font-bold text-red-500 hover:bg-red-50 hover:text-red-700 rounded-lg transition-colors text-left">
                            <span class="material-symbols-outlined text-[18px]">logout</span> Cerrar Sesión
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
                tituloCount.classList.replace('text-gray-400', 'text-primary');
                tituloCount.classList.replace('bg-gray-100', 'bg-primary/10');
            } else {
                badge.classList.add('hidden');
                tituloCount.innerText = 'Al día';
                tituloCount.classList.replace('text-primary', 'text-gray-400');
                tituloCount.classList.replace('bg-primary/10', 'bg-gray-100');
            }

            // 2. Inyectar HTML en la lista
            lista.innerHTML = '';
            
            if (data.alertas.length === 0) {
                lista.innerHTML = `
                    <div class="p-8 flex flex-col items-center justify-center text-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                            <span class="material-symbols-outlined text-gray-300 text-3xl">done_all</span>
                        </div>
                        <p class="text-sm font-black text-gray-700">¡Todo al día!</p>
                        <p class="text-xs text-gray-400 mt-1">No hay tareas pendientes por ahora.</p>
                    </div>`;
                pie.classList.add('hidden');
            } else {
                pie.classList.remove('hidden');
                data.alertas.forEach(alerta => {
                    lista.innerHTML += `
                        <a href="${alerta.url}" class="p-4 flex gap-4 hover:bg-gray-50/80 transition-colors group">
                            <div class="w-10 h-10 rounded-full ${alerta.bg} ${alerta.color} flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined text-[20px]">${alerta.icono}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start mb-0.5">
                                    <h4 class="text-sm font-black text-agro-dark truncate">${alerta.titulo}</h4>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase bg-white px-1.5 py-0.5 border border-gray-100 rounded">${alerta.tiempo}</span>
                                </div>
                                <p class="text-xs text-gray-500 leading-snug pr-2">${alerta.mensaje}</p>
                            </div>
                        </a>
                    `;
                });
            }
        })
        .catch(error => console.error('Error cargando notificaciones:', error));
    }

    // Ejecutar al cargar la página por primera vez
    document.addEventListener("DOMContentLoaded", () => {
        cargarNotificaciones();
        
        // El "Polling": Volver a consultar automáticamente cada 60 segundos (60000 ms)
        // Esto crea la magia del "Tiempo Real" sin saturar tu servidor cPanel
        setInterval(cargarNotificaciones, 60000); 
    });
</script>