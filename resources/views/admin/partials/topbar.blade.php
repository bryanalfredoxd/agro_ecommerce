<header class="bg-white/80 backdrop-blur-md border-b border-gray-100 h-20 px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30 shadow-sm">
    <div class="flex items-center gap-4">
        <button onclick="toggleAdminSidebar()" class="lg:hidden text-gray-500 hover:text-agro-dark transition-colors bg-gray-100 hover:bg-gray-200 p-2 rounded-xl focus:outline-none">
            <span class="material-symbols-outlined text-[24px]">menu</span>
        </button>
        <div>
            <h1 class="text-xl font-black text-agro-dark leading-none">Dashboard Principal</h1>
            <p class="text-[11px] font-bold text-gray-400 mt-1 uppercase tracking-wider">{{ date('d M Y, h:i A') }}</p>
        </div>
    </div>

    <div class="flex items-center gap-4">
        {{-- Alertas rápidas --}}
        <button class="relative p-2 text-gray-400 hover:text-primary transition-colors rounded-full hover:bg-gray-50 focus:outline-none">
            <span class="material-symbols-outlined">notifications</span>
            <span class="absolute top-1 right-1 flex h-3 w-3 items-center justify-center rounded-full bg-red-500 ring-2 ring-white"></span>
        </button>
        
        {{-- Perfil (Con desplegable para cerrar sesión) --}}
        <div class="relative group">
            <button class="flex items-center gap-3 pl-4 border-l border-gray-200 focus:outline-none">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-agro-dark leading-none">{{ Auth::user()->nombre ?? 'Administrador' }}</p>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-1">Super Admin</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-agro-dark text-white flex items-center justify-center font-bold shadow-sm group-hover:bg-primary group-hover:text-agro-dark transition-colors">
                    {{ substr(Auth::user()->nombre ?? 'A', 0, 1) }}
                </div>
            </button>
            
            {{-- Mini menú de sesión --}}
            <div class="absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 hidden group-hover:block z-50">
                <form method="POST" action="{{ route('logout') }}" class="p-2">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-sm font-bold text-red-500 hover:bg-red-50 rounded-lg transition-colors text-left">
                        <span class="material-symbols-outlined text-[18px]">logout</span> 
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>