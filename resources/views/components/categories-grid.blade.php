<section class="py-16 bg-background-light">
    <div class="layout-container">
        <div class="flex justify-between items-end mb-10">
            <div>
                <h3 class="text-agro-accent font-bold uppercase tracking-wider text-sm mb-2">Departamentos</h3>
                <h2 class="text-3xl font-bold text-agro-dark">Categorías Principales</h2>
            </div>
            <a class="hidden md:flex items-center gap-1 text-agro-dark font-semibold hover:text-primary transition-colors" href="{{ route('categorias.index') }}">
                Ver todas
                <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
            </a>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach($categorias as $categoria)
            <a class="group flex flex-col gap-3 bg-white p-4 rounded-xl shadow-sm border border-transparent hover:border-primary/50 hover:shadow-md transition-all" href="#">
                <div class="aspect-square w-full rounded-lg overflow-hidden bg-gray-100 relative">
                    <div class="absolute inset-0 bg-cover bg-center group-hover:scale-110 transition-transform duration-500" 
                         style="background-image: url('{{ $categoria->imagen_url ?? asset('images/default-category.jpg') }}');">
                    </div>
                </div>
                <div class="text-center">
                    <h4 class="font-bold text-agro-dark group-hover:text-agro-accent">{{ $categoria->nombre }}</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $categoria->hijos->count() }} Subcategorías
                    </p>
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-8 md:hidden">
            <a class="flex items-center justify-center gap-2 w-full py-3 rounded-lg bg-agro-dark hover:bg-agro-dark/90 text-white font-semibold text-sm transition-colors" href="#">
                <span>Ver todas las categorías</span>
                <span class="material-symbols-outlined text-[20px]">
                    arrow_forward
                </span>
            </a>
        </div>
    </div>
</section>