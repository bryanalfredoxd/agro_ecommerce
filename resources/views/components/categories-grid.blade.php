<section class="py-6 md:py-8 bg-white relative overflow-hidden border-b border-slate-100">
    <div class="layout-container w-full relative z-10 px-4 sm:px-0">
        
        {{-- Encabezado de la sección --}}
        <div class="flex flex-col md:flex-row justify-between items-end mb-6 gap-4 border-b border-slate-100 pb-3">
            <div class="text-center md:text-left w-full md:w-auto">
                <h2 class="text-2xl md:text-3xl font-black text-[#1e549e] tracking-tight">
                    Líneas de Productos <span class="text-slate-400 font-light">Especializadas</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">Explora por categorías agrícolas y pecuarias</p>
            </div>
            
            <a href="{{ route('catalogo') }}" class="hidden md:flex items-center gap-2 text-[#1e549e] font-bold hover:text-[#3492cf] transition-all bg-slate-50 px-4 py-2 rounded-xl border border-slate-200 hover:border-[#3492cf]/40 hover:bg-[#e5f2fb]">
                <span>Ver catálogo completo</span>
                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </a>
        </div>
        
        {{-- Grid de Tarjetas Inmersivas --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-4 md:gap-5">
            @foreach($categoriasPrincipales->take(10) as $categoria)
                <a href="{{ route('catalogo', ['categoria' => $categoria->id]) }}" 
                   class="group relative h-40 sm:h-48 md:h-52 rounded-2xl overflow-hidden shadow-xs hover:shadow-xl hover:shadow-[#3492cf]/20 transition-all duration-300 transform hover:-translate-y-1 bg-slate-900 border border-slate-200/50">
                    
                    {{-- Imagen de Fondo --}}
                    @if($categoria->imagen_url && file_exists(public_path($categoria->imagen_url)))
                        <div class="absolute inset-0 bg-cover bg-center group-hover:scale-105 transition-transform duration-500 ease-out" 
                             style="background-image: url('{{ asset($categoria->imagen_url) }}');">
                        </div>
                    @else
                        <div class="absolute inset-0 bg-cover bg-center group-hover:scale-105 transition-transform duration-500 ease-out bg-gradient-to-br from-[#1e549e] to-[#0b1d38]">
                        </div>
                    @endif
                    
                    {{-- Gradiente y Textos --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-transparent flex flex-col justify-end p-3.5 text-center items-center">
                        <h4 class="font-bold text-white text-sm sm:text-base leading-tight group-hover:text-[#aad6f3] transition-colors line-clamp-2">
                            {{ $categoria->nombre }}
                        </h4>
                        
                        <span class="text-[10px] text-slate-300 font-medium mt-1 flex items-center gap-1 bg-white/10 px-2 py-0.5 rounded-full backdrop-blur-xs">
                            <span class="material-symbols-outlined text-[12px] text-[#3492cf]">category</span>
                            Ver Insumos
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
        
        {{-- Botón Móvil --}}
        <div class="mt-6 md:hidden">
            <a href="{{ route('catalogo') }}" class="btn-outline w-full text-center">
                <span>Ver catálogo completo</span>
            </a>
        </div>
    </div>
</section>