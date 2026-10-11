@php
    if (!isset($favoritosIds)) {
        $favoritosIds = auth()->check()
            ? \Illuminate\Support\Facades\DB::table('favoritos')->where('usuario_id', auth()->id())->pluck('producto_id')->toArray()
            : [];
    }
@endphp

<section class="py-12 sm:py-16 bg-white relative">
    
    <div class="layout-container w-full relative z-10 px-4 sm:px-0">
        
        <div class="mb-10 md:mb-12 max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#e5f2fb] text-[#1e549e] text-xs font-bold mb-3 border border-[#aad6f3]">
                <span class="material-symbols-outlined text-[16px] text-[#3492cf]">verified</span>
                Insumos Seleccionados
            </div>
            <h2 class="text-3xl sm:text-4xl font-black text-[#1e549e] leading-tight">
                Productos Más <span class="text-[#3492cf]">Solicitados</span>
            </h2>
            <p class="text-slate-600 text-sm md:text-base mt-2 leading-relaxed">
                Nuestra selección recomendada para garantizar el rendimiento y sanidad de tu unidad productiva.
            </p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    
            @foreach($productosDestacados as $producto)
            <article class="group relative flex flex-col bg-white rounded-2xl border border-gray-100 hover:border-primary/30 hover:shadow-xl hover:shadow-primary/5 transition-all duration-300 overflow-hidden h-full">
                
                {{-- ÁREA DE IMAGEN (Con imagen completa sin recortes) --}}
                <div class="relative w-full aspect-[4/3] bg-gradient-to-b from-slate-50/80 to-white overflow-hidden flex items-center justify-center p-3 sm:p-4 border-b border-slate-100">
                    
                    @if($producto->imagen_url)
                        <img src="{{ asset($producto->imagen_url) }}" 
                             alt="{{ $producto->nombre }}" 
                             loading="lazy"
                             class="w-full h-full object-contain max-h-full max-w-full drop-shadow-2xs transition-transform duration-500 ease-out group-hover:scale-105">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-300">
                            <span class="material-symbols-outlined text-5xl">inventory_2</span>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-1">Sin Imagen</span>
                        </div>
                    @endif
                    
                    {{-- Enlace a Detalle de Producto desde la Imagen --}}
                    <a href="{{ route('catalogo.show', $producto->id) }}" class="absolute inset-0 z-10" title="Ver detalles de {{ $producto->nombre }}"></a>

                    {{-- Badges Superior Izquierda (Controlado deshabilitado a solicitud del cliente) --}}
                    {{-- @if($producto->es_controlado)
                    <div class="absolute top-3 left-3 flex flex-col gap-1.5 items-start z-10 pointer-events-none">
                        <span class="inline-flex items-center gap-1 bg-red-500 text-white text-[10px] font-black px-2 py-1 rounded-md uppercase shadow-sm tracking-wide">
                            <span class="material-symbols-outlined text-[12px]">lock</span> Controlado
                        </span>
                    </div>
                    @endif --}}

                    @php
                        $esFavorito = in_array($producto->id, $favoritosIds);
                    @endphp
                    {{-- Botón Favorito Funcional --}}
                    <div class="absolute top-3 right-3 z-20">
                        <button type="button" 
                                data-producto-id="{{ $producto->id }}"
                                class="btn-favorito-destacado flex items-center justify-center w-8 h-8 rounded-full transition-all duration-300 shadow-xs {{ $esFavorito ? 'bg-red-50 text-red-500 shadow-md ring-1 ring-red-200' : 'bg-white/95 text-slate-400 hover:text-red-500 hover:bg-white hover:shadow-md' }}" 
                                title="{{ $esFavorito ? 'Quitar de favoritos' : 'Añadir a favoritos' }}">
                            <span class="material-symbols-outlined text-[19px] transition-transform duration-200 pointer-events-none {{ $esFavorito ? 'fill-1 text-red-500' : '' }}">favorite</span>
                        </button>
                    </div>
                    
                    {{-- Unidad de medida --}}
                    <div class="absolute bottom-3 left-3 pointer-events-none z-20">
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
                    
                    {{-- Título (Con redirección a detalle de producto) --}}
                    <a href="{{ route('catalogo.show', $producto->id) }}" class="block group/link mb-2" title="Ver detalles de {{ $producto->nombre }}">
                        <h3 class="text-slate-900 font-bold text-base leading-snug line-clamp-2 min-h-[2.5rem] group-hover/link:text-[#1e549e] group-hover:text-[#1e549e] transition-colors cursor-pointer">
                            {{ $producto->nombre }}
                        </h3>
                    </a>
                    
                    {{-- Descripción --}}
                    <p class="text-xs text-slate-500 mb-4 line-clamp-2 flex-grow leading-relaxed relative z-20">
                        {{ $producto->descripcion }}
                    </p>
                    
                    {{-- Fila Inferior: Precios (USD y VES) y Call to Action (CTA) --}}
                    <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between relative z-20">
                        
                        {{-- Contenedor de Precios (PVP Final al Público) --}}
                        @php
                            $precioFinalPvp = $producto->precio_final_pvp;
                            $precioRegularPvp = $producto->precio_regular_pvp;
                        @endphp
                        <div class="flex flex-col">
                            {{-- Precio en Dólares (PVP Final con IVA) --}}
                            @if($producto->precio_oferta_usd)
                                <div class="flex items-baseline gap-1.5">
                                    <span class="text-xl font-black text-rose-600">${{ number_format($precioFinalPvp, 2) }}</span>
                                    <span class="text-[11px] text-slate-400 font-bold line-through">${{ number_format($precioRegularPvp, 2) }}</span>
                                </div>
                            @else
                                <div class="flex items-baseline gap-1">
                                    <span class="text-xl font-black text-[#1e549e]">${{ number_format($precioFinalPvp, 2) }}</span>
                                    <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">USD</span>
                                </div>
                            @endif
                            
                            {{-- Precio en Bolívares Oficial y Etiqueta IVA --}}
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="text-[11px] text-slate-500 font-bold tracking-wide">
                                    Bs. {{ number_format($precioFinalPvp * $tasaDolar, 2, ',', '.') }}
                                </span>
                                <span class="text-[9px] font-bold px-1.5 py-0.2 rounded {{ $producto->precio_incluye_iva ? 'bg-teal-50 text-teal-700' : 'bg-slate-100 text-slate-600' }}" title="{{ $producto->precio_incluye_iva ? 'Precio con IVA incluido' : 'PVP con IVA al 16% (Base $' . number_format($producto->precio_venta_usd, 2) . ')' }}">
                                    {{ $producto->precio_incluye_iva ? 'IVA inc.' : 'PVP' }}
                                </span>
                            </div>
                        </div>
                        
                        {{-- BOTÓN AÑADIR AL CARRITO --}}
                        <button type="button" 
                                data-producto-id="{{ $producto->id }}"
                                class="btn-add-cart-destacado flex items-center justify-center size-10 rounded-xl bg-[#e5f2fb] text-[#1e549e] hover:bg-[#1e549e] hover:text-white transition-all duration-200 group/btn shrink-0" 
                                title="Añadir al carrito">
                            <span class="material-symbols-outlined text-[20px] group-active/btn:scale-95 transition-transform pointer-events-none">add_shopping_cart</span>
                        </button>

                    </div>

                </div>
            </article>
            @endforeach
        </div>
        
        <div class="mt-12 md:mt-16 text-center">
            <a href="{{ route('catalogo') }}" class="btn-primary h-12 px-8 text-base shadow-md group">
                <span>Explorar Catálogo Completo</span>
                <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </a>
        </div>
    </div>
</section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Capturar todos los botones de añadir al carrito
        const botonesCart = document.querySelectorAll('.btn-add-cart-destacado');

        botonesCart.forEach(boton => {
            boton.addEventListener('click', function(e) {
                e.preventDefault();
                const productoId = this.getAttribute('data-producto-id');
                agregarAlCarrito(productoId, this);
            });
        });

        // 2. Capturar todos los botones de favoritos
        const botonesFavoritos = document.querySelectorAll('.btn-favorito-destacado');

        botonesFavoritos.forEach(boton => {
            boton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const productoId = this.getAttribute('data-producto-id');
                toggleFavorito(productoId, this);
            });
        });

        // 3. Lógica principal para Alternar Favorito (AJAX)
        function toggleFavorito(productoId, botonElement) {
            const icono = botonElement.querySelector('.material-symbols-outlined');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Feedback visual instantáneo
            if (icono) {
                icono.classList.add('scale-125');
                setTimeout(() => icono.classList.remove('scale-125'), 200);
            }
            botonElement.disabled = true;

            fetch("{{ route('favoritos.toggle') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "Accept": "application/json"
                },
                body: JSON.stringify({ producto_id: productoId })
            })
            .then(async response => {
                if (response.status === 401) {
                    mostrarToastIndependiente('Debes iniciar sesión para guardar favoritos', 'error');
                    setTimeout(() => {
                        window.location.href = "{{ route('login') }}";
                    }, 1200);
                    return Promise.reject('Requiere autenticación');
                }
                return response.json();
            })
            .then(data => {
                if (data && data.status === 'success') {
                    if (data.is_favorite) {
                        // Cambiar a estado Activo (Rojo / Relleno)
                        botonElement.className = 'btn-favorito-destacado flex items-center justify-center w-8 h-8 rounded-full transition-all duration-300 shadow-xs bg-red-50 text-red-500 shadow-md ring-1 ring-red-200';
                        if (icono) {
                            icono.className = 'material-symbols-outlined text-[19px] transition-transform duration-200 pointer-events-none fill-1 text-red-500';
                        }
                        botonElement.setAttribute('title', 'Quitar de favoritos');
                        mostrarToastIndependiente(data.message || '¡Guardado en tus favoritos!', 'success');
                    } else {
                        // Cambiar a estado Inactivo (Gris / Línea)
                        botonElement.className = 'btn-favorito-destacado flex items-center justify-center w-8 h-8 rounded-full transition-all duration-300 shadow-xs bg-white/95 text-slate-400 hover:text-red-500 hover:bg-white hover:shadow-md';
                        if (icono) {
                            icono.className = 'material-symbols-outlined text-[19px] transition-transform duration-200 pointer-events-none text-slate-400';
                        }
                        botonElement.setAttribute('title', 'Añadir a favoritos');
                        mostrarToastIndependiente(data.message || 'Eliminado de tus favoritos', 'info');
                    }
                } else {
                    mostrarToastIndependiente(data.message || 'Error al actualizar favoritos', 'error');
                }
            })
            .catch(error => {
                if (error !== 'Requiere autenticación') {
                    console.error('Error al alternar favorito:', error);
                    mostrarToastIndependiente('Error de conexión con el servidor', 'error');
                }
            })
            .finally(() => {
                botonElement.disabled = false;
            });
        }

        // 4. Lógica principal de Carrito (AJAX)
        function agregarAlCarrito(productoId, botonElement) {
            const iconoOriginal = botonElement.innerHTML;
            botonElement.innerHTML = '<span class="material-symbols-outlined text-[22px] animate-spin">sync</span>';
            botonElement.disabled = true;

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
                if (response.status === 401) {
                    window.location.href = "{{ route('login') }}";
                    return Promise.reject('Requiere autenticación');
                }
                return response.json();
            })
            .then(data => {
                if (data && data.status === 'success') {
                    mostrarToastIndependiente('Producto agregado al carrito', 'success');

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
                botonElement.innerHTML = iconoOriginal;
                botonElement.disabled = false;
            });
        }

        // 5. Mini sistema de notificaciones (Toast) corporativo inyectado dinámicamente
        function mostrarToastIndependiente(mensaje, tipo) {
            let bgClase = 'bg-[#1e549e] border border-[#3492cf]/40 shadow-[#1e549e]/30';
            let icono = 'info';

            if (tipo === 'success') {
                bgClase = 'bg-[#0f3d6e] border border-[#3492cf] shadow-[#1e549e]/40';
                icono = 'check_circle';
            } else if (tipo === 'error') {
                bgClase = 'bg-rose-600 border border-rose-400 shadow-rose-900/40';
                icono = 'error';
            } else if (tipo === 'info') {
                bgClase = 'bg-slate-800 border border-slate-600 shadow-slate-900/40';
                icono = 'bookmark_remove';
            }

            const toast = document.createElement('div');
            toast.className = `fixed bottom-6 right-6 z-[9999] flex items-center gap-3 px-5 py-3 rounded-2xl shadow-2xl text-white transform transition-all duration-300 translate-y-20 opacity-0 font-sans text-sm font-semibold backdrop-blur-md ${bgClase}`;
            toast.innerHTML = `<span class="material-symbols-outlined text-xl text-[#aad6f3] shrink-0">${icono}</span> <span>${mensaje}</span>`;

            document.body.appendChild(toast);

            requestAnimationFrame(() => {
                setTimeout(() => {
                    toast.classList.remove('translate-y-20', 'opacity-0');
                }, 10);
            });

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