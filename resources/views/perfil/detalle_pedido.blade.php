@extends('layouts.app')

@section('title', 'Detalle del Pedido #' . str_pad($pedido->id, 6, '0', STR_PAD_LEFT) . ' - Agrosuministros Rodríguez')

@section('content')
<div class="bg-slate-50 min-h-screen font-sans pb-24 relative">

    {{-- CABECERA STICKY SUPERIOR --}}
    <div class="bg-white/95 backdrop-blur-md shadow-2xs border-b border-slate-200/80 sticky top-0 z-30">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('perfil.pedidos') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-[#1e549e] transition-colors group">
                <span class="material-symbols-outlined text-[18px] group-hover:-translate-x-1 transition-transform">arrow_back</span>
                <span class="hidden sm:inline">Volver a mis pedidos</span>
                <span class="sm:hidden">Volver</span>
            </a>

            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Orden</span>
                    <span class="font-mono text-sm font-black text-slate-900">#{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>

                @if(in_array($pedido->estado, ['pagado', 'aprobado', 'completado_caja', 'en_ruta', 'entregado']))
                    <a href="{{ route('perfil.pedido.ticket', $pedido->id) }}" 
                       target="_blank" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#e5f2fb] hover:bg-[#3492cf] hover:text-white text-[#1e549e] font-bold text-xs transition-colors shadow-2xs">
                        <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                        <span class="hidden sm:inline">Ticket / Factura</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- CONTENIDO PRINCIPAL --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-10">

        @php
            $estadoPill = match($pedido->estado) {
                'pendiente' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'icono' => 'hourglass_empty', 'texto' => 'Pendiente de Pago', 'paso' => 1],
                'revision' => ['bg' => 'bg-sky-50 text-sky-700 border-sky-200', 'icono' => 'search', 'texto' => 'En Verificación', 'paso' => 2],
                'pagado', 'aprobado', 'completado_caja' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'icono' => 'verified', 'texto' => 'Pago Verificado', 'paso' => 2],
                'preparacion' => ['bg' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'icono' => 'inventory', 'texto' => 'En Preparación', 'paso' => 3],
                'en_ruta' => ['bg' => 'bg-purple-50 text-purple-700 border-purple-200', 'icono' => 'local_shipping', 'texto' => 'En Ruta de Entrega', 'paso' => 3],
                'entregado' => ['bg' => 'bg-slate-100 text-slate-800 border-slate-300', 'icono' => 'package_2', 'texto' => 'Entregado con Éxito', 'paso' => 4],
                'rechazado', 'cancelado', 'devuelto' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'icono' => 'cancel', 'texto' => 'Cancelado / Rechazado', 'paso' => 0],
                default => ['bg' => 'bg-slate-50 text-slate-600 border-slate-200', 'icono' => 'info', 'texto' => ucfirst(str_replace('_', ' ', $pedido->estado)), 'paso' => 1]
            };

            $esCancelado = in_array($pedido->estado, ['rechazado', 'cancelado', 'devuelto']);
            $pasoActual = $estadoPill['paso'];
            $esDelivery = $pedido->costo_delivery_usd > 0 || !empty($pedido->direccion_texto);
        @endphp

        {{-- ENCABEZADO DE ORDEN --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                    <span>Orden de Compra</span>
                    <span>•</span>
                    <span class="text-[#1e549e] font-mono">ID: {{ $pedido->id }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    Detalle de la Orden
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 font-medium flex items-center gap-1.5 mt-1">
                    <span class="material-symbols-outlined text-[16px] text-slate-400">calendar_month</span>
                    Registrado el {{ \Carbon\Carbon::parse($pedido->creado_at)->format('d F Y, h:i A') }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider border {{ $estadoPill['bg'] }} shadow-2xs">
                    <span class="material-symbols-outlined text-[18px]">{{ $estadoPill['icono'] }}</span>
                    <span>{{ $estadoPill['texto'] }}</span>
                </div>
            </div>
        </div>

        {{-- LÍNEA DE TIEMPO / SEGUIMIENTO DEL PEDIDO --}}
        @if(!$esCancelado)
            <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-xs border border-slate-200/90 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#1e549e] text-[18px]">timeline</span>
                        Progreso del Pedido
                    </h3>
                    <span class="text-[11px] font-bold text-slate-400">Paso {{ min($pasoActual, 4) }} de 4</span>
                </div>

                {{-- Stepper de 4 Pasos --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 relative">
                    {{-- Paso 1: Registrado --}}
                    <div class="flex flex-col items-center text-center relative z-10">
                        <div class="size-11 rounded-2xl flex items-center justify-center font-black text-xs transition-all {{ $pasoActual >= 1 ? 'bg-[#1e549e] text-white shadow-xs' : 'bg-slate-100 text-slate-400' }}">
                            <span class="material-symbols-outlined text-[20px]">assignment</span>
                        </div>
                        <h4 class="font-bold text-xs text-slate-800 mt-2.5 leading-tight">Orden Recibida</h4>
                        <span class="text-[10px] text-slate-400 font-medium mt-0.5">Registrada en sistema</span>
                    </div>

                    {{-- Paso 2: Pago Aprobado --}}
                    <div class="flex flex-col items-center text-center relative z-10">
                        <div class="size-11 rounded-2xl flex items-center justify-center font-black text-xs transition-all {{ $pasoActual >= 2 ? 'bg-[#1e549e] text-white shadow-xs' : 'bg-slate-100 text-slate-400' }}">
                            <span class="material-symbols-outlined text-[20px]">verified</span>
                        </div>
                        <h4 class="font-bold text-xs text-slate-800 mt-2.5 leading-tight">Pago Verificado</h4>
                        <span class="text-[10px] text-slate-400 font-medium mt-0.5">Comprobante validado</span>
                    </div>

                    {{-- Paso 3: En Despacho / Preparación --}}
                    <div class="flex flex-col items-center text-center relative z-10">
                        <div class="size-11 rounded-2xl flex items-center justify-center font-black text-xs transition-all {{ $pasoActual >= 3 ? 'bg-[#1e549e] text-white shadow-xs' : 'bg-slate-100 text-slate-400' }}">
                            <span class="material-symbols-outlined text-[20px]">{{ $esDelivery ? 'local_shipping' : 'inventory_2' }}</span>
                        </div>
                        <h4 class="font-bold text-xs text-slate-800 mt-2.5 leading-tight">{{ $esDelivery ? 'En Ruta / Delivery' : 'En Preparación' }}</h4>
                        <span class="text-[10px] text-slate-400 font-medium mt-0.5">{{ $esDelivery ? 'Hacia tu ubicación' : 'Listo en mostrador' }}</span>
                    </div>

                    {{-- Paso 4: Entregado --}}
                    <div class="flex flex-col items-center text-center relative z-10">
                        <div class="size-11 rounded-2xl flex items-center justify-center font-black text-xs transition-all {{ $pasoActual >= 4 ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-400' }}">
                            <span class="material-symbols-outlined text-[20px]">package_2</span>
                        </div>
                        <h4 class="font-bold text-xs text-slate-800 mt-2.5 leading-tight">Entregado</h4>
                        <span class="text-[10px] text-slate-400 font-medium mt-0.5">Orden finalizada</span>
                    </div>
                </div>
            </div>
        @else
            {{-- Alerta de Cancelación --}}
            <div class="mb-8 p-5 rounded-3xl bg-rose-50 border border-rose-200/90 shadow-2xs">
                <div class="flex items-start gap-3.5">
                    <div class="size-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">error</span>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-black text-rose-900 leading-tight mb-1">
                            Esta orden se encuentra cancelada o rechazada
                        </h4>
                        @if($pedido->motivo_devolucion)
                            <p class="text-xs sm:text-sm text-rose-700 leading-relaxed font-medium">
                                <span class="font-bold">Motivo:</span> {{ $pedido->motivo_devolucion }}
                            </p>
                        @endif
                        <p class="text-xs text-rose-600 mt-2 font-medium">
                            Si consideras que se trata de un error o deseas reprogramar tu pago, comunícate con nuestro equipo de soporte.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- CUADRÍCULA PRINCIPAL --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
            
            {{-- COLUMNA IZQUIERDA: Artículos, Entrega y Pago --}}
            <div class="lg:col-span-7 xl:col-span-8 space-y-6">
                
                {{-- Tarjeta de Artículos --}}
                <div class="bg-white rounded-3xl shadow-xs border border-slate-200/90 overflow-hidden">
                    <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[#1e549e] text-[22px]">shopping_cart</span>
                            <h3 class="text-base font-black text-slate-900 leading-tight">
                                Insumos de la Orden ({{ $pedido->detalles->count() }})
                            </h3>
                        </div>
                    </div>
                    
                    <div class="divide-y divide-slate-100">
                        @foreach($pedido->detalles as $detalle)
                            <div class="p-5 sm:p-6 flex flex-col sm:flex-row gap-4 sm:items-center justify-between hover:bg-slate-50/50 transition-colors">
                                
                                <div class="flex items-center gap-4 min-w-0 flex-1">
                                    {{-- Thumbnail --}}
                                    <div class="size-16 sm:size-20 bg-slate-50 rounded-2xl border border-slate-200 overflow-hidden shrink-0 relative flex items-center justify-center">
                                        @if($detalle->producto && $detalle->producto->imagen_url)
                                            <img src="{{ asset($detalle->producto->imagen_url) }}" 
                                                 alt="{{ $detalle->producto->nombre ?? 'Producto' }}"
                                                 class="size-full object-cover">
                                        @else
                                            <span class="material-symbols-outlined text-slate-300 text-[26px]">inventory_2</span>
                                        @endif
                                        <span class="absolute top-1 right-1 size-5 rounded-full bg-[#1e549e] text-white text-[10px] font-black flex items-center justify-center">
                                            {{ (int)$detalle->cantidad_solicitada }}
                                        </span>
                                    </div>
                                    
                                    {{-- Información --}}
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-bold text-slate-900 text-sm sm:text-base leading-snug truncate">
                                            {{ $detalle->producto ? $detalle->producto->nombre : 'Insumo Agropecuario' }}
                                        </h4>
                                        <div class="flex flex-wrap items-center gap-2 mt-1">
                                            <span class="text-xs font-black text-[#1e549e]">
                                                ${{ number_format($detalle->precio_historico_usd, 2) }}
                                                <span class="text-[10px] font-normal text-slate-400">c/u</span>
                                            </span>
                                            <span class="text-slate-300">•</span>
                                            <span class="text-xs font-semibold text-slate-500">
                                                Cant: {{ (int)$detalle->cantidad_solicitada }}
                                            </span>
                                        </div>

                                        {{-- Observación del producto si existe --}}
                                        @if($detalle->observaciones)
                                            <p class="text-[11px] text-slate-500 mt-1 italic">
                                                Nota: {{ $detalle->observaciones }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                
                                {{-- Subtotal del Producto --}}
                                <div class="sm:text-right pt-2 sm:pt-0 border-t sm:border-0 border-slate-100 flex justify-between sm:block">
                                    <span class="text-xs font-bold text-slate-400 sm:hidden">Total Item</span>
                                    <div>
                                        <span class="font-black text-slate-900 text-base sm:text-lg">
                                            ${{ number_format($detalle->cantidad_solicitada * $detalle->precio_historico_usd, 2) }}
                                        </span>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase block">USD</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Tarjeta de Entrega y Destino --}}
                <div class="bg-white rounded-3xl shadow-xs border border-slate-200/90 p-5 sm:p-6">
                    <div class="flex items-center gap-2.5 mb-4">
                        <span class="material-symbols-outlined text-[#1e549e] text-[22px]">local_shipping</span>
                        <h3 class="text-base font-black text-slate-900 leading-tight">
                            Modalidad y Destino de Entrega
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider block mb-1">
                                Modalidad de Despacho
                            </span>
                            <div class="flex items-center gap-2 text-sm font-bold text-slate-800">
                                <span class="material-symbols-outlined text-[20px] text-[#1e549e]">
                                    {{ $esDelivery ? 'local_shipping' : 'storefront' }}
                                </span>
                                <span>{{ $esDelivery ? 'Servicio de Delivery a Domicilio' : 'Retiro en Mostrador / Tienda' }}</span>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider block mb-1">
                                Dirección de Entrega
                            </span>
                            <p class="text-xs sm:text-sm font-medium text-slate-700 leading-relaxed">
                                {{ $pedido->direccion_texto ?: 'Retiro directo en sede Agrosuministros Rodríguez.' }}
                            </p>
                        </div>
                    </div>

                    @if($pedido->instrucciones_entrega)
                        <div class="mt-4 p-3.5 rounded-2xl bg-amber-50/80 border border-amber-200/70 text-amber-900 text-xs flex items-start gap-2.5">
                            <span class="material-symbols-outlined text-amber-600 text-[18px] shrink-0 mt-0.5">comment</span>
                            <div class="leading-relaxed">
                                <span class="font-bold">Indicaciones de despacho:</span> 
                                <span class="text-slate-800 italic">"{{ $pedido->instrucciones_entrega }}"</span>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Tarjeta de Información de Pago --}}
                @if($pedido->pago)
                    @php
                        $iconoPago = match($pedido->pago->metodo) {
                            'pago_movil' => 'phone_iphone',
                            'zelle' => 'payments',
                            'efectivo_usd', 'efectivo_bs' => 'attach_money',
                            'transferencia' => 'account_balance',
                            'binance' => 'currency_bitcoin',
                            default => 'payments'
                        };
                    @endphp

                    <div class="bg-white rounded-3xl shadow-xs border border-slate-200/90 p-5 sm:p-6">
                        <div class="flex items-center gap-2.5 mb-4">
                            <span class="material-symbols-outlined text-emerald-600 text-[22px]">payments</span>
                            <h3 class="text-base font-black text-slate-900 leading-tight">
                                Información de Pago Registrado
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider block mb-1">Forma de Pago</span>
                                    <div class="flex items-center gap-2 text-sm font-bold text-slate-900">
                                        <span class="material-symbols-outlined text-[18px] text-[#1e549e]">{{ $iconoPago }}</span>
                                        <span>{{ ucfirst(str_replace('_', ' ', $pedido->pago->metodo)) }}</span>
                                    </div>
                                </div>
                            </div>

                            @if($pedido->pago->referencia_bancaria)
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider block mb-1">Referencia Bancaria</span>
                                        <span class="font-mono font-bold text-sm text-slate-900 tracking-wider block truncate">
                                            {{ $pedido->pago->referencia_bancaria }}
                                        </span>
                                    </div>
                                    <button type="button" 
                                            onclick="navigator.clipboard.writeText('{{ $pedido->pago->referencia_bancaria }}'); this.innerText='¡Copiado!'"
                                            class="shrink-0 text-xs font-bold text-[#1e549e] bg-white px-2.5 py-1.5 rounded-xl border border-slate-200 shadow-2xs hover:bg-[#e5f2fb] transition-colors cursor-pointer">
                                        Copiar
                                    </button>
                                </div>
                            @endif
                        </div>

                        {{-- Comprobante / Voucher Adjunto --}}
                        @if($pedido->pago->captura_pago_url)
                            <div class="mt-4 pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="material-symbols-outlined text-slate-400 text-[20px]">image</span>
                                    <span class="text-xs font-bold text-slate-700">Comprobante de pago digital adjunto</span>
                                </div>

                                <button type="button" 
                                        onclick="window.PedidoConfig.abrirModalImagen('{{ asset($pedido->pago->captura_pago_url) }}')" 
                                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#e5f2fb] hover:bg-[#3492cf] hover:text-white text-[#1e549e] font-bold text-xs rounded-xl transition-all shadow-2xs cursor-pointer">
                                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                                    <span>Ver Comprobante</span>
                                </button>
                            </div>
                        @endif
                    </div>
                @endif

            </div>

            {{-- COLUMNA DERECHA: Resumen Financiero y Acciones --}}
            <div class="lg:col-span-5 xl:col-span-4 space-y-6 lg:sticky lg:top-24">
                
                {{-- Tarjeta Resumen Financiero --}}
                <div class="bg-white rounded-3xl shadow-xs border border-slate-200/90 overflow-hidden">
                    <div class="p-5 sm:p-6 bg-gradient-to-r from-[#0b1d38] to-[#1e549e] text-white relative overflow-hidden">
                        <div class="absolute -top-10 -right-10 size-32 bg-[#3492cf]/20 rounded-full blur-2xl pointer-events-none"></div>
                        <h3 class="text-base sm:text-lg font-black flex items-center gap-2 relative z-10">
                            <span class="material-symbols-outlined text-[22px]">receipt_long</span>
                            Resumen de la Orden
                        </h3>
                    </div>

                    <div class="p-5 sm:p-6 space-y-3">
                        <div class="flex justify-between items-center text-xs sm:text-sm text-slate-500 font-medium">
                            <span>Subtotal</span>
                            <span class="text-slate-800 font-bold">${{ number_format($pedido->subtotal_usd, 2) }}</span>
                        </div>

                        <div class="flex justify-between items-center text-xs sm:text-sm text-slate-500 font-medium">
                            <span>Costo de Delivery</span>
                            <span class="font-bold {{ $pedido->costo_delivery_usd > 0 ? 'text-slate-800' : 'text-emerald-600' }}">
                                {{ $pedido->costo_delivery_usd > 0 ? '$'.number_format($pedido->costo_delivery_usd, 2) : 'Gratis' }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center text-xs sm:text-sm text-slate-500 font-medium">
                            <span>Impuestos (IVA 16%)</span>
                            <span class="text-slate-800 font-bold">
                                ${{ number_format(max(0, $pedido->total_usd - $pedido->subtotal_usd - $pedido->costo_delivery_usd), 2) }}
                            </span>
                        </div>

                        {{-- Total Final --}}
                        <div class="border-t border-slate-100 pt-3 mt-3">
                            <div class="flex justify-between items-baseline mb-3">
                                <span class="text-sm font-black text-slate-900">Total a Pagar</span>
                                <div class="text-right">
                                    <span class="text-2xl sm:text-3xl font-black text-[#1e549e] leading-none">
                                        ${{ number_format($pedido->total_usd, 2) }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase ml-1">USD</span>
                                </div>
                            </div>

                            {{-- Conversión BCV --}}
                            <div class="bg-gradient-to-r from-[#e5f2fb] to-slate-50 rounded-2xl p-3 border border-[#aad6f3]/60 flex justify-between items-center">
                                <div>
                                    <span class="text-[10px] font-black text-[#1e549e] uppercase tracking-wider block">Equivalente BCV</span>
                                    <span class="text-[10px] text-slate-400 font-medium">Monto oficial en Bolívares</span>
                                </div>
                                <span class="text-base font-black text-slate-800">
                                    Bs. {{ number_format($pedido->total_ves_calculado, 2, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        {{-- Botón Ticket / Factura --}}
                        @if(in_array($pedido->estado, ['pagado', 'aprobado', 'completado_caja', 'en_ruta', 'entregado']))
                            <a href="{{ route('perfil.pedido.ticket', $pedido->id) }}" 
                               target="_blank" 
                               class="w-full mt-4 py-3 bg-[#1e549e] hover:bg-[#3492cf] text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 active:scale-95">
                                <span class="material-symbols-outlined text-[18px]">receipt</span>
                                <span>Ver Factura / Ticket Térmico</span>
                            </a>
                        @endif

                        {{-- Botón de Soporte WhatsApp --}}
                        <a href="https://wa.me/584129614615?text={{ urlencode('Hola, Agrosuministros Rodríguez. Necesito asistencia con mi pedido #' . $pedido->id) }}" 
                           target="_blank" 
                           class="w-full py-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs rounded-xl border border-emerald-200 transition-colors flex items-center justify-center gap-2 shadow-2xs mt-2">
                            <span class="material-symbols-outlined text-[18px]">support_agent</span>
                            <span>¿Dudas con este pedido? WhatsApp</span>
                        </a>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

{{-- MODAL LIGHTBOX PARA VER COMPROBANTE --}}
<div id="modal-imagen" class="fixed inset-0 z-[150] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true" onclick="window.PedidoConfig.cerrarModalImagen()">
    <div class="fixed inset-0 bg-[#0b1d38]/80 backdrop-blur-xs transition-opacity opacity-0" id="modal-img-backdrop"></div>
    
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all opacity-0 scale-95 max-w-3xl border border-slate-100" id="modal-img-panel" onclick="event.stopPropagation()">
                
                <div class="bg-white px-5 py-3.5 border-b border-slate-100 flex justify-between items-center">
                    <span class="text-xs font-black text-slate-800 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[#1e549e] text-[18px]">receipt</span>
                        Comprobante de Pago Adjunto
                    </span>
                    <button type="button" onclick="window.PedidoConfig.cerrarModalImagen()" class="size-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center transition-colors">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>

                <div class="p-4 bg-slate-900 flex items-center justify-center max-h-[75vh] overflow-hidden">
                    <img id="imagen-modal-src" src="" alt="Comprobante de pago" class="max-w-full max-h-[70vh] object-contain rounded-xl">
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                    <a id="link-descarga" href="" download class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#1e549e] text-white text-xs font-bold rounded-xl hover:bg-[#3492cf] transition-colors shadow-2xs">
                        <span class="material-symbols-outlined text-[16px]">download</span>
                        Descargar Imagen
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.PedidoConfig = (function() {
        function abrirModalImagen(url) {
            const modal = document.getElementById('modal-imagen');
            const backdrop = document.getElementById('modal-img-backdrop');
            const panel = document.getElementById('modal-img-panel');
            const img = document.getElementById('imagen-modal-src');
            const link = document.getElementById('link-descarga');

            if (!modal || !img) return;

            img.src = url;
            if (link) link.href = url;

            modal.classList.remove('hidden');
            setTimeout(() => {
                if (backdrop) backdrop.classList.remove('opacity-0');
                if (panel) {
                    panel.classList.remove('opacity-0', 'scale-95');
                    panel.classList.add('opacity-100', 'scale-100');
                }
            }, 10);
        }

        function cerrarModalImagen() {
            const modal = document.getElementById('modal-imagen');
            const backdrop = document.getElementById('modal-img-backdrop');
            const panel = document.getElementById('modal-img-panel');

            if (!modal) return;

            if (backdrop) backdrop.classList.add('opacity-0');
            if (panel) {
                panel.classList.remove('opacity-100', 'scale-100');
                panel.classList.add('opacity-0', 'scale-95');
            }

            setTimeout(() => {
                modal.classList.add('hidden');
                const img = document.getElementById('imagen-modal-src');
                if (img) img.src = '';
            }, 250);
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                cerrarModalImagen();
            }
        });

        return {
            abrirModalImagen,
            cerrarModalImagen
        };
    })();
</script>
@endpush