<!-- Featured Products -->
<section class="py-10 sm:py-12 md:py-16 bg-white">
    <div class="layout-container">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 mb-8 md:mb-12">
            <div class="max-w-2xl">
                <span class="text-agro-accent font-bold uppercase tracking-wider text-xs md:text-sm mb-1 md:mb-2 block">
                    Destacados del Mes
                </span>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-agro-dark leading-tight">
                    Productos Más <span class="text-agro-accent">Vendidos</span>
                </h2>
                <p class="text-gray-600 text-sm md:text-base mt-2 md:mt-3 max-w-xl">
                    Selección de los productos agropecuarios más demandados por nuestros clientes
                </p>
            </div>
            
            <!-- Filter Tabs - Carousel Style -->
            <div class="w-full sm:w-auto">
                <div class="flex items-center gap-1 sm:gap-2 overflow-x-auto pb-3 scrollbar-hide">
                    <button class="shrink-0 px-4 sm:px-5 py-2 rounded-full bg-agro-dark text-white text-xs sm:text-sm font-semibold whitespace-nowrap transition-all hover:scale-105 active:scale-95">
                        Todos
                    </button>
                    <button class="shrink-0 px-4 sm:px-5 py-2 rounded-full bg-gray-100 text-agro-dark hover:bg-gray-200 text-xs sm:text-sm font-medium whitespace-nowrap transition-all">
                        Veterinaria
                    </button>
                    <button class="shrink-0 px-4 sm:px-5 py-2 rounded-full bg-gray-100 text-agro-dark hover:bg-gray-200 text-xs sm:text-sm font-medium whitespace-nowrap transition-all">
                        Semillas
                    </button>
                    <button class="shrink-0 px-4 sm:px-5 py-2 rounded-full bg-gray-100 text-agro-dark hover:bg-gray-200 text-xs sm:text-sm font-medium whitespace-nowrap transition-all">
                        Nutrición
                    </button>
                    <button class="shrink-0 px-4 sm:px-5 py-2 rounded-full bg-gray-100 text-agro-dark hover:bg-gray-200 text-xs sm:text-sm font-medium whitespace-nowrap transition-all">
                        Fertilizantes
                    </button>
                    <button class="shrink-0 px-4 sm:px-5 py-2 rounded-full bg-gray-100 text-agro-dark hover:bg-gray-200 text-xs sm:text-sm font-medium whitespace-nowrap transition-all">
                        Maquinaria
                    </button>
                    <button class="shrink-0 px-4 sm:px-5 py-2 rounded-full bg-gray-100 text-agro-dark hover:bg-gray-200 text-xs sm:text-sm font-medium whitespace-nowrap transition-all">
                        Ferretería
                    </button>
                </div>
                
                <!-- Scroll Indicator (Mobile only) -->
                <div class="sm:hidden flex justify-center mt-2">
                    <div class="flex items-center gap-1">
                        <div class="size-1.5 rounded-full bg-gray-300"></div>
                        <div class="size-1.5 rounded-full bg-gray-400"></div>
                        <div class="size-1.5 rounded-full bg-gray-300"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
            @foreach([
                [
                    'brand' => 'Calox', 
                    'name' => 'Ivermectina 1% Inyectable', 
                    'desc' => 'Antiparasitario para bovinos y porcinos.', 
                    'price' => '12.50', 
                    'img' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80', 
                    'controlled' => true,
                    'unit' => 'Frasco 100ml',
                    'stock' => true
                ],
                [
                    'brand' => 'Dekalb', 
                    'name' => 'Maíz Blanco Híbrido', 
                    'desc' => 'Alto rendimiento, semillas certificadas.', 
                    'price' => '180.00', 
                    'img' => 'https://images.unsplash.com/photo-1591803264389-4e8c8c45f3e4?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80', 
                    'controlled' => false,
                    'unit' => 'Saco 25kg',
                    'stock' => true
                ],
                [
                    'brand' => 'Purina', 
                    'name' => 'Concentrado Iniciador', 
                    'desc' => 'Para pollos de engorde. Máxima nutrición.', 
                    'price' => '35.00', 
                    'img' => 'https://images.unsplash.com/photo-1579113800032-c38bd7635818?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80', 
                    'controlled' => false,
                    'unit' => 'Saco 40kg',
                    'stock' => true
                ],
                [
                    'brand' => 'RoyalCondor', 
                    'name' => 'Fumigadora 20L', 
                    'desc' => 'Resistente a químicos. Garantía 1 año.', 
                    'price' => '45.00', 
                    'img' => 'https://images.unsplash.com/photo-1605000797499-95a51c5269ae?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80', 
                    'controlled' => false,
                    'unit' => 'Unidad',
                    'stock' => false
                ]
            ] as $product)
            <div class="group relative flex flex-col bg-white rounded-xl border border-gray-200 hover:border-primary/40 hover:shadow-xl transition-all duration-300 overflow-hidden">
                <!-- Product Image -->
                <div class="relative w-full aspect-[4/3] bg-gray-100 overflow-hidden">
                    <div class="absolute inset-0 bg-cover bg-center group-hover:scale-110 transition-transform duration-500" 
                         style="background-image: url('{{ $product['img'] }}');"></div>
                    
                    <!-- Top Badges Container -->
                    <div class="absolute top-3 left-3 right-3 flex justify-between items-start z-10">
                        <!-- Controlled Badge -->
                        @if($product['controlled'])
                        <span class="inline-flex items-center gap-1 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded-full uppercase shadow-sm">
                            <span class="material-symbols-outlined text-[12px]">lock</span>
                            <span>Controlado</span>
                        </span>
                        @else
                        <div></div> <!-- Empty spacer -->
                        @endif
                        
                        <!-- Stock Badge -->
                        <div class="@if($product['controlled']) ml-2 @endif">
                            @if($product['stock'])
                            <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 text-[10px] font-bold px-2 py-1 rounded-full shadow-sm">
                                <span class="size-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                <span>En stock</span>
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-800 text-[10px] font-bold px-2 py-1 rounded-full shadow-sm">
                                <span class="material-symbols-outlined text-[12px]">schedule</span>
                                <span>Próximo</span>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Unit Badge -->
                    <div class="absolute bottom-3 right-3">
                        <span class="inline-block bg-white/90 backdrop-blur-sm text-agro-dark text-[10px] font-semibold px-2 py-1 rounded-md shadow-sm">
                            {{ $product['unit'] }}
                        </span>
                    </div>
                    
                    <!-- Quick View Overlay -->
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <button class="transform translate-y-2 group-hover:translate-y-0 transition-all duration-300 bg-white text-agro-dark font-semibold text-sm px-4 py-2 rounded-lg shadow-lg hover:bg-primary hover:text-white">
                            Vista Rápida
                        </button>
                    </div>
                </div>
                
                <!-- Product Info -->
                <div class="p-3 sm:p-4 flex flex-col flex-1">
                    <!-- Brand -->
                    <p class="text-xs text-agro-accent font-semibold mb-1 uppercase tracking-wide">
                        {{ $product['brand'] }}
                    </p>
                    
                    <!-- Product Name -->
                    <h3 class="text-agro-dark font-bold text-sm sm:text-base leading-tight mb-2 line-clamp-2 min-h-[2.5rem]">
                        {{ $product['name'] }}
                    </h3>
                    
                    <!-- Description -->
                    <p class="text-xs sm:text-sm text-gray-600 mb-3 sm:mb-4 line-clamp-2 flex-grow">
                        {{ $product['desc'] }}
                    </p>
                    
                    <!-- Price & CTA -->
                    <div class="mt-auto pt-3 sm:pt-4 border-t border-gray-100">
                        <!-- Price Container -->
                        <div class="flex items-end justify-between mb-2 sm:mb-3">
                            <!-- USD Price -->
                            <div class="flex flex-col">
                                <div class="flex items-baseline gap-1">
                                    <span class="text-lg sm:text-xl font-black text-agro-dark">
                                        ${{ $product['price'] }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        USD
                                    </span>
                                </div>
                                <!-- Bs. Price -->
                                <span class="text-xs text-gray-500 mt-0.5">
                                    ≈ Bs. {{ number_format($product['price'] * 36.5, 0, ',', '.') }}
                                </span>
                            </div>
                            
                            <!-- Save Badge (Optional) -->
                            @if($product['price'] > 40)
                            <span class="text-[10px] font-bold bg-primary/20 text-primary px-2 py-1 rounded">
                                -15%
                            </span>
                            @endif
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2">
                            <button class="flex-1 h-9 sm:h-10 rounded-lg bg-primary hover:bg-primary/90 text-agro-dark font-semibold text-xs sm:text-sm transition-all duration-300 flex items-center justify-center gap-1 sm:gap-2 group/cta hover:scale-[1.02]">
                                <span class="material-symbols-outlined text-[16px] sm:text-[18px]">add_shopping_cart</span>
                                <span class="hidden xs:inline">Agregar</span>
                            </button>
                            <button class="h-9 sm:h-10 w-9 sm:w-10 rounded-lg border border-gray-300 hover:border-agro-dark text-agro-dark hover:bg-gray-50 flex items-center justify-center transition-all">
                                <span class="material-symbols-outlined text-[18px]">favorite</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- View All Button -->
        <div class="mt-10 sm:mt-12 md:mt-16 text-center">
            <a href="#" class="inline-flex items-center gap-2 px-6 sm:px-8 py-3 sm:py-4 rounded-lg bg-agro-dark hover:bg-agro-dark/90 text-white font-semibold text-sm sm:text-base transition-all duration-300 hover:scale-105 shadow-lg">
                <span>Ver Todo el Catálogo</span>
                <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
            </a>
        </div>
    </div>
</section>