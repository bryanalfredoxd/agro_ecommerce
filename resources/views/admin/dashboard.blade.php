@extends('layouts.admin') {{-- AHORA USA EL NUEVO LAYOUT --}}

@section('title', 'Panel de Control - Corpo Agrícola')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    {{-- ========================================== --}}
    {{-- SIDEBAR LATERAL (Fijo en la pantalla)      --}}
    {{-- ========================================== --}}
    <aside class="hidden lg:flex flex-col w-72 bg-agro-dark text-white h-screen sticky top-0 shadow-xl z-40 flex-shrink-0">
        {{-- Logo Admin --}}
        <div class="h-20 flex items-center gap-3 px-6 border-b border-white/10 bg-black/10 flex-shrink-0">
            <div class="flex items-center justify-center size-10 bg-primary/20 rounded-xl text-primary border border-primary/30">
                <span class="material-symbols-outlined text-[24px]">admin_panel_settings</span>
            </div>
            <div class="flex flex-col">
                <span class="text-white text-lg font-black tracking-tight leading-none">Corpo<span class="text-primary">Admin</span></span>
                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Centro de Mando</span>
            </div>
        </div>

        {{-- Menú Sidebar (Scrollable internamente si hay muchas opciones) --}}
        <div class="flex-1 overflow-y-auto py-6 px-4 custom-scrollbar">
            <nav class="space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-primary text-agro-dark font-bold transition-colors">
                    <span class="material-symbols-outlined">dashboard</span> Resumen
                </a>
                
                <div class="pt-4 pb-1">
                    <p class="px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Ventas</p>
                </div>
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition-colors group">
                    <span class="material-symbols-outlined text-[20px] group-hover:text-primary transition-colors">receipt_long</span> Pedidos Web
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition-colors group">
                    <span class="material-symbols-outlined text-[20px] group-hover:text-primary transition-colors">point_of_sale</span> Punto de Venta
                </a>

                <div class="pt-4 pb-1">
                    <p class="px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Catálogo</p>
                </div>
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition-colors group">
                    <span class="material-symbols-outlined text-[20px] group-hover:text-primary transition-colors">inventory_2</span> Productos e Inventario
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition-colors group">
                    <span class="material-symbols-outlined text-[20px] group-hover:text-primary transition-colors">medical_services</span> Recetas Veterinarias
                </a>

                <div class="pt-4 pb-1">
                    <p class="px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Sistema</p>
                </div>
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition-colors group">
                    <span class="material-symbols-outlined text-[20px] group-hover:text-primary transition-colors">group</span> Usuarios y Roles
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition-colors group">
                    <span class="material-symbols-outlined text-[20px] group-hover:text-primary transition-colors">settings</span> Configuración General
                </a>
            </nav>
        </div>
        
        {{-- User Footer --}}
        <div class="p-4 border-t border-white/10 bg-black/20 flex-shrink-0">
            <a href="{{ route('home') }}" class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-white/10 text-white hover:bg-white/20 transition-colors text-xs font-bold border border-white/10 shadow-sm">
                <span class="material-symbols-outlined text-[16px]">storefront</span> Volver a la Tienda
            </a>
        </div>
    </aside>

    {{-- ========================================== --}}
    {{-- CONTENIDO PRINCIPAL                        --}}
    {{-- ========================================== --}}
    {{-- Eliminamos h-screen y overflow-y-auto de aquí para usar el scroll del navegador --}}
    <main class="flex-1 min-w-0 flex flex-col">
        
        {{-- Topbar Admin (Fijo en la parte superior) --}}
        <header class="bg-white/80 backdrop-blur-md border-b border-gray-100 h-20 px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                <button class="lg:hidden text-gray-500 hover:text-agro-dark transition-colors">
                    <span class="material-symbols-outlined text-[28px]">menu</span>
                </button>
                <div>
                    <h1 class="text-xl font-black text-agro-dark leading-none">Dashboard Principal</h1>
                    <p class="text-[11px] font-bold text-gray-400 mt-1 uppercase tracking-wider">{{ date('d M Y, h:i A') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                {{-- Alertas rápidas --}}
                <button class="relative p-2 text-gray-400 hover:text-primary transition-colors rounded-full hover:bg-gray-50 focus:outline-none">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute top-1 right-1 flex h-3 w-3 items-center justify-center rounded-full bg-red-500 ring-2 ring-white"></span>
                </button>
                
                {{-- Perfil (Con desplegable para cerrar sesión) --}}
                <div class="relative group">
                    <button class="flex items-center gap-3 pl-4 border-l border-gray-200 focus:outline-none">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-agro-dark leading-none">{{ Auth::user()->nombre ?? 'Administrador' }}</p>
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-1">Super Admin</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-agro-dark text-white flex items-center justify-center font-bold shadow-sm group-hover:bg-primary group-hover:text-agro-dark transition-colors">
                            {{ substr(Auth::user()->nombre ?? 'A', 0, 1) }}
                        </div>
                    </button>
                    
                    {{-- Mini menú de sesión --}}
                    <div class="absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 hidden group-hover:block z-50">
                        <form method="POST" action="{{ route('logout') }}" class="p-2">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-sm font-bold text-red-500 hover:bg-red-50 rounded-lg transition-colors text-left">
                                <span class="material-symbols-outlined text-[18px]">logout</span> 
                                Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Área de Contenido (Aquí usa el scroll natural de la ventana) --}}
        <div class="p-4 sm:p-8 animate-fade-in-up pb-20">
            
            {{-- 1. INDICADORES (KPIs) SUPERIORES --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6 mb-8 lg:mb-10">
                
                {{-- KPI: Ventas del Día --}}
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-green-50 rounded-full group-hover:scale-[1.8] transition-transform duration-500 ease-out z-0"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Ventas de Hoy</span>
                            <div class="w-10 h-10 bg-green-100 text-green-600 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined">payments</span></div>
                        </div>
                        <h3 class="text-3xl font-black text-agro-dark">${{ number_format($ventasHoy ?? 1245.50, 2) }}</h3>
                        <p class="text-xs font-bold text-green-500 flex items-center gap-1 mt-2">
                            <span class="material-symbols-outlined text-[14px]">trending_up</span> Ingresos comprobados
                        </p>
                    </div>
                </div>

                {{-- KPI: Pedidos Pendientes --}}
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full group-hover:scale-[1.8] transition-transform duration-500 ease-out z-0"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Por Procesar</span>
                            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined">pending_actions</span></div>
                        </div>
                        <h3 class="text-3xl font-black text-agro-dark">{{ $pedidosPendientes ?? 18 }}</h3>
                        <p class="text-xs font-bold text-blue-500 flex items-center gap-1 mt-2">
                            <span class="material-symbols-outlined text-[14px]">info</span> Requieren atención
                        </p>
                    </div>
                </div>

                {{-- KPI: Alertas de Stock --}}
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-orange-50 rounded-full group-hover:scale-[1.8] transition-transform duration-500 ease-out z-0"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Alertas de Stock</span>
                            <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined">inventory_2</span></div>
                        </div>
                        <h3 class="text-3xl font-black text-agro-dark">{{ $alertasStock ?? 5 }}</h3>
                        <p class="text-xs font-bold text-orange-500 flex items-center gap-1 mt-2">
                            <span class="material-symbols-outlined text-[14px]">warning</span> Productos en crítico
                        </p>
                    </div>
                </div>

                {{-- KPI: Recetas Pendientes --}}
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-50 rounded-full group-hover:scale-[1.8] transition-transform duration-500 ease-out z-0"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Récipes por Aprobar</span>
                            <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined">medical_services</span></div>
                        </div>
                        <h3 class="text-3xl font-black text-agro-dark">{{ $recetasPendientes ?? 3 }}</h3>
                        <p class="text-xs font-bold text-purple-500 flex items-center gap-1 mt-2">
                            <span class="material-symbols-outlined text-[14px]">hourglass_top</span> Pendientes de revisión
                        </p>
                    </div>
                </div>

            </div>

            {{-- 2. CUADRÍCULA DE MÓDULOS --}}
            <div class="mb-6">
                <h2 class="text-lg font-black text-agro-dark flex items-center gap-2 mb-6">
                    <span class="material-symbols-outlined text-primary">apps</span>
                    Módulos del Sistema
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 lg:gap-6">
                    
                    {{-- GRUPO 1: E-COMMERCE Y VENTAS --}}
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group cursor-pointer">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[28px]">shopping_cart</span>
                        </div>
                        <h3 class="text-lg font-black text-agro-dark mb-1 group-hover:text-primary transition-colors">Ventas y Pedidos</h3>
                        <p class="text-sm text-gray-500 mb-4 line-clamp-2">Gestión de órdenes web, validación de pagos, facturación y control de devoluciones.</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Pedidos</span>
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Pagos</span>
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Facturas</span>
                        </div>
                    </div>

                    {{-- GRUPO 2: CATÁLOGO --}}
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group cursor-pointer">
                        <div class="w-12 h-12 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-green-600 group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[28px]">category</span>
                        </div>
                        <h3 class="text-lg font-black text-agro-dark mb-1 group-hover:text-primary transition-colors">Catálogo de Productos</h3>
                        <p class="text-sm text-gray-500 mb-4 line-clamp-2">Creación de productos, combos, categorías, marcas, imágenes y control de precios.</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Productos</span>
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Categorías</span>
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Precios</span>
                        </div>
                    </div>

                    {{-- GRUPO 3: INVENTARIO --}}
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group cursor-pointer">
                        <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[28px]">shelves</span>
                        </div>
                        <h3 class="text-lg font-black text-agro-dark mb-1 group-hover:text-primary transition-colors">Inventario y Lotes</h3>
                        <p class="text-sm text-gray-500 mb-4 line-clamp-2">Gestión de stock físico, fechas de vencimiento, lotes de proveedores y costos.</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Lotes</span>
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Vencimientos</span>
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Proveedores</span>
                        </div>
                    </div>

                    {{-- GRUPO 4: RECETAS VETERINARIAS --}}
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group cursor-pointer">
                        <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[28px]">stethoscope</span>
                        </div>
                        <h3 class="text-lg font-black text-agro-dark mb-1 group-hover:text-primary transition-colors">Control Veterinario</h3>
                        <p class="text-sm text-gray-500 mb-4 line-clamp-2">Revisión y aprobación de récipes médicos para productos controlados y dosificación.</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Recetas</span>
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Aprobaciones</span>
                        </div>
                    </div>

                    {{-- GRUPO 5: LOGÍSTICA --}}
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group cursor-pointer">
                        <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[28px]">local_shipping</span>
                        </div>
                        <h3 class="text-lg font-black text-agro-dark mb-1 group-hover:text-primary transition-colors">Logística y Despachos</h3>
                        <p class="text-sm text-gray-500 mb-4 line-clamp-2">Tarifas de delivery por zona, asignación de repartidores y seguimiento de rutas.</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Zonas Delivery</span>
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Repartidores</span>
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Seguimiento</span>
                        </div>
                    </div>

                    {{-- GRUPO 6: PUNTO DE VENTA (POS) --}}
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group cursor-pointer">
                        <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-teal-600 group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[28px]">point_of_sale</span>
                        </div>
                        <h3 class="text-lg font-black text-agro-dark mb-1 group-hover:text-primary transition-colors">Caja Fuerte (POS)</h3>
                        <p class="text-sm text-gray-500 mb-4 line-clamp-2">Gestión de tienda física, apertura/cierre de cajas, movimientos y cuadres diarios.</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Cajas Físicas</span>
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Sesiones</span>
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Movimientos</span>
                        </div>
                    </div>

                    {{-- GRUPO 7: USUARIOS Y ROLES --}}
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group cursor-pointer">
                        <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[28px]">manage_accounts</span>
                        </div>
                        <h3 class="text-lg font-black text-agro-dark mb-1 group-hover:text-primary transition-colors">Usuarios y Accesos</h3>
                        <p class="text-sm text-gray-500 mb-4 line-clamp-2">Base de datos de clientes, asignación de roles al personal y gestión de permisos.</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Clientes</span>
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Staff</span>
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Roles</span>
                        </div>
                    </div>

                    {{-- GRUPO 8: FINANZAS Y CONFIGURACIÓN --}}
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group cursor-pointer">
                        <div class="w-12 h-12 bg-gray-100 text-gray-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-gray-800 group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[28px]">settings_suggest</span>
                        </div>
                        <h3 class="text-lg font-black text-agro-dark mb-1 group-hover:text-primary transition-colors">Ajustes y Finanzas</h3>
                        <p class="text-sm text-gray-500 mb-4 line-clamp-2">Tasa de cambio (BCV), impuestos, cuentas bancarias, cupones y configuración del sitio.</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Tasas Cambio</span>
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Cuentas Banco</span>
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Ajustes Web</span>
                        </div>
                    </div>

                    {{-- GRUPO 9: REPORTES Y AUDITORÍA --}}
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group cursor-pointer md:col-span-2 xl:col-span-1">
                        <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-rose-600 group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[28px]">query_stats</span>
                        </div>
                        <h3 class="text-lg font-black text-agro-dark mb-1 group-hover:text-primary transition-colors">Reportes y Auditoría</h3>
                        <p class="text-sm text-gray-500 mb-4 line-clamp-2">Vistas SQL detalladas de ventas, productos más vendidos y logs de movimientos.</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Estadísticas Diarias</span>
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Top Ventas</span>
                            <span class="text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-500 px-2 py-1 rounded-md">Logs Sistema</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </main>
</div>

@push('styles')
<style>
    /* Efecto para que los elementos aparezcan suavemente */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.4s ease-out forwards;
    }
    
    /* Scrollbar delgada y elegante para el Sidebar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.2);
    }
</style>
@endpush
@endsection