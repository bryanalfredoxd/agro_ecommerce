@extends('layouts.app')

@section('title', 'Finalizar Compra - Corpo Agrícola')

@section('content')
<div class="bg-gray-50 min-h-screen py-8 lg:py-12 animate-fade-in-up font-sans">
    <div class="layout-container px-4 sm:px-0">
        
        {{-- HEADER DEL CHECKOUT --}}
        <div class="mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('carrito.index') }}" class="inline-flex items-center text-gray-500 hover:text-primary transition-colors font-bold text-sm mb-2 group">
                    <span class="material-symbols-outlined text-[18px] mr-1 group-hover:-translate-x-1 transition-transform">arrow_back</span>
                    Volver al Carrito
                </a>
                <h1 class="text-3xl lg:text-4xl font-black text-agro-dark flex items-center gap-3 tracking-tight">
                    <span class="material-symbols-outlined text-primary text-[36px]">shopping_cart_checkout</span>
                    Finalizar Compra
                </h1>
            </div>
            
            {{-- Breadcrumbs / Steps Visual --}}
            <div class="hidden sm:flex items-center gap-3 text-xs font-bold text-gray-400 bg-white py-2.5 px-5 rounded-xl shadow-sm border border-gray-100">
                <span class="text-primary flex items-center gap-1.5"><span class="material-symbols-outlined text-[18px]">shopping_cart</span> Carrito</span>
                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                <span class="text-agro-dark bg-gray-50 px-3 py-1.5 rounded-lg flex items-center gap-1.5 border border-gray-200"><span class="material-symbols-outlined text-[18px]">local_shipping</span> Envío y Pago</span>
                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[18px]">verified</span> Confirmación</span>
            </div>
        </div>

        <form action="{{ route('checkout.store') }}" method="POST" enctype="multipart/form-data" id="checkout-form">
            @csrf
            
            {{-- LAYOUT PRINCIPAL GRID --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                {{-- COLUMNA IZQUIERDA (Proceso de Compra Completo) --}}
                <div class="lg:col-span-7 xl:col-span-8 space-y-6">
                    
                    {{-- SECCIÓN 1: Método de Entrega --}}
                    <section class="bg-white p-6 lg:p-8 rounded-3xl shadow-sm border border-gray-100">
                        <h2 class="text-lg font-black text-agro-dark mb-5 flex items-center gap-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 font-black text-sm">1</span>
                            ¿Cómo deseas recibir tu pedido?
                        </h2>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="metodo_entrega" value="delivery" class="peer sr-only" checked onchange="window.CheckoutConfig.toggleDirecciones(true)">
                                <div class="p-4 rounded-2xl border-2 border-gray-100 bg-white peer-checked:border-primary peer-checked:bg-primary/5 hover:border-gray-200 transition-all h-full flex items-center gap-4">
                                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 peer-checked:text-primary peer-checked:bg-white peer-checked:shadow-sm transition-all flex-shrink-0">
                                        <span class="material-symbols-outlined text-[28px]">local_shipping</span>
                                    </div>
                                    <div>
                                        <span class="font-bold text-agro-dark block">Delivery / Envío</span>
                                        <p class="text-xs text-gray-500 font-medium leading-tight mt-0.5">Lo llevamos a tu dirección registrada.</p>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="metodo_entrega" value="pickup" class="peer sr-only" onchange="window.CheckoutConfig.toggleDirecciones(false)">
                                <div class="p-4 rounded-2xl border-2 border-gray-100 bg-white peer-checked:border-primary peer-checked:bg-primary/5 hover:border-gray-200 transition-all h-full flex items-center gap-4">
                                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 peer-checked:text-primary peer-checked:bg-white peer-checked:shadow-sm transition-all flex-shrink-0">
                                        <span class="material-symbols-outlined text-[28px]">storefront</span>
                                    </div>
                                    <div>
                                        <span class="font-bold text-agro-dark block">Retiro en Tienda</span>
                                        <p class="text-xs text-gray-500 font-medium leading-tight mt-0.5">Pasa a buscarlo a nuestra sucursal.</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </section>

                    {{-- SECCIÓN 2: Dirección de Envío --}}
                    <section id="seccion-direcciones" class="bg-white p-6 lg:p-8 rounded-3xl shadow-sm border border-gray-100 transition-all duration-300">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-5 gap-3">
                            <h2 class="text-lg font-black text-agro-dark flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-orange-50 text-orange-600 font-black text-sm">2</span>
                                Dirección de Entrega
                            </h2>
                            <a href="{{ route('perfil') }}#direcciones" target="_blank" class="text-xs font-bold text-orange-600 hover:text-white hover:bg-orange-500 bg-orange-50 px-3 py-2 rounded-xl transition-colors flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px]">add_location_alt</span> Agregar Nueva
                            </a>
                        </div>

                        @if($direcciones->count() > 0)
                            <div class="grid grid-cols-1 gap-3">
                                @foreach($direcciones->sortByDesc('es_principal')->take(2) as $dir)
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="direccion_id" value="{{ $dir->id }}" class="peer sr-only" {{ $dir->es_principal ? 'checked' : '' }}>
                                        <div class="p-4 rounded-2xl border-2 border-gray-100 bg-white peer-checked:border-orange-500 peer-checked:bg-orange-50/20 hover:border-gray-200 transition-all flex items-start gap-4">
                                            <div class="mt-0.5 text-gray-300 peer-checked:text-orange-500 transition-colors">
                                                <span class="material-symbols-outlined text-[24px]">radio_button_checked</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="font-bold text-agro-dark">{{ $dir->alias }}</span>
                                                    @if($dir->es_principal)
                                                        <span class="bg-orange-100 text-orange-700 text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md">Principal</span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-gray-600 leading-snug">{{ $dir->direccion_texto }}</p>
                                                @if($dir->referencia_punto)
                                                    <p class="text-[11px] font-medium text-gray-400 mt-1.5 flex items-start gap-1">
                                                        <span class="material-symbols-outlined text-[14px]">info</span> Ref: {{ $dir->referencia_punto }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            @if($direcciones->count() > 2)
                                <button type="button" onclick="window.CheckoutConfig.toggleModal('modal-otras-direcciones')" class="mt-4 w-full py-3 text-sm font-bold text-gray-600 bg-gray-50 rounded-xl hover:bg-gray-100 hover:text-agro-dark transition-colors flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-[20px]">list</span>
                                    Ver mis otras {{ $direcciones->count() - 2 }} direcciones
                                </button>
                            @endif
                        @else
                            <div class="text-center p-8 bg-red-50 rounded-2xl border border-red-100 border-dashed">
                                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm">
                                    <span class="material-symbols-outlined text-red-500 text-[32px]">wrong_location</span>
                                </div>
                                <p class="text-red-700 font-bold text-base mb-1">¡No tienes direcciones registradas!</p>
                                <p class="text-red-600/80 text-sm mb-5">Debes agregar al menos una para procesar envíos.</p>
                                <a href="{{ route('perfil') }}#direcciones" class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 text-white font-bold text-sm rounded-xl hover:bg-red-700 transition-colors shadow-sm shadow-red-200">
                                    Ir a mi perfil
                                </a>
                            </div>
                        @endif
                        @error('direccion_id') <span class="text-xs text-red-500 font-bold flex items-center gap-1 mt-3"><span class="material-symbols-outlined text-sm">error</span> {{ $message }}</span> @enderror
                    </section>

                    {{-- SECCIÓN 3: Métodos de Pago (AHORA EN LA IZQUIERDA) --}}
                    <section class="bg-white p-6 lg:p-8 rounded-3xl shadow-sm border border-gray-100">
                        <h2 class="text-lg font-black text-agro-dark mb-5 flex items-center gap-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-green-50 text-green-600 font-black text-sm">3</span>
                            Forma de Pago
                        </h2>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($metodosPago->where('activo', 1)->take(6) as $metodo)
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

                                <label class="relative cursor-pointer group payment-card-radio h-full">
                                    <input type="radio" name="metodo_pago_id" value="{{ $metodo->id }}" required 
                                           class="peer sr-only"
                                           onclick="window.CheckoutConfig.mostrarDatosPago('{{ $metodo->tipo_metodo }}', `{{ $metodo->info }}`)">
                                    
                                    <div class="rounded-2xl border-2 border-gray-100 bg-white p-4 flex flex-col items-center justify-center gap-2 text-center h-full hover:border-gray-200 transition-all relative overflow-hidden peer-checked:border-primary peer-checked:bg-primary/5">
                                        
                                        <div class="absolute top-2 right-2 text-white bg-primary rounded-full p-0.5 opacity-0 transform scale-50 transition-all duration-300 peer-checked:opacity-100 peer-checked:scale-100">
                                            <span class="material-symbols-outlined text-[14px] font-bold">check</span>
                                        </div>

                                        <div class="w-12 h-12 rounded-full flex items-center justify-center {{ $iconColor }} mb-1 overflow-hidden transition-transform group-hover:scale-110">
                                            @if($icon === 'zelle_svg')
                                                <img src="{{ asset('img/pagos/zelle.png') }}" alt="Zelle" class="w-full h-full object-contain p-2">
                                            @elseif($icon === 'binance_svg')
                                                <img src="{{ asset('img/pagos/binance.png') }}" alt="Binance" class="w-full h-full object-contain p-2">
                                            @else
                                                <span class="material-symbols-outlined text-[28px]">{{ $icon }}</span>
                                            @endif
                                        </div>
                                        
                                        <span class="text-xs font-bold text-agro-dark leading-tight">{{ $metodo->nombre }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        @if($metodosPago->where('activo', 1)->count() > 6)
                            <button type="button" onclick="window.CheckoutConfig.toggleModal('modal-todos-pagos')" class="mt-4 w-full py-3 text-sm font-bold text-gray-600 bg-gray-50 rounded-xl hover:bg-gray-100 hover:text-agro-dark transition-colors flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-[20px]">apps</span>
                                Ver todos los métodos de pago
                            </button>
                        @endif
                        @error('metodo_pago_id') <span class="text-xs text-red-500 font-bold flex items-center gap-1 mt-3"><span class="material-symbols-outlined text-sm">error</span> Selección requerida</span> @enderror

                        {{-- Área de Datos Bancarios (Estilo Recibo) --}}
                        <div id="info-pago-container" class="mt-6 p-5 bg-gray-50 rounded-2xl border border-gray-200 hidden animate-fade-in-up">
                            
                            <h4 class="text-sm font-black text-agro-dark mb-3 flex items-center gap-2">
                                <span class="material-symbols-outlined text-[20px] text-primary">account_balance</span> 
                                Instrucciones de Pago:
                            </h4>
                            
                            <pre id="datos-bancarios-texto" class="text-sm text-gray-600 whitespace-pre-wrap font-medium font-sans mb-5 bg-white p-4 rounded-xl border border-gray-100 shadow-sm"></pre>
                            
                            <div id="campos-referencia" class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nro. Referencia *</label>
                                    <input type="text" name="referencia" id="input-referencia" class="w-full h-11 rounded-xl border-gray-200 bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 text-sm px-4 shadow-sm" placeholder="Ej: 12345678">
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Comprobante (Opcional)</label>
                                    <div class="relative w-full h-11 bg-white border border-gray-200 rounded-xl shadow-sm flex items-center overflow-hidden hover:border-primary hover:bg-primary/5 cursor-pointer transition-colors" onclick="document.getElementById('input-comprobante').click()">
                                        <div class="px-4 border-r border-gray-100 text-gray-400 bg-gray-50 h-full flex items-center">
                                            <span class="material-symbols-outlined text-[20px]">upload_file</span>
                                        </div>
                                        <div id="nombre-archivo-container" class="px-4 text-sm text-gray-500 truncate flex-1 font-medium">Adjuntar imagen...</div>
                                        <input type="file" name="comprobante" id="input-comprobante" accept="image/*" class="hidden" onchange="window.CheckoutConfig.mostrarNombreArchivo(this)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- SECCIÓN 4: Notas del Pedido --}}
                    <section class="bg-white p-6 lg:p-8 rounded-3xl shadow-sm border border-gray-100">
                         <h2 class="text-lg font-black text-agro-dark mb-4 flex items-center gap-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-600 font-black text-sm">4</span>
                            Notas adicionales (Opcional)
                        </h2>
                        <textarea name="observaciones" rows="3" class="w-full rounded-2xl border-gray-200 bg-gray-50 focus:border-primary focus:ring-2 focus:ring-primary/20 text-sm p-4 transition-all placeholder:text-gray-400 resize-none shadow-inner" placeholder="Ej: Dejar con el vigilante en recepción, enviar el día martes por la mañana..."></textarea>
                    </section>

                </div>

                {{-- COLUMNA DERECHA (Resumen del Pedido - STICKY) --}}
                <div class="lg:col-span-5 xl:col-span-4">
                    <section class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-gray-100 overflow-hidden sticky top-24">
                        
                        <div class="p-6 bg-agro-dark text-white relative overflow-hidden">
                            <div class="absolute -top-10 -right-10 w-32 h-32 bg-primary/20 rounded-full blur-2xl"></div>
                            <h3 class="text-xl font-black flex items-center gap-2 relative z-10">
                                <span class="material-symbols-outlined">receipt_long</span>
                                Resumen de Compra
                            </h3>
                        </div>

                        <div class="p-6">
                            {{-- Mini Items del Carrito --}}
                            <div class="space-y-4 max-h-56 overflow-y-auto custom-scrollbar pr-2 mb-6">
                                @foreach($carrito as $item)
                                    <div class="flex gap-4 items-center">
                                        <div class="w-12 h-12 bg-gray-50 rounded-xl overflow-hidden border border-gray-100 flex-shrink-0">
                                            @if($item->producto && $item->producto->imagen_url)
                                                <img src="{{ asset($item->producto->imagen_url) }}" 
                                                    alt="{{ $item->producto->nombre ?? 'Producto' }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                                                    <span class="material-symbols-outlined text-[20px] mb-1">image</span>
                                                    <span class="text-[8px] font-bold uppercase tracking-widest">Sin Imagen</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-agro-dark truncate">{{ $item->producto->nombre }}</p>
                                            <div class="flex justify-between items-center mt-0.5">
                                                <span class="text-xs font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-md">Cant: {{ (int)$item->cantidad }}</span>
                                                <span class="text-sm font-black text-primary">${{ number_format($item->producto->precio_venta_usd * $item->cantidad, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Desglose de Totales --}}
                            <div class="border-t border-dashed border-gray-200 pt-5 space-y-3">
                                <div class="flex justify-between text-sm text-gray-500 font-medium">
                                    <span>Subtotal</span>
                                    <span class="text-gray-800 font-bold">${{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm text-gray-500 font-medium">
                                    <span>Impuestos (IVA 16%)</span>
                                    <span class="text-gray-800 font-bold">${{ number_format($montoIva, 2) }}</span>
                                </div>
                                <div id="fila-delivery" class="flex justify-between text-sm text-blue-600 font-bold transition-all">
                                    <span>Delivery Urbano</span>
                                    <span>+ ${{ number_format($zonas->first()->precio_delivery_usd ?? 0, 2) }}</span>
                                </div>
                                
                                <div class="flex justify-between items-end pt-4 border-t border-gray-100 mt-2">
                                    <span class="text-base font-black text-agro-dark">Total a Pagar</span>
                                    <div class="text-right">
                                        <span id="total-usd-display" class="block text-3xl font-black text-agro-dark leading-none">${{ number_format($totalUsd, 2) }}</span>
                                        <span class="block text-[11px] text-gray-400 font-bold mt-1">USD</span>
                                    </div>
                                </div>

                                {{-- Conversión VES --}}
                                <div class="bg-primary/10 rounded-xl p-4 mt-4 border border-primary/20 flex justify-between items-center">
                                    <div>
                                        <span class="text-[10px] font-black text-agro-dark uppercase tracking-wider block mb-0.5">Equivalente VES</span>
                                        <span class="text-[10px] font-medium text-gray-500 flex items-center gap-1">
                                            Tasa BCV: {{ number_format($tasaValor, 2) }}
                                        </span>
                                    </div>
                                    <span id="total-ves-display" class="text-lg font-black text-agro-dark">{{ number_format($totalVes, 2) }} Bs</span>
                                </div>
                            </div>

                            {{-- BOTÓN GIGANTE --}}
                            <button type="submit" id="btn-confirmar" class="w-full mt-6 py-4 bg-primary text-agro-dark font-black text-base rounded-2xl hover:bg-green-500 shadow-lg shadow-primary/30 transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-2 group">
                                <span class="material-symbols-outlined text-[24px]">verified</span>
                                Procesar Pedido
                            </button>
                            
                            <p class="text-[10px] text-center text-gray-400 mt-4 leading-tight flex justify-center items-center gap-1 font-medium">   
                                <span class="material-symbols-outlined text-[14px]">lock</span> Tu compra es segura y encriptada.
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
<div id="modal-otras-direcciones" class="fixed inset-0 z-[120] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-agro-dark/60 backdrop-blur-sm transition-opacity opacity-0" id="modal-otras-direcciones-backdrop" onclick="window.CheckoutConfig.toggleModal('modal-otras-direcciones')"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-3xl bg-gray-50 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95 flex flex-col max-h-[85vh]" id="modal-otras-direcciones-panel">
                <div class="bg-white px-6 py-5 border-b border-gray-100 flex items-center justify-between sticky top-0 z-10">
                    <h3 class="text-lg font-black text-agro-dark flex items-center gap-2">
                        <span class="material-symbols-outlined text-orange-500">location_on</span> Mis Direcciones
                    </h3>
                    <button type="button" onclick="window.CheckoutConfig.toggleModal('modal-otras-direcciones')" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors"><span class="material-symbols-outlined text-[20px]">close</span></button>
                </div>
                <div class="p-6 overflow-y-auto flex-1 custom-scrollbar">
                    <div class="space-y-3">
                        @foreach($direcciones->sortByDesc('es_principal') as $dir)
                            <label class="relative cursor-pointer group block">
                                <input type="radio" name="direccion_id_modal" value="{{ $dir->id }}" class="peer sr-only" 
                                       {{ $dir->es_principal ? 'checked' : '' }}
                                       onchange="window.CheckoutConfig.seleccionarDireccionDesdeModal({{ $dir->id }})">
                                <div class="p-4 rounded-2xl border-2 border-gray-200 bg-white peer-checked:border-orange-500 peer-checked:bg-orange-50/30 hover:border-gray-300 transition-all flex items-start gap-4 shadow-sm">
                                    <div class="mt-0.5 text-gray-300 peer-checked:text-orange-500 transition-colors">
                                        <span class="material-symbols-outlined text-[24px]">radio_button_checked</span>
                                    </div>
                                    <div class="flex-1 min-w-0 text-left">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="font-bold text-agro-dark">{{ $dir->alias }}</span>
                                            @if($dir->es_principal)
                                                <span class="bg-orange-100 text-orange-700 text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md">Principal</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 leading-snug">{{ $dir->direccion_texto }}</p>
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
<div id="modal-todos-pagos" class="fixed inset-0 z-[120] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-agro-dark/60 backdrop-blur-sm transition-opacity opacity-0" id="modal-todos-pagos-backdrop" onclick="window.CheckoutConfig.toggleModal('modal-todos-pagos')"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-3xl bg-gray-50 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95 flex flex-col max-h-[85vh]" id="modal-todos-pagos-panel">
                <div class="bg-white px-6 py-5 border-b border-gray-100 flex items-center justify-between sticky top-0 z-10">
                    <h3 class="text-lg font-black text-agro-dark flex items-center gap-2">
                        <span class="material-symbols-outlined text-green-500">payments</span> Todos los Métodos
                    </h3>
                    <button type="button" onclick="window.CheckoutConfig.toggleModal('modal-todos-pagos')" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors"><span class="material-symbols-outlined text-[20px]">close</span></button>
                </div>
                <div class="p-6 overflow-y-auto flex-1 custom-scrollbar">
                     <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
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
                                       onclick="window.CheckoutConfig.seleccionarPagoDesdeModal('{{ $metodo->id }}', '{{ $metodo->tipo_metodo }}', `{{ $metodo->info }}`)">
                                <div class="rounded-2xl border-2 border-gray-200 bg-white p-4 flex flex-col items-center justify-center gap-2 text-center h-full hover:border-gray-300 transition-all relative overflow-hidden peer-checked:border-primary peer-checked:bg-primary/5 shadow-sm">
                                    <div class="absolute top-2 right-2 text-white bg-primary rounded-full p-0.5 opacity-0 transform scale-50 transition-all duration-300 peer-checked:opacity-100 peer-checked:scale-100"><span class="material-symbols-outlined text-[14px] font-bold">check</span></div>
                                    
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center {{ $iconColor }} mb-1 overflow-hidden">
                                        @if($icon === 'zelle_svg') <img src="{{ asset('img/pagos/zelle.png') }}" class="w-full h-full object-contain p-2">
                                        @elseif($icon === 'binance_svg') <img src="{{ asset('img/pagos/binance.png') }}" class="w-full h-full object-contain p-2">
                                        @else <span class="material-symbols-outlined text-[28px]">{{ $icon }}</span> @endif
                                    </div>
                                    <span class="text-xs font-bold text-agro-dark leading-tight">{{ $metodo->nombre }}</span>
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
{{-- Script dedicado para el comportamiento del Checkout --}}
<script src="{{ asset('js/checkout.js') }}"></script>
<script>
    // Inyectamos las variables de servidor al objeto global ANTES de que el archivo externo las lea
    window.CheckoutVars = {
        subtotalBase: {{ $subtotal + $montoIva }},
        tasaCambio: {{ $tasaValor }},
        precioDelivery: {{ $zonas->first()->precio_delivery_usd ?? 0 }}
    };
</script>
@endpush