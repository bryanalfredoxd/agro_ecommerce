<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50/50">
                <th class="px-6 py-4 text-[10px] font-black text-gray-700 uppercase tracking-widest w-20">Imagen</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-700 uppercase tracking-widest">Categoría</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-700 uppercase tracking-widest">Jerarquía (Padre)</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-700 uppercase tracking-widest text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($categorias as $cat)
            <tr class="hover:bg-gray-50/50 transition-colors group {{ $cat->trashed() ? 'opacity-60 bg-gray-50/30' : '' }}">
                <td class="px-6 py-4">
                    <div class="w-12 h-12 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden flex items-center justify-center shadow-sm {{ $cat->trashed() ? 'grayscale' : '' }}">
                        @if($cat->imagen_url)
                            <img src="{{ asset($cat->imagen_url) }}" alt="{{ $cat->nombre }}" class="w-full h-full object-cover">
                        @else
                            <span class="material-symbols-outlined text-gray-700">image</span>
                        @endif
                    </div>
                </td>
                <td class="px-6 py-4">
                    <p class="font-bold text-agro-dark capitalize text-base flex items-center gap-2">
                        <span class="{{ $cat->trashed() ? 'line-through text-gray-700' : '' }}">{{ $cat->nombre }}</span>
                        @if($cat->trashed())
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black bg-red-100 text-red-600 uppercase tracking-widest">Inactiva</span>
                        @endif
                    </p>
                    <p class="text-[10px] text-gray-700 font-bold uppercase tracking-wider mt-0.5">ID: {{ $cat->id }}</p>
                </td>
                <td class="px-6 py-4">
                    @if($cat->padre)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 {{ $cat->trashed() ? 'bg-gray-100 text-gray-500 border-gray-200' : 'bg-green-50 text-green-700 border-green-100' }} text-[11px] font-bold rounded-lg border">
                            <span class="material-symbols-outlined text-[14px]">subdirectory_arrow_right</span>
                            Sub de: {{ $cat->padre->nombre }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 text-gray-600 text-[11px] font-bold rounded-lg border border-gray-200">
                            <span class="material-symbols-outlined text-[14px]">category</span>
                            Categoría Principal
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        @if($cat->trashed())
                            {{-- Botón Reactivar si está deshabilitada --}}
                            <button onclick="restoreCategoria({{ $cat->id }})" class="flex items-center gap-1 px-3 py-1.5 rounded-lg bg-green-50 border border-green-200 text-green-700 hover:bg-green-600 hover:text-white font-bold text-xs transition-all shadow-sm">
                                <span class="material-symbols-outlined text-[16px]">restore</span> Reactivar
                            </button>
                        @else
                            {{-- Botones normales si está activa --}}
                            <button onclick="openModal({{ $cat->toJson() }})" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 flex items-center justify-center transition-all shadow-sm" title="Editar">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </button>
                            <button onclick="deleteCategoria({{ $cat->id }})" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-orange-600 hover:border-orange-200 hover:bg-orange-50 flex items-center justify-center transition-all shadow-sm" title="Deshabilitar">
                                <span class="material-symbols-outlined text-[18px]">block</span>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-16 text-center text-gray-500">
                    <span class="material-symbols-outlined text-4xl mb-2 text-gray-300">category</span>
                    <p class="font-bold text-gray-600">No hay categorías registradas</p>
                    <p class="text-xs mt-1">Prueba ajustando los filtros.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="px-6 py-4 border-t border-gray-100 flex justify-center ajax-pagination">
    {{ $categorias->links('pagination::tailwind') }}
</div>