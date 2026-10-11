@extends('layouts.app')

@section('title', $producto->nombre . ' - Agrosuministros Rodríguez')

@section('content')
<div class="bg-slate-50 min-h-screen font-sans pb-20 animate-fade-in-up">

    {{-- 1. BREADCRUMB & HEADER SUPERIOR SUTIL --}}
    <div class="bg-white border-b border-slate-200/80 py-3.5 mb-6">
        <div class="layout-container px-4 sm:px-6">
            <nav class="flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-500">
                <a href="{{ url('/') }}" class="hover:text-[#1e549e] transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">home</span> Inicio
                </a>
                <span class="text-slate-300">/</span>
                <a href="{{ route('catalogo') }}" class="hover:text-[#1e549e] transition-colors">
                    Catálogo
                </a>
                @if($producto->categoria)
                    <span class="text-slate-300">/</span>
                    <a href="{{ route('catalogo', ['categoria' => $producto->categoria_id]) }}" class="hover:text-[#1e549e] transition-colors">
                        {{ $producto->categoria->nombre }}
                    </a>
                @endif
                <span class="text-slate-300">/</span>
                <span class="text-slate-800 font-bold truncate max-w-xs sm:max-w-md">{{ $producto->nombre }}</span>
            </nav>
        </div>
    </div>

    {{-- 2. CONTENEDOR PRINCIPAL DEL PRODUCTO --}}
    <div class="layout-container px-4 sm:px-6">
        
        {{-- Tarjeta Principal: Galería + Información de Compra --}}
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-5 sm:p-8 lg:p-10 mb-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                
                {{-- COLUMNA IZQUIERDA: GALERÍA DE FOTOS INTERACTIVA (5 cols en lg) --}}
                <div class="lg:col-span-6 flex flex-col gap-4">
                    
                    @php
                        // Colección combinada de fotos: La principal siempre va de primera
                        $todasLasFotos = collect();
                        if ($producto->imagen_url) {
                            $todasLasFotos->push((object)[
                                'id' => 'principal',
                                'url' => asset($producto->imagen_url),
                                'es_principal' => true,
                                'titulo' => 'Foto Principal (Portada)'
                            ]);
                        }
                        if ($producto->imagenes && $producto->imagenes->count() > 0) {
                            foreach ($producto->imagenes as $idx => $imgSec) {
                                $todasLasFotos->push((object)[
                                    'id' => $imgSec->id,
                                    'url' => asset($imgSec->url_imagen),
                                    'es_principal' => false,
                                    'titulo' => 'Ángulo ' . ($idx + 1)
                                ]);
                            }
                        }
                        $fotoInicial = $todasLasFotos->first();
                        $totalFotos = $todasLasFotos->count();
                    @endphp

                    {{-- Visor Principal de Imagen --}}
                    <div class="relative w-full aspect-square sm:aspect-[4/3] lg:aspect-square bg-gradient-to-b from-slate-50 to-slate-100/60 rounded-3xl border border-slate-200/80 overflow-hidden flex items-center justify-center p-6 group">
                        
                        {{-- Imagen en pantalla completa / activa --}}
                        @if($fotoInicial)
                            <img id="mainProductImage" 
                                 src="{{ $fotoInicial->url }}" 
                                 alt="{{ $producto->nombre }}" 
                                 class="w-full h-full object-contain max-h-[460px] drop-shadow-md transition-all duration-300 transform group-hover:scale-105 cursor-zoom-in"
                                 onclick="abrirZoomModal()">
                        @else
                            <div class="flex flex-col items-center justify-center text-slate-300">
                                <span class="material-symbols-outlined text-7xl mb-2">inventory_2</span>
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Sin Imagen Disponible</span>
                            </div>
                        @endif

                        {{-- Badges Flotantes en la Imagen --}}
                        <div class="absolute top-4 left-4 flex flex-col gap-1.5 items-start pointer-events-none z-10">
                            @if($producto->es_combo)
                                <span class="inline-flex items-center gap-1 bg-[#1e549e] text-white text-[11px] font-black px-2.5 py-1 rounded-lg uppercase shadow-sm tracking-wide">
                                    <span class="material-symbols-outlined text-[14px]">layers</span> Combo Especial
                                </span>
                            @endif

                            @if($producto->precio_oferta_usd)
                                <span class="inline-flex items-center gap-1 bg-rose-600 text-white text-[11px] font-black px-2.5 py-1 rounded-lg uppercase shadow-sm tracking-wide animate-pulse">
                                    <span class="material-symbols-outlined text-[14px]">local_fire_department</span> ¡Oferta!
                                </span>
                            @endif
                        </div>

                        {{-- Badge Contador de Fotos --}}
                        @if($totalFotos > 1)
                            <div class="absolute bottom-4 right-4 bg-slate-900/75 backdrop-blur-md text-white text-[11px] font-bold px-3 py-1 rounded-full border border-white/20 flex items-center gap-1.5 shadow-md pointer-events-none">
                                <span class="material-symbols-outlined text-[14px]">photo_camera</span>
                                <span id="galleryCounter">1 / {{ $totalFotos }}</span>
                            </div>

                            {{-- Flechas de navegación rápida sobre la imagen --}}
                            <button type="button" 
                                    onclick="navegarFoto(-1)" 
                                    class="absolute left-3 top-1/2 -translate-y-1/2 size-9 rounded-full bg-white/90 hover:bg-white text-slate-700 hover:text-[#1e549e] flex items-center justify-center shadow-md border border-slate-200/80 opacity-0 group-hover:opacity-100 transition-all hover:scale-110 active:scale-95" 
                                    title="Foto anterior">
                                <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                            </button>

                            <button type="button" 
                                    onclick="navegarFoto(1)" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 size-9 rounded-full bg-white/90 hover:bg-white text-slate-700 hover:text-[#1e549e] flex items-center justify-center shadow-md border border-slate-200/80 opacity-0 group-hover:opacity-100 transition-all hover:scale-110 active:scale-95" 
                                    title="Foto siguiente">
                                <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                            </button>
                        @endif

                        {{-- Botón para abrir en modal / zoom --}}
                        @if($fotoInicial)
                            <button type="button" 
                                    onclick="abrirZoomModal()" 
                                    class="absolute top-4 right-4 size-9 rounded-full bg-white/90 hover:bg-white text-slate-600 hover:text-[#1e549e] flex items-center justify-center shadow-sm border border-slate-200/70 opacity-80 hover:opacity-100 transition-all" 
                                    title="Ver imagen en tamaño completo">
                                <span class="material-symbols-outlined text-[19px]">zoom_in</span>
                            </button>
                        @endif
                    </div>

                    {{-- Miniaturas de la Galería (Swiper / Thumbnails) --}}
                    @if($totalFotos > 1)
                        <div class="flex items-center gap-3 overflow-x-auto pb-2 pt-1 scrollbar-thin">
                            @foreach($todasLasFotos as $index => $foto)
                                <button type="button" 
                                        onclick="seleccionarFotoGaleria({{ $index }}, '{{ $foto->url }}')"
                                        id="thumb-btn-{{ $index }}"
                                        class="thumb-item relative size-18 sm:size-20 rounded-2xl overflow-hidden border-2 bg-slate-50 shrink-0 transition-all {{ $index === 0 ? 'border-[#1e549e] ring-2 ring-[#1e549e]/30 scale-102 shadow-sm' : 'border-slate-200 hover:border-slate-300 opacity-70 hover:opacity-100' }}">
                                    <img src="{{ $foto->url }}" alt="Miniatura {{ $index + 1 }}" class="w-full h-full object-cover">
                                    @if($foto->es_principal)
                                        <span class="absolute bottom-0 inset-x-0 bg-slate-900/80 text-[8px] font-black uppercase text-white py-0.5 text-center leading-none">
                                            Portada
                                        </span>
                                    @else
                                        <span class="absolute bottom-1 right-1 bg-black/60 text-white font-mono text-[8px] px-1 rounded font-bold">
                                            #{{ $index + 1 }}
                                        </span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    @else
                        <div class="flex items-center gap-2 text-slate-400 text-xs px-2">
                            <span class="material-symbols-outlined text-[16px] text-emerald-600">verified</span>
                            <span>Fotografía oficial y certificada de presentación</span>
                        </div>
                    @endif

                </div>

                {{-- COLUMNA DERECHA: INFORMACIÓN DEL PRODUCTO Y COMPRA (7 cols en lg) --}}
                <div class="lg:col-span-6 flex flex-col justify-between">
                    <div>
                        
                        {{-- Fila Superior: Marca, Categoría y Botón Favorito --}}
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <div class="flex flex-wrap items-center gap-2">
                                @if($producto->categoria)
                                    <a href="{{ route('catalogo', ['categoria' => $producto->categoria_id]) }}" 
                                       class="inline-flex items-center gap-1 text-[11px] font-black uppercase tracking-wider text-[#1e549e] bg-[#e5f2fb] hover:bg-[#aad6f3]/60 px-3 py-1 rounded-xl transition-colors">
                                        <span class="material-symbols-outlined text-[14px]">category</span>
                                        {{ $producto->categoria->nombre }}
                                    </a>
                                @endif

                                @if($producto->marca)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-black uppercase tracking-wider text-slate-600 bg-slate-100 px-3 py-1 rounded-xl">
                                        <span class="material-symbols-outlined text-[14px] text-amber-500">verified</span>
                                        {{ $producto->marca->nombre }}
                                    </span>
                                @endif
                            </div>

                            {{-- Botón Favorito Funcional --}}
                            <button type="button" 
                                    id="btnFavoritoDetalle"
                                    onclick="toggleFavoritoDetalle({{ $producto->id }})"
                                    class="size-11 rounded-2xl flex items-center justify-center transition-all border {{ $esFavorito ? 'bg-rose-50 text-rose-500 border-rose-200 shadow-sm' : 'bg-white text-slate-400 border-slate-200 hover:text-rose-500 hover:border-rose-200' }}"
                                    title="{{ $esFavorito ? 'Quitar de favoritos' : 'Guardar en favoritos' }}">
                                <span id="iconoFavoritoDetalle" class="material-symbols-outlined text-[24px] {{ $esFavorito ? 'fill-1 text-rose-500' : '' }}">favorite</span>
                            </button>
                        </div>

                        {{-- Título Principal del Producto --}}
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight leading-snug mb-3">
                            {{ $producto->nombre }}
                        </h1>

                        {{-- Códigos de Referencia (SKU / Barras) --}}
                        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 mb-6">
                            @if($producto->sku)
                                <div class="flex items-center gap-1 font-mono">
                                    <span class="text-slate-400 uppercase text-[10px] font-bold">SKU:</span>
                                    <span class="font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">{{ $producto->sku }}</span>
                                </div>
                            @endif

                            @if($producto->codigo_barras)
                                <div class="flex items-center gap-1 font-mono">
                                    <span class="text-slate-400 uppercase text-[10px] font-bold">CÓDIGO:</span>
                                    <span class="font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">{{ $producto->codigo_barras }}</span>
                                </div>
                            @endif

                            {{-- Distintivo de Contenido Neto / Presentación --}}
                            @if($producto->contenido_neto)
                                <div class="flex items-center gap-1">
                                    <span class="text-slate-400 uppercase text-[10px] font-bold">Contenido:</span>
                                    <span class="font-extrabold text-emerald-800 bg-emerald-50 border border-emerald-200/80 px-2 py-0.5 rounded">
                                        {{ (float)$producto->contenido_neto }} {{ strtoupper($producto->unidad_contenido) }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- CAJA DE PRECIOS CONVERSORES (PVP FINAL AL PÚBLICO) --}}
                        @php
                            $precioFinalPvp = $producto->precio_final_pvp;
                            $precioRegularPvp = $producto->precio_regular_pvp;
                            $precioOfertaPvp = $producto->precio_oferta_pvp;
                            
                            $precioBs = round($precioFinalPvp * $tasaDolar, 2);
                            $ahorroUsd = $precioOfertaPvp ? ($precioRegularPvp - $precioOfertaPvp) : 0;
                        @endphp

                        <div class="bg-gradient-to-br from-slate-50 to-blue-50/40 rounded-3xl border border-slate-200/90 p-5 sm:p-6 mb-6">
                            <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-2">
                                <div>
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Precio Final al Público</span>
                                    <div class="flex items-baseline gap-3">
                                        @if($producto->precio_oferta_usd)
                                            <span class="text-3xl sm:text-4xl font-black text-rose-600">${{ number_format($precioFinalPvp, 2) }}</span>
                                            <span class="text-lg font-bold text-slate-400 line-through">${{ number_format($precioRegularPvp, 2) }}</span>
                                            <span class="text-xs font-black text-rose-700 bg-rose-100 px-2 py-0.5 rounded-md uppercase">
                                                Ahorras ${{ number_format($ahorroUsd, 2) }}
                                            </span>
                                        @else
                                            <span class="text-3xl sm:text-4xl font-black text-[#1e549e]">${{ number_format($precioFinalPvp, 2) }}</span>
                                            <span class="text-sm font-bold text-slate-500 uppercase">USD</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Precio en Bolívares Oficial --}}
                                <div class="sm:text-right mt-2 sm:mt-0">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-0.5">Tasa Oficial BCV</span>
                                    <div class="text-xl sm:text-2xl font-black text-slate-800">
                                        Bs. {{ number_format($precioBs, 2, ',', '.') }}
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-500">
                                        1 USD = Bs. {{ number_format($tasaDolar, 2, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            {{-- Estado del Impuesto / IVA --}}
                            <div class="mt-4 pt-3.5 border-t border-slate-200/70 flex flex-wrap items-center justify-between gap-2">
                                @if($producto->precio_incluye_iva)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-teal-800 bg-teal-50 border border-teal-200/80 px-2.5 py-1 rounded-xl">
                                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                        PVP con IVA incluido (Base imponible: ${{ number_format($producto->precio_venta_usd, 2) }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 bg-white border border-slate-200 px-2.5 py-1 rounded-xl">
                                        <span class="material-symbols-outlined text-[16px] text-slate-400">info</span>
                                        PVP final (Base imponible: ${{ number_format($producto->precio_venta_usd, 2) }} + {{ (float)$ivaPorcentaje }}% IVA)
                                    </span>
                                @endif

                                <span class="text-xs text-slate-500 font-medium">
                                    Por {{ $producto->unidad_medida }}
                                </span>
                            </div>
                        </div>

                        {{-- ESTADO DE INVENTARIO Y STOCK --}}
                        <div class="mb-6 flex items-center justify-between">
                            @if($producto->stock_total <= 0)
                                <div class="flex items-center gap-2">
                                    <span class="size-3 rounded-full bg-rose-500"></span>
                                    <span class="text-sm font-black text-rose-600">Agotado Temporalmente</span>
                                </div>
                            @elseif($producto->stock_total <= $producto->stock_minimo_alerta)
                                <div class="flex items-center gap-2">
                                    <span class="size-3 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span class="text-sm font-black text-amber-700">Pocas Unidades Disponibles ({{ (float)$producto->stock_total }} {{ $producto->unidad_medida }})</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2">
                                    <span class="size-3 rounded-full bg-emerald-500"></span>
                                    <span class="text-sm font-black text-emerald-700">En Inventario ({{ (float)$producto->stock_total }} {{ $producto->unidad_medida }} disponibles)</span>
                                </div>
                            @endif

                            <span class="text-xs text-slate-400 font-semibold">Entrega inmediata</span>
                        </div>

                        {{-- SELECTOR DE CANTIDAD Y BOTONES DE COMPRA --}}
                        @if($producto->stock_total > 0)
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 mb-8">
                                
                                {{-- Control de Cantidad --}}
                                <div class="flex items-center justify-between border border-slate-200 rounded-2xl bg-slate-50 p-1 w-full sm:w-40 shrink-0 shadow-2xs">
                                    <button type="button" 
                                            onclick="modificarCantidad(-1)" 
                                            class="size-10 rounded-xl bg-white hover:bg-slate-100 text-slate-700 flex items-center justify-center font-black transition-colors shadow-2xs active:scale-95" 
                                            title="Disminuir">
                                        <span class="material-symbols-outlined text-[18px]">remove</span>
                                    </button>
                                    
                                    <input type="number" 
                                           id="cantidadInput" 
                                           value="1" 
                                           min="1" 
                                           max="{{ (int)$producto->stock_total }}" 
                                           class="w-14 text-center bg-transparent border-none font-black text-slate-900 text-base outline-none p-0 focus:ring-0">
                                    
                                    <button type="button" 
                                            onclick="modificarCantidad(1)" 
                                            class="size-10 rounded-xl bg-white hover:bg-slate-100 text-slate-700 flex items-center justify-center font-black transition-colors shadow-2xs active:scale-95" 
                                            title="Aumentar">
                                        <span class="material-symbols-outlined text-[18px]">add</span>
                                    </button>
                                </div>

                                {{-- Botón Añadir al Carrito --}}
                                <button type="button" 
                                        id="btnAddToCart"
                                        onclick="ejecutarAgregarCarrito()"
                                        class="flex-1 h-12 px-6 rounded-2xl bg-[#1e549e] hover:bg-[#163f77] text-white font-extrabold text-sm transition-all flex items-center justify-center gap-2.5 shadow-md shadow-[#1e549e]/20 active:scale-98">
                                    <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span>
                                    <span>Añadir al Carrito</span>
                                </button>

                                {{-- Botón Comprar Directo --}}
                                <button type="button" 
                                        onclick="comprarAhora()"
                                        class="h-12 px-6 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm transition-all flex items-center justify-center gap-2 shadow-md shadow-emerald-600/20 active:scale-98">
                                    <span class="material-symbols-outlined text-[20px]">shopping_bag</span>
                                    <span>Comprar Ahora</span>
                                </button>
                            </div>
                        @else
                            {{-- Botón Solicitud si está agotado --}}
                            <div class="mb-8 p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm">¿Necesitas este producto?</h4>
                                    <p class="text-xs text-slate-500">Solicítalo y te avisaremos en cuanto recibamos nuevo lote.</p>
                                </div>
                                <a href="{{ route('catalogo') }}#solicitar" class="px-4 py-2.5 rounded-xl bg-[#1e549e] text-white font-bold text-xs hover:bg-[#163f77] transition-all">
                                    Solicitar Insumo
                                </a>
                            </div>
                        @endif

                    </div>

                    {{-- BENEFICIOS / GARANTÍAS COMERCIALES --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-6 border-t border-slate-100 text-slate-600">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[#1e549e] text-[22px]">local_shipping</span>
                            <div class="leading-tight">
                                <span class="block text-[11px] font-bold text-slate-800">Despacho</span>
                                <span class="text-[10px] text-slate-400">Rápido y Seguro</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-emerald-600 text-[22px]">verified</span>
                            <div class="leading-tight">
                                <span class="block text-[11px] font-bold text-slate-800">100% Original</span>
                                <span class="text-[10px] text-slate-400">Calidad Certificada</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-amber-600 text-[22px]">support_agent</span>
                            <div class="leading-tight">
                                <span class="block text-[11px] font-bold text-slate-800">Asesoría</span>
                                <span class="text-[10px] text-slate-400">Agrónomos Expertos</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-purple-600 text-[22px]">security</span>
                            <div class="leading-tight">
                                <span class="block text-[11px] font-bold text-slate-800">Pago Flexible</span>
                                <span class="text-[10px] text-slate-400">USD, Pago Móvil, Bs.</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        {{-- 3. SECCIÓN DETALLES, ESPECIFICACIONES Y TÉCNICAS --}}
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-10 mb-12">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-5 mb-6">
                <span class="material-symbols-outlined text-2xl text-[#1e549e]">description</span>
                <h2 class="text-xl font-black text-slate-900">Descripción y Especificaciones Técnicas</h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                {{-- Descripción extendida --}}
                <div class="lg:col-span-7">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-3">Información del Producto</h3>
                    <div class="prose prose-slate max-w-none text-slate-600 text-sm leading-relaxed">
                        @if($producto->descripcion)
                            <p class="whitespace-pre-line">{{ $producto->descripcion }}</p>
                        @else
                            <p class="text-slate-400 italic">No se ha registrado una descripción detallada para este artículo.</p>
                        @endif
                    </div>
                </div>

                {{-- Atributos Técnicos (JSON) --}}
                <div class="lg:col-span-5">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-3">Ficha Técnica</h3>
                    
                    @php
                        $atributos = [];
                        if ($producto->atributos_json) {
                            $atributos = is_array($producto->atributos_json) 
                                ? $producto->atributos_json 
                                : json_decode($producto->atributos_json, true);
                        }
                    @endphp

                    <div class="bg-slate-50 rounded-2xl border border-slate-200/80 overflow-hidden divide-y divide-slate-200/60 text-xs">
                        <div class="flex items-center justify-between p-3">
                            <span class="font-bold text-slate-500">Categoría</span>
                            <span class="font-black text-slate-800">{{ $producto->categoria->nombre ?? 'General' }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3">
                            <span class="font-bold text-slate-500">Marca</span>
                            <span class="font-black text-slate-800">{{ $producto->marca->nombre ?? 'Genérico' }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3">
                            <span class="font-bold text-slate-500">Unidad de Venta</span>
                            <span class="font-black text-slate-800 uppercase">{{ $producto->unidad_medida }}</span>
                        </div>
                        @if($producto->contenido_neto)
                        <div class="flex items-center justify-between p-3">
                            <span class="font-bold text-slate-500">Contenido Neto</span>
                            <span class="font-black text-slate-800">{{ (float)$producto->contenido_neto }} {{ strtoupper($producto->unidad_contenido) }}</span>
                        </div>
                        @endif

                        @if(!empty($atributos) && is_array($atributos))
                            @foreach($atributos as $attrKey => $attrVal)
                                <div class="flex items-center justify-between p-3">
                                    <span class="font-bold text-slate-500 capitalize">{{ str_replace('_', ' ', $attrKey) }}</span>
                                    <span class="font-black text-slate-800 text-right max-w-[60%] truncate">
                                        {{ is_array($attrVal) ? implode(', ', $attrVal) : $attrVal }}
                                    </span>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

            </div>
        </div>

        {{-- 4. PRODUCTOS RELACIONADOS --}}
        @if(isset($relacionados) && $relacionados->count() > 0)
            <div class="mb-12">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#1e549e]">category</span>
                            Productos Relacionados
                        </h2>
                        <p class="text-xs text-slate-500 mt-1">Otros insumos de la misma categoría o marca para complementar tu compra</p>
                    </div>
                    <a href="{{ route('catalogo') }}" class="text-xs font-bold text-[#1e549e] hover:underline flex items-center gap-1">
                        Ver todo el catálogo <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relacionados as $rel)
                        @php
                            $relImg = $rel->imagen_url;
                            $relPrecio = $rel->precio_final_pvp;
                        @endphp
                        <article class="group relative flex flex-col bg-white rounded-2xl border border-slate-200 hover:border-[#1e549e]/40 hover:shadow-lg transition-all duration-300 overflow-hidden h-full">
                            {{-- Imagen --}}
                            <div class="relative w-full aspect-[4/3] bg-slate-50 overflow-hidden flex items-center justify-center p-3">
                                @if($relImg)
                                    <img src="{{ asset($relImg) }}" alt="{{ $rel->nombre }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <span class="material-symbols-outlined text-4xl text-slate-300">inventory_2</span>
                                @endif
                                <a href="{{ route('catalogo.show', $rel->id) }}" class="absolute inset-0 z-10" title="Ver {{ $rel->nombre }}"></a>
                            </div>

                            {{-- Información --}}
                            <div class="p-4 flex flex-col flex-1">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ $rel->categoria->nombre ?? 'General' }}</span>
                                <a href="{{ route('catalogo.show', $rel->id) }}" class="group-hover:text-[#1e549e] transition-colors">
                                    <h4 class="font-bold text-slate-900 text-sm line-clamp-2 mb-2 min-h-[2.5rem]">{{ $rel->nombre }}</h4>
                                </a>

                                <div class="mt-auto pt-3 border-t border-slate-100 flex items-center justify-between">
                                    <div>
                                        <span class="text-base font-black text-[#1e549e]">${{ number_format($relPrecio, 2) }}</span>
                                        <span class="text-[10px] text-slate-400 block">Bs. {{ number_format($relPrecio * $tasaDolar, 2, ',', '.') }}</span>
                                    </div>
                                    <a href="{{ route('catalogo.show', $rel->id) }}" class="size-8 rounded-xl bg-slate-100 hover:bg-[#1e549e] hover:text-white text-slate-600 flex items-center justify-center transition-colors" title="Ver producto">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

</div>

{{-- MODAL LIGHTBOX / ZOOM DE IMAGEN --}}
<div id="modalZoom" class="fixed inset-0 z-50 hidden bg-black/90 backdrop-blur-md flex items-center justify-center p-4" onclick="cerrarZoomModal()">
    <div class="relative max-w-4xl max-h-[90vh] flex flex-col items-center justify-center" onclick="event.stopPropagation()">
        <button type="button" onclick="cerrarZoomModal()" class="absolute -top-12 right-0 size-10 rounded-full bg-white/20 text-white hover:bg-white/40 flex items-center justify-center transition-colors">
            <span class="material-symbols-outlined text-2xl">close</span>
        </button>
        <img id="modalZoomImage" src="" alt="Vista ampliada" class="max-w-full max-h-[85vh] object-contain rounded-2xl shadow-2xl">
    </div>
</div>

@push('scripts')
<script>
    // Colección de fotos en JavaScript
    const fotosGaleria = @json($todasLasFotos->pluck('url')->toArray());
    let fotoActualIndex = 0;
    const maxStock = {{ (int)$producto->stock_total }};

    function seleccionarFotoGaleria(index, url) {
        fotoActualIndex = index;
        const mainImg = document.getElementById('mainProductImage');
        if (mainImg) {
            mainImg.style.opacity = '0';
            setTimeout(() => {
                mainImg.src = url;
                mainImg.style.opacity = '1';
            }, 150);
        }

        // Actualizar contador
        const counter = document.getElementById('galleryCounter');
        if (counter && fotosGaleria.length > 0) {
            counter.innerText = `${index + 1} / ${fotosGaleria.length}`;
        }

        // Resaltar miniatura activa
        document.querySelectorAll('.thumb-item').forEach((thumb, idx) => {
            if (idx === index) {
                thumb.classList.add('border-[#1e549e]', 'ring-2', 'ring-[#1e549e]/30', 'scale-102', 'shadow-sm');
                thumb.classList.remove('opacity-70');
            } else {
                thumb.classList.remove('border-[#1e549e]', 'ring-2', 'ring-[#1e549e]/30', 'scale-102', 'shadow-sm');
                thumb.classList.add('opacity-70');
            }
        });
    }

    function navegarFoto(direccion) {
        if (!fotosGaleria || fotosGaleria.length <= 1) return;
        fotoActualIndex = (fotoActualIndex + direccion + fotosGaleria.length) % fotosGaleria.length;
        seleccionarFotoGaleria(fotoActualIndex, fotosGaleria[fotoActualIndex]);
    }

    function abrirZoomModal() {
        if (!fotosGaleria || fotosGaleria.length === 0) return;
        const modal = document.getElementById('modalZoom');
        const modalImg = document.getElementById('modalZoomImage');
        modalImg.src = fotosGaleria[fotoActualIndex] || '';
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function cerrarZoomModal() {
        const modal = document.getElementById('modalZoom');
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // MODIFICADOR DE CANTIDAD
    function modificarCantidad(delta) {
        const input = document.getElementById('cantidadInput');
        if (!input) return;
        let valor = parseInt(input.value) || 1;
        valor += delta;
        if (valor < 1) valor = 1;
        if (valor > maxStock) valor = maxStock;
        input.value = valor;
    }

    // AÑADIR AL CARRITO
    function ejecutarAgregarCarrito(callbackAfter) {
        const btn = document.getElementById('btnAddToCart');
        const cantidad = parseInt(document.getElementById('cantidadInput')?.value) || 1;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        let originalContent = '';
        if (btn) {
            originalContent = btn.innerHTML;
            btn.innerHTML = '<span class="material-symbols-outlined text-[20px] animate-spin">sync</span> Procesando...';
            btn.disabled = true;
        }

        fetch("{{ route('carrito.add') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json"
            },
            body: JSON.stringify({ producto_id: {{ $producto->id }}, cantidad: cantidad })
        })
        .then(res => {
            if (res.status === 401) {
                window.location.href = "{{ route('login') }}";
                return Promise.reject('Requiere autenticación');
            }
            return res.json();
        })
        .then(data => {
            if (data && data.status === 'success') {
                mostrarToastDetalle('¡Producto agregado al carrito con éxito!', 'success');
                
                // Actualizar badge de carrito si existe en el header
                const badge = document.getElementById('cart-count-badge');
                const badgeContainer = document.getElementById('cart-badge-container');
                if (badge && badgeContainer) {
                    badge.innerText = data.cart_count;
                    badgeContainer.classList.remove('hidden');
                    badge.classList.add('animate-bounce');
                    setTimeout(() => badge.classList.remove('animate-bounce'), 1000);
                }

                if (typeof callbackAfter === 'function') {
                    callbackAfter();
                }
            } else {
                mostrarToastDetalle(data.message || 'Error al agregar producto', 'error');
            }
        })
        .catch(err => {
            if (err !== 'Requiere autenticación') {
                console.error(err);
                mostrarToastDetalle('Error de conexión al agregar al carrito', 'error');
            }
        })
        .finally(() => {
            if (btn) {
                btn.innerHTML = originalContent;
                btn.disabled = false;
            }
        });
    }

    function comprarAhora() {
        ejecutarAgregarCarrito(() => {
            window.location.href = "{{ route('checkout.index') }}";
        });
    }

    // TOGGLE FAVORITO
    function toggleFavoritoDetalle(productoId) {
        const btn = document.getElementById('btnFavoritoDetalle');
        const icono = document.getElementById('iconoFavoritoDetalle');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        btn.disabled = true;

        fetch("{{ route('favoritos.toggle') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json"
            },
            body: JSON.stringify({ producto_id: productoId })
        })
        .then(res => {
            if (res.status === 401) {
                mostrarToastDetalle('Inicia sesión para guardar favoritos', 'error');
                setTimeout(() => { window.location.href = "{{ route('login') }}"; }, 1200);
                return Promise.reject('Requiere login');
            }
            return res.json();
        })
        .then(data => {
            if (data && data.status === 'success') {
                if (data.is_favorite) {
                    btn.className = 'size-11 rounded-2xl flex items-center justify-center transition-all border bg-rose-50 text-rose-500 border-rose-200 shadow-sm';
                    icono.className = 'material-symbols-outlined text-[24px] fill-1 text-rose-500';
                    mostrarToastDetalle(data.message || 'Añadido a tus favoritos', 'success');
                } else {
                    btn.className = 'size-11 rounded-2xl flex items-center justify-center transition-all border bg-white text-slate-400 border-slate-200 hover:text-rose-500 hover:border-rose-200';
                    icono.className = 'material-symbols-outlined text-[24px]';
                    mostrarToastDetalle(data.message || 'Eliminado de tus favoritos', 'info');
                }
            } else {
                mostrarToastDetalle(data.message || 'Error al actualizar', 'error');
            }
        })
        .catch(err => {
            if (err !== 'Requiere login') console.error(err);
        })
        .finally(() => {
            btn.disabled = false;
        });
    }

    // TOAST NOTIFICACIÓN
    function mostrarToastDetalle(mensaje, tipo) {
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
            toast.classList.remove('translate-y-20', 'opacity-0');
        });

        setTimeout(() => {
            toast.classList.add('translate-y-20', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
</script>
@endpush
@endsection
