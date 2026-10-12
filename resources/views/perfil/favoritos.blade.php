@extends('layouts.app')

@section('title', 'Mis Favoritos - Agrosuministros Rodríguez')

@section('content')
<div class="bg-slate-50 min-h-screen font-sans pb-20 relative">

    {{-- Encabezado decorativo corporativo --}}
    <div class="relative bg-gradient-to-r from-[#0b1d38] via-[#1e549e] to-[#3492cf] h-40 lg:h-48 overflow-hidden transition-all">
        <div class="absolute inset-0 opacity-15 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute -bottom-10 -right-10 w-40 h-40 lg:w-64 lg:h-64 rounded-full bg-accent/20 blur-3xl animate-pulse-slow"></div>
    </div>

    {{-- Contenedor Principal --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 -mt-10 lg:-mt-16 relative z-10">
        
        <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">

            {{-- ASIDE: Menú Lateral Corporativo --}}
            @include('perfil.partials.sidebar', ['active' => 'favoritos'])

            {{-- MAIN: Grid de Favoritos --}}
            <main class="flex-1 min-w-0 animate-fade-in-up">
                
                {{-- Cabecera del Main --}}
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-xl lg:text-2xl font-black text-agro-dark flex items-center gap-3">
                            <span class="material-symbols-outlined text-red-500 fill-1 text-[32px]">favorite</span>
                            Mis Productos Favoritos
                        </h3>
                        <p class="text-sm text-gray-500 mt-1 font-medium">
                            Acceso directo a los insumos que has guardado para tu campo y producción.
                        </p>
                    </div>
                    <div>
                        <span class="bg-[#e5f2fb] text-[#1e549e] font-bold text-xs px-3.5 py-1.5 rounded-full border border-[#aad6f3] inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-[#3492cf]">bookmark</span>
                            {{ $productos->total() }} {{ $productos->total() === 1 ? 'Producto' : 'Productos' }}
                        </span>
                    </div>
                </div>

                @if($productos->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($productos as $producto)
                            <article id="fav-card-{{ $producto->id }}" class="group relative flex flex-col bg-white rounded-2xl border border-gray-100 hover:border-primary/30 hover:shadow-xl hover:shadow-primary/5 transition-all duration-300 overflow-hidden h-full">
                                
                                {{-- ÁREA DE IMAGEN --}}
                                <div class="relative w-full aspect-[4/3] bg-gray-50 overflow-hidden">
                                    @php
                                        $imgUrl = $producto->imagenes->first()?->imagen_url ?? $producto->imagen_url;
                                    @endphp

                                    @if($imgUrl)
                                        <div class="absolute inset-0 bg-cover bg-center group-hover:scale-105 transition-transform duration-700 ease-out mix-blend-multiply" 
                                             style="background-image: url('{{ asset($imgUrl) }}');"></div>
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center text-gray-300 bg-gray-50">
                                            <span class="material-symbols-outlined text-5xl mb-2">image</span>
                                            <span class="text-[10px] font-bold uppercase tracking-widest">Sin Imagen</span>
                                        </div>
                                    @endif

                                    {{-- Enlace a Detalle de Producto desde la Imagen --}}
                                    <a href="{{ route('catalogo.show', $producto->id) }}" class="absolute inset-0 z-10" title="Ver detalles de {{ $producto->nombre }}"></a>

                                    {{-- Botón Quitar de Favoritos --}}
                                    <div class="absolute top-3 right-3 z-20">
                                        <button type="button" 
                                                onclick="removerDeFavoritos({{ $producto->id }}, this)"
                                                class="flex items-center justify-center w-8 h-8 rounded-full bg-red-50 text-red-500 ring-1 ring-red-200 shadow-sm hover:scale-110 active:scale-95 transition-all" 
                                                title="Quitar de favoritos">
                                            <span class="material-symbols-outlined text-[19px] fill-1 text-red-500 pointer-events-none">favorite</span>
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
                                    <div class="flex justify-between items-center mb-2">
                                        <p class="text-[11px] text-agro-accent font-bold uppercase tracking-wider truncate pr-2">
                                            {{ $producto->marca->nombre ?? 'GENÉRICO' }}
                                        </p>
                                        
                                        <div>
                                            @if($producto->stock_total > 0)
                                                <span class="text-[10px] font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Stock
                                                </span>
                                            @else
                                                <span class="text-[10px] font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded">
                                                    Agotado
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Título con redirección a detalle --}}
                                    <a href="{{ route('catalogo.show', $producto->id) }}" class="block group/link mb-2" title="Ver detalles de {{ $producto->nombre }}">
                                        <h3 class="text-slate-900 font-bold text-base leading-snug line-clamp-2 min-h-[2.5rem] group-hover/link:text-[#1e549e] group-hover:text-[#1e549e] transition-colors cursor-pointer">
                                            {{ $producto->nombre }}
                                        </h3>
                                    </a>

                                    <p class="text-xs text-slate-500 mb-4 line-clamp-2 flex-grow leading-relaxed">
                                        {{ $producto->descripcion }}
                                    </p>

                                    {{-- Precios y Añadir al Carrito (PVP Final) --}}
                                    @php
                                        $precioFinalPvp = $producto->precio_final_pvp;
                                        $precioRegularPvp = $producto->precio_regular_pvp;
                                    @endphp
                                    <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
                                        <div class="flex flex-col">
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
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <span class="text-[11px] text-slate-500 font-bold tracking-wide">
                                                    Bs. {{ number_format($precioFinalPvp * $tasaDolar, 2, ',', '.') }}
                                                </span>
                                                <span class="text-[9px] font-bold px-1.5 py-0.2 rounded {{ $producto->precio_incluye_iva ? 'bg-teal-50 text-teal-700' : 'bg-slate-100 text-slate-600' }}">
                                                    {{ $producto->precio_incluye_iva ? 'IVA inc.' : 'PVP' }}
                                                </span>
                                            </div>
                                        </div>

                                        <button type="button" 
                                                onclick="agregarAlCarritoFav({{ $producto->id }}, this)"
                                                class="flex items-center justify-center size-10 rounded-xl bg-[#e5f2fb] text-[#1e549e] hover:bg-[#1e549e] hover:text-white transition-all duration-200 shrink-0" 
                                                title="Añadir al carrito">
                                            <span class="material-symbols-outlined text-[20px] pointer-events-none">add_shopping_cart</span>
                                        </button>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    {{-- Paginación --}}
                    <div class="mt-8 flex justify-center">
                        {{ $productos->links('pagination::tailwind') }}
                    </div>
                @else
                    {{-- ESTADO VACÍO --}}
                    <div class="bg-white rounded-3xl p-12 text-center border border-gray-100 shadow-sm flex flex-col items-center">
                        <div class="w-20 h-20 bg-red-50 text-red-400 rounded-full flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined text-[40px]">favorite_border</span>
                        </div>
                        <h4 class="text-xl font-black text-agro-dark mb-2">No tienes productos en favoritos</h4>
                        <p class="text-gray-500 text-sm max-w-md mb-6 leading-relaxed">
                            Guarda los insumos y productos que más necesitas haciendo clic en el icono de corazón en la página principal o catálogo.
                        </p>
                        <a href="{{ route('catalogo') }}" class="btn-primary h-11 px-6 text-sm">
                            <span class="material-symbols-outlined text-[18px]">storefront</span>
                            Explorar Catálogo
                        </a>
                    </div>
                @endif
            </main>
        </div>
    </div>
</div>

{{-- Toast container --}}
<div id="fav-toast" class="fixed bottom-6 right-6 z-[9999] flex items-center gap-3 px-5 py-3 rounded-2xl shadow-2xl text-white transform transition-all duration-300 translate-y-20 opacity-0 font-sans text-sm font-semibold pointer-events-none">
</div>
@endsection

@push('scripts')
<script>
    function removerDeFavoritos(productoId, btnElement) {
        btnElement.disabled = true;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch("{{ route('favoritos.toggle') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json"
            },
            body: JSON.stringify({ producto_id: productoId })
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.status === 'success') {
                const card = document.getElementById('fav-card-' + productoId);
                if (card) {
                    card.style.transition = 'all 0.3s ease';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.9)';
                    setTimeout(() => card.remove(), 300);
                }
                mostrarFavToast('Producto eliminado de favoritos', 'info');
            }
        })
        .catch(err => {
            console.error(err);
            mostrarFavToast('Error al conectar con el servidor', 'error');
            btnElement.disabled = false;
        });
    }

    function agregarAlCarritoFav(productoId, btnElement) {
        const iconOriginal = btnElement.innerHTML;
        btnElement.innerHTML = '<span class="material-symbols-outlined text-[20px] animate-spin">sync</span>';
        btnElement.disabled = true;
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
        .then(res => res.json())
        .then(data => {
            if (data && data.status === 'success') {
                mostrarFavToast('Producto añadido al carrito', 'success');
                const badge = document.getElementById('cart-count-badge');
                const badgeContainer = document.getElementById('cart-badge-container');
                if (badge && badgeContainer) {
                    badge.innerText = data.cart_count;
                    badgeContainer.classList.remove('hidden');
                }
            } else {
                mostrarFavToast(data.message || 'Error al agregar', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            mostrarFavToast('Error al agregar al carrito', 'error');
        })
        .finally(() => {
            btnElement.innerHTML = iconOriginal;
            btnElement.disabled = false;
        });
    }

    function mostrarFavToast(mensaje, tipo) {
        const toast = document.getElementById('fav-toast');
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

        toast.className = `fixed bottom-6 right-6 z-[9999] flex items-center gap-3 px-5 py-3 rounded-2xl shadow-2xl text-white transform transition-all duration-300 font-sans text-sm font-semibold backdrop-blur-md ${bgClase}`;
        toast.innerHTML = `<span class="material-symbols-outlined text-xl text-[#aad6f3] shrink-0">${icono}</span> <span>${mensaje}</span>`;

        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-20', 'opacity-0');
        });

        setTimeout(() => {
            toast.classList.add('translate-y-20', 'opacity-0');
        }, 3500);
    }
</script>
@endpush
