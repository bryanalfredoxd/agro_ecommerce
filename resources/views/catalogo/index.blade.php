@extends('layouts.app')

@section('title', 'Catálogo de Insumos - Agropecuaria Venezuela')

@section('content')
<div class="bg-gray-50 min-h-screen font-sans animate-fade-in-up">

    <div class="relative bg-gradient-to-br from-agro-dark via-teal-900 to-primary/90 text-white pb-32 pt-16 shadow-xl overflow-hidden">
        <div class="absolute inset-0 opacity-20 mix-blend-overlay bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-primary/30 blur-3xl animate-pulse-slow"></div>
        
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center lg:text-left">
            <h1 class="text-4xl md:text-5xl font-black tracking-tight mb-4 drop-shadow-sm">
                Catálogo de <span class="text-green-300">Insumos</span>
            </h1>
            <p class="text-green-50 text-lg max-w-2xl mx-auto lg:mx-0 font-medium leading-relaxed">
                Calidad certificada para tu producción. Encuentra las mejores marcas y productos veterinarios.
            </p>
        </div>
    </div>

    <div id="contenido-catalogo" class="container mx-auto px-4 sm:px-6 lg:px-8 -mt-20 pb-12 relative z-20 scroll-mt-24">
        
        <div class="flex flex-col lg:flex-row gap-8">

            <aside class="w-full lg:w-72 flex-shrink-0">
                
                <button id="mobile-filter-btn" onclick="toggleFilters()" 
                        class="lg:hidden w-full flex items-center justify-center gap-2 bg-white text-agro-dark px-4 py-4 rounded-xl shadow-md font-bold mb-6 border-0 ring-1 ring-gray-100 active:scale-95 transition-all">
                    <span class="material-symbols-outlined text-primary">tune</span>
                    Filtrar Productos
                </button>

                <div id="filters-panel" class="hidden lg:block bg-white/95 backdrop-blur-md rounded-2xl shadow-xl shadow-gray-200/50 border border-white/50 p-6 sticky top-6 transition-all duration-300">
                    
                    <form action="{{ route('catalogo') }}" method="GET" class="mb-8 relative group">
                        {{-- Mantenemos los filtros actuales al buscar --}}
                        @if(request('categoria')) <input type="hidden" name="categoria" value="{{ request('categoria') }}"> @endif
                        @if(request('marca')) <input type="hidden" name="marca" value="{{ request('marca') }}"> @endif
                        @if(request('orden')) <input type="hidden" name="orden" value="{{ request('orden') }}"> @endif
                        
                        <input type="text" name="buscar" value="{{ request('buscar') }}" 
                               class="w-full pl-11 pr-4 py-3 rounded-xl bg-gray-100/80 border-transparent focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/20 transition-all text-sm font-medium text-gray-800 placeholder-gray-400" 
                               placeholder="Buscar insumo...">
                        <span class="material-symbols-outlined absolute left-3 top-3 text-gray-400 group-focus-within:text-primary transition-colors duration-300">search</span>
                    </form>

                    <div class="mb-8">
                        <h3 class="font-bold text-agro-dark text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg text-primary">category</span> Categorías
                        </h3>
                        <nav class="space-y-2 max-h-[350px] overflow-y-auto pr-2 custom-scrollbar">
                            <a href="{{ route('catalogo') }}" 
                               class="flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition-all duration-300 group {{ !request('categoria') ? 'bg-primary text-white shadow-lg shadow-primary/30 scale-[1.02]' : 'text-gray-700 bg-gray-50 hover:bg-gray-100 hover:text-primary' }}">
                                <span>Todas</span>
                                @if(!request('categoria'))
                                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                                @endif
                            </a>

                            @foreach($categorias as $cat)
                                <a href="{{ route('catalogo', array_merge(request()->except('page'), ['categoria' => $cat->id])) }}" 
                                   class="flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition-all duration-300 group {{ request('categoria') == $cat->id ? 'bg-primary text-white shadow-lg shadow-primary/30 scale-[1.02]' : 'text-gray-700 bg-gray-50 hover:bg-gray-100 hover:text-primary' }}">
                                    <span class="line-clamp-1">{{ $cat->nombre }}</span>
                                    @if(request('categoria') == $cat->id)
                                        <span class="material-symbols-outlined text-[20px]">check_circle</span>
                                    @else
                                        <span class="material-symbols-outlined text-[20px] opacity-0 group-hover:opacity-100 text-primary/60 transition-opacity">chevron_right</span>
                                    @endif
                                </a>
                            @endforeach
                        </nav>
                    </div>

                    <div class="mb-6">
                        <h3 class="font-bold text-agro-dark text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                             <span class="material-symbols-outlined text-lg text-primary">verified</span> Marcas
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($marcas as $marca)
                                <a href="{{ route('catalogo', array_merge(request()->except('page'), ['marca' => $marca->id])) }}" 
                                   class="px-3 py-1.5 rounded-lg text-xs font-bold border transition-all duration-200 {{ request('marca') == $marca->id ? 'bg-agro-dark text-white border-agro-dark shadow-md' : 'bg-white text-gray-600 border-gray-200 hover:border-primary hover:text-primary hover:bg-primary/5' }}">
                                    {{ $marca->nombre }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    @if(request()->hasAny(['categoria', 'marca', 'buscar']))
                        <a href="{{ route('catalogo') }}" class="flex items-center justify-center gap-2 w-full py-3 mt-6 text-sm text-red-500 font-bold bg-red-50 rounded-xl hover:bg-red-100 hover:shadow-sm transition-all duration-300 group">
                            <span class="material-symbols-outlined text-[20px] group-hover:rotate-180 transition-transform duration-500">restart_alt</span>
                            Limpiar Filtros
                        </a>
                    @endif
                </div>
            </aside>

            <main class="flex-1">
                
                <div class="bg-white/80 backdrop-blur-sm p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4 transition-all hover:shadow-md">
                    <p class="text-gray-600 text-sm font-medium">
                        Mostrando <span class="font-black text-agro-dark">{{ $productos->firstItem() ?? 0 }} - {{ $productos->lastItem() ?? 0 }}</span> de <span class="font-black text-agro-dark">{{ $productos->total() }}</span> resultados
                    </p>
                    
                    <div class="flex items-center gap-3">
                        <label class="text-xs font-bold text-gray-500 uppercase hidden sm:block">Ordenar por:</label>
                        <div class="relative group">
                            <select onchange="location = this.value;" class="appearance-none bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-primary focus:border-primary block w-full p-2.5 pr-10 font-bold cursor-pointer hover:bg-white hover:shadow-sm transition-all">
                                <option value="{{ route('catalogo', array_merge(request()->all(), ['orden' => 'reciente'])) }}" {{ request('orden') == 'reciente' ? 'selected' : '' }}>Más Nuevos</option>
                                <option value="{{ route('catalogo', array_merge(request()->all(), ['orden' => 'precio_asc'])) }}" {{ request('orden') == 'precio_asc' ? 'selected' : '' }}>Precio: Bajo a Alto</option>
                                <option value="{{ route('catalogo', array_merge(request()->all(), ['orden' => 'precio_desc'])) }}" {{ request('orden') == 'precio_desc' ? 'selected' : '' }}>Precio: Alto a Bajo</option>
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
                                        <button class="bg-primary text-white p-3 rounded-full hover:bg-green-600 hover:scale-110 transition-all shadow-lg transform translate-y-8 group-hover:translate-y-0 duration-500 ease-out delay-100">
                                            <span class="material-symbols-outlined">add_shopping_cart</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="p-6 flex-1 flex flex-col bg-white relative z-10">
                                    <div class="mb-3">
                                        <a href="{{ route('catalogo', ['categoria' => $producto->categoria_id]) }}" class="inline-block text-[11px] font-black text-primary bg-primary/10 px-3 py-1 rounded-full uppercase tracking-wider hover:bg-primary hover:text-white transition-colors">
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
                                        
                                        <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-primary group-hover:text-white group-hover:shadow-lg group-hover:shadow-primary/30 transition-all duration-300 transform group-hover:rotate-12 cursor-pointer">
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
                        <a href="{{ route('catalogo') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 border border-transparent text-base font-bold rounded-2xl text-white bg-agro-dark hover:bg-primary shadow-lg shadow-agro-dark/20 hover:shadow-primary/40 transform hover:-translate-y-1 transition-all duration-300">
                            <span class="material-symbols-outlined">restart_alt</span>
                            Limpiar Filtros
                        </a>
                    </div>
                @endif
            </main>

        </div>
    </div>
</div>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.6s ease-out forwards;
    }
    @keyframes pulseSlow {
        0%, 100% { opacity: 0.3; transform: scale(1); }
        50% { opacity: 0.15; transform: scale(1.1); }
    }
    .animate-pulse-slow {
        animation: pulseSlow 8s infinite ease-in-out;
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 5px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent; 
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #d1d5db; 
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #10B981; 
    }
</style>
@endsection