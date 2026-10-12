{{-- Backdrop oscuro para móvil --}}
<div id="admin-sidebar-backdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-40 hidden lg:hidden transition-opacity opacity-0 duration-300" onclick="toggleAdminSidebar()"></div>

{{-- Sidebar --}}
<aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-[#0b1d38] text-white h-screen shadow-2xl lg:shadow-xl lg:sticky lg:top-0 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex-shrink-0 border-r border-[#1e549e]/30">
    
    {{-- Header Logo Admin --}}
    <div class="h-20 flex items-center justify-between px-6 border-b border-white/10 bg-black/15 flex-shrink-0">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <img src="{{ asset('img/logo.png') }}" alt="Agrosuministros Rodríguez" class="h-9 w-auto object-contain bg-white/10 p-1 rounded-lg">
            <div class="flex flex-col">
                <span class="text-white text-sm font-black tracking-tight leading-tight">Agrosuministros</span>
                <span class="text-[10px] text-[#aad6f3] font-bold uppercase tracking-wider">Panel Admin</span>
            </div>
        </a>
        <button onclick="toggleAdminSidebar()" class="lg:hidden text-slate-400 hover:text-white transition-colors p-1 bg-white/5 rounded-lg">
            <span class="material-symbols-outlined text-[22px]">close</span>
        </button>
    </div>

    {{-- Menú Sidebar (Acordeón) --}}
    <div class="flex-1 overflow-y-auto py-5 px-3.5 custom-scrollbar">
        <nav class="space-y-1.5">
            
            @if(Auth::user()->tienePermiso('ver_dashboard'))
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-[#3492cf] text-white font-bold shadow-md shadow-[#3492cf]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' }} text-sm font-semibold transition-all">
                <span class="material-symbols-outlined text-[20px] text-[#aad6f3]">dashboard</span> 
                <span>Resumen General</span>
            </a>
            @endif

            @if(Auth::user()->esAdmin() || Auth::user()->tienePermiso('ver_estadisticas_analitica') || Auth::user()->tienePermiso('ver_kpis_financieros'))
            <a href="{{ route('admin.estadisticas.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl {{ request()->routeIs('admin.estadisticas.*') ? 'bg-[#3492cf] text-white font-bold shadow-md shadow-[#3492cf]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' }} text-sm font-semibold transition-all group">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[20px] text-[#aad6f3] group-hover:scale-110 transition-transform">query_stats</span> 
                    <span>Analítica y Reportes</span>
                </div>
                <span class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Nuevo</span>
            </a>
            @endif
            <div class="h-px bg-white/10 my-3 mx-2"></div>

            {{-- MÓDULO: Ventas y Pedidos --}}
            @if(Auth::user()->esAdmin() || Auth::user()->tieneAlgunPermiso(['ver_pedidos', 'ver_pedidos_todos', 'ver_pedidos_por_verificar', 'ver_pedidos_despacho', 'ver_pedidos_en_ruta', 'ver_pedidos_completados', 'ver_pedidos_cancelados', 'gestionar_pagos', 'crear_venta_manual', 'ver_cotizaciones', 'gestionar_cotizaciones', 'procesar_devoluciones', 'gestionar_zonas_delivery', 'ver_logistica']))
            <details class="group/menu" {{ request()->routeIs('admin.pedidos.*', 'admin.pagos.*', 'admin.pos.*', 'admin.zonas_delivery.*', 'admin.cotizaciones.*', 'admin.estadisticas.*') ? 'open' : '' }}>
                <summary class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-300 hover:bg-white/5 hover:text-white transition-colors cursor-pointer select-none font-semibold text-sm">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] text-[#3492cf]">shopping_cart</span>
                        <span>Ventas y Pedidos</span>
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-200 group-open/menu:rotate-180 text-slate-400">expand_more</span>
                </summary>
                <div class="mt-1 pl-10 pr-2 space-y-1">
                    @if(Auth::user()->esAdmin() || Auth::user()->tieneAlgunPermiso(['ver_pedidos', 'ver_pedidos_todos', 'ver_pedidos_por_verificar', 'ver_pedidos_despacho', 'ver_pedidos_en_ruta', 'ver_pedidos_completados', 'ver_pedidos_cancelados', 'gestionar_pagos']))
                        @php
                            $pendientesPagoCount = \App\Models\Pago::where('estado', 'revision')->count();
                            $puedeVerificarPagos = Auth::user()->esAdmin() || Auth::user()->tienePermiso('ver_pedidos_por_verificar') || Auth::user()->tienePermiso('gestionar_pagos');
                        @endphp
                        
                        <a href="{{ route('admin.pedidos.index') }}" class="flex items-center justify-between py-1.5 text-xs font-medium {{ request()->routeIs('admin.pedidos.*') ? 'text-[#aad6f3] font-bold' : 'text-slate-300 hover:text-white' }} transition-colors">
                            <span>Gestión de Pedidos</span>
                            @if($pendientesPagoCount > 0 && $puedeVerificarPagos)
                                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black bg-amber-500 text-slate-900 leading-none animate-pulse">{{ $pendientesPagoCount }}</span>
                            @endif
                        </a>
                    @endif
                    @if(Auth::user()->esAdmin() || Auth::user()->tienePermiso('crear_venta_manual'))
                        <a href="{{ route('admin.pos.index') }}" class="block py-1.5 text-xs font-medium {{ request()->routeIs('admin.pos.*') ? 'text-[#aad6f3] font-bold' : 'text-slate-300 hover:text-white' }} transition-colors">Terminal POS (Venta)</a>
                    @endif
                    @if(Auth::user()->esAdmin() || Auth::user()->tienePermiso('ver_cotizaciones') || Auth::user()->tienePermiso('gestionar_cotizaciones'))
                        <a href="{{ route('admin.cotizaciones.index') }}" class="block py-1.5 text-xs font-medium {{ request()->routeIs('admin.cotizaciones.*') ? 'text-[#aad6f3] font-bold' : 'text-slate-300 hover:text-white' }} transition-colors">Cotizaciones / Presupuestos</a>
                    @endif
                    @if(Auth::user()->esAdmin() || Auth::user()->tienePermiso('gestionar_zonas_delivery') || Auth::user()->tienePermiso('ver_logistica'))
                        <a href="{{ route('admin.zonas_delivery.index') }}" class="block py-1.5 text-xs font-medium {{ request()->routeIs('admin.zonas_delivery.*') ? 'text-[#aad6f3] font-bold' : 'text-slate-300 hover:text-white' }} transition-colors">Zonas de Delivery (Mapa)</a>
                    @endif
                    @if(Auth::user()->esAdmin() || Auth::user()->tienePermiso('ver_estadisticas_analitica') || Auth::user()->tienePermiso('ver_kpis_financieros'))
                        <a href="{{ route('admin.estadisticas.index') }}" class="flex items-center justify-between py-1.5 text-xs font-medium {{ request()->routeIs('admin.estadisticas.*') ? 'text-[#aad6f3] font-bold' : 'text-slate-300 hover:text-white' }} transition-colors">
                            <span>Estadísticas y Top Ventas</span>
                            <span class="material-symbols-outlined text-[15px] text-[#aad6f3]">monitoring</span>
                        </a>
                    @endif
                </div>
            </details>
            @endif

            {{-- MÓDULO: Catálogo e Inventario --}}
            @if(Auth::user()->esAdmin() || Auth::user()->tienePermiso('ver_productos') || Auth::user()->tienePermiso('gestionar_inventario_lotes') || Auth::user()->tienePermiso('gestionar_categorias_marcas') || Auth::user()->tienePermiso('ver_historico_precios') || Auth::user()->tienePermiso('gestionar_solicitudes_productos'))
            <details class="group/menu" {{ request()->routeIs('admin.productos.*', 'admin.categorias.*', 'admin.marcas.*', 'admin.historico_precios.*', 'admin.solicitudes_productos.*') ? 'open' : '' }}>
                <summary class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-300 hover:bg-white/5 hover:text-white transition-colors cursor-pointer select-none font-semibold text-sm">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] text-[#3492cf]">category</span>
                        <span>Catálogo e Inventario</span>
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-200 group-open/menu:rotate-180 text-slate-400">expand_more</span>
                </summary>
                <div class="mt-1 pl-10 pr-2 space-y-1">
                    @if(Auth::user()->esAdmin() || Auth::user()->tienePermiso('ver_productos'))
                        <a href="{{ route('admin.productos.index') }}" class="block py-1.5 text-xs font-medium {{ request()->routeIs('admin.productos.*') ? 'text-[#aad6f3] font-bold' : 'text-slate-300 hover:text-white' }} transition-colors">Catálogo de Productos</a>
                    @endif
                    @if(Auth::user()->esAdmin() || Auth::user()->tienePermiso('gestionar_categorias_marcas'))
                        <a href="{{ route('admin.categorias.index') }}" class="block py-1.5 text-xs font-medium {{ request()->routeIs('admin.categorias.*') ? 'text-[#aad6f3] font-bold' : 'text-slate-300 hover:text-white' }} transition-colors">Categorías</a>
                    @endif
                    @if(Auth::user()->esAdmin() || Auth::user()->tienePermiso('gestionar_categorias_marcas'))
                        <a href="{{ route('admin.marcas.index') }}" class="block py-1.5 text-xs font-medium {{ request()->routeIs('admin.marcas.*') ? 'text-[#aad6f3] font-bold' : 'text-slate-300 hover:text-white' }} transition-colors">Marcas</a>
                    @endif
                    @if(Auth::user()->esAdmin() || Auth::user()->tienePermiso('ver_historico_precios'))
                        <a href="{{ route('admin.historico_precios.index') }}" class="block py-1.5 text-xs font-medium {{ request()->routeIs('admin.historico_precios.*') ? 'text-[#aad6f3] font-bold' : 'text-slate-300 hover:text-white' }} transition-colors">Histórico de Precios</a>
                    @endif
                    @if(Auth::user()->esAdmin() || Auth::user()->tienePermiso('ver_productos') || Auth::user()->tienePermiso('gestionar_solicitudes_productos'))
                        <a href="{{ route('admin.solicitudes_productos.index') }}" class="flex items-center justify-between py-1.5 text-xs font-medium {{ request()->routeIs('admin.solicitudes_productos.*') ? 'text-[#aad6f3] font-bold' : 'text-slate-300 hover:text-white' }} transition-colors">
                            <span>Solicitudes de Insumos</span>
                            @php
                                $solicitudesPendientesCount = \App\Models\SolicitudProducto::where('estado', 'pendiente')->count();
                            @endphp
                            @if($solicitudesPendientesCount > 0)
                                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black bg-blue-500 text-white leading-none">{{ $solicitudesPendientesCount }}</span>
                            @endif
                        </a>
                    @endif
                </div>
            </details>
            @endif

            {{-- MÓDULO: Finanzas y Facturación --}}
            @if(Auth::user()->esAdmin() || Auth::user()->tienePermiso('ver_facturas') || Auth::user()->tienePermiso('ver_devoluciones') || Auth::user()->tienePermiso('procesar_devoluciones') || Auth::user()->tienePermiso('configurar_facturacion') || Auth::user()->tienePermiso('gestionar_caja_diaria') || Auth::user()->tienePermiso('gestionar_tasas_cambio'))
            <details class="group/menu" {{ request()->routeIs('admin.facturas.*', 'admin.devoluciones.*', 'admin.facturacion_config.*', 'admin.caja_diaria.*', 'admin.tasas-cambio.*') ? 'open' : '' }}>
                <summary class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-300 hover:bg-white/5 hover:text-white transition-colors cursor-pointer select-none font-semibold text-sm">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] text-[#3492cf]">receipt_long</span>
                        <span>Finanzas y Facturas</span>
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-200 group-open/menu:rotate-180 text-slate-400">expand_more</span>
                </summary>
                <div class="mt-1 pl-10 pr-2 space-y-1">
                    @if(Auth::user()->esAdmin() || Auth::user()->tienePermiso('ver_facturas'))
                        <a href="{{ route('admin.facturas.index') }}" class="block py-1.5 text-xs font-medium {{ request()->routeIs('admin.facturas.*') ? 'text-[#aad6f3] font-bold' : 'text-slate-300 hover:text-white' }} transition-colors">Facturas Emitidas</a>
                    @endif
                    @if(Auth::user()->esAdmin() || Auth::user()->tienePermiso('ver_devoluciones') || Auth::user()->tienePermiso('procesar_devoluciones'))
                        <a href="{{ route('admin.devoluciones.index') }}" class="block py-1.5 text-xs font-medium {{ request()->routeIs('admin.devoluciones.*') ? 'text-[#aad6f3] font-bold' : 'text-slate-300 hover:text-white' }} transition-colors">Devoluciones y Reembolsos</a>
                    @endif
                    @if(Auth::user()->esAdmin() || Auth::user()->tienePermiso('configurar_facturacion'))
                        <a href="{{ route('admin.facturacion_config.index') }}" class="block py-1.5 text-xs font-medium {{ request()->routeIs('admin.facturacion_config.*') ? 'text-[#aad6f3] font-bold' : 'text-slate-300 hover:text-white' }} transition-colors">Series y Correlativos</a>
                    @endif
                    @if(Auth::user()->esAdmin() || Auth::user()->tienePermiso('gestionar_caja_diaria'))
                        <a href="{{ route('admin.caja_diaria.index') }}" class="block py-1.5 text-xs font-medium {{ request()->routeIs('admin.caja_diaria.*') ? 'text-[#aad6f3] font-bold' : 'text-slate-300 hover:text-white' }} transition-colors">Arqueos de Caja</a>
                    @endif
                    @if(Auth::user()->esAdmin() || Auth::user()->tienePermiso('gestionar_tasas_cambio'))
                        <a href="{{ route('admin.tasas-cambio.index') }}" class="block py-1.5 text-xs font-medium {{ request()->routeIs('admin.tasas-cambio.*') ? 'text-[#aad6f3] font-bold' : 'text-slate-300 hover:text-white' }} transition-colors">Tasas de Cambio (BCV)</a>
                    @endif
                </div>
            </details>
            @endif

            {{-- MÓDULO: Usuarios y Roles --}}
            @if(Auth::user()->tienePermiso('ver_usuarios') || Auth::user()->tienePermiso('gestionar_roles_permisos'))
            <details class="group/menu" {{ request()->routeIs('admin.usuarios.*', 'admin.roles.*') ? 'open' : '' }}>
                <summary class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-300 hover:bg-white/5 hover:text-white transition-colors cursor-pointer select-none font-semibold text-sm">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] text-[#3492cf]">manage_accounts</span>
                        <span>Usuarios y Roles</span>
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-200 group-open/menu:rotate-180 text-slate-400">expand_more</span>
                </summary>
                <div class="mt-1 pl-10 pr-2 space-y-1">
                    @if(Auth::user()->tienePermiso('ver_usuarios'))
                        <a href="{{ route('admin.usuarios.index') }}" class="block py-1.5 text-xs font-medium {{ request()->routeIs('admin.usuarios.*') ? 'text-[#aad6f3] font-bold' : 'text-slate-300 hover:text-white' }} transition-colors">Lista de Usuarios</a>
                    @endif
                    @if(Auth::user()->tienePermiso('gestionar_roles_permisos'))
                        <a href="{{ route('admin.roles.index') }}" class="block py-1.5 text-xs font-medium {{ request()->routeIs('admin.roles.*') ? 'text-[#aad6f3] font-bold' : 'text-slate-300 hover:text-white' }} transition-colors">Roles y Permisos</a>
                    @endif
                </div>
            </details>
            @endif

            {{-- MÓDULO: Auditoría y Trazabilidad (Logs) --}}
            @if(Auth::user()->tienePermiso('ver_logs_auditoria'))
            <a href="{{ route('admin.logs.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl {{ request()->routeIs('admin.logs.*') ? 'bg-[#1e549e] text-white font-black shadow-md' : 'text-slate-300 hover:bg-white/5 hover:text-white font-semibold' }} transition-all text-sm group">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.logs.*') ? 'text-white' : 'text-[#3492cf]' }} group-hover:scale-110 transition-transform">manage_search</span>
                    <span>Logs & Auditoría</span>
                </div>
                <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Activo</span>
            </a>
            @endif

            {{-- MÓDULO: Configuración del Sistema --}}
            @if(Auth::user()->tienePermiso('configurar_tienda') || Auth::user()->tienePermiso('gestionar_cuentas_banco') || Auth::user()->tienePermiso('gestionar_horarios'))
            <details class="group/menu" {{ request()->routeIs('admin.configuracion.*', 'admin.cuentas_bancarias.*', 'admin.horarios.*') ? 'open' : '' }}>
                <summary class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-300 hover:bg-white/5 hover:text-white transition-colors cursor-pointer select-none font-semibold text-sm">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] text-[#3492cf]">settings</span>
                        <span>Configuración</span>
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-200 group-open/menu:rotate-180 text-slate-400">expand_more</span>
                </summary>
                <div class="mt-1 pl-10 pr-2 space-y-1">
                    @if(Auth::user()->tienePermiso('configurar_tienda'))
                        <a href="{{ route('admin.configuracion.index') }}" class="block py-1.5 text-xs font-medium {{ request()->routeIs('admin.configuracion.*') ? 'text-[#aad6f3] font-bold' : 'text-slate-300 hover:text-white' }} transition-colors">Datos de la Empresa</a>
                    @endif
                    @if(Auth::user()->tienePermiso('gestionar_cuentas_banco'))
                        <a href="{{ route('admin.cuentas_bancarias.index') }}" class="block py-1.5 text-xs font-medium {{ request()->routeIs('admin.cuentas_bancarias.*') ? 'text-[#aad6f3] font-bold' : 'text-slate-300 hover:text-white' }} transition-colors">Cuentas Bancarias</a>
                    @endif
                    @if(Auth::user()->tienePermiso('gestionar_horarios'))
                        <a href="{{ route('admin.horarios.index') }}" class="block py-1.5 text-xs font-medium {{ request()->routeIs('admin.horarios.*') ? 'text-[#aad6f3] font-bold' : 'text-slate-300 hover:text-white' }} transition-colors">Horarios de Atención</a>
                    @endif
                </div>
            </details>
            @endif

        </nav>
    </div>
    
    {{-- Footer Sidebar: Tienda y Usuario --}}
    <div class="p-3.5 border-t border-white/10 bg-black/20 flex-shrink-0">
        <a href="{{ route('home') }}" class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-white/10 text-white hover:bg-[#3492cf] transition-all text-xs font-bold border border-white/10 shadow-xs group">
            <span class="material-symbols-outlined text-[18px] text-[#aad6f3] group-hover:text-white">storefront</span> 
            <span>Ir a la Tienda Pública</span>
        </a>
    </div>
</aside>