@extends('layouts.app')

@section('title', 'Finalizar Compra - Agropecuaria Venezuela')

@section('styles')
<style>
    /* Pequeños ajustes para los radios personalizados */
    .payment-card-radio:checked + div {
        border-color: rgb(var(--color-primary));
        background-color: rgb(var(--color-primary) / 0.05);
    }
    .payment-card-radio:checked + div .check-icon {
        opacity: 1;
        transform: scale(1);
    }
    /* Ajuste para el mapa si se abre el modal de nueva dirección */
    #map-canvas { z-index: 1; width: 100%; height: 100%; min-height: 300px; }
</style>
@endsection

@section('content')
<div class="bg-gray-50 min-h-screen py-6 lg:py-10 animate-fade-in-up font-sans">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-6 lg:mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('carrito.index') }}" class="inline-flex items-center text-gray-500 hover:text-primary transition-colors font-bold text-sm mb-2 group">
                    <span class="material-symbols-outlined text-lg mr-1 group-hover:-translate-x-1 transition-transform">arrow_back</span>
                    Volver al Carrito
                </a>
                <h1 class="text-2xl lg:text-3xl font-black text-agro-dark flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-3xl">shopping_cart_checkout</span>
                    Finalizar Compra
                </h1>
            </div>
            
            {{-- Steps rápido (visual) --}}
            <div class="hidden sm:flex items-center gap-2 text-sm font-bold text-gray-400 bg-white py-2 px-4 rounded-full shadow-sm">
                <span class="text-primary flex items-center gap-1"><span class="material-symbols-outlined text-base">shopping_cart</span> Carrito</span>
                <span class="material-symbols-outlined text-base">chevron_right</span>
                <span class="text-agro-dark flex items-center gap-1 bg-gray-100 px-2 py-0.5 rounded-full"><span class="material-symbols-outlined text-base">local_shipping</span> Envío y Pago</span>
                <span class="material-symbols-outlined text-base">chevron_right</span>
                <span>Confirmación</span>
            </div>
        </div>

        <form action="{{ route('checkout.store') }}" method="POST" enctype="multipart/form-data" id="checkout-form">
            @csrf
            
            {{-- LAYOUT PRINCIPAL GRID --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
                
                {{-- COLUMNA IZQUIERDA (Envío y Direcciones) --}}
                <div class="lg:col-span-7 space-y-6">
                    
                    {{-- SECCIÓN 1: Método de Entrega --}}
                    <section class="bg-white p-5 lg:p-6 rounded-3xl shadow-sm border border-gray-100">
                        <h2 class="text-lg font-black text-agro-dark mb-4 flex items-center gap-2 uppercase tracking-wide">
                            <span class="bg-blue-50 text-blue-600 p-1.5 rounded-lg material-symbols-outlined text-[20px]">local_shipping</span>
                            1. ¿Cómo lo quieres recibir?
                        </h2>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="metodo_entrega" value="delivery" class="peer sr-only" checked onchange="toggleSeccionDirecciones(true)">
                                <div class="p-4 rounded-2xl border-2 border-gray-100 bg-gray-50/50 peer-checked:border-blue-500 peer-checked:bg-blue-50/30 hover:bg-gray-50 transition-all h-full flex items-center gap-4">
                                    <div class="bg-white p-2 rounded-full shadow-sm text-gray-400 peer-checked:text-blue-500 peer-checked:shadow-md transition-all">
                                        <span class="material-symbols-outlined text-2xl">delivery_dining</span>
                                    </div>
                                    <div>
                                        <span class="font-bold text-gray-800 block peer-checked:text-blue-700">Delivery / Envío</span>
                                        <p class="text-xs text-gray-500 font-medium">Lo llevamos a tu ubicación.</p>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="metodo_entrega" value="pickup" class="peer sr-only" onchange="toggleSeccionDirecciones(false)">
                                <div class="p-4 rounded-2xl border-2 border-gray-100 bg-gray-50/50 peer-checked:border-blue-500 peer-checked:bg-blue-50/30 hover:bg-gray-50 transition-all h-full flex items-center gap-4">
                                    <div class="bg-white p-2 rounded-full shadow-sm text-gray-400 peer-checked:text-blue-500 peer-checked:shadow-md transition-all">
                                        <span class="material-symbols-outlined text-2xl">storefront</span>
                                    </div>
                                    <div>
                                        <span class="font-bold text-gray-800 block peer-checked:text-blue-700">Retiro en Tienda</span>
                                        <p class="text-xs text-gray-500 font-medium">Pasa a buscarlo tú mismo.</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </section>

                    {{-- SECCIÓN 2: Dirección de Envío (Condicional) --}}
                    <section id="seccion-direcciones" class="bg-white p-5 lg:p-6 rounded-3xl shadow-sm border border-gray-100 transition-all duration-300">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-black text-agro-dark flex items-center gap-2 uppercase tracking-wide">
                                <span class="bg-orange-50 text-orange-600 p-1.5 rounded-lg material-symbols-outlined text-[20px]">location_on</span>
                                2. Dirección de Entrega
                            </h2>
                            <a href="{{ route('perfil') }}#direcciones" target="_blank" class="text-xs font-bold text-primary hover:underline flex items-center gap-1 bg-primary/5 px-2 py-1 rounded-lg">
                                <span class="material-symbols-outlined text-sm">add_location_alt</span> Nueva
                            </a>
                        </div>

                        @if($direcciones->count() > 0)
                            <div class="grid grid-cols-1 gap-3">
                                {{-- Mostramos solo las primeras 2 direcciones para ahorrar espacio --}}
                                @foreach($direcciones->sortByDesc('es_principal')->take(2) as $dir)
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="direccion_id" value="{{ $dir->id }}" class="peer sr-only" {{ $dir->es_principal ? 'checked' : '' }}>
                                        <div class="p-3 rounded-xl border border-gray-200 bg-white peer-checked:border-orange-500 peer-checked:bg-orange-50/30 peer-checked:shadow-sm hover:border-gray-300 transition-all flex items-start gap-3">
                                            <div class="mt-0.5 text-gray-300 peer-checked:text-orange-500">
                                                <span class="material-symbols-outlined text-2xl">radio_button_checked</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-0.5">
                                                    <span class="font-bold text-gray-800 text-sm">{{ $dir->alias }}</span>
                                                    @if($dir->es_principal)
                                                        <span class="bg-orange-100 text-orange-700 text-[10px] font-bold px-1.5 py-0.5 rounded-md">Principal</span>
                                                    @endif
                                                </div>
                                                <p class="text-xs text-gray-600 truncate">{{ Str::limit($dir->direccion_texto, 80) }}</p>
                                                @if($dir->referencia_punto)
                                                    <p class="text-[10px] text-gray-400 italic mt-0.5 truncate">Ref: {{ $dir->referencia_punto }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            @if($direcciones->count() > 2)
                                <button type="button" onclick="toggleModalDirecciones()" class="mt-3 w-full py-2 text-xs font-bold text-gray-600 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors flex items-center justify-center gap-1">
                                    <span class="material-symbols-outlined text-sm">list</span>
                                    Ver mis otras {{ $direcciones->count() - 2 }} direcciones
                                </button>
                            @endif
                        @else
                            <div class="text-center p-4 bg-red-50 rounded-xl border border-red-100">
                                <span class="material-symbols-outlined text-red-400 text-3xl mb-2">no_encryption</span>
                                <p class="text-red-700 font-bold text-sm">¡Ups! No tienes direcciones.</p>
                                <p class="text-red-600 text-xs mb-3">Debes registrar al menos una para envíos.</p>
                                <a href="{{ route('perfil') }}#direcciones" class="inline-block px-4 py-2 bg-red-100 text-red-700 font-bold text-xs rounded-lg hover:bg-red-200 transition">
                                    Ir a crear dirección
                                </a>
                            </div>
                        @endif
                        @error('direccion_id') <span class="text-xs text-red-500 font-bold flex items-center gap-1 mt-2"><span class="material-symbols-outlined text-sm">error</span> {{ $message }}</span> @enderror
                    </section>

                    {{-- SECCIÓN 4: Observaciones --}}
                    <section class="bg-white p-5 lg:p-6 rounded-3xl shadow-sm border border-gray-100">
                         <h2 class="text-lg font-black text-agro-dark mb-4 flex items-center gap-2 uppercase tracking-wide">
                            <span class="bg-gray-100 text-gray-600 p-1.5 rounded-lg material-symbols-outlined text-[20px]">comment</span>
                            Notas del Pedido
                        </h2>
                        <textarea name="observaciones" rows="2" class="w-full rounded-xl border-gray-200 bg-gray-50 focus:border-primary focus:ring-primary/20 text-sm transition-all placeholder:text-gray-400" placeholder="Ej: Tocar timbre, dejar con el vigilante... (Opcional)"></textarea>
                    </section>

                </div>

                {{-- COLUMNA DERECHA (Pagos y Resumen - STICKY) --}}
                <div class="lg:col-span-5 space-y-6 lg:sticky lg:top-6">
                    
                    {{-- SECCIÓN 3: Métodos de Pago --}}
                    <section class="bg-white p-5 lg:p-6 rounded-3xl shadow-sm border border-gray-100">
                        <h2 class="text-lg font-black text-agro-dark mb-4 flex items-center gap-2 uppercase tracking-wide">
                            <span class="bg-green-50 text-green-600 p-1.5 rounded-lg material-symbols-outlined text-[20px]">payments</span>
                            3. Forma de Pago
                        </h2>

                        {{-- Grid de Tarjetas de Pago --}}
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mb-4">
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
                                        'pago_movil', 'transferencia' => 'text-blue-600 bg-blue-50',
                                        'zelle' => 'text-[#5800FD] bg-[#5800FD]/10',
                                        'efectivo_usd', 'efectivo_bs' => 'text-green-600 bg-green-50',
                                        'binance' => 'text-[#F3BA2F] bg-[#F3BA2F]/10',
                                        default => 'text-gray-600 bg-gray-100'
                                    };
                                @endphp

                                <label class="relative cursor-pointer payment-card-radio">
                                    <input type="radio" name="metodo_pago_id" value="{{ $metodo->id }}" required 
                                           class="peer sr-only"
                                           onclick="mostrarDatosPago('{{ $metodo->tipo_metodo }}', `{{ $metodo->info }}`)">
                                    
                                    <div class="rounded-xl border-2 border-gray-100 bg-white p-3 flex flex-col items-center justify-center gap-2 text-center h-full hover:border-gray-300 transition-all relative overflow-hidden">
                                        
                                        {{-- Icono Check animado --}}
                                        <div class="check-icon absolute top-1 right-1 text-white bg-primary rounded-full p-0.5 opacity-0 transform scale-50 transition-all duration-300 peer-checked:opacity-100 peer-checked:scale-100">
                                            <span class="material-symbols-outlined text-[14px] font-bold">check</span>
                                        </div>

                                        {{-- LÓGICA DE ICONO ACTUALIZADA (PNG para Zelle/Binance) --}}
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $iconColor }} mb-1 overflow-hidden">
                                            @if($icon === 'zelle_svg')
                                                <img src="{{ asset('img/pagos/zelle.png') }}" alt="Zelle" class="w-full h-full object-contain p-1">
                                            @elseif($icon === 'binance_svg')
                                                <img src="{{ asset('img/pagos/binance.png') }}" alt="Binance" class="w-full h-full object-contain p-1">
                                            @else
                                                <span class="material-symbols-outlined text-2xl">{{ $icon }}</span>
                                            @endif
                                        </div>
                                        
                                        <span class="text-[10px] sm:text-xs font-bold text-gray-700 leading-tight group-hover:text-primary">{{ $metodo->nombre }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        @if($metodosPago->where('activo', 1)->count() > 6)
                            <button type="button" onclick="toggleModalPagos()" class="w-full py-2 text-xs font-bold text-gray-600 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors flex items-center justify-center gap-1">
                                <span class="material-symbols-outlined text-sm">apps</span>
                                Ver todos los métodos
                            </button>
                        @endif
                        @error('metodo_pago_id') <span class="text-xs text-red-500 font-bold flex items-center gap-1 mt-2"><span class="material-symbols-outlined text-sm">error</span> Selección requerida</span> @enderror

                        {{-- Área de Datos Bancarios (Aparece al seleccionar) --}}
                        <div id="info-pago-container" class="mt-4 p-4 bg-blue-50/50 rounded-xl border border-blue-100 hidden animate-fade-in-up relative">
                            <div class="absolute -top-2 left-6 w-4 h-4 bg-blue-50/50 border-t border-l border-blue-100 transform rotate-45"></div>
                            
                            <h4 class="text-xs font-black text-blue-800 uppercase tracking-wide mb-2 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">info</span> Datos para el pago:
                            </h4>
                            
                            <pre id="datos-bancarios-texto" class="text-xs sm:text-sm text-blue-900 whitespace-pre-wrap font-medium font-sans mb-4 bg-white/50 p-3 rounded-lg border border-blue-100/50"></pre>
                            
                            <div id="campos-referencia" class="grid grid-cols-1 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nro. Referencia / Comprobante *</label>
                                    <input type="text" name="referencia" id="input-referencia" class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary/20 text-sm py-2">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1 flex justify-between">
                                        <span>Adjuntar Capture (Opcional)</span>
                                        <span class="text-primary cursor-pointer hover:underline" onclick="document.getElementById('input-comprobante').click()">Examinar</span>
                                    </label>
                                    <input type="file" name="comprobante" id="input-comprobante" accept="image/*" class="hidden" onchange="mostrarNombreArchivo(this)">
                                    <div id="nombre-archivo-container" class="text-xs text-gray-500 italic bg-white border border-gray-200 rounded-xl p-2 truncate hidden"></div>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Resumen del Pedido (Pegajoso) --}}
                    <section class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="p-5 lg:p-6 bg-agro-dark text-white">
                            <h3 class="text-lg font-black flex items-center gap-2">
                                <span class="material-symbols-outlined">receipt_long</span>
                                Resumen del Pedido
                            </h3>
                        </div>

                        <div class="p-5 lg:p-6 space-y-4">
                            {{-- Items Miniatura (Scrollable si son muchos) --}}
                            <div class="space-y-3 max-h-48 overflow-y-auto custom-scrollbar pr-2">
                                @foreach($carrito as $item)
                                    <div class="flex gap-3 items-start">
                                        <div class="w-10 h-10 bg-gray-50 rounded-lg overflow-hidden border border-gray-100 flex-shrink-0">
                                            @if($item->producto->imagenes->first())
                                                <img src="{{ $item->producto->imagenes->first()->url_imagen }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-300"><span class="material-symbols-outlined text-sm">image</span></div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold text-gray-800 truncate">{{ $item->producto->nombre }}</p>
                                            <div class="flex justify-between items-center">
                                                <p class="text-[10px] text-gray-500">x{{ (int)$item->cantidad }}</p>
                                                <span class="text-xs font-bold text-gray-700">${{ number_format($item->producto->precio_venta_usd * $item->cantidad, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Totales --}}
                            <div class="border-t border-gray-100 pt-4 space-y-2">
                                <div class="flex justify-between text-xs text-gray-600">
                                    <span>Subtotal</span>
                                    <span class="font-medium">${{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-xs text-gray-600">
                                    <span>IVA (16%)</span>
                                    <span class="font-medium">${{ number_format($montoIva, 2) }}</span>
                                </div>
                                <div id="fila-delivery" class="flex justify-between text-xs text-primary font-bold hidden animate-fade-in-up">
                                    <span>Delivery Estimado (Urbano)</span>
                                    <span>+ ${{ number_format($zonas->first()->precio_delivery_usd ?? 0, 2) }}</span>
                                </div>
                                
                                <div class="flex justify-between text-base font-black text-agro-dark pt-3 border-t border-gray-100 mt-3">
                                    <span>Total a Pagar (USD)</span>
                                    <span id="total-usd-display">${{ number_format($totalUsd, 2) }}</span>
                                </div>

                                {{-- Conversión a VES --}}
                                <div class="bg-primary/5 rounded-xl p-3 mt-4 border border-primary/10">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-[10px] font-bold text-primary/70 uppercase flex items-center gap-1">
                                            <span class="material-symbols-outlined text-xs">currency_exchange</span> Tasa BCV/Monitor
                                        </span>
                                        <span class="text-[10px] font-bold text-primary/70">{{ number_format($tasaValor, 2) }} Bs/$</span>
                                    </div>
                                    <div class="flex justify-between items-center text-xl font-black text-primary">
                                        <span>Total en Bolívares</span>
                                        <span id="total-ves-display">{{ number_format($totalVes, 2) }} Bs</span>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" id="btn-confirmar" class="w-full mt-4 py-3.5 bg-agro-dark text-white font-bold rounded-xl hover:bg-primary shadow-lg shadow-agro-dark/20 hover:shadow-primary/30 transition-all flex items-center justify-center gap-2 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                <span class="material-symbols-outlined">check_circle</span>
                                Confirmar Pedido
                            </button>
                            
                            <p class="text-[10px] text-center text-gray-400 mt-3 leading-tight flex justify-center items-center gap-1">
                                <span class="material-symbols-outlined text-xs">secure</span>
                                Al confirmar, aceptas los términos y condiciones.
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
<div id="modal-otras-direcciones" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="modal-dir-backdrop"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95 flex flex-col max-h-[80vh]" id="modal-dir-panel">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 flex-shrink-0 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold leading-6 text-gray-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-orange-500">list_alt</span> Mis Direcciones
                        </h3>
                        <button type="button" onclick="toggleModalDirecciones()" class="text-gray-400 hover:text-gray-500"><span class="material-symbols-outlined">close</span></button>
                    </div>
                </div>
                <div class="px-4 py-4 sm:p-6 overflow-y-auto flex-1 custom-scrollbar">
                    <div class="space-y-3">
                        @foreach($direcciones->sortByDesc('es_principal') as $dir)
                            <label class="relative cursor-pointer group block">
                                <input type="radio" name="direccion_id_modal" value="{{ $dir->id }}" class="peer sr-only" 
                                       {{ $dir->es_principal ? 'checked' : '' }}
                                       onchange="seleccionarDireccionDesdeModal({{ $dir->id }})">
                                <div class="p-3 rounded-xl border border-gray-200 bg-white peer-checked:border-orange-500 peer-checked:bg-orange-50/30 peer-checked:shadow-sm hover:border-gray-300 transition-all flex items-start gap-3">
                                    <div class="mt-0.5 text-gray-300 peer-checked:text-orange-500">
                                        <span class="material-symbols-outlined text-2xl">radio_button_checked</span>
                                    </div>
                                    <div class="flex-1 min-w-0 text-left">
                                        <div class="flex items-center gap-2 mb-0.5">
                                            <span class="font-bold text-gray-800 text-sm">{{ $dir->alias }}</span>
                                            @if($dir->es_principal)
                                                <span class="bg-orange-100 text-orange-700 text-[10px] font-bold px-1.5 py-0.5 rounded-md">Principal</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-600 truncate">{{ $dir->direccion_texto }}</p>
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
<div id="modal-todos-pagos" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="modal-pagos-backdrop"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95 flex flex-col max-h-[80vh]" id="modal-pagos-panel">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 flex-shrink-0 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold leading-6 text-gray-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-green-500">payments</span> Todos los Métodos de Pago
                        </h3>
                        <button type="button" onclick="toggleModalPagos()" class="text-gray-400 hover:text-gray-500"><span class="material-symbols-outlined">close</span></button>
                    </div>
                </div>
                <div class="px-4 py-4 sm:p-6 overflow-y-auto flex-1 custom-scrollbar">
                     <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($metodosPago->where('activo', 1) as $metodo)
                            @php
                                $icon = match($metodo->tipo_metodo) {
                                    'pago_movil' => 'phone_iphone', 'zelle' => 'zelle_svg', 'transferencia' => 'account_balance',
                                    'efectivo_usd', 'efectivo_bs' => 'attach_money', 'binance' => 'binance_svg', 'punto_venta', 'biopago' => 'point_of_sale',
                                    default => 'payments'
                                };
                                $iconColor = match($metodo->tipo_metodo) {
                                    'pago_movil', 'transferencia' => 'text-blue-600 bg-blue-50', 'zelle' => 'text-[#5800FD] bg-[#5800FD]/10',
                                    'efectivo_usd', 'efectivo_bs' => 'text-green-600 bg-green-50', 'binance' => 'text-[#F3BA2F] bg-[#F3BA2F]/10',
                                    default => 'text-gray-600 bg-gray-100'
                                };
                            @endphp
                             <label class="relative cursor-pointer payment-card-radio block">
                                <input type="radio" name="metodo_pago_id_modal" value="{{ $metodo->id }}" class="peer sr-only"
                                       onclick="seleccionarPagoDesdeModal('{{ $metodo->id }}', '{{ $metodo->tipo_metodo }}', `{{ $metodo->info }}`)">
                                <div class="rounded-xl border-2 border-gray-100 bg-white p-3 flex flex-col items-center justify-center gap-2 text-center h-full hover:border-gray-300 transition-all relative overflow-hidden">
                                    <div class="check-icon absolute top-1 right-1 text-white bg-primary rounded-full p-0.5 opacity-0 transform scale-50 transition-all duration-300 peer-checked:opacity-100 peer-checked:scale-100"><span class="material-symbols-outlined text-[14px] font-bold">check</span></div>
                                    
                                    {{-- LÓGICA DE ICONO ACTUALIZADA (PNG para Zelle/Binance) --}}
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $iconColor }} mb-1 overflow-hidden">
                                        @if($icon === 'zelle_svg')
                                            <img src="{{ asset('img/pagos/zelle.png') }}" alt="Zelle" class="w-full h-full object-contain p-1">
                                        @elseif($icon === 'binance_svg')
                                            <img src="{{ asset('img/pagos/binance.png') }}" alt="Binance" class="w-full h-full object-contain p-1">
                                        @else
                                            <span class="material-symbols-outlined text-2xl">{{ $icon }}</span>
                                        @endif
                                    </div>
                                    
                                    <span class="text-[10px] sm:text-xs font-bold text-gray-700 leading-tight group-hover:text-primary">{{ $metodo->nombre }}</span>
                                </div>
                            </label>
                        @endforeach
                     </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Variables JS para cálculos --}}
<script>
    const subtotalBase = {{ $subtotal + $montoIva }};
    const tasaCambio = {{ $tasaValor }};
    const precioDelivery = {{ $zonas->first()->precio_delivery_usd ?? 0 }};
</script>

<script>
    // --- Lógica de UI ---
    function toggleSeccionDirecciones(mostrar) {
        const div = document.getElementById('seccion-direcciones');
        const inputs = div.querySelectorAll('input[type="radio"][name="direccion_id"]');
        const filaDelivery = document.getElementById('fila-delivery');
        const totalUsdDisplay = document.getElementById('total-usd-display');
        const totalVesDisplay = document.getElementById('total-ves-display');
        
        let nuevoTotalUsd = subtotalBase;

        if(mostrar) {
            div.classList.remove('opacity-50', 'pointer-events-none', 'grayscale');
            inputs.forEach(input => input.disabled = false);
            filaDelivery.classList.remove('hidden');
            nuevoTotalUsd += precioDelivery;
        } else {
            div.classList.add('opacity-50', 'pointer-events-none', 'grayscale');
            inputs.forEach(input => { input.disabled = true; input.checked = false; });
            filaDelivery.classList.add('hidden');
        }

        totalUsdDisplay.innerText = '$' + nuevoTotalUsd.toFixed(2);
        totalVesDisplay.innerText = (nuevoTotalUsd * tasaCambio).toFixed(2) + ' Bs';
    }

    function mostrarDatosPago(tipo, info) {
        const container = document.getElementById('info-pago-container');
        const texto = document.getElementById('datos-bancarios-texto');
        const camposRef = document.getElementById('campos-referencia');
        const inputRef = document.getElementById('input-referencia');
        
        container.classList.remove('hidden');
        texto.innerText = info;
        
        // Ocultar campos de referencia para efectivo o punto
        if(['efectivo_usd', 'efectivo_bs', 'punto_venta', 'biopago'].includes(tipo)) {
            camposRef.classList.add('hidden');
            inputRef.required = false;
        } else {
            camposRef.classList.remove('hidden');
            inputRef.required = true;
        }
    }

    function mostrarNombreArchivo(input) {
        const container = document.getElementById('nombre-archivo-container');
        if(input.files && input.files[0]) {
            container.innerText = input.files[0].name;
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    }

    // --- Lógica de Modales ---
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        const backdrop = modal.querySelector('div[id$="-backdrop"]');
        const panel = modal.querySelector('div[id$="-panel"]');

        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                backdrop.classList.remove('opacity-0');
                panel.classList.remove('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
            }, 10);
        } else {
            backdrop.classList.add('opacity-0');
            panel.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }
    }
    const toggleModalDirecciones = () => toggleModal('modal-otras-direcciones');
    const toggleModalPagos = () => toggleModal('modal-todos-pagos');

    // --- Sincronización Modales -> Formulario Principal ---

    function seleccionarDireccionDesdeModal(id) {
        let mainInput = document.querySelector(`input[name="direccion_id"][value="${id}"]`);
        if (!mainInput) {
             const form = document.getElementById('checkout-form');
             let hiddenInput = document.getElementById('hidden-dir-input');
             if(!hiddenInput) {
                 hiddenInput = document.createElement('input');
                 hiddenInput.type = 'hidden';
                 hiddenInput.name = 'direccion_id';
                 hiddenInput.id = 'hidden-dir-input';
                 form.appendChild(hiddenInput);
             }
             hiddenInput.value = id;
             document.querySelectorAll('input[name="direccion_id"]').forEach(i => i.checked = false);
        } else {
            mainInput.checked = true;
            const hiddenInput = document.getElementById('hidden-dir-input');
            if(hiddenInput) hiddenInput.remove();
        }
        toggleModalDirecciones();
    }

    function seleccionarPagoDesdeModal(id, tipo, info) {
        let mainInput = document.querySelector(`input[name="metodo_pago_id"][value="${id}"]`);
        
        if (!mainInput) {
            document.querySelectorAll('input[name="metodo_pago_id"]').forEach(i => i.checked = false);

            const form = document.getElementById('checkout-form');
            let hiddenInput = document.getElementById('hidden-pago-input');
            if(!hiddenInput) {
                hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'metodo_pago_id';
                hiddenInput.id = 'hidden-pago-input';
                form.appendChild(hiddenInput);
            }
            hiddenInput.value = id;
            mostrarDatosPago(tipo, info);
        } else {
             mainInput.click(); 
             const hiddenInput = document.getElementById('hidden-pago-input');
             if(hiddenInput) hiddenInput.remove();
        }
        toggleModalPagos();
    }

    document.addEventListener("DOMContentLoaded", () => {
        const deliveryRadio = document.querySelector('input[name="metodo_entrega"][value="delivery"]');
        if(deliveryRadio && deliveryRadio.checked) {
            toggleSeccionDirecciones(true);
        } else {
            toggleSeccionDirecciones(false);
        }
    });
</script>
@endsection