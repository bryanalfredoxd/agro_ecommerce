<!DOCTYPE html>
<html class="light" lang="es" style="scroll-behavior: smooth;">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Agropecuaria Venezuela - Soluciones Integrales para el Campo')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @if(file_exists(public_path('css/agro-colors.css')))
        <link href="{{ asset('css/agro-colors.css') }}" rel="stylesheet">
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
    
    <div class="relative flex min-h-screen w-full flex-col overflow-x-hidden">
        
        <div class="sticky top-0 z-40 w-full">
            @include('components.top-bar')
            @include('components.header')
        </div>
        
        <main class="flex-grow w-full">
            @yield('content')
        </main>
        
        <div id="contacto">
            @include('components.footer')
        </div>
    </div>

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
                                <a href="#catalogo" onclick="toggleMobileMenu()" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-primary/10 hover:text-agro-dark font-medium transition-colors">
                                    <span class="material-symbols-outlined text-[22px] text-gray-400">storefront</span>
                                    Catálogo
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
                                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-primary/10 hover:text-agro-dark font-medium transition-colors">
                                        <span class="material-symbols-outlined text-[22px] text-gray-400">account_circle</span>
                                        Mi Perfil
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-primary/10 hover:text-agro-dark font-medium transition-colors">
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