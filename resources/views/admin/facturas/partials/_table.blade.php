<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50/50">
                <th class="px-6 py-4 text-[10px] font-black text-gray-700 uppercase tracking-widest">Nº Factura / Fecha</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-700 uppercase tracking-widest">Datos del Cliente</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-700 uppercase tracking-widest">Total (USD/VES)</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-700 uppercase tracking-widest text-center">Estado</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-700 uppercase tracking-widest text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($facturas as $factura)
            <tr class="hover:bg-gray-50/50 transition-colors group {{ $factura->estado == 'anulada' ? 'opacity-60 bg-red-50/20' : '' }}">
                
                {{-- Columna 1: Factura y Fecha --}}
                <td class="px-6 py-4">
                    <div class="flex flex-col gap-1">
                        <span class="font-black text-teal-700 text-sm flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">receipt</span>
                            {{ $factura->numero_factura }}
                        </span>
                        <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">{{ \Carbon\Carbon::parse($factura->fecha_emision)->format('d M Y, h:i A') }}</span>
                    </div>
                </td>

                {{-- Columna 2: Cliente --}}
                <td class="px-6 py-4">
                    <p class="font-bold text-gray-800 text-sm capitalize truncate max-w-[200px]">{{ $factura->nombre_razon_social ?? 'Consumidor Final' }}</p>
                    <p class="text-[11px] font-mono text-gray-500 mt-0.5"><span class="font-bold">CI/RIF:</span> {{ $factura->cedula_rif_cliente ?? 'N/A' }}</p>
                </td>

                {{-- Columna 3: Montos --}}
                <td class="px-6 py-4">
                    <p class="font-black text-agro-dark text-base">${{ number_format($factura->total_usd, 2) }}</p>
                    <p class="text-[10px] font-bold text-gray-700">Bs. {{ number_format($factura->total_ves, 2) }}</p>
                </td>

                {{-- Columna 4: Estado --}}
                <td class="px-6 py-4 text-center">
                    @php
                        $estadoBadge = match($factura->estado) {
                            'emitida' => 'bg-green-50 text-green-700 border-green-200',
                            'anulada' => 'bg-red-50 text-red-700 border-red-200',
                            'reembolsada' => 'bg-amber-50 text-amber-700 border-amber-200',
                            default => 'bg-gray-50 text-gray-700 border-gray-200',
                        };
                    @endphp
                    <span class="inline-block px-3 py-1 rounded-lg border text-[10px] font-black uppercase tracking-widest {{ $estadoBadge }}">
                        {{ $factura->estado }}
                    </span>
                </td>

                {{-- Columna 5: Acciones --}}
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        
                        {{-- Botón Ver Factura --}}
                        <button onclick="verFactura({{ $factura->id }})" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-teal-600 hover:border-teal-200 hover:bg-teal-50 flex items-center justify-center transition-all shadow-sm" title="Ver Documento">
                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                        </button>

                        {{-- Botón Anular (Solo si está emitida) --}}
                        @if($factura->estado === 'emitida')
                        <button onclick="anularFactura({{ $factura->id }}, '{{ $factura->numero_factura }}')" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-red-600 hover:border-red-200 hover:bg-red-50 flex items-center justify-center transition-all shadow-sm" title="Anular Factura">
                            <span class="material-symbols-outlined text-[18px]">cancel</span>
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-16 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                            <span class="material-symbols-outlined text-3xl text-gray-700">receipt_long</span>
                        </div>
                        <p class="font-bold text-gray-600">No hay facturas registradas</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="px-6 py-4 border-t border-gray-100 flex justify-center ajax-pagination">
    {{ $facturas->links('pagination::tailwind') }}
</div>