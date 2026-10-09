{{-- MENU MÓVIL LATERAL --}}
<div id="mobile-menu-overlay" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true">
    
    {{-- BACKDROP (Fondo Oscuro) --}}
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity opacity-0" 
         id="mobile-menu-backdrop"
         onclick="toggleMobileMenu()"></div>

    {{-- PANEL LATERAL --}}
    <div class="fixed inset-y-0 right-0 z-[110] w-full max-w-[85%] sm:max-w-sm bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-out flex flex-col h-full" 
         id="mobile-menu-panel">
        
        {{-- CABECERA CON LOGO OFICIAL --}}
        <div class="flex items-center justify-between p-4 border-b border-slate-100 bg-white">
            <a href="{{ route('home') }}" class="flex items-center gap-2 flex-shrink-0 outline-hidden">
                <img src="{{ asset('img/logo.png') }}" alt="Agrosuministros Rodríguez" class="h-9 w-auto object-contain">
            </a>
            <button type="button" class="p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-full transition-colors" onclick="toggleMobileMenu()">
                <span class="material-symbols-outlined text-[24px]">close</span>
            </button>
        </div>

        {{-- CONTENIDO SCROLLABLE --}}
        <div class="flex-1 overflow-y-auto px-5 py-6">
            <div class="space-y-6">
                
                {{-- NAVEGACIÓN --}}
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 block px-2">Navegación</span>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('home') }}" onclick="toggleMobileMenu(); window.scrollTo(0,0);" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-700 hover:bg-[#e5f2fb] hover:text-[#1e549e] font-semibold text-sm transition-colors">
                                <span class="material-symbols-outlined text-[20px] text-slate-400">home</span>
                                Inicio
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('catalogo') }}" onclick="toggleMobileMenu()" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-700 hover:bg-[#e5f2fb] hover:text-[#1e549e] font-semibold text-sm transition-colors">
                                <span class="material-symbols-outlined text-[20px] text-slate-400">storefront</span>
                                Catálogo Completo
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('carrito.index') }}" onclick="toggleMobileMenu()" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-700 hover:bg-[#e5f2fb] hover:text-[#1e549e] font-semibold text-sm transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-[20px] text-slate-400">shopping_cart</span>
                                    Mi Carrito
                                </div>
                                @php
                                    $cartCount = Auth::check() ? (int)\App\Models\Carrito::where('usuario_id', Auth::id())->sum('cantidad') : 0;
                                @endphp
                                @if($cartCount > 0)
                                    <span class="bg-[#1e549e] text-white text-[10px] font-black px-2 py-0.5 rounded-full">{{ $cartCount }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- SECCIÓN MI CUENTA --}}
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 block px-2">Mi Cuenta</span>
                    
                    @guest
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route('login') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-700 hover:bg-[#e5f2fb] hover:text-[#1e549e] font-semibold text-sm transition-colors">
                                    <span class="material-symbols-outlined text-[20px] text-slate-400">login</span>
                                    Iniciar Sesión
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('register') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-700 hover:bg-[#e5f2fb] hover:text-[#1e549e] font-semibold text-sm transition-colors">
                                    <span class="material-symbols-outlined text-[20px] text-slate-400">person_add</span>
                                    Registrarse
                                </a>
                            </li>
                        </ul>
                    @endguest

                    @auth
                        <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3 mb-3 flex items-center gap-3">
                            <div class="size-9 rounded-full bg-[#1e549e] flex items-center justify-center text-white font-black text-sm shrink-0">
                                {{ strtoupper(substr(Auth::user()->nombre, 0, 1)) }}
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-sm font-bold text-slate-800 truncate">{{ Auth::user()->nombre }}</p>
                                <p class="text-[11px] text-slate-500 truncate">{{ Auth::user()->email }}</p>
                            </div>
                        </div>

                        <ul class="space-y-1">
                            @if(Auth::user()->puedeAccederPanelAdmin())
                                <li>
                                    <a href="{{ Auth::user()->ruta_panel }}" onclick="toggleMobileMenu()" class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-[#e5f2fb] text-[#1e549e] font-bold text-sm transition-colors">
                                        <span class="material-symbols-outlined text-[20px]">{{ Auth::user()->icono_panel }}</span>
                                        {{ Auth::user()->nombre_panel }}
                                    </a>
                                </li>
                            @endif

                            <li>
                                <a href="{{ route('perfil') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-700 hover:bg-[#e5f2fb] hover:text-[#1e549e] font-semibold text-sm transition-colors">
                                    <span class="material-symbols-outlined text-[20px] text-slate-400">person</span>
                                    Mi Perfil
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('perfil.pedidos') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-700 hover:bg-[#e5f2fb] hover:text-[#1e549e] font-semibold text-sm transition-colors">
                                    <span class="material-symbols-outlined text-[20px] text-slate-400">inventory_2</span>
                                    Mis Pedidos
                                </a>
                            </li>
                            <li class="pt-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-rose-600 hover:bg-rose-50 font-bold text-sm transition-colors text-left">
                                        <span class="material-symbols-outlined text-[20px] text-rose-400">logout</span>
                                        Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    @endauth
                </div>
            </div>
        </div>

        {{-- BOTÓN INFERIOR CATÁLOGO --}}
        <div class="p-4 border-t border-slate-100 bg-white">
            <a href="{{ route('catalogo') }}" class="btn-primary w-full shadow-sm">
                <span class="material-symbols-outlined text-[20px]">storefront</span>
                Explorar Catálogo
            </a>
        </div>
    </div>
</div>