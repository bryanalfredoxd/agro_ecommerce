@php
    // Esta variable viene del controlador
    $productos = $productos ?? [];
@endphp

<div id="products-content">
    
    {{-- 1. CABECERA DE ORDENAMIENTO (Limpia y profesional) --}}
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4 transition-all">
        <p class="text-gray-500 text-sm font-medium">
            Mostrando <span class="font-black text-agro-dark text-base">{{ $productos->firstItem() ?? 0 }} - {{ $productos->lastItem() ?? 0 }}</span> de <span class="font-black text-agro-dark text-base">{{ $productos->total() }}</span> resultados
        </p>
        
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider hidden sm:block">Ordenar por:</label>
            <div class="relative w-full sm:w-auto group">
                <select name="orden" 
                        onchange="window.dispatchEvent(new CustomEvent('catalogo:orden-change', {detail: {value: this.value}}))" 
                        class="appearance-none w-full sm:w-56 bg-gray-50 border border-gray-200 text-agro-dark text-sm rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary block p-2.5 pl-4 pr-10 font-bold cursor-pointer hover:bg-white hover:border-primary/50 transition-all">
                    <option value="reciente" {{ request('orden') == 'reciente' ? 'selected' : '' }}>Más Recientes</option>
                    <option value="precio_asc" {{ request('orden') == 'precio_asc' ? 'selected' : '' }}>Menor Precio</option>
                    <option value="precio_desc" {{ request('orden') == 'precio_desc' ? 'selected' : '' }}>Mayor Precio</option>
                    <option value="nombre_asc" {{ request('orden') == 'nombre_asc' ? 'selected' : '' }}>Nombre (A-Z)</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-2.5 text-gray-400 pointer-events-none text-[20px] group-hover:text-primary transition-colors">expand_more</span>
            </div>
        </div>
    </div>

    {{-- 2. GRID DE PRODUCTOS --}}
    @if($productos->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($productos as $index => $producto)
                <article class="group relative flex flex-col bg-white rounded-2xl border border-gray-100 hover:border-primary/30 hover:shadow-xl hover:shadow-primary/5 transition-all duration-300 overflow-hidden h-full animate-fade-in-up" style="animation-delay: {{ $index * 50 }}ms;">
                    
                    {{-- ÁREA DE IMAGEN (Limpia, sin overlays molestos) --}}
                    <div class="relative w-full aspect-[4/3] bg-gray-50 overflow-hidden flex items-center justify-center">
                    
                        {{-- CARGA DE IMAGEN DESDE EL STORAGE PUBLICO --}}
                        @if($producto->imagen_url)
                            <div class="absolute inset-0 bg-cover bg-center group-hover:scale-105 transition-transform duration-700 ease-out mix-blend-multiply" 
                                style="background-image: url('{{ asset($producto->imagen_url) }}');"></div>
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-300 bg-gray-50">
                                <span class="material-symbols-outlined text-5xl mb-2">image</span>
                                <span class="text-[10px] font-bold uppercase tracking-widest">Sin Imagen</span>
                            </div>
                        @endif

                        {{-- Badges Superior Izquierda --}}
                        <div class="absolute top-3 left-3 flex flex-col gap-1.5 items-start z-10 pointer-events-none">
                            @if($producto->es_controlado)
                                <span class="inline-flex items-center gap-1 bg-red-500 text-white text-[10px] font-black px-2 py-1 rounded-md uppercase shadow-sm tracking-wide">
                                    <span class="material-symbols-outlined text-[12px]">lock</span> Controlado
                                </span>
                            @endif

                            @if($producto->precio_oferta_usd)
                                <span class="inline-flex items-center gap-1 bg-primary text-agro-dark text-[10px] font-black px-2 py-1 rounded-md uppercase shadow-sm tracking-wide">
                                    Oferta
                                </span>
                            @endif
                        </div>

                        {{-- Botón Favorito Superior Derecha (Estándar e-commerce) --}}
                        <div class="absolute top-3 right-3 z-20">
                            <button class="flex items-center justify-center w-8 h-8 rounded-full bg-white text-gray-400 hover:text-red-500 hover:bg-red-50 hover:shadow-md transition-all duration-300" title="Añadir a favoritos">
                                <span class="material-symbols-outlined text-[18px] hover:fill-current">favorite</span>
                            </button>
                        </div>
                        
                        {{-- Unidad de Medida --}}
                        <div class="absolute bottom-3 left-3 pointer-events-none z-10">
                            <span class="inline-block bg-white/95 backdrop-blur-sm text-agro-dark text-[10px] font-bold px-2 py-1 rounded-md shadow-sm uppercase tracking-wider">
                                {{ $producto->unidad_medida }}
                            </span>
                        </div>
                    </div>

                    {{-- ÁREA DE INFORMACIÓN --}}
                    <div class="p-5 flex flex-col flex-1 relative bg-white z-10">
                        
                        {{-- Marca y Stock --}}
                        <div class="flex justify-between items-center mb-2">
                            <a href="#" data-filter="categoria" data-value="{{ $producto->categoria_id }}" 
                               class="filter-link text-[10px] text-agro-accent font-bold uppercase tracking-wider flex items-center gap-1 hover:text-primary transition-colors relative z-20 truncate pr-2">
                                <span class="material-symbols-outlined text-[14px]">verified</span>
                                {{ $producto->categoria->nombre ?? 'General' }}
                            </a>
                            
                            <div>
                                @if($producto->stock_total > 0 && $producto->stock_total <= $producto->stock_minimo_alerta)
                                    <span class="text-[10px] font-bold text-amber-500 bg-amber-50 px-2 py-0.5 rounded text-right whitespace-nowrap">
                                        Poco Stock
                                    </span>
                                @elseif($producto->stock_total > 0)
                                    <span class="text-[10px] font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded text-right whitespace-nowrap flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Stock
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Título clickeable en toda la tarjeta --}}
                        <h3 class="font-bold text-agro-dark text-base leading-snug mb-2 line-clamp-2 min-h-[2.5rem] group-hover:text-primary transition-colors">
                            <a href="#" class="focus:outline-none before:absolute before:inset-0">
                                {{ $producto->nombre }}
                            </a>
                        </h3>
                        
                        <p class="text-xs text-gray-500 mb-4 line-clamp-2 flex-grow leading-relaxed relative z-20">{{ $producto->descripcion }}</p>

                        {{-- Precios y Call to Action --}}
                        <div class="mt-auto pt-4 border-t border-gray-100 flex items-end justify-between relative z-20">
                            <div class="flex flex-col">
                                <div class="flex items-baseline gap-1.5">
                                    @if($producto->precio_oferta_usd)
                                        <span class="text-xl font-black text-agro-dark">${{ number_format($producto->precio_oferta_usd, 2) }}</span>
                                        <span class="text-xs text-gray-400 line-through font-semibold">${{ number_format($producto->precio_venta_usd, 2) }}</span>
                                    @else
                                        <span class="text-xl font-black text-agro-dark">${{ number_format($producto->precio_venta_usd, 2) }}</span>
                                        <span class="text-[10px] text-gray-400 font-bold self-start mt-1">USD</span>
                                    @endif
                                </div>
                                <span class="text-[11px] text-gray-400 font-medium mt-0.5">
                                    ≈ Bs. {{ number_format(($producto->precio_oferta_usd ?? $producto->precio_venta_usd) * 60.50, 2, ',', '.') }}
                                </span>
                            </div>
                            
                            {{-- BOTÓN AÑADIR AL CARRITO SIEMPRE VISIBLE --}}
                            <button type="button" onclick="addToCart({{ $producto->id }})" class="flex items-center justify-center w-11 h-11 rounded-xl bg-primary/10 text-agro-dark hover:bg-primary hover:text-white hover:shadow-lg hover:shadow-primary/20 transition-all duration-300 group/btn" title="Añadir al carrito">
                                <span class="material-symbols-outlined text-[22px] group-active/btn:scale-95 transition-transform">add_shopping_cart</span>
                            </button>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- 3. PAGINACIÓN --}}
        <div class="mt-12 flex justify-center animate-fade-in-up">
            {{ $productos->links('pagination::tailwind') }} 
        </div>

    @else
        {{-- 4. ESTADO VACÍO (NO HAY RESULTADOS) --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-12 flex flex-col items-center justify-center text-center shadow-sm animate-fade-in-up">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                <span class="material-symbols-outlined text-4xl text-gray-400">search_off</span>
            </div>
            <h3 class="text-xl font-black text-agro-dark mb-2">No encontramos resultados</h3>
            <p class="text-gray-500 text-sm max-w-sm mx-auto mb-6 leading-relaxed">
                No hay productos que coincidan con los filtros aplicados. Intenta cambiar de categoría o buscar con otro término.
            </p>
            {{-- Usando onclick en lugar de event listeners para que sobreviva al AJAX --}}
            <button onclick="if(window.catalogoClearFilters) window.catalogoClearFilters(event)" class="inline-flex items-center justify-center gap-2 px-6 py-3 border border-transparent text-sm font-bold rounded-xl text-agro-dark bg-primary/10 hover:bg-primary hover:text-white transition-all duration-300 group">
                <span class="material-symbols-outlined text-[20px] group-hover:-rotate-180 transition-transform duration-500">restart_alt</span>
                Limpiar Búsqueda
            </button>
        </div>
    @endif
</div>