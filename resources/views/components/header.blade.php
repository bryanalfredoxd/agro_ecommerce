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
                <form action="#" class="relative w-full group">
                    <input type="text" 
                           class="w-full h-11 pl-4 pr-12 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all text-sm placeholder:text-gray-400 group-hover:bg-white"
                           placeholder="¿Qué estás buscando para tu campo hoy?">
                    <button type="button" class="absolute right-0 top-0 h-11 w-12 flex items-center justify-center text-agro-dark hover:text-primary transition-colors">
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
                
                <a href="#" class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-gray-600 hover:bg-primary/10 hover:text-agro-dark rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[18px]">account_circle</span> Mi Perfil
                </a>
                <a href="#" class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-gray-600 hover:bg-primary/10 hover:text-agro-dark rounded-lg transition-colors">
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
                
                <a href="#" class="relative p-2 text-agro-dark hover:text-primary hover:bg-gray-50 rounded-lg transition-colors group">
                    <span class="material-symbols-outlined text-[24px] group-hover:animate-bounce">shopping_cart</span>
                    <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white">3</span>
                </a>
                
                <button type="button" onclick="toggleMobileMenu()" class="lg:hidden p-2 text-agro-dark hover:bg-gray-100 rounded-lg transition-colors z-50">
                    <span class="material-symbols-outlined text-[26px]">menu</span>
                </button>
            </div>
        </div>
        
        <div id="mobile-search" class="hidden lg:hidden pb-4 animate-fade-in-up px-1">
            <form class="relative">
                <input type="text" 
                       class="w-full h-11 pl-4 pr-12 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary text-sm shadow-sm"
                       placeholder="Buscar productos...">
                <button type="submit" class="absolute right-0 top-0 h-11 w-12 flex items-center justify-center text-primary font-bold">
                    <span class="material-symbols-outlined">search</span>
                </button>
            </form>
        </div>

    <!-- Quitar por el momento ya que no creo que sea necesario
         Si se logra hacer que quede bien, pues se puede reactivar

        <div class="hidden lg:flex items-center justify-between py-1 border-t border-gray-100">
            <nav class="flex items-center gap-1">
                @foreach([
                    ['name' => 'Veterinaria', 'icon' => 'vaccines'],
                    ['name' => 'Semillas', 'icon' => 'grass'],
                    ['name' => 'Fertilizantes', 'icon' => 'compost'],
                    ['name' => 'Nutrición', 'icon' => 'nutrition'],
                    ['name' => 'Maquinaria', 'icon' => 'precision_manufacturing']
                ] as $item)
                <a href="#" class="flex items-center gap-2 px-4 py-3 text-sm font-medium text-agro-dark hover:text-primary border-b-2 border-transparent hover:border-primary transition-all group">
                    <span class="material-symbols-outlined text-[20px] text-gray-400 group-hover:text-primary transition-colors">{{ $item['icon'] }}</span>
                    <span>{{ $item['name'] }}</span>
                </a>
                @endforeach
            </nav>
            <a href="#" class="flex items-center gap-2 px-4 py-2 bg-agro-accent/10 text-agro-accent rounded-lg hover:bg-agro-accent hover:text-white transition-all font-medium text-sm">
                <span class="material-symbols-outlined text-[20px]">upload_file</span>
                <span>Subir Recipe</span>
            </a>
        </div>
    </div>
    
    <div class="lg:hidden border-t border-gray-100 py-3 bg-gray-50/50">
        <div class="layout-container overflow-x-auto scrollbar-hide">
            <div class="flex gap-4 min-w-max px-1 pb-1">
                @foreach([
                    ['icon' => 'vaccines', 'name' => 'Veterinaria'],
                    ['icon' => 'grass', 'name' => 'Semillas'],
                    ['icon' => 'compost', 'name' => 'Abonos'],
                    ['icon' => 'nutrition', 'name' => 'Nutrición'],
                    ['icon' => 'hardware', 'name' => 'Equipos'],
                    ['icon' => 'pets', 'name' => 'Mascotas']
                ] as $cat)
                <a href="#" class="flex flex-col items-center gap-1.5 min-w-[72px] group">
                    <div class="size-14 bg-white rounded-full border border-gray-200 flex items-center justify-center shadow-sm group-active:scale-95 transition-all group-hover:border-primary group-hover:text-primary text-gray-600">
                        <span class="material-symbols-outlined text-[24px]">{{ $cat['icon'] }}</span>
                    </div>
                    <span class="text-[11px] font-medium text-gray-700 text-center w-full truncate px-0.5">{{ $cat['name'] }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    -->
</header>