<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Corpo Agrícola')</title>

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
<body class="bg-gray-50 text-gray-800 font-sans antialiased overflow-x-hidden">

    {{-- Notificaciones Toast Globales (Opcional, si las usas en el admin) --}}
    @if(session('success') || session('error'))
        <div id="admin-toast" class="fixed bottom-6 right-6 z-[999] flex items-center gap-3 px-5 py-4 rounded-xl shadow-2xl animate-fade-in-up {{ session('success') ? 'bg-green-600 text-white' : 'bg-red-600 text-white' }}">
            <span class="material-symbols-outlined">{{ session('success') ? 'check_circle' : 'error' }}</span>
            <p class="font-bold text-sm">{{ session('success') ?? session('error') }}</p>
        </div>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('admin-toast');
                if(toast) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(20px)';
                    toast.style.transition = 'all 0.5s ease';
                    setTimeout(() => toast.remove(), 500);
                }
            }, 4000);
        </script>
    @endif

    @yield('content')

    @stack('scripts')
</body>
</html>