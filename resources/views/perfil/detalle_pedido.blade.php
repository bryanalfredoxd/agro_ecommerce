@extends('layouts.app')

@section('title', 'Detalle del Pedido #' . $pedido->id)

@section('content')
<div class="bg-gray-50 min-h-screen font-sans pb-10 animate-fade-in-up">

    {{-- Header Simple con Botón Volver --}}
    <div class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-30">
        <div class="container mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('perfil.pedidos') }}" class="flex items-center gap-2 text-gray-500 hover:text-primary transition-colors font-bold text-sm">
                <span class="material-symbols-outlined text-lg">arrow_back</span>
                <span class="hidden sm:inline">Volver a mis pedidos</span>
                <span class="sm:hidden">Volver</span>
            </a>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pedido</span>
                <span class="text-xl font-black text-agro-dark">#{{ $pedido->id }}</span>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6 sm:py-8">
        
        {{-- Encabezado: Estado y Fecha --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-black text-gray-800">Detalles de la Orden</h1>
                <p class="text-sm text-gray-500">Realizado el {{ \Carbon\Carbon::parse($pedido->creado_at)->format('d F Y, h:i A') }}</p>
            </div>
            
            @php
                $estadoStyles = match($pedido->estado) {
                    'pendiente' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'hourglass_empty'],
                    'pagado', 'aprobado', 'completado_caja' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'verified'],
                    'revision' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'search'],
                    'rechazado', 'cancelado', 'devuelto' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'cancel'],
                    'en_ruta', 'preparacion' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'icon' => 'local_shipping'],
                    'entregado' => ['bg' => 'bg-gray-200', 'text' => 'text-gray-800', 'icon' => 'package_2'],
                    default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'icon' => 'info']
                };
            @endphp
            
            <div class="inline-flex self-start sm:self-auto items-center gap-2 px-4 py-2 rounded-xl {{ $estadoStyles['bg'] }} {{ $estadoStyles['text'] }} border border-transparent shadow-sm">
                <span class="material-symbols-outlined">{{ $estadoStyles['icon'] }}</span>
                <span class="font-bold uppercase text-sm tracking-wide">{{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}</span>
            </div>
        </div>

        {{-- BLOQUE: Motivo Devolución (Si el admin rechazó el pedido globalmente) --}}
        @if(in_array($pedido->estado, ['rechazado', 'cancelado', 'devuelto']) && $pedido->motivo_devolucion)
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl animate-pulse-slow">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <span class="material-symbols-outlined text-red-500 text-2xl">error</span>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-bold text-red-800 uppercase tracking-wide">
                            Motivo de la cancelación / devolución
                        </h3>
                        <div class="mt-1 text-sm text-red-700 font-medium">
                            {{ $pedido->motivo_devolucion }}
                        </div>
                        <p class="mt-2 text-xs text-red-600">
                            Si crees que es un error, por favor contáctanos.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- COLUMNA IZQUIERDA: Productos y Entrega --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Tarjeta de Productos --}}
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">shopping_cart</span>
                            Productos ({{ $pedido->detalles->count() }})
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($pedido->detalles as $detalle)
                            <div class="p-4 flex gap-4 items-start hover:bg-gray-50 transition-colors">
                                {{-- Imagen --}}
                                <div class="w-16 h-16 bg-gray-100 rounded-xl flex-shrink-0 overflow-hidden border border-gray-200 relative">
                                    @if($detalle->producto && $detalle->producto->imagenes->first())
                                        <img src="{{ $detalle->producto->imagenes->first()->url_imagen }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300"><span class="material-symbols-outlined">image</span></div>
                                    @endif
                                </div>
                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-gray-800 text-sm sm:text-base truncate">
                                        {{ $detalle->producto ? $detalle->producto->nombre : 'Producto no disponible' }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">Precio Unitario: ${{ number_format($detalle->precio_historico_usd, 2) }}</p>
                                    
                                    {{-- === AQUÍ ESTÁ EL CAMBIO: OBSERVACIÓN ESPECÍFICA POR PRODUCTO === --}}
                                    {{-- Esto viene de la tabla 'pedido_detalles', columna 'observaciones' --}}
                                    @if($detalle->observaciones)
                                        <div class="mt-2 flex items-start gap-1.5">
                                            <span class="material-symbols-outlined text-orange-500 text-[16px] mt-0.5">info</span>
                                            <p class="text-xs text-orange-800 bg-orange-50 px-2 py-1 rounded-lg border border-orange-100 inline-block font-medium">
                                                Nota: {{ $detalle->observaciones }}
                                            </p>
                                        </div>
                                    @endif
                                    {{-- ============================================================== --}}

                                </div>
                                {{-- Cantidad y Total --}}
                                <div class="text-right">
                                    <span class="block text-xs font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded-lg mb-1 inline-block">x{{ (float)$detalle->cantidad_solicitada }}</span>
                                    <p class="font-bold text-agro-dark text-sm sm:text-base">
                                        ${{ number_format($detalle->cantidad_solicitada * $detalle->precio_historico_usd, 2) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Tarjeta de Entrega / Observaciones Globales --}}
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden p-6">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-orange-500">location_on</span>
                        Información de Entrega
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase mb-1">Dirección</p>
                            <p class="text-gray-700 font-medium">{{ $pedido->direccion_texto }}</p>
                        </div>
                        
                        {{-- Aquí mostramos lo que el usuario escribió en 'instrucciones_entrega' del checkout --}}
                        @if($pedido->instrucciones_entrega)
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase mb-1">Tus indicaciones de entrega</p>
                            <div class="bg-yellow-50 text-yellow-800 p-3 rounded-xl border border-yellow-100 italic text-xs">
                                <span class="font-bold not-italic text-yellow-900 block mb-1">Nota:</span> 
                                "{{ $pedido->instrucciones_entrega }}"
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- COLUMNA DERECHA: Resumen Financiero --}}
            <div class="lg:col-span-1 space-y-6">
                
                {{-- Tarjeta Totales --}}
                <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="p-5 bg-agro-dark text-white">
                        <h3 class="font-bold flex items-center gap-2">
                            <span class="material-symbols-outlined">receipt_long</span>
                            Resumen de Pago
                        </h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-medium">${{ number_format($pedido->subtotal_usd, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Envío</span>
                            <span class="font-medium">{{ $pedido->costo_delivery_usd > 0 ? '$'.number_format($pedido->costo_delivery_usd, 2) : 'Gratis' }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Impuestos/IVA</span>
                            {{-- Calculamos IVA aproximado restando --}}
                            <span class="font-medium">${{ number_format($pedido->total_usd - $pedido->subtotal_usd - $pedido->costo_delivery_usd, 2) }}</span>
                        </div>
                        
                        <div class="border-t border-gray-100 my-2 pt-2">
                            <div class="flex justify-between items-end">
                                <span class="font-black text-gray-800 text-lg">Total USD</span>
                                <span class="font-black text-agro-dark text-2xl">${{ number_format($pedido->total_usd, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center mt-2 text-sm text-gray-500 bg-gray-50 p-2 rounded-lg">
                                <span>Total en Bolívares</span>
                                <span class="font-bold text-gray-700">Bs {{ number_format($pedido->total_ves_calculado, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Datos del Pago Realizado --}}
                @if($pedido->pago)
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-green-600">payments</span>
                        Pago Registrado
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <span class="text-xs font-bold text-gray-500 uppercase">Método</span>
                            <span class="font-bold text-gray-800 flex items-center gap-1">
                                {{ ucfirst(str_replace('_', ' ', $pedido->pago->metodo)) }}
                            </span>
                        </div>
                        
                        @if($pedido->pago->referencia_bancaria)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <span class="text-xs font-bold text-gray-500 uppercase">Referencia</span>
                            <span class="font-mono font-bold text-gray-800">{{ $pedido->pago->referencia_bancaria }}</span>
                        </div>
                        @endif

                        @if($pedido->pago->captura_pago_url)
                        <div class="pt-2">
                            <p class="text-xs font-bold text-gray-500 uppercase mb-2">Comprobante Adjunto</p>
                            
                            {{-- BOTÓN QUE ABRE EL MODAL --}}
                            <button onclick="abrirModalImagen('{{ asset('storage/' . $pedido->pago->captura_pago_url) }}')" class="w-full py-2 bg-blue-50 text-blue-600 text-center rounded-xl text-xs font-bold hover:bg-blue-100 transition-colors border border-blue-200 flex items-center justify-center gap-1">
                                <span class="material-symbols-outlined text-base">image</span> Ver Capture
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Botón de Ayuda --}}
                <div class="text-center mt-4">
                    <a href="https://wa.me/584120000000?text=Hola,%20tengo%20una%20duda%20con%20el%20pedido%20{{ $pedido->id }}" target="_blank" class="inline-flex items-center gap-2 text-green-600 font-bold hover:underline text-sm bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100">
                        <span class="material-symbols-outlined">support_agent</span>
                        ¿Ayuda con este pedido?
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- MODAL PARA VER IMAGEN (CAPTURE) --}}
<div id="modal-imagen" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true" onclick="cerrarModalImagen()">
    {{-- Fondo oscuro con desenfoque --}}
    <div class="fixed inset-0 bg-gray-900/90 backdrop-blur-sm transition-opacity opacity-0" id="modal-img-backdrop"></div>
    
    {{-- Contenedor de la imagen --}}
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            
            <div class="relative transform overflow-hidden rounded-lg bg-transparent text-left shadow-xl transition-all opacity-0 scale-95" id="modal-img-panel" onclick="event.stopPropagation()">
                
                {{-- Botón cerrar flotante --}}
                <button type="button" onclick="cerrarModalImagen()" class="absolute top-2 right-2 bg-black/50 text-white rounded-full p-1 hover:bg-black/70 transition-colors z-20">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>

                <img id="imagen-modal-src" src="" alt="Comprobante de pago" class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl">
                
                <div class="mt-2 text-center">
                    <a id="link-descarga" href="" download class="inline-block text-white text-xs hover:underline opacity-80">
                        Descargar imagen original
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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

    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            cerrarModalImagen();
        }
    });
</script>
@endsection