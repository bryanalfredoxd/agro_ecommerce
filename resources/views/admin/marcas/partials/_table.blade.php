<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50/75">
                <th class="px-5 py-4 text-[11px] font-black text-gray-500 uppercase tracking-wider w-16">ID</th>
                <th class="px-5 py-4 text-[11px] font-black text-gray-500 uppercase tracking-wider">Marca / Fabricante</th>
                <th class="px-5 py-4 text-[11px] font-black text-gray-500 uppercase tracking-wider">País de Origen</th>
                <th class="px-5 py-4 text-[11px] font-black text-gray-500 uppercase tracking-wider text-center">Productos Asociados</th>
                <th class="px-5 py-4 text-[11px] font-black text-gray-500 uppercase tracking-wider text-center">Estado</th>
                <th class="px-5 py-4 text-[11px] font-black text-gray-500 uppercase tracking-wider text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100/80 bg-white">
            @forelse($marcas as $marca)
            <tr class="hover:bg-blue-50/30 transition-colors group">
                {{-- ID --}}
                <td class="px-5 py-4 text-xs font-mono font-bold text-gray-400">#{{ str_pad($marca->id, 2, '0', STR_PAD_LEFT) }}</td>
                
                {{-- Marca / Nombre Comercial con Avatar Elegante --}}
                <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="size-10 rounded-xl bg-gradient-to-br from-[#1e549e] to-[#0f2347] text-white flex items-center justify-center font-black text-sm uppercase shadow-2xs shrink-0 select-none">
                            {{ mb_substr($marca->nombre, 0, 2) }}
                        </div>
                        <div>
                            <p class="font-black text-gray-900 text-sm uppercase tracking-wide group-hover:text-blue-600 transition-colors leading-tight">
                                {{ $marca->nombre }}
                            </p>
                            <span class="text-[11px] text-gray-400 font-medium">Fabricante Registrado</span>
                        </div>
                    </div>
                </td>

                {{-- País de Origen --}}
                <td class="px-5 py-4">
                    @if($marca->pais_origen)
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-700 bg-gray-50 border border-gray-200 px-2.5 py-1 rounded-lg">
                            <span class="material-symbols-outlined text-[15px] text-blue-600">public</span>
                            {{ $marca->pais_origen }}
                        </span>
                    @else
                        <span class="text-xs text-gray-400 italic">No especificado</span>
                    @endif
                </td>

                {{-- Productos Asociados --}}
                <td class="px-5 py-4 text-center">
                    @if($marca->productos_count > 0)
                        <a href="{{ route('admin.productos.index', ['buscar' => $marca->nombre]) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-black bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition-colors" title="Ver productos de esta marca en el catálogo">
                            <span class="material-symbols-outlined text-[14px]">inventory_2</span>
                            {{ $marca->productos_count }} {{ $marca->productos_count == 1 ? 'producto' : 'productos' }}
                        </a>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold text-gray-400 bg-gray-50 border border-gray-200">
                            0 productos
                        </span>
                    @endif
                </td>

                {{-- Estado Activo / Inactivo con Toggle Interactivo --}}
                <td class="px-5 py-4 text-center">
                    @if(Auth::user()->tienePermiso('gestionar_categorias_marcas'))
                    <label class="relative inline-flex items-center cursor-pointer select-none" title="{{ $marca->activo ? 'Clic para desactivar' : 'Clic para activar' }}">
                        <input type="checkbox" onchange="toggleStatusMarca({{ $marca->id }}, this)" class="sr-only peer" {{ $marca->activo ? 'checked' : '' }}>
                        <div class="w-10 h-5.5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4.5 after:w-4.5 after:transition-all peer-checked:bg-emerald-500 shadow-2xs"></div>
                        <span class="ml-2 text-xs font-bold {{ $marca->activo ? 'text-emerald-700' : 'text-gray-400' }}">
                            {{ $marca->activo ? 'Activa' : 'Inactiva' }}
                        </span>
                    </label>
                    @else
                    <span class="inline-flex items-center gap-1 text-xs font-bold {{ $marca->activo ? 'text-emerald-700' : 'text-gray-400' }}">
                        <span class="size-2 rounded-full {{ $marca->activo ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
                        {{ $marca->activo ? 'Activa' : 'Inactiva' }}
                    </span>
                    @endif
                </td>

                {{-- Acciones --}}
                <td class="px-5 py-4 text-right">
                    @if(Auth::user()->tienePermiso('gestionar_categorias_marcas'))
                    <div class="flex items-center justify-end gap-1.5">
                        <button type="button" onclick="openModal({{ $marca->toJson() }})" class="size-8 rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 flex items-center justify-center transition-all shadow-2xs hover:scale-105 active:scale-95" title="Editar Marca">
                            <span class="material-symbols-outlined text-[17px]">edit</span>
                        </button>
                        <button type="button" onclick="deleteMarca({{ $marca->id }}, '{{ addslashes($marca->nombre) }}', {{ $marca->productos_count }})" class="size-8 rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-rose-600 hover:border-rose-200 hover:bg-rose-50 flex items-center justify-center transition-all shadow-2xs hover:scale-105 active:scale-95" title="Eliminar Marca">
                            <span class="material-symbols-outlined text-[17px]">delete</span>
                        </button>
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
                            <span class="material-symbols-outlined text-3xl">sell</span>
                        </div>
                        <h4 class="font-black text-gray-800 text-base">No hay marcas registradas</h4>
                        <p class="text-xs text-gray-500 mt-1">Prueba ajustando los filtros o registra un nuevo fabricante con el botón superior.</p>
                        @if(request('buscar') || (request('estado') && request('estado') !== 'all'))
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
        Mostrando {{ $marcas->firstItem() ?? 0 }} a {{ $marcas->lastItem() ?? 0 }} de {{ $marcas->total() }} marcas registradas
    </div>
    <div>
        {{ $marcas->links('pagination::tailwind') }}
    </div>
</div>