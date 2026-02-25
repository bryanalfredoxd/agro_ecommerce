<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50/50">
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest w-16">ID</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Marca</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">País de Origen</th>
                
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($marcas as $marca)
            <tr class="hover:bg-gray-50/50 transition-colors group">
                <td class="px-6 py-4 text-xs font-bold text-gray-400">#{{ $marca->id }}</td>
                <td class="px-6 py-4">
                    <p class="font-black text-agro-dark uppercase tracking-wide">{{ $marca->nombre }}</p>
                </td>
                <td class="px-6 py-4">
                    @if($marca->pais_origen)
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 bg-white border border-gray-200 px-3 py-1 rounded-lg shadow-sm">
                            <span class="material-symbols-outlined text-[16px] text-gray-400">public</span>
                            {{ $marca->pais_origen }}
                        </span>
                    @else
                        <span class="text-xs font-bold text-gray-400 italic">No especificado</span>
                    @endif
                </td>
                
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <button onclick="openModal({{ $marca->toJson() }})" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 flex items-center justify-center transition-all shadow-sm">
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                        </button>
                        <button onclick="deleteMarca({{ $marca->id }})" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-red-600 hover:border-red-200 hover:bg-red-50 flex items-center justify-center transition-all shadow-sm">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                    <span class="material-symbols-outlined text-4xl mb-2 text-gray-300">sell</span>
                    <p class="font-bold">No hay marcas registradas</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="px-6 py-4 border-t border-gray-100 flex justify-center ajax-pagination">
    {{ $marcas->links('pagination::tailwind') }}
</div>