<section class="py-12 sm:py-16 bg-white relative">
    
    <div class="layout-container w-full relative z-10 px-4 sm:px-0">
        
        <div class="mb-10 md:mb-12 max-w-3xl">
            <span class="inline-block py-1 px-3 rounded-full bg-primary/10 text-primary font-bold uppercase tracking-wider text-xs mb-3">
                Selección del Mes
            </span>
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-black text-agro-dark leading-tight">
                Productos Más <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-agro-dark">Vendidos</span>
            </h2>
            <p class="text-gray-500 text-sm md:text-base mt-4 leading-relaxed">
                Nuestra selección premium de insumos agropecuarios, avalada por la compra recurrente de nuestros productores certificados.
            </p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            
            @foreach($productosDestacados as $producto)
            <article class="group relative flex flex-col bg-white rounded-2xl border border-gray-100 hover:border-primary/30 hover:shadow-xl hover:shadow-primary/5 transition-all duration-300 overflow-hidden h-full">
                
                {{-- ÁREA DE IMAGEN: Corregida para apuntar al Storage de Laravel --}}
                <div class="relative w-full aspect-[4/3] bg-gray-50 overflow-hidden">
                    
                    @if($producto->imagen_url)
                        {{-- Usamos asset('storage/...') para renderizar la imagen correctamente --}}
                        <div class="absolute inset-0 bg-cover bg-center group-hover:scale-105 transition-transform duration-700 ease-out mix-blend-multiply" 
                             style="background-image: url('{{ asset('storage/' . $producto->imagen_url) }}');"></div>
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-gray-300 bg-gray-50">
                            <span class="material-symbols-outlined text-5xl mb-2">image</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest">Sin Imagen</span>
                        </div>
                    @endif
                    
                    {{-- Badges Superior Izquierda --}}
                    <div class="absolute top-3 left-3 flex flex-col gap-1.5 items-start z-10 pointer-events-none">
                        @if($producto->es_controlado)
                            <span class="inline-flex items-center gap-1 bg-red-500 text-white text-[10px] font-black px-2 py-1 rounded-md uppercase shadow-sm tracking-wide">
                                <span class="material-symbols-outlined text-[12px]">lock</span> Controlado
                            </span>
                        @endif

                        @if($producto->precio_oferta_usd)
                            <span class="inline-flex items-center gap-1 bg-primary text-agro-dark text-[10px] font-black px-2 py-1 rounded-md uppercase shadow-sm tracking-wide">
                                Oferta
                            </span>
                        @endif
                    </div>

                    {{-- Botón Favorito --}}
                    <div class="absolute top-3 right-3 z-20">
                        <button class="flex items-center justify-center w-8 h-8 rounded-full bg-white text-gray-400 hover:text-red-500 hover:bg-red-50 hover:shadow-md transition-all duration-300" title="Añadir a favoritos">
                            <span class="material-symbols-outlined text-[18px] hover:fill-current">favorite</span>
                        </button>
                    </div>
                    
                    {{-- Unidad de medida --}}
                    <div class="absolute bottom-3 left-3 pointer-events-none">
                        <span class="inline-block bg-white/95 backdrop-blur-sm text-agro-dark text-[10px] font-bold px-2 py-1 rounded-md shadow-sm uppercase tracking-wider">
                            {{ $producto->unidad_medida }}
                        </span>
                    </div>
                </div>
                
                {{-- ÁREA DE INFORMACIÓN --}}
                <div class="p-5 flex flex-col flex-1 relative bg-white">
                    
                    {{-- Fila superior: Marca y Estado de Stock --}}
                    <div class="flex justify-between items-center mb-2">
                        <p class="text-[11px] text-agro-accent font-bold uppercase tracking-wider flex items-center gap-1 truncate pr-2">
                            <span class="material-symbols-outlined text-[14px]">verified</span>
                            {{ $producto->marca->nombre ?? 'GENÉRICO' }}
                        </p>
                        
                        {{-- Indicador de Stock --}}
                        <div>
                            @if($producto->stock_total > 0 && $producto->stock_total <= $producto->stock_minimo_alerta)
                                <span class="text-[10px] font-bold text-amber-500 bg-amber-50 px-2 py-0.5 rounded text-right whitespace-nowrap">
                                    Poco Stock
                                </span>
                            @elseif($producto->stock_total > 0)
                                <span class="text-[10px] font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded text-right whitespace-nowrap flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Stock
                                </span>
                            @else
                                <span class="text-[10px] font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded text-right whitespace-nowrap flex items-center gap-1">
                                    Agotado
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Título --}}
                    <h3 class="text-agro-dark font-bold text-base leading-snug mb-2 line-clamp-2 min-h-[2.5rem] group-hover:text-primary transition-colors">
                        <a href="#" class="focus:outline-none before:absolute before:inset-0">
                            {{ $producto->nombre }}
                        </a>
                    </h3>
                    
                    {{-- Descripción --}}
                    <p class="text-xs text-gray-500 mb-4 line-clamp-2 flex-grow leading-relaxed relative z-20">
                        {{ $producto->descripcion }}
                    </p>
                    
                    {{-- Fila Inferior: Precios y Call to Action (CTA) --}}
                    <div class="mt-auto pt-4 border-t border-gray-100 flex items-end justify-between relative z-20">
                        
                        <div class="flex flex-col">
                            <div class="flex items-baseline gap-1.5">
                                @if($producto->precio_oferta_usd)
                                    <span class="text-xl font-black text-agro-dark">${{ number_format($producto->precio_oferta_usd, 2) }}</span>
                                    <span class="text-xs text-gray-400 line-through font-semibold">${{ number_format($producto->precio_venta_usd, 2) }}</span>
                                @else
                                    <span class="text-xl font-black text-agro-dark">${{ number_format($producto->precio_venta_usd, 2) }}</span>
                                    <span class="text-[10px] text-gray-400 font-bold self-start mt-1">USD</span>
                                @endif
                            </div>
                            <span class="text-[11px] text-gray-400 font-medium mt-0.5">
                                {{-- Aquí multiplicas por 60.50 como demo. A futuro, este número vendrá de la variable de Tasa de Cambio del BCV --}}
                                ≈ Bs. {{ number_format(($producto->precio_oferta_usd ?? $producto->precio_venta_usd) * 60.50, 2, ',', '.') }}
                            </span>
                        </div>
                        
                        {{-- BOTÓN AÑADIR AL CARRITO EXPLÍCITO Y SIEMPRE VISIBLE --}}
<button type="button" 
        data-producto-id="{{ $producto->id }}"
        class="btn-add-cart-destacado flex items-center justify-center w-11 h-11 rounded-xl bg-primary/10 text-agro-dark hover:bg-primary hover:text-white hover:shadow-lg hover:shadow-primary/20 transition-all duration-300 group/btn" 
        title="Añadir al carrito">
    <span class="material-symbols-outlined text-[22px] group-active/btn:scale-95 transition-transform pointer-events-none">add_shopping_cart</span>
</button>
                    </div>

                </div>
            </article>
            @endforeach
        </div>
        
        <div class="mt-12 md:mt-16 text-center">
            <a href="{{ route('catalogo') }}" class="inline-flex items-center gap-3 px-8 py-4 rounded-xl bg-agro-dark hover:bg-primary text-white font-bold text-sm md:text-base transition-all duration-300 hover:shadow-xl hover:shadow-primary/20 transform hover:-translate-y-1 group">
                <span>Explorar Catálogo Completo</span>
                <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </a>
        </div>
    </div>
</section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Capturar todos los botones de esta sección específica
        const botonesCart = document.querySelectorAll('.btn-add-cart-destacado');

        botonesCart.forEach(boton => {
            boton.addEventListener('click', function(e) {
                e.preventDefault();
                const productoId = this.getAttribute('data-producto-id');
                agregarAlCarrito(productoId, this);
            });
        });

        // 2. Lógica principal de AJAX
        function agregarAlCarrito(productoId, botonElement) {
            // Guardar el ícono original y poner un spinner de carga
            const iconoOriginal = botonElement.innerHTML;
            botonElement.innerHTML = '<span class="material-symbols-outlined text-[22px] animate-spin">sync</span>';
            botonElement.disabled = true;

            // Obtener el token CSRF del meta tag de Laravel
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch("{{ route('carrito.add') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "Accept": "application/json"
                },
                body: JSON.stringify({ producto_id: productoId, cantidad: 1 })
            })
            .then(response => {
                // Si el usuario no está logueado, Laravel devuelve un 401
                if (response.status === 401) {
                    window.location.href = "{{ route('login') }}";
                    return Promise.reject('Requiere autenticación');
                }
                return response.json();
            })
            .then(data => {
                if (data && data.status === 'success') {
                    mostrarToastIndependiente('Producto agregado al carrito', 'success');

                    // Actualizar el contador del menú superior (si tienes los IDs configurados así)
                    const badge = document.getElementById('cart-count-badge');
                    const badgeContainer = document.getElementById('cart-badge-container');
                    
                    if(badge && badgeContainer) {
                        badge.innerText = data.cart_count;
                        badgeContainer.classList.remove('hidden');
                        badge.classList.add('animate-bounce');
                        setTimeout(() => badge.classList.remove('animate-bounce'), 1000);
                    }
                } else {
                    mostrarToastIndependiente(data.message || 'Error al agregar producto', 'error');
                }
            })
            .catch(error => {
                if (error !== 'Requiere autenticación') {
                    console.error('Error de red:', error);
                    mostrarToastIndependiente('Error de conexión con el servidor', 'error');
                }
            })
            .finally(() => {
                // Devolver el botón a su estado normal
                botonElement.innerHTML = iconoOriginal;
                botonElement.disabled = false;
            });
        }

        // 3. Mini sistema de notificaciones (Toast) inyectado dinámicamente
        function mostrarToastIndependiente(mensaje, tipo) {
            // Definir colores según el tipo
            const bgClase = tipo === 'success' ? 'bg-green-600' : 'bg-red-600';
            const icono = tipo === 'success' ? 'check_circle' : 'error';

            // Crear el elemento HTML del toast
            const toast = document.createElement('div');
            toast.className = `fixed bottom-6 right-6 z-[9999] flex items-center gap-3 px-5 py-3 rounded-xl shadow-2xl text-white transform transition-all duration-300 translate-y-20 opacity-0 font-sans text-sm font-medium ${bgClase}`;
            toast.innerHTML = `<span class="material-symbols-outlined text-xl">${icono}</span> ${mensaje}`;

            // Insertarlo en el documento
            document.body.appendChild(toast);

            // Animar entrada (usando setTimeout para que el DOM registre el elemento primero)
            requestAnimationFrame(() => {
                setTimeout(() => {
                    toast.classList.remove('translate-y-20', 'opacity-0');
                }, 10);
            });

            // Animar salida y destruir elemento después de 3.5 segundos
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 3500);
        }
    });
</script>
@endpush