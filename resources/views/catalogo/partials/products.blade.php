@php
    // Esta variable viene del controlador
    $productos = $productos ?? [];
@endphp

<div id="products-content">
    <div class="bg-white/80 backdrop-blur-sm p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4 transition-all hover:shadow-md">
        <p class="text-gray-600 text-sm font-medium">
            Mostrando <span class="font-black text-agro-dark">{{ $productos->firstItem() ?? 0 }} - {{ $productos->lastItem() ?? 0 }}</span> de <span class="font-black text-agro-dark">{{ $productos->total() }}</span> resultados
        </p>
        
        <div class="flex items-center gap-3">
            <label class="text-xs font-bold text-gray-500 uppercase hidden sm:block">Ordenar por:</label>
            <div class="relative group">
                <select name="orden" class="order-select appearance-none bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-primary focus:border-primary block w-full p-2.5 pr-10 font-bold cursor-pointer hover:bg-white hover:shadow-sm transition-all">
                    <option value="reciente" {{ request('orden') == 'reciente' ? 'selected' : '' }}>Más Nuevos</option>
                    <option value="precio_asc" {{ request('orden') == 'precio_asc' ? 'selected' : '' }}>Precio: Bajo a Alto</option>
                    <option value="precio_desc" {{ request('orden') == 'precio_desc' ? 'selected' : '' }}>Precio: Alto a Bajo</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-2.5 text-gray-400 pointer-events-none text-[22px] group-hover:text-primary transition-colors">expand_more</span>
            </div>
        </div>
    </div>

    @if($productos->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-8" style="perspective: 1000px;">
            @foreach($productos as $index => $producto)
                <div class="group bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-[0_20px_40px_-15px_rgba(0,128,0,0.15)] hover:-translate-y-2 transition-all duration-500 flex flex-col overflow-hidden relative animate-fade-in-up" style="animation-delay: {{ $index * 100 }}ms;">
                    
                    <div class="absolute top-4 left-4 z-10 flex flex-col gap-2 items-start">
                        @if($producto->precio_oferta_usd)
                            <span class="bg-red-500 text-white text-[11px] font-black px-3 py-1.5 rounded-full shadow-sm uppercase tracking-wider flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">local_offer</span> Oferta
                            </span>
                        @endif
                        @if($producto->stock_total <= $producto->stock_minimo_alerta)
                            <span class="bg-amber-500 text-white text-[11px] font-black px-3 py-1.5 rounded-full shadow-sm uppercase tracking-wider flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">inventory_2</span> Poco Stock
                            </span>
                        @endif
                    </div>

                    <div class="relative aspect-[4/3] bg-gradient-to-b from-gray-50 to-white p-6 overflow-hidden">
                        @php
                            $img = $producto->imagenes->where('es_principal', 1)->first()?->url_imagen 
                                   ?? $producto->imagenes->first()?->url_imagen 
                                   ?? null;
                        @endphp
                        
                        @if($img)
                            <img src="{{ $img }}" alt="{{ $producto->nombre }}" class="w-full h-full object-contain mix-blend-multiply filter drop-shadow-sm group-hover:scale-110 transition-transform duration-700 ease-in-out z-0">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-300 z-0">
                                <span class="material-symbols-outlined text-6xl mb-2">image_not_supported</span>
                                <span class="text-xs font-bold uppercase tracking-widest">Sin Imagen</span>
                            </div>
                        @endif
                        
                        <div class="absolute inset-0 bg-agro-dark/20 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-end justify-center pb-6 gap-3 backdrop-blur-[1px]">
                            <button class="bg-white text-agro-dark p-3 rounded-full hover:bg-primary hover:text-white hover:scale-110 transition-all shadow-lg transform translate-y-8 group-hover:translate-y-0 duration-500 ease-out">
                                <span class="material-symbols-outlined">visibility</span>
                            </button>
                            
                            <button onclick="addToCart({{ $producto->id }})" class="bg-primary text-white p-3 rounded-full hover:bg-green-600 hover:scale-110 transition-all shadow-lg transform translate-y-8 group-hover:translate-y-0 duration-500 ease-out delay-100" title="Agregar al carrito">
                                <span class="material-symbols-outlined">add_shopping_cart</span>
                            </button>
                        </div>
                    </div>

                    <div class="p-6 flex-1 flex flex-col bg-white relative z-10">
                        <div class="mb-3">
                            <a href="#" data-filter="categoria" data-value="{{ $producto->categoria_id }}" 
                               class="filter-link inline-block text-[11px] font-black text-primary bg-primary/10 px-3 py-1 rounded-full uppercase tracking-wider hover:bg-primary hover:text-white transition-colors">
                                {{ $producto->categoria->nombre ?? 'General' }}
                            </a>
                        </div>
                        
                        <h3 class="font-bold text-agro-dark text-xl leading-tight mb-3 group-hover:text-primary transition-colors line-clamp-2">
                            <a href="#" class="focus:outline-none">{{ $producto->nombre }}</a>
                        </h3>
                        
                        <p class="text-sm text-gray-500 mb-6 line-clamp-2 leading-relaxed">{{ $producto->descripcion }}</p>

                        <div class="mt-auto pt-5 border-t border-gray-100 flex items-end justify-between">
                            <div>
                                @if($producto->precio_oferta_usd)
                                    <div class="flex flex-col">
                                        <span class="text-xs text-red-400 line-through font-semibold mb-0.5">USD {{ number_format($producto->precio_venta_usd, 2) }}</span>
                                        <span class="text-2xl font-black text-agro-dark tracking-tight">USD {{ number_format($producto->precio_oferta_usd, 2) }}</span>
                                    </div>
                                @else
                                    <span class="text-2xl font-black text-agro-dark tracking-tight">USD {{ number_format($producto->precio_venta_usd, 2) }}</span>
                                @endif
                                <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wider mt-1">{{ $producto->unidad_medida }}</p>
                            </div>
                            
                            <div onclick="addToCart({{ $producto->id }})" class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-primary group-hover:text-white group-hover:shadow-lg group-hover:shadow-primary/30 transition-all duration-300 transform group-hover:rotate-12 cursor-pointer" title="Agregar al carrito">
                                 <span class="material-symbols-outlined text-[24px]">shopping_bag</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-16 flex justify-center animate-fade-in-up" style="animation-delay: 300ms;">
            {{ $productos->links('pagination::tailwind') }} 
        </div>

    @else
        <div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-sm border border-gray-100 p-16 text-center animate-fade-in-up">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6 relative">
                <span class="material-symbols-outlined text-5xl text-gray-300">travel_explore</span>
            </div>
            <h3 class="text-2xl font-black text-agro-dark mb-3">No encontramos resultados</h3>
            <p class="text-gray-500 text-lg max-w-md mx-auto mb-8 leading-relaxed">
                No hay productos para esta búsqueda. Intenta limpiar los filtros.
            </p>
            <a href="#" id="clear-filters-main" class="clear-filters-btn inline-flex items-center justify-center gap-2 px-8 py-4 border border-transparent text-base font-bold rounded-2xl text-white bg-agro-dark hover:bg-primary shadow-lg shadow-agro-dark/20 hover:shadow-primary/40 transform hover:-translate-y-1 transition-all duration-300">
                <span class="material-symbols-outlined">restart_alt</span>
                Limpiar Filtros
            </a>
        </div>
    @endif
</div>

<script>
// Script para la vista parcial
document.addEventListener('DOMContentLoaded', function() {
    // Configurar el select de ordenamiento en la vista parcial
    document.querySelectorAll('.order-select').forEach(select => {
        select.addEventListener('change', function(e) {
            // Enviar evento al script principal (index.blade.php)
            window.dispatchEvent(new CustomEvent('catalogo:orden-change', {
                detail: { value: e.target.value }
            }));
        });
    });
    
    // Botón de limpiar filtros en "no resultados"
    document.addEventListener('click', function(e) {
        if (e.target.matches('.clear-filters-btn') || e.target.closest('.clear-filters-btn')) {
            e.preventDefault();
            // Llamar directamente a la función global definida en index.blade.php
            if (window.catalogoClearFilters) {
                window.catalogoClearFilters(e);
            } else if (typeof clearFilters === 'function') {
                // Fallback por si la función se llama diferente en el scope global
                clearFilters(e);
            }
        }
    });
});
</script>