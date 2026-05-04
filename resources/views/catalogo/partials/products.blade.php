@php
    // Estas variables vienen del CatalogoController.php
    $productos = $productos ?? collect([]);
    $tasaDolar = $tasaDolar ?? 1; 
@endphp

<div id="products-content">
    
    {{-- 1. CABECERA DE ORDENAMIENTO (Limpia y profesional) --}}
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4 transition-all">
        <p class="text-gray-500 text-sm font-medium">
            Mostrando <span class="font-black text-agro-dark text-base">{{ $productos->firstItem() ?? 0 }} - {{ $productos->lastItem() ?? 0 }}</span> de <span class="font-black text-agro-dark text-base">{{ $productos->total() }}</span> resultados
        </p>
        
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <label class="text-xs font-bold text-gray-700 uppercase tracking-wider hidden sm:block">Ordenar por:</label>
            <div class="relative w-full sm:w-auto group">
                <select name="orden" 
                        onchange="window.dispatchEvent(new CustomEvent('catalogo:orden-change', {detail: {value: this.value}}))" 
                        class="appearance-none w-full sm:w-56 bg-gray-50 border border-gray-200 text-agro-dark text-sm rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary block p-2.5 pl-4 pr-10 font-bold cursor-pointer hover:bg-white hover:border-primary/50 transition-all">
                    <option value="reciente" {{ request('orden') == 'reciente' ? 'selected' : '' }}>Más Recientes</option>
                    <option value="precio_asc" {{ request('orden') == 'precio_asc' ? 'selected' : '' }}>Menor Precio</option>
                    <option value="precio_desc" {{ request('orden') == 'precio_desc' ? 'selected' : '' }}>Mayor Precio</option>
                    <option value="nombre_asc" {{ request('orden') == 'nombre_asc' ? 'selected' : '' }}>Nombre (A-Z)</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-2.5 text-gray-700 pointer-events-none text-[20px] group-hover:text-primary transition-colors">expand_more</span>
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

                        {{-- Badges Superior Izquierda (Solo Venta Controlada) --}}
                        <div class="absolute top-3 left-3 flex flex-col gap-1.5 items-start z-10 pointer-events-none">
                            @if($producto->es_controlado)
                                <span class="inline-flex items-center gap-1 bg-red-500 text-white text-[10px] font-black px-2 py-1 rounded-md uppercase shadow-sm tracking-wide">
                                    <span class="material-symbols-outlined text-[12px]">lock</span> Controlado
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
                        
                        {{-- Título --}}
                        <h3 class="font-bold text-agro-dark text-base leading-snug mb-2 line-clamp-2 min-h-[2.5rem] group-hover:text-primary transition-colors">
                            <a href="#" class="focus:outline-none before:absolute before:inset-0">
                                {{ $producto->nombre }}
                            </a>
                        </h3>
                        
                        <p class="text-xs text-gray-500 mb-4 line-clamp-2 flex-grow leading-relaxed relative z-20">{{ $producto->descripcion }}</p>

                        {{-- Precios y Call to Action --}}
                        <div class="mt-auto pt-4 border-t border-gray-100 flex items-end justify-between relative z-20">
                            
                            <div class="flex flex-col">
                                <div class="flex items-baseline gap-1">
                                    <span class="text-xl font-black text-agro-dark">${{ number_format($producto->precio_venta_usd, 2) }}</span>
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">USD</span>
                                </div>
                                <span class="text-[11px] text-gray-400 font-bold mt-0.5 tracking-wide">
                                    Bs. {{ number_format($producto->precio_venta_usd * $tasaDolar, 2, ',', '.') }}
                                </span>
                            </div>
                            
                            {{-- BOTÓN AÑADIR AL CARRITO SIEMPRE VISIBLE --}}
                            <button type="button" 
                                    onclick="addToCart({{ $producto->id }})" 
                                    class="flex items-center justify-center w-10 h-10 rounded-xl bg-primary/10 text-agro-dark hover:bg-primary hover:text-white hover:shadow-lg hover:shadow-primary/20 transition-all duration-300 group/btn shrink-0" 
                                    title="Añadir al carrito">
                                <span class="material-symbols-outlined text-[20px] group-active/btn:scale-95 transition-transform pointer-events-none">add_shopping_cart</span>
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
                <span class="material-symbols-outlined text-4xl text-gray-700">search_off</span>
            </div>
            <h3 class="text-xl font-black text-agro-dark mb-2">No encontramos resultados</h3>
            <p class="text-gray-500 text-sm max-w-sm mx-auto mb-6 leading-relaxed">
                No hay productos que coincidan con los filtros aplicados. Intenta cambiar de categoría o buscar con otro término.
            </p>
            
            {{-- BOTONES DEL ESTADO VACÍO --}}
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <button onclick="if(window.catalogoClearFilters) window.catalogoClearFilters(event)" class="inline-flex items-center justify-center gap-2 px-6 py-3 border border-gray-200 text-sm font-bold rounded-xl text-gray-700 bg-white hover:bg-gray-50 transition-all duration-300 group w-full sm:w-auto">
                    <span class="material-symbols-outlined text-[20px] group-hover:-rotate-180 transition-transform duration-500">restart_alt</span>
                    Limpiar Búsqueda
                </button>
                
                {{-- Botón para abrir el Modal --}}
                <button onclick="document.getElementById('modal-solicitar-producto').classList.remove('hidden')" class="inline-flex items-center justify-center gap-2 px-6 py-3 border border-transparent text-sm font-bold rounded-xl text-agro-dark bg-primary/20 hover:bg-primary hover:text-white hover:shadow-md hover:shadow-primary/20 transition-all duration-300 group w-full sm:w-auto">
                    <span class="material-symbols-outlined text-[20px] group-hover:scale-110 transition-transform">inventory_2</span>
                    Solicitar Producto
                </button>
            </div>
        </div>
    @endif
</div>

{{-- ================= MODAL DE SOLICITUD ================= --}}
<div id="modal-solicitar-producto" class="fixed inset-0 z-[120] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-agro-dark/60 backdrop-blur-sm transition-opacity" onclick="document.getElementById('modal-solicitar-producto').classList.add('hidden')"></div>
    
    {{-- Panel --}}
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md animate-fade-in-up">
                
                {{-- Formulario apunta a la ruta web --}}
                <form action="{{ route('catalogo.solicitar') }}" method="POST">
                    @csrf
                    <div class="bg-white px-6 pb-6 pt-8">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-xl font-black text-agro-dark flex items-center gap-2" id="modal-title">
                                <span class="material-symbols-outlined text-primary text-[28px]">box_add</span>
                                Solicitar Producto
                            </h3>
                            <button type="button" onclick="document.getElementById('modal-solicitar-producto').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center transition-colors">
                                <span class="material-symbols-outlined text-[20px]">close</span>
                            </button>
                        </div>
                        
                        <p class="text-sm text-gray-500 font-medium leading-relaxed mb-6">
                            Dinos qué producto estás buscando y haremos lo posible por agregarlo a nuestro catálogo pronto.
                        </p>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="nombre_producto" class="block text-[11px] font-black text-gray-700 uppercase tracking-wider mb-2">Nombre del producto *</label>
                                <input type="text" name="nombre_producto" id="nombre_producto" required placeholder="Ej: Fertilizante Triple 15 (Saco 50kg)" class="block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 text-agro-dark placeholder:text-gray-400 transition-all shadow-sm">
                            </div>
                            
                            <div>
                                <label for="descripcion_adicional" class="block text-[11px] font-black text-gray-700 uppercase tracking-wider mb-2">Detalles adicionales (Opcional)</label>
                                <textarea name="descripcion_adicional" id="descripcion_adicional" rows="3" placeholder="Marca de tu preferencia, cantidad aproximada que buscas, etc..." class="block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 text-agro-dark placeholder:text-gray-400 transition-all shadow-sm resize-none"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-5 border-t border-gray-100 flex flex-col-reverse sm:flex-row justify-end gap-3">
                        <button type="button" onclick="document.getElementById('modal-solicitar-producto').classList.add('hidden')" class="w-full sm:w-auto inline-flex justify-center items-center rounded-xl bg-white px-6 py-2.5 text-sm font-bold text-gray-700 shadow-sm border border-gray-200 hover:bg-gray-50 transition-all duration-300">
                            Cancelar
                        </button>
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-xl bg-primary px-6 py-2.5 text-sm font-black text-agro-dark shadow-sm shadow-primary/30 hover:bg-green-500 transition-all duration-300 transform hover:-translate-y-0.5">
                            <span class="material-symbols-outlined text-[18px]">send</span> Enviar Solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>