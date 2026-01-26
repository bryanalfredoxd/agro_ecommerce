<!-- Main Header -->
<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-100 shadow-sm">
    <div class="layout-container">
        <!-- Top Row: Logo, Search, Actions -->
        <div class="flex items-center justify-between py-4">
            <!-- Logo -->
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center size-10 bg-agro-dark rounded-lg text-primary">
                    <span class="material-symbols-outlined text-[28px]">agriculture</span>
                </div>
                <div>
                    <h1 class="text-agro-dark text-lg sm:text-xl font-bold leading-tight tracking-tight">
                        Agropecuaria<br class="hidden sm:block"/><span class="text-agro-accent">Venezuela</span>
                    </h1>
                </div>
            </div>
            
            <!-- Desktop Search - Centered -->
            <div class="hidden lg:flex flex-1 max-w-[500px] mx-8">
                <div class="relative flex items-center w-full h-10 rounded-lg focus-within:ring-2 focus-within:ring-agro-dark/20 bg-gray-50 overflow-hidden border border-transparent transition-all">
                    <input class="w-full bg-transparent border-none focus:ring-0 text-agro-dark placeholder:text-agro-dark/40 h-full pl-4 pr-12 text-sm" 
                           placeholder="Buscar productos, marcas o principios activos..." type="text"/>
                    <button class="absolute right-0 flex items-center justify-center h-full px-4 bg-agro-dark hover:bg-agro-dark/90 text-white transition-colors">
                        <span class="material-symbols-outlined text-[20px]">search</span>
                    </button>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex items-center gap-3">
                
                
                <!-- Account -->
                <button class="hidden sm:flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 text-agro-dark transition-colors group">
                    <span class="material-symbols-outlined group-hover:text-agro-accent">person</span>
                    <div class="flex flex-col items-start leading-none">
                        <span class="text-[10px] uppercase text-agro-dark/60 font-bold">Mi Cuenta</span>
                        <span class="text-sm font-bold">Ingresar</span>
                    </div>
                </button>
                
                <!-- Mobile Account Icon -->
                <button class="sm:hidden flex items-center justify-center size-10 rounded-lg hover:bg-gray-100 text-agro-dark transition-colors">
                    <span class="material-symbols-outlined">person</span>
                </button>
                
                <!-- Cart -->
                <button class="relative flex items-center justify-center size-10 rounded-lg bg-primary hover:bg-primary/90 text-agro-dark transition-colors">
                    <span class="material-symbols-outlined">shopping_cart</span>
                    <span class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[10px] font-bold h-5 w-5 flex items-center justify-center rounded-full border-2 border-white">3</span>
                </button>
            </div>
        </div>
        
        <!-- Mobile Search Bar (Hidden by default) -->
        <div class="lg:hidden mb-3 px-1">
            <div class="relative flex items-center w-full h-11 rounded-lg focus-within:ring-2 focus-within:ring-agro-dark/20 bg-gray-50 overflow-hidden border border-transparent transition-all">
                <input class="w-full bg-transparent border-none focus:ring-0 text-agro-dark placeholder:text-agro-dark/40 h-full pl-4 pr-12 text-sm" 
                       placeholder="Buscar productos, marcas o principios activos..." type="text"/>
                <button class="absolute right-0 flex items-center justify-center h-full px-4 bg-agro-dark hover:bg-agro-dark/90 text-white transition-colors">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                </button>
            </div>
        </div>
        
        <!-- Secondary Navigation (Desktop only) -->
        <div class="hidden lg:flex items-center justify-between py-3 border-t border-gray-100">
            <!-- Categories -->
            <nav class="flex items-center gap-6 text-sm font-medium text-agro-dark">
                <a class="flex items-center gap-1.5 hover:text-agro-accent transition-colors px-2 py-1 rounded-md hover:bg-gray-50" href="#">
                    <span class="material-symbols-outlined text-[18px]">vaccines</span>
                    <span>Veterinaria</span>
                </a>
                <a class="flex items-center gap-1.5 hover:text-agro-accent transition-colors px-2 py-1 rounded-md hover:bg-gray-50" href="#">
                    <span class="material-symbols-outlined text-[18px]">grass</span>
                    <span>Semillas</span>
                </a>
                <a class="flex items-center gap-1.5 hover:text-agro-accent transition-colors px-2 py-1 rounded-md hover:bg-gray-50" href="#">
                    <span class="material-symbols-outlined text-[18px]">compost</span>
                    <span>Fertilizantes</span>
                </a>
                <a class="flex items-center gap-1.5 hover:text-agro-accent transition-colors px-2 py-1 rounded-md hover:bg-gray-50" href="#">
                    <span class="material-symbols-outlined text-[18px]">nutrition</span>
                    <span>Nutrición Animal</span>
                </a>
                <a class="flex items-center gap-1.5 hover:text-agro-accent transition-colors px-2 py-1 rounded-md hover:bg-gray-50" href="#">
                    <span class="material-symbols-outlined text-[18px]">precision_manufacturing</span>
                    <span>Maquinaria</span>
                </a>
            </nav>
            
            <!-- Special Action -->
            <a class="flex items-center gap-2 text-agro-accent hover:text-agro-dark font-semibold text-sm transition-colors group" href="#">
                <span class="material-symbols-outlined text-[20px] group-hover:scale-110 transition-transform">upload_file</span>
                <span>Subir Recipe Veterinario</span>
            </a>
        </div>
        
        <!-- Mobile Categories Menu -->
        <div class="lg:hidden border-t border-gray-100 pt-3">
            <div class="flex overflow-x-auto gap-4 pb-2 scrollbar-hide">
                <a class="flex flex-col items-center gap-1 min-w-[70px] px-2 py-2 rounded-lg hover:bg-gray-50 transition-colors" href="#">
                    <span class="material-symbols-outlined text-[22px] text-agro-dark">vaccines</span>
                    <span class="text-xs font-medium text-agro-dark">Veterinaria</span>
                </a>
                <a class="flex flex-col items-center gap-1 min-w-[70px] px-2 py-2 rounded-lg hover:bg-gray-50 transition-colors" href="#">
                    <span class="material-symbols-outlined text-[22px] text-agro-dark">grass</span>
                    <span class="text-xs font-medium text-agro-dark">Semillas</span>
                </a>
                <a class="flex flex-col items-center gap-1 min-w-[70px] px-2 py-2 rounded-lg hover:bg-gray-50 transition-colors" href="#">
                    <span class="material-symbols-outlined text-[22px] text-agro-dark">compost</span>
                    <span class="text-xs font-medium text-agro-dark">Fertilizantes</span>
                </a>
                <a class="flex flex-col items-center gap-1 min-w-[70px] px-2 py-2 rounded-lg hover:bg-gray-50 transition-colors" href="#">
                    <span class="material-symbols-outlined text-[22px] text-agro-dark">nutrition</span>
                    <span class="text-xs font-medium text-agro-dark">Nutrición</span>
                </a>
                <a class="flex flex-col items-center gap-1 min-w-[70px] px-2 py-2 rounded-lg hover:bg-gray-50 transition-colors" href="#">
                    <span class="material-symbols-outlined text-[22px] text-agro-dark">hardware</span>
                    <span class="text-xs font-medium text-agro-dark">Ferretería</span>
                </a>
                <a class="flex flex-col items-center gap-1 min-w-[70px] px-2 py-2 rounded-lg hover:bg-gray-50 transition-colors" href="#">
                    <span class="material-symbols-outlined text-[22px] text-agro-accent">upload_file</span>
                    <span class="text-xs font-medium text-agro-accent">Recipe</span>
                </a>
            </div>
        </div>
    </div>
</header>