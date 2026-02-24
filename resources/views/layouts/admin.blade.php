<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Administrativo') - Agro E-commerce</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/fontawesome/all.min.css') }}">
    <style>
        :root {
            --agro-green: #22c55e;
            --agro-dark: #1f2937;
            --agro-bg: #f9fafb;
        }
        body {
            font-family: 'Work Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--agro-bg);
            overflow-x: hidden;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <nav id="sidebar" class="w-64 min-h-screen bg-gray-900 text-white transition-all duration-300 ease-in-out fixed lg:static lg:translate-x-0 -translate-x-full z-40">
            <div class="flex justify-between items-center p-5 bg-gray-800 border-b border-gray-700">
                <h4 class="text-xl font-bold flex items-center">
                    <i class="fas fa-leaf text-green-500 mr-2"></i> AgroAdmin
                </h4>
                <button class="lg:hidden text-white hover:text-gray-300" id="closeSidebar">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <ul class="py-4">
                <li class="mb-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-5 py-3 text-gray-300 hover:bg-gray-800 hover:text-white border-l-4 border-transparent hover:border-green-500 transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white border-green-500' : '' }}">
                        <i class="fas fa-tachometer-alt mr-3 w-5 text-center"></i> Dashboard
                    </a>
                </li>
                <li class="mb-1">
                    <a href="{{ route('admin.pedidos.index') }}" class="flex items-center px-5 py-3 text-gray-300 hover:bg-gray-800 hover:text-white border-l-4 border-transparent hover:border-green-500 transition-all {{ request()->routeIs('admin.pedidos.*') ? 'bg-gray-800 text-white border-green-500' : '' }}">
                        <i class="fas fa-shopping-cart mr-3 w-5 text-center"></i> Pedidos
                    </a>
                </li>
                <li class="mb-1">
                    <a href="{{ route('admin.inventario.index') }}" class="flex items-center px-5 py-3 text-gray-300 hover:bg-gray-800 hover:text-white border-l-4 border-transparent hover:border-green-500 transition-all {{ request()->routeIs('admin.inventario.*') ? 'bg-gray-800 text-white border-green-500' : '' }}">
                        <i class="fas fa-box-open mr-3 w-5 text-center"></i> Inventario
                    </a>
                </li>
                <li class="mb-1">
                    <a href="{{ route('admin.recetas.index') }}" class="flex items-center px-5 py-3 text-gray-300 hover:bg-gray-800 hover:text-white border-l-4 border-transparent hover:border-green-500 transition-all">
                        <i class="fas fa-file-prescription mr-3 w-5 text-center"></i> Recetas Vet.
                    </a>
                </li>
                <li class="mb-1">
                    <a href="{{ route('admin.clientes.index') }}" class="flex items-center px-5 py-3 text-gray-300 hover:bg-gray-800 hover:text-white border-l-4 border-transparent hover:border-green-500 transition-all {{ request()->routeIs('admin.clientes.*') ? 'bg-gray-800 text-white border-green-500' : '' }}">
                        <i class="fas fa-users mr-3 w-5 text-center"></i> Clientes
                    </a>
                </li>
                <li class="mb-1">
                    <a href="#" class="flex items-center px-5 py-3 text-gray-300 hover:bg-gray-800 hover:text-white border-l-4 border-transparent hover:border-green-500 transition-all">
                        <i class="fas fa-cash-register mr-3 w-5 text-center"></i> Cajas y Pagos
                    </a>
                </li>
                <li class="mb-1">
                    <a href="#" class="flex items-center px-5 py-3 text-gray-300 hover:bg-gray-800 hover:text-white border-l-4 border-transparent hover:border-green-500 transition-all">
                        <i class="fas fa-cog mr-3 w-5 text-center"></i> Configuración
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Overlay for mobile -->
        <div id="content-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen">
            <!-- Topbar -->
            <header class="bg-white shadow-sm px-4 py-3 lg:px-6 sticky top-0 z-20">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <button id="sidebarCollapse" class="lg:hidden mr-3 p-2 bg-gray-100 rounded-md hover:bg-gray-200 transition">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h5 class="text-lg font-bold text-gray-800">@yield('page_header', 'Dashboard')</h5>
                    </div>
                    <div class="flex items-center">
                        <span class="hidden sm:inline mr-3 text-gray-600">
                            <i class="fas fa-user-circle mr-1"></i> Administrador
                        </span>
                        <a href="#" class="p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition" title="Cerrar Sesión">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 p-4 lg:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebarCollapse');
            const closeBtn = document.getElementById('closeSidebar');
            const overlay = document.getElementById('content-overlay');

            function toggleSidebar() {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }

            if(toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
            if(closeBtn) closeBtn.addEventListener('click', toggleSidebar);
            if(overlay) overlay.addEventListener('click', toggleSidebar);
        });
    </script>
    @stack('scripts')
</body>
</html>