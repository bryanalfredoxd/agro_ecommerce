<section class="relative w-full bg-agro-dark overflow-hidden h-[320px] md:h-[340px] lg:h-[360px]">
    
    {{-- CARRUSEL DE IMÁGENES DE FONDO --}}
    <div id="hero-slider" class="absolute inset-0 z-0">
        {{-- Imagen 1 (Cultivos / Frescura) --}}
        <div class="hero-slide absolute inset-0 bg-cover bg-center transition-all duration-1000 ease-in-out opacity-100 scale-105" 
             style="background-image: url('{{ asset('img/hero/tractor.jpg') }}');">
        </div>
        {{-- Imagen 2 (Tecnología / Invernadero) --}}
        <div class="hero-slide absolute inset-0 bg-cover bg-center transition-all duration-1000 ease-in-out opacity-0 scale-100" 
             style="background-image: url('{{ asset('https://images.unsplash.com/photo-1500382017468-9049fed747ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80') }}');">
        </div>
        {{-- Imagen 3 (Maquinaria / Campo abierto) --}}
        <div class="hero-slide absolute inset-0 bg-cover bg-center transition-all duration-1000 ease-in-out opacity-0 scale-100" 
             style="background-image: url('{{ asset('https://images.unsplash.com/photo-1625246333195-78d9c38ad449?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80') }}');">
        </div>
        
        {{-- Capa oscura para que el texto sea legible siempre --}}
        <div class="absolute inset-0 bg-gradient-to-r from-agro-dark/95 via-agro-dark/80 to-agro-dark/40 md:via-agro-dark/60 md:to-transparent z-10"></div>
    </div>
    
    {{-- Contenido del Hero (Centrado verticalmente sin padding extra abajo) --}}
    <div class="layout-container relative z-10 w-full h-full flex flex-col justify-center px-4 sm:px-0">
        <div class="max-w-xl md:max-w-2xl w-full animate-fade-in-up mt-4">
            
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-white leading-[1.1] tracking-tight mb-3">
                Soluciones integrales <br class="hidden sm:block" />
                para el <span class="text-primary mt-1 inline-block">campo venezolano</span>
            </h1>
            
            <p class="text-sm sm:text-base text-gray-200 mb-6 font-light leading-relaxed max-w-lg text-pretty">
                Desde semillas certificadas hasta maquinaria especializada. 
                Todo lo que necesita su unidad de producción, con entrega directa a nivel nacional.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 w-full sm:w-auto">
                <a href="{{ route('catalogo') }}" class="group relative flex items-center justify-center gap-2 h-12 px-6 rounded-xl bg-primary hover:bg-primary/90 text-agro-dark font-bold text-sm transition-all duration-300 shadow-lg shadow-primary/20 hover:shadow-primary/40 w-full sm:w-auto overflow-hidden">
                    <div class="absolute inset-0 -translate-x-full group-hover:animate-[shimmer_1.5s_infinite] bg-gradient-to-r from-transparent via-white/20 to-transparent z-0"></div>
                    <span class="relative z-10">Explorar Catálogo</span>
                    <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform relative z-10">
                        arrow_forward
                    </span>
                </a>
                
            </div>

        </div>
    </div>
</section>

{{-- Script autónomo para el carrusel del Hero --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const slides = document.querySelectorAll('.hero-slide');
        let currentSlide = 0;
        
        if(slides.length > 1) {
            setInterval(() => {
                // Ocultar slide actual y quitar el zoom (scale)
                slides[currentSlide].classList.remove('opacity-100', 'scale-105');
                slides[currentSlide].classList.add('opacity-0', 'scale-100');
                
                // Pasar al siguiente
                currentSlide = (currentSlide + 1) % slides.length;
                
                // Mostrar nuevo slide y aplicar zoom suave
                slides[currentSlide].classList.remove('opacity-0', 'scale-100');
                slides[currentSlide].classList.add('opacity-100', 'scale-105');
            }, 4000); // Cambia cada 4 segundos
        }
    });
</script>