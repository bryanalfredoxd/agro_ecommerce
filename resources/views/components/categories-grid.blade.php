<!-- Categories Grid -->
<section class="py-16 bg-background-light">
    <div class="layout-container">
        <div class="flex justify-between items-end mb-10">
            <div>
                <h3 class="text-agro-accent font-bold uppercase tracking-wider text-sm mb-2">Departamentos</h3>
                <h2 class="text-3xl font-bold text-agro-dark">Categorías Principales</h2>
            </div>
            <a class="hidden md:flex items-center gap-1 text-agro-dark font-semibold hover:text-primary transition-colors" href="#">
                Ver todas
                <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
            </a>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach([
                ['name' => 'Veterinaria', 'desc' => 'Vacunas y Medicinas', 'img' => 'https://images.unsplash.com/photo-1552053831-71594a27632d?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'],
                ['name' => 'Nutrición', 'desc' => 'Alimentos y Sales', 'img' => 'https://images.unsplash.com/photo-1579113800032-c38bd7635818?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'],
                ['name' => 'Semillas', 'desc' => 'Pastos y Hortalizas', 'img' => 'https://images.unsplash.com/photo-1591803264389-4e8c8c45f3e4?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'],
                ['name' => 'Fertilizantes', 'desc' => 'NPK y Foliares', 'img' => 'https://images.unsplash.com/photo-1595278069441-2cf29f8005a4?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'],
                ['name' => 'Ferretería', 'desc' => 'Implementos', 'img' => 'https://images.unsplash.com/photo-1589256469067-ea99122bbdc4?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80']
            ] as $category)
            <a class="group flex flex-col gap-3 bg-white p-4 rounded-xl shadow-sm border border-transparent hover:border-primary/50 hover:shadow-md transition-all" href="#">
                <div class="aspect-square w-full rounded-lg overflow-hidden bg-gray-100 relative">
                    <div class="absolute inset-0 bg-cover bg-center group-hover:scale-110 transition-transform duration-500" style="background-image: url('{{ $category['img'] }}');"></div>
                </div>
                <div class="text-center">
                    <h4 class="font-bold text-agro-dark group-hover:text-agro-accent">{{ $category['name'] }}</h4>
                    <p class="text-xs text-gray-500 mt-1">{{ $category['desc'] }}</p>
                </div>
            </a>
            @endforeach
        </div>

        <!-- Mobile View All Button -->
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