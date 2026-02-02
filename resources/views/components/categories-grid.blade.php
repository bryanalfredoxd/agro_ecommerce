<section class="py-16 bg-gradient-to-b from-white to-gray-50 relative overflow-hidden">
    
    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-primary/50 to-transparent"></div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
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
            
            @foreach($categoriasPrincipales->take(5) as $categoria)
                <a href="{{ route('catalogo', ['categoria' => $categoria->id]) }}" 
                   class="group relative flex flex-col bg-white rounded-2xl shadow-sm hover:shadow-xl hover:shadow-primary/10 border border-gray-100 overflow-hidden transition-all duration-300 hover:-translate-y-1 h-full">
                    
                    <div class="aspect-[4/3] w-full bg-gray-100 relative overflow-hidden">
                        {{-- Fallback de imagen --}}
                        <div class="absolute inset-0 bg-cover bg-center group-hover:scale-110 transition-transform duration-700 ease-out" 
                             style="background-image: url('{{ $categoria->imagen_url ?? 'https://placehold.co/400x300/F3F4F6/10B981?text=' . urlencode($categoria->nombre) }}');">
                        </div>
                        
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>

                    <div class="p-4 text-center flex-1 flex flex-col justify-center relative bg-white group-hover:bg-green-50/30 transition-colors">
                        <div class="w-10 h-10 bg-primary/10 text-primary rounded-full flex items-center justify-center mx-auto mb-2 group-hover:scale-110 group-hover:bg-primary group-hover:text-white transition-all duration-300">
                             {{-- Primera letra como icono --}}
                             <span class="font-black text-lg">{{ substr($categoria->nombre, 0, 1) }}</span>
                        </div>

                        <h4 class="font-bold text-gray-800 text-sm md:text-base group-hover:text-primary transition-colors line-clamp-1">
                            {{ $categoria->nombre }}
                        </h4>
                        
                        <p class="text-[10px] uppercase tracking-wide text-gray-400 mt-1 group-hover:text-gray-500">
                            {{-- Validación segura por si hijos es null --}}
                            {{ $categoria->hijos ? $categoria->hijos->count() : 0 }} Subcategorías
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