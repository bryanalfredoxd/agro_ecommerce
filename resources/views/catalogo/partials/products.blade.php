@php
    $productos = $productos ?? collect([]);
    $tasaDolar = $tasaDolar ?? 1; 
    if (!isset($favoritosIds)) {
        $favoritosIds = auth()->check()
            ? \Illuminate\Support\Facades\DB::table('favoritos')->where('usuario_id', auth()->id())->pluck('producto_id')->toArray()
            : [];
    }

    $catActual = isset($categorias) ? $categorias->firstWhere('id', request('categoria')) : null;
    $marcaActual = isset($marcas) ? $marcas->firstWhere('id', request('marca')) : null;
    $tieneFiltros = request()->hasAny(['categoria', 'marca', 'buscar']);
@endphp

<div id="products-content">
    
    {{-- 1. BARRA SUPERIOR DE RESULTADOS Y ORDENAMIENTO --}}
    <div class="bg-white p-4 sm:px-5 rounded-2xl shadow-xs border border-slate-200/80 flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 transition-all">
        
        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 flex-wrap">
            <p class="text-slate-600 text-xs sm:text-sm font-medium flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px] text-[#3492cf]">format_list_bulleted</span>
                Mostrando <span class="font-black text-[#1e549e]">{{ $productos->firstItem() ?? 0 }} - {{ $productos->lastItem() ?? 0 }}</span> de <span class="font-black text-slate-800">{{ $productos->total() }}</span> insumos
            </p>

            {{-- Chips de filtros activos en la cabecera del catálogo --}}
            @if($tieneFiltros)
                <div class="flex items-center gap-1.5 flex-wrap">
                    @if(request('buscar'))
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-[#e5f2fb] text-[#1e549e] border border-[#aad6f3]/50 text-xs font-bold shadow-2xs">
                            <span class="material-symbols-outlined text-[13px]">search</span>
                            "{{ Str::limit(request('buscar'), 18) }}"
                            <button type="button" onclick="removeFilter('buscar')" class="hover:text-rose-600 text-slate-400 hover:bg-white rounded size-3.5 flex items-center justify-center font-black ml-0.5 text-xs">✕</button>
                        </span>
                    @endif

                    @if(request('categoria') && $catActual)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-[#e5f2fb] text-[#1e549e] border border-[#aad6f3]/50 text-xs font-bold shadow-2xs">
                            <span class="material-symbols-outlined text-[13px]">category</span>
                            {{ $catActual->nombre }}
                            <button type="button" onclick="removeFilter('categoria')" class="hover:text-rose-600 text-slate-400 hover:bg-white rounded size-3.5 flex items-center justify-center font-black ml-0.5 text-xs">✕</button>
                        </span>
                    @endif

                    @if(request('marca') && $marcaActual)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-[#e5f2fb] text-[#1e549e] border border-[#aad6f3]/50 text-xs font-bold shadow-2xs">
                            <span class="material-symbols-outlined text-[13px]">verified</span>
                            {{ $marcaActual->nombre }}
                            <button type="button" onclick="removeFilter('marca')" class="hover:text-rose-600 text-slate-400 hover:bg-white rounded size-3.5 flex items-center justify-center font-black ml-0.5 text-xs">✕</button>
                        </span>
                    @endif

                    <button type="button" onclick="catalogoClearFilters(event)" class="text-[11px] font-bold text-rose-600 hover:text-rose-700 hover:underline px-1 py-0.5">
                        Limpiar todos
                    </button>
                </div>
            @endif
        </div>
        
        {{-- Selector de Ordenamiento --}}
        <div class="flex items-center gap-2.5 w-full md:w-auto shrink-0">
            <label for="orden-select" class="text-xs font-bold text-slate-500 uppercase tracking-wider hidden sm:flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">swap_vert</span> Ordenar:
            </label>
            <div class="relative w-full md:w-52 group">
                <select id="orden-select"
                        name="orden" 
                        onchange="window.dispatchEvent(new CustomEvent('catalogo:orden-change', {detail: {value: this.value}}))" 
                        class="appearance-none w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-[#3492cf]/20 focus:border-[#3492cf] block py-2.5 pl-3.5 pr-9 font-bold cursor-pointer hover:bg-white hover:border-[#3492cf]/60 transition-all shadow-2xs">
                    <option value="reciente" {{ request('orden') == 'reciente' ? 'selected' : '' }}>Más Recientes</option>
                    <option value="precio_asc" {{ request('orden') == 'precio_asc' ? 'selected' : '' }}>Menor Precio ($)</option>
                    <option value="precio_desc" {{ request('orden') == 'precio_desc' ? 'selected' : '' }}>Mayor Precio ($)</option>
                    <option value="nombre_asc" {{ request('orden') == 'nombre_asc' ? 'selected' : '' }}>Nombre (A - Z)</option>
                </select>
                <span class="material-symbols-outlined absolute right-2.5 top-2.5 text-slate-400 pointer-events-none text-[18px] group-hover:text-[#1e549e] transition-colors">unfold_more</span>
            </div>
        </div>
    </div>

    {{-- 2. GRID PRINCIPAL DE PRODUCTOS --}}
    @if($productos->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($productos as $index => $producto)
                @php
                    $imgSrc = $producto->imagenes->first()?->imagen_url ?? $producto->imagen_url;
                    $esFavorito = in_array($producto->id, $favoritosIds);
                    $pocoStock = ($producto->stock_total > 0 && $producto->stock_total <= $producto->stock_minimo_alerta);
                @endphp

                <article class="product-card group relative flex flex-col bg-white rounded-2xl border border-slate-200/90 hover:border-[#3492cf]/50 hover:shadow-xl hover:shadow-[#1e549e]/5 transition-all duration-300 overflow-hidden h-full animate-fade-in-up" 
                         style="animation-delay: {{ min($index * 40, 400) }}ms;">
                    
                    {{-- ÁREA DE IMAGEN --}}
                    <div class="relative w-full aspect-[4/3] bg-gradient-to-b from-slate-50/80 to-white overflow-hidden flex items-center justify-center p-3 sm:p-4 border-b border-slate-100">
                    
                        @if($imgSrc)
                            <img src="{{ asset($imgSrc) }}" 
                                 alt="{{ $producto->nombre }}" 
                                 loading="lazy"
                                 class="w-full h-full object-contain max-h-full max-w-full drop-shadow-2xs transition-transform duration-500 ease-out group-hover:scale-105"
                                 onerror="this.onerror=null; this.remove(); document.getElementById('placeholder-{{ $producto->id }}').classList.remove('hidden');">
                            
                            {{-- Fallback si la imagen falla al cargar --}}
                            <div id="placeholder-{{ $producto->id }}" class="hidden w-full h-full flex flex-col items-center justify-center text-slate-300">
                                <span class="material-symbols-outlined text-5xl">inventory_2</span>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-1">Sin imagen</span>
                            </div>
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-300">
                                <span class="material-symbols-outlined text-5xl">inventory_2</span>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-1">Sin imagen</span>
                            </div>
                        @endif

                        {{-- Enlace a Detalle de Producto desde la Imagen --}}
                        <a href="{{ route('catalogo.show', $producto->id) }}" class="absolute inset-0 z-10" title="Ver detalles de {{ $producto->nombre }}"></a>

                        {{-- Badges Superior Izquierda --}}
                        <div class="absolute top-3 left-3 flex flex-col gap-1 items-start z-10 pointer-events-none">
                            {{-- Badge Controlado (Deshabilitado a solicitud del cliente) --}}
                            {{-- @if($producto->es_controlado)
                                <span class="inline-flex items-center gap-1 bg-rose-600 text-white text-[10px] font-black px-2 py-0.5 rounded-md uppercase shadow-xs tracking-wider">
                                    <span class="material-symbols-outlined text-[12px]">lock</span> Controlado
                                </span>
                            @endif --}}

                            @if($producto->es_combo)
                                <span class="inline-flex items-center gap-1 bg-[#1e549e] text-white text-[10px] font-black px-2 py-0.5 rounded-md uppercase shadow-xs tracking-wider">
                                    Combo
                                </span>
                            @endif
                        </div>

                        {{-- Botón Favorito Superior Derecha --}}
                        <div class="absolute top-3 right-3 z-20">
                            <button type="button" 
                                    onclick="toggleCatalogoFavorito({{ $producto->id }}, this, event)"
                                    class="btn-favorito-catalogo flex items-center justify-center size-8.5 rounded-full transition-all duration-200 shadow-2xs {{ $esFavorito ? 'bg-rose-50 text-rose-500 ring-1 ring-rose-200 shadow-sm' : 'bg-white/95 text-slate-400 hover:text-rose-500 hover:bg-white hover:shadow-md' }}" 
                                    title="{{ $esFavorito ? 'Quitar de favoritos' : 'Añadir a favoritos' }}">
                                <span class="material-symbols-outlined text-[19px] transition-transform duration-200 pointer-events-none {{ $esFavorito ? 'fill-1 text-rose-500' : '' }}">favorite</span>
                            </button>
                        </div>
                        
                        {{-- Unidad de Medida / Presentación (Abajo a la izquierda) --}}
                        <div class="absolute bottom-2.5 left-2.5 pointer-events-none z-10">
                            <span class="inline-block bg-white/90 backdrop-blur-xs text-slate-700 text-[10px] font-bold px-2 py-0.5 rounded-md shadow-2xs border border-slate-200/60 uppercase tracking-wider">
                                {{ $producto->contenido_neto ? (float)$producto->contenido_neto . ' ' . $producto->unidad_contenido : $producto->unidad_medida }}
                            </span>
                        </div>
                    </div>

                    {{-- ÁREA DE INFORMACIÓN Y CONTENIDO --}}
                    <div class="p-4 sm:p-5 flex flex-col flex-1 relative bg-white z-10">
                        
                        {{-- Categoría y Estado de Stock --}}
                        <div class="flex justify-between items-center gap-2 mb-2">
                            <a href="#" 
                               data-filter="categoria" 
                               data-value="{{ $producto->categoria_id }}" 
                               class="filter-link text-[10px] font-extrabold uppercase tracking-wide text-[#1e549e] bg-[#e5f2fb] px-2 py-0.5 rounded-md hover:bg-[#aad6f3]/50 transition-colors truncate max-w-[65%]"
                               title="{{ $producto->categoria->nombre ?? 'General' }}">
                                {{ $producto->categoria->nombre ?? 'General' }}
                            </a>
                            
                            <div>
                                @if($pocoStock)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200/80 px-2 py-0.5 rounded-md whitespace-nowrap">
                                        <span class="size-1.5 rounded-full bg-amber-500 pulse-soft"></span>
                                        Poco Stock ({{ (float)$producto->stock_total }})
                                    </span>
                                @elseif($producto->stock_total > 0)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/80 px-2 py-0.5 rounded-md whitespace-nowrap">
                                        <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                        En Stock
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Título del Producto (Con enlace a detalle de producto) --}}
                        <a href="{{ route('catalogo.show', $producto->id) }}" class="block group/link" title="Ver detalles de {{ $producto->nombre }}">
                            <h3 class="font-bold text-slate-900 text-sm sm:text-base leading-snug line-clamp-2 min-h-[2.5rem] group-hover/link:text-[#1e549e] group-hover:text-[#1e549e] transition-colors cursor-pointer">
                                {{ $producto->nombre }}
                            </h3>
                        </a>

                        {{-- Marca y SKU --}}
                        <div class="flex items-center justify-between text-xs text-slate-500 mt-1 mb-2">
                            @if($producto->marca)
                                <span class="font-semibold text-slate-600 truncate">
                                    Marca: <strong class="text-slate-800">{{ $producto->marca->nombre }}</strong>
                                </span>
                            @else
                                <span class="text-slate-400 text-[11px]">Calidad garantizada</span>
                            @endif

                            @if($producto->sku)
                                <span class="text-[10px] font-mono text-slate-400 uppercase">
                                    {{ $producto->sku }}
                                </span>
                            @endif
                        </div>
                        
                        {{-- Descripción corta --}}
                        @if($producto->descripcion)
                            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed mb-4 flex-grow">
                                {{ $producto->descripcion }}
                            </p>
                        @else
                            <div class="flex-grow mb-4"></div>
                        @endif

                        {{-- Precios y Call to Action --}}
                        <div class="mt-auto pt-3.5 border-t border-slate-100 flex items-end justify-between gap-2 relative z-20">
                            
                            {{-- Precios USD y Bs (PVP Final con recargos de modalidad) --}}
                            @php
                                $precioFinalPvp = $producto->precio_final_pvp;
                                $precioRegularPvp = $producto->precio_regular_pvp;
                                $precioBolivaresUsd = $producto->getPrecioModalidad('bolivares', 'web');
                                $precioBs = round($precioBolivaresUsd * $tasaDolar, 2);
                            @endphp
                            <div class="flex flex-col min-w-0">
                                @if($producto->precio_oferta_usd)
                                    <div class="flex items-baseline gap-1.5">
                                        <span class="text-xl sm:text-2xl font-black text-rose-600 tracking-tight">
                                            ${{ number_format($precioFinalPvp, 2) }}
                                        </span>
                                        <span class="text-[11px] text-slate-400 font-bold line-through">
                                            ${{ number_format($precioRegularPvp, 2) }}
                                        </span>
                                    </div>
                                @else
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-xl sm:text-2xl font-black text-[#1e549e] tracking-tight">
                                            ${{ number_format($precioFinalPvp, 2) }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">USD</span>
                                    </div>
                                @endif
                                
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[11px] text-slate-500 font-bold tracking-wide truncate">
                                        Bs. {{ number_format($precioBs, 2, ',', '.') }}
                                    </span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded shrink-0 {{ $producto->precio_incluye_iva ? 'bg-teal-50 text-teal-700' : 'bg-slate-100 text-slate-600' }}" title="{{ $producto->precio_incluye_iva ? 'Precio con IVA incluido' : 'PVP con IVA al 16% (Base $' . number_format($producto->precio_venta_usd, 2) . ')' }}">
                                        {{ $producto->precio_incluye_iva ? 'IVA inc.' : 'PVP' }}
                                    </span>
                                </div>
                            </div>
                            
                            {{-- Botón Añadir al Carrito --}}
                            <button type="button" 
                                    onclick="addToCart({{ $producto->id }})" 
                                    class="inline-flex items-center justify-center gap-1.5 px-3 sm:px-3.5 py-2 rounded-xl bg-[#e5f2fb] hover:bg-[#1e549e] text-[#1e549e] hover:text-white font-bold text-xs shadow-2xs hover:shadow-xs transition-all duration-200 active:scale-95 shrink-0" 
                                    title="Añadir {{ $producto->nombre }} al carrito">
                                <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                                <span class="font-bold hidden sm:inline">Agregar</span>
                            </button>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- 3. PAGINACIÓN PROFESIONAL --}}
        <div class="mt-12 flex justify-center animate-fade-in-up">
            {{ $productos->links('pagination::tailwind') }} 
        </div>

    @else
        {{-- 4. ESTADO VACÍO (SIN RESULTADOS) --}}
        <div class="bg-white rounded-3xl border border-slate-200/90 p-10 sm:p-14 flex flex-col items-center justify-center text-center shadow-xs animate-fade-in-up">
            <div class="size-20 bg-[#e5f2fb] rounded-3xl flex items-center justify-center mx-auto mb-4 border border-[#aad6f3]/40 text-[#1e549e]">
                <span class="material-symbols-outlined text-4xl">search_off</span>
            </div>
            
            <h3 class="text-xl font-black text-slate-800 mb-2">No encontramos insumos que coincidan</h3>
            
            <p class="text-slate-500 text-sm max-w-md mx-auto mb-6 leading-relaxed">
                @if(request('buscar'))
                    No hay productos disponibles para la búsqueda <strong class="text-slate-700">"{{ request('buscar') }}"</strong> con los filtros seleccionados.
                @else
                    No se encontraron productos activos con los filtros de categoría o marca actuales.
                @endif
            </p>
            
            {{-- Botones de Acción Rápida en Estado Vacío --}}
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                <button type="button" 
                        onclick="catalogoClearFilters(event)" 
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 border border-slate-200 text-xs font-bold rounded-xl text-slate-700 bg-white hover:bg-slate-50 hover:border-slate-300 transition-all shadow-2xs w-full sm:w-auto">
                    <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                    Limpiar todos los filtros
                </button>
                
                <button type="button" 
                        onclick="typeof abrirModalSolicitarProducto === 'function' ? abrirModalSolicitarProducto('{{ addslashes(request('buscar', '')) }}') : document.getElementById('modal-solicitar-producto').classList.remove('hidden')" 
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#1e549e] hover:bg-[#3492cf] text-white text-xs font-black rounded-xl shadow-xs transition-colors w-full sm:w-auto cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">box_add</span>
                    Solicitar este insumo
                </button>
            </div>
        </div>
    @endif
</div>