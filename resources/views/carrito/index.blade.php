@extends('layouts.app')

@section('title', 'Mi Carrito de Compras - Corpo Agrícola')

@section('content')
<div class="bg-gray-50 min-h-screen pb-20 pt-8 font-sans">
    
    <div class="layout-container px-4 sm:px-0">
        
        {{-- CABECERA DEL CARRITO --}}
        <div class="flex items-center justify-between mb-8 sm:mb-10">
            <div>
                <h1 class="text-2xl sm:text-4xl font-black text-agro-dark tracking-tight flex items-center gap-3">
                    <span class="material-symbols-outlined text-[32px] sm:text-[40px] text-primary">shopping_bag</span>
                    Tu Carrito
                </h1>
                @if($items->count() > 0)
                    <p id="cart-items-count-text" class="text-gray-500 text-sm mt-2 font-medium">
                        Tienes <span class="text-primary font-bold">{{ $items->count() }}</span> {{ $items->count() === 1 ? 'producto' : 'productos' }} en tu lista
                    </p>
                @endif
            </div>
            
            @if($items->count() > 0)
                <a href="{{ route('catalogo') }}" class="hidden sm:flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-primary transition-colors bg-white px-4 py-2 rounded-xl border border-gray-200 shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    Seguir comprando
                </a>
            @endif
        </div>

        @if($items->count() > 0)
        <div class="flex flex-col lg:flex-row gap-8 xl:gap-10">
            
            {{-- LISTA DE PRODUCTOS --}}
            <div class="flex-1">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden">
                    
                    <div class="hidden sm:grid grid-cols-12 gap-4 p-6 border-b border-gray-100 bg-gray-50/50 text-xs font-black text-gray-400 uppercase tracking-wider">
                        <div class="col-span-6">Producto</div>
                        <div class="col-span-3 text-center">Cantidad</div>
                        <div class="col-span-2 text-right">Total</div>
                        <div class="col-span-1"></div>
                    </div>

                    <div class="divide-y divide-gray-100" id="cart-items-wrapper">
                        @foreach($items as $item)
                            {{-- Fila del producto con Clases y Atributos para JS --}}
                            <div class="p-4 sm:p-6 flex flex-col sm:grid sm:grid-cols-12 gap-6 items-center sm:items-center relative group transition-colors hover:bg-gray-50/30 cart-item-row" id="item-{{ $item->id }}" data-price="{{ $item->producto->precio_venta_usd }}">
                                
                                <div class="col-span-6 flex items-center gap-4 sm:gap-6 w-full">
                                    <div class="w-24 h-24 sm:w-28 sm:h-28 flex-shrink-0 bg-gray-50 rounded-2xl overflow-hidden border border-gray-100 relative p-2">
                                        @if($item->producto->imagenes->first() && $item->producto->imagenes->first()->url_imagen)
                                            <img src="{{ asset('storage/' . $item->producto->imagenes->first()->url_imagen) }}" 
                                                alt="{{ $item->producto->nombre }}" 
                                                class="w-full h-full object-contain mix-blend-multiply drop-shadow-sm group-hover:scale-105 transition-transform duration-500">
                                        @else
                                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                                                <span class="material-symbols-outlined text-3xl mb-1">image_not_supported</span>
                                                <span class="text-[8px] font-bold uppercase tracking-widest">Sin Imagen</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex-1 pr-8 sm:pr-0">
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-500 mb-2">
                                            SKU: {{ $item->producto->sku }}
                                        </span>
                                        <h3 class="text-base sm:text-lg font-bold text-agro-dark leading-snug line-clamp-2 mb-1">
                                            <a href="#" class="hover:text-primary transition-colors">{{ $item->producto->nombre }}</a>
                                        </h3>
                                        <p class="text-sm font-black text-primary">${{ number_format($item->producto->precio_venta_usd, 2) }} <span class="text-[10px] font-bold text-gray-400 uppercase">c/u</span></p>
                                    </div>
                                </div>

                                {{-- Controles de Cantidad (AHORA CENTRADOS EN MÓVIL Y PC) --}}
                                <div class="col-span-3 flex justify-center w-full sm:w-auto">
                                    <div class="flex items-center bg-white border border-gray-200 rounded-xl shadow-sm h-11 w-32 overflow-hidden focus-within:ring-2 focus-within:ring-primary/20 focus-within:border-primary transition-all">
                                        <button type="button" onclick="updateQty({{ $item->id }}, -1)" class="w-10 h-full flex items-center justify-center text-gray-400 hover:text-agro-dark hover:bg-gray-50 transition-colors active:bg-gray-100 flex-shrink-0">
                                            <span class="material-symbols-outlined text-[20px]">remove</span>
                                        </button>
                                        
                                        <input type="text" inputmode="numeric" id="qty-{{ $item->id }}" readonly value="{{ $item->cantidad }}" class="qty-input flex-1 w-12 h-full text-center font-black text-agro-dark text-sm bg-transparent border-0 focus:ring-0 p-0 m-0 select-none">
                                        
                                        <button type="button" onclick="updateQty({{ $item->id }}, 1)" class="w-10 h-full flex items-center justify-center text-gray-400 hover:text-primary hover:bg-gray-50 transition-colors active:bg-gray-100 flex-shrink-0">
                                            <span class="material-symbols-outlined text-[20px]">add</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Total del Item --}}
                                <div class="col-span-2 text-right w-full sm:w-auto flex justify-between sm:block border-t border-gray-50 sm:border-0 pt-4 sm:pt-0 mt-2 sm:mt-0">
                                    <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider sm:hidden">Total</span>
                                    <span class="item-total-display text-lg font-black text-agro-dark">${{ number_format($item->producto->precio_venta_usd * $item->cantidad, 2) }}</span>
                                </div>

                                {{-- Botón Eliminar (AHORA ROJO Y MÁS VISIBLE) --}}
                                <div class="col-span-1 absolute top-4 right-4 sm:relative sm:top-0 sm:right-0 flex justify-end">
                                    <button onclick="openDeleteModal({{ $item->id }})" class="flex items-center justify-center w-10 h-10 text-red-500 bg-red-50 hover:bg-red-100 hover:text-red-700 rounded-xl transition-all duration-300 border border-red-100 hover:border-red-200 shadow-sm" title="Eliminar producto">
                                        <span class="material-symbols-outlined text-[22px]">delete</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="mt-6 sm:hidden">
                    <a href="{{ route('catalogo') }}" class="flex items-center justify-center gap-2 w-full text-sm font-bold text-gray-600 hover:text-primary transition-colors bg-white px-4 py-3.5 rounded-xl border border-gray-200 shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                        Seguir comprando
                    </a>
                </div>
            </div>

            {{-- PANEL DE RESUMEN --}}
            <aside class="w-full lg:w-[380px] xl:w-[420px] flex-shrink-0">
                <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.06)] border border-gray-100 p-6 sm:p-8 sticky top-24">
                    
                    <h2 class="text-xl font-black text-agro-dark mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">receipt_long</span>
                        Resumen del Pedido
                    </h2>
                    
                    <div class="space-y-4 text-sm font-medium text-gray-500 mb-6">
                        <div class="flex justify-between items-center">
                            <span id="summary-items-count">Subtotal ({{ $items->count() }} productos)</span>
                            <span id="summary-subtotal" class="font-bold text-gray-800">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Impuestos (IVA {{ $ivaPorcentaje }}%)</span>
                            <span id="summary-iva" class="font-bold text-gray-800">${{ number_format($montoIva, 2) }}</span>
                        </div>
                        
                        <div class="border-t border-dashed border-gray-200 my-4"></div>
                        
                        <div class="flex justify-between items-end">
                            <span class="text-base text-agro-dark font-bold">Total a pagar</span>
                            <div class="text-right">
                                <span id="summary-total" class="block text-3xl font-black text-agro-dark leading-none">${{ number_format($total, 2) }}</span>
                                <span class="block text-[11px] text-gray-400 font-bold uppercase mt-1">USD</span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="w-full py-4 bg-agro-dark text-white font-bold rounded-2xl shadow-lg shadow-agro-dark/20 hover:bg-primary hover:shadow-primary/30 transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-3 group">
                        Proceder al Pago
                        <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </a>
                    
                    <div class="mt-6 flex items-center justify-center gap-2 text-xs font-bold text-gray-400">
                        <span class="material-symbols-outlined text-[16px] text-green-500">lock</span>
                        Transacción 100% Segura
                    </div>
                </div>
            </aside>

        </div>
        
        @else
        
        {{-- ESTADO VACÍO --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-200 px-6 py-24 flex flex-col items-center justify-center text-center animate-fade-in-up">
            <div class="relative mb-8">
                <div class="absolute inset-0 bg-primary/20 rounded-full blur-3xl animate-pulse-slow"></div>
                <div class="w-32 h-32 bg-gray-50 rounded-full flex items-center justify-center relative z-10 border-8 border-white shadow-lg">
                    <span class="material-symbols-outlined text-[64px] text-gray-300">shopping_cart_off</span>
                </div>
            </div>
            
            <h2 class="text-2xl sm:text-3xl font-black text-agro-dark mb-3">Tu carrito está vacío</h2>
            <p class="text-gray-500 max-w-md mx-auto mb-8 text-sm sm:text-base leading-relaxed">
                Aún no has agregado insumos a tu carrito. Explora nuestro catálogo y encuentra los mejores productos para tu producción agrícola.
            </p>
            
            <a href="{{ route('catalogo') }}" class="inline-flex items-center gap-3 px-8 py-4 bg-primary text-agro-dark rounded-2xl font-black shadow-lg shadow-primary/30 hover:bg-primary/90 hover:-translate-y-1 transition-all duration-300 group">
                <span class="material-symbols-outlined group-hover:-rotate-12 transition-transform">storefront</span>
                Explorar Catálogo
            </a>
        </div>
        @endif

    </div>
</div>

{{-- TOAST NOTIFICATION --}}
<div id="toast-notification" class="fixed bottom-6 right-6 z-[100] transform transition-all duration-500 translate-y-24 opacity-0 pointer-events-none">
    <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-gray-100 p-4 flex items-center gap-4 min-w-[320px] max-w-md">
        <div id="toast-icon-container" class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 transition-colors">
            <span id="toast-icon" class="material-symbols-outlined text-2xl">check_circle</span>
        </div>
        <div class="flex-1">
            <h4 id="toast-title" class="font-bold text-gray-900 text-sm">Notificación</h4>
            <p id="toast-message" class="text-xs font-medium text-gray-500 mt-0.5">Mensaje...</p>
        </div>
        <button onclick="hideToast()" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100">
            <span class="material-symbols-outlined text-xl">close</span>
        </button>
    </div>
</div>

{{-- MODAL DE ELIMINACIÓN --}}
<div id="delete-modal" class="fixed inset-0 z-[120] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-agro-dark/40 backdrop-blur-sm transition-opacity opacity-0" id="delete-modal-backdrop"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95" id="delete-modal-panel">
                
                <div class="px-6 py-8 sm:p-8">
                    <div class="flex flex-col items-center text-center">
                        <div class="mx-auto flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full bg-red-50 mb-6">
                            <span class="material-symbols-outlined text-4xl text-red-500">delete_forever</span>
                        </div>
                        <h3 class="text-xl font-black text-agro-dark mb-2" id="modal-title">¿Eliminar producto?</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">
                            El producto será removido de tu carrito de compras. ¿Estás seguro de que deseas continuar?
                        </p>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row gap-3">
                    <button type="button" onclick="confirmDelete()" class="inline-flex w-full justify-center items-center rounded-xl bg-red-500 px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-red-600 sm:w-1/2 transition-colors">
                        Eliminar
                    </button>
                    <button type="button" onclick="closeDeleteModal()" class="inline-flex w-full justify-center items-center rounded-xl bg-white px-4 py-3 text-sm font-bold text-gray-700 shadow-sm border border-gray-200 hover:bg-gray-50 sm:w-1/2 transition-colors">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {{-- Configuración global enviada a JS --}}
    <script>
        window.CarritoConfig = {
            routes: {
                update: "{{ route('carrito.update') }}",
                remove: "{{ route('carrito.remove') }}"
            },
            ivaPercentage: {{ $ivaPorcentaje ?? 16 }}, // Usa 16 por defecto si no existe
            csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            sessionError: "{{ session('error') ?? '' }}",
            sessionSuccess: "{{ session('success') ?? '' }}"
        };
    </script>
    
    {{-- Incluir el nuevo script externo --}}
    <script src="{{ asset('js/carrito.js') }}"></script>
@endpush