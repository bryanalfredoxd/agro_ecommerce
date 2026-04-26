@php
    // Calculamos la cantidad total de items en el carrito para el usuario actual
    $cartCount = 0;
    if(Auth::check()) {
        $cartCount = (int) \App\Models\Carrito::where('usuario_id', Auth::id())->sum('cantidad');
    }
@endphp

<header class="bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-sm relative z-40 transition-all duration-300">
    <div class="layout-container">
        
        {{-- Redujimos el padding vertical de py-3/py-4 a py-2/py-2.5 --}}
        <div class="flex items-center justify-between py-2 lg:py-2.5 gap-4">
            
            {{-- 1. LOGO: Tamaños ajustados para ser más sutiles --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 flex-shrink-0 group outline-none">
                <div class="flex items-center justify-center size-8 sm:size-9 bg-agro-dark rounded-lg text-primary shadow-sm group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[20px] sm:text-[24px]">agriculture</span>
                </div>
                <div class="flex items-baseline gap-0.5">
                    <span class="text-agro-dark text-lg sm:text-xl font-black tracking-tight leading-none">Corpo</span>
                    <span class="text-agro-accent text-lg sm:text-xl font-black tracking-tight leading-none">Agrícola</span>
                </div>
            </a>
            
            {{-- BUSCADOR: Altura reducida a h-10 (40px) --}}
            <div class="hidden lg:flex flex-1 max-w-xl mx-auto px-6">
                <form action="{{ route('catalogo') }}" method="GET" class="relative w-full group">
                    <input type="text" 
                           name="buscar"
                           class="w-full h-10 pl-4 pr-10 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all text-sm placeholder:text-gray-700 group-hover:bg-white"
                           placeholder="¿Qué estás buscando para tu campo hoy?">
                    <button type="submit" class="absolute right-0 top-0 h-10 w-10 flex items-center justify-center text-agro-dark hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-[20px]">search</span>
                    </button>
                </form>
            </div>
            
            <div class="flex items-center gap-1.5 sm:gap-2">
                
                {{-- Lupa móvil: Ajustada a h-10 --}}
                <button onclick="document.getElementById('mobile-search').classList.toggle('hidden')" 
                        class="lg:hidden flex items-center justify-center w-10 h-10 text-agro-dark hover:bg-gray-100 rounded-xl transition-colors">
                    <span class="material-symbols-outlined text-[22px]">search</span>
                </button>

                {{-- 2. PERFIL DE USUARIO: GUEST --}}
                @guest
                    <a href="{{ route('login') }}" class="hidden sm:flex items-center gap-2.5 px-2 py-1.5 text-agro-dark hover:bg-gray-50 rounded-xl transition-all duration-300 border border-transparent hover:border-gray-200 group">
                        <div class="size-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-700 group-hover:bg-primary/10 group-hover:text-primary transition-colors shrink-0">
                            <span class="material-symbols-outlined text-[18px]">person</span>
                        </div>
                        <div class="flex flex-col items-start leading-none justify-center">
                            <span class="text-[9px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Bienvenido</span>
                            <span class="text-xs font-bold text-agro-dark group-hover:text-primary transition-colors">Ingresar</span>
                        </div>
                    </a>
                @endguest

                {{-- 3. PERFIL DE USUARIO: AUTENTICADO --}}
                @auth
                    <div class="hidden sm:flex relative group">
                        <button class="flex items-center gap-2.5 px-2 py-1.5 hover:bg-gray-50 rounded-xl transition-all duration-300 border border-transparent hover:border-gray-200 focus:outline-none">
                            <div class="size-8 bg-primary/10 rounded-full flex items-center justify-center text-primary font-black text-xs border border-primary/20 shrink-0">
                                {{ substr(Auth::user()->nombre, 0, 1) }}
                            </div>
                            <div class="flex flex-col items-start leading-none justify-center">
                                <span class="text-[9px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Hola,</span>
                                <span class="text-xs font-bold text-agro-dark flex items-center gap-1 group-hover:text-primary transition-colors">
                                    {{ Str::limit(Auth::user()->nombre, 12) }}
                                    <span class="material-symbols-outlined text-[14px] text-gray-700 group-hover:text-primary transition-colors">expand_more</span>
                                </span>
                            </div>
                        </button>

                        {{-- Tarjeta Desplegable Premium --}}
                        <div class="absolute top-full right-0 pt-2 w-56 hidden group-hover:block z-50">
                            <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-gray-100 overflow-hidden animate-fade-in-up">
                                
                                <div class="bg-gray-50/80 px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-black text-agro-dark truncate">{{ Auth::user()->nombre }}</p>
                                    <p class="text-[10px] text-gray-500 truncate mt-0.5 font-medium">{{ Auth::user()->email }}</p>
                                </div>
                                
                                <div class="p-2 space-y-1">
                                    
                                    @if(Auth::user()->rol_id == 1)
                                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-bold text-primary bg-primary/5 hover:bg-primary/10 rounded-xl transition-all duration-200 group/link">
                                            <span class="material-symbols-outlined text-[18px] text-primary transition-colors">admin_panel_settings</span> 
                                            Panel de Control
                                        </a>
                                        <div class="h-px bg-gray-100 my-1 mx-2"></div>
                                    @endif

                                    <a href="{{ route('perfil') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-gray-600 hover:bg-primary/10 hover:text-agro-dark rounded-xl transition-all duration-200 group/link">
                                        <span class="material-symbols-outlined text-[18px] text-gray-700 group-hover/link:text-primary transition-colors">account_circle</span> 
                                        Mi Perfil
                                    </a>
                                    <a href="{{ route('perfil.pedidos') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-gray-600 hover:bg-primary/10 hover:text-agro-dark rounded-xl transition-all duration-200 group/link">
                                        <span class="material-symbols-outlined text-[18px] text-gray-700 group-hover/link:text-primary transition-colors">inventory_2</span> 
                                        Mis Pedidos
                                    </a>
                                    
                                    <div class="h-px bg-gray-100 my-1 mx-2"></div>
                                    
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-sm font-bold text-red-500 hover:bg-red-50 rounded-xl transition-all duration-200 text-left group/btn">
                                            <span class="material-symbols-outlined text-[18px] text-red-400 group-hover/btn:text-red-600 transition-colors">logout</span> 
                                            Cerrar Sesión
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endauth
                
                {{-- 4. ICONO DEL CARRITO: Ajustado a w-10 h-10 (40px) --}}
                <a href="{{ route('carrito.index') }}" class="relative flex items-center justify-center size-10 text-agro-dark bg-gray-50 hover:bg-primary/10 hover:text-primary rounded-xl transition-all duration-300 group shrink-0">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">shopping_cart</span>
                    
                    <div class="absolute -top-1 -right-1 {{ $cartCount > 0 ? '' : 'hidden' }}" id="cart-badge-container">
                        <span id="cart-count-badge" 
                              class="flex min-w-[18px] h-[18px] px-1 items-center justify-center rounded-full bg-red-500 text-[9px] font-black text-white shadow-sm ring-2 ring-white">
                            {{ $cartCount }}
                        </span>
                    </div>
                </a>
                
                {{-- Botón menú móvil: Ajustado a w-10 h-10 --}}
                <button type="button" onclick="toggleMobileMenu()" class="lg:hidden flex items-center justify-center size-10 text-agro-dark bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors z-50 shrink-0">
                    <span class="material-symbols-outlined text-[24px]">menu</span>
                </button>
            </div>
        </div>
        
        {{-- Buscador Móvil Ajustado --}}
        <div id="mobile-search" class="hidden lg:hidden pb-3 animate-fade-in-up px-1">
            <form action="{{ route('catalogo') }}" method="GET" class="relative">
                <input type="text" 
                       name="buscar"
                       class="w-full h-10 pl-4 pr-10 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary text-sm shadow-sm"
                       placeholder="Buscar productos...">
                <button type="submit" class="absolute right-0 top-0 h-10 w-10 flex items-center justify-center text-primary font-bold">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                </button>
            </form>
        </div>
    </div>
</header>