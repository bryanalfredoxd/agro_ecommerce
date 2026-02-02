<section class="py-12 sm:py-16 bg-white relative">
    <div class="layout-container container mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-10 md:mb-12 max-w-3xl">
            <span class="inline-block py-1 px-3 rounded-full bg-primary/10 text-primary font-bold uppercase tracking-wider text-xs mb-3">
                Selección del Mes
            </span>
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-black text-agro-dark leading-tight">
                Productos Más <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-teal-500">Vendidos</span>
            </h2>
            <p class="text-gray-500 text-sm md:text-base mt-4 leading-relaxed">
                Nuestra selección premium de insumos agropecuarios, avalada por la compra recurrente de nuestros productores certificados.
            </p>
        </div>
        
        <div class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            
            @foreach($productosDestacados as $producto)
            <div class="group relative flex flex-col bg-white rounded-2xl border border-gray-100 hover:border-primary/30 hover:shadow-[0_8px_30px_rgb(0,0,0,0.06)] transition-all duration-300 overflow-hidden h-full">
                
                <div class="relative w-full aspect-[4/3] bg-gray-50 overflow-hidden group-hover:bg-gray-100/50 transition-colors">
                    
                    @php
                        $img = $producto->imagenes->where('es_principal', 1)->first()?->url_imagen 
                               ?? $producto->imagenes->first()?->url_imagen 
                               ?? null;
                    @endphp

                    @if($img)
                        <div class="absolute inset-0 bg-cover bg-center group-hover:scale-110 transition-transform duration-700 mix-blend-multiply" 
                             style="background-image: url('{{ $img }}');"></div>
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                            <span class="material-symbols-outlined text-5xl mb-2">image</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest">Sin Imagen</span>
                        </div>
                    @endif
                    
                    <div class="absolute top-3 left-3 right-3 flex justify-between items-start z-10 pointer-events-none">
                        <div class="flex flex-col gap-1.5 items-start">
                            @if($producto->es_controlado)
                            <span class="inline-flex items-center gap-1 bg-red-500/90 backdrop-blur-md text-white text-[10px] font-black px-2.5 py-1 rounded-lg uppercase shadow-sm tracking-wide">
                                <span class="material-symbols-outlined text-[12px]">lock</span> Controlado
                            </span>
                            @endif

                            @if($producto->precio_oferta_usd)
                            <span class="inline-flex items-center gap-1 bg-primary text-white text-[10px] font-black px-2.5 py-1 rounded-lg uppercase shadow-sm tracking-wide">
                                Oferta
                            </span>
                            @endif
                        </div>
                        
                        <div>
                            @if($producto->stock_total > 0 && $producto->stock_total <= $producto->stock_minimo_alerta)
                            <span class="inline-flex items-center gap-1 bg-amber-400 text-white text-[10px] font-black px-2.5 py-1 rounded-lg shadow-sm uppercase tracking-wide">
                                <span class="material-symbols-outlined text-[12px]">inventory_2</span> Poco Stock
                            </span>
                            @elseif($producto->stock_total > 0)
                            <span class="inline-flex items-center gap-1 bg-green-500/10 text-green-700 backdrop-blur-md text-[10px] font-black px-2.5 py-1 rounded-lg shadow-sm border border-green-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Stock
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="absolute bottom-3 right-3 pointer-events-none">
                        <span class="inline-block bg-white/90 backdrop-blur-sm text-agro-dark text-[10px] font-bold px-2.5 py-1 rounded-lg border border-gray-100 shadow-sm uppercase tracking-wider">
                            {{ $producto->unidad_medida }}
                        </span>
                    </div>
                    
                    <div class="absolute inset-0 bg-agro-dark/10 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center gap-3 backdrop-blur-[1px]">
                        <button class="transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 bg-white text-agro-dark hover:text-primary p-3 rounded-xl shadow-lg hover:shadow-xl font-bold" title="Vista Rápida">
                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                        <button class="transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 delay-75 bg-primary text-white hover:bg-green-600 p-3 rounded-xl shadow-lg hover:shadow-xl hover:scale-105 font-bold" title="Añadir">
                            <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span>
                        </button>
                    </div>
                </div>
                
                <div class="p-5 flex flex-col flex-1">
                    <p class="text-[11px] text-agro-accent font-bold mb-1.5 uppercase tracking-wider flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">verified</span>
                        {{ $producto->marca->nombre ?? 'GENÉRICO' }}
                    </p>
                    
                    <h3 class="text-agro-dark font-bold text-base leading-snug mb-2 line-clamp-2 min-h-[2.5rem] group-hover:text-primary transition-colors">
                        <a href="#" class="focus:outline-none">
                            {{ $producto->nombre }}
                        </a>
                    </h3>
                    
                    <p class="text-xs text-gray-500 mb-4 line-clamp-2 flex-grow leading-relaxed">
                        {{ $producto->descripcion }}
                    </p>
                    
                    <div class="mt-auto pt-4 border-t border-gray-50">
                        <div class="flex items-end justify-between">
                            <div class="flex flex-col">
                                <div class="flex items-baseline gap-1.5">
                                    @if($producto->precio_oferta_usd)
                                        <span class="text-xl font-black text-agro-dark">${{ number_format($producto->precio_oferta_usd, 2) }}</span>
                                        <span class="text-xs text-red-400 line-through font-semibold">${{ number_format($producto->precio_venta_usd, 2) }}</span>
                                    @else
                                        <span class="text-xl font-black text-agro-dark">${{ number_format($producto->precio_venta_usd, 2) }}</span>
                                        <span class="text-[10px] text-gray-400 font-bold self-start mt-1">USD</span>
                                    @endif
                                </div>
                                <span class="text-[11px] text-gray-400 font-medium mt-0.5">
                                    ≈ Bs. {{ number_format(($producto->precio_oferta_usd ?? $producto->precio_venta_usd) * 60.50, 2, ',', '.') }}
                                </span>
                            </div>
                            
                            <button class="w-10 h-10 rounded-xl border border-gray-200 text-gray-400 hover:border-red-200 hover:bg-red-50 hover:text-red-500 flex items-center justify-center transition-all duration-300">
                                <span class="material-symbols-outlined text-[20px] fill-current">favorite</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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