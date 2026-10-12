<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Administrativo - ' . ($configTienda->nombre_empresa ?? \App\Models\ConfiguracionTienda::nombre()))</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    {{-- Google Fonts: Work Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    {{-- Material Symbols --}}
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    {{-- Tailwind CSS y Estilos base --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased overflow-x-hidden selection:bg-[#3492cf] selection:text-white">

    {{-- Notificaciones Toast Globales --}}
    @if(session('success') || session('error'))
        <div id="admin-toast" class="fixed bottom-6 right-6 z-[999] flex items-center gap-3 px-5 py-4 rounded-xl shadow-2xl animate-fade-in-up border {{ session('success') ? 'bg-[#1e549e] text-white border-[#3492cf]/40' : 'bg-rose-600 text-white border-rose-700' }}">
            <span class="material-symbols-outlined text-[22px]">{{ session('success') ? 'check_circle' : 'error' }}</span>
            <p class="font-bold text-sm">{{ session('success') ?? session('error') }}</p>
        </div>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('admin-toast');
                if(toast) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(16px)';
                    toast.style.transition = 'all 0.4s ease';
                    setTimeout(() => toast.remove(), 400);
                }
            }, 4000);
        </script>
    @endif

    @yield('content')

    @stack('scripts')

    {{-- Script Global para el Sidebar del Administrador --}}
    <script>
        function toggleAdminSidebar() {
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('admin-sidebar-backdrop');
            if (!sidebar || !backdrop) return;
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                setTimeout(() => backdrop.classList.remove('opacity-0'), 10);
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('opacity-0');
                setTimeout(() => backdrop.classList.add('hidden'), 300);
            }
        }
    </script>
</body>
</html>