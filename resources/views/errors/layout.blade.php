<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Error') - {{ config('app.name', 'Agrosuministros Rodríguez') }}</title>
    
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Work Sans', sans-serif; }
        h1, h2, h3, h4, .font-heading { font-family: 'Outfit', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .material-symbols-outlined.fill { font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        
        @keyframes pulseSoft {
            0%, 100% { transform: scale(1); opacity: 0.15; }
            50% { transform: scale(1.08); opacity: 0.25; }
        }
        .animate-pulse-soft { animation: pulseSoft 6s ease-in-out infinite; }
    </style>
</head>
<body class="h-full bg-slate-950 text-slate-100 flex flex-col justify-between selection:bg-emerald-500 selection:text-white relative overflow-x-hidden antialiased">

    {{-- Fondos Ambientales Difuminados (Glow Orbs) --}}
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-emerald-600/20 blur-3xl animate-pulse-soft"></div>
        <div class="absolute top-1/3 -right-32 w-96 h-96 rounded-full bg-sky-600/20 blur-3xl animate-pulse-soft" style="animation-delay: 2s;"></div>
        <div class="absolute -bottom-32 left-1/4 w-96 h-96 rounded-full bg-indigo-600/15 blur-3xl animate-pulse-soft" style="animation-delay: 4s;"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(15,23,42,0.6),rgba(2,6,23,0.95))]"></div>
    </div>

    {{-- Encabezado Minimalista con Logo --}}
    <header class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex items-center justify-between">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-3 group focus:outline-none focus:ring-2 focus:ring-emerald-500 rounded-2xl p-1 transition-all">
            <div class="size-11 rounded-2xl bg-gradient-to-tr from-emerald-600 to-sky-500 p-0.5 shadow-lg shadow-emerald-500/20 group-hover:scale-105 transition-transform">
                <div class="w-full h-full bg-slate-900 rounded-[14px] flex items-center justify-center">
                    <span class="material-symbols-outlined text-emerald-400 text-2xl group-hover:rotate-12 transition-transform">agriculture</span>
                </div>
            </div>
            <div class="flex flex-col">
                <span class="font-heading font-black text-lg tracking-tight text-white leading-none">
                    Agrosuministros <span class="text-emerald-400">Rodríguez</span>
                </span>
                <span class="text-[10px] uppercase font-bold tracking-widest text-slate-400 mt-1">Plataforma Digital</span>
            </div>
        </a>

        {{-- Estado de Conexión / Botón Rápido --}}
        <div class="flex items-center gap-2">
            @auth
                <a href="{{ Auth::user()->puedeAccederPanelAdmin() ? route('admin.dashboard') : route('perfil') }}" class="hidden sm:inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-slate-900/80 hover:bg-slate-800 text-xs font-bold text-slate-300 hover:text-white border border-slate-800 transition-all shadow-sm">
                    <span class="material-symbols-outlined text-[16px] text-emerald-400">{{ Auth::user()->icono_panel ?? 'account_circle' }}</span>
                    <span>{{ Auth::user()->nombre }}</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-slate-900/80 hover:bg-slate-800 text-xs font-bold text-slate-300 hover:text-white border border-slate-800 transition-all shadow-sm">
                    <span class="material-symbols-outlined text-[16px]">login</span>
                    <span>Iniciar Sesión</span>
                </a>
            @endauth
        </div>
    </header>

    {{-- Contenido Principal Centralizado --}}
    <main class="relative z-10 flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-8 sm:py-16">
        <div class="w-full max-w-xl mx-auto text-center">
            
            {{-- Tarjeta Principal Glassmorphic --}}
            <div class="relative bg-slate-900/80 backdrop-blur-xl border border-slate-800/80 rounded-3xl p-6 sm:p-10 shadow-2xl shadow-black/60 overflow-hidden">
                
                {{-- Brillo superior --}}
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-slate-600/40 to-transparent"></div>

                {{-- Ícono e Ilustración --}}
                <div class="mb-6 flex justify-center">
                    @yield('illustration')
                </div>

                {{-- Código de Error Badge --}}
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-black tracking-widest uppercase mb-4 border @yield('badge_style', 'bg-slate-800/80 text-slate-300 border-slate-700')">
                    <span class="size-2 rounded-full @yield('badge_dot', 'bg-slate-400') animate-ping"></span>
                    <span>Código @yield('code', 'Error')</span>
                </div>

                {{-- Título --}}
                <h1 class="font-heading text-2xl sm:text-3xl font-black text-white mb-3 tracking-tight">
                    @yield('title')
                </h1>

                {{-- Descripción Detallada --}}
                <p class="text-sm sm:text-base text-slate-300 leading-relaxed font-normal mb-6">
                    @yield('message')
                </p>

                {{-- Nota de Ayuda Adicional (Opcional) --}}
                @hasSection('details')
                <div class="bg-slate-950/60 rounded-2xl p-4 border border-slate-800/60 text-xs text-slate-400 text-left mb-6 space-y-1.5 font-medium">
                    @yield('details')
                </div>
                @endif

                {{-- Botones de Acción --}}
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                    @yield('actions')
                </div>

            </div>

        </div>
    </main>

    {{-- Pie de Página --}}
    <footer class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center text-xs text-slate-400 border-t border-slate-900/60 flex flex-col sm:flex-row items-center justify-between gap-3">
        <p>&copy; {{ date('Y') }} Agrosuministros Rodríguez, C.A. Todos los derechos reservados.</p>
        <div class="flex items-center gap-4 font-bold text-slate-400">
            <a href="{{ url('/') }}" class="hover:text-emerald-400 transition-colors">Inicio</a>
            <span>&bull;</span>
            <a href="{{ route('catalogo') }}" class="hover:text-emerald-400 transition-colors">Catálogo</a>
            <span>&bull;</span>
            <a href="https://wa.me/584120000000" target="_blank" class="hover:text-emerald-400 transition-colors flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">support_agent</span> Soporte
            </a>
        </div>
    </footer>

</body>
</html>
