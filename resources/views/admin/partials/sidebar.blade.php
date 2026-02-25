{{-- Backdrop oscuro para móvil --}}
<div id="admin-sidebar-backdrop" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity opacity-0 duration-300" onclick="toggleAdminSidebar()"></div>

{{-- Sidebar --}}
<aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-agro-dark text-white h-screen shadow-2xl lg:shadow-xl lg:sticky lg:top-0 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex-shrink-0">
    
    {{-- Logo Admin --}}
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
        <button onclick="toggleAdminSidebar()" class="lg:hidden text-gray-400 hover:text-white transition-colors p-1 bg-white/5 rounded-lg">
            <span class="material-symbols-outlined text-[24px]">close</span>
        </button>
    </div>

    {{-- Menú Sidebar (Acordeón) --}}
    <div class="flex-1 overflow-y-auto py-6 px-4 custom-scrollbar">
        <nav class="space-y-2">
            
            @if(Auth::user()->tienePermiso('ver_dashboard'))
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-primary text-agro-dark font-bold transition-colors shadow-sm">
                <span class="material-symbols-outlined text-[22px]">dashboard</span> Resumen
            </a>
            <div class="h-px bg-white/5 my-4 mx-2"></div>
            @endif

            {{-- MÓDULO 2: Ventas y Pedidos --}}
            @if(Auth::user()->tienePermiso('ver_pedidos') || Auth::user()->tienePermiso('gestionar_pagos') || Auth::user()->tienePermiso('crear_venta_manual') || Auth::user()->tienePermiso('procesar_devoluciones'))
            <details class="group/menu" open>
                <summary class="flex items-center justify-between px-4 py-2.5 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition-colors cursor-pointer select-none font-bold text-sm">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] text-blue-400">shopping_cart</span>
                        Ventas y Pedidos
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-300 group-open/menu:rotate-180">expand_more</span>
                </summary>
                <div class="mt-1 pl-12 pr-2 space-y-1">
                    @if(Auth::user()->tienePermiso('ver_pedidos'))
                        <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Listado de Pedidos</a>
                    @endif
                    @if(Auth::user()->tienePermiso('gestionar_pagos'))
                        <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Gestión de Pagos</a>
                    @endif
                    @if(Auth::user()->tienePermiso('crear_venta_manual'))
                        <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Nueva Venta Manual</a>
                    @endif
                    @if(Auth::user()->tienePermiso('procesar_devoluciones'))
                        <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Devoluciones</a>
                    @endif
                </div>
            </details>
            @endif

            {{-- MÓDULO 3: Catálogo y Productos --}}
            @if(Auth::user()->tienePermiso('ver_productos') || Auth::user()->tienePermiso('gestionar_inventario_lotes') || Auth::user()->tienePermiso('gestionar_categorias_marcas') || Auth::user()->tienePermiso('ver_historico_precios'))
            <details class="group/menu">
                <summary class="flex items-center justify-between px-4 py-2.5 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition-colors cursor-pointer select-none font-bold text-sm">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] text-green-400">category</span>
                        Catálogo e Inventario
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-300 group-open/menu:rotate-180">expand_more</span>
                </summary>
                <div class="mt-1 pl-12 pr-2 space-y-1">
                    @if(Auth::user()->tienePermiso('ver_productos'))
                        <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Listado de Productos</a>
                    @endif
                    @if(Auth::user()->tienePermiso('gestionar_inventario_lotes'))
                        <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Inventario por Lotes</a>
                    @endif
                    @if(Auth::user()->tienePermiso('gestionar_categorias_marcas'))
                        <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Categorías y Marcas</a>
                    @endif
                    @if(Auth::user()->tienePermiso('ver_historico_precios'))
                        <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Histórico de Precios</a>
                    @endif
                </div>
            </details>
            @endif

            {{-- MÓDULO 4: Clientes y Usuarios --}}
            @if(Auth::user()->tienePermiso('ver_usuarios') || Auth::user()->tienePermiso('gestionar_roles_permisos'))
            <details class="group/menu">
                <summary class="flex items-center justify-between px-4 py-2.5 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition-colors cursor-pointer select-none font-bold text-sm">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] text-indigo-400">manage_accounts</span>
                        Usuarios y Roles
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-300 group-open/menu:rotate-180">expand_more</span>
                </summary>
                <div class="mt-1 pl-12 pr-2 space-y-1">
                    @if(Auth::user()->tienePermiso('ver_usuarios'))
                        <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Listado de Usuarios</a>
                    @endif
                    @if(Auth::user()->tienePermiso('gestionar_roles_permisos'))
                        <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Roles y Permisos</a>
                    @endif
                </div>
            </details>
            @endif

            {{-- MÓDULO 5: Logística y Delivery --}}
            @if(Auth::user()->tienePermiso('ver_seguimiento_rutas') || Auth::user()->tienePermiso('gestionar_zonas_delivery') || Auth::user()->tienePermiso('gestionar_repartidores'))
            <details class="group/menu">
                <summary class="flex items-center justify-between px-4 py-2.5 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition-colors cursor-pointer select-none font-bold text-sm">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] text-orange-400">local_shipping</span>
                        Logística y Delivery
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-300 group-open/menu:rotate-180">expand_more</span>
                </summary>
                <div class="mt-1 pl-12 pr-2 space-y-1">
                    @if(Auth::user()->tienePermiso('gestionar_zonas_delivery'))
                        <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Zonas de Delivery</a>
                    @endif
                    @if(Auth::user()->tienePermiso('gestionar_repartidores'))
                        <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Repartidores</a>
                    @endif
                    @if(Auth::user()->tienePermiso('ver_seguimiento_rutas'))
                        <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Seguimiento Entregas</a>
                    @endif
                </div>
            </details>
            @endif

            {{-- MÓDULO 6: Facturación y Finanzas --}}
            @if(Auth::user()->tienePermiso('ver_facturas') || Auth::user()->tienePermiso('configurar_facturacion') || Auth::user()->tienePermiso('gestionar_caja_diaria') || Auth::user()->tienePermiso('gestionar_tasas_cambio'))
            <details class="group/menu">
                <summary class="flex items-center justify-between px-4 py-2.5 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition-colors cursor-pointer select-none font-bold text-sm">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] text-teal-400">point_of_sale</span>
                        Finanzas y Caja
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-300 group-open/menu:rotate-180">expand_more</span>
                </summary>
                <div class="mt-1 pl-12 pr-2 space-y-1">
                    @if(Auth::user()->tienePermiso('ver_facturas'))
                        <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Listado de Facturas</a>
                    @endif
                    @if(Auth::user()->tienePermiso('configurar_facturacion'))
                        <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Config. Facturación</a>
                    @endif
                    @if(Auth::user()->tienePermiso('gestionar_caja_diaria'))
                        <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Caja Diaria / POS</a>
                    @endif
                    @if(Auth::user()->tienePermiso('gestionar_tasas_cambio'))
                        <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Tasas de Cambio</a>
                    @endif
                </div>
            </details>
            @endif

            {{-- MÓDULO 7: Recetas Veterinarias --}}
            @if(Auth::user()->tienePermiso('ver_recetas') || Auth::user()->tienePermiso('auditar_recetas'))
            <details class="group/menu">
                <summary class="flex items-center justify-between px-4 py-2.5 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition-colors cursor-pointer select-none font-bold text-sm">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] text-purple-400">stethoscope</span>
                        Récipes Veterinarios
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-300 group-open/menu:rotate-180">expand_more</span>
                </summary>
                <div class="mt-1 pl-12 pr-2 space-y-1">
                    @if(Auth::user()->tienePermiso('ver_recetas'))
                        <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Listado de Recetas</a>
                    @endif
                    @if(Auth::user()->tienePermiso('auditar_recetas'))
                        <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Revisión Pendientes</a>
                    @endif
                </div>
            </details>
            @endif

            {{-- MÓDULO 8: Configuración y Utilidades --}}
            @if(Auth::user()->tienePermiso('configurar_tienda') || Auth::user()->tienePermiso('gestionar_cuentas_banco') || Auth::user()->tienePermiso('configurar_apis') || Auth::user()->tienePermiso('gestionar_horarios') || Auth::user()->tienePermiso('ver_logs_auditoria'))
            <details class="group/menu">
                <summary class="flex items-center justify-between px-4 py-2.5 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition-colors cursor-pointer select-none font-bold text-sm">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] text-gray-400">settings_suggest</span>
                        Sistema y Ajustes
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-300 group-open/menu:rotate-180">expand_more</span>
                </summary>
                <div class="mt-1 pl-12 pr-2 space-y-1">
                    @if(Auth::user()->tienePermiso('configurar_tienda'))
                        <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Configuración Tienda</a>
                    @endif
                    @if(Auth::user()->tienePermiso('gestionar_cuentas_banco'))
                        <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Cuentas Bancarias</a>
                    @endif
                    @if(Auth::user()->tienePermiso('configurar_apis'))
                        <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">APIs Externas</a>
                    @endif
                    @if(Auth::user()->tienePermiso('gestionar_horarios'))
                        <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Horarios Físicos</a>
                    @endif
                    @if(Auth::user()->tienePermiso('ver_logs_auditoria'))
                        <a href="#" class="block py-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">Logs de Auditoría</a>
                    @endif
                </div>
            </details>
            @endif

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