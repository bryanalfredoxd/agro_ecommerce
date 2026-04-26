{{-- Información del Cliente y Envío --}}
<div class="bg-blue-50/50 p-5 border-b border-blue-100 flex flex-col sm:flex-row justify-between gap-4">
    <div>
        <h4 class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">Datos del Cliente</h4>
        <p class="font-bold text-agro-dark text-sm capitalize">{{ $pedido->usuario->nombre ?? 'Anónimo' }} {{ $pedido->usuario->apellido ?? '' }}</p>
        <p class="text-xs text-gray-500 mt-0.5"><span class="font-medium text-gray-700">CI/RIF:</span> {{ $pedido->usuario->documento_identidad ?? 'N/A' }}</p>
        <p class="text-xs text-gray-500"><span class="font-medium text-gray-700">Tel:</span> {{ $pedido->usuario->telefono ?? 'N/A' }}</p>
    </div>
    <div class="sm:text-right">
        <h4 class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">Entrega</h4>
        @if($pedido->canal_venta === 'web' || $pedido->canal_venta === 'whatsapp')
            <p class="font-bold text-agro-dark text-sm line-clamp-2 max-w-[250px] sm:ml-auto">{{ $pedido->direccion_texto ?? 'Retiro en Tienda' }}</p>
            @if($pedido->instrucciones_entrega)
                <p class="text-xs text-orange-600 font-medium mt-1 bg-orange-50 px-2 py-1 rounded inline-block"><span class="font-bold">Nota:</span> {{ $pedido->instrucciones_entrega }}</p>
            @endif
        @else
            <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs font-bold border border-gray-200">
                <span class="material-symbols-outlined text-[14px]">storefront</span> Venta en Mostrador
            </span>
        @endif
    </div>
</div>

{{-- Lista de Productos a Empacar --}}
<div class="p-6">
    <h4 class="text-[10px] font-black text-gray-700 uppercase tracking-widest mb-3">Artículos a Empacar</h4>
    
    <div class="space-y-3">
        @foreach($pedido->detalles as $detalle)
            <div class="flex items-center gap-4 p-3 rounded-xl border border-gray-100 hover:bg-gray-50 transition-colors">
                
                {{-- Imagen miniatura --}}
                <div class="w-12 h-12 rounded-lg bg-white border border-gray-200 flex items-center justify-center overflow-hidden shrink-0">
                    @if($detalle->producto && $detalle->producto->imagen_url)
                        <img src="{{ asset($detalle->producto->imagen_url) }}" class="w-full h-full object-cover">
                    @else
                        <span class="material-symbols-outlined text-gray-300">image</span>
                    @endif
                </div>
                
                {{-- Info Producto --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-agro-dark truncate">{{ $detalle->producto->nombre ?? 'Producto Eliminado' }}</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-[10px] text-gray-700 font-medium">SKU: {{ $detalle->producto->sku ?? 'N/A' }}</span>
                        <span class="text-[10px] bg-gray-100 text-gray-500 px-1.5 rounded uppercase tracking-wider">{{ $detalle->producto->marca->nombre ?? 'GENÉRICO' }}</span>
                    </div>
                </div>
                
                {{-- Cantidad a despachar (MUY GRANDE) --}}
                <div class="text-right">
                    <p class="text-xs text-gray-700 font-bold mb-0.5">Cantidad</p>
                    <p class="text-xl font-black text-blue-600 leading-none">
                        {{ rtrim(rtrim(number_format($detalle->cantidad_solicitada, 3), '0'), '.') }} 
                        <span class="text-xs text-blue-400 uppercase tracking-widest">{{ $detalle->producto->unidad_medida ?? 'UND' }}</span>
                    </p>
                </div>

            </div>
        @endforeach
    </div>
</div>