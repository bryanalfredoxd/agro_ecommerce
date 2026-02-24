<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Administrativo') - Agro E-commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --agro-green: #28a745;
            --agro-dark: #212529;
            --agro-bg: #f8f9fa;
        }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: var(--agro-bg); 
            overflow-x: hidden; 
        }
        
        /* Sidebar Styles */
        #sidebar { 
            min-width: 260px; max-width: 260px; min-height: 100vh; 
            background-color: var(--agro-dark); color: #fff; 
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative; z-index: 1040;
        }
        #sidebar .sidebar-header { padding: 20px; background: #1a1e21; border-bottom: 1px solid #343a40; }
        #sidebar ul.components { padding: 15px 0; }
        #sidebar ul li a { 
            padding: 12px 20px; font-size: 1.05em; display: block; color: #adb5bd; 
            text-decoration: none; transition: 0.2s; border-left: 4px solid transparent;
        }
        #sidebar ul li a:hover, #sidebar ul li.active > a { 
            color: #fff; background: rgba(255,255,255,0.05); border-left-color: var(--agro-green); 
        }
        #sidebar ul li a i { margin-right: 12px; width: 20px; text-align: center; }
        
        /* Content Styles */
        #content { width: 100%; min-height: 100vh; transition: all 0.3s; }
        .topbar { background-color: #fff; box-shadow: 0 2px 10px rgba(0,0,0,.05); padding: 15px 25px; margin-bottom: 25px; }
        
        /* Utility Classes */
        .hover-elevate { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .hover-elevate:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
        
        /* Responsive CSS */
        @media (max-width: 768px) {
            #sidebar { margin-left: -260px; position: fixed; height: 100%; }
            #sidebar.active { margin-left: 0; box-shadow: 5px 0 15px rgba(0,0,0,0.5); }
            #content-overlay {
                display: none; position: fixed; width: 100vw; height: 100vh;
                background: rgba(0,0,0,0.4); z-index: 1030; top: 0; left: 0;
            }
            #content-overlay.active { display: block; }
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <nav id="sidebar">
            <div class="sidebar-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold"><i class="fas fa-leaf text-success me-2"></i> AgroAdmin</h4>
                <button class="btn btn-sm btn-outline-light d-md-none border-0" id="closeSidebar"><i class="fas fa-times fa-lg"></i></button>
            </div>

            <ul class="list-unstyled components">
                <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                </li>
                <li class="{{ request()->routeIs('admin.pedidos.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.pedidos.index') }}"><i class="fas fa-shopping-cart"></i> Pedidos</a>
                </li>
                <li><a href="#"><i class="fas fa-box-open"></i> Inventario</a></li>
                <li><a href="#"><i class="fas fa-file-prescription"></i> Recetas Vet.</a></li>
                <li><a href="#"><i class="fas fa-users"></i> Clientes</a></li>
                <li><a href="#"><i class="fas fa-cash-register"></i> Cajas y Pagos</a></li>
                <li><a href="#"><i class="fas fa-cog"></i> Configuración</a></li>
            </ul>
        </nav>

        <div id="content-overlay"></div>

        <div id="content" class="flex-grow-1 w-100 overflow-hidden">
            <div class="topbar d-flex justify-content-between align-items-center sticky-top">
                <div class="d-flex align-items-center">
                    <button type="button" id="sidebarCollapse" class="btn btn-light border shadow-sm me-3 d-md-none">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h5 class="m-0 text-dark fw-bold">@yield('page_header', 'Dashboard')</h5>
                </div>
                <div>
                    <span class="me-3 d-none d-sm-inline"><i class="fas fa-user-circle text-muted"></i> Administrador</span>
                    <a href="#" class="btn btn-sm btn-outline-danger" title="Cerrar Sesión"><i class="fas fa-sign-out-alt"></i></a>
                </div>
            </div>

            <div class="container-fluid px-3 px-md-4 pb-4">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebarCollapse');
            const closeBtn = document.getElementById('closeSidebar');
            const overlay = document.getElementById('content-overlay');

            function toggleSidebar() {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            }

            if(toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
            if(closeBtn) closeBtn.addEventListener('click', toggleSidebar);
            if(overlay) overlay.addEventListener('click', toggleSidebar);
        });
    </script>
    @stack('scripts')
</body>
</html>