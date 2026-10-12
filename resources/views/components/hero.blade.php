<section class="relative w-full bg-[#0b1d38] overflow-hidden h-[340px] md:h-[380px] lg:h-[400px]">
    
    {{-- CARRUSEL DE IMÁGENES DE FONDO --}}
    <div id="hero-slider" class="absolute inset-0 z-0">
        {{-- Imagen 1 (Cultivos / Frescura) --}}
        <div class="hero-slide absolute inset-0 bg-cover bg-center transition-all duration-1000 ease-in-out opacity-100 scale-105" 
             style="background-image: url('{{ asset('img/hero/tractor.jpg') }}');">
        </div>
        {{-- Imagen 2 (Tecnología / Invernadero) --}}
        <div class="hero-slide absolute inset-0 bg-cover bg-center transition-all duration-1000 ease-in-out opacity-0 scale-100" 
             style="background-image: url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');">
        </div>
        {{-- Imagen 3 (Maquinaria / Campo abierto) --}}
        <div class="hero-slide absolute inset-0 bg-cover bg-center transition-all duration-1000 ease-in-out opacity-0 scale-100" 
             style="background-image: url('https://images.unsplash.com/photo-1625246333195-78d9c38ad449?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');">
        </div>
        
        {{-- Capa degradada institucional --}}
        <div class="absolute inset-0 bg-gradient-to-r from-[#0b1d38]/95 via-[#1e549e]/85 to-transparent z-10"></div>
    </div>
    
    {{-- Contenido del Hero --}}
    <div class="layout-container relative z-10 w-full h-full flex flex-col justify-center px-4 sm:px-0">
        <div class="max-w-xl md:max-w-2xl w-full animate-fade-in-up">
            
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-xs text-[#aad6f3] text-xs font-semibold mb-3 border border-white/10">
                <span class="size-2 rounded-full bg-[#3492cf] animate-pulse"></span>
                Insumos Agrícolas y Salud Animal
            </div>

            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-white leading-[1.1] tracking-tight mb-3">
                Calidad garantizada para el <span class="text-[#aad6f3]">campo venezolano</span>
            </h1>
            
            <p class="text-sm sm:text-base text-slate-200 mb-6 font-normal leading-relaxed max-w-lg">
                {{ $configTienda->nombre_empresa ?? \App\Models\ConfiguracionTienda::nombre() }}: {{ $configTienda->slogan ?? 'Semillas certificadas, fertilizantes, medicina veterinaria y herramientas con despacho directo a todo el país.' }}
            </p>
            
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 w-full sm:w-auto">
                <a href="{{ route('catalogo') }}" class="btn-secondary h-12 px-7 text-base shadow-lg shadow-[#3492cf]/25 hover:shadow-[#3492cf]/40 group">
                    <span>Ver Catálogo</span>
                    <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">
                        arrow_forward
                    </span>
                </a>
                
                <a href="{{ \App\Models\ConfiguracionTienda::whatsappUrl('Hola ' . ($configTienda->nombre_empresa ?? \App\Models\ConfiguracionTienda::nombre()) . ', deseo asesoría para mi compra en la tienda virtual') }}" target="_blank" class="inline-flex items-center justify-center gap-2 h-12 px-6 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-sm backdrop-blur-xs border border-white/20 transition-all">
                    <span class="material-symbols-outlined text-[20px] text-emerald-400">chat</span>
                    <span>Asesoría por WhatsApp</span>
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
                slides[currentSlide].classList.remove('opacity-100', 'scale-105');
                slides[currentSlide].classList.add('opacity-0', 'scale-100');
                
                currentSlide = (currentSlide + 1) % slides.length;
                
                slides[currentSlide].classList.remove('opacity-0', 'scale-100');
                slides[currentSlide].classList.add('opacity-100', 'scale-105');
            }, 6000);
        }
    });
</script>