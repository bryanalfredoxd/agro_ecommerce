{{-- Controles de Ordenamiento --}}
<div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4 bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
    <p class="text-sm font-bold text-gray-500">
        Mostrando <span class="text-agro-dark">{{ $productos->firstItem() ?? 0 }} - {{ $productos->lastItem() ?? 0 }}</span> de <span class="text-agro-dark">{{ $productos->total() }}</span> productos
    </p>
    
    <div class="flex items-center gap-2 w-full sm:w-auto">
        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest hidden sm:block">Ordenar por:</label>
        <select id="sort-select" class="bg-gray-50 border border-gray-200 text-agro-dark text-sm rounded-xl focus:ring-primary focus:border-primary block w-full sm:w-48 p-2.5 font-medium outline-none cursor-pointer" onchange="catalogoSort(this.value)">
            <option value="reciente" {{ request('orden') == 'reciente' ? 'selected' : '' }}>Más recientes</option>
            <option value="precio_asc" {{ request('orden') == 'precio_asc' ? 'selected' : '' }}>Precio: Menor a Mayor</option>
            <option value="precio_desc" {{ request('orden') == 'precio_desc' ? 'selected' : '' }}>Precio: Mayor a Menor</option>
            <option value="nombre_asc" {{ request('orden') == 'nombre_asc' ? 'selected' : '' }}>Nombre: A - Z</option>
        </select>
    </div>
</div>

{{-- Cuadrícula de Productos --}}
@if($productos->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($productos as $producto)
            <article class="group relative flex flex-col bg-white rounded-2xl border border-gray-100 hover:border-primary/30 hover:shadow-xl hover:shadow-primary/5 transition-all duration-300 overflow-hidden h-full">
                
                {{-- ÁREA DE IMAGEN --}}
                <div class="relative w-full aspect-[4/3] bg-gray-50 overflow-hidden flex items-center justify-center">
                    
                    {{-- CARGA DE IMAGEN DESDE EL STORAGE PUBLICO --}}
                    @if($producto->imagen_url)
                        <div class="absolute inset-0 bg-cover bg-center group-hover:scale-105 transition-transform duration-700 ease-out mix-blend-multiply" 
                             style="background-image: url('{{ asset('storage/' . $producto->imagen_url) }}');"></div>
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
                    
                    {{-- Unidad de medida --}}
                    <div class="absolute bottom-3 left-3 pointer-events-none">
                        <span class="inline-block bg-white/95 backdrop-blur-sm text-agro-dark text-[10px] font-bold px-2 py-1 rounded-md shadow-sm uppercase tracking-wider border border-gray-100">
                            {{ $producto->unidad_medida }}
                        </span>
                    </div>
                </div>
                
                {{-- ÁREA DE INFORMACIÓN --}}
                <div class="p-5 flex flex-col flex-1 relative bg-white">
                    
                    {{-- Fila superior: Marca y Estado de Stock --}}
                    <div class="flex justify-between items-center mb-2">
                        <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wider flex items-center gap-1 truncate pr-2">
                            <span class="material-symbols-outlined text-[14px]">verified</span>
                            {{ $producto->marca->nombre ?? 'GENÉRICO' }}
                        </p>
                        
                        {{-- Indicador de Stock Lógico --}}
                        <div>
                            @if($producto->stock_total <= 0)
                                <span class="text-[10px] font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded text-right whitespace-nowrap">
                                    Agotado
                                </span>
                            @elseif($producto->stock_total <= $producto->stock_minimo_alerta)
                                <span class="text-[10px] font-bold text-amber-500 bg-amber-50 px-2 py-0.5 rounded text-right whitespace-nowrap">
                                    Poco Stock
                                </span>
                            @else
                                <span class="text-[10px] font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded text-right whitespace-nowrap flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Disponible
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Título --}}
                    <h3 class="text-agro-dark font-bold text-base leading-snug mb-2 line-clamp-2 min-h-[2.5rem] group-hover:text-primary transition-colors">
                        <a href="#" class="focus:outline-none before:absolute before:inset-0">
                            {{ $producto->nombre }}
                        </a>
                    </h3>
                    
                    {{-- Fila Inferior: Precios y Call to Action --}}
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
                        
                        {{-- BOTÓN AÑADIR AL CARRITO (AJAX Integrado) --}}
                        <button type="button" 
                                onclick="agregarAlCarrito({{ $producto->id }}, this)" 
                                {{ $producto->stock_total <= 0 ? 'disabled' : '' }}
                                class="relative flex items-center justify-center w-11 h-11 rounded-xl transition-all duration-300 group/btn overflow-hidden disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400 {{ $producto->stock_total > 0 ? 'bg-primary/10 text-agro-dark hover:bg-primary hover:text-white hover:shadow-lg hover:shadow-primary/20' : '' }}" 
                                title="{{ $producto->stock_total <= 0 ? 'Agotado' : 'Añadir al carrito' }}">
                            
                            <span class="material-symbols-outlined text-[22px] group-active/btn:scale-95 transition-transform btn-icon">
                                {{ $producto->stock_total <= 0 ? 'remove_shopping_cart' : 'add_shopping_cart' }}
                            </span>
                            
                            {{-- Iconos para la animación AJAX --}}
                            <span class="material-symbols-outlined text-[22px] animate-spin absolute hidden btn-spinner">autorenew</span>
                            <span class="material-symbols-outlined text-[22px] absolute hidden btn-success">check</span>
                        </button>
                    </div>

                </div>
            </article>
        @endforeach
    </div>

    {{-- Paginación Nativa de Laravel --}}
    <div class="mt-8 flex justify-center ajax-pagination">
        {{ $productos->links() }}
    </div>

@else
    {{-- Pantalla de Estado Vacío --}}
    <div class="bg-white rounded-3xl border border-gray-100 p-12 text-center shadow-sm">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-4xl text-gray-300">search_off</span>
        </div>
        <h3 class="text-xl font-black text-agro-dark mb-2">No encontramos resultados</h3>
        <p class="text-gray-500 text-sm max-w-md mx-auto mb-6">
            No hay productos que coincidan con los filtros seleccionados. Intenta borrar los filtros o buscar con otras palabras.
        </p>
        <button onclick="catalogoClearFilters(event)" class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary/10 text-primary hover:bg-primary hover:text-white font-bold rounded-xl transition-colors">
            <span class="material-symbols-outlined text-[18px]">restart_alt</span>
            Limpiar Filtros
        </button>
    </div>
@endif