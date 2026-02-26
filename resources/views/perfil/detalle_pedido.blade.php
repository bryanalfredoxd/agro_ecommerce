@extends('layouts.app')

@section('title', 'Detalle del Pedido #' . $pedido->id . ' - Corpo Agrícola')

@section('content')
<div class="bg-gray-50 min-h-screen font-sans pb-20 animate-fade-in-up">

    {{-- CABECERA STICKY (Navegación) --}}
    <div class="bg-white/95 backdrop-blur-md shadow-sm border-b border-gray-100 sticky top-0 z-30">
        <div class="layout-container px-4 sm:px-0 h-16 flex items-center justify-between">
            <a href="{{ route('perfil.pedidos') }}" class="flex items-center gap-2 text-gray-500 hover:text-primary transition-colors font-bold text-sm group">
                <span class="material-symbols-outlined text-[20px] group-hover:-translate-x-1 transition-transform">arrow_back</span>
                <span class="hidden sm:inline">Volver a mis pedidos</span>
                <span class="sm:hidden">Volver</span>
            </a>
            <div class="flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">
                <span class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Orden</span>
                <span class="text-base font-black text-agro-dark">#{{ $pedido->id }}</span>
            </div>
        </div>
    </div>

    <div class="layout-container px-4 sm:px-0 py-8 lg:py-10">
        
        {{-- ENCABEZADO: Título, Fecha y Estado --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-black text-agro-dark tracking-tight mb-1">Detalles de la Orden</h1>
                <p class="text-sm font-medium text-gray-500 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                    Realizado el {{ \Carbon\Carbon::parse($pedido->creado_at)->format('d F Y, h:i A') }}
                </p>
            </div>
            
            @php
                $estadoStyles = match($pedido->estado) {
                    'pendiente' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'icon' => 'hourglass_empty'],
                    'pagado', 'aprobado', 'completado_caja' => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'border' => 'border-green-200', 'icon' => 'verified'],
                    'revision' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'icon' => 'search'],
                    'rechazado', 'cancelado', 'devuelto' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'icon' => 'cancel'],
                    'en_ruta', 'preparacion' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'icon' => 'local_shipping'],
                    'entregado' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'border' => 'border-gray-300', 'icon' => 'package_2'],
                    default => ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'border' => 'border-gray-200', 'icon' => 'info']
                };
            @endphp
            
            <div class="inline-flex self-start md:self-auto items-center gap-2 px-4 py-2.5 rounded-xl {{ $estadoStyles['bg'] }} {{ $estadoStyles['text'] }} border {{ $estadoStyles['border'] }} shadow-sm">
                <span class="material-symbols-outlined text-[20px]">{{ $estadoStyles['icon'] }}</span>
                <span class="font-black uppercase text-xs tracking-wider">{{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}</span>
            </div>
        </div>

        {{-- ALERTA: Motivo Devolución / Cancelación --}}
        @if(in_array($pedido->estado, ['rechazado', 'cancelado', 'devuelto']) && $pedido->motivo_devolucion)
            <div class="mb-8 bg-red-50 border-l-4 border-red-500 p-5 rounded-r-2xl animate-pulse-slow shadow-sm">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-red-500 text-[28px]">error</span>
                    <div>
                        <h3 class="text-sm font-black text-red-800 uppercase tracking-wider mb-1">
                            Motivo de la cancelación / devolución
                        </h3>
                        <p class="text-sm text-red-700 font-medium leading-relaxed">
                            {{ $pedido->motivo_devolucion }}
                        </p>
                        <p class="mt-2 text-xs font-bold text-red-500 flex items-center gap-1 hover:underline cursor-pointer">
                            <span class="material-symbols-outlined text-[14px]">support_agent</span>
                            ¿Crees que es un error? Contáctanos.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- GRID PRINCIPAL (Misma proporción que Checkout: 7/5 o 8/4) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- COLUMNA IZQUIERDA: Productos y Entrega --}}
            <div class="lg:col-span-7 xl:col-span-8 space-y-6">
                
                {{-- Tarjeta de Productos --}}
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="text-lg font-black text-agro-dark flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[24px]">shopping_cart</span>
                            Artículos del Pedido ({{ $pedido->detalles->count() }})
                        </h3>
                    </div>
                    
                    <div class="divide-y divide-gray-100">
                        @foreach($pedido->detalles as $detalle)
                            <div class="p-5 sm:p-6 flex flex-col sm:flex-row gap-5 items-start sm:items-center hover:bg-gray-50/30 transition-colors">
                                
                                {{-- Imagen --}}
                                <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gray-50 rounded-2xl flex-shrink-0 overflow-hidden border border-gray-200 relative">
                                    @if($detalle->producto && $detalle->producto->imagen_url)
                                        <img src="{{ asset($detalle->producto->imagen_url) }}" 
                                            alt="{{ $detalle->producto->nombre ?? 'Producto' }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                                            <span class="material-symbols-outlined text-[28px] mb-1">image_not_supported</span>
                                            <span class="text-[10px] font-bold uppercase tracking-widest">Sin Imagen</span>
                                        </div>
                                    @endif
                                </div>
                                
                                {{-- Info Principal --}}
                                <div class="flex-1 min-w-0 w-full">
                                    <p class="font-bold text-agro-dark text-base sm:text-lg leading-tight mb-1">
                                        {{ $detalle->producto ? $detalle->producto->nombre : 'Producto no disponible' }}
                                    </p>
                                    <div class="flex items-center gap-3 mb-2">
                                        <p class="text-sm font-black text-primary">${{ number_format($detalle->precio_historico_usd, 2) }} <span class="text-[10px] font-bold text-gray-400 uppercase">c/u</span></p>
                                        <span class="inline-block text-[11px] font-bold text-gray-600 bg-gray-100 px-2 py-0.5 rounded-lg">Cant: {{ (int)$detalle->cantidad_solicitada }}</span>
                                    </div>
                                    
                                    {{-- Observación del producto --}}
                                    @if($detalle->observaciones)
                                        <div class="inline-flex items-start gap-1.5 bg-orange-50 border border-orange-100 px-3 py-2 rounded-xl mt-1">
                                            <span class="material-symbols-outlined text-orange-500 text-[16px] mt-0.5">speaker_notes</span>
                                            <p class="text-xs text-orange-800 font-medium leading-snug">
                                                <span class="font-bold uppercase tracking-wider text-[10px] block mb-0.5">Nota:</span>
                                                {{ $detalle->observaciones }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                                
                                {{-- Total del Item --}}
                                <div class="w-full sm:w-auto text-right border-t border-gray-100 sm:border-0 pt-3 sm:pt-0 mt-2 sm:mt-0 flex justify-between sm:block">
                                    <span class="text-xs font-bold text-gray-400 uppercase sm:hidden">Subtotal</span>
                                    <p class="font-black text-agro-dark text-lg">
                                        ${{ number_format($detalle->cantidad_solicitada * $detalle->precio_historico_usd, 2) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Tarjeta de Entrega / Notas --}}
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden p-6 lg:p-8">
                    <h3 class="text-lg font-black text-agro-dark mb-5 flex items-center gap-2">
                        <span class="material-symbols-outlined text-orange-500 text-[24px]">local_shipping</span>
                        Información de Entrega
                    </h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                            <p class="text-[11px] font-black text-gray-400 uppercase tracking-wider mb-2 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">location_on</span> Dirección de Destino
                            </p>
                            <p class="text-sm text-gray-700 font-medium leading-relaxed">{{ $pedido->direccion_texto }}</p>
                        </div>
                        
                        @if($pedido->instrucciones_entrega)
                        <div class="bg-blue-50/50 p-4 rounded-2xl border border-blue-100">
                            <p class="text-[11px] font-black text-blue-400 uppercase tracking-wider mb-2 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">comment</span> Notas del Pedido
                            </p>
                            <p class="text-sm text-blue-800 font-medium italic leading-relaxed">
                                "{{ $pedido->instrucciones_entrega }}"
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- COLUMNA DERECHA: Resumen Financiero (STICKY) --}}
            <div class="lg:col-span-5 xl:col-span-4 space-y-6 lg:sticky lg:top-24">
                
                {{-- Tarjeta Resumen --}}
                <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.06)] border border-gray-100 overflow-hidden">
                    <div class="p-6 bg-agro-dark text-white relative overflow-hidden">
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-primary/20 rounded-full blur-2xl"></div>
                        <h3 class="text-xl font-black flex items-center gap-2 relative z-10">
                            <span class="material-symbols-outlined text-[24px]">receipt_long</span>
                            Resumen de Pago
                        </h3>
                    </div>
                    
                    <div class="p-6 lg:p-8 space-y-4">
                        <div class="flex justify-between items-center text-sm text-gray-500 font-medium">
                            <span>Subtotal</span>
                            <span class="text-gray-800 font-bold">${{ number_format($pedido->subtotal_usd, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm text-gray-500 font-medium">
                            <span>Envío</span>
                            <span class="text-gray-800 font-bold">{{ $pedido->costo_delivery_usd > 0 ? '$'.number_format($pedido->costo_delivery_usd, 2) : 'Gratis' }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm text-gray-500 font-medium">
                            <span>Impuestos (IVA 16%)</span>
                            <span class="text-gray-800 font-bold">${{ number_format($pedido->total_usd - $pedido->subtotal_usd - $pedido->costo_delivery_usd, 2) }}</span>
                        </div>
                        
                        <div class="border-t border-dashed border-gray-200 my-4 pt-4">
                            <div class="flex justify-between items-end mb-4">
                                <span class="font-black text-agro-dark text-lg">Total Pagado</span>
                                <div class="text-right">
                                    <span class="block font-black text-agro-dark text-3xl leading-none">${{ number_format($pedido->total_usd, 2) }}</span>
                                    <span class="block text-[10px] font-bold text-gray-400 uppercase mt-1">USD</span>
                                </div>
                            </div>
                            
                            <div class="bg-primary/5 border border-primary/10 p-3 rounded-xl flex justify-between items-center">
                                <span class="text-[10px] font-black text-agro-dark uppercase tracking-wider">Total Equivalente</span>
                                <span class="text-sm font-black text-primary">Bs {{ number_format($pedido->total_ves_calculado, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tarjeta de Pago Registrado --}}
                @if($pedido->pago)
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 lg:p-8">
                    <h3 class="text-lg font-black text-agro-dark mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-green-500 text-[24px]">payments</span>
                        Pago Registrado
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-100 rounded-xl">
                            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Método</span>
                            <span class="text-sm font-bold text-gray-800 flex items-center gap-1.5">
                                @php
                                    $iconoPago = match($pedido->pago->metodo) {
                                        'pago_movil' => 'phone_iphone', 'zelle' => 'attach_money',
                                        'efectivo_usd', 'efectivo_bs' => 'payments', 'transferencia' => 'account_balance',
                                        default => 'credit_card'
                                    };
                                @endphp
                                <span class="material-symbols-outlined text-[18px] text-primary">{{ $iconoPago }}</span>
                                {{ ucfirst(str_replace('_', ' ', $pedido->pago->metodo)) }}
                            </span>
                        </div>
                        
                        @if($pedido->pago->referencia_bancaria)
                        <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-100 rounded-xl">
                            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Referencia</span>
                            <span class="text-sm font-mono font-black text-gray-700 bg-white px-2 py-0.5 rounded shadow-sm">{{ $pedido->pago->referencia_bancaria }}</span>
                        </div>
                        @endif

                        @if($pedido->pago->captura_pago_url)
                        <div class="pt-3">
                            <button onclick="window.PedidoConfig.abrirModalImagen('{{ asset($pedido->pago->captura_pago_url) }}')" class="w-full py-3.5 bg-blue-50 text-blue-700 rounded-xl text-sm font-bold hover:bg-blue-600 hover:text-white transition-colors border border-blue-200 flex items-center justify-center gap-2 shadow-sm group">
                                <span class="material-symbols-outlined text-[20px] group-hover:scale-110 transition-transform">image</span> 
                                Ver Comprobante Adjunto
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Botón de Soporte --}}
                <a href="https://wa.me/584120000000?text=Hola,%20necesito%20ayuda%20con%20mi%20pedido%20%23{{ $pedido->id }}" target="_blank" class="w-full py-4 bg-white border-2 border-green-500 text-green-600 font-black rounded-2xl hover:bg-green-500 hover:text-white transition-all flex items-center justify-center gap-2 shadow-sm group">
                    <span class="material-symbols-outlined text-[24px] group-hover:animate-bounce">support_agent</span>
                    ¿Necesitas ayuda con esta orden?
                </a>

            </div>
        </div>
    </div>
</div>

{{-- MODAL PARA VER IMAGEN (CAPTURE) --}}
<div id="modal-imagen" class="fixed inset-0 z-[150] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true" onclick="window.PedidoConfig.cerrarModalImagen()">
    <div class="fixed inset-0 bg-agro-dark/80 backdrop-blur-md transition-opacity opacity-0" id="modal-img-backdrop"></div>
    
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            
            <div class="relative transform overflow-hidden rounded-2xl bg-transparent text-left shadow-2xl transition-all opacity-0 scale-95 max-w-4xl" id="modal-img-panel" onclick="event.stopPropagation()">
                
                {{-- Botón cerrar flotante y elegante --}}
                <button type="button" onclick="window.PedidoConfig.cerrarModalImagen()" class="absolute top-4 right-4 bg-black/60 text-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-red-500 transition-colors z-20 backdrop-blur-sm border border-white/20">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>

                <img id="imagen-modal-src" src="" alt="Comprobante de pago" class="max-w-full max-h-[85vh] object-contain rounded-2xl shadow-2xl ring-1 ring-white/10">
                
                <div class="mt-4 text-center">
                    <a id="link-descarga" href="" download class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white px-6 py-2.5 rounded-full text-sm font-bold backdrop-blur-sm transition-colors border border-white/20">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        Descargar Comprobante
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

            img.src = url;
            link.href = url;

            modal.classList.remove('hidden');
            setTimeout(() => {
                backdrop.classList.remove('opacity-0');
                panel.classList.remove('opacity-0', 'scale-95');
                panel.classList.add('opacity-100', 'scale-100');
            }, 10);
        }

        function cerrarModalImagen() {
            const modal = document.getElementById('modal-imagen');
            const backdrop = document.getElementById('modal-img-backdrop');
            const panel = document.getElementById('modal-img-panel');

            backdrop.classList.add('opacity-0');
            panel.classList.remove('opacity-100', 'scale-100');
            panel.classList.add('opacity-0', 'scale-95');

            setTimeout(() => {
                modal.classList.add('hidden');
                document.getElementById('imagen-modal-src').src = '';
            }, 300);
        }

        // Cerrar con tecla Escape
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