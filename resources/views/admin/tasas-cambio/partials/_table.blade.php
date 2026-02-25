<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50/50">
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Fecha y Hora</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Valor Tasa (Bs)</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Fuente</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Autor / Editor</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Impacto (Pedidos)</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($tasas as $tasa)
            <tr class="hover:bg-gray-50/50 transition-colors group">
                <td class="px-6 py-4">
                    <p class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($tasa->creado_at)->format('d/m/Y') }}</p>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">{{ \Carbon\Carbon::parse($tasa->creado_at)->format('h:i A') }}</p>
                </td>
                <td class="px-6 py-4">
                    <span class="text-base font-black text-teal-600 bg-teal-50 px-3 py-1 rounded-lg border border-teal-100">
                        {{ number_format($tasa->valor_tasa, 4) }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    @if($tasa->fuente === 'API')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 text-blue-700 text-[10px] font-black rounded-md border border-blue-200 uppercase tracking-wider">
                            <span class="material-symbols-outlined text-[14px]">smart_toy</span> API Automática
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-orange-50 text-orange-700 text-[10px] font-black rounded-md border border-orange-200 uppercase tracking-wider">
                            <span class="material-symbols-outlined text-[14px]">person_edit</span> Ingreso Manual
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    @if($tasa->fuente === 'API')
                        <span class="text-sm font-bold text-gray-400 italic">Sistema (Job Cron)</span>
                    @else
                        <p class="font-bold text-gray-800 capitalize">{{ $tasa->editor->nombre ?? 'Admin Desconocido' }}</p>
                        <p class="text-[10px] text-gray-500">ID: {{ $tasa->usuario_editor_id }}</p>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    <span class="inline-flex items-center justify-center gap-1.5 {{ $tasa->pedidos_count > 0 ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : 'bg-gray-100 text-gray-500 border border-transparent' }} px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">
                        <span class="material-symbols-outlined text-[16px]">shopping_cart</span>
                        {{ $tasa->pedidos_count }} Pedidos
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                    <span class="material-symbols-outlined text-4xl mb-2 text-gray-300">timeline</span>
                    <p class="font-bold">No hay registros de tasas de cambio.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="px-6 py-4 border-t border-gray-100 flex justify-center ajax-pagination">
    {{ $tasas->links('pagination::tailwind') }}
</div>