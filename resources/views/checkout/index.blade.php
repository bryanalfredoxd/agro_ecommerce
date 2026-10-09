@extends('layouts.app')

@section('title', 'Finalizar Compra - Agrosuministros Rodríguez')

@section('content')
<div class="bg-slate-50 min-h-screen py-8 lg:py-12 animate-fade-in-up font-sans">
    <div class="layout-container px-4 sm:px-6">
        
        {{-- HEADER DEL CHECKOUT Y PASOS DE COMPRA --}}
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-200/80">
            <div>
                <a href="{{ route('carrito.index') }}" class="inline-flex items-center text-slate-500 hover:text-[#1e549e] transition-colors font-bold text-xs sm:text-sm mb-2 group">
                    <span class="material-symbols-outlined text-[18px] mr-1 group-hover:-translate-x-1 transition-transform">arrow_back</span>
                    Volver al Carrito
                </a>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 flex items-center gap-3 tracking-tight">
                    <span class="material-symbols-outlined text-[#3492cf] text-[32px] sm:text-[38px]">shopping_cart_checkout</span>
                    Finalizar <span class="text-[#1e549e]">Compra</span>
                </h1>
            </div>
            
            {{-- Breadcrumbs / Steps Visual --}}
            <div class="hidden sm:flex items-center gap-2 text-xs font-bold text-slate-500 bg-white py-2 px-4 rounded-xl border border-slate-200/80 shadow-2xs">
                <a href="{{ route('carrito.index') }}" class="text-slate-500 hover:text-[#1e549e] flex items-center gap-1.5 transition-colors">
                    <span class="material-symbols-outlined text-[16px]">shopping_cart</span> 1. Carrito
                </a>
                <span class="material-symbols-outlined text-[15px] text-slate-300">chevron_right</span>
                <span class="text-[#1e549e] bg-[#e5f2fb] px-3 py-1 rounded-lg flex items-center gap-1.5 border border-[#aad6f3]/60">
                    <span class="material-symbols-outlined text-[16px]">local_shipping</span> 2. Entrega y Pago
                </span>
                <span class="material-symbols-outlined text-[15px] text-slate-300">chevron_right</span>
                <span class="text-slate-400 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">verified</span> 3. Confirmación
                </span>
            </div>
        </div>

        <form action="{{ route('checkout.store') }}" method="POST" enctype="multipart/form-data" id="checkout-form">
            @csrf
            
            {{-- LAYOUT PRINCIPAL GRID --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                {{-- COLUMNA IZQUIERDA: FORMULARIO DE CHECKOUT --}}
                <div class="lg:col-span-7 xl:col-span-8 space-y-6">
                    
                    {{-- SECCIÓN 1: Método de Entrega --}}
                    <section class="bg-white p-6 sm:p-7 rounded-3xl shadow-xs border border-slate-200/90">
                        <div class="flex items-center justify-between mb-5">
                            <h2 class="text-base sm:text-lg font-black text-slate-900 flex items-center gap-2.5">
                                <span class="flex items-center justify-center size-7 rounded-xl bg-[#e5f2fb] text-[#1e549e] font-black text-xs">1</span>
                                ¿Cómo deseas recibir tu pedido?
                            </h2>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider hidden sm:inline">Paso 1 de 4</span>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            
                            {{-- OPCIÓN RETIRO EN SEDE (PICKUP) --}}
                            <label class="relative cursor-pointer group">
                                <input type="radio" 
                                       name="metodo_entrega" 
                                       value="pickup" 
                                       class="peer sr-only" 
                                       checked 
                                       onchange="window.CheckoutConfig.toggleDirecciones(false)">
                                
                                <div class="p-4 sm:p-5 rounded-2xl border-2 border-slate-200 bg-white peer-checked:border-[#3492cf] peer-checked:bg-[#e5f2fb]/40 hover:border-slate-300 transition-all h-full flex items-start gap-3.5 shadow-2xs">
                                    <div class="size-11 bg-slate-100 rounded-xl flex items-center justify-center text-slate-600 peer-checked:text-white peer-checked:bg-[#1e549e] transition-all shrink-0">
                                        <span class="material-symbols-outlined text-[24px]">storefront</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-1">
                                            <span class="font-bold text-slate-900 text-sm block">Retiro en Sede</span>
                                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/60">Gratis</span>
                                        </div>
                                        <p class="text-xs text-slate-500 font-medium leading-relaxed mt-1">
                                            Retira directamente en nuestra sede física de Agrosuministros Rodríguez.
                                        </p>
                                    </div>
                                </div>
                            </label>

                            {{-- OPCIÓN DELIVERY --}}
                            @if($deliveryActivo)
                                <label class="relative cursor-pointer group">
                                    <input type="radio" 
                                           name="metodo_entrega" 
                                           value="delivery" 
                                           class="peer sr-only" 
                                           onchange="window.CheckoutConfig.toggleDirecciones(true)">
                                    
                                    <div class="p-4 sm:p-5 rounded-2xl border-2 border-slate-200 bg-white peer-checked:border-[#3492cf] peer-checked:bg-[#e5f2fb]/40 hover:border-slate-300 transition-all h-full flex items-start gap-3.5 shadow-2xs">
                                        <div class="size-11 bg-slate-100 rounded-xl flex items-center justify-center text-slate-600 peer-checked:text-white peer-checked:bg-[#1e549e] transition-all shrink-0">
                                            <span class="material-symbols-outlined text-[24px]">local_shipping</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-1">
                                                <span class="font-bold text-slate-900 text-sm block">Envío / Delivery</span>
                                                <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-md bg-[#e5f2fb] text-[#1e549e] border border-[#aad6f3]/60">Despacho</span>
                                            </div>
                                            <p class="text-xs text-slate-500 font-medium leading-relaxed mt-1">
                                                Despacho y entrega segura directo a tu finca, hogar o punto acordado.
                                            </p>
                                        </div>
                                    </div>
                                </label>
                            @else
                                <div class="relative cursor-not-allowed group opacity-55">
                                    <div class="p-4 sm:p-5 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 h-full flex items-start gap-3.5 shadow-none">
                                        <div class="size-11 bg-slate-200 rounded-xl flex items-center justify-center text-slate-400 transition-all shrink-0">
                                            <span class="material-symbols-outlined text-[24px]">local_shipping</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-1">
                                                <span class="font-bold text-slate-500 text-sm block">Envío / Delivery</span>
                                                <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 border border-amber-200">Pausado</span>
                                            </div>
                                            <p class="text-xs text-slate-400 font-medium leading-relaxed mt-1">
                                                Servicio de envíos pausado temporalmente por la administración. Solo retiro en sede física.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                        </div>
                    </section>

                    {{-- SECCIÓN 2: Dirección de Envío --}}
                    <section id="seccion-direcciones" class="bg-white p-6 sm:p-7 rounded-3xl shadow-xs border border-slate-200/90 transition-all duration-300">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-5 gap-3">
                            <h2 class="text-base sm:text-lg font-black text-slate-900 flex items-center gap-2.5">
                                <span class="flex items-center justify-center size-7 rounded-xl bg-[#e5f2fb] text-[#1e549e] font-black text-xs">2</span>
                                Dirección de Entrega
                            </h2>
                            <a href="{{ route('perfil') }}#direcciones" target="_blank" class="text-xs font-bold text-[#1e549e] hover:text-white hover:bg-[#1e549e] bg-[#e5f2fb] px-3 py-1.5 rounded-xl transition-colors flex items-center gap-1.5 border border-[#aad6f3]/50">
                                <span class="material-symbols-outlined text-[16px]">add_location_alt</span> Registrar Nueva Dirección
                            </a>
                        </div>

                        @if($direcciones->count() > 0)
                            <div class="grid grid-cols-1 gap-3">
                                @foreach($direcciones->sortByDesc('es_principal')->take(2) as $dir)
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" 
                                               name="direccion_id" 
                                               value="{{ $dir->id }}" 
                                               class="peer sr-only" 
                                               data-cobertura="{{ $dir->cobertura_valida ? '1' : '0' }}"
                                               data-flete="{{ $dir->costo_delivery_calculado }}"
                                               data-zona-nombre="{{ $dir->zona_calculada->nombre_zona ?? 'Zona Delivery' }}"
                                               data-distancia="{{ $dir->distancia_km_calculada ?? '' }}"
                                               {{ $dir->es_principal ? 'checked' : '' }}
                                               onchange="window.CheckoutConfig.alCambiarDireccion(this)">
                                        
                                        <div class="p-4 rounded-2xl border-2 {{ $dir->cobertura_valida ? 'border-slate-200 peer-checked:border-[#3492cf] peer-checked:bg-[#e5f2fb]/40' : 'border-amber-200/80 bg-amber-50/20' }} hover:border-slate-300 transition-all flex items-start gap-3.5 shadow-2xs">
                                            <div class="mt-0.5 text-slate-300 peer-checked:text-[#1e549e] transition-colors">
                                                <span class="material-symbols-outlined text-[22px]">radio_button_checked</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1 flex-wrap">
                                                    <span class="font-bold text-slate-900 text-sm">{{ $dir->alias }}</span>
                                                    @if($dir->es_principal)
                                                        <span class="bg-[#e5f2fb] text-[#1e549e] border border-[#aad6f3]/60 text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md">Principal</span>
                                                    @endif
                                                    @if($dir->cobertura_valida)
                                                        <span class="text-[10px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md flex items-center gap-1">
                                                            <span class="material-symbols-outlined text-[12px]">verified</span>
                                                            Zona: {{ $dir->zona_calculada->nombre_zona }} (+${{ number_format($dir->costo_delivery_calculado, 2) }})
                                                            @if($dir->distancia_km_calculada)
                                                                <span class="text-emerald-600 font-normal">({{ $dir->distancia_km_calculada }} km)</span>
                                                            @endif
                                                        </span>
                                                    @else
                                                        <span class="text-[10px] font-bold text-amber-800 bg-amber-50 border border-amber-300 px-2 py-0.5 rounded-md flex items-center gap-1">
                                                            <span class="material-symbols-outlined text-[12px] text-amber-600">warning</span>
                                                            Fuera de radio de delivery
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-xs sm:text-sm text-slate-600 leading-snug">{{ $dir->direccion_texto }}</p>
                                                @if($dir->referencia_punto)
                                                    <p class="text-xs font-medium text-slate-500 mt-1 flex items-start gap-1">
                                                        <span class="material-symbols-outlined text-[15px] text-slate-400">info</span> 
                                                        Ref: {{ $dir->referencia_punto }}
                                                    </p>
                                                @endif
                                                @if(!$dir->cobertura_valida)
                                                    <p class="text-[11px] text-amber-800 font-medium mt-1.5 flex items-center gap-1 bg-amber-50/80 p-2 rounded-xl border border-amber-200/80">
                                                        <span class="material-symbols-outlined text-[15px] text-amber-600 shrink-0">not_listed_location</span>
                                                        <span>Esta ubicación supera el radio de entrega. Al seleccionar esta dirección solo está disponible la opción de <strong>Retiro en Sede</strong>.</span>
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            @if($direcciones->count() > 2)
                                <button type="button" 
                                        onclick="window.CheckoutConfig.toggleModal('modal-otras-direcciones')" 
                                        class="mt-3 w-full py-2.5 text-xs font-bold text-slate-600 bg-slate-50 rounded-xl hover:bg-slate-100 hover:text-[#1e549e] transition-colors flex items-center justify-center gap-1.5 border border-slate-200">
                                    <span class="material-symbols-outlined text-[18px]">list</span>
                                    Ver mis otras {{ $direcciones->count() - 2 }} direcciones guardadas
                                </button>
                            @endif
                        @else
                            <div class="text-center p-8 bg-rose-50/50 rounded-2xl border border-rose-200/80 border-dashed">
                                <div class="size-14 bg-white rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-2xs text-rose-500">
                                    <span class="material-symbols-outlined text-[30px]">wrong_location</span>
                                </div>
                                <h3 class="text-slate-800 font-bold text-sm mb-1">No tienes direcciones registradas</h3>
                                <p class="text-slate-500 text-xs mb-4">Para solicitar delivery necesitas al menos una dirección registrada en tu cuenta.</p>
                                <a href="{{ route('perfil') }}#direcciones" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#1e549e] text-white font-bold text-xs rounded-xl hover:bg-[#3492cf] transition-colors shadow-2xs">
                                    <span class="material-symbols-outlined text-[16px]">add_location</span>
                                    Registrar dirección en mi perfil
                                </a>
                            </div>
                        @endif
                        @error('direccion_id') 
                            <span class="text-xs text-rose-500 font-bold flex items-center gap-1 mt-2.5">
                                <span class="material-symbols-outlined text-sm">error</span> {{ $message }}
                            </span> 
                        @enderror
                    </section>

                    {{-- SECCIÓN 3: Forma de Pago --}}
                    <section class="bg-white p-6 sm:p-7 rounded-3xl shadow-xs border border-slate-200/90">
                        <div class="flex items-center justify-between mb-5">
                            <h2 class="text-base sm:text-lg font-black text-slate-900 flex items-center gap-2.5">
                                <span class="flex items-center justify-center size-7 rounded-xl bg-[#e5f2fb] text-[#1e549e] font-black text-xs">3</span>
                                Forma de Pago
                            </h2>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider hidden sm:inline">Paso 3 de 4</span>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($metodosPago->where('activo', 1)->take(6) as $metodo)
                                @php
                                    $icon = match($metodo->tipo_metodo) {
                                        'pago_movil' => 'phone_iphone', 
                                        'zelle' => 'zelle_svg', 
                                        'transferencia' => 'account_balance',
                                        'efectivo_usd', 'efectivo_bs' => 'attach_money', 
                                        'binance' => 'binance_svg', 
                                        'punto_venta', 'biopago' => 'point_of_sale',
                                        default => 'payments'
                                    };
                                    $iconColor = match($metodo->tipo_metodo) {
                                        'pago_movil', 'transferencia' => 'text-[#1e549e] bg-[#e5f2fb]', 
                                        'zelle' => 'text-[#5800FD] bg-[#5800FD]/10',
                                        'efectivo_usd', 'efectivo_bs' => 'text-emerald-700 bg-emerald-50', 
                                        'binance' => 'text-[#F3BA2F] bg-[#F3BA2F]/10',
                                        default => 'text-slate-600 bg-slate-100'
                                    };
                                @endphp

                                <label class="relative cursor-pointer group payment-card-radio h-full">
                                    <input type="radio" 
                                           name="metodo_pago_id" 
                                           value="{{ $metodo->id }}" 
                                           required 
                                           class="peer sr-only"
                                           data-cuenta="{{ json_encode([
                                               'id' => $metodo->id,
                                               'tipo' => $metodo->tipo_metodo,
                                               'nombre' => $metodo->nombre,
                                               'banco' => $metodo->banco_entidad,
                                               'cuenta' => $metodo->numero_cuenta,
                                               'telefono' => $metodo->telefono,
                                               'identificacion' => $metodo->identificacion,
                                               'email' => $metodo->email,
                                               'titular' => $metodo->nombre_titular,
                                               'instrucciones' => $metodo->instrucciones_adicionales,
                                               'icono' => $icon,
                                               'icono_url' => in_array($icon, ['zelle_svg', 'binance_svg']) ? ($icon === 'zelle_svg' ? asset('img/pagos/zelle.png') : asset('img/pagos/binance.png')) : null
                                           ]) }}"
                                           onchange="window.CheckoutConfig.manejarCambioPago(this)"
                                           onclick="window.CheckoutConfig.manejarCambioPago(this)">
                                    
                                    <div class="rounded-2xl border-2 border-slate-200 bg-white p-3.5 sm:p-4 flex flex-col items-center justify-center gap-2 text-center h-full hover:border-slate-300 transition-all relative overflow-hidden peer-checked:border-[#3492cf] peer-checked:bg-[#e5f2fb]/40 shadow-2xs">
                                        
                                        <div class="absolute top-2 right-2 text-white bg-[#1e549e] rounded-full size-4 flex items-center justify-center opacity-0 scale-50 transition-all duration-200 peer-checked:opacity-100 peer-checked:scale-100">
                                            <span class="material-symbols-outlined text-[12px] font-bold">check</span>
                                        </div>

                                        <div class="size-11 rounded-2xl flex items-center justify-center {{ $iconColor }} mb-0.5 overflow-hidden transition-transform group-hover:scale-105">
                                            @if($icon === 'zelle_svg')
                                                <img src="{{ asset('img/pagos/zelle.png') }}" alt="Zelle" class="w-full h-full object-contain p-2">
                                            @elseif($icon === 'binance_svg')
                                                <img src="{{ asset('img/pagos/binance.png') }}" alt="Binance" class="w-full h-full object-contain p-2">
                                            @else
                                                <span class="material-symbols-outlined text-[24px]">{{ $icon }}</span>
                                            @endif
                                        </div>
                                        
                                        <span class="text-xs font-bold text-slate-800 leading-tight">{{ $metodo->nombre }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        @if($metodosPago->where('activo', 1)->count() > 6)
                            <button type="button" 
                                    onclick="window.CheckoutConfig.toggleModal('modal-todos-pagos')" 
                                    class="mt-3.5 w-full py-2.5 text-xs font-bold text-slate-600 bg-slate-50 rounded-xl hover:bg-slate-100 hover:text-[#1e549e] transition-colors flex items-center justify-center gap-1.5 border border-slate-200">
                                <span class="material-symbols-outlined text-[18px]">apps</span>
                                Ver todos los métodos de pago disponibles ({{ $metodosPago->where('activo', 1)->count() }})
                            </button>
                        @endif
                        @error('metodo_pago_id') 
                            <span class="text-xs text-rose-500 font-bold flex items-center gap-1 mt-2.5">
                                <span class="material-symbols-outlined text-sm">error</span> Debes seleccionar una forma de pago
                            </span> 
                        @enderror

                        {{-- Instrucciones de Pago y Datos Bancarios --}}
                        <div id="info-pago-container" class="mt-6 p-5 sm:p-6 bg-slate-50/70 rounded-3xl border border-slate-200/90 shadow-2xs hidden transition-all duration-300">
                            
                            {{-- Header del Método de Pago --}}
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-slate-200/80 mb-5">
                                <div class="flex items-center gap-3">
                                    <div id="info-pago-icon-badge" class="size-11 rounded-2xl bg-[#e5f2fb] text-[#1e549e] flex items-center justify-center shrink-0 shadow-2xs font-bold overflow-hidden p-2">
                                        <span id="info-pago-icon-span" class="material-symbols-outlined text-[24px]">account_balance</span>
                                        <img id="info-pago-icon-img" src="" alt="Logo" class="w-full h-full object-contain hidden">
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 id="info-pago-titulo" class="text-sm sm:text-base font-black text-slate-900 leading-tight">
                                                Instrucciones de Pago Oficiales
                                            </h4>
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200/70">
                                                <span class="material-symbols-outlined text-[12px]">verified</span> Oficial
                                            </span>
                                        </div>
                                        <p id="info-pago-subtitulo" class="text-xs text-slate-500 mt-0.5">
                                            Utiliza los siguientes datos para realizar tu transferencia o pago.
                                        </p>
                                    </div>
                                </div>

                                <button type="button" 
                                        id="btn-copiar-todo"
                                        onclick="window.CheckoutConfig.copiarTodosLosDatos()" 
                                        class="self-start sm:self-auto inline-flex items-center gap-1.5 text-xs font-bold text-[#1e549e] bg-white hover:bg-[#e5f2fb] px-3.5 py-2 rounded-xl border border-slate-200 shadow-2xs transition-all active:scale-95 cursor-pointer">
                                    <span class="material-symbols-outlined text-[16px]">content_copy</span>
                                    <span>Copiar todo</span>
                                </button>
                            </div>

                            {{-- Grid de Datos Bancarios (Para Transferencias, Pago Móvil, Zelle, Binance, etc.) --}}
                            <div id="info-pago-grid" class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                                {{-- Inyectado dinámicamente vía JS con tarjetas ejecutivas y botón de copiar individual --}}
                            </div>

                            {{-- Estado para Pagos Presenciales / Efectivo / Punto de Venta --}}
                            <div id="info-pago-presencial" class="hidden mb-4 p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
                                <div class="flex items-start gap-3.5">
                                    <div class="size-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-[26px]">payments</span>
                                    </div>
                                    <div>
                                        <h5 class="text-sm font-black text-slate-900" id="info-presencial-titulo">Pago Presencial Directo</h5>
                                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed" id="info-presencial-desc"></p>
                                        <div class="mt-3 inline-flex items-center gap-1.5 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100">
                                            <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                            No requiere comprobante bancario anticipado
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Nota de Instrucciones Adicionales (si el método la tiene) --}}
                            <div id="info-pago-nota-container" class="hidden mb-4 p-3.5 rounded-2xl bg-amber-50/90 border border-amber-200/80 text-amber-900 text-xs">
                                <div class="flex items-start gap-2.5">
                                    <span class="material-symbols-outlined text-amber-600 text-[18px] shrink-0 mt-0.5">info</span>
                                    <div class="leading-relaxed">
                                        <span class="font-bold">Indicación importante:</span> 
                                        <span id="info-pago-nota-texto" class="font-medium text-slate-800"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Campos para Comprobante y Nro de Referencia --}}
                            <div id="campos-referencia" class="pt-5 border-t border-slate-200/80">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <div class="flex items-center justify-between mb-1.5">
                                            <label for="input-referencia" id="label-referencia" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                                                Nro. de Referencia <span class="text-rose-500">*</span>
                                            </label>
                                            <span id="helper-referencia" class="text-[10px] text-slate-400 font-medium hidden sm:inline">Últimos dígitos</span>
                                        </div>
                                        <div class="relative">
                                            <input type="text" 
                                                   name="referencia" 
                                                   id="input-referencia" 
                                                   class="w-full h-11 rounded-xl border border-slate-200 bg-white focus:border-[#3492cf] focus:ring-2 focus:ring-[#3492cf]/20 text-sm px-4 shadow-2xs font-mono font-bold text-slate-900 placeholder:font-sans placeholder:font-normal placeholder:text-slate-400 transition-all" 
                                                   placeholder="Ej: 12345678">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                            Comprobante / Voucher <span class="text-slate-400 font-normal lowercase">(opcional)</span>
                                        </label>
                                        <div class="relative w-full h-11 bg-white border border-slate-200 rounded-xl shadow-2xs flex items-center overflow-hidden hover:border-[#3492cf] cursor-pointer transition-colors group" 
                                             onclick="document.getElementById('input-comprobante').click()">
                                            <div class="px-3.5 border-r border-slate-100 text-slate-500 bg-slate-50 h-full flex items-center group-hover:text-[#1e549e] group-hover:bg-[#e5f2fb] transition-colors">
                                                <span class="material-symbols-outlined text-[18px]">upload_file</span>
                                            </div>
                                            <div id="nombre-archivo-container" class="px-3.5 text-xs text-slate-400 truncate flex-1 font-medium">
                                                Adjuntar captura o voucher (PNG, JPG)...
                                            </div>
                                            <input type="file" 
                                                   name="comprobante" 
                                                   id="input-comprobante" 
                                                   accept="image/*" 
                                                   class="hidden" 
                                                   onchange="window.CheckoutConfig.mostrarNombreArchivo(this)">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- SECCIÓN 4: Notas del Pedido --}}
                    <section class="bg-white p-6 sm:p-7 rounded-3xl shadow-xs border border-slate-200/90">
                        <h2 class="text-base sm:text-lg font-black text-slate-900 mb-3 flex items-center gap-2.5">
                            <span class="flex items-center justify-center size-7 rounded-xl bg-slate-100 text-slate-600 font-black text-xs">4</span>
                            Instrucciones o Notas Especiales (Opcional)
                        </h2>
                        <p class="text-xs text-slate-500 mb-3">Si tienes alguna indicación especial sobre el despacho o entrega, anótala aquí.</p>
                        <textarea name="observaciones" 
                                  rows="3" 
                                  class="w-full rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-[#3492cf] focus:ring-2 focus:ring-[#3492cf]/20 text-xs sm:text-sm p-4 transition-all placeholder:text-slate-400 resize-none shadow-inner" 
                                  placeholder="Ej: Dejar con el encargado en la portería, entregar preferiblemente en la mañana..."></textarea>
                    </section>

                </div>

                {{-- COLUMNA DERECHA: RESUMEN DE COMPRA (STICKY) --}}
                <div class="lg:col-span-5 xl:col-span-4">
                    <section class="bg-white rounded-3xl shadow-xs border border-slate-200/90 overflow-hidden sticky top-24">
                        
                        {{-- Cabecera del Resumen --}}
                        <div class="p-5 sm:p-6 bg-gradient-to-r from-[#0f2347] to-[#1e549e] text-white relative overflow-hidden">
                            <div class="absolute -top-10 -right-10 size-32 bg-[#3492cf]/20 rounded-full blur-2xl pointer-events-none"></div>
                            <div class="flex items-center justify-between relative z-10">
                                <h3 class="text-base sm:text-lg font-black flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[22px]">receipt_long</span>
                                    Resumen de Compra
                                </h3>
                                <span class="text-[10px] font-bold uppercase tracking-wider bg-white/15 px-2.5 py-0.5 rounded-full text-[#aad6f3]">
                                    {{ $carrito->count() }} {{ $carrito->count() === 1 ? 'insumo' : 'insumos' }}
                                </span>
                            </div>
                        </div>

                        <div class="p-5 sm:p-6">
                            {{-- Mini Items del Carrito --}}
                            <div class="space-y-3.5 max-h-56 overflow-y-auto custom-scrollbar pr-1.5 mb-5">
                                @foreach($carrito as $item)
                                    @php
                                        $imgItem = $item->producto?->imagenes?->first()?->imagen_url ?? $item->producto?->imagen_url;
                                    @endphp
                                    <div class="flex gap-3 items-center">
                                        <div class="size-12 bg-slate-50 rounded-xl overflow-hidden border border-slate-200/80 shrink-0 p-1 flex items-center justify-center">
                                            @if($imgItem)
                                                <img src="{{ asset($imgItem) }}" 
                                                     alt="{{ $item->producto->nombre ?? 'Producto' }}"
                                                     class="w-full h-full object-contain">
                                            @else
                                                <span class="material-symbols-outlined text-slate-300 text-xl">inventory_2</span>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold text-slate-900 truncate">{{ $item->producto->nombre }}</p>
                                            <div class="flex justify-between items-center mt-0.5">
                                                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-1.5 py-0.2 rounded">
                                                    Cant: {{ (float)$item->cantidad }}
                                                </span>
                                                <span class="text-xs font-black text-[#1e549e]">
                                                    ${{ number_format(($item->producto->precio_oferta_usd ?: $item->producto->precio_venta_usd) * $item->cantidad, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Desglose de Totales --}}
                            <div class="border-t border-dashed border-slate-200 pt-4 space-y-2.5">
                                <div class="flex justify-between text-xs sm:text-sm text-slate-600 font-medium">
                                    <span>Subtotal</span>
                                    <span class="text-slate-800 font-bold">${{ number_format($subtotal, 2) }}</span>
                                </div>

                                <div class="flex justify-between text-xs sm:text-sm text-slate-600 font-medium">
                                    <span>Impuestos (IVA 16%)</span>
                                    <span class="text-slate-800 font-bold">${{ number_format($montoIva, 2) }}</span>
                                </div>

                                <div id="fila-delivery" class="flex justify-between text-xs sm:text-sm font-bold text-[#1e549e] transition-all">
                                    <span>Retiro en Tienda</span>
                                    <span class="text-emerald-600">Gratis</span>
                                </div>
                                
                                <div class="border-t border-slate-100 pt-3 mt-2 flex justify-between items-baseline">
                                    <span class="text-sm sm:text-base font-black text-slate-900">Total a Pagar</span>
                                    <div class="text-right">
                                        <span id="total-usd-display" class="text-2xl sm:text-3xl font-black text-[#1e549e] leading-none">
                                            ${{ number_format($totalUsd, 2) }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase ml-1">USD</span>
                                    </div>
                                </div>

                                {{-- Conversión en Bolívares (VES) --}}
                                <div class="bg-gradient-to-r from-[#e5f2fb] to-slate-50 rounded-2xl p-3.5 border border-[#aad6f3]/60 flex justify-between items-center mt-3">
                                    <div>
                                        <span class="text-[10px] font-black text-[#1e549e] uppercase tracking-wider block">Equivalente BCV</span>
                                        <span class="text-[10px] font-medium text-slate-500">
                                            Tasa: {{ number_format($tasaValor, 2, ',', '.') }} Bs/$
                                        </span>
                                    </div>
                                    <span id="total-ves-display" class="text-base sm:text-lg font-black text-slate-800">
                                        Bs. {{ number_format($totalVes, 2, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            {{-- BOTÓN PRINCIPAL: PROCESAR COMPRA --}}
                            <button type="submit" 
                                    id="btn-confirmar" 
                                    class="w-full mt-5 py-3.5 bg-[#1e549e] hover:bg-[#3492cf] text-white font-black text-sm sm:text-base rounded-2xl shadow-xs hover:shadow-md transition-all duration-200 flex items-center justify-center gap-2 group active:scale-98">
                                <span class="material-symbols-outlined text-[22px]">verified</span>
                                <span>Procesar y Confirmar Pedido</span>
                            </button>
                            
                            <p class="text-[11px] text-center text-slate-400 mt-3.5 flex justify-center items-center gap-1 font-medium">   
                                <span class="material-symbols-outlined text-[15px] text-emerald-600">lock</span> 
                                Compra protegida y confirmada por Agrosuministros Rodríguez.
                            </p>
                        </div>
                    </section>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ================= MODALES ================= --}}
{{-- MODAL: SELECCIONAR OTRAS DIRECCIONES --}}
<div id="modal-otras-direcciones" class="fixed inset-0 z-[120] hidden" aria-labelledby="modal-dir-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-[#0f2347]/50 backdrop-blur-xs transition-opacity opacity-0" id="modal-otras-direcciones-backdrop" onclick="window.CheckoutConfig.toggleModal('modal-otras-direcciones')"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg opacity-0 translate-y-6 sm:translate-y-0 sm:scale-95 flex flex-col max-h-[85vh] border border-slate-100" id="modal-otras-direcciones-panel">
                <div class="bg-white px-6 py-4 border-b border-slate-100 flex items-center justify-between sticky top-0 z-10">
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2" id="modal-dir-title">
                        <span class="material-symbols-outlined text-[#1e549e]">location_on</span> Mis Direcciones Guardadas
                    </h3>
                    <button type="button" onclick="window.CheckoutConfig.toggleModal('modal-otras-direcciones')" class="size-8 flex items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto flex-1 custom-scrollbar">
                    <div class="space-y-3">
                        @foreach($direcciones->sortByDesc('es_principal') as $dir)
                            <label class="relative cursor-pointer group block">
                                <input type="radio" 
                                       name="direccion_id_modal" 
                                       value="{{ $dir->id }}" 
                                       class="peer sr-only" 
                                       data-cobertura="{{ $dir->cobertura_valida ? '1' : '0' }}"
                                       data-flete="{{ $dir->costo_delivery_calculado }}"
                                       data-zona-nombre="{{ $dir->zona_calculada->nombre_zona ?? 'Zona Delivery' }}"
                                       data-distancia="{{ $dir->distancia_km_calculada ?? '' }}"
                                       {{ $dir->es_principal ? 'checked' : '' }}
                                       onchange="window.CheckoutConfig.seleccionarDireccionDesdeModal({{ $dir->id }}, this)">
                                
                                <div class="p-4 rounded-2xl border-2 {{ $dir->cobertura_valida ? 'border-slate-200 peer-checked:border-[#3492cf] peer-checked:bg-[#e5f2fb]/40' : 'border-amber-200/80 bg-amber-50/20' }} hover:border-slate-300 transition-all flex items-start gap-3.5 shadow-2xs">
                                    <div class="mt-0.5 text-slate-300 peer-checked:text-[#1e549e] transition-colors">
                                        <span class="material-symbols-outlined text-[22px]">radio_button_checked</span>
                                    </div>
                                    <div class="flex-1 min-w-0 text-left">
                                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                                            <span class="font-bold text-slate-900 text-sm">{{ $dir->alias }}</span>
                                            @if($dir->es_principal)
                                                <span class="bg-[#e5f2fb] text-[#1e549e] border border-[#aad6f3]/60 text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md">Principal</span>
                                            @endif
                                            @if($dir->cobertura_valida)
                                                <span class="text-[10px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[12px]">verified</span>
                                                    {{ $dir->zona_calculada->nombre_zona }} (+${{ number_format($dir->costo_delivery_calculado, 2) }})
                                                </span>
                                            @else
                                                <span class="text-[10px] font-bold text-amber-800 bg-amber-50 border border-amber-300 px-2 py-0.5 rounded-md flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[12px] text-amber-600">warning</span>
                                                    Fuera de radio
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs sm:text-sm text-slate-600 leading-snug">{{ $dir->direccion_texto }}</p>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: TODOS LOS MÉTODOS DE PAGO --}}
<div id="modal-todos-pagos" class="fixed inset-0 z-[120] hidden" aria-labelledby="modal-pago-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-[#0f2347]/50 backdrop-blur-xs transition-opacity opacity-0" id="modal-todos-pagos-backdrop" onclick="window.CheckoutConfig.toggleModal('modal-todos-pagos')"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl opacity-0 translate-y-6 sm:translate-y-0 sm:scale-95 flex flex-col max-h-[85vh] border border-slate-100" id="modal-todos-pagos-panel">
                <div class="bg-white px-6 py-4 border-b border-slate-100 flex items-center justify-between sticky top-0 z-10">
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2" id="modal-pago-title">
                        <span class="material-symbols-outlined text-[#1e549e]">payments</span> Todos los Métodos de Pago
                    </h3>
                    <button type="button" onclick="window.CheckoutConfig.toggleModal('modal-todos-pagos')" class="size-8 flex items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto flex-1 custom-scrollbar">
                     <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($metodosPago->where('activo', 1) as $metodo)
                            @php
                                $icon = match($metodo->tipo_metodo) {
                                    'pago_movil' => 'phone_iphone', 
                                    'zelle' => 'zelle_svg', 
                                    'transferencia' => 'account_balance',
                                    'efectivo_usd', 'efectivo_bs' => 'attach_money', 
                                    'binance' => 'binance_svg', 
                                    'punto_venta', 'biopago' => 'point_of_sale',
                                    default => 'payments'
                                };
                                $iconColor = match($metodo->tipo_metodo) {
                                    'pago_movil', 'transferencia' => 'text-[#1e549e] bg-[#e5f2fb]', 
                                    'zelle' => 'text-[#5800FD] bg-[#5800FD]/10',
                                    'efectivo_usd', 'efectivo_bs' => 'text-emerald-700 bg-emerald-50', 
                                    'binance' => 'text-[#F3BA2F] bg-[#F3BA2F]/10',
                                    default => 'text-slate-600 bg-slate-100'
                                };
                            @endphp
                             <label class="relative cursor-pointer payment-card-radio block">
                                <input type="radio" 
                                       name="metodo_pago_id_modal" 
                                       value="{{ $metodo->id }}" 
                                       class="peer sr-only"
                                       data-cuenta="{{ json_encode([
                                           'id' => $metodo->id,
                                           'tipo' => $metodo->tipo_metodo,
                                           'nombre' => $metodo->nombre,
                                           'banco' => $metodo->banco_entidad,
                                           'cuenta' => $metodo->numero_cuenta,
                                           'telefono' => $metodo->telefono,
                                           'identificacion' => $metodo->identificacion,
                                           'email' => $metodo->email,
                                           'titular' => $metodo->nombre_titular,
                                           'instrucciones' => $metodo->instrucciones_adicionales,
                                           'icono' => $icon,
                                           'icono_url' => in_array($icon, ['zelle_svg', 'binance_svg']) ? ($icon === 'zelle_svg' ? asset('img/pagos/zelle.png') : asset('img/pagos/binance.png')) : null
                                       ]) }}"
                                       onclick="window.CheckoutConfig.seleccionarPagoDesdeModal(this)">
                                
                                <div class="rounded-2xl border-2 border-slate-200 bg-white p-3.5 flex flex-col items-center justify-center gap-2 text-center h-full hover:border-slate-300 transition-all relative overflow-hidden peer-checked:border-[#3492cf] peer-checked:bg-[#e5f2fb]/40 shadow-2xs">
                                    <div class="absolute top-2 right-2 text-white bg-[#1e549e] rounded-full size-4 flex items-center justify-center opacity-0 scale-50 transition-all duration-200 peer-checked:opacity-100 peer-checked:scale-100">
                                        <span class="material-symbols-outlined text-[12px] font-bold">check</span>
                                    </div>
                                    
                                    <div class="size-11 rounded-2xl flex items-center justify-center {{ $iconColor }} mb-0.5 overflow-hidden">
                                        @if($icon === 'zelle_svg') 
                                            <img src="{{ asset('img/pagos/zelle.png') }}" class="w-full h-full object-contain p-2">
                                        @elseif($icon === 'binance_svg') 
                                            <img src="{{ asset('img/pagos/binance.png') }}" class="w-full h-full object-contain p-2">
                                        @else 
                                            <span class="material-symbols-outlined text-[24px]">{{ $icon }}</span> 
                                        @endif
                                    </div>
                                    <span class="text-xs font-bold text-slate-800 leading-tight">{{ $metodo->nombre }}</span>
                                </div>
                            </label>
                        @endforeach
                     </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.CheckoutVars = {
        subtotalBase: {{ (float) ($subtotal + $montoIva) }},
        tasaCambio: {{ (float) $tasaValor }},
        precioDelivery: 0,
        deliveryActivo: {{ $deliveryActivo ? 'true' : 'false' }}
    };
</script>
<script src="{{ asset('js/checkout.js') }}"></script>
@endpush