{{-- MENU MÓVIL LATERAL --}}
    <div id="mobile-menu-overlay" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true">
        
        {{-- BACKDROP (Fondo Oscuro) --}}
        <div class="fixed inset-0 bg-agro-dark/40 backdrop-blur-sm transition-opacity opacity-0" 
             id="mobile-menu-backdrop"
             onclick="toggleMobileMenu()"></div>

        {{-- PANEL LATERAL --}}
        <div class="fixed inset-y-0 right-0 z-[110] w-full max-w-[85%] sm:max-w-sm bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-out flex flex-col h-full" 
             id="mobile-menu-panel">
            
            {{-- 1. CABECERA CON LOGO ORIGINAL --}}
            <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-white">
                <a href="{{ route('home') }}" class="flex items-center gap-3 flex-shrink-0 outline-none">
                    <div class="flex items-center justify-center size-9 bg-agro-dark rounded-lg text-primary shadow-sm">
                        <span class="material-symbols-outlined text-[24px]">agriculture</span>
                    </div>
                    <div class="flex items-baseline gap-1">
                        <span class="text-agro-dark text-xl font-black tracking-tight leading-none">Corpo</span>
                        <span class="text-agro-accent text-xl font-black tracking-tight leading-none">Agrícola</span>
                    </div>
                </a>
                <button type="button" class="group p-2 -mr-2 text-gray-400 hover:text-agro-dark hover:bg-gray-50 rounded-full transition-all" onclick="toggleMobileMenu()">
                    <span class="material-symbols-outlined text-[26px]">close</span>
                </button>
            </div>

            {{-- CONTENIDO SCROLLABLE --}}
            <div class="flex-1 overflow-y-auto px-5 py-6">
                <div class="space-y-8">
                    
                    {{-- 2. MENÚ DE NAVEGACIÓN --}}
                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 block px-2">Navegación</span>
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route('home') }}" onclick="toggleMobileMenu(); window.scrollTo(0,0);" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-600 hover:bg-primary/5 hover:text-primary font-medium transition-colors group">
                                    <span class="material-symbols-outlined text-[22px] text-gray-400 group-hover:text-primary transition-colors">home</span>
                                    Inicio
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('catalogo') }}" onclick="toggleMobileMenu()" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-600 hover:bg-primary/5 hover:text-primary font-medium transition-colors group">
                                    <span class="material-symbols-outlined text-[22px] text-gray-400 group-hover:text-primary transition-colors">storefront</span>
                                    Catálogo
                                </a>
                            </li>
                            
                            {{-- ENLACE AL CARRITO (MÓVIL) --}}
                            <li>
                                <a href="{{ route('carrito.index') }}" onclick="toggleMobileMenu()" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-gray-600 hover:bg-primary/5 hover:text-primary font-medium transition-colors group">
                                    <div class="flex items-center gap-3">
                                        <span class="material-symbols-outlined text-[22px] text-gray-400 group-hover:text-primary transition-colors">shopping_cart</span>
                                        Mi Carrito
                                    </div>
                                    {{-- Badge del carrito en móvil --}}
                                    @if(isset($cartCount) && $cartCount > 0)
                                        <span class="bg-red-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-sm">{{ $cartCount }}</span>
                                    @endif
                                </a>
                            </li>

                            <li>
                                <a href="#nosotros" onclick="toggleMobileMenu()" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-600 hover:bg-primary/5 hover:text-primary font-medium transition-colors group">
                                    <span class="material-symbols-outlined text-[22px] text-gray-400 group-hover:text-primary transition-colors">groups</span>
                                    Nosotros
                                </a>
                            </li>
                             <li>
                                <a href="#contacto" onclick="toggleMobileMenu()" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-600 hover:bg-primary/5 hover:text-primary font-medium transition-colors group">
                                    <span class="material-symbols-outlined text-[22px] text-gray-400 group-hover:text-primary transition-colors">contact_support</span>
                                    Contacto
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- 3. SECCIÓN MI CUENTA --}}
                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 block px-2">Mi Cuenta</span>
                        
                        @guest
                            <ul class="space-y-1">
                                <li>
                                    <a href="{{ route('login') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-600 hover:bg-primary/5 hover:text-primary font-medium transition-colors group">
                                        <span class="material-symbols-outlined text-[22px] text-gray-400 group-hover:text-primary transition-colors">login</span>
                                        Ingresar
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('register') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-600 hover:bg-primary/5 hover:text-primary font-medium transition-colors group">
                                        <span class="material-symbols-outlined text-[22px] text-gray-400 group-hover:text-primary transition-colors">person_add</span>
                                        Registrarse
                                    </a>
                                </li>
                            </ul>
                        @endguest

                        @auth
                            {{-- Tarjeta Destacada del Usuario Logueado --}}
                            <div class="bg-gray-50 border border-gray-100 rounded-xl p-3 mb-3 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-black text-sm border border-primary/20 shrink-0">
                                    {{ substr(Auth::user()->nombre, 0, 1) }}
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-sm font-bold text-agro-dark truncate">{{ Auth::user()->nombre }}</p>
                                    <p class="text-[10px] text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                </div>
                            </div>

                            <ul class="space-y-1">
                                <li>
                                    <a href="{{ route('perfil') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-600 hover:bg-primary/5 hover:text-primary font-medium transition-colors group">
                                        <span class="material-symbols-outlined text-[22px] text-gray-400 group-hover:text-primary transition-colors">account_circle</span>
                                        Mi Perfil
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('perfil.pedidos') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-600 hover:bg-primary/5 hover:text-primary font-medium transition-colors group">
                                        <span class="material-symbols-outlined text-[22px] text-gray-400 group-hover:text-primary transition-colors">local_shipping</span>
                                        Mis Pedidos
                                    </a>
                                </li>
                                <li class="pt-2">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-500 hover:bg-red-50 font-bold transition-colors text-left group">
                                            <span class="material-symbols-outlined text-[22px] text-red-400 group-hover:text-red-500 transition-colors">logout</span>
                                            Cerrar Sesión
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        @endauth
                    </div>
                </div>
            </div>

            {{-- 4. ÁREA INFERIOR FIJA (Botón Récipe) --}}
            <div class="p-5 border-t border-gray-100 bg-white">
                 <a href="#" class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-4 py-3.5 text-sm font-bold text-agro-dark shadow-md shadow-primary/20 hover:bg-primary/90 hover:shadow-lg transition-all group">
                    <span class="material-symbols-outlined text-[22px] group-hover:-translate-y-0.5 transition-transform">upload_file</span>
                    Subir Récipe Médico
                </a>
            </div>
        </div>
    </div>