@extends('layouts.admin')
@section('title', 'Terminal POS - Agrosuministros Rodríguez')

@section('content')
<div class="bg-gray-100 flex h-screen w-screen font-sans overflow-hidden select-none">
    
    {{-- BARRA LATERAL FIJA --}}
    <aside class="w-16 bg-[#0b1d38] flex flex-col items-center py-4 border-r border-slate-800 shrink-0 z-30 justify-between">
        <a href="{{ route('admin.dashboard') }}" class="w-10 h-10 rounded-xl bg-white/10 text-white flex items-center justify-center hover:bg-[#1e549e] transition-colors" title="Volver al Dashboard">
            <span class="material-symbols-outlined text-[22px]">dashboard</span>
        </a>
        <button onclick="abrirModalCierre()" class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-400 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-colors shadow-xs" title="Cerrar Turno / Arqueo de Caja">
            <span class="material-symbols-outlined text-[22px]">lock</span>
        </button>
    </aside>

    {{-- CONTENEDOR PRINCIPAL: SIEMPRE EN FILA (2 COLUMNAS) --}}
    <main class="flex-1 flex flex-row min-w-0 h-full overflow-hidden relative">
        
        {{-- COLUMNA IZQUIERDA: CARRITO / TICKET (ANCHO FIJO) --}}
        <section class="bg-white flex flex-col h-full border-r border-gray-200 shadow-md shrink-0 z-20" style="width: 380px; min-width: 340px; max-width: 420px; flex-shrink: 0;">
            
            {{-- Encabezado del Ticket y Tasa BCV --}}
            <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50/80 shrink-0">
                <div>
                    <h2 class="font-black text-agro-dark flex items-center gap-1.5 text-base leading-tight">
                        <span class="material-symbols-outlined text-primary text-[22px]">receipt_long</span> Ticket de Venta
                    </h2>
                    <p class="text-[10px] text-gray-700 font-bold uppercase mt-0.5">Tasa BCV: Bs. <span id="tasa_bcv">{{ number_format($valorTasa, 2) }}</span></p>
                </div>
                <button onclick="vaciarCarrito()" class="text-rose-500 hover:bg-rose-50 p-1.5 rounded-lg transition-colors" title="Vaciar Ticket">
                    <span class="material-symbols-outlined text-[20px]">delete_sweep</span>
                </button>
            </div>

            {{-- BARRA DE CLIENTE --}}
            <div class="px-3.5 py-2 bg-slate-50 border-b border-gray-100 shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 truncate">
                        <span class="material-symbols-outlined text-teal-600 text-[18px] shrink-0">person</span>
                        <div class="truncate">
                            <p class="text-[11px] font-black text-gray-800 truncate" id="label_cliente_nombre">Consumidor Final</p>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <p class="text-[9.5px] font-mono text-gray-700 font-bold" id="label_cliente_doc">V-00000000</p>
                                <span id="badge_cliente_email" class="hidden text-[8.5px] font-bold text-teal-700 bg-teal-50 border border-teal-200/80 px-1.5 py-0.2 rounded items-center gap-0.5 truncate max-w-[160px]" title="Factura digital por correo habilitada">
                                    <span class="material-symbols-outlined text-[11px]">mark_email_read</span>
                                    <span id="label_cliente_email_txt" class="truncate"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <button type="button" onclick="togglePanelCliente()" class="px-2 py-1 text-[10px] font-bold text-teal-700 hover:text-teal-800 bg-teal-50 hover:bg-teal-100 rounded-lg transition-colors flex items-center gap-1 shrink-0">
                        <span class="material-symbols-outlined text-[13px]">edit</span>
                        <span id="btn_txt_cliente">Cambiar</span>
                    </button>
                </div>

                {{-- Panel Desplegable para Buscar o Ingresar Cliente --}}
                <div id="panel_cliente" class="hidden mt-2 pt-2 border-t border-slate-200 space-y-2">
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-2.5 top-1.5 text-gray-400 text-[16px]">search</span>
                        <input type="text" id="buscar_cliente_input" oninput="buscarClienteAutocompletar(this.value)" placeholder="Buscar cliente registrado o factura..." class="w-full h-7 pl-8 pr-3 text-xs rounded-lg border border-gray-200 bg-white focus:border-teal-500 outline-none">
                        <div id="cliente_sugerencias" class="hidden absolute top-8 left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-lg z-30 max-h-36 overflow-y-auto"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <div class="flex items-center justify-between mb-0.5">
                                <label class="block text-[8.5px] font-black text-gray-700 uppercase">Cédula / RIF</label>
                            </div>
                            <div class="relative">
                                <input type="text" id="input_cliente_doc" onblur="consultarCneAuto()" onkeydown="if(event.key==='Enter'){event.preventDefault(); consultarCne(true);}" placeholder="V-12345678" class="w-full h-7 pl-2 pr-7 text-xs rounded-lg border border-gray-200 bg-white focus:border-teal-500 outline-none font-bold">
                                <button type="button" id="btn_buscar_cne" onclick="consultarCne(true)" title="Consultar en CNE o BD" class="absolute right-1 top-1 w-5 h-5 flex items-center justify-center text-teal-600 hover:text-teal-800 hover:bg-teal-50 rounded transition-colors">
                                    <span class="material-symbols-outlined text-[15px]" id="cne_btn_icon">search</span>
                                </button>
                            </div>
                            <div id="cne_status_msg" class="text-[8.5px] mt-0.5 hidden truncate leading-tight"></div>
                        </div>
                        <div>
                            <label class="block text-[8.5px] font-black text-gray-700 uppercase mb-0.5">Nombre / Razón Social</label>
                            <input type="text" id="input_cliente_nombre" onkeydown="if(event.key==='Enter'){event.preventDefault(); guardarDatosCliente();}" placeholder="Nombre completo" class="w-full h-7 px-2 text-xs rounded-lg border border-gray-200 bg-white focus:border-teal-500 outline-none font-bold">
                        </div>
                    </div>

                    {{-- Campo de Correo Electrónico para Factura Digital SMTP --}}
                    <div>
                        <div class="flex items-center justify-between mb-0.5">
                            <label class="block text-[8.5px] font-black text-gray-700 uppercase">Correo Electrónico</label>
                            <span class="text-[8px] font-bold text-teal-600 flex items-center gap-0.5">
                                <span class="material-symbols-outlined text-[10px]">mark_email_read</span> Ticket Digital SMTP
                            </span>
                        </div>
                        <div class="relative">
                            <input type="email" id="input_cliente_email" onkeydown="if(event.key==='Enter'){event.preventDefault(); guardarDatosCliente();}" placeholder="cliente@correo.com (opcional)" class="w-full h-7 pl-7 pr-2 text-xs rounded-lg border border-gray-200 bg-white focus:border-teal-500 outline-none">
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-0.5">
                        <button type="button" onclick="restablecerConsumidorFinal()" class="px-2 py-0.5 text-[9.5px] font-bold text-gray-500 hover:bg-gray-200 rounded transition-colors">Consumidor Final</button>
                        <button type="button" onclick="guardarDatosCliente()" class="px-3 py-1 text-[10px] font-black text-white bg-teal-600 hover:bg-teal-700 rounded-lg shadow-2xs transition-colors flex items-center gap-1">
                            <span class="material-symbols-outlined text-[13px]">check</span> Aplicar
                        </button>
                    </div>
                </div>
            </div>

            {{-- Contenedor de Items del Ticket --}}
            <div class="flex-1 overflow-y-auto custom-scrollbar p-2 bg-gray-50/40" id="cart-container">
                <div id="empty-cart-msg" class="flex flex-col items-center justify-center h-full text-gray-300 py-10">
                    <span class="material-symbols-outlined text-5xl mb-2 text-gray-300">shopping_cart</span>
                    <p class="text-sm font-bold text-gray-400">El ticket está vacío</p>
                    <p class="text-xs text-gray-700 mt-1">Haz clic en un producto o escanea su código</p>
                </div>
            </div>

            {{-- TOTALES Y BOTÓN COBRAR --}}
            <div class="p-3.5 border-t border-gray-200 bg-white shadow-[0_-10px_20px_rgba(0,0,0,0.02)] shrink-0">
                <div class="flex justify-between items-center mb-1 text-xs">
                    <span class="text-gray-500 font-bold">Subtotal Neto</span>
                    <span class="font-bold text-gray-700" id="subtotal_usd_ui">$0.00</span>
                </div>
                <div class="flex justify-between items-center mb-1 text-xs">
                    <span class="text-gray-500 font-bold">IVA ({{ number_format($porcentajeIva, 0) }}%)</span>
                    <span class="font-bold text-gray-700" id="iva_usd_ui">$0.00</span>
                </div>
                <div class="flex justify-between items-center mb-2 text-xs hidden" id="fila_recargo_ui">
                    <span class="text-amber-700 font-bold flex items-center gap-1" id="label_recargo_ui">
                        <span class="material-symbols-outlined text-[13px]">percent</span>
                        <span>Ajuste Modalidad</span>
                    </span>
                    <span class="font-bold text-amber-800" id="recargo_usd_ui">+$0.00</span>
                </div>
                <div class="flex justify-between items-end border-t border-dashed border-gray-200 pt-2 mb-3">
                    <span class="text-base font-black text-agro-dark uppercase tracking-wider">Total</span>
                    <div class="text-right">
                        <p class="text-2xl font-black text-primary leading-none" id="total_usd_ui">$0.00</p>
                        <p class="text-xs font-bold text-gray-500 mt-0.5" id="total_ves_ui">Bs. 0.00</p>
                    </div>
                </div>
                <button onclick="abrirModalCobro()" id="btnCobrar" disabled class="w-full h-11 rounded-xl font-black text-white bg-primary hover:bg-secondary disabled:bg-gray-300 disabled:cursor-not-allowed shadow-md shadow-primary/20 transition-all flex items-center justify-center gap-2 text-base active:scale-95">
                    <span class="material-symbols-outlined text-[20px]">payments</span> COBRAR (F9)
                </button>
            </div>
        </section>

        {{-- COLUMNA DERECHA: BÚSQUEDA Y CATÁLOGO DE PRODUCTOS (FLEX-1) --}}
        <section class="flex-1 min-w-0 flex flex-col h-full bg-slate-100/70 overflow-hidden" style="flex: 1; min-width: 0;">
            
            {{-- Barra de Modalidad de Pago y Divisa en Vivo --}}
            <div class="px-3.5 py-2 bg-gradient-to-r from-[#0b1d38] via-[#12315e] to-[#1e549e] border-b border-[#3492cf]/30 shrink-0 flex flex-wrap items-center justify-between gap-2 text-white shadow-2xs">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-[10px] font-black uppercase tracking-wider text-[#aad6f3] flex items-center gap-1 shrink-0">
                        <span class="material-symbols-outlined text-[16px] text-teal-300">price_change</span>
                        Modalidad de Pago:
                    </span>
                    <div class="inline-flex p-0.5 bg-black/30 backdrop-blur-xs rounded-xl border border-white/10 shrink-0 shadow-inner" id="pos_modalidad_group">
                        <button type="button" onclick="cambiarModalidadPos('divisas')" id="btn_mod_pos_divisas" class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 bg-emerald-500 text-white shadow-sm" title="Precios en Divisas / Dólares (Alt+1)">
                            <span>💵 Divisas (USD)</span>
                            <span class="text-[9.5px] opacity-90 font-mono font-black">({{ (!empty($recargosPos['divisas']['porcentaje']) && $recargosPos['divisas']['porcentaje'] > 0) ? '+' . $recargosPos['divisas']['porcentaje'] . '%' : '0%' }})</span>
                        </button>
                        <button type="button" onclick="cambiarModalidadPos('bolivares')" id="btn_mod_pos_bolivares" class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 text-slate-300 hover:text-white hover:bg-white/10" title="Precios en Bolívares / Pago Móvil / Punto de Venta (Alt+2)">
                            <span>🇻🇪 Bolívares (VES)</span>
                            <span class="text-[9.5px] font-mono font-black {{ (!empty($recargosPos['bolivares']['porcentaje']) && $recargosPos['bolivares']['porcentaje'] > 0) ? 'text-amber-300' : 'opacity-90' }}">({{ (!empty($recargosPos['bolivares']['porcentaje']) && $recargosPos['bolivares']['porcentaje'] > 0) ? '+' . $recargosPos['bolivares']['porcentaje'] . '%' : '0%' }})</span>
                        </button>
                        <button type="button" onclick="cambiarModalidadPos('cashea')" id="btn_mod_pos_cashea" class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 text-slate-300 hover:text-white hover:bg-white/10" title="Precios Cashea Financiado (Alt+3)">
                            <span>🛍️ Cashea</span>
                            <span class="text-[9.5px] font-mono font-black {{ (!empty($recargosPos['cashea']['porcentaje']) && $recargosPos['cashea']['porcentaje'] > 0) ? 'text-amber-300' : 'opacity-90' }}">({{ (!empty($recargosPos['cashea']['porcentaje']) && $recargosPos['cashea']['porcentaje'] > 0) ? '+' . $recargosPos['cashea']['porcentaje'] . '%' : '0%' }})</span>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 text-xs font-bold shrink-0">
                    <span class="text-[10px] text-[#aad6f3] hidden md:inline-flex items-center gap-1">
                        <span class="material-symbols-outlined text-[13px] text-teal-300">bolt</span>
                        Precios ajustados en tiempo real
                    </span>
                    <span class="px-2.5 py-1 bg-black/25 rounded-lg text-[11px] font-mono font-bold text-teal-300 border border-teal-400/20 shadow-inner">
                        BCV: Bs. {{ number_format($valorTasa, 2) }}
                    </span>
                </div>
            </div>

            {{-- Barra de Búsqueda --}}
            <div class="p-3 bg-white border-b border-gray-200 shadow-2xs shrink-0 flex items-center gap-2">
                <div class="relative flex-1">
                    <span class="material-symbols-outlined absolute left-3.5 top-2.5 text-gray-400 text-[19px] pointer-events-none">search</span>
                    <input type="text" id="pos-search" autofocus autocomplete="off" placeholder="Buscar producto por nombre, SKU o código..." class="w-full h-10 pl-10 pr-14 rounded-xl bg-gray-100 border border-transparent focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-sm font-bold text-agro-dark outline-none placeholder-gray-400">
                    <kbd class="hidden sm:inline-block absolute right-9 top-2.5 px-1.5 py-0.5 text-[10px] font-bold text-gray-400 bg-gray-200/60 rounded pointer-events-none">F2</kbd>
                    <button type="button" onclick="limpiarBuscador()" class="absolute right-2.5 top-2.5 text-gray-400 hover:text-gray-600 hidden" id="btn_limpiar_search" title="Limpiar búsqueda">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
                <button type="button" onclick="actualizarCatalogoProductos(false)" id="btn_sync_catalogo" class="h-10 w-10 flex items-center justify-center rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-primary transition-all shrink-0" title="Actualizar catálogo y stock en pantalla">
                    <span class="material-symbols-outlined text-[20px]" id="icon_sync_catalogo">sync</span>
                </button>
            </div>

            {{-- Barra de Filtro de Categorías --}}
            <div class="px-4 py-2 bg-white/80 border-b border-gray-200/80 shrink-0 flex items-center gap-2 overflow-x-auto custom-scrollbar">
                <button type="button" onclick="filtrarPorCategoria(null)" class="cat-pill active px-3 py-1 rounded-lg text-xs font-bold transition-all bg-[#0b1d38] text-white shrink-0" data-cat="all">
                    Todos
                </button>
                @if(isset($categorias) && count($categorias) > 0)
                    @foreach($categorias as $cat)
                        <button type="button" onclick="filtrarPorCategoria({{ $cat->id }})" class="cat-pill px-3 py-1 rounded-lg text-xs font-bold transition-all bg-gray-100 text-gray-700 hover:bg-gray-200 shrink-0" data-cat="{{ $cat->id }}">
                            {{ $cat->nombre }}
                        </button>
                    @endforeach
                @endif
            </div>

            {{-- Grid de Productos Precargado --}}
            <div class="flex-1 overflow-y-auto custom-scrollbar p-4">
                <div id="search-loader" class="hidden text-center py-10">
                    <span class="material-symbols-outlined animate-spin text-primary text-3xl">autorenew</span>
                    <p class="text-xs text-gray-700 font-bold mt-2">Buscando productos...</p>
                </div>

                <div id="products-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                    @if(isset($productosIniciales) && count($productosIniciales) > 0)
                        @foreach($productosIniciales as $prod)
                            @php
                                $precioBase = $prod->precio_oferta_usd ? (float)$prod->precio_oferta_usd : (float)$prod->precio_venta_usd;
                                $precioOriginalBase = (float)$prod->precio_venta_usd;
                                $tieneOferta = $prod->precio_oferta_usd && (float)$prod->precio_oferta_usd < $precioOriginalBase;
                                
                                // PRECIO FINAL PVP (con IVA incluido) y equivalencias
                                $precioFinalPvp = $prod->precio_final_pvp;
                                $precioRegularPvp = $prod->precio_regular_pvp;
                                $precioBsPvp = $valorTasa > 0 ? round($precioFinalPvp * $valorTasa, 2) : 0;

                                $imgUrl = $prod->imagen_url ? asset($prod->imagen_url) : null;
                                $catNombre = $prod->categoria ? $prod->categoria->nombre : 'General';
                                
                                $unidadRaw = $prod->unidad_medida ?: 'ud';
                                $unidadLower = strtolower(trim($unidadRaw));
                                $unidadLabel = match($unidadLower) {
                                    'unidad' => 'ud',
                                    'litro' => 'Lt',
                                    default => $unidadLower
                                };

                                $ventaMin = (float)($prod->venta_minima ?: 1);
                                $minimoTotal = $precioFinalPvp * $ventaMin;
                                $stockTotal = (float)$prod->stock_total;
                                $esPocoStock = $stockTotal <= ($prod->stock_minimo_alerta ?: 5);
                            @endphp
                            <div id="product-card-{{ $prod->id }}" data-id="{{ $prod->id }}" data-pvp="{{ $precioFinalPvp }}" data-regular="{{ $precioRegularPvp }}" data-base="{{ $precioBase }}" onclick='agregarAlCarrito(@json($prod))' class="product-item-card bg-white rounded-2xl border border-gray-200 hover:border-teal-500 shadow-2xs hover:shadow-md cursor-pointer transition-all active:scale-95 flex flex-col h-full overflow-hidden group relative">
                                {{-- Imagen y Badges Flotantes --}}
                                <div class="h-24 bg-gray-50 flex items-center justify-center p-2 border-b border-gray-100 group-hover:bg-teal-50/30 transition-colors relative">
                                    @if($imgUrl)
                                        <img src="{{ $imgUrl }}" class="w-full h-full object-cover mix-blend-multiply" alt="{{ $prod->nombre }}">
                                    @else
                                        <span class="material-symbols-outlined text-3xl text-gray-300">image</span>
                                    @endif

                                    @if($tieneOferta)
                                        <span class="absolute top-1.5 left-1.5 bg-rose-600 text-white text-[9px] font-black px-1.5 py-0.5 rounded shadow-2xs">
                                            OFERTA
                                        </span>
                                    @endif

                                    <span id="product-stock-badge-{{ $prod->id }}" class="stock-badge absolute bottom-1.5 right-1.5 {{ $esPocoStock ? 'bg-amber-100 text-amber-900 border-amber-300' : 'bg-white/95 text-emerald-800 border-emerald-200' }} border text-[9px] font-black px-1.5 py-0.5 rounded-md shadow-2xs backdrop-blur-xs">
                                        {{ $stockTotal }} {{ $unidadLabel }} disp.
                                    </span>
                                </div>

                                {{-- Contenido Informativo del Producto --}}
                                <div class="p-2.5 flex flex-col flex-1 justify-between">
                                    <div>
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5 truncate">{{ $catNombre }}</p>
                                        <h4 class="font-bold text-agro-dark text-xs leading-snug mb-1 line-clamp-2" title="{{ $prod->nombre }}">{{ $prod->nombre }}</h4>
                                    </div>

                                    <div>
                                        {{-- Indicador destacado si la venta mínima es distinta a 1 --}}
                                        @if($ventaMin != 1)
                                            <div class="mb-1.5 px-2 py-0.5 bg-amber-50 border border-amber-200 rounded-lg flex items-center justify-between text-[10px] text-amber-900 font-bold" title="Venta mínima requerida">
                                                <span class="flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[13px] text-amber-600">scale</span>
                                                    Mín: {{ $ventaMin }} {{ $unidadLabel }}
                                                </span>
                                                <span class="font-black text-amber-800">${{ number_format($minimoTotal, 2) }}</span>
                                            </div>
                                        @endif

                                        {{-- Precios y Unidades: Precio Final (PVP) Destacado --}}
                                        <div class="flex justify-between items-end pt-1 border-t border-gray-100">
                                            <div>
                                                @if($tieneOferta)
                                                    <span class="line-through text-gray-400 text-[10px] font-bold block">${{ number_format($precioRegularPvp, 2) }}</span>
                                                @endif
                                                <div class="flex items-baseline gap-0.5">
                                                    <span class="card-precio-usd font-black {{ $tieneOferta ? 'text-rose-600' : 'text-primary' }} text-base leading-none">${{ number_format($precioFinalPvp, 2) }}</span>
                                                    <span class="text-[10px] font-bold text-gray-500">/ {{ $unidadLabel }}</span>
                                                </div>
                                                <div class="flex items-center gap-1 mt-0.5 flex-wrap">
                                                    <span class="text-[8.5px] font-extrabold px-1.5 py-0.2 rounded {{ $prod->precio_incluye_iva ? 'bg-teal-50 text-teal-700 border border-teal-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}" title="Base imponible: ${{ number_format($precioBase, 2) }}">
                                                        {{ $prod->precio_incluye_iva ? 'IVA inc.' : 'PVP' }}
                                                    </span>
                                                    <span class="card-mod-badge text-[8.5px] font-extrabold px-1.5 py-0.2 rounded hidden"></span>
                                                    <span class="text-[8.5px] font-semibold text-gray-400">Base: ${{ number_format($precioBase, 2) }}</span>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <span class="card-precio-bs text-xs font-black text-gray-700 block">Bs. {{ number_format($precioBsPvp, 2) }}</span>
                                                <span class="text-[8.5px] font-medium text-gray-400">/ {{ $unidadLabel }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </section>

    </main>
</div>

{{-- ========================================== --}}
{{-- MODAL DE COBRO (CHECKOUT MEJORADO)        --}}
{{-- ========================================== --}}
<div id="checkoutModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-xs transition-opacity opacity-0" id="checkoutBackdrop" onclick="cerrarModalCobro()"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-center p-3 sm:p-4">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-lg max-h-[95vh] flex flex-col opacity-0 scale-95" id="checkoutPanel">
            
            <div class="px-5 py-3.5 border-b border-gray-100 flex justify-between items-center bg-agro-dark text-white shrink-0">
                <h3 class="text-base font-black flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">point_of_sale</span> Procesar Cobro
                </h3>
                <button type="button" onclick="cerrarModalCobro()" class="text-gray-700 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="p-5 overflow-y-auto custom-scrollbar flex-1 space-y-3.5">
                
                {{-- Banner Resumen Financiero --}}
                <div class="flex justify-between items-center bg-slate-50 p-3.5 rounded-2xl border border-slate-200">
                    <div>
                        <p class="text-[9.5px] font-black text-gray-700 uppercase tracking-widest">Total a Cobrar</p>
                        <p class="text-2xl font-black text-agro-dark leading-none mt-1" id="checkout_total_usd">$0.00</p>
                        <p class="text-[10px] font-bold text-amber-700 mt-1 hidden" id="checkout_recargo_badge">+0.0% recargo</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[9.5px] font-black text-gray-700 uppercase tracking-widest">Equivalente BCV</p>
                        <p class="text-lg font-black text-primary mt-0.5" id="checkout_total_ves">Bs. 0.00</p>
                        <p class="text-[9px] text-gray-700 font-bold mt-0.5">Tasa: Bs. {{ number_format($valorTasa, 2) }}</p>
                    </div>
                </div>

                {{-- Selector de Modo de Pago: Pago Único vs Pago Mixto --}}
                <div class="flex bg-gray-100 p-1 rounded-xl">
                    <button type="button" onclick="cambiarModoPago('unico')" id="tab_modo_unico" class="flex-1 py-1.5 text-xs font-black rounded-lg transition-all bg-white text-agro-dark shadow-xs flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined text-[15px]">payments</span> Pago Único
                    </button>
                    <button type="button" onclick="cambiarModoPago('mixto')" id="tab_modo_mixto" class="flex-1 py-1.5 text-xs font-black rounded-lg transition-all text-gray-500 hover:text-agro-dark flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined text-[15px]">call_split</span> Pago Mixto / Dividido
                    </button>
                </div>

                {{-- SECCIÓN A: PAGO ÚNICO --}}
                <div id="seccion_pago_unico" class="space-y-3.5">
                    <div>
                        <label class="block text-[9.5px] font-black text-gray-700 uppercase tracking-widest mb-1.5 ml-1">Método de Pago</label>
                        <select id="metodo_pago" onchange="actualizarMetodoPagoUnico()" class="w-full h-10 px-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-primary focus:bg-white outline-none font-bold text-agro-dark text-xs">
                            <option value="efectivo_usd" data-modalidad="divisas">💵 Efectivo Dólares (USD){{ (!empty($recargosPos['divisas']['porcentaje']) && $recargosPos['divisas']['porcentaje'] > 0) ? ' (+' . $recargosPos['divisas']['porcentaje'] . '%)' : '' }}</option>
                            <option value="efectivo_bs" data-modalidad="bolivares">💵 Efectivo Bolívares (VES){{ (!empty($recargosPos['bolivares']['porcentaje']) && $recargosPos['bolivares']['porcentaje'] > 0) ? ' (+' . $recargosPos['bolivares']['porcentaje'] . '%)' : '' }}</option>
                            <option value="pago_movil" data-modalidad="bolivares">📱 Pago Móvil{{ (!empty($recargosPos['bolivares']['porcentaje']) && $recargosPos['bolivares']['porcentaje'] > 0) ? ' (+' . $recargosPos['bolivares']['porcentaje'] . '%)' : '' }}</option>
                            <option value="punto_venta" data-modalidad="bolivares">💳 Punto de Venta / Tarjeta{{ (!empty($recargosPos['bolivares']['porcentaje']) && $recargosPos['bolivares']['porcentaje'] > 0) ? ' (+' . $recargosPos['bolivares']['porcentaje'] . '%)' : '' }}</option>
                            <option value="zelle" data-modalidad="divisas">⚡ Zelle{{ (!empty($recargosPos['divisas']['porcentaje']) && $recargosPos['divisas']['porcentaje'] > 0) ? ' (+' . $recargosPos['divisas']['porcentaje'] . '%)' : '' }}</option>
                            <option value="binance" data-modalidad="divisas">🟡 Binance Pay{{ (!empty($recargosPos['divisas']['porcentaje']) && $recargosPos['divisas']['porcentaje'] > 0) ? ' (+' . $recargosPos['divisas']['porcentaje'] . '%)' : '' }}</option>
                            <option value="transferencia" data-modalidad="bolivares">🏦 Transferencia Bancaria{{ (!empty($recargosPos['bolivares']['porcentaje']) && $recargosPos['bolivares']['porcentaje'] > 0) ? ' (+' . $recargosPos['bolivares']['porcentaje'] . '%)' : '' }}</option>
                            <option value="cashea" data-modalidad="cashea">🛍️ Cashea (Financiado){{ (!empty($recargosPos['cashea']['porcentaje']) && $recargosPos['cashea']['porcentaje'] > 0) ? ' (+' . $recargosPos['cashea']['porcentaje'] . '%)' : '' }}</option>
                        </select>
                    </div>

                    {{-- Calculadora de Vuelto para Efectivo --}}
                    <div id="caja_calculadora_vuelto" class="bg-emerald-50/60 border border-emerald-200 rounded-2xl p-3.5 space-y-2.5">
                        <div class="flex justify-between items-center">
                            <label class="block text-[10px] font-black text-emerald-800 uppercase tracking-wider">
                                Calculadora de Cambio / Vuelto
                            </label>
                            <span class="text-[9.5px] text-emerald-700 font-bold" id="label_moneda_recibida">Moneda: USD ($)</span>
                        </div>

                        <div class="relative">
                            <span class="absolute left-3 top-2 font-bold text-emerald-800 text-sm" id="simbolo_recibido">$</span>
                            <input type="number" step="0.01" id="monto_recibido_input" oninput="calcularVuelto()" placeholder="Monto entregado por el cliente" class="w-full h-10 pl-8 pr-3 text-sm font-black text-emerald-900 rounded-xl bg-white border border-emerald-300 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                        </div>

                        {{-- Botones Rápidos de Billetes --}}
                        <div class="flex flex-wrap gap-1" id="botones_billetes">
                            <button type="button" onclick="setBilletesRapido('exacto')" class="px-2 py-0.5 bg-white hover:bg-emerald-100 border border-emerald-300 rounded text-[11px] font-bold text-emerald-800">Exacto</button>
                            <button type="button" onclick="setBilletesRapido(5)" class="px-2 py-0.5 bg-white hover:bg-emerald-100 border border-emerald-300 rounded text-[11px] font-bold text-emerald-800">$5</button>
                            <button type="button" onclick="setBilletesRapido(10)" class="px-2 py-0.5 bg-white hover:bg-emerald-100 border border-emerald-300 rounded text-[11px] font-bold text-emerald-800">$10</button>
                            <button type="button" onclick="setBilletesRapido(20)" class="px-2 py-0.5 bg-white hover:bg-emerald-100 border border-emerald-300 rounded text-[11px] font-bold text-emerald-800">$20</button>
                            <button type="button" onclick="setBilletesRapido(50)" class="px-2 py-0.5 bg-white hover:bg-emerald-100 border border-emerald-300 rounded text-[11px] font-bold text-emerald-800">$50</button>
                            <button type="button" onclick="setBilletesRapido(100)" class="px-2 py-0.5 bg-white hover:bg-emerald-100 border border-emerald-300 rounded text-[11px] font-bold text-emerald-800">$100</button>
                        </div>

                        {{-- Resultado Dinámico de Vuelto --}}
                        <div id="resultado_vuelto_box" class="p-2.5 bg-white rounded-xl border border-emerald-200 flex justify-between items-center">
                            <span class="text-xs font-bold text-emerald-800" id="vuelto_estado_texto">Vuelto a entregar:</span>
                            <div class="text-right">
                                <p class="text-base font-black text-emerald-600 leading-none" id="vuelto_usd_texto">$0.00</p>
                                <p class="text-[9.5px] text-emerald-700 font-bold mt-0.5" id="vuelto_ves_texto">Bs. 0.00</p>
                            </div>
                        </div>
                    </div>

                    {{-- Campo de Referencia para Pagos Digitales --}}
                    <div id="caja_referencia" class="hidden">
                        <label class="block text-[9.5px] font-black text-blue-600 uppercase tracking-widest mb-1.5 ml-1">Referencia Bancaria / Comprobante</label>
                        <div class="relative">
                            <input type="text" id="referencia_pago" class="w-full h-10 pl-9 pr-3 rounded-xl bg-blue-50/50 border border-blue-200 focus:border-blue-500 focus:bg-white text-xs font-bold text-agro-dark outline-none uppercase" placeholder="Ej: 12345678">
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN B: PAGO MIXTO / DIVIDIDO --}}
                <div id="seccion_pago_mixto" class="hidden space-y-3">
                    
                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200 text-xs">
                        <div class="flex justify-between font-bold mb-1">
                            <span class="text-gray-500">Cubierto: <strong class="text-emerald-600" id="mixto_pagado_txt">$0.00</strong></span>
                            <span class="text-gray-500">Pendiente: <strong class="text-amber-600" id="mixto_pendiente_txt">$0.00</strong></span>
                        </div>
                        <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div id="barra_progreso_mixto" class="h-full bg-emerald-500 transition-all duration-300" style="width: 0%;"></div>
                        </div>
                    </div>

                    <div id="lista_pagos_mixtos" class="space-y-1.5 max-h-32 overflow-y-auto custom-scrollbar"></div>

                    <div class="p-2.5 bg-gray-50 rounded-xl border border-gray-200 space-y-2">
                        <p class="text-[9px] font-black text-gray-700 uppercase tracking-widest">Agregar Fracción de Pago</p>
                        
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <label class="block text-[9px] font-bold text-gray-500 mb-0.5">Método</label>
                                <select id="mixto_select_metodo" onchange="cambiarMetodoMixto()" class="w-full h-8 px-2 rounded-lg bg-white border border-gray-200 font-bold outline-none text-xs">
                                    <option value="efectivo_usd">Efectivo USD ($)</option>
                                    <option value="efectivo_bs">Efectivo Bs (VES)</option>
                                    <option value="pago_movil">Pago Móvil</option>
                                    <option value="punto_venta">Punto de Venta</option>
                                    <option value="zelle">Zelle</option>
                                    <option value="binance">Binance Pay</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="cashea">Cashea</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[9px] font-bold text-gray-500 mb-0.5">Monto USD ($)</label>
                                <input type="number" step="0.01" id="mixto_input_monto" placeholder="0.00" class="w-full h-8 px-2.5 rounded-lg bg-white border border-gray-200 font-black text-xs outline-none focus:border-teal-500">
                            </div>
                        </div>

                        <div id="mixto_caja_ref" class="hidden">
                            <label class="block text-[9px] font-bold text-blue-600 mb-0.5">Referencia</label>
                            <input type="text" id="mixto_input_ref" placeholder="Ej: REF-987654" class="w-full h-7 px-2.5 rounded-lg bg-white border border-blue-200 font-bold text-xs outline-none uppercase">
                        </div>

                        <button type="button" onclick="agregarLineaPagoMixto()" class="w-full h-8 bg-slate-800 hover:bg-slate-900 text-white rounded-lg font-black text-xs flex items-center justify-center gap-1">
                            <span class="material-symbols-outlined text-[15px]">add_circle</span> Añadir a la Venta
                        </button>
                    </div>

                </div>

            </div>

            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50 flex gap-2.5 shrink-0">
                <button type="button" onclick="cerrarModalCobro()" class="px-4 py-2 rounded-xl font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 text-xs">
                    Cancelar
                </button>
                <button type="button" onclick="procesarTransaccion()" id="btnConfirmarPago" class="btn-primary flex-1 h-11 rounded-xl justify-center text-sm shadow-md shadow-primary/20">
                    Confirmar Transacción
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{-- MODAL DE ÉXITO & IMPRESIÓN (PUNTO 1)      --}}
{{-- ========================================== --}}
<div id="ventaExitosaModal" class="fixed inset-0 z-[110] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-xs transition-opacity opacity-0" id="exitosaBackdrop"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-center p-4">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-center shadow-2xl transition-all w-full max-w-sm opacity-0 scale-95 p-5 flex flex-col items-center" id="exitosaPanel">
            
            <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mb-2.5">
                <span class="material-symbols-outlined text-3xl">check_circle</span>
            </div>

            <h3 class="text-lg font-black text-gray-900 leading-tight">¡Venta Exitosa!</h3>
            <p class="text-xs text-gray-500 mt-0.5">Inventario descontado y factura emitida</p>

            <div class="w-full bg-gray-50 rounded-2xl p-3.5 my-3 border border-gray-100 text-left text-xs space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-gray-500 font-bold">Factura Fiscal:</span>
                    <strong class="text-gray-800" id="modal_exito_factura">F-0000000</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 font-bold">Cliente:</span>
                    <strong class="text-gray-800 truncate max-w-[170px]" id="modal_exito_cliente">Consumidor Final</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 font-bold">Total Cobrado:</span>
                    <strong class="text-emerald-700 font-black text-sm" id="modal_exito_monto">$0.00</strong>
                </div>
                <div class="flex justify-between text-emerald-800" id="modal_exito_vuelto_row">
                    <span class="font-bold">Vuelto Entregado:</span>
                    <strong class="font-black" id="modal_exito_vuelto">$0.00</strong>
                </div>
                <div class="flex justify-between items-center text-xs pt-1.5 border-t border-gray-200" id="modal_exito_correo_row">
                    <span class="text-gray-500 font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px] text-teal-600">mark_email_read</span>
                        Ticket Digital:
                    </span>
                    <span class="font-bold text-teal-700 truncate max-w-[180px] text-[10.5px]" id="modal_exito_correo_txt">Sin correo</span>
                </div>
            </div>

            {{-- Opción para Enviar / Reenviar copia por correo al instante --}}
            <div class="w-full bg-slate-50 p-2.5 rounded-xl border border-slate-200 text-left mb-3 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-[9px] font-black text-slate-700 uppercase tracking-wider flex items-center gap-1">
                        <span class="material-symbols-outlined text-[13px] text-teal-600">send</span> Enviar Ticket a un correo
                    </span>
                    <span class="text-[8.5px] text-gray-500 font-semibold" id="msg_correo_manual"></span>
                </div>
                <div class="flex gap-1.5">
                    <input type="email" id="input_reenvio_email" onkeydown="if(event.key==='Enter'){event.preventDefault(); enviarTicketCorreoManual();}" placeholder="correo@ejemplo.com" class="flex-1 h-7.5 px-2 text-xs rounded-lg border border-gray-200 bg-white focus:border-teal-500 outline-none">
                    <button type="button" onclick="enviarTicketCorreoManual()" id="btnEnviarCorreoManual" class="h-7.5 px-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-lg text-[10.5px] flex items-center gap-1 transition-colors shrink-0">
                        <span class="material-symbols-outlined text-[13px]">mail</span> Enviar
                    </button>
                </div>
            </div>

            <div class="w-full space-y-2">
                <button type="button" onclick="imprimirTicketDirecto()" id="btnImprimirTicketExito" class="w-full h-11 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-black text-sm flex items-center justify-center gap-2 shadow-md shadow-teal-600/30 transition-all hover:scale-[1.02] active:scale-95">
                    <span class="material-symbols-outlined text-[19px]">receipt</span> Imprimir Ticket (80mm) [Enter]
                </button>
                <div class="flex gap-2">
                    <button type="button" onclick="imprimirA4Directo()" class="flex-1 h-9 rounded-xl bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 font-bold text-xs flex items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-[15px]">print</span> Formato A4
                    </button>
                    <button type="button" onclick="cerrarModalExito()" class="flex-1 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs">
                        Nueva Venta (Esc)
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ========================================== --}}
{{-- MODAL CERRAR CAJA / ARQUEO Z              --}}
{{-- ========================================== --}}
<div id="cierreModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-xs transition-opacity opacity-0" id="cierreBackdrop" onclick="cerrarModalCierre()"></div>
    <div class="fixed inset-0 z-10 flex justify-center items-center p-3 sm:p-4">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-lg max-h-[92vh] opacity-0 scale-95 flex flex-col" id="cierrePanel">
            
            <div class="bg-red-600 p-4 text-white flex justify-between items-center shrink-0">
                <div class="flex items-center gap-2.5">
                    <span class="material-symbols-outlined text-2xl">lock_person</span>
                    <div>
                        <h3 class="text-base font-black leading-tight">Cierre de Turno / Arqueo</h3>
                        <p class="text-[9.5px] text-red-100 font-bold" id="cierre_cajero_txt">Cajero: Cargando...</p>
                    </div>
                </div>
                <button type="button" onclick="cerrarModalCierre()" class="text-white/80 hover:text-white">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="p-4 overflow-y-auto custom-scrollbar flex-1 space-y-3.5" id="cierre_body_content">
                <div id="cierre_loader" class="text-center py-8">
                    <span class="material-symbols-outlined animate-spin text-red-600 text-3xl">autorenew</span>
                    <p class="text-xs text-gray-500 font-bold mt-2">Calculando arqueo de turno...</p>
                </div>

                <div id="cierre_data_container" class="hidden space-y-3.5">
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="bg-slate-50 p-2 rounded-xl border border-slate-200">
                            <span class="text-[8.5px] font-black text-gray-700 uppercase">Fondo Base</span>
                            <p class="text-sm font-black text-slate-800" id="cierre_kpi_base">$0.00</p>
                        </div>
                        <div class="bg-slate-50 p-2 rounded-xl border border-slate-200">
                            <span class="text-[8.5px] font-black text-gray-700 uppercase">Ventas Sistema</span>
                            <p class="text-sm font-black text-emerald-600" id="cierre_kpi_ventas">$0.00</p>
                        </div>
                        <div class="bg-slate-50 p-2 rounded-xl border border-slate-200">
                            <span class="text-[8.5px] font-black text-gray-700 uppercase">Tickets</span>
                            <p class="text-sm font-black text-slate-800" id="cierre_kpi_tickets">0</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[9.5px] font-black text-gray-700 uppercase tracking-widest mb-1 ml-1">Desglose por Método de Pago</label>
                        <div class="bg-slate-50 border border-slate-200 rounded-xl overflow-hidden max-h-36 overflow-y-auto custom-scrollbar" id="cierre_tabla_metodos"></div>
                    </div>

                    <div class="bg-red-50/60 border border-red-200 rounded-2xl p-3 space-y-2.5">
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-bold text-red-900">Efectivo Físico Esperado en Gaveta:</span>
                            <span class="text-sm font-black text-red-700" id="cierre_efectivo_esperado_txt">$0.00</span>
                        </div>

                        <div>
                            <label class="block text-[9px] font-black text-gray-700 uppercase tracking-widest mb-1 ml-1">Efectivo Físico Contado Real (USD) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 font-bold text-gray-700 text-base">$</span>
                                <input type="number" step="0.01" id="dinero_fisico_usd" oninput="calcularDiferenciaArqueo()" class="w-full h-10 pl-8 pr-3 rounded-xl bg-white border border-red-300 focus:border-red-500 text-base font-black text-agro-dark outline-none" placeholder="0.00">
                            </div>
                        </div>

                        <div id="cierre_diferencia_box" class="p-2.5 bg-white rounded-xl border border-gray-200 flex justify-between items-center text-xs">
                            <span class="font-bold text-gray-700">Diferencia de Caja:</span>
                            <span class="font-black text-sm" id="cierre_diferencia_txt">$0.00</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[9px] font-black text-gray-700 uppercase tracking-widest mb-1 ml-1">Observaciones (Opcional)</label>
                        <textarea id="observaciones_cierre" rows="2" class="w-full p-2 rounded-xl bg-gray-50 border border-gray-200 focus:border-red-500 outline-none text-xs" placeholder="Novedades del turno, billetes deteriorados, etc."></textarea>
                    </div>
                </div>
            </div>

            <div class="px-4 py-3 border-t border-gray-100 bg-gray-50 flex justify-end gap-2 shrink-0">
                <button type="button" onclick="cerrarModalCierre()" class="px-3.5 py-1.5 rounded-xl font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 text-xs">Cancelar</button>
                <button type="button" onclick="ejecutarCierreCaja()" id="btnCerrarCaja" class="px-4 py-2 rounded-xl font-black text-white bg-red-600 hover:bg-red-700 shadow-sm text-xs transition-all flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[15px]">lock</span> Finalizar Turno
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@push('scripts')
<script>
    // ==========================================
    // VARIABLES GLOBALES
    // ==========================================
    let carrito = [];
    const tasaBcv = parseFloat({{ $valorTasa }});
    const ivaPorcentaje = parseFloat({{ $porcentajeIva }});
    const recargosPos = @json($recargosPos ?? []);
    let modalidadPosActual = 'divisas';
    let searchTimeout;
    let clienteSearchTimeout;
    let categoriaSeleccionada = null;

    let clienteActual = {
        documento: 'V-00000000',
        nombre: 'Consumidor Final',
        email: '',
        telefono: '',
        direccion: 'Compra en Mostrador POS'
    };

    let modoPagoActual = 'unico';
    let modalTotalUsdActual = 0;
    let modalTotalVesActual = 0;
    let pagosMixtos = [];
    let ultimaVentaExitosa = null;
    let ultimoResumenCierre = null;

    const searchInput = document.getElementById('pos-search');
    const btnLimpiarSearch = document.getElementById('btn_limpiar_search');
    const grid = document.getElementById('products-grid');
    const cartContainer = document.getElementById('cart-container');
    const emptyMsg = document.getElementById('empty-cart-msg');
    const btnCobrar = document.getElementById('btnCobrar');

    // ==========================================
    // GESTIÓN DE MODALIDAD Y PRECIOS PORCENTUALES
    // ==========================================
    function getPorcentajeModalidad(mod) {
        if (!recargosPos || !recargosPos[mod]) return 0;
        if (typeof recargosPos[mod] === 'object' && recargosPos[mod] !== null) {
            return parseFloat(recargosPos[mod].porcentaje) || 0;
        }
        return parseFloat(recargosPos[mod]) || 0;
    }

    function cambiarModalidadPos(nuevaMod) {
        modalidadPosActual = nuevaMod;

        // 1. Estilos visuales de los botones de modalidad
        const btnDivisas = document.getElementById('btn_mod_pos_divisas');
        const btnBs = document.getElementById('btn_mod_pos_bolivares');
        const btnCashea = document.getElementById('btn_mod_pos_cashea');

        if (btnDivisas) btnDivisas.className = 'px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 ' + (nuevaMod === 'divisas' ? 'bg-emerald-500 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-white/10');
        if (btnBs) btnBs.className = 'px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 ' + (nuevaMod === 'bolivares' ? 'bg-emerald-500 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-white/10');
        if (btnCashea) btnCashea.className = 'px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 ' + (nuevaMod === 'cashea' ? 'bg-emerald-500 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-white/10');

        // 2. Actualizar tarjetas visibles en el catálogo
        actualizarPreciosTarjetasModalidad();

        // 3. Recalcular carrito y totales del ticket
        renderizarCarrito();
    }

    function actualizarPreciosTarjetasModalidad() {
        const pct = getPorcentajeModalidad(modalidadPosActual);
        const factor = 1 + (pct / 100);

        document.querySelectorAll('.product-item-card').forEach(card => {
            const pvpBase = parseFloat(card.dataset.pvp) || 0;
            const pvpAjustado = pvpBase * factor;
            const bsAjustado = pvpAjustado * tasaBcv;

            const spanUsd = card.querySelector('.card-precio-usd');
            const spanBs = card.querySelector('.card-precio-bs');
            const badgeMod = card.querySelector('.card-mod-badge');

            if (spanUsd) spanUsd.innerText = `$${pvpAjustado.toFixed(2)}`;
            if (spanBs) spanBs.innerText = `Bs. ${bsAjustado.toFixed(2)}`;

            if (badgeMod) {
                if (pct > 0) {
                    badgeMod.innerText = `${modalidadPosActual === 'bolivares' ? 'VES' : 'CSH'} (+${pct}%)`;
                    badgeMod.className = 'card-mod-badge text-[8.5px] font-extrabold px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 border border-amber-300 inline-block';
                } else {
                    badgeMod.className = 'card-mod-badge hidden';
                }
            }
        });
    }

    // ==========================================
    // 1. FILTRADO & BÚSQUEDA DE PRODUCTOS
    // ==========================================
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        let query = this.value.trim();
        btnLimpiarSearch.classList.toggle('hidden', query.length === 0);
        searchTimeout = setTimeout(() => realizarBusqueda(query, false), 200);
    });

    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(searchTimeout);
            realizarBusqueda(this.value.trim(), true);
        }
    });

    function limpiarBuscador() {
        searchInput.value = '';
        btnLimpiarSearch.classList.add('hidden');
        realizarBusqueda('', false);
        searchInput.focus();
    }

    function filtrarPorCategoria(catId) {
        categoriaSeleccionada = catId;
        document.querySelectorAll('.cat-pill').forEach(btn => {
            const dataCat = btn.getAttribute('data-cat');
            if ((catId === null && dataCat === 'all') || dataCat == catId) {
                btn.className = 'cat-pill active px-3 py-1 rounded-lg text-xs font-bold transition-all bg-[#0b1d38] text-white shrink-0';
            } else {
                btn.className = 'cat-pill px-3 py-1 rounded-lg text-xs font-bold transition-all bg-gray-100 text-gray-700 hover:bg-gray-200 shrink-0';
            }
        });
        realizarBusqueda(searchInput.value.trim(), false);
    }

    function formatUnidad(u) {
        if (!u) return 'ud';
        let lower = u.toString().toLowerCase().trim();
        if (lower === 'unidad') return 'ud';
        if (lower === 'litro') return 'Lt';
        return lower;
    }

    function renderizarGridProductos(productos) {
        grid.innerHTML = '';
        const pctMod = getPorcentajeModalidad(modalidadPosActual);
        const factorMod = 1 + (pctMod / 100);

        if (productos && productos.length > 0) {
            productos.forEach(prod => {
                let precioBase = parseFloat(prod.precio_oferta_usd ? prod.precio_oferta_usd : prod.precio_venta_usd);
                let precioOriginalBase = parseFloat(prod.precio_venta_usd);
                let tieneOferta = prod.precio_oferta_usd && parseFloat(prod.precio_oferta_usd) < precioOriginalBase;
                
                let precioFinalPvp = parseFloat(prod.precio_final_pvp) || (precioBase * (1 + (ivaPorcentaje / 100)));
                let precioRegularPvp = parseFloat(prod.precio_regular_pvp) || (precioOriginalBase * (1 + (ivaPorcentaje / 100)));
                
                // Precios ajustados a la modalidad activa
                let pvpAjustado = precioFinalPvp * factorMod;
                let precioBsAjustado = pvpAjustado * tasaBcv;

                let imgUrl = prod.imagen_url ? (prod.imagen_url.startsWith('http') ? prod.imagen_url : `/${prod.imagen_url.replace(/^\//, '')}`) : '';
                let imgHtml = imgUrl 
                    ? `<img src="${imgUrl}" class="w-full h-full object-cover mix-blend-multiply" alt="${prod.nombre}">` 
                    : `<span class="material-symbols-outlined text-3xl text-gray-300">image</span>`;
                
                let catNombre = prod.categoria ? prod.categoria.nombre : 'General';
                let unidadLabel = formatUnidad(prod.unidad_medida);
                let ventaMin = parseFloat(prod.venta_minima) || 1;
                let minimoTotal = pvpAjustado * ventaMin;
                let stockTotal = parseFloat(prod.stock_total) || 0;
                let alertaStock = parseFloat(prod.stock_minimo_alerta) || 5;
                let esPocoStock = stockTotal <= alertaStock;

                let badgeOferta = tieneOferta 
                    ? `<span class="absolute top-1.5 left-1.5 bg-rose-600 text-white text-[9px] font-black px-1.5 py-0.5 rounded shadow-2xs">OFERTA</span>` 
                    : '';
                
                let badgeStock = `
                    <span id="product-stock-badge-${prod.id}" class="stock-badge absolute bottom-1.5 right-1.5 ${esPocoStock ? 'bg-amber-100 text-amber-900 border-amber-300' : 'bg-white/95 text-emerald-800 border-emerald-200'} border text-[9px] font-black px-1.5 py-0.5 rounded-md shadow-2xs backdrop-blur-xs">
                        ${stockTotal} ${unidadLabel} disp.
                    </span>
                `;

                let avisoVentaMin = (ventaMin !== 1) ? `
                    <div class="mb-1.5 px-2 py-0.5 bg-amber-50 border border-amber-200 rounded-lg flex items-center justify-between text-[10px] text-amber-900 font-bold" title="Venta mínima requerida">
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-[13px] text-amber-600">scale</span>
                            Mín: ${ventaMin} ${unidadLabel}
                        </span>
                        <span class="font-black text-amber-800">$${minimoTotal.toFixed(2)}</span>
                    </div>
                ` : '';

                let precioRegularHtml = tieneOferta 
                    ? `<span class="line-through text-gray-400 text-[10px] font-bold block">$${(precioRegularPvp * factorMod).toFixed(2)}</span>` 
                    : '';

                let badgeIvaHtml = prod.precio_incluye_iva 
                    ? `<span class="text-[8.5px] font-extrabold px-1.5 py-0.2 rounded bg-teal-50 text-teal-700 border border-teal-200" title="Base: $${precioBase.toFixed(2)}">IVA inc.</span>` 
                    : `<span class="text-[8.5px] font-extrabold px-1.5 py-0.2 rounded bg-slate-100 text-slate-600 border border-slate-200" title="Base: $${precioBase.toFixed(2)}">PVP</span>`;

                let badgeModHtml = (pctMod > 0)
                    ? `<span class="card-mod-badge text-[8.5px] font-extrabold px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 border border-amber-300 inline-block">${modalidadPosActual === 'bolivares' ? 'VES' : 'CSH'} (+${pctMod}%)</span>`
                    : `<span class="card-mod-badge text-[8.5px] font-extrabold px-1.5 py-0.2 rounded hidden"></span>`;

                let colorPrecio = tieneOferta ? 'text-rose-600' : 'text-primary';
                let prodJson = JSON.stringify(prod).replace(/'/g, "&apos;");

                grid.innerHTML += `
                    <div id="product-card-${prod.id}" data-id="${prod.id}" data-pvp="${precioFinalPvp}" data-regular="${precioRegularPvp}" data-base="${precioBase}" onclick='agregarAlCarrito(${prodJson})' class="product-item-card bg-white rounded-2xl border border-gray-200 hover:border-teal-500 shadow-2xs hover:shadow-md cursor-pointer transition-all active:scale-95 flex flex-col h-full overflow-hidden group relative">
                        <div class="h-24 bg-gray-50 flex items-center justify-center p-2 border-b border-gray-100 group-hover:bg-teal-50/30 transition-colors relative">
                            ${imgHtml}
                            ${badgeOferta}
                            ${badgeStock}
                        </div>
                        <div class="p-2.5 flex flex-col flex-1 justify-between">
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5 truncate">${catNombre}</p>
                                <h4 class="font-bold text-agro-dark text-xs leading-snug mb-1 line-clamp-2" title="${prod.nombre}">${prod.nombre}</h4>
                            </div>
                            <div>
                                ${avisoVentaMin}
                                <div class="flex justify-between items-end pt-1 border-t border-gray-100">
                                    <div>
                                        ${precioRegularHtml}
                                        <div class="flex items-baseline gap-0.5">
                                            <span class="card-precio-usd font-black ${colorPrecio} text-base leading-none">$${pvpAjustado.toFixed(2)}</span>
                                            <span class="text-[10px] font-bold text-gray-500">/ ${unidadLabel}</span>
                                        </div>
                                        <div class="flex items-center gap-1 mt-0.5 flex-wrap">
                                            ${badgeIvaHtml}
                                            ${badgeModHtml}
                                            <span class="text-[8.5px] font-semibold text-gray-400">Base: $${precioBase.toFixed(2)}</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="card-precio-bs text-xs font-black text-gray-700 block">Bs. ${precioBsAjustado.toFixed(2)}</span>
                                        <span class="text-[8.5px] font-medium text-gray-400">/ ${unidadLabel}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            grid.innerHTML = `<div class="col-span-full text-center text-gray-700 py-10 font-bold text-sm">No se encontraron productos coincidentes.</div>`;
        }
    }

    function actualizarStockTarjetasLocales(items) {
        if (!items || !items.length) return;
        items.forEach(item => {
            const card = document.getElementById(`product-card-${item.id}`);
            const badge = document.getElementById(`product-stock-badge-${item.id}`);
            if (card && badge) {
                const stock = parseFloat(item.stock_total);
                const unidad = formatUnidad(item.unidad_medida);
                const alerta = parseFloat(item.stock_minimo_alerta) || 5;

                badge.innerText = `${stock} ${unidad} disp.`;

                if (stock <= 0) {
                    badge.innerText = 'Agotado';
                    badge.className = 'stock-badge absolute bottom-1.5 right-1.5 bg-rose-100 text-rose-800 border-rose-300 border text-[9px] font-black px-1.5 py-0.5 rounded-md shadow-2xs backdrop-blur-xs';
                    card.classList.add('opacity-40', 'pointer-events-none');
                } else if (stock <= alerta) {
                    badge.className = 'stock-badge absolute bottom-1.5 right-1.5 bg-amber-100 text-amber-900 border-amber-300 border text-[9px] font-black px-1.5 py-0.5 rounded-md shadow-2xs backdrop-blur-xs';
                    card.classList.remove('opacity-40', 'pointer-events-none');
                } else {
                    badge.className = 'stock-badge absolute bottom-1.5 right-1.5 bg-white/95 text-emerald-800 border-emerald-200 border text-[9px] font-black px-1.5 py-0.5 rounded-md shadow-2xs backdrop-blur-xs';
                    card.classList.remove('opacity-40', 'pointer-events-none');
                }

                if (item.producto && stock > 0) {
                    card.onclick = function() {
                        agregarAlCarrito(item.producto);
                    };
                }
            }
        });
    }

    function realizarBusqueda(query, isExact, silencioso = false) {
        const syncIcon = document.getElementById('icon_sync_catalogo');
        if (syncIcon) syncIcon.classList.add('animate-spin');

        if (!silencioso) {
            document.getElementById('search-loader').classList.remove('hidden');
        }

        fetch(`{{ url('/admin/pos/buscar-producto') }}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ buscar: query, exact: isExact, categoria_id: categoriaSeleccionada })
        })
        .then(res => res.json())
        .then(data => {
            if (!silencioso) {
                document.getElementById('search-loader').classList.add('hidden');
            }
            if (syncIcon) syncIcon.classList.remove('animate-spin');
            
            if (data.exact && data.producto) {
                agregarAlCarrito(data.producto);
                searchInput.value = '';
                btnLimpiarSearch.classList.add('hidden');
                return;
            }

            renderizarGridProductos(data.productos);
        })
        .catch(err => {
            if (!silencioso) {
                document.getElementById('search-loader').classList.add('hidden');
            }
            if (syncIcon) syncIcon.classList.remove('animate-spin');
        });
    }

    function actualizarCatalogoProductos(silencioso = true) {
        const query = searchInput ? searchInput.value.trim() : '';
        realizarBusqueda(query, false, silencioso);
    }

    // ==========================================
    // 2. GESTIÓN DEL CLIENTE
    // ==========================================
    function togglePanelCliente() {
        const panel = document.getElementById('panel_cliente');
        panel.classList.toggle('hidden');
        if (!panel.classList.contains('hidden')) {
            document.getElementById('buscar_cliente_input').focus();
        }
    }

    function buscarClienteAutocompletar(texto) {
        clearTimeout(clienteSearchTimeout);
        const container = document.getElementById('cliente_sugerencias');
        if (texto.trim().length < 2) {
            container.classList.add('hidden');
            return;
        }

        clienteSearchTimeout = setTimeout(() => {
            fetch(`{{ url('/admin/pos/buscar-cliente') }}?buscar=${encodeURIComponent(texto.trim())}`)
            .then(res => res.json())
            .then(data => {
                if (data.clientes && data.clientes.length > 0) {
                    container.innerHTML = '';
                    data.clientes.forEach(c => {
                        container.innerHTML += `
                            <div onclick='seleccionarClienteSugerido(${JSON.stringify(c).replace(/'/g, "&apos;")})' class="p-2 hover:bg-slate-100 cursor-pointer border-b border-gray-100 text-xs">
                                <div class="font-bold text-gray-800 flex items-center justify-between">
                                    <span>${c.nombre}</span>
                                    ${c.email ? '<span class="text-[9px] font-bold text-teal-600 bg-teal-50 border border-teal-200 px-1 py-0.2 rounded flex items-center gap-0.5"><span class="material-symbols-outlined text-[10px]">mail</span> Ticket</span>' : ''}
                                </div>
                                <div class="text-[10px] text-gray-500 font-mono flex items-center justify-between mt-0.5">
                                    <span>${c.documento}</span>
                                    ${c.email ? `<span class="truncate max-w-[140px] text-[9.5px] text-teal-700">${c.email}</span>` : ''}
                                </div>
                            </div>
                        `;
                    });
                    container.classList.remove('hidden');
                } else {
                    container.classList.add('hidden');
                }
            });
        }, 250);
    }

    function seleccionarClienteSugerido(cliente) {
        document.getElementById('input_cliente_doc').value = cliente.documento;
        document.getElementById('input_cliente_nombre').value = cliente.nombre;
        document.getElementById('input_cliente_email').value = cliente.email || '';
        document.getElementById('cliente_sugerencias').classList.add('hidden');
        document.getElementById('buscar_cliente_input').value = '';

        guardarDatosCliente();
    }

    let ultimaCedulaConsultada = '';
    let estaConsultandoCne = false;

    function consultarCneAuto() {
        const doc = document.getElementById('input_cliente_doc').value.trim();
        const nomActual = document.getElementById('input_cliente_nombre').value.trim();
        if (doc.length >= 4 && (doc !== ultimaCedulaConsultada || nomActual === '')) {
            consultarCne(false);
        }
    }

    function consultarCne(forzarAlerta = false) {
        const inputDoc = document.getElementById('input_cliente_doc');
        const inputNom = document.getElementById('input_cliente_nombre');
        const statusMsg = document.getElementById('cne_status_msg');
        const btnIcon = document.getElementById('cne_btn_icon');
        const valorDoc = inputDoc.value.trim();

        if (valorDoc.length < 4) {
            if (forzarAlerta) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cédula incompleta',
                    text: 'Ingresa al menos 5 dígitos de la cédula o RIF.',
                    toast: true,
                    position: 'top-end',
                    timer: 2500,
                    showConfirmButton: false
                });
            }
            return;
        }

        if (estaConsultandoCne) return;
        estaConsultandoCne = true;
        ultimaCedulaConsultada = valorDoc;

        if (btnIcon) {
            btnIcon.innerHTML = 'autorenew';
            btnIcon.classList.add('animate-spin');
        }
        if (statusMsg) {
            statusMsg.classList.remove('hidden');
            statusMsg.innerHTML = '<span class="text-teal-600 font-bold flex items-center gap-1"><span class="material-symbols-outlined text-[11px] animate-spin">autorenew</span> Consultando CNE</span>';
        }

        fetch(`{{ url('/admin/pos/consultar-cedula') }}?documento=${encodeURIComponent(valorDoc)}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                inputDoc.value = data.documento;
                if (data.nombre) {
                    inputNom.value = data.nombre;
                }
                if (data.email) {
                    document.getElementById('input_cliente_email').value = data.email;
                }

                if (statusMsg) {
                    if (data.fuente === 'cne_api') {
                        statusMsg.innerHTML = '<span class="text-emerald-700 font-bold flex items-center gap-1"><span class="material-symbols-outlined text-[12px]">verified</span> Extraído del CNE</span>';
                    } else if (data.fuente === 'usuario_local') {
                        let msg = '<span class="text-teal-700 font-bold flex items-center gap-1"><span class="material-symbols-outlined text-[12px]">check_circle</span> Cliente registrado';
                        if (data.email) msg += ' (con email)';
                        msg += '</span>';
                        statusMsg.innerHTML = msg;
                    } else {
                        statusMsg.innerHTML = '<span class="text-blue-700 font-bold flex items-center gap-1"><span class="material-symbols-outlined text-[12px]">history</span> Cliente frecuente</span>';
                    }
                }
                inputNom.focus();
            } else {
                if (statusMsg) {
                    statusMsg.innerHTML = '<span class="text-amber-700 font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[12px]">info</span> No figura en CNE. Ingresa el nombre manual.</span>';
                }
                if (forzarAlerta) {
                    Swal.fire({
                        icon: 'info',
                        title: 'No encontrado en CNE',
                        text: 'El documento no está registrado en el CNE. Puedes ingresar el nombre manualmente.',
                        toast: true,
                        position: 'top-end',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
                inputNom.focus();
            }
        })
        .catch(err => {
            if (statusMsg) {
                statusMsg.innerHTML = '<span class="text-rose-600 font-medium">Error al consultar. Ingresa el nombre manual.</span>';
            }
        })
        .finally(() => {
            estaConsultandoCne = false;
            if (btnIcon) {
                btnIcon.innerHTML = 'search';
                btnIcon.classList.remove('animate-spin');
            }
        });
    }

    function guardarDatosCliente() {
        const doc = document.getElementById('input_cliente_doc').value.trim();
        const nom = document.getElementById('input_cliente_nombre').value.trim();
        const email = document.getElementById('input_cliente_email').value.trim();

        if (doc && nom) {
            clienteActual.documento = doc;
            clienteActual.nombre = nom;
            clienteActual.email = email;
            clienteActual.telefono = '';
            clienteActual.direccion = 'Compra en Tienda Física';

            document.getElementById('label_cliente_nombre').innerText = nom;
            document.getElementById('label_cliente_doc').innerText = doc;
            document.getElementById('btn_txt_cliente').innerText = 'Editar';

            const badgeEmail = document.getElementById('badge_cliente_email');
            const labelEmailTxt = document.getElementById('label_cliente_email_txt');
            if (email) {
                labelEmailTxt.innerText = email;
                badgeEmail.classList.remove('hidden');
                badgeEmail.classList.add('inline-flex');
            } else {
                badgeEmail.classList.add('hidden');
                badgeEmail.classList.remove('inline-flex');
            }
        } else {
            restablecerConsumidorFinal();
        }
        document.getElementById('panel_cliente').classList.add('hidden');
    }

    function restablecerConsumidorFinal() {
        clienteActual.documento = 'V-00000000';
        clienteActual.nombre = 'Consumidor Final';
        clienteActual.email = '';
        clienteActual.telefono = '';
        clienteActual.direccion = 'Compra en Tienda Física';

        document.getElementById('input_cliente_doc').value = '';
        document.getElementById('input_cliente_nombre').value = '';
        document.getElementById('input_cliente_email').value = '';
        document.getElementById('buscar_cliente_input').value = '';
        const statusMsg = document.getElementById('cne_status_msg');
        if (statusMsg) {
            statusMsg.classList.add('hidden');
            statusMsg.innerHTML = '';
        }
        ultimaCedulaConsultada = '';

        document.getElementById('label_cliente_nombre').innerText = 'Consumidor Final';
        document.getElementById('label_cliente_doc').innerText = 'V-00000000';
        document.getElementById('btn_txt_cliente').innerText = 'Cambiar';

        const badgeEmail = document.getElementById('badge_cliente_email');
        if (badgeEmail) {
            badgeEmail.classList.add('hidden');
            badgeEmail.classList.remove('inline-flex');
        }

        document.getElementById('panel_cliente').classList.add('hidden');
    }

    // ==========================================
    // 3. CARRITO DE COMPRAS
    // ==========================================
    function agregarAlCarrito(producto) {
        let index = carrito.findIndex(p => p.id === producto.id);
        let paso = parseFloat(producto.paso_venta) || 1;
        let precio = parseFloat(producto.precio_oferta_usd ? producto.precio_oferta_usd : producto.precio_venta_usd);
        let min = parseFloat(producto.venta_minima) || 1;
        let unidad = producto.unidad_medida || 'ud';
        let stockMax = parseFloat(producto.stock_total) || 0;

        if (index > -1) {
            let nuevaCant = Math.round((carrito[index].cantidad + paso) * 1000) / 1000;
            if (nuevaCant > stockMax) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Límite de Stock',
                    text: `Stock máximo disponible: ${stockMax} ${formatUnidad(unidad)}.`,
                    toast: true,
                    position: 'top-end',
                    timer: 2500,
                    showConfirmButton: false
                });
                return;
            }
            carrito[index].cantidad = nuevaCant;
        } else {
            if (min > stockMax) {
                Swal.fire({
                    icon: 'error',
                    title: 'Stock Insuficiente',
                    text: `El stock disponible (${stockMax} ${formatUnidad(unidad)}) es menor a la venta mínima (${min} ${formatUnidad(unidad)}).`,
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });
                return;
            }
            carrito.push({
                id: producto.id,
                nombre: producto.nombre,
                precio: precio,
                cantidad: min,
                unidad_medida: unidad,
                paso: paso,
                venta_minima: min,
                stock_max: stockMax
            });
        }
        renderizarCarrito();
    }

    function cambiarCantidadDirecta(index, valorInput) {
        let item = carrito[index];
        let nuevaCantidad = parseFloat(valorInput);
        let unidad = formatUnidad(item.unidad_medida);

        if (isNaN(nuevaCantidad) || nuevaCantidad <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Cantidad inválida',
                text: 'Por favor ingresa un número válido mayor a 0.',
                toast: true,
                position: 'top-end',
                timer: 2000,
                showConfirmButton: false
            });
            renderizarCarrito();
            return;
        }

        if (nuevaCantidad < item.venta_minima) {
            Swal.fire({
                icon: 'warning',
                title: 'Venta Mínima Requerida',
                text: `La venta mínima para este producto es de ${item.venta_minima} ${unidad}.`,
                toast: true,
                position: 'top-end',
                timer: 2500,
                showConfirmButton: false
            });
            item.cantidad = item.venta_minima;
            renderizarCarrito();
            return;
        }

        if (nuevaCantidad > item.stock_max) {
            Swal.fire({
                icon: 'warning',
                title: 'Stock Insuficiente',
                text: `Solo hay ${item.stock_max} ${unidad} disponibles en inventario.`,
                toast: true,
                position: 'top-end',
                timer: 2500,
                showConfirmButton: false
            });
            item.cantidad = item.stock_max;
            renderizarCarrito();
            return;
        }

        item.cantidad = Math.round(nuevaCantidad * 1000) / 1000;
        renderizarCarrito();
    }

    function modificarCantidad(index, accion) {
        let item = carrito[index];
        if (accion === 'suma') {
            let proxima = Math.round((item.cantidad + item.paso) * 1000) / 1000;
            if (proxima > item.stock_max) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Límite de Stock',
                    text: `Stock máximo: ${item.stock_max} ${formatUnidad(item.unidad_medida)}.`,
                    toast: true,
                    position: 'top-end',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }
            item.cantidad = proxima;
        } else {
            let proxima = Math.round((item.cantidad - item.paso) * 1000) / 1000;
            if (proxima < item.venta_minima) {
                carrito.splice(index, 1);
            } else {
                item.cantidad = proxima;
            }
        }
        renderizarCarrito();
    }

    function eliminarItem(index) {
        carrito.splice(index, 1);
        renderizarCarrito();
    }

    function vaciarCarrito(confirmar = true) {
        if (carrito.length === 0) return;

        if (!confirmar) {
            carrito = [];
            renderizarCarrito();
            return;
        }

        Swal.fire({
            title: '¿Vaciar Ticket?',
            text: 'Se eliminarán todos los productos agregados a la cuenta.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, vaciar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                carrito = [];
                renderizarCarrito();
            }
        });
    }

    function getTotalesVenta() {
        let subtotalUsd = 0;
        carrito.forEach(item => subtotalUsd += (item.precio * item.cantidad));
        let ivaUsd = subtotalUsd * (ivaPorcentaje / 100);
        let baseConIva = subtotalUsd + ivaUsd;

        let pctRecargo = getPorcentajeModalidad(modalidadPosActual);
        let recargoUsd = Math.round((baseConIva * (pctRecargo / 100)) * 100) / 100;
        let totalUsd = Math.round((baseConIva + recargoUsd) * 100) / 100;
        let totalVes = Math.round((totalUsd * tasaBcv) * 100) / 100;

        return { subtotalUsd, ivaUsd, baseConIva, pctRecargo, recargoUsd, totalUsd, totalVes };
    }

    function renderizarCarrito() {
        cartContainer.innerHTML = '';
        const { subtotalUsd, ivaUsd, baseConIva, pctRecargo, recargoUsd, totalUsd, totalVes } = getTotalesVenta();

        if (carrito.length === 0) {
            cartContainer.appendChild(emptyMsg);
            emptyMsg.classList.remove('hidden');
            btnCobrar.disabled = true;
        } else {
            emptyMsg.classList.add('hidden');
            btnCobrar.disabled = false;

            const factorMod = 1 + (pctRecargo / 100);

            carrito.forEach((item, index) => {
                let subItem = (item.precio * item.cantidad) * factorMod;
                let subItemVes = subItem * tasaBcv;
                let pvpItem = item.precio * (1 + (ivaPorcentaje / 100)) * factorMod;
                let unidadLabel = formatUnidad(item.unidad_medida);

                let avisoMin = (item.venta_minima && item.venta_minima !== 1) 
                    ? `<span class="inline-block text-[9.5px] font-bold text-amber-800 bg-amber-50 border border-amber-200 px-1 py-0.2 rounded ml-1">Mín: ${item.venta_minima} ${unidadLabel}</span>` 
                    : '';

                let html = `
                    <div class="bg-white p-2.5 rounded-xl border border-gray-200 hover:border-gray-300 shadow-2xs mb-2 relative transition-all">
                        <div class="flex justify-between items-start gap-2 mb-1.5">
                            <div class="pr-6">
                                <h4 class="font-bold text-agro-dark text-xs leading-tight line-clamp-2">${item.nombre}</h4>
                                <div class="flex items-center gap-1 mt-0.5 flex-wrap">
                                    <span class="text-[11px] font-black text-agro-dark">$${pvpItem.toFixed(2)}</span>
                                    <span class="text-[9.5px] text-gray-400 font-medium">(Base: $${item.precio.toFixed(2)}) / ${unidadLabel}</span>
                                    ${pctRecargo > 0 ? `<span class="text-[8.5px] font-bold px-1 bg-amber-50 text-amber-800 border border-amber-200 rounded">+${pctRecargo}%</span>` : ''}
                                    ${avisoMin}
                                </div>
                            </div>
                            <button onclick="eliminarItem(${index})" class="text-gray-300 hover:text-rose-600 absolute top-2 right-2 transition-colors p-0.5" title="Eliminar producto">
                                <span class="material-symbols-outlined text-[16px]">delete</span>
                            </button>
                        </div>

                        <div class="flex justify-between items-end pt-1 border-t border-gray-100">
                            {{-- Cantidad con Input Directo y Botones Step --}}
                            <div class="flex items-center gap-1 bg-gray-50 rounded-lg p-0.5 border border-gray-200">
                                <button onclick="modificarCantidad(${index}, 'resta')" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow-2xs text-gray-600 hover:text-primary hover:bg-gray-100 transition-colors" title="Restar paso (-${item.paso})">
                                    <span class="material-symbols-outlined text-[14px]">remove</span>
                                </button>
                                <div class="flex items-center px-1">
                                    <input type="number" step="any" min="${item.venta_minima}" max="${item.stock_max}" value="${item.cantidad}" 
                                        onchange="cambiarCantidadDirecta(${index}, this.value)" 
                                        onfocus="this.select()"
                                        class="w-14 h-6 text-center text-xs font-black bg-transparent border-0 focus:bg-white focus:ring-1 focus:ring-teal-500 rounded outline-none text-agro-dark" 
                                        title="Haz clic para escribir la cantidad exacta (ej. peso en balanza)">
                                    <span class="text-[10px] font-bold text-gray-500 select-none">${unidadLabel}</span>
                                </div>
                                <button onclick="modificarCantidad(${index}, 'suma')" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow-2xs text-gray-600 hover:text-primary hover:bg-gray-100 transition-colors" title="Sumar paso (+${item.paso})">
                                    <span class="material-symbols-outlined text-[14px]">add</span>
                                </button>
                            </div>

                            {{-- Subtotal con ambos montos (USD y Bs) --}}
                            <div class="text-right">
                                <p class="font-black text-primary text-sm leading-none">$${subItem.toFixed(2)}</p>
                                <p class="text-[10px] font-bold text-gray-500 mt-0.5">Bs. ${subItemVes.toFixed(2)}</p>
                            </div>
                        </div>
                    </div>
                `;
                cartContainer.insertAdjacentHTML('beforeend', html);
            });
        }

        document.getElementById('subtotal_usd_ui').innerText = `$${subtotalUsd.toFixed(2)}`;
        document.getElementById('iva_usd_ui').innerText = `$${ivaUsd.toFixed(2)}`;

        const filaRecargo = document.getElementById('fila_recargo_ui');
        const labelRecargo = document.getElementById('label_recargo_ui');
        const recargoUsdUi = document.getElementById('recargo_usd_ui');

        if (pctRecargo > 0) {
            if (filaRecargo) filaRecargo.classList.remove('hidden');
            if (labelRecargo) {
                const nombreMod = modalidadPosActual === 'bolivares' ? 'Bolívares (VES)' : (modalidadPosActual === 'cashea' ? 'Cashea' : 'Divisas');
                labelRecargo.innerHTML = `<span class="material-symbols-outlined text-[13px]">percent</span><span>Ajuste ${nombreMod} (+${pctRecargo}%)</span>`;
            }
            if (recargoUsdUi) recargoUsdUi.innerText = `+$${recargoUsd.toFixed(2)}`;
        } else {
            if (filaRecargo) filaRecargo.classList.add('hidden');
        }

        document.getElementById('total_usd_ui').innerText = `$${totalUsd.toFixed(2)}`;
        document.getElementById('total_ves_ui').innerText = `Bs. ${totalVes.toFixed(2)}`;
        
        document.getElementById('checkout_total_usd').innerText = `$${totalUsd.toFixed(2)}`;
        document.getElementById('checkout_total_ves').innerText = `Bs. ${totalVes.toFixed(2)}`;
    }

    // ==========================================
    // 4. CHECKOUT, CALCULADORA Y PAGO MIXTO
    // ==========================================
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F9') { 
            e.preventDefault(); 
            if (carrito.length > 0) abrirModalCobro(); 
        }
        if (e.key === 'F2') {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        }
        if (e.key === 'F4') {
            e.preventDefault();
            togglePanelCliente();
        }
        if (e.altKey && e.key === '1') {
            e.preventDefault();
            cambiarModalidadPos('divisas');
        }
        if (e.altKey && e.key === '2') {
            e.preventDefault();
            cambiarModalidadPos('bolivares');
        }
        if (e.altKey && e.key === '3') {
            e.preventDefault();
            cambiarModalidadPos('cashea');
        }
        if (e.key === 'Enter') {
            const exitoModal = document.getElementById('ventaExitosaModal');
            if (!exitoModal.classList.contains('hidden')) {
                e.preventDefault();
                imprimirTicketDirecto();
            }
        }
        if (e.key === 'Escape') {
            cerrarModalExito();
            cerrarModalCobro();
            cerrarModalCierre();
            document.getElementById('panel_cliente').classList.add('hidden');
        }
    });

    function cambiarModoPago(modo) {
        modoPagoActual = modo;
        const btnU = document.getElementById('tab_modo_unico');
        const btnM = document.getElementById('tab_modo_mixto');
        const secU = document.getElementById('seccion_pago_unico');
        const secM = document.getElementById('seccion_pago_mixto');

        if (modo === 'unico') {
            btnU.className = 'flex-1 py-1.5 text-xs font-black rounded-lg transition-all bg-white text-agro-dark shadow-xs flex items-center justify-center gap-1.5';
            btnM.className = 'flex-1 py-1.5 text-xs font-black rounded-lg transition-all text-gray-500 hover:text-agro-dark flex items-center justify-center gap-1.5';
            secU.classList.remove('hidden');
            secM.classList.add('hidden');
            actualizarMetodoPagoUnico();
        } else {
            btnM.className = 'flex-1 py-1.5 text-xs font-black rounded-lg transition-all bg-white text-agro-dark shadow-xs flex items-center justify-center gap-1.5';
            btnU.className = 'flex-1 py-1.5 text-xs font-black rounded-lg transition-all text-gray-500 hover:text-agro-dark flex items-center justify-center gap-1.5';
            secM.classList.remove('hidden');
            secU.classList.add('hidden');

            const { totalUsd, totalVes } = getTotalesVenta();
            modalTotalUsdActual = totalUsd;
            modalTotalVesActual = totalVes;
            document.getElementById('checkout_total_usd').innerText = `$${totalUsd.toFixed(2)}`;
            document.getElementById('checkout_total_ves').innerText = `Bs. ${totalVes.toFixed(2)}`;
            const recargoBadge = document.getElementById('checkout_recargo_badge');
            if (recargoBadge) recargoBadge.classList.add('hidden');

            actualizarUIProgresoMixto();
        }
    }

    function getTotalesActualesModal() {
        if (modalTotalUsdActual > 0 || modalTotalVesActual > 0) {
            return {
                totalUsd: modalTotalUsdActual,
                totalVes: modalTotalVesActual
            };
        }
        const { totalUsd, totalVes } = getTotalesVenta();
        return { totalUsd, totalVes };
    }

    function actualizarMetodoPagoUnico() {
        const metodo = document.getElementById('metodo_pago').value;
        const opt = document.getElementById('metodo_pago').selectedOptions[0];
        const modalidad = opt ? (opt.dataset.modalidad || (metodo === 'cashea' ? 'cashea' : (['efectivo_usd', 'zelle', 'binance'].includes(metodo) ? 'divisas' : 'bolivares'))) : 'divisas';
        
        // Sincronizar modalidad activa si el cajero cambió el selector en el modal
        if (modalidad !== modalidadPosActual) {
            modalidadPosActual = modalidad;
            const btnDivisas = document.getElementById('btn_mod_pos_divisas');
            const btnBs = document.getElementById('btn_mod_pos_bolivares');
            const btnCashea = document.getElementById('btn_mod_pos_cashea');

            if (btnDivisas) btnDivisas.className = 'px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 ' + (modalidad === 'divisas' ? 'bg-emerald-500 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-white/10');
            if (btnBs) btnBs.className = 'px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 ' + (modalidad === 'bolivares' ? 'bg-emerald-500 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-white/10');
            if (btnCashea) btnCashea.className = 'px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 ' + (modalidad === 'cashea' ? 'bg-emerald-500 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-white/10');

            actualizarPreciosTarjetasModalidad();
        }

        let pct = 0;
        if (recargosPos && recargosPos[modalidad]) {
            if (typeof recargosPos[modalidad] === 'object' && recargosPos[modalidad] !== null) {
                pct = parseFloat(recargosPos[modalidad].porcentaje) || 0;
            } else {
                pct = parseFloat(recargosPos[modalidad]) || 0;
            }
        }

        const { subtotalUsd, ivaUsd } = getTotalesVenta();
        const base = subtotalUsd + ivaUsd;
        const recargoUsd = Math.round((base * (pct / 100)) * 100) / 100;
        const finalUsd = Math.round((base + recargoUsd) * 100) / 100;
        const finalVes = Math.round((finalUsd * tasaBcv) * 100) / 100;

        modalTotalUsdActual = finalUsd;
        modalTotalVesActual = finalVes;

        document.getElementById('checkout_total_usd').innerText = `$${finalUsd.toFixed(2)}`;
        document.getElementById('checkout_total_ves').innerText = `Bs. ${finalVes.toFixed(2)}`;

        const recargoBadge = document.getElementById('checkout_recargo_badge');
        if (recargoBadge) {
            if (Math.abs(pct) > 0.001) {
                const signo = pct > 0 ? '+' : '';
                recargoBadge.innerText = `Ajuste (${modalidad.toUpperCase()}: ${signo}${pct.toFixed(1)}%) = ${signo}$${recargoUsd.toFixed(2)}`;
                recargoBadge.classList.remove('hidden');
            } else {
                recargoBadge.classList.add('hidden');
            }
        }

        const cajaRef = document.getElementById('caja_referencia');
        const cajaCalc = document.getElementById('caja_calculadora_vuelto');
        const labelMoneda = document.getElementById('label_moneda_recibida');
        const simboloRecibido = document.getElementById('simbolo_recibido');
        const botonesBilletes = document.getElementById('botones_billetes');

        if (metodo === 'efectivo_usd') {
            cajaCalc.classList.remove('hidden');
            labelMoneda.innerText = 'Moneda: Dólares (USD)';
            simboloRecibido.innerText = '$';
            cajaRef.classList.add('hidden');
            if (botonesBilletes) {
                botonesBilletes.innerHTML = `
                    <button type="button" onclick="setBilletesRapido('exacto')" class="px-2 py-0.5 bg-white hover:bg-emerald-100 border border-emerald-300 rounded text-[11px] font-bold text-emerald-800">Exacto</button>
                    <button type="button" onclick="setBilletesRapido(5)" class="px-2 py-0.5 bg-white hover:bg-emerald-100 border border-emerald-300 rounded text-[11px] font-bold text-emerald-800">$5</button>
                    <button type="button" onclick="setBilletesRapido(10)" class="px-2 py-0.5 bg-white hover:bg-emerald-100 border border-emerald-300 rounded text-[11px] font-bold text-emerald-800">$10</button>
                    <button type="button" onclick="setBilletesRapido(20)" class="px-2 py-0.5 bg-white hover:bg-emerald-100 border border-emerald-300 rounded text-[11px] font-bold text-emerald-800">$20</button>
                    <button type="button" onclick="setBilletesRapido(50)" class="px-2 py-0.5 bg-white hover:bg-emerald-100 border border-emerald-300 rounded text-[11px] font-bold text-emerald-800">$50</button>
                    <button type="button" onclick="setBilletesRapido(100)" class="px-2 py-0.5 bg-white hover:bg-emerald-100 border border-emerald-300 rounded text-[11px] font-bold text-emerald-800">$100</button>
                `;
            }
        } else if (metodo === 'efectivo_bs') {
            cajaCalc.classList.remove('hidden');
            labelMoneda.innerText = 'Moneda: Bolívares (VES)';
            simboloRecibido.innerText = 'Bs.';
            cajaRef.classList.add('hidden');
            if (botonesBilletes) {
                botonesBilletes.innerHTML = `
                    <button type="button" onclick="setBilletesRapido('exacto')" class="px-2 py-0.5 bg-white hover:bg-emerald-100 border border-emerald-300 rounded text-[11px] font-bold text-emerald-800">Exacto</button>
                    <button type="button" onclick="setBilletesRapido(100)" class="px-2 py-0.5 bg-white hover:bg-emerald-100 border border-emerald-300 rounded text-[11px] font-bold text-emerald-800">Bs. 100</button>
                    <button type="button" onclick="setBilletesRapido(200)" class="px-2 py-0.5 bg-white hover:bg-emerald-100 border border-emerald-300 rounded text-[11px] font-bold text-emerald-800">Bs. 200</button>
                    <button type="button" onclick="setBilletesRapido(500)" class="px-2 py-0.5 bg-white hover:bg-emerald-100 border border-emerald-300 rounded text-[11px] font-bold text-emerald-800">Bs. 500</button>
                    <button type="button" onclick="setBilletesRapido(1000)" class="px-2 py-0.5 bg-white hover:bg-emerald-100 border border-emerald-300 rounded text-[11px] font-bold text-emerald-800">Bs. 1000</button>
                    <button type="button" onclick="setBilletesRapido(2000)" class="px-2 py-0.5 bg-white hover:bg-emerald-100 border border-emerald-300 rounded text-[11px] font-bold text-emerald-800">Bs. 2000</button>
                `;
            }
        } else if (metodo === 'cashea') {
            cajaCalc.classList.add('hidden');
            cajaRef.classList.remove('hidden');
            const refInput = document.getElementById('referencia_pago');
            if (refInput) {
                refInput.placeholder = 'Ej: CSH-849204 (Código en app Cashea)';
                refInput.focus();
            }
        } else {
            cajaCalc.classList.add('hidden');
            cajaRef.classList.remove('hidden');
            const refInput = document.getElementById('referencia_pago');
            if (refInput) {
                refInput.placeholder = 'Ej: 12345678';
                refInput.focus();
            }
        }
        calcularVuelto();
    }

    function setBilletesRapido(monto) {
        const { totalUsd, totalVes } = getTotalesActualesModal();
        const metodo = document.getElementById('metodo_pago').value;
        const input = document.getElementById('monto_recibido_input');

        if (monto === 'exacto') {
            input.value = (metodo === 'efectivo_bs' ? totalVes : totalUsd).toFixed(2);
        } else {
            input.value = monto.toFixed(2);
        }
        calcularVuelto();
    }

    function calcularVuelto() {
        const { totalUsd, totalVes } = getTotalesActualesModal();
        const metodo = document.getElementById('metodo_pago').value;
        const input = document.getElementById('monto_recibido_input');
        const recibido = parseFloat(input.value) || 0;
        const totalAComparar = metodo === 'efectivo_bs' ? totalVes : totalUsd;

        const estadoTxt = document.getElementById('vuelto_estado_texto');
        const vueltoUsdTxt = document.getElementById('vuelto_usd_texto');
        const vueltoVesTxt = document.getElementById('vuelto_ves_texto');
        const box = document.getElementById('resultado_vuelto_box');

        if (recibido === 0) {
            estadoTxt.innerText = 'Vuelto a entregar:';
            vueltoUsdTxt.innerText = '$0.00';
            vueltoVesTxt.innerText = 'Bs. 0.00';
            box.className = 'p-2.5 bg-white rounded-xl border border-emerald-200 flex justify-between items-center';
            return;
        }

        if (recibido >= totalAComparar) {
            let vuelto = recibido - totalAComparar;
            let vueltoUsd = metodo === 'efectivo_bs' ? (vuelto / tasaBcv) : vuelto;
            let vueltoVes = metodo === 'efectivo_bs' ? vuelto : (vuelto * tasaBcv);

            estadoTxt.innerText = '✓ Vuelto a Entregar:';
            vueltoUsdTxt.innerText = `$${vueltoUsd.toFixed(2)}`;
            vueltoVesTxt.innerText = `Bs. ${vueltoVes.toFixed(2)}`;
            box.className = 'p-2.5 bg-emerald-100/60 rounded-xl border border-emerald-300 flex justify-between items-center';
        } else {
            let faltante = totalAComparar - recibido;
            let faltanteUsd = metodo === 'efectivo_bs' ? (faltante / tasaBcv) : faltante;
            let faltanteVes = metodo === 'efectivo_bs' ? faltante : (faltante * tasaBcv);

            estadoTxt.innerText = '⚠ Faltan:';
            vueltoUsdTxt.innerText = `-$${faltanteUsd.toFixed(2)}`;
            vueltoVesTxt.innerText = `Bs. ${faltanteVes.toFixed(2)}`;
            box.className = 'p-2.5 bg-amber-50 rounded-xl border border-amber-300 flex justify-between items-center text-amber-900';
        }
    }

    // Funciones Pago Mixto
    function cambiarMetodoMixto() {
        const metodo = document.getElementById('mixto_select_metodo').value;
        const cajaRef = document.getElementById('mixto_caja_ref');
        if (['pago_movil', 'zelle', 'binance', 'transferencia', 'punto_venta'].includes(metodo)) {
            cajaRef.classList.remove('hidden');
        } else {
            cajaRef.classList.add('hidden');
            document.getElementById('mixto_input_ref').value = '';
        }
    }

    function agregarLineaPagoMixto() {
        const { totalUsd } = getTotalesVenta();
        const metodo = document.getElementById('mixto_select_metodo').value;
        const monto = parseFloat(document.getElementById('mixto_input_monto').value) || 0;
        const ref = document.getElementById('mixto_input_ref').value.trim();

        if (monto <= 0) {
            Swal.fire({icon: 'warning', title: 'Monto inválido', text: 'Ingresa un monto en USD mayor a 0', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false});
            return;
        }

        const metodosDigitales = ['pago_movil', 'zelle', 'binance', 'transferencia', 'punto_venta'];
        if (metodosDigitales.includes(metodo) && !ref) {
            Swal.fire({icon: 'warning', title: 'Falta referencia', text: 'Por favor ingresa la referencia bancaria', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false});
            return;
        }

        pagosMixtos.push({
            metodo: metodo,
            monto_usd: monto,
            monto_ves: monto * tasaBcv,
            referencia: ref || null
        });

        document.getElementById('mixto_input_monto').value = '';
        document.getElementById('mixto_input_ref').value = '';
        actualizarUIProgresoMixto();
    }

    function eliminarLineaPagoMixto(index) {
        pagosMixtos.splice(index, 1);
        actualizarUIProgresoMixto();
    }

    function actualizarUIProgresoMixto() {
        const { totalUsd } = getTotalesVenta();
        const container = document.getElementById('lista_pagos_mixtos');
        container.innerHTML = '';

        let sumaPagado = 0;
        pagosMixtos.forEach((p, idx) => {
            sumaPagado += p.monto_usd;
            container.innerHTML += `
                <div class="flex justify-between items-center bg-white p-2 rounded-lg border border-gray-200 text-xs">
                    <div>
                        <strong class="text-agro-dark capitalize">${p.metodo.replace('_', ' ')}</strong>
                        ${p.referencia ? `<span class="text-gray-500 font-mono text-[10px] ml-1">Ref: ${p.referencia}</span>` : ''}
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-black text-emerald-600">$${p.monto_usd.toFixed(2)}</span>
                        <button type="button" onclick="eliminarLineaPagoMixto(${idx})" class="text-red-400 hover:text-red-600"><span class="material-symbols-outlined text-[15px]">delete</span></button>
                    </div>
                </div>
            `;
        });

        let pendiente = Math.max(0, totalUsd - sumaPagado);
        let porcentaje = Math.min(100, (sumaPagado / (totalUsd || 1)) * 100);

        document.getElementById('mixto_pagado_txt').innerText = `$${sumaPagado.toFixed(2)}`;
        document.getElementById('mixto_pendiente_txt').innerText = `$${pendiente.toFixed(2)}`;
        document.getElementById('barra_progreso_mixto').style.width = `${porcentaje}%`;

        if (pendiente > 0) {
            document.getElementById('mixto_input_monto').placeholder = pendiente.toFixed(2);
        }
    }

    function abrirModalCobro() {
        if (carrito.length === 0) return;
        pagosMixtos = [];
        cambiarModoPago('unico');

        // Preseleccionar método coherente con la modalidad de precio activa
        const selectMetodo = document.getElementById('metodo_pago');
        if (selectMetodo) {
            if (modalidadPosActual === 'bolivares') {
                selectMetodo.value = 'pago_movil';
            } else if (modalidadPosActual === 'cashea') {
                selectMetodo.value = 'cashea';
            } else {
                selectMetodo.value = 'efectivo_usd';
            }
        }

        document.getElementById('monto_recibido_input').value = '';
        actualizarMetodoPagoUnico();

        const modal = document.getElementById('checkoutModal');
        const backdrop = document.getElementById('checkoutBackdrop');
        const panel = document.getElementById('checkoutPanel');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    function cerrarModalCobro() {
        const modal = document.getElementById('checkoutModal');
        const backdrop = document.getElementById('checkoutBackdrop');
        const panel = document.getElementById('checkoutPanel');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 200);
    }

    // ==========================================
    // 5. PROCESAR VENTA
    // ==========================================
    function procesarTransaccion() {
        const { totalUsd, totalVes } = getTotalesVenta();
        const emailIngresado = document.getElementById('input_cliente_email') ? document.getElementById('input_cliente_email').value.trim() : '';
        const docIngresado = document.getElementById('input_cliente_doc') ? document.getElementById('input_cliente_doc').value.trim() : '';
        const nomIngresado = document.getElementById('input_cliente_nombre') ? document.getElementById('input_cliente_nombre').value.trim() : '';

        const docFinal = (clienteActual.documento && clienteActual.documento !== 'V-00000000') ? clienteActual.documento : (docIngresado || 'V-00000000');
        const nomFinal = (clienteActual.nombre && clienteActual.nombre !== 'Consumidor Final') ? clienteActual.nombre : (nomIngresado || 'Consumidor Final');
        const emailFinal = clienteActual.email || emailIngresado || '';

        let payload = {
            carrito: carrito,
            tipo_pago: modoPagoActual,
            modalidad_pago: modalidadPosActual,
            cliente_documento: docFinal,
            cliente_nombre: nomFinal,
            cliente_email: emailFinal,
            cliente_direccion: clienteActual.direccion || 'Compra en Tienda Física',
            cliente_telefono: clienteActual.telefono || ''
        };

        let vueltoCalculado = 0;

        if (modoPagoActual === 'unico') {
            const metodo = document.getElementById('metodo_pago').value;
            const referencia = document.getElementById('referencia_pago').value;
            const metodosDigitales = ['pago_movil', 'zelle', 'binance', 'transferencia', 'punto_venta', 'cashea'];

            if (metodosDigitales.includes(metodo) && referencia.trim() === '') {
                Swal.fire('Falta Referencia', 'Por favor ingresa el número de referencia del comprobante.', 'warning');
                return;
            }

            if (['efectivo_usd', 'efectivo_bs'].includes(metodo)) {
                const rec = parseFloat(document.getElementById('monto_recibido_input').value) || 0;
                const { totalUsd: totalActualUsd, totalVes: totalActualVes } = getTotalesActualesModal();
                const totalComp = metodo === 'efectivo_bs' ? totalActualVes : totalActualUsd;
                if (rec > 0 && rec >= totalComp) {
                    vueltoCalculado = metodo === 'efectivo_bs' ? ((rec - totalComp) / tasaBcv) : (rec - totalComp);
                }
            }

            payload.metodo_pago = metodo;
            payload.referencia_pago = referencia;
            payload.vuelto_usd = vueltoCalculado;
        } else {
            let suma = pagosMixtos.reduce((acc, curr) => acc + curr.monto_usd, 0);
            if (suma < totalUsd - 0.02) {
                Swal.fire('Pago Incompleto', `Aún faltan $${(totalUsd - suma).toFixed(2)} por cubrir en los pagos mixtos.`, 'warning');
                return;
            }
            payload.pagos = pagosMixtos;
        }

        const btn = document.getElementById('btnConfirmarPago');
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[16px]">autorenew</span> Procesando...';

        fetch(`{{ url('/admin/pos/procesar-venta') }}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify(payload)
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Error al procesar la venta');
            return data;
        })
        .then(data => {
            cerrarModalCobro();
            vaciarCarrito(false);
            restablecerConsumidorFinal();

            // 1. Actualización inmediata del stock en las tarjetas visibles (AJAX)
            if (data.productos_actualizados && data.productos_actualizados.length > 0) {
                actualizarStockTarjetasLocales(data.productos_actualizados);
            }
            // 2. Refresco silencioso del catálogo para sincronizar inventario
            actualizarCatalogoProductos(true);

            ultimaVentaExitosa = data;
            abrirModalExito(data);
        })
        .catch(err => {
            Swal.fire('Error al procesar', err.message, 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = 'Confirmar Transacción';
        });
    }

    // ==========================================
    // 6. MODAL DE ÉXITO & IMPRESIÓN DIRECTA
    // ==========================================
    function abrirModalExito(data) {
        document.getElementById('modal_exito_factura').innerText = data.numero_factura || 'F-0000000';
        document.getElementById('modal_exito_cliente').innerText = data.cliente || 'Consumidor Final';
        document.getElementById('modal_exito_monto').innerText = `$${parseFloat(data.gran_total_usd).toFixed(2)}`;

        const vueltoRow = document.getElementById('modal_exito_vuelto_row');
        if (data.vuelto_usd && data.vuelto_usd > 0) {
            const vUsd = parseFloat(data.vuelto_usd);
            const vVes = vUsd * tasaBcv;
            document.getElementById('modal_exito_vuelto').innerText = `$${vUsd.toFixed(2)} (Bs. ${vVes.toFixed(2)})`;
            vueltoRow.classList.remove('hidden');
        } else {
            vueltoRow.classList.add('hidden');
        }

        // Estado del Envío por Correo (Factura Ticket Digital)
        const correoTxt = document.getElementById('modal_exito_correo_txt');
        const inputReenvio = document.getElementById('input_reenvio_email');
        const msgManual = document.getElementById('msg_correo_manual');
        if (msgManual) msgManual.innerText = '';

        if (data.correo_enviado) {
            correoTxt.innerHTML = `<span class="text-emerald-700 font-bold flex items-center gap-1" title="${data.cliente_email}">✓ Enviado a ${data.cliente_email}</span>`;
            if (inputReenvio) inputReenvio.value = data.cliente_email || '';
        } else if (data.cliente_email) {
            correoTxt.innerHTML = `<span class="text-amber-600 font-medium">No se pudo enviar (${data.cliente_email})</span>`;
            if (inputReenvio) inputReenvio.value = data.cliente_email;
        } else {
            correoTxt.innerHTML = `<span class="text-gray-400 font-medium">Sin correo registrado</span>`;
            if (inputReenvio) inputReenvio.value = '';
        }

        const modal = document.getElementById('ventaExitosaModal');
        const backdrop = document.getElementById('exitosaBackdrop');
        const panel = document.getElementById('exitosaPanel');

        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
            document.getElementById('btnImprimirTicketExito').focus();
        }, 10);
    }

    function enviarTicketCorreoManual() {
        if (!ultimaVentaExitosa || !ultimaVentaExitosa.factura_id) {
            Swal.fire('Error', 'No hay una factura activa para enviar.', 'warning');
            return;
        }
        const email = document.getElementById('input_reenvio_email').value.trim();
        if (!email || !email.includes('@')) {
            Swal.fire('Correo Inválido', 'Por favor ingresa un correo electrónico válido.', 'warning');
            return;
        }
        const btn = document.getElementById('btnEnviarCorreoManual');
        const msg = document.getElementById('msg_correo_manual');
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[12px]">autorenew</span>';
        if (msg) msg.innerText = '';

        fetch(`{{ url('/admin/pos/enviar-ticket-correo') }}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                factura_id: ultimaVentaExitosa.factura_id,
                email: email
            })
        })
        .then(async res => {
            const json = await res.json();
            if (!res.ok) throw new Error(json.message || 'Error al enviar');
            return json;
        })
        .then(json => {
            if (msg) msg.innerHTML = '<span class="text-emerald-600 font-bold">¡Enviado!</span>';
            const correoTxt = document.getElementById('modal_exito_correo_txt');
            if (correoTxt) {
                correoTxt.innerHTML = `<span class="text-emerald-700 font-bold flex items-center gap-1">✓ Enviado a ${email}</span>`;
            }
            Swal.fire({
                icon: 'success',
                title: 'Ticket Enviado',
                text: json.message,
                timer: 2500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        })
        .catch(err => {
            if (msg) msg.innerHTML = '<span class="text-rose-600 font-bold">Error</span>';
            Swal.fire('Error al enviar', err.message, 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<span class="material-symbols-outlined text-[13px]">mail</span> Enviar';
        });
    }

    function cerrarModalExito() {
        const modal = document.getElementById('ventaExitosaModal');
        const backdrop = document.getElementById('exitosaBackdrop');
        const panel = document.getElementById('exitosaPanel');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 200);
        searchInput.focus();
    }

    function imprimirTicketDirecto() {
        if (ultimaVentaExitosa && ultimaVentaExitosa.ticket_url) {
            window.open(ultimaVentaExitosa.ticket_url, '_blank');
        } else if (ultimaVentaExitosa && ultimaVentaExitosa.factura_id) {
            window.open(`{{ url('/factura') }}/${ultimaVentaExitosa.factura_id}/ticket`, '_blank');
        }
    }

    function imprimirA4Directo() {
        if (ultimaVentaExitosa && ultimaVentaExitosa.factura_id) {
            window.open(`{{ url('/factura') }}/${ultimaVentaExitosa.factura_id}/imprimir`, '_blank');
        }
    }

    // ==========================================
    // 7. CIERRE DE CAJA / ARQUEO Z
    // ==========================================
    function abrirModalCierre() {
        const modal = document.getElementById('cierreModal');
        const backdrop = document.getElementById('cierreBackdrop');
        const panel = document.getElementById('cierrePanel');
        const loader = document.getElementById('cierre_loader');
        const dataContainer = document.getElementById('cierre_data_container');

        loader.classList.remove('hidden');
        dataContainer.classList.add('hidden');
        document.getElementById('dinero_fisico_usd').value = '';
        document.getElementById('observaciones_cierre').value = '';

        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);

        fetch(`{{ url('/admin/pos/resumen-cierre') }}`)
        .then(res => res.json())
        .then(data => {
            ultimoResumenCierre = data;
            document.getElementById('cierre_cajero_txt').innerText = `Cajero: ${data.cajero_nombre} • ${data.caja_nombre}`;
            document.getElementById('cierre_kpi_base').innerText = `$${data.monto_inicial_usd.toFixed(2)}`;
            document.getElementById('cierre_kpi_ventas').innerText = `$${data.total_ventas_usd.toFixed(2)}`;
            document.getElementById('cierre_kpi_tickets').innerText = data.total_pedidos;
            document.getElementById('cierre_efectivo_esperado_txt').innerText = `$${data.efectivo_esperado_gaveta_usd.toFixed(2)}`;

            const tablaMetodos = document.getElementById('cierre_tabla_metodos');
            tablaMetodos.innerHTML = '';
            if (data.desglose_metodos && data.desglose_metodos.length > 0) {
                data.desglose_metodos.forEach(m => {
                    tablaMetodos.innerHTML += `
                        <div class="flex justify-between items-center p-2 border-b border-gray-100 text-xs">
                            <div>
                                <span class="font-bold text-gray-800">${m.metodo_label}</span>
                                <span class="text-[10px] text-gray-700 font-bold ml-1">(${m.cantidad} op.)</span>
                            </div>
                            <span class="font-black text-slate-800">$${m.total_usd.toFixed(2)}</span>
                        </div>
                    `;
                });
            } else {
                tablaMetodos.innerHTML = `<div class="p-3 text-center text-xs text-gray-700 italic font-bold">Sin operaciones en el turno</div>`;
            }

            calcularDiferenciaArqueo();
            loader.classList.add('hidden');
            dataContainer.classList.remove('hidden');
            document.getElementById('dinero_fisico_usd').focus();
        })
        .catch(err => {
            loader.innerHTML = `<p class="text-xs text-red-600 font-bold py-6">Error al cargar datos del turno.</p>`;
        });
    }

    function calcularDiferenciaArqueo() {
        if (!ultimoResumenCierre) return;
        const esperado = ultimoResumenCierre.efectivo_esperado_gaveta_usd;
        const contado = parseFloat(document.getElementById('dinero_fisico_usd').value) || 0;
        const dif = contado - esperado;

        const box = document.getElementById('cierre_diferencia_box');
        const txt = document.getElementById('cierre_diferencia_txt');

        if (document.getElementById('dinero_fisico_usd').value.trim() === '') {
            txt.innerText = '$0.00';
            txt.className = 'font-black text-sm text-gray-500';
            return;
        }

        if (Math.abs(dif) < 0.01) {
            txt.innerText = '✓ Cuadrada ($0.00)';
            txt.className = 'font-black text-sm text-emerald-600';
            box.className = 'p-2.5 bg-emerald-50 rounded-xl border border-emerald-300 flex justify-between items-center text-xs';
        } else if (dif > 0) {
            txt.innerText = `+$${dif.toFixed(2)} (Sobrante)`;
            txt.className = 'font-black text-sm text-blue-600';
            box.className = 'p-2.5 bg-blue-50 rounded-xl border border-blue-300 flex justify-between items-center text-xs';
        } else {
            txt.innerText = `-$${Math.abs(dif).toFixed(2)} (Faltante)`;
            txt.className = 'font-black text-sm text-red-600';
            box.className = 'p-2.5 bg-red-50 rounded-xl border border-red-300 flex justify-between items-center text-xs';
        }
    }

    function cerrarModalCierre() {
        const modal = document.getElementById('cierreModal');
        const backdrop = document.getElementById('cierreBackdrop');
        const panel = document.getElementById('cierrePanel');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 200);
    }

    function ejecutarCierreCaja() {
        const dineroFisico = document.getElementById('dinero_fisico_usd').value;
        const obs = document.getElementById('observaciones_cierre').value;

        if (dineroFisico.trim() === '') {
            Swal.fire('Campo Requerido', 'Debes ingresar el total del efectivo contado físicamente en tu gaveta.', 'warning');
            return;
        }

        const btn = document.getElementById('btnCerrarCaja');
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[15px]">autorenew</span> Finalizando...';

        fetch(`{{ url('/admin/pos/cerrar-caja') }}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ dinero_real_usd: dineroFisico, observaciones: obs })
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Error al cerrar caja');
            return data;
        })
        .then(data => {
            Swal.fire({ 
                title: '¡Turno Finalizado!', 
                text: 'La caja fue cerrada y el arqueo quedó registrado exitosamente.',
                icon: 'success', 
                timer: 2000, 
                showConfirmButton: false 
            }).then(() => {
                window.location.reload();
            });
        })
        .catch(err => {
            Swal.fire('Error', err.message, 'error');
            btn.disabled = false;
            btn.innerHTML = 'Finalizar Turno';
        });
    }

    // Sincronización en segundo plano al enfocar la pestaña del terminal
    window.addEventListener('focus', function() {
        actualizarCatalogoProductos(true);
    });
</script>
@endpush
@endsection