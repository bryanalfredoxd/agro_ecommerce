@php
    // Calculamos la cantidad total de items en el carrito para el usuario actual
    $cartCount = 0;
    if(Auth::check()) {
        // Sumamos la columna 'cantidad' de la tabla 'carrito'
        $cartCount = (int) \App\Models\Carrito::where('usuario_id', Auth::id())->sum('cantidad');
    }
@endphp

<header class="bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-sm relative z-40 transition-all duration-300">
    <div class="layout-container">
        
        <div class="flex items-center justify-between py-3 lg:py-4 gap-4">
            
            {{-- 1. LOGO REDISEÑADO: Más elegante y moderno --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3 flex-shrink-0 group outline-none">
                <div class="flex items-center justify-center size-9 sm:size-10 bg-agro-dark rounded-lg text-primary shadow-sm group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[24px] sm:text-[28px]">agriculture</span>
                </div>
                <div class="flex items-baseline gap-1">
                    <span class="text-agro-dark text-xl sm:text-2xl font-black tracking-tight leading-none">Corpo</span>
                    <span class="text-agro-accent text-xl sm:text-2xl font-black tracking-tight leading-none">Agrícola</span>
                </div>
            </a>
            
            {{-- BUSCADOR: Intacto --}}
            <div class="hidden lg:flex flex-1 max-w-xl mx-auto px-6">
                <form action="{{ route('catalogo') }}" method="GET" class="relative w-full group">
                    <input type="text" 
                           name="buscar"
                           class="w-full h-11 pl-4 pr-12 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all text-sm placeholder:text-gray-400 group-hover:bg-white"
                           placeholder="¿Qué estás buscando para tu campo hoy?">
                    <button type="submit" class="absolute right-0 top-0 h-11 w-12 flex items-center justify-center text-agro-dark hover:text-primary transition-colors">
                        <span class="material-symbols-outlined">search</span>
                    </button>
                </form>
            </div>
            
            <div class="flex items-center gap-2 sm:gap-3">
                
                <button onclick="document.getElementById('mobile-search').classList.toggle('hidden')" 
                        class="lg:hidden p-2 text-agro-dark hover:bg-gray-100 rounded-xl transition-colors">
                    <span class="material-symbols-outlined text-[24px]">search</span>
                </button>

                {{-- 2. PERFIL DE USUARIO: GUEST --}}
                @guest
                    <a href="{{ route('login') }}" class="hidden sm:flex items-center gap-3 px-3 py-2 text-agro-dark hover:bg-gray-50 rounded-xl transition-all duration-300 border border-transparent hover:border-gray-200 group">
                        <div class="w-9 h-9 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 group-hover:bg-primary/10 group-hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px]">person</span>
                        </div>
                        <div class="flex flex-col items-start leading-tight">
                            <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Bienvenido</span>
                            <span class="text-sm font-bold text-agro-dark group-hover:text-primary transition-colors">Ingresar</span>
                        </div>
                    </a>
                @endguest

                {{-- 3. PERFIL DE USUARIO: AUTENTICADO --}}
                @auth
                    <div class="hidden sm:flex relative group">
                        <button class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 rounded-xl transition-all duration-300 border border-transparent hover:border-gray-200 focus:outline-none">
                            <div class="w-9 h-9 bg-primary/10 rounded-full flex items-center justify-center text-primary font-black text-sm border border-primary/20">
                                {{ substr(Auth::user()->nombre, 0, 1) }}
                            </div>
                            <div class="flex flex-col items-start leading-tight">
                                <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Hola,</span>
                                <span class="text-sm font-bold text-agro-dark flex items-center gap-1 group-hover:text-primary transition-colors">
                                    {{ Str::limit(Auth::user()->nombre, 12) }}
                                    <span class="material-symbols-outlined text-[16px] text-gray-400 group-hover:text-primary transition-colors">expand_more</span>
                                </span>
                            </div>
                        </button>

                        {{-- Tarjeta Desplegable Premium --}}
                        <div class="absolute top-full right-0 pt-2 w-64 hidden group-hover:block z-50">
                            <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-gray-100 overflow-hidden animate-fade-in-up">
                                
                                <div class="bg-gray-50/80 px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-black text-agro-dark truncate">{{ Auth::user()->nombre }}</p>
                                    <p class="text-[11px] text-gray-500 truncate mt-0.5 font-medium">{{ Auth::user()->email }}</p>
                                </div>
                                
                                <div class="p-2 space-y-1">
                                    
                                    {{-- VALIDACIÓN PARA ADMIN (rol_id == 1) --}}
                                    @if(Auth::user()->rol_id == 1)
                                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-bold text-primary bg-primary/5 hover:bg-primary/10 rounded-xl transition-all duration-200 group/link">
                                            <span class="material-symbols-outlined text-[20px] text-primary transition-colors">admin_panel_settings</span> 
                                            Panel de Control
                                        </a>
                                        <div class="h-px bg-gray-100 my-1 mx-2"></div>
                                    @endif

                                    <a href="{{ route('perfil') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-600 hover:bg-primary/10 hover:text-agro-dark rounded-xl transition-all duration-200 group/link">
                                        <span class="material-symbols-outlined text-[20px] text-gray-400 group-hover/link:text-primary transition-colors">account_circle</span> 
                                        Mi Perfil
                                    </a>
                                    <a href="{{ route('perfil.pedidos') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-600 hover:bg-primary/10 hover:text-agro-dark rounded-xl transition-all duration-200 group/link">
                                        <span class="material-symbols-outlined text-[20px] text-gray-400 group-hover/link:text-primary transition-colors">inventory_2</span> 
                                        Mis Pedidos
                                    </a>
                                    
                                    <div class="h-px bg-gray-100 my-1 mx-2"></div>
                                    
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-bold text-red-500 hover:bg-red-50 rounded-xl transition-all duration-200 text-left group/btn">
                                            <span class="material-symbols-outlined text-[20px] text-red-400 group-hover/btn:text-red-600 transition-colors">logout</span> 
                                            Cerrar Sesión
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endauth
                
                {{-- 4. ICONO DEL CARRITO: Bug de animación resuelto y diseño mejorado --}}
                <a href="{{ route('carrito.index') }}" class="relative flex items-center justify-center w-11 h-11 text-agro-dark bg-gray-50 hover:bg-primary/10 hover:text-primary rounded-xl transition-all duration-300 group">
                    <span class="material-symbols-outlined text-[24px] group-hover:scale-110 transition-transform">shopping_cart</span>
                    
                    {{-- El contenedor posiciona, el span se anima --}}
                    <div class="absolute -top-1.5 -right-1.5 {{ $cartCount > 0 ? '' : 'hidden' }}" id="cart-badge-container">
                        <span id="cart-count-badge" 
                              class="flex min-w-[20px] h-5 px-1.5 items-center justify-center rounded-full bg-red-500 text-[10px] font-black text-white shadow-sm ring-2 ring-white">
                            {{ $cartCount }}
                        </span>
                    </div>
                </a>
                
                <button type="button" onclick="toggleMobileMenu()" class="lg:hidden flex items-center justify-center w-11 h-11 text-agro-dark bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors z-50">
                    <span class="material-symbols-outlined text-[26px]">menu</span>
                </button>
            </div>
        </div>
        
        <div id="mobile-search" class="hidden lg:hidden pb-4 animate-fade-in-up px-1">
            <form action="{{ route('catalogo') }}" method="GET" class="relative">
                <input type="text" 
                       name="buscar"
                       class="w-full h-11 pl-4 pr-12 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary text-sm shadow-sm"
                       placeholder="Buscar productos...">
                <button type="submit" class="absolute right-0 top-0 h-11 w-12 flex items-center justify-center text-primary font-bold">
                    <span class="material-symbols-outlined">search</span>
                </button>
            </form>
        </div>
    </div>
</header>