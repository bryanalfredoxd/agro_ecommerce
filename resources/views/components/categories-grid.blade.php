<section class="py-2 md:py-2 bg-white relative overflow-hidden">
    
    <div class="layout-container w-full relative z-10 px-4 sm:px-0">
        
        {{-- Encabezado de la sección --}}
        <div class="flex flex-col md:flex-row justify-between items-end mb-4 gap-4 border-b border-gray-100 pb-2">
            <div class="text-center md:text-left w-full md:w-auto">
                <h2 class="text-2xl md:text-4xl font-black text-agro-dark tracking-tight">
                    Categorías <span class="text-gray-700 font-light">Destacadas</span>
                </h2>
            </div>
            
            <a href="{{ route('catalogo') }}" class="hidden md:flex items-center gap-2 text-agro-dark font-bold hover:text-primary transition-all group bg-gray-50 px-5 py-2.5 rounded-full border border-gray-200 hover:border-primary/30 hover:bg-primary/5">
                <span>Ver todo el catálogo</span>
                <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </a>
        </div>
        
        {{-- Grid de Tarjetas Inmersivas --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-4 md:gap-5">
    
    @foreach($categoriasPrincipales->take(10) as $categoria)
        <a href="{{ route('catalogo', ['categoria' => $categoria->id]) }}" 
           class="group relative h-40 sm:h-48 md:h-56 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl hover:shadow-primary/20 transition-all duration-300 transform hover:-translate-y-1 bg-gray-900">
            
            {{-- 1. Imagen de Fondo --}}
            @if($categoria->imagen_url)
                @php
                    $imagePath = public_path($categoria->imagen_url);
                    $imageExists = file_exists($imagePath);
                @endphp
                
                @if($imageExists)
                    <div class="absolute inset-0 bg-cover bg-center group-hover:scale-110 transition-transform duration-700 ease-out" 
                         style="background-image: url('{{ asset($categoria->imagen_url) }}');">
                    </div>
                @else
                    <div class="absolute inset-0 bg-cover bg-center group-hover:scale-110 transition-transform duration-700 ease-out" 
                         style="background-image: url('https://placehold.co/400x400/1e293b/10B981?text={{ urlencode($categoria->nombre) }}');">
                    </div>
                @endif
            @else
                <div class="absolute inset-0 bg-cover bg-center group-hover:scale-110 transition-transform duration-700 ease-out" 
                     style="background-image: url('https://placehold.co/400x400/1e293b/10B981?text={{ urlencode($categoria->nombre) }}');">
                </div>
            @endif
            
            {{-- 2. Textos (Centrados y con sombra localizada) --}}
            <div class="absolute bottom-0 left-0 w-full p-4 sm:p-5 pt-16 flex flex-col justify-end items-center text-center bg-gradient-to-t from-black/90 via-black/40 to-transparent">
                
                <h4 class="font-black text-white text-sm sm:text-base md:text-lg leading-tight group-hover:text-primary transition-colors line-clamp-2 drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]">
                    {{ $categoria->nombre }}
                </h4>
                
                <p class="text-[9px] sm:text-[10px] text-gray-200 uppercase tracking-widest font-bold mt-1.5 opacity-100 flex items-center justify-center gap-1 drop-shadow-[0_1px_2px_rgba(0,0,0,0.8)]">
                    <span class="material-symbols-outlined text-[12px]">inventory_2</span>
                    {{ $categoria->subcategorias ? $categoria->subcategorias->count() : 0 }} Opciones
                </p>
                
            </div>
        </a>
    @endforeach
</div>

        {{-- Botón Móvil --}}
        <div class="mt-8 md:hidden">
            <a href="{{ route('catalogo') }}" class="flex items-center justify-center gap-2 w-full py-3.5 rounded-xl bg-gray-50 text-agro-dark font-bold text-sm border border-gray-200 active:bg-gray-100 transition-colors">
                <span>Ver todo el catálogo</span>
                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </a>
        </div>
        
    </div>
</section>