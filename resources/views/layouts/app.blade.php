<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Agropecuaria Venezuela - Soluciones Integrales para el Campo')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @if(file_exists(public_path('css/agro-colors.css')))
        <link href="{{ asset('css/agro-colors.css') }}" rel="stylesheet">
    @endif
    
    <style>
        body {
            font-family: "Work Sans", sans-serif;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        
        /* Animaciones */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
        
        /* Layout Containers - AJUSTADO PARA RESPONSIVE FLUIDO */
        .layout-container {
            max-width: 1440px;
            width: 100%;
            margin: 0 auto;
        }
        
        /* Móvil (Pequeño) */
        @media (max-width: 640px) { 
            .layout-container { padding: 0 1rem; } /* 16px a los lados */
        }
        
        /* Tablet y Móvil Grande */
        @media (min-width: 641px) and (max-width: 1024px) { 
            .layout-container { padding: 0 2rem; } /* 32px a los lados (antes era demasiado ancho) */
        }
        
        /* Laptop (Breakpoint nuevo para evitar que se vea aplastado en pantallas de 13 pulgadas) */
        @media (min-width: 1025px) and (max-width: 1280px) { 
            .layout-container { padding: 0 5rem; } /* 80px a los lados (transición suave) */
        }
        
        /* Desktop Grande */
        @media (min-width: 1281px) { 
            .layout-container { padding: 0 10rem; } /* 160px a los lados (tu valor original) */
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-background-light text-agro-dark font-sans antialiased">
    <div class="relative flex min-h-screen w-full flex-col overflow-x-hidden">
        
        <div class="sticky top-0 z-50 w-full">
            @include('components.top-bar')
            @include('components.header')
        </div>
        
        <main class="flex-grow w-full">
            @yield('content')
        </main>
        
        @include('components.footer')
    </div>
    
    @stack('scripts')
</body>
</html>