<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Agropecuaria Venezuela - Soluciones Integrales para el Campo')</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    
    <!-- Nuestros colores personalizados -->
    <link href="{{ asset('css/agro-colors.css') }}" rel="stylesheet">
    
    <!-- Tailwind Config -->
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#13ec13",
                        "agro-dark": "#1B4332",
                        "agro-gold": "#D4A373",
                        "agro-accent": "#BC6C25",
                        "background-light": "#F8F9FA",
                        "background-dark": "#102210",
                    },
                    fontFamily: {
                        "display": ["Work Sans", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    
    <style>
        body {
            font-family: "Work Sans", sans-serif;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        /* Animaciones */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
        /* Mejorar responsive */
        .layout-container {
            max-width: 1440px;
            margin-left: auto;
            margin-right: auto;
        }
        @media (max-width: 640px) {
            .layout-container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
        @media (min-width: 641px) and (max-width: 1024px) {
            .layout-container {
                padding-left: 2.5rem;
                padding-right: 2.5rem;
            }
        }
        @media (min-width: 1025px) {
            .layout-container {
                padding-left: 10rem;
                padding-right: 10rem;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-background-light text-agro-dark">
    <div class="relative flex min-h-screen w-full flex-col overflow-x-hidden">
        @include('components.top-bar')
        @include('components.header')
        
        <main class="flex-grow">
            @yield('content')
        </main>
        
        @include('components.footer')
    </div>
    
    @stack('scripts')
</body>
</html>