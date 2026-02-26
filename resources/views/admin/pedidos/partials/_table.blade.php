<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50/50">
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Pedido / Canal</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Cliente</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Total (USD/Bs)</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Estado</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($pedidos as $pedido)
            <tr class="hover:bg-gray-50/50 transition-colors group">
                
                {{-- Columna 1: Info del Pedido y Canal --}}
                <td class="px-6 py-4">
                    <div class="flex flex-col gap-1">
                        <span class="font-black text-agro-dark text-sm">#{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}</span>
                        <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">{{ \Carbon\Carbon::parse($pedido->creado_at)->format('d M Y, h:i A') }}</span>
                        <div class="mt-1">
                            @if($pedido->canal_venta === 'web')
                                <span class="inline-flex items-center gap-1 text-[9px] font-black text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-100 uppercase"><span class="material-symbols-outlined text-[12px]">language</span> Web</span>
                            @elseif($pedido->canal_venta === 'whatsapp')
                                <span class="inline-flex items-center gap-1 text-[9px] font-black text-green-600 bg-green-50 px-2 py-0.5 rounded border border-green-100 uppercase"><span class="material-symbols-outlined text-[12px]">chat</span> WhatsApp</span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[9px] font-black text-gray-600 bg-gray-100 px-2 py-0.5 rounded border border-gray-200 uppercase"><span class="material-symbols-outlined text-[12px]">storefront</span> Tienda Fís.</span>
                            @endif
                        </div>
                    </div>
                </td>

                {{-- Columna 2: Cliente --}}
                <td class="px-6 py-4">
                    @if($pedido->usuario)
                        <p class="font-bold text-gray-800 text-sm capitalize line-clamp-1">{{ $pedido->usuario->nombre }} {{ $pedido->usuario->apellido }}</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">{{ $pedido->usuario->documento_identidad ?? $pedido->usuario->email }}</p>
                    @else
                        <span class="text-xs text-gray-400 italic font-bold">Cliente Anónimo (POS)</span>
                    @endif
                </td>

                {{-- Columna 3: Montos --}}
                <td class="px-6 py-4">
                    <p class="font-black text-agro-dark text-base">${{ number_format($pedido->total_usd, 2) }}</p>
                    <p class="text-[10px] font-bold text-gray-400">Bs. {{ number_format($pedido->total_ves_calculado, 2) }}</p>
                </td>

                {{-- Columna 4: Estado (Badges Semánticos) --}}
                <td class="px-6 py-4 text-center">
                    @php
                        $clasesEstado = match($pedido->estado) {
                            'pendiente' => 'bg-amber-50 text-amber-600 border-amber-200',
                            'pagado' => 'bg-blue-50 text-blue-600 border-blue-200',
                            'preparacion' => 'bg-indigo-50 text-indigo-600 border-indigo-200',
                            'en_ruta' => 'bg-orange-50 text-orange-600 border-orange-200',
                            'entregado', 'completado_caja' => 'bg-green-50 text-green-600 border-green-200',
                            'devuelto', 'cancelado' => 'bg-red-50 text-red-600 border-red-200',
                            default => 'bg-gray-50 text-gray-600 border-gray-200',
                        };
                        $iconoEstado = match($pedido->estado) {
                            'pendiente' => 'hourglass_empty',
                            'pagado' => 'payments',
                            'preparacion' => 'box_add',
                            'en_ruta' => 'local_shipping',
                            'entregado', 'completado_caja' => 'check_circle',
                            'devuelto' => 'assignment_return',
                            'cancelado' => 'cancel',
                            default => 'info',
                        };
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-[10px] font-black uppercase tracking-wider {{ $clasesEstado }}">
                        <span class="material-symbols-outlined text-[14px]">{{ $iconoEstado }}</span>
                        {{ str_replace('_', ' ', $pedido->estado) }}
                    </span>
                </td>

                {{-- Columna 5: Acciones (Botones directos, sin solapamientos) --}}
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        {{-- Botón Cambiar Estado Rápido --}}
                        <button onclick="openStatusModal({{ $pedido->id }}, '{{ $pedido->estado }}')" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 flex items-center justify-center transition-all shadow-sm" title="Cambiar Estado">
                            <span class="material-symbols-outlined text-[18px]">sync_alt</span>
                        </button>
                        
                        {{-- Botón Ver Detalle Completo 
                        <a href="#" class="w-8 h-8 rounded-lg bg-agro-dark border border-transparent text-white hover:bg-black flex items-center justify-center transition-all shadow-sm" title="Ver Detalle del Pedido">
                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                        </a>
                        --}}
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-16 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                            <span class="material-symbols-outlined text-3xl text-gray-400">receipt_long</span>
                        </div>
                        <p class="font-bold text-gray-600">No se encontraron pedidos</p>
                        <p class="text-xs mt-1">Prueba ajustando los filtros de búsqueda.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="px-6 py-4 border-t border-gray-100 flex justify-center ajax-pagination">
    {{ $pedidos->links('pagination::tailwind') }}
</div>