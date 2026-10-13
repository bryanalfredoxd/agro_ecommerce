{{-- Indicador sutil de desplazamiento en móviles --}}
<div class="sm:hidden px-4 py-2.5 bg-gradient-to-r from-blue-50/90 via-indigo-50/60 to-blue-50/90 border-b border-blue-100/70 flex items-center justify-between text-[11px] text-[#1e549e] font-semibold select-none">
    <div class="flex items-center gap-1.5">
        <span class="material-symbols-outlined text-[16px] text-blue-600">swipe</span>
        <span>Desliza la tabla para ver precios, stock y acciones</span>
    </div>
    <span class="material-symbols-outlined text-[15px] text-blue-500 animate-pulse">arrow_forward</span>
</div>

<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-collapse min-w-[880px]">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50/75">
                <th class="px-4 sm:px-5 py-3 sm:py-4 text-[11px] font-black text-gray-500 uppercase tracking-wider w-20">Foto</th>
                <th class="px-4 sm:px-5 py-3 sm:py-4 text-[11px] font-black text-gray-500 uppercase tracking-wider min-w-[240px]">Artículo & Especificaciones</th>
                <th class="px-4 sm:px-5 py-3 sm:py-4 text-[11px] font-black text-gray-500 uppercase tracking-wider min-w-[200px]">Precios (USD / Bs)</th>
                <th class="px-4 sm:px-5 py-3 sm:py-4 text-[11px] font-black text-gray-500 uppercase tracking-wider min-w-[160px]">Inventario & Stock</th>
                <th class="px-4 sm:px-5 py-3 sm:py-4 text-[11px] font-black text-gray-500 uppercase tracking-wider text-center w-16">Tipo</th>
                <th class="px-4 sm:px-5 py-3 sm:py-4 text-[11px] font-black text-gray-500 uppercase tracking-wider text-right min-w-[140px]">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100/80 bg-white">
            @forelse($productos as $producto)
            @php
                $esSuspendido = $producto->trashed();
                $stockTotal = (float) $producto->stock_total;
                $stockMinimo = (float) $producto->stock_minimo_alerta;
                $stockCritico = $stockTotal <= $stockMinimo;
                $stockVacio = $stockTotal <= 0;
                $tasa = $tasaVes ?? 0;
                $precioBs = $tasa > 0 ? ($producto->precio_oferta_usd ?: $producto->precio_venta_usd) * $tasa : 0;
            @endphp
            <tr class="hover:bg-blue-50/30 transition-colors group {{ $esSuspendido ? 'bg-rose-50/20' : '' }}">
                
                {{-- Columna 1: Imagen y Botón Destacado --}}
                <td class="px-4 sm:px-5 py-3.5 sm:py-4 align-top w-20">
                    <div class="size-14 rounded-2xl bg-white border border-gray-200 overflow-hidden flex items-center justify-center shadow-2xs relative group/img shrink-0">
                        @if($producto->imagen_url)
                            <img src="{{ asset($producto->imagen_url) }}" alt="{{ $producto->nombre }}" class="size-full object-cover">
                        @else
                            <span class="material-symbols-outlined text-gray-300 text-3xl">inventory_2</span>
                        @endif
                        
                        {{-- Botón interactivo para destacar (Solo con permisos) --}}
                        @if(!$esSuspendido && Auth::user()->tienePermiso('gestionar_productos'))
                            <button type="button" onclick="toggleDestacadoProducto({{ $producto->id }}, this)" class="absolute top-1 right-1 size-6 rounded-lg flex items-center justify-center transition-all bg-white/90 backdrop-blur-xs border border-white shadow-2xs {{ $producto->destacado ? 'text-amber-500' : 'text-gray-300 hover:text-amber-400' }}" title="{{ $producto->destacado ? 'Quitar de destacados' : 'Destacar en la portada' }}">
                                <span class="material-symbols-outlined text-[15px] {{ $producto->destacado ? 'fill-current' : '' }}">star</span>
                            </button>
                        @elseif($producto->destacado)
                            <div class="absolute top-1 right-1 size-6 rounded-lg flex items-center justify-center bg-white/90 border border-white shadow-2xs text-amber-500">
                                <span class="material-symbols-outlined text-[15px] fill-current">star</span>
                            </div>
                        @endif
                    </div>
                </td>
                
                {{-- Columna 2: Información principal --}}
                <td class="px-4 sm:px-5 py-3.5 sm:py-4 align-top min-w-[240px]">
                    <div class="flex flex-col">
                        <span class="font-black text-gray-900 text-sm leading-snug line-clamp-2 group-hover:text-blue-600 transition-colors {{ $esSuspendido ? 'line-through text-gray-400' : '' }}" title="{{ $producto->nombre }}">
                            {{ $producto->nombre }}
                        </span>
                        
                        <div class="mt-1.5 flex items-center gap-1.5 flex-wrap">
                            @if($producto->sku)
                                <span class="font-mono text-[10px] text-gray-600 bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200/60 font-bold whitespace-nowrap">
                                    SKU: {{ $producto->sku }}
                                </span>
                            @endif

                            @if($producto->categoria)
                                <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200/60 whitespace-nowrap">
                                    <span class="material-symbols-outlined text-[11px]">folder</span>
                                    {{ $producto->categoria->nombre }}
                                </span>
                            @endif

                            @if($producto->marca)
                                <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded border border-slate-200 uppercase whitespace-nowrap">
                                    <span class="material-symbols-outlined text-[11px]">sell</span>
                                    {{ $producto->marca->nombre }}
                                </span>
                            @endif
                        </div>
                    </div>
                </td>
                
                {{-- Columna 3: Precio (PVP Final con IVA y Desglose) --}}
                @php
                    $precioFinalPvp = $producto->precio_final_pvp;
                    $precioRegularPvp = $producto->precio_regular_pvp;
                    $precioBs = $tasa > 0 ? ($precioFinalPvp * $tasa) : 0;
                @endphp
                <td class="px-4 sm:px-5 py-3.5 sm:py-4 align-top min-w-[200px]">
                    <div class="flex flex-col">
                        @if($producto->precio_oferta_usd)
                            <div class="flex items-baseline gap-1.5">
                                <span class="font-black text-rose-600 text-base">${{ number_format($precioFinalPvp, 2) }}</span>
                                <span class="text-[11px] font-bold text-gray-400 line-through">${{ number_format($precioRegularPvp, 2) }}</span>
                            </div>
                        @else
                            <span class="font-black text-agro-dark text-base">${{ number_format($precioFinalPvp, 2) }}</span>
                        @endif

                        {{-- Detalle del IVA y Base Imponible --}}
                        @if($producto->precio_incluye_iva)
                            <span class="inline-flex items-center gap-0.5 text-[9px] font-extrabold text-teal-700 bg-teal-50 border border-teal-200/80 px-1.5 py-0.5 rounded-md w-fit mt-1 whitespace-nowrap" title="Precio final con IVA incluido (Base imponible: ${{ number_format($producto->precio_venta_usd, 2) }})">
                                <span class="material-symbols-outlined text-[11px]">verified</span> IVA inc. (Base: ${{ number_format($producto->precio_venta_usd, 2) }})
                            </span>
                        @else
                            @php
                                $ivaPct = \App\Models\ConfiguracionTienda::iva();
                            @endphp
                            <span class="inline-flex items-center gap-0.5 text-[9px] font-extrabold text-slate-600 bg-slate-100 border border-slate-200 px-1.5 py-0.5 rounded-md w-fit mt-1 whitespace-nowrap" title="PVP final con {{ (float)$ivaPct }}% IVA (Base imponible: ${{ number_format($producto->precio_venta_usd, 2) }})">
                                <span class="material-symbols-outlined text-[11px]">payments</span> PVP (+{{ (float)$ivaPct }}% IVA | Base: ${{ number_format($producto->precio_venta_usd, 2) }})
                            </span>
                        @endif

                        @if($precioBs > 0)
                            <span class="text-[11px] font-bold text-gray-500 mt-0.5 whitespace-nowrap">
                                Bs. {{ number_format($precioBs, 2) }}
                            </span>
                        @endif
                    </div>
                </td>
                
                {{-- Columna 4: Stock con Barra de Nivel --}}
                <td class="px-4 sm:px-5 py-3.5 sm:py-4 align-top min-w-[160px]">
                    <div class="flex flex-col gap-1 w-full max-w-[160px]">
                        <div class="flex items-baseline justify-between gap-2">
                            <span class="font-black text-sm whitespace-nowrap {{ $stockVacio ? 'text-rose-600' : ($stockCritico ? 'text-amber-600' : 'text-emerald-700') }}">
                                {{ rtrim(rtrim(number_format($producto->stock_total, 3), '0'), '.') }} 
                                <span class="text-[10px] font-bold uppercase text-gray-500">{{ $producto->unidad_medida }}</span>
                            </span>

                            @if($stockVacio)
                                <span class="inline-flex items-center gap-0.5 text-[9px] font-black text-rose-700 bg-rose-100 px-1.5 py-0.5 rounded-md uppercase whitespace-nowrap shrink-0">
                                    Agotado
                                </span>
                            @elseif($stockCritico)
                                <span class="inline-flex items-center gap-0.5 text-[9px] font-black text-amber-700 bg-amber-100 px-1.5 py-0.5 rounded-md uppercase animate-pulse whitespace-nowrap shrink-0">
                                    Crítico
                                </span>
                            @else
                                <span class="inline-flex items-center gap-0.5 text-[9px] font-black text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded-md uppercase whitespace-nowrap shrink-0">
                                    Óptimo
                                </span>
                            @endif
                        </div>

                        {{-- Barra de Progreso Stock --}}
                        @php
                            $maxRef = max($stockMinimo * 3, 10);
                            $porcentajeStock = min(100, max(0, ($stockTotal / $maxRef) * 100));
                        @endphp
                        <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-300 {{ $stockVacio ? 'bg-rose-500' : ($stockCritico ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $stockVacio ? '0%' : $porcentajeStock . '%' }}"></div>
                        </div>
                        <span class="text-[10px] text-gray-400 font-medium whitespace-nowrap">Mín. alerta: {{ (int)$producto->stock_minimo_alerta }}</span>
                    </div>
                </td>
                
                {{-- Columna 5: Atributos / Tipo --}}
                <td class="px-4 sm:px-5 py-3.5 sm:py-4 align-top text-center w-16">
                    <div class="flex items-center justify-center gap-1">
                        @if($producto->es_combo)
                            <span class="inline-flex items-center justify-center size-7 rounded-lg bg-blue-50 text-blue-700 border border-blue-200" title="Producto Compuesto (Combo / Paquete)">
                                <span class="material-symbols-outlined text-[16px]">layers</span>
                            </span>
                        @else
                            <span class="text-xs text-gray-300 font-bold">-</span>
                        @endif
                    </div>
                </td>
                
                {{-- Columna 6: Acciones --}}
                <td class="px-4 sm:px-5 py-3.5 sm:py-4 align-top text-right min-w-[140px]">
                    <div class="flex items-center justify-end gap-1.5">
                        @if(Auth::user()->tienePermiso('gestionar_productos'))
                            @if($esSuspendido)
                                <button type="button" onclick="restoreProducto({{ $producto->id }}, '{{ addslashes($producto->nombre) }}')" class="size-8 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all shadow-2xs hover:scale-105 active:scale-95 shrink-0" title="Reactivar Producto">
                                    <span class="material-symbols-outlined text-[17px]">settings_backup_restore</span>
                                </button>
                            @else
                                {{-- Botón Galería de Fotos --}}
                                <button type="button" onclick="abrirModalGaleria({{ $producto->id }}, '{{ addslashes($producto->nombre) }}')" class="size-8 rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-[#1e549e] hover:border-blue-200 hover:bg-blue-50 flex items-center justify-center transition-all shadow-2xs hover:scale-105 active:scale-95 shrink-0" title="Gestionar Galería de Fotos ({{ $producto->imagenes ? $producto->imagenes->count() : 0 }} adicionales)">
                                    <span class="material-symbols-outlined text-[17px]">collections</span>
                                </button>

                                <a href="{{ route('admin.productos.edit', $producto->id) }}" class="size-8 rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 flex items-center justify-center transition-all shadow-2xs hover:scale-105 active:scale-95 shrink-0" title="Editar Producto">
                                    <span class="material-symbols-outlined text-[17px]">edit</span>
                                </a>
                                
                                <button type="button" onclick="deleteProducto({{ $producto->id }}, '{{ addslashes($producto->nombre) }}')" class="size-8 rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-amber-600 hover:border-amber-200 hover:bg-amber-50 flex items-center justify-center transition-all shadow-2xs hover:scale-105 active:scale-95 shrink-0" title="Suspender / Pausar Producto">
                                    <span class="material-symbols-outlined text-[17px]">pause_circle</span>
                                </button>
                            @endif
                        @else
                            <span class="text-[11px] text-gray-400 italic font-semibold whitespace-nowrap">Solo lectura</span>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-16 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                        <div class="size-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-3 text-gray-400">
                            <span class="material-symbols-outlined text-3xl">inventory_2</span>
                        </div>
                        <h4 class="font-black text-gray-800 text-base">No se encontraron productos</h4>
                        <p class="text-xs text-gray-500 mt-1">Prueba ajustando los filtros de categoría, marca o búsqueda.</p>
                        @if(request('buscar') || request('categoria_id') || request('marca_id') || request('filtro_rapido'))
                        <button type="button" onclick="resetearTodosFiltros()" class="mt-4 px-4 py-2 rounded-xl text-xs font-bold text-[#1e549e] bg-blue-50 hover:bg-blue-100 border border-blue-200/60 transition-all flex items-center gap-1.5 shadow-2xs hover:scale-105 active:scale-95">
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
<div class="px-4 sm:px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 ajax-pagination bg-gray-50/50">
    <div class="text-xs text-gray-500 font-medium text-center sm:text-left">
        Mostrando {{ $productos->firstItem() ?? 0 }} a {{ $productos->lastItem() ?? 0 }} de {{ $productos->total() }} artículos registrados
    </div>
    <div class="w-full sm:w-auto flex justify-center">
        {{ $productos->links('pagination::tailwind') }}
    </div>
</div>