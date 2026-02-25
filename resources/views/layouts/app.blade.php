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

    <x-splash-screen />
    
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

    @include('components.header-movile')
    
    @stack('scripts')
</body>
</html>