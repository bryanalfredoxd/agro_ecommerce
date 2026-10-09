@extends('layouts.app')

@section('title', 'Mi Carrito de Compras - Agrosuministros Rodríguez')

@section('content')
@php
    $tiendaAbierta = \App\Models\HorarioSemanal::estaAbiertoAhora();
    $estadoHorario = \App\Models\HorarioSemanal::obtenerEstadoHorarioHoy();
@endphp
<div class="bg-slate-50 min-h-screen pb-20 pt-6 sm:pt-8 font-sans animate-fade-in-up">
    
    <div class="layout-container px-4 sm:px-6">

        @if(session('tienda_cerrada'))
            <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 flex items-start gap-3 shadow-xs animate-shake">
                <span class="material-symbols-outlined text-amber-600 text-2xl shrink-0 mt-0.5">store</span>
                <div class="text-xs sm:text-sm">
                    <h4 class="font-black text-amber-950">Aviso de Horario Comercial</h4>
                    <p class="mt-0.5 text-amber-800 leading-relaxed">{{ session('tienda_cerrada') }}</p>
                </div>
            </div>
        @elseif(!$tiendaAbierta)
            <div class="mb-6 p-4 rounded-2xl bg-amber-50/90 border border-amber-300/80 text-amber-900 flex items-start gap-3 shadow-xs">
                <span class="material-symbols-outlined text-amber-600 text-2xl shrink-0 mt-0.5">schedule</span>
                <div class="text-xs sm:text-sm">
                    <h4 class="font-black text-amber-950">Tienda fuera de horario de atención</h4>
                    <p class="mt-0.5 text-amber-800 leading-relaxed">
                        En este momento nos encontramos fuera de horario de apertura comercial (<strong>{{ $estadoHorario['mostrar'] }}</strong>). 
                        Puedes consultar insumos y modificar tu carrito con total normalidad, pero la compra y verificación de pagos se habilitarán al abrir la tienda.
                    </p>
                </div>
            </div>
        @endif
        
        {{-- PASOS DE COMPRA (STEPS PROGRESS INDICATOR) --}}
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-200/80">
            <div>
                {{-- Breadcrumb --}}
                <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-2">
                    <a href="{{ url('/') }}" class="hover:text-[#1e549e] transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-[15px]">home</span> Inicio
                    </a>
                    <span>/</span>
                    <a href="{{ route('catalogo') }}" class="hover:text-[#1e549e] transition-colors">Catálogo</a>
                    <span>/</span>
                    <span class="text-[#1e549e] font-bold">Carrito de Compras</span>
                </nav>

                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="material-symbols-outlined text-[32px] sm:text-[38px] text-[#3492cf]">shopping_bag</span>
                    Tu Carrito de <span class="text-[#1e549e]">Compras</span>
                </h1>
                
                @if($items->count() > 0)
                    <p id="cart-items-count-text" class="text-slate-500 text-xs sm:text-sm mt-1.5 font-medium">
                        Tienes <span class="text-[#1e549e] font-black">{{ $items->count() }}</span> {{ $items->count() === 1 ? 'insumo' : 'insumos' }} en tu lista
                    </p>
                @endif
            </div>
            
            <div class="flex items-center gap-3">
                {{-- Steps Visual --}}
                <div class="hidden sm:flex items-center gap-2 text-xs font-bold text-slate-500 bg-white py-2 px-4 rounded-xl border border-slate-200/80 shadow-2xs">
                    <span class="text-[#1e549e] bg-[#e5f2fb] px-3 py-1 rounded-lg flex items-center gap-1.5 border border-[#aad6f3]/60">
                        <span class="material-symbols-outlined text-[16px]">shopping_cart</span> 1. Carrito
                    </span>
                    <span class="material-symbols-outlined text-[15px] text-slate-300">chevron_right</span>
                    <span class="text-slate-400 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">local_shipping</span> 2. Entrega y Pago
                    </span>
                    <span class="material-symbols-outlined text-[15px] text-slate-300">chevron_right</span>
                    <span class="text-slate-400 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">verified</span> 3. Confirmación
                    </span>
                </div>

                @if($items->count() > 0)
                    <a href="{{ route('catalogo') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-slate-700 hover:text-[#1e549e] transition-colors bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-2xs hover:border-[#3492cf]/40">
                        <span class="material-symbols-outlined text-[17px]">arrow_back</span>
                        <span class="hidden sm:inline">Seguir comprando</span>
                        <span class="sm:hidden">Seguir</span>
                    </a>
                @endif
            </div>
        </div>

        @if($items->count() > 0)
        <div class="flex flex-col lg:flex-row gap-8 items-start">
            
            {{-- LISTA DE PRODUCTOS --}}
            <div class="flex-1 w-full min-w-0">
                <div class="bg-white rounded-3xl shadow-xs border border-slate-200/90 overflow-hidden">
                    
                    {{-- Cabecera de la tabla (Desktop) --}}
                    <div class="hidden sm:grid grid-cols-12 gap-4 px-6 py-4 border-b border-slate-100 bg-slate-50/75 text-[11px] font-black text-slate-500 uppercase tracking-wider">
                        <div class="col-span-6">Insumo / Producto</div>
                        <div class="col-span-3 text-center">Cantidad</div>
                        <div class="col-span-2 text-right">Subtotal</div>
                        <div class="col-span-1 text-center">Acción</div>
                    </div>

                    {{-- Lista de Filas --}}
                    <div class="divide-y divide-slate-100" id="cart-items-wrapper">
                        @foreach($items as $item)
                            @php
                                $precioUsado = $item->producto->precio_oferta_usd ?: $item->producto->precio_venta_usd;
                                $imgSrc = $item->producto->imagenes->first()?->imagen_url ?? $item->producto->imagen_url;
                                $subtotalItemUsd = $precioUsado * $item->cantidad;
                                $subtotalItemBs = $subtotalItemUsd * ($tasaDolar ?? 1);
                            @endphp

                            {{-- Fila del Item --}}
                            <div class="p-4 sm:p-6 flex flex-col sm:grid sm:grid-cols-12 gap-4 sm:gap-6 items-start sm:items-center relative group transition-colors hover:bg-slate-50/40 cart-item-row" 
                                 id="item-{{ $item->id }}" 
                                 data-price="{{ $precioUsado }}"
                                 data-min="{{ $item->producto->venta_minima }}"
                                 data-step="{{ $item->producto->paso_venta }}">
                                
                                {{-- 1. Información e Imagen --}}
                                <div class="col-span-6 flex items-center gap-4 w-full">
                                    <div class="size-20 sm:size-24 shrink-0 bg-slate-50 rounded-2xl overflow-hidden border border-slate-200/80 p-2 flex items-center justify-center relative">
                                        @if($imgSrc)
                                            <img src="{{ asset($imgSrc) }}" 
                                                 alt="{{ $item->producto->nombre }}" 
                                                 class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300"
                                                 onerror="this.onerror=null; this.remove(); document.getElementById('placeholder-cart-{{ $item->id }}').classList.remove('hidden');">
                                            
                                            <div id="placeholder-cart-{{ $item->id }}" class="hidden w-full h-full flex flex-col items-center justify-center text-slate-300">
                                                <span class="material-symbols-outlined text-3xl">inventory_2</span>
                                                <span class="text-[8px] font-bold uppercase text-slate-400">Sin foto</span>
                                            </div>
                                        @else
                                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-300">
                                                <span class="material-symbols-outlined text-3xl">inventory_2</span>
                                                <span class="text-[8px] font-bold uppercase text-slate-400">Sin foto</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex-1 min-w-0 pr-8 sm:pr-0">
                                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                                            @if($item->producto->sku)
                                                <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-500">
                                                    {{ $item->producto->sku }}
                                                </span>
                                            @endif
                                            @if($item->producto->categoria)
                                                <span class="text-[10px] font-extrabold uppercase tracking-wide text-[#1e549e] bg-[#e5f2fb] px-1.5 py-0.5 rounded">
                                                    {{ $item->producto->categoria->nombre }}
                                                </span>
                                            @endif
                                        </div>

                                        <h3 class="text-sm sm:text-base font-bold text-slate-900 leading-snug line-clamp-2 mb-1 group-hover:text-[#1e549e] transition-colors">
                                            {{ $item->producto->nombre }}
                                        </h3>
                                        
                                        <div class="flex items-baseline gap-2">
                                            <span class="text-xs sm:text-sm font-black text-[#1e549e]">
                                                ${{ number_format($precioUsado, 2) }}
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-bold uppercase">
                                                / {{ $item->producto->unidad_medida }}
                                            </span>
                                            <span class="text-[10px] text-slate-500 font-medium hidden sm:inline">
                                                (Bs. {{ number_format($precioUsado * ($tasaDolar ?? 1), 2, ',', '.') }})
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- 2. Selector de Cantidad --}}
                                <div class="col-span-3 flex sm:justify-center w-full sm:w-auto mt-2 sm:mt-0">
                                    <div class="flex items-center bg-slate-50 border border-slate-200 rounded-xl shadow-2xs h-10 overflow-hidden focus-within:ring-2 focus-within:ring-[#3492cf]/20 focus-within:border-[#3492cf] transition-all">
                                        <button type="button" 
                                                onclick="updateQty({{ $item->id }}, 'sub')" 
                                                class="size-9 flex items-center justify-center text-slate-600 hover:text-[#1e549e] hover:bg-slate-100 transition-colors active:scale-90 shrink-0"
                                                title="Disminuir cantidad">
                                            <span class="material-symbols-outlined text-[18px]">remove</span>
                                        </button>
                                        
                                        <input type="text" 
                                               inputmode="decimal" 
                                               id="qty-{{ $item->id }}" 
                                               readonly 
                                               value="{{ (float) $item->cantidad }}" 
                                               class="qty-input w-12 text-center font-black text-slate-800 text-sm bg-transparent border-0 focus:ring-0 p-0 m-0 select-none">
                                        
                                        <button type="button" 
                                                onclick="updateQty({{ $item->id }}, 'add')" 
                                                class="size-9 flex items-center justify-center text-slate-600 hover:text-[#1e549e] hover:bg-slate-100 transition-colors active:scale-90 shrink-0"
                                                title="Aumentar cantidad">
                                            <span class="material-symbols-outlined text-[18px]">add</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- 3. Subtotal del Item (USD y Bs.) --}}
                                <div class="col-span-2 text-right w-full sm:w-auto flex justify-between sm:flex-col sm:items-end border-t border-slate-100 sm:border-0 pt-3 sm:pt-0 mt-1 sm:mt-0">
                                    <span class="text-xs font-bold text-slate-500 uppercase sm:hidden">Subtotal:</span>
                                    <div class="text-right">
                                        <div class="item-total-display text-base sm:text-lg font-black text-slate-900 leading-tight">
                                            ${{ number_format($subtotalItemUsd, 2) }}
                                        </div>
                                        <div class="item-total-bs-display text-[11px] font-bold text-slate-500 mt-0.5">
                                            Bs. {{ number_format($subtotalItemBs, 2, ',', '.') }}
                                        </div>
                                    </div>
                                </div>

                                {{-- 4. Botón Eliminar --}}
                                <div class="col-span-1 absolute top-4 right-4 sm:relative sm:top-0 sm:right-0 flex justify-end w-full sm:w-auto">
                                    <button onclick="openDeleteModal({{ $item->id }})" 
                                            type="button"
                                            class="flex items-center justify-center size-9 text-rose-500 bg-rose-50 hover:bg-rose-100 hover:text-rose-700 rounded-xl transition-all duration-200 border border-rose-100 shadow-2xs" 
                                            title="Eliminar insumo del carrito">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- PANEL DE RESUMEN DE COMPRA (STICKY) --}}
            <aside class="w-full lg:w-[380px] xl:w-[400px] shrink-0">
                <div class="bg-white rounded-3xl shadow-xs border border-slate-200/90 p-6 sm:p-7 sticky top-24 space-y-6">
                    
                    {{-- Título del Resumen --}}
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5 text-slate-900">
                            <span class="material-symbols-outlined text-[#1e549e] text-[22px]">receipt_long</span>
                            <h2 class="text-base sm:text-lg font-black tracking-tight">Resumen del Pedido</h2>
                        </div>
                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-2 py-0.5 rounded-full">
                            Tasa BCV Activa
                        </span>
                    </div>
                    
                    {{-- Desglose de Precios --}}
                    <div class="space-y-3.5 text-sm">
                        <div class="flex justify-between items-center text-slate-600">
                            <span id="summary-items-count" class="font-medium">Subtotal ({{ $items->count() }} {{ $items->count() === 1 ? 'insumo' : 'insumos' }})</span>
                            <span id="summary-subtotal" class="font-bold text-slate-800">${{ number_format($subtotal, 2) }}</span>
                        </div>

                        <div class="flex justify-between items-center text-slate-600">
                            <span class="font-medium flex items-center gap-1">
                                Impuesto IVA ({{ (float) $ivaPorcentaje }}%)
                                <span class="material-symbols-outlined text-[14px] text-slate-400" title="IVA legal aplicable">info</span>
                            </span>
                            <span id="summary-iva" class="font-bold text-slate-800">${{ number_format($montoIva, 2) }}</span>
                        </div>
                        
                        <div class="border-t border-dashed border-slate-200 pt-3"></div>
                        
                        {{-- Total USD --}}
                        <div class="flex justify-between items-baseline">
                            <span class="text-sm sm:text-base text-slate-800 font-bold">Total a pagar:</span>
                            <div class="text-right">
                                <span id="summary-total" class="text-2xl sm:text-3xl font-black text-[#1e549e] tracking-tight leading-none">
                                    ${{ number_format($total, 2) }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase ml-1">USD</span>
                            </div>
                        </div>

                        {{-- Total Bolívares (VES) --}}
                        <div class="bg-gradient-to-r from-[#e5f2fb] to-slate-50 p-3.5 rounded-2xl border border-[#aad6f3]/60 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-black text-[#1e549e] uppercase tracking-wider block">Equivalente BCV</span>
                                <span class="text-[10px] text-slate-500 font-medium">1 USD = Bs. {{ number_format($tasaDolar, 2, ',', '.') }}</span>
                            </div>
                            <span id="summary-total-bs" class="text-base font-black text-slate-800">
                                Bs. {{ number_format($total * ($tasaDolar ?? 1), 2, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    {{-- Botón Proceder al Pago --}}
                    <div>
                        @if($tiendaAbierta)
                            <a href="{{ route('checkout.index') }}" class="w-full flex items-center justify-center gap-2 py-3.5 px-6 rounded-2xl bg-[#1e549e] hover:bg-[#3492cf] text-white font-black text-sm sm:text-base shadow-sm hover:shadow-md transition-all duration-200 group active:scale-98">
                                <span>Proceder al Pago</span>
                                <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                            </a>
                        @else
                            <button type="button" disabled class="w-full flex items-center justify-center gap-2 py-3.5 px-6 rounded-2xl bg-slate-200 text-slate-500 font-bold text-sm sm:text-base cursor-not-allowed shadow-none border border-slate-300/80">
                                <span class="material-symbols-outlined text-[20px]">lock_clock</span>
                                <span>Tienda Cerrada ({{ $estadoHorario['mostrar'] }})</span>
                            </button>
                            <p class="text-[11px] text-center text-amber-700 font-bold mt-2 flex items-center justify-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">info</span> Pagos disponibles en horario laboral
                            </p>
                        @endif
                    </div>
                    
                    {{-- Badges de Seguridad --}}
                    <div class="pt-2 border-t border-slate-100 space-y-2">
                        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                            <span class="material-symbols-outlined text-[16px] text-emerald-600">lock</span>
                            <span>Compra 100% segura y encriptada</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                            <span class="material-symbols-outlined text-[16px] text-[#3492cf]">local_shipping</span>
                            <span>Despacho directo o retiro en tienda física</span>
                        </div>
                    </div>

                </div>
            </aside>

        </div>
        
        @else
        
        {{-- ESTADO VACÍO (CARRITO VACÍO) --}}
        <div class="bg-white rounded-3xl shadow-xs border border-slate-200/90 px-6 py-20 sm:py-24 flex flex-col items-center justify-center text-center animate-fade-in-up">
            <div class="size-24 rounded-3xl bg-[#e5f2fb] flex items-center justify-center mx-auto mb-6 text-[#1e549e] border border-[#aad6f3]/40">
                <span class="material-symbols-outlined text-[48px]">remove_shopping_cart</span>
            </div>
            
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 mb-2">Tu carrito está vacío</h2>
            <p class="text-slate-500 max-w-md mx-auto mb-8 text-xs sm:text-sm leading-relaxed">
                Aún no has seleccionado insumos agrícolas o productos veterinarios. Explora nuestro catálogo y equipa tu unidad productiva con calidad garantizada.
            </p>
            
            <a href="{{ route('catalogo') }}" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-2xl bg-[#1e549e] hover:bg-[#3492cf] text-white font-bold text-sm shadow-xs hover:shadow-md transition-all group">
                <span class="material-symbols-outlined text-[19px]">storefront</span>
                <span>Explorar Catálogo de Insumos</span>
            </a>
        </div>
        @endif

    </div>
</div>

{{-- TOAST NOTIFICATION --}}
<div id="toast-notification" class="fixed bottom-6 right-6 z-[100] transform transition-all duration-300 translate-y-24 opacity-0 pointer-events-none">
    <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-slate-200/80 p-4 flex items-center gap-3.5 min-w-[320px] max-w-md">
        <div id="toast-icon-container" class="size-11 rounded-xl flex items-center justify-center shrink-0 transition-colors">
            <span id="toast-icon" class="material-symbols-outlined text-2xl">check_circle</span>
        </div>
        <div class="flex-1 min-w-0">
            <h4 id="toast-title" class="font-bold text-slate-900 text-sm">Notificación</h4>
            <p id="toast-message" class="text-xs font-medium text-slate-500 mt-0.5 truncate">Mensaje...</p>
        </div>
        <button onclick="hideToast()" class="text-slate-400 hover:text-slate-700 transition-colors p-1 rounded-lg hover:bg-slate-100">
            <span class="material-symbols-outlined text-lg">close</span>
        </button>
    </div>
</div>

{{-- MODAL DE ELIMINACIÓN --}}
<div id="delete-modal" class="fixed inset-0 z-[120] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-[#0f2347]/50 backdrop-blur-xs transition-opacity opacity-0" id="delete-modal-backdrop" onclick="closeDeleteModal()"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md opacity-0 translate-y-6 sm:translate-y-0 sm:scale-95 border border-slate-100" id="delete-modal-panel">
                
                <div class="p-6 sm:p-7">
                    <div class="flex flex-col items-center text-center">
                        <div class="size-16 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center mb-4 border border-rose-100">
                            <span class="material-symbols-outlined text-3xl">delete_forever</span>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 mb-1.5" id="modal-title">¿Eliminar insumo del carrito?</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Este producto será retirado de tu lista de compra actual. Podrás volver a agregarlo desde el catálogo en cualquier momento.
                        </p>
                    </div>
                </div>
                
                <div class="bg-slate-50 px-6 py-4 flex flex-col-reverse sm:flex-row gap-2.5 border-t border-slate-100">
                    <button type="button" 
                            onclick="closeDeleteModal()" 
                            class="w-full sm:w-1/2 inline-flex justify-center items-center rounded-xl bg-white px-4 py-2.5 text-xs font-bold text-slate-600 border border-slate-200 hover:bg-slate-100 transition-colors">
                        Conservar
                    </button>
                    <button type="button" 
                            onclick="confirmDelete()" 
                            class="w-full sm:w-1/2 inline-flex justify-center items-center rounded-xl bg-rose-600 px-4 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-rose-700 transition-colors">
                        Sí, eliminar
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
            ivaPercentage: {{ $ivaPorcentaje ?? 16 }},
            tasaDolar: {{ $tasaDolar ?? 1 }},
            csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            sessionError: "{{ session('error') ?? '' }}",
            sessionSuccess: "{{ session('success') ?? '' }}"
        };
    </script>
    
    <script src="{{ asset('js/carrito.js') }}"></script>
@endpush