{{-- Backdrop oscuro para móvil --}}
<div id="admin-sidebar-backdrop" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity opacity-0 duration-300" onclick="toggleAdminSidebar()"></div>

{{-- Sidebar --}}
<aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-agro-dark text-white h-screen shadow-2xl lg:shadow-xl lg:sticky lg:top-0 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex-shrink-0">
    
    {{-- Logo Admin y Botón Cerrar (Móvil) --}}
    <div class="h-20 flex items-center justify-between px-6 border-b border-white/10 bg-black/10 flex-shrink-0">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center size-10 bg-primary/20 rounded-xl text-primary border border-primary/30">
                <span class="material-symbols-outlined text-[24px]">admin_panel_settings</span>
            </div>
            <div class="flex flex-col">
                <span class="text-white text-lg font-black tracking-tight leading-none">Corpo<span class="text-primary">Admin</span></span>
                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Centro de Mando</span>
            </div>
        </div>
        {{-- Botón X solo visible en móvil --}}
        <button onclick="toggleAdminSidebar()" class="lg:hidden text-gray-400 hover:text-white transition-colors p-1 bg-white/5 rounded-lg">
            <span class="material-symbols-outlined text-[24px]">close</span>
        </button>
    </div>

    {{-- Menú Sidebar (Acordeón Scrollable) --}}
    <div class="flex-1 overflow-y-auto py-6 px-4 custom-scrollbar">
        <nav class="space-y-2">
            
            {{-- MÓDULO 1: Dashboard --}}
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-primary text-agro-dark font-bold transition-colors shadow-sm">
                <span class="material-symbols-outlined text-[22px]">dashboard</span> Resumen
            </a>
            
            <div class="h-px bg-white/5 my-4 mx-2"></div>

            {{-- MÓDULO 2: Ventas y Pedidos --}}
            <details class="group/menu" open>
                <summary class="flex items-center justify-between px-4 py-2.5 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition-colors cursor-pointer select-none font-bold text-sm">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] text-blue-400">shopping_cart</span>
                        Ventas y Pedidos
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-300 group-open/menu:rotate-180">expand_more</span>
                </summary>
                <div class="mt-1 pl-12 pr-2 space-y-1">
                    <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Listado de Pedidos</a>
                    <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Gestión de Pagos</a>
                    <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Nueva Venta Manual</a>
                    <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Devoluciones</a>
                </div>
            </details>

            {{-- MÓDULO 3: Catálogo y Productos --}}
            <details class="group/menu">
                <summary class="flex items-center justify-between px-4 py-2.5 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition-colors cursor-pointer select-none font-bold text-sm">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] text-green-400">category</span>
                        Catálogo e Inventario
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-300 group-open/menu:rotate-180">expand_more</span>
                </summary>
                <div class="mt-1 pl-12 pr-2 space-y-1">
                    <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Listado de Productos</a>
                    <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Inventario por Lotes</a>
                    <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Categorías</a>
                    <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Marcas</a>
                    <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Histórico de Precios</a>
                </div>
            </details>

            {{-- MÓDULO 4: Clientes y Usuarios --}}
            <details class="group/menu">
                <summary class="flex items-center justify-between px-4 py-2.5 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition-colors cursor-pointer select-none font-bold text-sm">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] text-indigo-400">manage_accounts</span>
                        Usuarios y Roles
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-300 group-open/menu:rotate-180">expand_more</span>
                </summary>
                <div class="mt-1 pl-12 pr-2 space-y-1">
                    <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Listado de Usuarios</a>
                    <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Roles y Permisos</a>
                </div>
            </details>

            {{-- MÓDULO 5: Logística y Delivery --}}
            <details class="group/menu">
                <summary class="flex items-center justify-between px-4 py-2.5 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition-colors cursor-pointer select-none font-bold text-sm">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] text-orange-400">local_shipping</span>
                        Logística y Delivery
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-300 group-open/menu:rotate-180">expand_more</span>
                </summary>
                <div class="mt-1 pl-12 pr-2 space-y-1">
                    <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Zonas de Delivery</a>
                    <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Repartidores</a>
                    <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Seguimiento Entregas</a>
                </div>
            </details>

            {{-- MÓDULO 6: Facturación y Finanzas --}}
            <details class="group/menu">
                <summary class="flex items-center justify-between px-4 py-2.5 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition-colors cursor-pointer select-none font-bold text-sm">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] text-teal-400">point_of_sale</span>
                        Finanzas y Caja
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-300 group-open/menu:rotate-180">expand_more</span>
                </summary>
                <div class="mt-1 pl-12 pr-2 space-y-1">
                    <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Listado de Facturas</a>
                    <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Config. Facturación</a>
                    <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Caja Diaria / POS</a>
                    <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Tasas de Cambio</a>
                </div>
            </details>

            {{-- MÓDULO 7: Recetas Veterinarias --}}
            <details class="group/menu">
                <summary class="flex items-center justify-between px-4 py-2.5 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition-colors cursor-pointer select-none font-bold text-sm">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] text-purple-400">stethoscope</span>
                        Récipes Veterinarios
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-300 group-open/menu:rotate-180">expand_more</span>
                </summary>
                <div class="mt-1 pl-12 pr-2 space-y-1">
                    <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Listado de Recetas</a>
                    <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Revisión Pendientes</a>
                </div>
            </details>

            {{-- MÓDULO 8: Configuración y Utilidades --}}
            <details class="group/menu">
                <summary class="flex items-center justify-between px-4 py-2.5 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition-colors cursor-pointer select-none font-bold text-sm">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] text-gray-400">settings_suggest</span>
                        Sistema y Ajustes
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-300 group-open/menu:rotate-180">expand_more</span>
                </summary>
                <div class="mt-1 pl-12 pr-2 space-y-1">
                    <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Configuración Tienda</a>
                    <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Cuentas Bancarias</a>
                    <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">APIs Externas</a>
                    <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Horarios Físicos</a>
                    <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Logs de Auditoría</a>
                </div>
            </details>

        </nav>
    </div>
    
    {{-- User Footer --}}
    <div class="p-4 border-t border-white/10 bg-black/20 flex-shrink-0">
        <a href="{{ route('home') }}" class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-white/10 text-white hover:bg-white/20 transition-colors text-xs font-bold border border-white/10 shadow-sm group">
            <span class="material-symbols-outlined text-[16px] group-hover:-translate-x-1 transition-transform">storefront</span> 
            Volver a la Tienda
        </a>
    </div>
</aside>