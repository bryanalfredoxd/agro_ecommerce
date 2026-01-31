<footer class="bg-background-dark text-white border-t border-agro-dark">
    
    <div class="bg-agro-dark/40 border-b border-white/5 backdrop-blur-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-primary/5 rounded-full blur-3xl"></div>
        
        <div class="layout-container py-8 md:py-10 relative z-10">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-6 lg:gap-12">
                <div class="text-center lg:text-left max-w-xl">
                    <h3 class="text-xl md:text-2xl font-bold text-white mb-2 flex items-center justify-center lg:justify-start gap-2">
                        <span class="material-symbols-outlined text-primary">mail</span>
                        Suscríbete a nuestro boletín
                    </h3>
                    <p class="text-gray-400 text-sm md:text-base leading-relaxed">
                        Recibe ofertas exclusivas, novedades de productos y consejos técnicos para optimizar tu producción.
                    </p>
                </div>
                
                <form class="w-full lg:w-auto flex-shrink-0">
                    <div class="flex flex-col sm:flex-row gap-3 w-full max-w-md mx-auto lg:mx-0">
                        <div class="relative flex-grow">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 material-symbols-outlined text-[20px]">email</span>
                            <input 
                                type="email" 
                                placeholder="Tu correo electrónico"
                                class="w-full h-12 pl-11 pr-4 rounded-xl bg-white/5 border border-white/10 text-white placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all text-sm"
                            >
                        </div>
                        <button 
                            type="submit"
                            class="h-12 px-8 rounded-xl bg-primary hover:bg-primary/90 text-agro-dark font-bold transition-all duration-300 hover:shadow-lg hover:shadow-primary/20 hover:-translate-y-0.5 whitespace-nowrap flex items-center justify-center gap-2"
                        >
                            <span>Suscribirse</span>
                            <span class="material-symbols-outlined text-[20px]">send</span>
                        </button>
                    </div>
                    <p class="text-[10px] text-gray-500 mt-2 text-center lg:text-left">
                        Al suscribirte aceptas nuestros términos y condiciones.
                    </p>
                </form>
            </div>
        </div>
    </div>

    <div class="layout-container pt-12 pb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-8">
            
            <div class="lg:col-span-4 flex flex-col gap-6">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center size-10 sm:size-11 bg-primary rounded-xl text-agro-dark shadow-lg shadow-primary/10">
                        <span class="material-symbols-outlined text-[26px]">agriculture</span>
                    </div>
                    <div class="flex flex-col leading-none">
                        <div class="flex items-baseline gap-1">
                            <span class="text-white text-xl font-bold tracking-tight">Corpo</span>
                            <span class="text-agro-gold text-xl font-bold tracking-tight">Agrícola</span>
                        </div>
                        <span class="text-[10px] text-gray-400 font-medium tracking-wider uppercase mt-0.5">Venezuela</span>
                    </div>
                </div>
                
                <p class="text-gray-400 text-sm leading-relaxed text-pretty">
                    Su aliado estratégico en el campo venezolano. Ofrecemos insumos, maquinaria y asesoría técnica especializada para garantizar el éxito de su cosecha.
                </p>
                
                <div class="flex gap-3">
                    @foreach(['facebook', 'instagram', 'youtube'] as $social)
                    <a href="#" class="size-10 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center text-gray-400 hover:text-primary transition-all duration-300 group">
                        <img src="https://cdn.simpleicons.org/{{ $social }}/ffffff" class="w-5 h-5 opacity-60 group-hover:opacity-100 transition-opacity" alt="{{ $social }}">
                    </a>
                    @endforeach
                </div>
            </div>

            <div class="lg:col-span-2 sm:col-span-1">
                <h4 class="font-bold text-white text-base mb-5 flex items-center gap-2">
                    <span class="w-1 h-4 bg-primary rounded-full"></span>
                    Navegación
                </h4>
                <ul class="space-y-3">
                    @foreach(['Inicio', 'Nosotros', 'Catálogo', 'Servicios', 'Blog', 'Contacto'] as $link)
                    <li>
                        <a href="#" class="text-sm text-gray-400 hover:text-primary hover:pl-1 transition-all duration-200 flex items-center gap-2">
                            <span class="w-1 h-1 bg-gray-600 rounded-full"></span>
                            {{ $link }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-3 sm:col-span-1">
                <h4 class="font-bold text-white text-base mb-5 flex items-center gap-2">
                    <span class="w-1 h-4 bg-primary rounded-full"></span>
                    Categorías
                </h4>
                <div class="grid grid-cols-1 gap-2">
                    @php
                        $cats = [
                            ['name' => 'Veterinaria', 'count' => '120+'],
                            ['name' => 'Semillas', 'count' => '45+'],
                            ['name' => 'Fertilizantes', 'count' => '30+'],
                            ['name' => 'Maquinaria', 'count' => '15+'],
                        ];
                    @endphp
                    @foreach($cats as $cat)
                    <a href="#" class="group flex items-center justify-between p-2 rounded-lg hover:bg-white/5 transition-colors border border-transparent hover:border-white/5">
                        <span class="text-sm text-gray-400 group-hover:text-white transition-colors">{{ $cat['name'] }}</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-white/5 text-gray-500 group-hover:bg-primary/20 group-hover:text-primary transition-colors">{{ $cat['count'] }}</span>
                    </a>
                    @endforeach
                </div>
            </div>

            <div class="lg:col-span-3 sm:col-span-2">
                <h4 class="font-bold text-white text-base mb-5 flex items-center gap-2">
                    <span class="w-1 h-4 bg-primary rounded-full"></span>
                    Atención al Cliente
                </h4>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-3 group">
                        <div class="size-8 rounded-lg bg-white/5 flex items-center justify-center text-primary shrink-0 group-hover:bg-primary group-hover:text-agro-dark transition-colors">
                            <span class="material-symbols-outlined text-[18px]">phone_in_talk</span>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500 mb-0.5">Llámanos</span>
                            <a href="tel:+584241234567" class="text-sm font-medium text-gray-300 hover:text-white transition-colors">+58 424-123-4567</a>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 group">
                        <div class="size-8 rounded-lg bg-white/5 flex items-center justify-center text-primary shrink-0 group-hover:bg-primary group-hover:text-agro-dark transition-colors">
                            <span class="material-symbols-outlined text-[18px]">mail</span>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500 mb-0.5">Escríbenos</span>
                            <a href="mailto:ventas@corpoagricola.com" class="text-sm font-medium text-gray-300 hover:text-white transition-colors">ventas@corpoagricola.com</a>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 group">
                        <div class="size-8 rounded-lg bg-white/5 flex items-center justify-center text-primary shrink-0 group-hover:bg-primary group-hover:text-agro-dark transition-colors">
                            <span class="material-symbols-outlined text-[18px]">location_on</span>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500 mb-0.5">Visítanos</span>
                            <p class="text-sm font-medium text-gray-300">Av. Principal Zona Industrial, Galpón 5, Venezuela.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-12 pt-8 border-t border-white/5 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex flex-col gap-2 text-center md:text-left">
                <span class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Métodos de Pago Aceptados</span>
                <div class="flex flex-wrap justify-center md:justify-start gap-2">
                    @foreach(['Zelle', 'Pago Móvil', 'Banesco', 'Binance', 'Efectivo'] as $method)
                    <div class="px-3 py-1.5 rounded bg-white/5 border border-white/5 text-[11px] font-medium text-gray-300 hover:border-primary/30 transition-colors cursor-default">
                        {{ $method }}
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-6 opacity-60 grayscale hover:grayscale-0 transition-all duration-500">
                <div class="flex flex-col items-center">
                    <span class="material-symbols-outlined text-3xl mb-1">verified_user</span>
                    <span class="text-[10px] font-bold">SENASA</span>
                </div>
                <div class="h-8 w-px bg-white/10"></div>
                <div class="flex flex-col items-center">
                    <span class="material-symbols-outlined text-3xl mb-1">workspace_premium</span>
                    <span class="text-[10px] font-bold">FEDEAGRO</span>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-8 border-t border-white/5 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-gray-500">
            <p>© {{ date('Y') }} Corpo Agrícola C.A. - RIF: J-12345678-9. Todos los derechos reservados.</p>
            <div class="flex gap-4">
                <a href="#" class="hover:text-primary transition-colors">Privacidad</a>
                <a href="#" class="hover:text-primary transition-colors">Términos</a>
                <a href="#" class="hover:text-primary transition-colors">Mapa del Sitio</a>
            </div>
        </div>
    </div>
</footer>