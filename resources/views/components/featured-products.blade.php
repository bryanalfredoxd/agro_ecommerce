<section class="py-12 sm:py-16 bg-white relative">
    
    {{-- Alineación corregida usando tu layout-container estándar --}}
    <div class="layout-container w-full relative z-10 px-4 sm:px-0">
        
        <div class="mb-10 md:mb-12 max-w-3xl">
            <span class="inline-block py-1 px-3 rounded-full bg-primary/10 text-primary font-bold uppercase tracking-wider text-xs mb-3">
                Selección del Mes
            </span>
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-black text-agro-dark leading-tight">
                Productos Más <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-agro-dark">Vendidos</span>
            </h2>
            <p class="text-gray-500 text-sm md:text-base mt-4 leading-relaxed">
                Nuestra selección premium de insumos agropecuarios, avalada por la compra recurrente de nuestros productores certificados.
            </p>
        </div>
        
        {{-- Reemplacé xs:grid-cols-2 por sm:grid-cols-2 para respetar los breakpoints nativos de Tailwind --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            
            @foreach($productosDestacados as $producto)
            <article class="group relative flex flex-col bg-white rounded-2xl border border-gray-100 hover:border-primary/30 hover:shadow-xl hover:shadow-primary/5 transition-all duration-300 overflow-hidden h-full">
                
                {{-- ÁREA DE IMAGEN: Más limpia, sin overlays intrusivos --}}
                <div class="relative w-full aspect-[4/3] bg-gray-50 overflow-hidden">
                    
                    @php
                        $img = $producto->imagenes->where('es_principal', 1)->first()?->url_imagen 
                               ?? $producto->imagenes->first()?->url_imagen 
                               ?? null;
                    @endphp

                    @if($img)
                        <div class="absolute inset-0 bg-cover bg-center group-hover:scale-105 transition-transform duration-700 ease-out mix-blend-multiply" 
                             style="background-image: url('{{ $img }}');"></div>
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

                    {{-- Botón Favorito: Movido a la imagen superior derecha (Estándar E-commerce) --}}
                    <div class="absolute top-3 right-3 z-20">
                        <button class="flex items-center justify-center w-8 h-8 rounded-full bg-white text-gray-400 hover:text-red-500 hover:bg-red-50 hover:shadow-md transition-all duration-300" title="Añadir a favoritos">
                            <span class="material-symbols-outlined text-[18px] hover:fill-current">favorite</span>
                        </button>
                    </div>
                    
                    {{-- Unidad de medida --}}
                    <div class="absolute bottom-3 left-3 pointer-events-none">
                        <span class="inline-block bg-white/95 backdrop-blur-sm text-agro-dark text-[10px] font-bold px-2 py-1 rounded-md shadow-sm uppercase tracking-wider">
                            {{ $producto->unidad_medida }}
                        </span>
                    </div>
                </div>
                
                {{-- ÁREA DE INFORMACIÓN --}}
                <div class="p-5 flex flex-col flex-1 relative bg-white">
                    
                    {{-- Fila superior: Marca y Estado de Stock --}}
                    <div class="flex justify-between items-center mb-2">
                        <p class="text-[11px] text-agro-accent font-bold uppercase tracking-wider flex items-center gap-1 truncate pr-2">
                            <span class="material-symbols-outlined text-[14px]">verified</span>
                            {{ $producto->marca->nombre ?? 'GENÉRICO' }}
                        </p>
                        
                        {{-- Indicador de Stock reubicado --}}
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
                    <h3 class="text-agro-dark font-bold text-base leading-snug mb-2 line-clamp-2 min-h-[2.5rem] group-hover:text-primary transition-colors">
                        <a href="#" class="focus:outline-none before:absolute before:inset-0">
                            {{ $producto->nombre }}
                        </a>
                    </h3>
                    
                    {{-- Descripción --}}
                    <p class="text-xs text-gray-500 mb-4 line-clamp-2 flex-grow leading-relaxed relative z-20">
                        {{ $producto->descripcion }}
                    </p>
                    
                    {{-- Fila Inferior: Precios y Call to Action (CTA) --}}
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
                        
                        {{-- BOTÓN AÑADIR AL CARRITO EXPLÍCITO Y SIEMPRE VISIBLE --}}
                        <button class="flex items-center justify-center w-11 h-11 rounded-xl bg-primary/10 text-agro-dark hover:bg-primary hover:text-white hover:shadow-lg hover:shadow-primary/20 transition-all duration-300 group/btn" title="Añadir al carrito">
                            <span class="material-symbols-outlined text-[22px] group-active/btn:scale-95 transition-transform">add_shopping_cart</span>
                        </button>
                    </div>

                </div>
            </article>
            @endforeach
        </div>
        
        <div class="mt-12 md:mt-16 text-center">
            <a href="{{ route('catalogo') }}" class="inline-flex items-center gap-3 px-8 py-4 rounded-xl bg-agro-dark hover:bg-primary text-white font-bold text-sm md:text-base transition-all duration-300 hover:shadow-xl hover:shadow-primary/20 transform hover:-translate-y-1 group">
                <span>Explorar Catálogo Completo</span>
                <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </a>
        </div>
    </div>
</section>