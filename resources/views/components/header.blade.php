@php
    $cartCount = 0;
    if(Auth::check()) {
        $cartCount = (int) \App\Models\Carrito::where('usuario_id', Auth::id())->sum('cantidad');
    }
@endphp

<header class="bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs relative z-40 transition-all duration-300">
    <div class="layout-container">
        
        <div class="flex items-center justify-between py-2.5 lg:py-3 gap-4">
            
            {{-- 1. LOGO OFICIAL AGROSUMINISTROS RODRÍGUEZ --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 sm:gap-3 flex-shrink-0 group outline-hidden select-none">
                <img src="{{ asset('img/logo.png') }}" 
                     alt="Agrosuministros Rodríguez" 
                     class="h-11 sm:h-12 lg:h-14 w-11 sm:w-12 lg:w-14 object-contain rounded-full shadow-xs transition-transform duration-200 group-hover:scale-105 shrink-0">
                <div class="hidden sm:flex flex-col leading-none">
                    <span class="text-sm sm:text-base lg:text-lg font-black tracking-tight text-[#1e549e] group-hover:text-[#3492cf] transition-colors uppercase">
                        Agrosuministros
                    </span>
                    <span class="text-[10px] sm:text-xs font-black tracking-wider text-[#3492cf] uppercase mt-0.5">
                        Rodríguez
                    </span>
                </div>
            </a>
            
            {{-- BUSCADOR PRINCIPAL CON RESULTADOS EN VIVO --}}
            <div class="hidden lg:flex flex-1 max-w-xl mx-auto px-6" id="header-search-container">
                <div class="relative w-full" id="header-search-wrapper" style="position: relative; width: 100%;">
                    <form action="{{ route('catalogo') }}" method="GET" class="relative w-full group" id="header-search-form" style="position: relative; width: 100%;">
                        <input type="text" 
                               name="buscar"
                               id="header-search-input"
                               autocomplete="off"
                               value="{{ request('buscar') }}"
                               class="w-full h-11 pl-4 pr-11 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#3492cf]/30 focus:border-[#3492cf] transition-all text-sm placeholder:text-slate-400 group-hover:bg-white text-slate-800 shadow-xs"
                               placeholder="¿Qué producto buscas para tu campo hoy?">
                        
                        {{-- Spinner de búsqueda --}}
                        <div id="header-search-spinner" class="absolute right-10 top-3 hidden text-[#3492cf]">
                            <span class="material-symbols-outlined text-[20px] animate-spin">progress_activity</span>
                        </div>

                        <button type="submit" class="absolute right-0 top-0 h-11 w-11 flex items-center justify-center text-[#1e549e] hover:text-[#3492cf] transition-colors" title="Buscar">
                            <span class="material-symbols-outlined text-[20px]">search</span>
                        </button>
                    </form>

                    {{-- Contenedor de Resultados en Vivo (Desktop: Ancho idéntico al buscador 100%) --}}
                    <div id="header-search-results" 
                         style="position: absolute; left: 0; right: 0; top: 100%; width: 100%; margin-top: 0.5rem; z-index: 9999;"
                         class="bg-white rounded-2xl shadow-2xl border border-slate-200/90 overflow-hidden hidden transition-all duration-200 max-h-[440px] overflow-y-auto">
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                
                {{-- Lupa móvil --}}
                <button onclick="document.getElementById('mobile-search').classList.toggle('hidden')" 
                        class="lg:hidden flex items-center justify-center size-10 text-slate-700 hover:bg-slate-100 rounded-xl transition-colors">
                    <span class="material-symbols-outlined text-[22px]">search</span>
                </button>

                {{-- PERFIL DE USUARIO: GUEST --}}
                @guest
                    <a href="{{ route('login') }}" class="hidden sm:flex items-center gap-2.5 px-3 py-1.5 text-slate-700 hover:bg-slate-50 rounded-xl transition-all duration-200 border border-transparent hover:border-slate-200 group">
                        <div class="size-8 bg-slate-100 rounded-full flex items-center justify-center text-slate-600 group-hover:bg-[#3492cf]/10 group-hover:text-[#1e549e] transition-colors shrink-0">
                            <span class="material-symbols-outlined text-[18px]">person</span>
                        </div>
                        <div class="flex flex-col items-start leading-none justify-center">
                            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mb-0.5">Bienvenido</span>
                            <span class="text-xs font-bold text-[#1e549e] group-hover:text-[#3492cf] transition-colors">Ingresar</span>
                        </div>
                    </a>
                @endguest

                {{-- PERFIL DE USUARIO: AUTENTICADO --}}
                @auth
                    <div class="hidden sm:flex relative group">
                        <button class="flex items-center gap-2.5 px-2.5 py-1.5 hover:bg-slate-50 rounded-xl transition-all duration-200 border border-transparent hover:border-slate-200 focus:outline-hidden">
                            <div class="size-8 bg-[#1e549e] text-white rounded-full flex items-center justify-center font-black text-xs shadow-xs shrink-0">
                                {{ strtoupper(substr(Auth::user()->nombre, 0, 1)) }}
                            </div>
                            <div class="flex flex-col items-start leading-none justify-center text-left">
                                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mb-0.5">Hola,</span>
                                <span class="text-xs font-bold text-slate-800 flex items-center gap-1 group-hover:text-[#1e549e] transition-colors">
                                    {{ Str::limit(Auth::user()->nombre, 12) }}
                                    <span class="material-symbols-outlined text-[14px] text-slate-400 group-hover:text-[#1e549e] transition-colors">expand_more</span>
                                </span>
                            </div>
                        </button>

                        {{-- Dropdown Menú --}}
                        <div class="absolute top-full right-0 pt-2 w-60 hidden group-hover:block z-50">
                            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden animate-fade-in-up">
                                
                                <div class="bg-slate-50 px-4 py-3 border-b border-slate-100">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-sm font-bold text-[#1e549e] truncate">{{ Auth::user()->nombre }} {{ Auth::user()->apellido }}</p>
                                        @if(Auth::user()->rol && (int)Auth::user()->rol_id !== 4)
                                            <span class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md bg-[#1e549e]/10 text-[#1e549e] shrink-0">
                                                {{ Auth::user()->rol->nombre }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ Auth::user()->email }}</p>
                                </div>
                                
                                <div class="p-2 space-y-1">
                                    @if(Auth::user()->puedeAccederPanelAdmin())
                                        <a href="{{ Auth::user()->ruta_panel }}" class="flex items-center gap-3 px-3 py-2 text-sm font-bold text-[#1e549e] bg-[#e5f2fb] hover:bg-[#aad6f3]/30 rounded-xl transition-colors">
                                            <span class="material-symbols-outlined text-[18px] text-[#1e549e]">{{ Auth::user()->icono_panel }}</span> 
                                            <span>{{ Auth::user()->nombre_panel }}</span>
                                        </a>
                                        <div class="h-px bg-slate-100 my-1 mx-2"></div>
                                    @endif

                                    <a href="{{ route('perfil') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-[#1e549e] rounded-xl transition-colors">
                                        <span class="material-symbols-outlined text-[18px] text-slate-400">person</span> 
                                        Mi Perfil
                                    </a>
                                    <a href="{{ route('perfil.pedidos') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-[#1e549e] rounded-xl transition-colors">
                                        <span class="material-symbols-outlined text-[18px] text-slate-400">inventory_2</span> 
                                        Mis Pedidos
                                    </a>
                                    <a href="{{ route('perfil.favoritos') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-[#1e549e] rounded-xl transition-colors">
                                        <span class="material-symbols-outlined text-[18px] text-red-500 fill-1">favorite</span> 
                                        Mis Favoritos
                                    </a>
                                    
                                    <div class="h-px bg-slate-100 my-1 mx-2"></div>
                                    
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-sm font-bold text-rose-600 hover:bg-rose-50 rounded-xl transition-colors text-left">
                                            <span class="material-symbols-outlined text-[18px] text-rose-400">logout</span> 
                                            Cerrar Sesión
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endauth
                
                {{-- ICONO DEL CARRITO --}}
                <a href="{{ route('carrito.index') }}" class="relative flex items-center justify-center size-10 text-slate-700 bg-slate-50 hover:bg-[#e5f2fb] hover:text-[#1e549e] rounded-xl transition-all duration-200 shrink-0">
                    <span class="material-symbols-outlined text-[22px]">shopping_cart</span>
                    
                    <div class="absolute -top-1 -right-1 {{ $cartCount > 0 ? '' : 'hidden' }}" id="cart-badge-container">
                        <span id="cart-count-badge" 
                              class="flex min-w-[18px] h-[18px] px-1 items-center justify-center rounded-full bg-[#1e549e] text-[9px] font-black text-white shadow-xs ring-2 ring-white">
                            {{ $cartCount }}
                        </span>
                    </div>
                </a>
                
                {{-- Botón menú móvil --}}
                <button type="button" onclick="toggleMobileMenu()" class="lg:hidden flex items-center justify-center size-10 text-slate-700 bg-slate-50 hover:bg-slate-100 rounded-xl transition-colors shrink-0">
                    <span class="material-symbols-outlined text-[24px]">menu</span>
                </button>
            </div>
        </div>
        
        {{-- Buscador Móvil con Resultados en Vivo --}}
        <div id="mobile-search" class="hidden lg:hidden pb-3 animate-fade-in-up px-1 relative">
            <div class="relative w-full" id="mobile-search-container" style="position: relative; width: 100%;">
                <form action="{{ route('catalogo') }}" method="GET" class="relative w-full" style="position: relative; width: 100%;">
                    <input type="text" 
                           name="buscar"
                           id="mobile-search-input"
                           autocomplete="off"
                           value="{{ request('buscar') }}"
                           class="w-full h-10 pl-4 pr-10 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#3492cf]/30 focus:border-[#3492cf] text-sm shadow-xs"
                           placeholder="Buscar insumos o medicamentos...">
                    
                    {{-- Spinner móvil --}}
                    <div id="mobile-search-spinner" class="absolute right-10 top-2.5 hidden text-[#3492cf]">
                        <span class="material-symbols-outlined text-[20px] animate-spin">progress_activity</span>
                    </div>

                    <button type="submit" class="absolute right-0 top-0 h-10 w-10 flex items-center justify-center text-[#1e549e] font-bold">
                        <span class="material-symbols-outlined text-[20px]">search</span>
                    </button>
                </form>

                {{-- Resultados en vivo móvil --}}
                <div id="mobile-search-results" 
                     style="position: absolute; left: 0; right: 0; top: 100%; width: 100%; margin-top: 0.5rem; z-index: 9999;"
                     class="bg-white rounded-2xl shadow-2xl border border-slate-200/90 overflow-hidden hidden max-h-[60vh] overflow-y-auto">
                </div>
            </div>
        </div>
    </div>
</header>

{{-- SCRIPT: BÚSQUEDA EN VIVO (LIVE AUTOCOMPLETE SEARCH) --}}
<script>
    (function() {
        const liveSearchUrl = "{{ route('catalogo.buscar-en-vivo') }}";
        const catalogoBaseUrl = "{{ route('catalogo') }}";

        function setupLiveSearch(inputId, resultsId, spinnerId) {
            const input = document.getElementById(inputId);
            const results = document.getElementById(resultsId);
            const spinner = document.getElementById(spinnerId);

            if (!input || !results) return;

            let debounceTimer = null;
            let abortController = null;

            function hideResults() {
                results.classList.add('hidden');
                results.innerHTML = '';
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text || '';
                return div.innerHTML;
            }

            function highlightMatch(text, query) {
                if (!query) return escapeHtml(text);
                const safeText = escapeHtml(text);
                const safeQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                const regex = new RegExp(`(${safeQuery})`, 'gi');
                return safeText.replace(regex, '<span class="text-[#1e549e] bg-[#e5f2fb] font-black px-0.5 rounded">$1</span>');
            }

            async function performSearch(query) {
                if (query.length < 2) {
                    hideResults();
                    if (spinner) spinner.classList.add('hidden');
                    return;
                }

                if (abortController) {
                    abortController.abort();
                }
                abortController = new AbortController();

                if (spinner) spinner.classList.remove('hidden');

                try {
                    const response = await fetch(`${liveSearchUrl}?q=${encodeURIComponent(query)}`, {
                        signal: abortController.signal,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) throw new Error('Error al buscar');
                    const data = await response.json();

                    if (query !== input.value.trim()) return;

                    renderResults(data, query);
                } catch (err) {
                    if (err.name !== 'AbortError') {
                        console.error('Error en búsqueda en vivo:', err);
                    }
                } finally {
                    if (spinner) spinner.classList.add('hidden');
                }
            }

            function renderResults(data, query) {
                const productos = data.productos || [];

                if (productos.length === 0) {
                    results.innerHTML = `
                        <div class="p-6 text-center">
                            <div class="w-12 h-12 rounded-full bg-[#e5f2fb] text-[#3492cf] flex items-center justify-center mx-auto mb-2.5">
                                <span class="material-symbols-outlined text-[24px]">search_off</span>
                            </div>
                            <p class="text-xs font-bold text-slate-800">No encontramos resultados para "${escapeHtml(query)}"</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Intenta con otro término o consulta todo el catálogo</p>
                            <a href="${catalogoBaseUrl}" class="inline-flex items-center gap-1 mt-3 px-3.5 py-1.5 rounded-lg bg-[#e5f2fb] hover:bg-[#aad6f3]/40 text-[#1e549e] text-xs font-bold transition-colors">
                                <span class="material-symbols-outlined text-[16px]">storefront</span> Ver todo el catálogo
                            </a>
                        </div>
                    `;
                    results.classList.remove('hidden');
                    return;
                }

                let html = `
                    <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <div class="flex items-center gap-1.5 text-slate-700">
                            <span class="material-symbols-outlined text-[16px] text-[#3492cf]">manage_search</span>
                            <span>Sugerencias de productos (${productos.length})</span>
                        </div>
                        <span class="text-[#3492cf] font-semibold text-[10px] normal-case tracking-normal hidden sm:inline-flex items-center gap-1">
                            <span>Presiona <kbd class="px-1.5 py-0.5 text-[9px] font-bold bg-white border border-slate-200 rounded text-slate-600 shadow-2xs">Enter</kbd> para buscar todo</span>
                        </span>
                    </div>
                    <div class="divide-y divide-slate-100 max-h-[340px] overflow-y-auto">
                `;

                productos.forEach(prod => {
                    html += `
                        <a href="${prod.url}" class="flex items-center gap-3.5 p-3 sm:px-4 hover:bg-[#e5f2fb]/40 transition-colors group">
                            <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200/80 overflow-hidden shrink-0 flex items-center justify-center relative">
                                <span class="material-symbols-outlined text-slate-300 text-2xl absolute">inventory_2</span>
                                ${prod.imagen 
                                    ? `<img src="${prod.imagen}" alt="" class="w-full h-full object-cover relative z-10" onerror="this.remove()">` 
                                    : ''
                                }
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="text-[10px] font-extrabold uppercase tracking-wide text-[#1e549e] bg-[#e5f2fb] px-2 py-0.5 rounded-md">
                                        ${escapeHtml(prod.categoria)}
                                    </span>
                                    ${prod.marca ? `<span class="text-[11px] text-slate-400 font-medium truncate">• ${escapeHtml(prod.marca)}</span>` : ''}
                                </div>
                                <h4 class="text-xs sm:text-sm font-bold text-slate-800 truncate group-hover:text-[#1e549e] transition-colors mt-0.5">
                                    ${highlightMatch(prod.nombre, query)}
                                </h4>
                                <div class="flex items-baseline gap-2 mt-0.5">
                                    <span class="text-xs sm:text-sm font-black text-[#1e549e]">$${prod.precio_usd}</span>
                                    <span class="text-[11px] text-slate-500 font-medium">Bs. ${prod.precio_bs}</span>
                                    ${prod.stock_total <= 5 ? '<span class="text-[9px] font-bold text-amber-700 bg-amber-50 border border-amber-200/60 px-1.5 py-0.5 rounded-md">Poco stock</span>' : ''}
                                </div>
                            </div>
                            <div class="size-8 rounded-lg flex items-center justify-center text-slate-300 group-hover:text-[#1e549e] group-hover:bg-white group-hover:shadow-xs transition-all shrink-0">
                                <span class="material-symbols-outlined text-[18px] group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                            </div>
                        </a>
                    `;
                });

                html += `
                    </div>
                    <a href="${catalogoBaseUrl}?buscar=${encodeURIComponent(query)}" class="flex items-center justify-center gap-2 p-3 bg-slate-50 hover:bg-[#e5f2fb] text-center border-t border-slate-100 text-xs font-bold text-[#1e549e] transition-colors group">
                        <span>Ver todos los resultados para "${escapeHtml(query)}"</span>
                        <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </a>
                `;

                results.innerHTML = html;
                results.classList.remove('hidden');
            }

            input.addEventListener('input', function() {
                const val = this.value.trim();
                clearTimeout(debounceTimer);

                if (val.length < 2) {
                    hideResults();
                    if (spinner) spinner.classList.add('hidden');
                    return;
                }

                debounceTimer = setTimeout(() => {
                    performSearch(val);
                }, 250);
            });

            input.addEventListener('focus', function() {
                const val = this.value.trim();
                if (val.length >= 2 && results.children.length > 0) {
                    results.classList.remove('hidden');
                }
            });

            document.addEventListener('click', function(e) {
                if (!input.contains(e.target) && !results.contains(e.target)) {
                    hideResults();
                }
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    hideResults();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            setupLiveSearch('header-search-input', 'header-search-results', 'header-search-spinner');
            setupLiveSearch('mobile-search-input', 'mobile-search-results', 'mobile-search-spinner');
        });
    })();
</script>