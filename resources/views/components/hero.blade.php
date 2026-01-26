<!-- Hero Section -->
<section class="relative overflow-hidden">
    <div class="w-full bg-agro-dark">
        <div class="relative h-[400px] md:h-[500px] lg:h-[600px] w-full">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" 
                 style="background-image: url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80'); 
                        background-position: center 30%;"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-agro-dark/95 via-agro-dark/80 to-agro-dark/60 md:from-agro-dark/90 md:via-agro-dark/60 md:to-transparent"></div>
            
            <!-- Content -->
            <div class="layout-container relative h-full flex items-center">
                <div class="max-w-xl md:max-w-2xl w-full animate-fade-in-up">
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/20 border border-primary/30 text-primary text-xs font-bold uppercase tracking-wider mb-6 backdrop-blur-md">
                        <span class="size-2 rounded-full bg-primary animate-pulse"></span>
                        Líderes en el Agro Venezolano
                    </div>
                    
                    <!-- Title -->
                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black text-white leading-tight mb-4 md:mb-6">
                        Soluciones integrales para el 
                        <span class="text-primary block md:inline">campo venezolano</span>
                    </h1>
                    
                    <!-- Description -->
                    <p class="text-base sm:text-lg text-gray-200 mb-6 md:mb-8 font-light leading-relaxed max-w-lg">
                        Desde semillas certificadas hasta maquinaria especializada. 
                        Todo lo que necesita su unidad de producción, con entrega 
                        directa en todo el territorio nacional.
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 md:gap-4">
                        <!-- Primary Button -->
                        <button class="group flex items-center justify-center gap-2 h-12 px-6 md:px-8 rounded-lg bg-primary hover:bg-primary/90 text-agro-dark font-bold text-sm md:text-base transition-all duration-300 shadow-lg shadow-primary/25 hover:shadow-xl hover:shadow-primary/35">
                            <span>Explorar Catálogo</span>
                            <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">
                                arrow_forward
                            </span>
                        </button>
                        
                        <!-- Secondary Button -->
                        <button class="group flex items-center justify-center gap-2 h-12 px-6 md:px-8 rounded-lg bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/20 text-white font-semibold text-sm md:text-base transition-all duration-300 hover:border-white/40">
                            <span class="material-symbols-outlined text-[20px]">support_agent</span>
                            <span>Asesoría Especializada</span>
                        </button>
                    </div>
                    
                    <!-- Trust Indicators (Mobile only) -->
                    <div class="mt-8 md:hidden grid grid-cols-2 gap-4">
                        <div class="flex items-center gap-2">
                            <div class="size-8 rounded-full bg-primary/20 flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary text-[16px]">local_shipping</span>
                            </div>
                            <span class="text-xs text-gray-300">Envios Nacionales</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="size-8 rounded-full bg-primary/20 flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary text-[16px]">verified</span>
                            </div>
                            <span class="text-xs text-gray-300">Productos Certificados</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Scroll Indicator (Desktop only) -->
            <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 hidden lg:block">
                <div class="animate-bounce">
                    <span class="material-symbols-outlined text-white/60 text-[32px]">
                        keyboard_arrow_down
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>