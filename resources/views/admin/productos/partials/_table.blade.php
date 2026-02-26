<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50/50">
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest w-20">Imagen</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Detalle del Producto</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Precio (USD)</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Stock Disponible</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Atributos</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($productos as $producto)
            <tr class="hover:bg-gray-50/50 transition-colors group">
                
                {{-- Columna: Imagen --}}
                <td class="px-6 py-4">
                    <div class="w-14 h-14 rounded-xl bg-white border border-gray-200 overflow-hidden flex items-center justify-center shadow-sm relative">
                        @if($producto->imagen_url)
                            {{-- Solo quitamos el 'storage/' --}}
                            <img src="{{ asset($producto->imagen_url) }}" alt="{{ $producto->nombre }}" class="w-full h-full object-cover">
                        @else
                            <span class="material-symbols-outlined text-gray-300 text-2xl">inventory</span>
                        @endif
                        
                        {{-- Icono flotante si es destacado --}}
                        <button onclick="toggleDestacado({{ $producto->id }}, this)" class="absolute top-0.5 right-0.5 w-6 h-6 rounded-lg flex items-center justify-center transition-all bg-white/80 backdrop-blur-sm border border-white {{ $producto->destacado ? 'text-amber-400' : 'text-gray-300 hover:text-amber-400' }}" title="Destacar producto">
                            <span class="material-symbols-outlined text-[16px] {{ $producto->destacado ? 'fill-current' : '' }}">star</span>
                        </button>
                    </div>
                </td>
                
                {{-- Columna: Información principal --}}
                <td class="px-6 py-4">
                    <p class="font-bold text-agro-dark capitalize text-sm mb-1 leading-tight line-clamp-2" title="{{ $producto->nombre }}">
                        {{ $producto->nombre }}
                    </p>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-[10px] text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded">SKU: {{ $producto->sku ?? 'N/A' }}</span>
                        <span class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">
                            {{ $producto->categoria->nombre ?? 'Sin Categoría' }}
                        </span>
                    </div>
                </td>
                
                {{-- Columna: Precio --}}
                <td class="px-6 py-4">
                    <p class="font-black text-agro-dark text-base">${{ number_format($producto->precio_venta_usd, 2) }}</p>
                    @if($producto->precio_oferta_usd)
                        <p class="text-[10px] font-bold text-red-500 line-through">${{ number_format($producto->precio_oferta_usd, 2) }}</p>
                    @else
                        <p class="text-[10px] font-medium text-gray-400">P. Base</p>
                    @endif
                </td>
                
                {{-- Columna: Stock con Lógica de Alertas --}}
                <td class="px-6 py-4">
                    @php
                        $stockCritico = $producto->stock_total <= $producto->stock_minimo_alerta;
                        $stockVacio = $producto->stock_total <= 0;
                    @endphp

                    <div class="flex flex-col items-start gap-1">
                        <span class="font-black text-sm {{ $stockVacio ? 'text-red-500' : ($stockCritico ? 'text-orange-500' : 'text-green-600') }}">
                            {{ rtrim(rtrim(number_format($producto->stock_total, 3), '0'), '.') }} 
                            <span class="text-[10px] font-bold uppercase">{{ $producto->unidad_medida }}</span>
                        </span>

                        @if($stockVacio)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-red-50 text-red-600 text-[9px] font-black rounded-md border border-red-100 uppercase tracking-widest">
                                <span class="material-symbols-outlined text-[12px]">error</span> Agotado
                            </span>
                        @elseif($stockCritico)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-orange-50 text-orange-600 text-[9px] font-black rounded-md border border-orange-100 uppercase tracking-widest">
                                <span class="material-symbols-outlined text-[12px]">warning</span> Crítico
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-50 text-green-600 text-[9px] font-black rounded-md border border-green-100 uppercase tracking-widest">
                                <span class="material-symbols-outlined text-[12px]">check_circle</span> Suficiente
                            </span>
                        @endif
                    </div>
                </td>
                
                {{-- Columna: Atributos y Etiquetas --}}
                <td class="px-6 py-4 text-center">
                    <div class="flex flex-wrap justify-center gap-1">
                        @if($producto->es_controlado)
                            <span class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center cursor-help" title="Producto de venta controlada (Requiere Receta)">
                                <span class="material-symbols-outlined text-[16px]">stethoscope</span>
                            </span>
                        @endif
                        @if($producto->es_combo)
                            <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center cursor-help" title="Producto Compuesto (Combo)">
                                <span class="material-symbols-outlined text-[16px]">layers</span>
                            </span>
                        @endif
                        @if(!$producto->es_controlado && !$producto->es_combo)
                            <span class="text-xs text-gray-300">-</span>
                        @endif
                    </div>
                </td>
                
                {{-- Columna: Acciones --}}
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        @if($producto->trashed())
                        {{-- Si está suspendido, solo mostramos el botón de restaurar --}}
                        <button onclick="restoreProducto({{ $producto->id }})" title="Reactivar Producto" class="w-8 h-8 rounded-lg bg-green-50 border border-green-200 text-green-600 hover:bg-green-600 hover:text-white flex items-center justify-center transition-all shadow-sm">
                            <span class="material-symbols-outlined text-[18px]">settings_backup_restore</span>
                        </button>
                        @else
                            {{-- Si está activo, mostramos los botones normales de Editar y Suspender --}}
                            <a href="{{ route('admin.productos.edit', $producto->id) }}" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 flex items-center justify-center transition-all shadow-sm">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </a>
                            
                            <button onclick="deleteProducto({{ $producto->id }})" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-amber-500 hover:border-amber-200 hover:bg-amber-50 flex items-center justify-center transition-all shadow-sm">
                                <span class="material-symbols-outlined text-[18px]">pause_circle</span>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-16 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                            <span class="material-symbols-outlined text-3xl text-gray-400">inventory_2</span>
                        </div>
                        <p class="font-bold text-gray-600">No se encontraron productos</p>
                        <p class="text-xs mt-1">Prueba ajustando los filtros o la búsqueda.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="px-6 py-4 border-t border-gray-100 flex justify-center ajax-pagination">
    {{ $productos->links('pagination::tailwind') }}
</div>