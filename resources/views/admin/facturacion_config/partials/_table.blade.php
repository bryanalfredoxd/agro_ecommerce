<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50/50">
                <th class="px-6 py-4 text-[10px] font-black text-gray-700 uppercase tracking-widest w-16">Estado</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-700 uppercase tracking-widest">Prefijo / Serie</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-700 uppercase tracking-widest">Siguiente Correlativo</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-700 uppercase tracking-widest">Impuesto Asignado</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-700 uppercase tracking-widest text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($ajustes as $ajuste)
            <tr class="hover:bg-gray-50/50 transition-colors group {{ !$ajuste->activo ? 'opacity-60 bg-gray-50' : '' }}">
                
                {{-- Toggle Switch --}}
                <td class="px-6 py-4">
                    <label class="relative inline-flex items-center cursor-pointer" title="{{ $ajuste->activo ? 'Desactivar' : 'Activar' }}">
                        <input type="checkbox" class="sr-only peer" {{ $ajuste->activo ? 'checked' : '' }} onchange="toggleSerie({{ $ajuste->id }})">
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-teal-500"></div>
                    </label>
                </td>

                {{-- Serie --}}
                <td class="px-6 py-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-sm font-black tracking-widest bg-gray-100 text-gray-800 border-gray-200">
                        {{ $ajuste->serie }}
                    </span>
                </td>

                {{-- Siguiente Correlativo --}}
                <td class="px-6 py-4">
                    <p class="font-mono text-base font-black text-teal-600">
                        {{ str_pad($ajuste->proximo_numero, 7, '0', STR_PAD_LEFT) }}
                    </p>
                    <p class="text-[10px] text-gray-700 font-bold mt-1">Siguiente factura a emitir</p>
                </td>

                {{-- Impuesto (IVA) --}}
                <td class="px-6 py-4">
                    <span class="font-black text-gray-700 bg-gray-100 px-2 py-1 rounded border border-gray-200">
                        {{ number_format($ajuste->porcentaje_iva, 2) }}%
                    </span>
                </td>

                {{-- Acciones --}}
                <td class="px-6 py-4 text-right">
                    <button onclick='openModal(@json($ajuste))' class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-teal-600 hover:border-teal-200 hover:bg-teal-50 flex items-center justify-center transition-all shadow-sm ml-auto">
                        <span class="material-symbols-outlined text-[18px]">edit</span>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                    <span class="material-symbols-outlined text-4xl mb-2 text-gray-300">settings</span>
                    <p class="font-bold">No hay series configuradas.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="px-6 py-4 border-t border-gray-100 flex justify-center ajax-pagination">
    {{ $ajustes->links('pagination::tailwind') }}
</div>