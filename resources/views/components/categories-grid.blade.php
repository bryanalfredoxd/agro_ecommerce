<section class="py-16 bg-gradient-to-b from-white to-gray-50 relative overflow-hidden">
    
    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-primary/50 to-transparent"></div>

    <div class="layout-container w-full relative z-10 px-4 sm:px-0">
        
        <div class="flex flex-col md:flex-row justify-between items-end mb-10 gap-4">
            <div class="text-center md:text-left w-full md:w-auto">
                <h3 class="text-primary font-black uppercase tracking-widest text-xs mb-2">Departamentos</h3>
                <h2 class="text-3xl md:text-4xl font-black text-agro-dark">
                    Categorías <span class="relative inline-block">
                        Principales
                        <span class="absolute bottom-1 left-0 w-full h-2 bg-primary/20 -z-10 rounded-sm"></span>
                    </span>
                </h2>
            </div>
            
            <a href="{{ route('catalogo') }}" class="hidden md:flex items-center gap-2 text-agro-dark font-bold hover:text-primary transition-all group bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100 hover:shadow-md">
                <span>Ver todo el catálogo</span>
                <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </a>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-6">
            
            @foreach($categoriasPrincipales->take(15) as $categoria)
                <a href="{{ route('catalogo', ['categoria' => $categoria->id]) }}" 
                   class="group relative flex flex-col bg-white rounded-2xl shadow-sm hover:shadow-xl hover:shadow-primary/5 border border-gray-100 overflow-hidden transition-all duration-500 hover:-translate-y-1.5 h-full">
                    
                    <div class="aspect-[4/3] w-full bg-gray-50 relative overflow-hidden flex items-center justify-center">
                        @if($categoria->imagen_url)
                            {{-- Verificar si la imagen existe físicamente --}}
                            @php
                                $imagePath = public_path($categoria->imagen_url);
                                $imageExists = file_exists($imagePath);
                            @endphp
                            
                            @if($imageExists)
                                {{-- Usar asset directamente sin storage --}}
                                <div class="absolute inset-0 bg-cover bg-center group-hover:scale-110 transition-transform duration-700 ease-in-out" 
                                     style="background-image: url('{{ asset($categoria->imagen_url) }}');">
                                </div>
                            @else
                                {{-- Fallback si la imagen no existe físicamente --}}
                                <div class="absolute inset-0 bg-cover bg-center group-hover:scale-110 transition-transform duration-700 ease-in-out" 
                                     style="background-image: url('https://placehold.co/400x300/F3F4F6/10B981?text={{ urlencode($categoria->nombre) }}');">
                                </div>
                            @endif
                        @else
                            {{-- Placeholder si no hay imagen --}}
                            <div class="absolute inset-0 bg-cover bg-center group-hover:scale-110 transition-transform duration-700 ease-in-out" 
                                 style="background-image: url('https://placehold.co/400x300/F3F4F6/10B981?text={{ urlencode($categoria->nombre) }}');">
                            </div>
                        @endif
                        
                        {{-- Overlay sutil que le da profundidad a la imagen --}}
                        <div class="absolute inset-0 bg-agro-dark/5 group-hover:bg-agro-dark/10 transition-colors duration-500"></div>
                    </div>

                    <div class="p-4 md:p-5 text-center flex-1 flex flex-col justify-start relative bg-white transition-colors z-10">
                        
                        {{-- Contenedor del ícono --}}
                        <div class="w-12 h-12 md:w-14 md:h-14 bg-white text-agro-dark rounded-xl shadow-md flex items-center justify-center mx-auto mb-3 -mt-10 md:-mt-12 relative z-20 group-hover:-translate-y-1 group-hover:bg-primary group-hover:shadow-lg transition-all duration-300 border-2 border-white">
                             <span class="font-black text-xl md:text-2xl">{{ substr($categoria->nombre, 0, 1) }}</span>
                        </div>

                        <h4 class="font-bold text-agro-dark text-sm md:text-base group-hover:text-primary transition-colors line-clamp-2 leading-tight">
                            {{ $categoria->nombre }}
                        </h4>
                        
                        {{-- Cambié $categoria->hijos por $categoria->subcategorias basado en el Modelo que creamos --}}
                        <p class="text-[10px] md:text-[11px] font-semibold uppercase tracking-wider text-gray-400 mt-2">
                            {{ $categoria->subcategorias ? $categoria->subcategorias->count() : 0 }} Subcategorías
                        </p>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8 md:hidden">
            <a href="{{ route('catalogo') }}" class="flex items-center justify-center gap-2 w-full py-4 rounded-xl bg-agro-dark text-white font-bold text-sm shadow-lg shadow-agro-dark/20 active:scale-95 transition-all">
                <span>Explorar todos los departamentos</span>
                <span class="material-symbols-outlined text-[20px]">grid_view</span>
            </a>
        </div>
    </div>
</section>