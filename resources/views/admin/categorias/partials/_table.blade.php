<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50/75">
                <th class="px-5 py-4 text-[11px] font-black text-gray-500 uppercase tracking-wider w-16">Imagen</th>
                <th class="px-5 py-4 text-[11px] font-black text-gray-500 uppercase tracking-wider">Categoría & Jerarquía</th>
                <th class="px-5 py-4 text-[11px] font-black text-gray-500 uppercase tracking-wider text-center">Subcategorías</th>
                <th class="px-5 py-4 text-[11px] font-black text-gray-500 uppercase tracking-wider text-center">Productos</th>
                <th class="px-5 py-4 text-[11px] font-black text-gray-500 uppercase tracking-wider text-center">Estado</th>
                <th class="px-5 py-4 text-[11px] font-black text-gray-500 uppercase tracking-wider text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100/80 bg-white">
            @forelse($categorias as $categoria)
            @php
                $esEliminada = $categoria->trashed();
                $esSubcategoria = !is_null($categoria->categoria_padre_id);
            @endphp
            <tr class="hover:bg-blue-50/30 transition-colors group {{ $esEliminada ? 'bg-rose-50/20' : '' }}">
                
                {{-- Imagen --}}
                <td class="px-5 py-4">
                    <div class="size-12 rounded-xl bg-gray-50 border border-gray-200 overflow-hidden flex items-center justify-center shadow-2xs shrink-0 relative">
                        @if($categoria->imagen_url)
                            <img src="{{ asset($categoria->imagen_url) }}" alt="{{ $categoria->nombre }}" class="size-full object-cover">
                        @else
                            <span class="material-symbols-outlined text-gray-400 text-2xl">category</span>
                        @endif
                    </div>
                </td>

                {{-- Nombre y Jerarquía --}}
                <td class="px-5 py-4">
                    <div class="flex flex-col">
                        <span class="font-black text-gray-900 text-sm group-hover:text-blue-600 transition-colors {{ $esEliminada ? 'line-through text-gray-400' : '' }}">
                            {{ $categoria->nombre }}
                        </span>
                        
                        <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                            @if(!$esSubcategoria)
                                <span class="inline-flex items-center gap-1 text-[10px] font-black text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-200/60 uppercase">
                                    <span class="material-symbols-outlined text-[13px]">folder</span> Principal (Raíz)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">
                                    <span class="material-symbols-outlined text-[13px] text-blue-600">subdirectory_arrow_right</span>
                                    Rama de: <strong class="text-gray-900">{{ $categoria->padre->nombre ?? 'N/A' }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </td>

                {{-- Subcategorías --}}
                <td class="px-5 py-4 text-center">
                    @if(!$esSubcategoria)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-black {{ $categoria->subcategorias_count > 0 ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-gray-50 text-gray-400 border border-gray-200' }}">
                            <span class="material-symbols-outlined text-[14px]">account_tree</span>
                            {{ $categoria->subcategorias_count }} {{ $categoria->subcategorias_count == 1 ? 'rama' : 'ramas' }}
                        </span>
                    @else
                        <span class="text-xs text-gray-400 font-medium italic">Nivel final</span>
                    @endif
                </td>

                {{-- Productos Asociados --}}
                <td class="px-5 py-4 text-center">
                    @if($categoria->productos_count > 0)
                        <a href="{{ route('admin.productos.index', ['categoria_id' => $categoria->id]) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition-colors" title="Ver productos de esta categoría">
                            <span class="material-symbols-outlined text-[14px]">inventory_2</span>
                            {{ $categoria->productos_count }} {{ $categoria->productos_count == 1 ? 'artículo' : 'artículos' }}
                        </a>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold text-gray-400 bg-gray-50 border border-gray-200">
                            0 artículos
                        </span>
                    @endif
                </td>

                {{-- Estado --}}
                <td class="px-5 py-4 text-center">
                    @if(!$esEliminada)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                            <span class="size-1.5 rounded-full bg-emerald-600"></span>
                            Activa
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-black bg-rose-100 text-rose-800 border border-rose-300">
                            <span class="size-1.5 rounded-full bg-rose-600"></span>
                            Deshabilitada
                        </span>
                    @endif
                </td>

                {{-- Acciones --}}
                <td class="px-5 py-4 text-right">
                    @if(Auth::user()->tienePermiso('gestionar_categorias_marcas'))
                    <div class="flex items-center justify-end gap-1.5">
                        @if(!$esEliminada)
                            <button type="button" onclick="openModal({{ $categoria->toJson() }})" class="size-8 rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 flex items-center justify-center transition-all shadow-2xs hover:scale-105 active:scale-95" title="Editar Categoría">
                                <span class="material-symbols-outlined text-[17px]">edit</span>
                            </button>
                            
                            <button type="button" onclick="deleteCategoria({{ $categoria->id }}, '{{ addslashes($categoria->nombre) }}', {{ $categoria->subcategorias_count }})" class="size-8 rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-rose-600 hover:border-rose-200 hover:bg-rose-50 flex items-center justify-center transition-all shadow-2xs hover:scale-105 active:scale-95" title="Deshabilitar Categoría">
                                <span class="material-symbols-outlined text-[17px]">visibility_off</span>
                            </button>
                        @else
                            <button type="button" onclick="restoreCategoria({{ $categoria->id }}, '{{ addslashes($categoria->nombre) }}')" class="size-8 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all shadow-2xs hover:scale-105 active:scale-95" title="Reactivar Categoría">
                                <span class="material-symbols-outlined text-[17px]">settings_backup_restore</span>
                            </button>
                        @endif
                    </div>
                    @else
                    <span class="text-xs text-gray-400 italic">Solo lectura</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-16 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                        <div class="size-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-3 text-gray-400">
                            <span class="material-symbols-outlined text-3xl">category</span>
                        </div>
                        <h4 class="font-black text-gray-800 text-base">No hay categorías que coincidan</h4>
                        <p class="text-xs text-gray-500 mt-1">Prueba seleccionando otro filtro o crea una nueva categoría.</p>
                        @if(request('buscar') || (request('tipo') && request('tipo') !== 'todas') || (request('estado') && request('estado') !== 'activas'))
                        <button type="button" onclick="resetearTodosFiltros()" class="mt-4 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors">
                            <span class="material-symbols-outlined text-[16px]">filter_alt_off</span>
                            Limpiar filtros aplicados
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Paginación Ajax --}}
<div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 ajax-pagination bg-gray-50/50">
    <div class="text-xs text-gray-500 font-medium">
        Mostrando {{ $categorias->firstItem() ?? 0 }} a {{ $categorias->lastItem() ?? 0 }} de {{ $categorias->total() }} categorías registradas
    </div>
    <div>
        {{ $categorias->links('pagination::tailwind') }}
    </div>
</div>