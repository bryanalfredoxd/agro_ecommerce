<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50/50">
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Fecha y Hora</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Producto afectado</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Variación (USD)</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Motivo / Autor</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($historicos as $hist)
            <tr class="hover:bg-gray-50/50 transition-colors group">
                
                <td class="px-6 py-4">
                    <p class="text-sm font-bold text-agro-dark">{{ \Carbon\Carbon::parse($hist->creado_at)->format('d M, Y') }}</p>
                    <p class="text-[10px] font-black text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($hist->creado_at)->format('h:i A') }}</p>
                </td>

                <td class="px-6 py-4">
                    <p class="text-sm font-bold text-agro-dark">{{ $hist->producto->nombre ?? 'Producto Eliminado' }}</p>
                    <p class="text-[10px] font-mono font-bold text-gray-400 mt-0.5">SKU: {{ $hist->producto->sku ?? 'N/A' }}</p>
                </td>

                <td class="px-6 py-4 text-center">
                    <div class="flex items-center justify-center gap-3">
                        <span class="text-gray-400 font-bold line-through text-sm">${{ number_format($hist->precio_anterior_usd, 2) }}</span>
                        
                        @if($hist->precio_nuevo_usd > $hist->precio_anterior_usd)
                            <span class="material-symbols-outlined text-green-500">trending_up</span>
                            <span class="font-black text-green-600 text-base">${{ number_format($hist->precio_nuevo_usd, 2) }}</span>
                        @else
                            <span class="material-symbols-outlined text-red-500">trending_down</span>
                            <span class="font-black text-red-600 text-base">${{ number_format($hist->precio_nuevo_usd, 2) }}</span>
                        @endif
                    </div>
                </td>

                <td class="px-6 py-4">
                    <p class="text-xs font-medium text-gray-700 bg-gray-50 p-2 rounded-lg border border-gray-100 italic">"{{ $hist->motivo_cambio }}"</p>
                    <p class="text-[9px] font-black text-blue-500 uppercase tracking-widest mt-2 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">shield_person</span> {{ $hist->editor->nombre ?? 'Sistema' }}
                    </p>
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                    <span class="material-symbols-outlined text-4xl mb-2 text-gray-300">history</span>
                    <p class="font-bold">No hay cambios de precio registrados.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="px-6 py-4 border-t border-gray-100 flex justify-center ajax-pagination">
    {{ $historicos->links('pagination::tailwind') }}
</div>