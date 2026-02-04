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
            
            <a href="{{ route('home') }}" class="flex items-center gap-3 flex-shrink-0 group">
                <div class="flex items-center justify-center size-9 sm:size-10 bg-agro-dark rounded-lg text-primary shadow-sm group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[24px] sm:text-[28px]">agriculture</span>
                </div>
                <div class="flex items-baseline gap-1">
                    <span class="text-agro-dark text-xl sm:text-2xl font-black tracking-tight leading-none">Corpo</span>
                    <span class="text-agro-accent text-xl sm:text-2xl font-black tracking-tight leading-none">Agrícola</span>
                </div>
            </a>
            
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
                        class="lg:hidden p-2 text-agro-dark hover:bg-gray-100 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[24px]">search</span>
                </button>

                @guest
                    <a href="{{ route('login') }}" class="hidden sm:flex items-center gap-2 px-3 py-2 text-agro-dark hover:bg-gray-50 rounded-lg transition-colors border border-transparent hover:border-gray-200 group">
                        <span class="material-symbols-outlined text-[22px] text-gray-400 group-hover:text-primary transition-colors">account_circle</span>
                        <div class="flex flex-col items-start leading-none text-xs">
                            <span class="text-gray-500 font-medium mb-0.5">Bienvenido</span>
                            <span class="font-bold group-hover:text-primary transition-colors">Ingresar</span>
                        </div>
                    </a>
                @endguest

                @auth
                    <div class="hidden sm:flex relative group">
                        <button class="flex items-center gap-2 px-3 py-2 text-agro-dark hover:bg-gray-50 rounded-lg transition-colors border border-transparent hover:border-gray-200">
                            <div class="size-8 bg-primary/20 rounded-full flex items-center justify-center text-agro-dark font-bold text-xs border border-primary/30">
                                {{ substr(Auth::user()->nombre, 0, 1) }}
                            </div>
                            <div class="flex flex-col items-start leading-none text-xs">
                                <span class="text-gray-500 font-medium mb-0.5">Hola,</span>
                                <span class="font-bold flex items-center gap-1">
                                    {{ Str::limit(Auth::user()->nombre, 10) }}
                                    <span class="material-symbols-outlined text-[14px]">expand_more</span>
                                </span>
                            </div>
                        </button>

                        <div class="absolute top-full right-0 pt-2 w-56 hidden group-hover:block z-50">
                            <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-2 animate-fade-in-up">
                                <div class="px-3 py-2 border-b border-gray-50 mb-1">
                                    <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->nombre }}</p>
                                    <p class="text-[10px] text-gray-400 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="{{ route('perfil') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-gray-600 hover:bg-primary/10 hover:text-agro-dark rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">account_circle</span> Mi Perfil
                                </a>
                                <a href="{{ route('perfil.pedidos') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-gray-600 hover:bg-primary/10 hover:text-agro-dark rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">inventory_2</span> Mis Pedidos
                                </a>
                                <div class="border-t border-gray-50 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs font-bold text-red-500 hover:bg-red-50 rounded-lg transition-colors text-left">
                                        <span class="material-symbols-outlined text-[18px]">logout</span> Cerrar Sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endauth
                
                <a href="{{ route('carrito.index') }}" class="relative p-2 text-agro-dark hover:text-primary hover:bg-gray-50 rounded-lg transition-colors group">
                    <span class="material-symbols-outlined text-[24px] group-hover:animate-bounce">shopping_cart</span>
                    
                    <span id="cart-count-badge" 
                          class="absolute top-0 right-0 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white transform translate-x-1 -translate-y-1 {{ $cartCount > 0 ? '' : 'hidden' }}">
                        {{ $cartCount }}
                    </span>
                </a>
                
                <button type="button" onclick="toggleMobileMenu()" class="lg:hidden p-2 text-agro-dark hover:bg-gray-100 rounded-lg transition-colors z-50">
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