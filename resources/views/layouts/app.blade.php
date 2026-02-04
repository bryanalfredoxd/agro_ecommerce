<!DOCTYPE html>
<html class="light" lang="es" style="scroll-behavior: smooth;">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @if(session('success_register'))
        <meta name="success-message" content="{{ session('success_register') }}">
    @endif
    
    <title>@yield('title', 'Agropecuaria Venezuela - Soluciones Integrales para el Campo')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @if(file_exists(public_path('css/agro-colors.css')))
        <link href="{{ asset('css/agro-colors.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/splash.css') }}">
    @endif
    
    <style>
        body { font-family: "Work Sans", sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        
        /* Animaciones */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out; }
        
        /* Contenedor Responsivo Fluido */
        .layout-container { max-width: 1440px; width: 100%; margin: 0 auto; }
        @media (max-width: 640px) { .layout-container { padding: 0 1rem; } }
        @media (min-width: 641px) and (max-width: 1024px) { .layout-container { padding: 0 2rem; } }
        @media (min-width: 1025px) and (max-width: 1280px) { .layout-container { padding: 0 5rem; } }
        @media (min-width: 1281px) { .layout-container { padding: 0 10rem; } }
    </style>
    
    @stack('styles')
</head>
<body class="bg-background-light text-agro-dark font-sans antialiased selection:bg-primary selection:text-agro-dark">
    
    {{-- Lógica para contar items del carrito --}}
    @php
        $cartCount = 0;
        if(Auth::check()) {
            $cartCount = (int) \App\Models\Carrito::where('usuario_id', Auth::id())->sum('cantidad');
        }
    @endphp

    <x-splash-screen />
    
    <div class="relative flex min-h-screen w-full flex-col overflow-x-hidden">
        
        <div class="sticky top-0 z-40 w-full">
            @include('components.top-bar')
            
            {{-- HEADER INTEGRADO AQUÍ --}}
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
                                <input type="text" name="buscar" 
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
                            
                            {{-- CARRITO CON BADGE DINÁMICO --}}
                            <a href="{{ route('carrito.index') }}" class="relative p-2 text-agro-dark hover:text-primary hover:bg-gray-50 rounded-lg transition-colors group">
                                <span class="material-symbols-outlined text-[24px] group-hover:animate-bounce">shopping_cart</span>
                                <span id="cart-count-badge" class="absolute top-0 right-0 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white transform translate-x-1 -translate-y-1 {{ $cartCount > 0 ? '' : 'hidden' }}">
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
                            <input type="text" name="buscar"
                                   class="w-full h-11 pl-4 pr-12 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary text-sm shadow-sm"
                                   placeholder="Buscar productos...">
                            <button type="submit" class="absolute right-0 top-0 h-11 w-12 flex items-center justify-center text-primary font-bold">
                                <span class="material-symbols-outlined">search</span>
                            </button>
                        </form>
                    </div>
                </div>
            </header>
        </div>
        
        <main class="flex-grow w-full">
            @yield('content')
        </main>
        
        <div id="contacto">
            @include('components.footer')
        </div>
    </div>

    {{-- MENU MÓVIL LATERAL --}}
    <div id="mobile-menu-overlay" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true">
        
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" 
             id="mobile-menu-backdrop"
             onclick="toggleMobileMenu()"></div>

        <div class="fixed inset-y-0 right-0 z-[110] w-full max-w-xs bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-out flex flex-col h-full" 
             id="mobile-menu-panel">
            
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center size-11 bg-agro-dark rounded-xl text-primary shadow-sm">
                        <span class="material-symbols-outlined text-[26px]">agriculture</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="block text-agro-dark font-black text-xl leading-none tracking-tight">Corpo</span>
                        <span class="block text-agro-accent font-black text-xl leading-none tracking-tight -mt-0.5">Agrícola</span>
                    </div>
                </div>
                <button type="button" class="group p-2 -mr-2 text-gray-400 hover:text-red-500 transition-colors rounded-full hover:bg-gray-50" onclick="toggleMobileMenu()">
                    <span class="material-symbols-outlined text-[28px] group-hover:scale-90 transition-transform">close</span>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-6 py-4">
                <div class="space-y-6">
                    
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 block">Navegación</span>
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route('home') }}" onclick="toggleMobileMenu(); window.scrollTo(0,0);" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-primary/10 hover:text-agro-dark font-medium transition-colors">
                                    <span class="material-symbols-outlined text-[22px] text-gray-400">home</span>
                                    Inicio
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('catalogo') }}" onclick="toggleMobileMenu()" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-primary/10 hover:text-agro-dark font-medium transition-colors">
                                    <span class="material-symbols-outlined text-[22px] text-gray-400">storefront</span>
                                    Catálogo
                                </a>
                            </li>
                            
                            {{-- ENLACE AL CARRITO (MÓVIL) --}}
                            <li>
                                <a href="{{ route('carrito.index') }}" onclick="toggleMobileMenu()" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-primary/10 hover:text-agro-dark font-medium transition-colors">
                                    <span class="material-symbols-outlined text-[22px] text-gray-400">shopping_cart</span>
                                    Mi Carrito
                                </a>
                            </li>

                            <li>
                                <a href="#nosotros" onclick="toggleMobileMenu()" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-primary/10 hover:text-agro-dark font-medium transition-colors">
                                    <span class="material-symbols-outlined text-[22px] text-gray-400">groups</span>
                                    Nosotros
                                </a>
                            </li>
                             <li>
                                <a href="#contacto" onclick="toggleMobileMenu()" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-primary/10 hover:text-agro-dark font-medium transition-colors">
                                    <span class="material-symbols-outlined text-[22px] text-gray-400">contact_support</span>
                                    Contacto
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 block">Mi Cuenta</span>
                        <ul class="space-y-1">
                            @guest
                                <li>
                                    <a href="{{ route('login') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-primary/10 hover:text-agro-dark font-medium transition-colors">
                                        <span class="material-symbols-outlined text-[22px] text-gray-400">login</span>
                                        Ingresar
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('register') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-primary/10 hover:text-agro-dark font-medium transition-colors">
                                        <span class="material-symbols-outlined text-[22px] text-gray-400">person_add</span>
                                        Registrarse
                                    </a>
                                </li>
                            @endguest

                            @auth
                                <li class="px-3 pb-2 mb-2 border-b border-gray-100">
                                    <div class="flex items-center gap-3">
                                        <div class="size-8 rounded-full bg-agro-dark/10 flex items-center justify-center text-agro-dark font-bold text-xs">
                                            {{ substr(Auth::user()->nombre, 0, 1) }}
                                        </div>
                                        <div class="overflow-hidden">
                                            <p class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->nombre }}</p>
                                            <p class="text-[10px] text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <a href="{{ route('perfil') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-primary/10 hover:text-agro-dark font-medium transition-colors">
                                        <span class="material-symbols-outlined text-[22px] text-gray-400">account_circle</span>
                                        Mi Perfil
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('perfil.pedidos') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-primary/10 hover:text-agro-dark font-medium transition-colors">
                                        <span class="material-symbols-outlined text-[22px] text-gray-400">local_shipping</span>
                                        Mis Pedidos
                                    </a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-red-600 hover:bg-red-50 font-medium transition-colors text-left">
                                            <span class="material-symbols-outlined text-[22px] text-red-400">logout</span>
                                            Cerrar Sesión
                                        </button>
                                    </form>
                                </li>
                            @endauth
                        </ul>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-gray-100 bg-gray-50/50">
                 <a href="#" class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-4 py-3.5 text-sm font-bold text-agro-dark shadow-lg shadow-primary/20 hover:bg-primary/90 hover:-translate-y-0.5 transition-all">
                    <span class="material-symbols-outlined text-[20px]">upload_file</span>
                    Subir Récipe
                </a>
            </div>
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>