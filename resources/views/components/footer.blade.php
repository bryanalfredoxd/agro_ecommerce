<footer class="bg-[#0b1d38] text-white border-t border-[#1e549e]/40 relative overflow-hidden">
    <br>
    {{-- CUERPO PRINCIPAL DEL FOOTER --}}
    <div class="layout-container py-12 lg:py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-8">
            
            {{-- COLUMNA 1: IDENTIDAD CORPORATIVA (4 cols) --}}
            <div class="lg:col-span-4 flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('img/logo.png') }}" 
                         alt="Agrosuministros Rodríguez" 
                         class="h-16 w-16 object-contain drop-shadow-md">
                    <div>
                        <h3 class="text-base font-black text-white leading-tight tracking-tight">Agrosuministros Rodríguez</h3>
                        <p class="text-xs text-[#aad6f3] font-semibold">C.A. • RIF: J-50493821-4</p>
                        <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-0.5">Insumos & Soluciones para el Campo</p>
                    </div>
                </div>
                
                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed pr-2">
                    Tu aliado estratégico en el desarrollo agrícola y pecuario. Ofrecemos fertilizantes, semillas certificadas, implementos y farmacología veterinaria con entrega confiable a nivel regional y nacional.
                </p>
                
                {{-- Redes Sociales Directas con SVGs --}}
                <div class="flex items-center gap-3 pt-2">
                    <a href="https://wa.me/584129614615?text=Hola,%20Agrosuministros%20Rodríguez%20deseo%20hacer%20una%20consulta" target="_blank" rel="noopener" class="size-10 rounded-xl bg-white/5 hover:bg-[#25D366] text-white flex items-center justify-center transition-all duration-300 hover:scale-110 shadow-xs group" title="WhatsApp Directo">
                        <svg class="size-5 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.53 1.771.82 2.791.82 3.181 0 5.767-2.586 5.768-5.766 0-3.18-2.586-5.766-5.768-5.766zm9.969 5.766c0 5.485-4.464 9.948-9.969 9.948-1.748 0-3.385-.453-4.815-1.246l-5.216 1.36 1.391-5.074c-.908-1.494-1.429-3.235-1.429-5.088 0-5.485 4.464-9.948 9.969-9.948 5.505 0 9.969 4.463 9.969 9.948z"/></svg>
                    </a>
                    
                    <a href="https://instagram.com" target="_blank" rel="noopener" class="size-10 rounded-xl bg-white/5 hover:bg-gradient-to-tr hover:from-amber-500 hover:via-rose-500 hover:to-purple-600 text-white flex items-center justify-center transition-all duration-300 hover:scale-110 shadow-xs group" title="Instagram">
                        <svg class="size-5 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>

                    <a href="https://facebook.com" target="_blank" rel="noopener" class="size-10 rounded-xl bg-white/5 hover:bg-[#1877F2] text-white flex items-center justify-center transition-all duration-300 hover:scale-110 shadow-xs group" title="Facebook">
                        <svg class="size-5 fill-current" viewBox="0 0 24 24"><path d="M9 8H6v4h3v12h5V12h3.642L18 8h-4V6.333C14 5.374 14.5 5 15.657 5H18V0h-3.808C10.596 0 9 1.583 9 4.615V8z"/></svg>
                    </a>

                    <a href="mailto:fitnessbigworld@gmail.com" class="size-10 rounded-xl bg-white/5 hover:bg-[#3492cf] text-white flex items-center justify-center transition-all duration-300 hover:scale-110 shadow-xs group" title="Correo Electrónico">
                        <span class="material-symbols-outlined text-[20px]">mail</span>
                    </a>
                </div>
            </div>

            {{-- COLUMNA 2: NAVEGACIÓN (2 cols) --}}
            <div class="lg:col-span-2">
                <h4 class="font-bold text-white text-xs uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-3.5 bg-[#3492cf] rounded-full"></span>
                    Navegación
                </h4>
                <ul class="space-y-2.5 text-xs sm:text-sm">
                    <li>
                        <a href="{{ route('home') }}" class="text-slate-300 hover:text-[#aad6f3] transition-all inline-flex items-center gap-1.5 hover:translate-x-1 duration-200">
                            <span class="material-symbols-outlined text-[15px] text-[#3492cf]">chevron_right</span>
                            Inicio
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('catalogo') }}" class="text-slate-300 hover:text-[#aad6f3] transition-all inline-flex items-center gap-1.5 hover:translate-x-1 duration-200">
                            <span class="material-symbols-outlined text-[15px] text-[#3492cf]">chevron_right</span>
                            Catálogo de Productos
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('carrito.index') }}" class="text-slate-300 hover:text-[#aad6f3] transition-all inline-flex items-center gap-1.5 hover:translate-x-1 duration-200">
                            <span class="material-symbols-outlined text-[15px] text-[#3492cf]">chevron_right</span>
                            Carrito de Compras
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('perfil') }}" class="text-slate-300 hover:text-[#aad6f3] transition-all inline-flex items-center gap-1.5 hover:translate-x-1 duration-200">
                            <span class="material-symbols-outlined text-[15px] text-[#3492cf]">chevron_right</span>
                            Mi Perfil & Pedidos
                        </a>
                    </li>
                </ul>
            </div>

            {{-- COLUMNA 3: LÍNEAS DE PRODUCTOS (3 cols) --}}
            <div class="lg:col-span-3">
                <h4 class="font-bold text-white text-xs uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-3.5 bg-[#3492cf] rounded-full"></span>
                    Líneas de Producción
                </h4>
                <ul class="space-y-2.5 text-xs sm:text-sm">
                    <li>
                        <a href="{{ route('catalogo', ['buscar' => 'veterinaria']) }}" class="text-slate-300 hover:text-[#aad6f3] transition-all inline-flex items-center gap-1.5 hover:translate-x-1 duration-200">
                            <span class="material-symbols-outlined text-[15px] text-[#3492cf]">pets</span>
                            Salud Animal & Veterinaria
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('catalogo', ['buscar' => 'fertilizantes']) }}" class="text-slate-300 hover:text-[#aad6f3] transition-all inline-flex items-center gap-1.5 hover:translate-x-1 duration-200">
                            <span class="material-symbols-outlined text-[15px] text-[#3492cf]">eco</span>
                            Fertilizantes y Nutrición
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('catalogo', ['buscar' => 'semillas']) }}" class="text-slate-300 hover:text-[#aad6f3] transition-all inline-flex items-center gap-1.5 hover:translate-x-1 duration-200">
                            <span class="material-symbols-outlined text-[15px] text-[#3492cf]">spa</span>
                            Semillas Certificadas
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('catalogo', ['buscar' => 'herramientas']) }}" class="text-slate-300 hover:text-[#aad6f3] transition-all inline-flex items-center gap-1.5 hover:translate-x-1 duration-200">
                            <span class="material-symbols-outlined text-[15px] text-[#3492cf]">construction</span>
                            Herramientas y Equipos
                        </a>
                    </li>
                </ul>
            </div>

            {{-- COLUMNA 4: ATENCIÓN Y CONTACTO (3 cols) --}}
            <div class="lg:col-span-3">
                <h4 class="font-bold text-white text-xs uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-3.5 bg-[#3492cf] rounded-full"></span>
                    Atención al Cliente
                </h4>
                
                <div class="space-y-3.5">
                    <div class="flex items-start gap-3">
                        <div class="size-8 rounded-xl bg-white/5 flex items-center justify-center text-[#aad6f3] shrink-0 border border-white/5">
                            <span class="material-symbols-outlined text-[17px]">location_on</span>
                        </div>
                        <div>
                            <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Ubicación</span>
                            <p class="text-xs text-slate-200 leading-snug">Barinitas, Estado Barinas, Venezuela</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="size-8 rounded-xl bg-white/5 flex items-center justify-center text-[#aad6f3] shrink-0 border border-white/5">
                            <span class="material-symbols-outlined text-[17px]">call</span>
                        </div>
                        <div>
                            <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">WhatsApp & Ventas</span>
                            <a href="https://wa.me/584129614615" target="_blank" class="text-xs font-bold text-white hover:text-[#aad6f3] transition-colors leading-snug block">+58 412-9614615</a>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="size-8 rounded-xl bg-white/5 flex items-center justify-center text-[#aad6f3] shrink-0 border border-white/5">
                            <span class="material-symbols-outlined text-[17px]">mail</span>
                        </div>
                        <div>
                            <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Correo Electrónico</span>
                            <a href="mailto:fitnessbigworld@gmail.com" class="text-xs font-semibold text-white hover:text-[#aad6f3] transition-colors leading-snug block truncate">fitnessbigworld@gmail.com</a>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="size-8 rounded-xl bg-white/5 flex items-center justify-center text-[#aad6f3] shrink-0 border border-white/5">
                            <span class="material-symbols-outlined text-[17px]">schedule</span>
                        </div>
                        <div>
                            <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Horario Laboral</span>
                            <p class="text-xs text-slate-200 leading-snug">Lun a Sáb: 8:00 AM - 5:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        
        {{-- BARRA INFERIOR / MÉTODOS DE PAGO Y DERECHOS --}}
        <div class="mt-12 pt-6 border-t border-white/10 flex flex-col items-center justify-center gap-4 text-xs text-slate-400">

            {{-- Métodos de Pago Aceptados con Insignias Visuales Registrados en la Base de Datos --}}
            @php
                try {
                    $metodosPagoFooter = \App\Models\CuentaBancaria::where('activo', 1)->get();
                } catch (\Throwable $e) {
                    $metodosPagoFooter = collect();
                }
            @endphp
            <div class="flex flex-wrap items-center justify-center gap-2">
                @forelse($metodosPagoFooter as $metodo)
                    @php
                        $iconData = match($metodo->tipo_metodo) {
                            'pago_movil' => ['tipo' => 'icon', 'icon' => 'phone_iphone', 'color' => 'text-[#3492cf]'],
                            'transferencia' => ['tipo' => 'icon', 'icon' => 'account_balance', 'color' => 'text-emerald-400'],
                            'zelle' => ['tipo' => 'img', 'src' => asset('img/pagos/zelle.png'), 'color' => 'text-purple-400'],
                            'binance' => ['tipo' => 'img', 'src' => asset('img/pagos/binance.png'), 'color' => 'text-amber-400'],
                            'efectivo_usd', 'efectivo_bs' => ['tipo' => 'icon', 'icon' => 'attach_money', 'color' => 'text-emerald-400'],
                            'punto_venta' => ['tipo' => 'icon', 'icon' => 'point_of_sale', 'color' => 'text-sky-400'],
                            'biopago' => ['tipo' => 'icon', 'icon' => 'fingerprint', 'color' => 'text-cyan-400'],
                            default => ['tipo' => 'icon', 'icon' => 'payments', 'color' => 'text-slate-300']
                        };
                    @endphp
                    <span class="px-2.5 py-1 rounded-lg bg-white/5 hover:bg-white/10 text-slate-300 font-semibold text-[10px] border border-white/10 flex items-center gap-1.5 transition-colors shadow-2xs" title="{{ $metodo->banco_entidad ?: $metodo->nombre }}">
                        @if($iconData['tipo'] === 'img')
                            <img src="{{ $iconData['src'] }}" alt="{{ $metodo->nombre }}" class="size-3.5 object-contain">
                        @else
                            <span class="material-symbols-outlined text-[13px] {{ $iconData['color'] }}">{{ $iconData['icon'] }}</span>
                        @endif
                        <span>{{ $metodo->nombre }}</span>
                    </span>
                @empty
                    <span class="px-2.5 py-1 rounded-lg bg-white/5 text-slate-300 font-semibold text-[10px] border border-white/5 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[13px] text-[#3492cf]">phone_iphone</span> Pago Móvil
                    </span>
                    <span class="px-2.5 py-1 rounded-lg bg-white/5 text-slate-300 font-semibold text-[10px] border border-white/5 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[13px] text-emerald-400">account_balance</span> Transferencia
                    </span>
                    <span class="px-2.5 py-1 rounded-lg bg-white/5 text-slate-300 font-semibold text-[10px] border border-white/5 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[13px] text-purple-400">payments</span> Zelle
                    </span>
                    <span class="px-2.5 py-1 rounded-lg bg-white/5 text-slate-300 font-semibold text-[10px] border border-white/5 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[13px] text-amber-400">attach_money</span> Efectivo Divisas
                    </span>
                @endforelse
            </div>

            {{-- Derechos Reservados --}}
            <div class="text-center">
                <p class="text-xs text-slate-400">© {{ date('Y') }} <strong class="text-white">Agrosuministros Rodríguez C.A.</strong> Todos los derechos reservados.</p>
            </div>
        </div>
        <br>
    </div>
</footer>