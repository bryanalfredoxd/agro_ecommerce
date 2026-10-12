@extends('layouts.admin')

@section('title', 'Configuración de la Tienda - Agrosuministros Rodríguez')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up pb-24">
            
            {{-- Encabezado --}}
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('admin.dashboard') }}" class="w-10 h-10 flex-shrink-0 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:text-gray-900 hover:border-gray-400 transition-colors shadow-sm">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div>
                    <h2 class="text-2xl font-black text-agro-dark flex items-center gap-2">
                        <span class="material-symbols-outlined text-gray-600 text-[32px]">storefront</span>
                        Configuración Global
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Ajusta los parámetros operativos principales de Agrosuministros Rodríguez.</p>
                </div>
            </div>

            <form action="{{ route('admin.configuracion.update') }}" method="POST" class="max-w-4xl">
                @csrf
                
                <div class="space-y-6">
                    {{-- Tarjeta 1: Datos Generales e Impuestos --}}
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
                        <h3 class="text-lg font-black text-agro-dark border-b border-gray-100 pb-4 mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-600">info</span> Información e Impuestos
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-2 ml-1">Nombre Comercial de la Empresa</label>
                                <input type="text" name="nombre_empresa" value="{{ old('nombre_empresa', $config->nombre_empresa) }}" required class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all font-bold text-agro-dark outline-none">
                            </div>
                            
                            <div>
                                <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-2 ml-1">Porcentaje de Impuesto (IVA) %</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-3.5 font-bold text-gray-700">%</span>
                                    <input type="number" step="0.01" min="0" max="100" name="iva_porcentaje" id="iva_porcentaje" value="{{ old('iva_porcentaje', $config->iva_porcentaje) }}" required class="limit-decimals w-full h-12 pl-10 pr-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all font-bold text-blue-600 text-lg outline-none">
                                </div>
                                <p class="text-[10px] text-blue-700 mt-1.5 ml-1 font-semibold flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">verified</span>
                                    Porcentaje único y centralizado para todo el sistema (Tienda Web, POS, Facturación, Cotizaciones y Productos).
                                </p>
                            </div>
                        </div>

                        {{-- SECCIÓN: 3 MODALIDADES GLOBALES DE PRECIO (DIVISAS, BOLÍVARES, CASHEA) --}}
                        <div class="mt-8 pt-6 border-t border-gray-100">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                                <div>
                                    <h4 class="text-sm font-black text-agro-dark uppercase tracking-wider flex items-center gap-2">
                                        <span class="material-symbols-outlined text-emerald-600 text-[20px]">price_change</span>
                                        Modalidades de Precio y Recargos Globales
                                    </h4>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        Define el porcentaje adicional (%) para los 3 métodos de venta y su ámbito de aplicación.
                                    </p>
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 w-fit">
                                    Ajuste Global
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                
                                {{-- 1. MODALIDAD DIVISAS --}}
                                <div class="bg-gradient-to-b from-emerald-50/40 to-white rounded-2xl border border-emerald-200/80 p-4.5 flex flex-col justify-between shadow-2xs">
                                    <div>
                                        <div class="flex items-center justify-between gap-2 mb-3">
                                            <div class="flex items-center gap-2">
                                                <div class="size-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-2xs">
                                                    <span class="material-symbols-outlined text-[18px]">attach_money</span>
                                                </div>
                                                <div>
                                                    <h5 class="text-xs font-black text-slate-800 leading-tight">Divisas (USD / USDT)</h5>
                                                    <span class="text-[10px] text-emerald-700 font-bold">Zelle, Binance, Efectivo $</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="space-y-3">
                                            <div>
                                                <label class="block text-[10px] font-black text-slate-600 uppercase tracking-wider mb-1">
                                                    Recargo % sobre PVP
                                                </label>
                                                <div class="relative">
                                                    <span class="absolute left-3.5 top-2.5 text-xs font-black text-emerald-700">%</span>
                                                    <input type="number" 
                                                           step="0.01" 
                                                           min="0" 
                                                           max="100" 
                                                           name="recargo_divisas_pct" 
                                                           id="recargo_divisas_pct" 
                                                           value="{{ old('recargo_divisas_pct', $config->recargo_divisas_pct ?? 0.00) }}" 
                                                           oninput="actualizarSimuladorPrecios()"
                                                           required 
                                                           class="limit-decimals w-full h-10 pl-8 pr-3 rounded-xl bg-white border border-emerald-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 font-black text-emerald-800 text-sm outline-none transition-all">
                                                </div>
                                                <p class="text-[10px] text-slate-600 font-medium mt-1">
                                                    0% = Precio directo estipulado.
                                                </p>
                                            </div>

                                            <div>
                                                <label class="block text-[10px] font-black text-slate-600 uppercase tracking-wider mb-1">
                                                    Ámbito de Aplicación
                                                </label>
                                                <select name="recargo_divisas_aplica" class="w-full h-9 px-3 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 outline-none cursor-pointer focus:border-emerald-500">
                                                    <option value="ambas" {{ ($config->recargo_divisas_aplica ?? 'ambas') === 'ambas' ? 'selected' : '' }}>Tienda Web y POS (Ambas)</option>
                                                    <option value="web" {{ ($config->recargo_divisas_aplica ?? '') === 'web' ? 'selected' : '' }}>Solo Tienda Digital (Web)</option>
                                                    <option value="pos" {{ ($config->recargo_divisas_aplica ?? '') === 'pos' ? 'selected' : '' }}>Solo Tienda Física (POS)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3 pt-2.5 border-t border-emerald-100 flex items-center justify-between text-[11px]">
                                        <span class="text-slate-500 font-medium">Ejemplo $10.00:</span>
                                        <strong class="text-emerald-800 font-black" id="simulador_divisas_badge">$10.00 USD</strong>
                                    </div>
                                </div>

                                {{-- 2. MODALIDAD BOLÍVARES --}}
                                <div class="bg-gradient-to-b from-blue-50/40 to-white rounded-2xl border border-blue-200/80 p-4.5 flex flex-col justify-between shadow-2xs">
                                    <div>
                                        <div class="flex items-center justify-between gap-2 mb-3">
                                            <div class="flex items-center gap-2">
                                                <div class="size-8 rounded-lg bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-2xs">
                                                    <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span>
                                                </div>
                                                <div>
                                                    <h5 class="text-xs font-black text-slate-800 leading-tight">Bolívares (VES)</h5>
                                                    <span class="text-[10px] text-blue-700 font-bold">Pago Móvil, Punto, Transferencias</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="space-y-3">
                                            <div>
                                                <label class="block text-[10px] font-black text-slate-600 uppercase tracking-wider mb-1">
                                                    Recargo % sobre PVP
                                                </label>
                                                <div class="relative">
                                                    <span class="absolute left-3.5 top-2.5 text-xs font-black text-blue-700">%</span>
                                                    <input type="number" 
                                                           step="0.01" 
                                                           min="0" 
                                                           max="100" 
                                                           name="recargo_bolivares_pct" 
                                                           id="recargo_bolivares_pct" 
                                                           value="{{ old('recargo_bolivares_pct', $config->recargo_bolivares_pct ?? 0.00) }}" 
                                                           oninput="actualizarSimuladorPrecios()"
                                                           required 
                                                           class="limit-decimals w-full h-10 pl-8 pr-3 rounded-xl bg-white border border-blue-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 font-black text-blue-800 text-sm outline-none transition-all">
                                                </div>
                                                <p class="text-[10px] text-slate-600 font-medium mt-1">
                                                    Ej: 20% más costoso al pagar en Bs.
                                                </p>
                                            </div>

                                            <div>
                                                <label class="block text-[10px] font-black text-slate-600 uppercase tracking-wider mb-1">
                                                    Ámbito de Aplicación
                                                </label>
                                                <select name="recargo_bolivares_aplica" class="w-full h-9 px-3 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 outline-none cursor-pointer focus:border-blue-500">
                                                    <option value="ambas" {{ ($config->recargo_bolivares_aplica ?? 'ambas') === 'ambas' ? 'selected' : '' }}>Tienda Web y POS (Ambas)</option>
                                                    <option value="web" {{ ($config->recargo_bolivares_aplica ?? '') === 'web' ? 'selected' : '' }}>Solo Tienda Digital (Web)</option>
                                                    <option value="pos" {{ ($config->recargo_bolivares_aplica ?? '') === 'pos' ? 'selected' : '' }}>Solo Tienda Física (POS)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3 pt-2.5 border-t border-blue-100 flex items-center justify-between text-[11px]">
                                        <span class="text-slate-500 font-medium">Ejemplo $10.00:</span>
                                        <strong class="text-blue-800 font-black" id="simulador_bolivares_badge">$12.00 ref.</strong>
                                    </div>
                                </div>

                                {{-- 3. MODALIDAD CASHEA --}}
                                <div class="bg-gradient-to-b from-purple-50/40 to-white rounded-2xl border border-purple-200/80 p-4.5 flex flex-col justify-between shadow-2xs">
                                    <div>
                                        <div class="flex items-center justify-between gap-2 mb-3">
                                            <div class="flex items-center gap-2">
                                                <div class="size-8 rounded-lg bg-[#5800FD] text-white flex items-center justify-center shrink-0 shadow-2xs">
                                                    <span class="material-symbols-outlined text-[18px]">credit_score</span>
                                                </div>
                                                <div>
                                                    <h5 class="text-xs font-black text-slate-800 leading-tight">Cashea (Cuotas)</h5>
                                                    <span class="text-[10px] text-purple-700 font-bold">Financiamiento y Cuotas</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="space-y-3">
                                            <div>
                                                <label class="block text-[10px] font-black text-slate-600 uppercase tracking-wider mb-1">
                                                    Recargo % sobre PVP
                                                </label>
                                                <div class="relative">
                                                    <span class="absolute left-3.5 top-2.5 text-xs font-black text-purple-700">%</span>
                                                    <input type="number" 
                                                           step="0.01" 
                                                           min="0" 
                                                           max="100" 
                                                           name="recargo_cashea_pct" 
                                                           id="recargo_cashea_pct" 
                                                           value="{{ old('recargo_cashea_pct', $config->recargo_cashea_pct ?? 0.00) }}" 
                                                           oninput="actualizarSimuladorPrecios()"
                                                           required 
                                                           class="limit-decimals w-full h-10 pl-8 pr-3 rounded-xl bg-white border border-purple-300 focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 font-black text-purple-900 text-sm outline-none transition-all">
                                                </div>
                                                <p class="text-[10px] text-slate-600 font-medium mt-1">
                                                    Ej: 15% más por compras en Cashea.
                                                </p>
                                            </div>

                                            <div>
                                                <label class="block text-[10px] font-black text-slate-600 uppercase tracking-wider mb-1">
                                                    Ámbito de Aplicación
                                                </label>
                                                <select name="recargo_cashea_aplica" class="w-full h-9 px-3 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 outline-none cursor-pointer focus:border-purple-500">
                                                    <option value="ambas" {{ ($config->recargo_cashea_aplica ?? 'ambas') === 'ambas' ? 'selected' : '' }}>Tienda Web y POS (Ambas)</option>
                                                    <option value="web" {{ ($config->recargo_cashea_aplica ?? '') === 'web' ? 'selected' : '' }}>Solo Tienda Digital (Web)</option>
                                                    <option value="pos" {{ ($config->recargo_cashea_aplica ?? '') === 'pos' ? 'selected' : '' }}>Solo Tienda Física (POS)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3 pt-2.5 border-t border-purple-100 flex items-center justify-between text-[11px]">
                                        <span class="text-slate-500 font-medium">Ejemplo $10.00:</span>
                                        <strong class="text-purple-900 font-black" id="simulador_cashea_badge">$11.50 USD</strong>
                                    </div>
                                </div>

                            </div>

                            {{-- SIMULADOR EN VIVO EXPLICATIVO --}}
                            <div class="mt-4 p-4 rounded-2xl bg-slate-50 border border-slate-200/90 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                <div class="flex items-center gap-2.5">
                                    <div class="size-9 rounded-xl bg-slate-200 text-slate-700 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-[20px]">calculate</span>
                                    </div>
                                    <div>
                                        <span class="font-black text-slate-800 block">Comportamiento en Catálogo y Tienda:</span>
                                        <span class="text-slate-500 text-[11px]">
                                            En las tarjetas del inicio y catálogo siempre se exhibe el precio en <strong>Dólares</strong> con su recargo de Divisas.
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] font-bold">
                                    <span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 border border-emerald-300" id="badge_resumen_divisas">Divisas: $10.00 (+0%)</span>
                                    <span class="px-2.5 py-1 rounded-lg bg-blue-100 text-blue-800 border border-blue-300" id="badge_resumen_bs">Bolívares: $12.00 (+20%)</span>
                                    <span class="px-2.5 py-1 rounded-lg bg-purple-100 text-purple-800 border border-purple-300" id="badge_resumen_cashea">Cashea: $11.50 (+15%)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tarjeta 2: Estado Operativo de la Tienda Web --}}
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
                        <h3 class="text-lg font-black text-agro-dark border-b border-gray-100 pb-4 mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-orange-500">power_settings_new</span> Operatividad Web
                        </h3>
                        
                        <div class="mb-6">
                            <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-2 ml-1">Modo de Funcionamiento Actual</label>
                            <select name="modo_operativo" id="modo_operativo" onchange="verificarModo()" class="w-full h-14 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-orange-500 focus:bg-white focus:ring-2 focus:ring-orange-500/20 transition-all font-bold text-agro-dark outline-none cursor-pointer">
                                <option value="automatico" {{ $config->modo_operativo == 'automatico' ? 'selected' : '' }}>Automático (Abre y cierra según los horarios configurados)</option>
                                <option value="manual_abierto" {{ $config->modo_operativo == 'manual_abierto' ? 'selected' : '' }}>Forzar Abierto 24/7 (Ignora los horarios)</option>
                                <option value="manual_cerrado" {{ $config->modo_operativo == 'manual_cerrado' ? 'selected' : '' }}>Cierre de Emergencia (Nadie puede comprar online)</option>
                            </select>
                        </div>

                        <div id="caja_mensaje_cierre" class="transition-all duration-300 {{ $config->modo_operativo == 'manual_cerrado' ? 'bg-red-50 border border-red-200 p-4 rounded-2xl' : '' }}">
                            <label class="block text-[10px] font-black uppercase tracking-widest mb-2 ml-1 {{ $config->modo_operativo == 'manual_cerrado' ? 'text-red-500' : 'text-gray-700' }}">Mensaje visible cuando la tienda esté cerrada</label>
                            <textarea name="mensaje_cierre_emergencia" rows="3" class="w-full p-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-orange-500 focus:bg-white outline-none text-sm text-gray-600 font-medium">{{ old('mensaje_cierre_emergencia', $config->mensaje_cierre_emergencia) }}</textarea>
                            <p class="text-[10px] text-gray-700 mt-1.5 ml-1">Este texto lo verán los clientes en la página web en lugar del botón "Añadir al Carrito".</p>
                        </div>
                    </div>

                    {{-- Tarjeta 3: Servicio de Delivery / Despacho a Domicilio --}}
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-6">
                            <h3 class="text-lg font-black text-agro-dark flex items-center gap-2">
                                <span class="material-symbols-outlined text-emerald-600">local_shipping</span> Servicio de Delivery / Despacho
                            </h3>
                            <a href="{{ route('admin.zonas_delivery.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1 transition-colors">
                                <span>Administrar Zonas y Tarifas</span>
                                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                            </a>
                        </div>

                        <div class="p-5 rounded-2xl border transition-all {{ $config->delivery_activo ? 'bg-emerald-50/50 border-emerald-200' : 'bg-amber-50/50 border-amber-200' }}" id="contenedor_delivery_toggle">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="size-12 rounded-xl {{ $config->delivery_activo ? 'bg-emerald-500 text-white' : 'bg-amber-500 text-white' }} flex items-center justify-center shrink-0 shadow-xs" id="icono_delivery_toggle">
                                        <span class="material-symbols-outlined text-[26px]">{{ $config->delivery_activo ? 'local_shipping' : 'pause_circle' }}</span>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-black text-slate-800 text-sm sm:text-base">Disponibilidad de Despacho</span>
                                            <span id="badge_delivery_toggle" class="text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase tracking-wider {{ $config->delivery_activo ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                                                {{ $config->delivery_activo ? 'Despacho Disponible' : 'Pausado / No Disponible' }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-600 mt-0.5 font-medium">
                                            Activa o pausa los envíos a domicilio en la tienda web y actualiza el encabezado público.
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center">
                                    <input type="hidden" name="delivery_activo_presente" value="1">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="delivery_activo" id="delivery_activo" value="1" {{ $config->delivery_activo ? 'checked' : '' }} onchange="actualizarEstadoDelivery(this)" class="sr-only peer">
                                        <div class="w-14 h-7 bg-gray-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-600"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Auditoría Footer --}}
                <div class="mt-4 px-2 flex justify-between items-center text-xs font-bold text-gray-700">
                    <p>Última actualización: {{ \Carbon\Carbon::parse($config->actualizado_at)->format('d/m/Y h:i A') }}</p>
                    <p>Editado por: {{ $config->ultimoEditor ? $config->ultimoEditor->nombre : 'Sistema' }}</p>
                </div>

                {{-- Botón Guardar Flotante --}}
                <div class="fixed bottom-0 left-0 lg:left-72 right-0 bg-white/80 backdrop-blur-md border-t border-gray-200 p-4 sm:px-8 flex justify-end z-40 shadow-[0_-10px_30px_rgba(0,0,0,0.05)]">
                    @if(Auth::user()->tienePermiso('configurar_tienda'))
                    <button type="submit" class="px-8 py-3 rounded-xl font-black text-white bg-gray-900 hover:bg-black shadow-lg shadow-gray-900/30 hover:-translate-y-1 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined">save</span> Guardar Configuración
                    </button>
                    @else
                    <button type="button" disabled class="px-8 py-3 rounded-xl font-bold text-gray-400 bg-gray-200 cursor-not-allowed flex items-center gap-2">
                        <span class="material-symbols-outlined">lock</span> Solo Lectura
                    </button>
                    @endif
                </div>
            </form>

        </div>
    </main>
</div>

<div id="toast-container" class="fixed bottom-6 right-6 z-[999] flex flex-col gap-2"></div>

@push('scripts')
<script>
    // 1. Mostrar Notificaciones de Sesión (si el controlador envía "success")
    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif

    @if(session('delivery_cambiado'))
        setTimeout(() => {
            if (typeof window.enviarNotificacionNavegadorDelivery === 'function') {
                window.enviarNotificacionNavegadorDelivery({{ session('delivery_cambiado')['activo'] ? 'true' : 'false' }});
            }
        }, 300);
    @endif
    
    @if(isset($errors) && $errors->any())
        showToast("Revisa los errores en el formulario.", 'error');
    @endif

    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if(!container) return;
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-600' : 'bg-red-600';
        const icon = type === 'success' ? 'check_circle' : 'error';

        toast.className = `flex items-center gap-3 px-5 py-4 rounded-xl shadow-2xl text-white transform translate-y-10 opacity-0 transition-all duration-300 ${bgColor}`;
        toast.innerHTML = `<span class="material-symbols-outlined">${icon}</span><p class="font-bold text-sm">${message}</p>`;

        container.appendChild(toast);
        setTimeout(() => toast.classList.remove('translate-y-10', 'opacity-0'), 10);
        setTimeout(() => {
            toast.classList.add('translate-y-10', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // 2. Control Visual del Modo Operativo
    function verificarModo() {
        const select = document.getElementById('modo_operativo');
        const cajaMensaje = document.getElementById('caja_mensaje_cierre');
        const labelMensaje = cajaMensaje.querySelector('label');

        if (select.value === 'manual_cerrado') {
            cajaMensaje.classList.add('bg-red-50', 'border', 'border-red-200', 'p-4', 'rounded-2xl');
            labelMensaje.classList.replace('text-gray-700', 'text-red-500');
        } else {
            cajaMensaje.classList.remove('bg-red-50', 'border', 'border-red-200', 'p-4', 'rounded-2xl');
            labelMensaje.classList.replace('text-red-500', 'text-gray-700');
        }
    }

    // 3. Control reactivo del Switch de Delivery / Despacho con Notificación del Navegador
    function actualizarEstadoDelivery(checkbox) {
        const contenedor = document.getElementById('contenedor_delivery_toggle');
        const icono = document.getElementById('icono_delivery_toggle');
        const badge = document.getElementById('badge_delivery_toggle');
        const activo = checkbox.checked;

        if (contenedor && icono && badge) {
            if (activo) {
                contenedor.className = "p-5 rounded-2xl border transition-all bg-emerald-50/50 border-emerald-200";
                icono.className = "size-12 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-xs";
                icono.innerHTML = '<span class="material-symbols-outlined text-[26px]">local_shipping</span>';
                badge.className = "text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-300";
                badge.textContent = "Despacho Disponible";
            } else {
                contenedor.className = "p-5 rounded-2xl border transition-all bg-amber-50/50 border-amber-200";
                icono.className = "size-12 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs";
                icono.innerHTML = '<span class="material-symbols-outlined text-[26px]">pause_circle</span>';
                badge.className = "text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-300";
                badge.textContent = "Pausado / No Disponible";
            }
        }

        // Emitir notificación por el navegador (Web Notifications API)
        if (typeof window.enviarNotificacionNavegadorDelivery === 'function') {
            window.enviarNotificacionNavegadorDelivery(activo);
        }

        // Sincronización inmediata vía AJAX en base de datos
        fetch('{{ route("admin.zonas_delivery.toggle_master") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ estado: activo ? 1 : 0 })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                showToast(data.message || 'Error al actualizar el estado de delivery', 'error');
            }
        })
        .catch(err => {
            console.error('Error al sincronizar delivery:', err);
        });
    }

    // 5. Simulador interactivo de modalidades de precio (Divisas, Bolívares, Cashea)
    function actualizarSimuladorPrecios() {
        const base = 10.00;
        const pctDiv = parseFloat(document.getElementById('recargo_divisas_pct')?.value || 0) || 0;
        const pctBs = parseFloat(document.getElementById('recargo_bolivares_pct')?.value || 0) || 0;
        const pctCash = parseFloat(document.getElementById('recargo_cashea_pct')?.value || 0) || 0;

        const valDiv = base * (1 + (pctDiv / 100));
        const valBs = base * (1 + (pctBs / 100));
        const valCash = base * (1 + (pctCash / 100));

        const badgeDiv = document.getElementById('simulador_divisas_badge');
        const badgeBs = document.getElementById('simulador_bolivares_badge');
        const badgeCash = document.getElementById('simulador_cashea_badge');

        const resDiv = document.getElementById('badge_resumen_divisas');
        const resBs = document.getElementById('badge_resumen_bs');
        const resCash = document.getElementById('badge_resumen_cashea');

        if (badgeDiv) badgeDiv.textContent = '$' + valDiv.toFixed(2) + ' USD';
        if (badgeBs) badgeBs.textContent = '$' + valBs.toFixed(2) + ' ref.';
        if (badgeCash) badgeCash.textContent = '$' + valCash.toFixed(2) + ' USD';

        if (resDiv) resDiv.textContent = `Divisas: $${valDiv.toFixed(2)} (${pctDiv >= 0 ? '+' : ''}${pctDiv.toFixed(1)}%)`;
        if (resBs) resBs.textContent = `Bolívares: $${valBs.toFixed(2)} (${pctBs >= 0 ? '+' : ''}${pctBs.toFixed(1)}%)`;
        if (resCash) resCash.textContent = `Cashea: $${valCash.toFixed(2)} (${pctCash >= 0 ? '+' : ''}${pctCash.toFixed(1)}%)`;
    }

    document.addEventListener('DOMContentLoaded', actualizarSimuladorPrecios);
</script>
@endpush
@endsection