@extends('layouts.app')

@section('title', 'Historial de Pedidos - Agrosuministros Rodríguez')

@section('content')
<div class="bg-slate-50 min-h-screen font-sans pb-24 relative">

    {{-- Encabezado decorativo corporativo --}}
    <div class="relative bg-gradient-to-r from-[#0b1d38] via-[#1e549e] to-[#3492cf] h-48 lg:h-56 overflow-hidden">
        <div class="absolute inset-0 opacity-15 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute -bottom-12 -right-12 size-64 rounded-full bg-[#3492cf]/25 blur-3xl pointer-events-none"></div>

        <div class="container mx-auto px-4 sm:px-6 lg:px-8 pt-8 relative z-10">
            <div class="flex items-center gap-2 text-xs font-bold text-white/70 uppercase tracking-wider mb-2">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors">Inicio</a>
                <span>/</span>
                <a href="{{ route('perfil') }}" class="hover:text-white transition-colors">Mi Cuenta</a>
                <span>/</span>
                <span class="text-[#aad6f3]">Pedidos</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Historial de Compras</h1>
            <p class="text-xs sm:text-sm text-white/80 font-medium mt-1 max-w-xl">
                Monitorea el estado de tus órdenes en tiempo real, verifica pagos y descarga tus comprobantes.
            </p>
        </div>
    </div>

    {{-- Contenedor Principal --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 -mt-14 lg:-mt-16 relative z-20">
        
        <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 items-start">

            {{-- ASIDE: Menú Lateral Corporativo --}}
            @include('perfil.partials.sidebar', ['active' => 'pedidos'])

            {{-- MAIN: Lista de Pedidos --}}
            <main class="flex-1 min-w-0 w-full space-y-6">
                
                {{-- Barra Superior de Filtros y Búsqueda --}}
                <div class="bg-white rounded-3xl shadow-xs border border-slate-200/90 p-5 sm:p-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="size-11 rounded-2xl bg-[#e5f2fb] text-[#1e549e] flex items-center justify-center shrink-0 shadow-2xs">
                                <span class="material-symbols-outlined text-[22px]">receipt_long</span>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-black text-slate-900 leading-tight">
                                    Mis Órdenes de Compra
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Mostrando {{ $pedidos->total() }} {{ $pedidos->total() === 1 ? 'pedido registrado' : 'pedidos registrados' }}
                                </p>
                            </div>
                        </div>

                        {{-- Barra de Búsqueda Rápida --}}
                        <div class="relative w-full md:w-72">
                            <span class="material-symbols-outlined text-[18px] text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2">search</span>
                            <input type="text" 
                                   id="buscar-pedidos-input" 
                                   placeholder="Buscar orden o insumo..." 
                                   class="w-full h-10 pl-10 pr-4 text-xs font-semibold rounded-xl bg-slate-50 border border-slate-200 text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-[#3492cf] focus:ring-2 focus:ring-[#3492cf]/20 transition-all shadow-2xs">
                        </div>
                    </div>

                    {{-- Chips de Filtros de Estado --}}
                    <div class="flex items-center gap-1.5 overflow-x-auto custom-scrollbar pt-4 mt-4 border-t border-slate-100">
                        <button type="button" onclick="filtrarPedidosPorEstado('todos')" class="tab-filtro px-3.5 py-1.5 rounded-full text-xs font-black transition-all bg-[#1e549e] text-white shadow-2xs shrink-0 cursor-pointer" data-filter="todos">
                            Todos ({{ $pedidos->total() }})
                        </button>
                        <button type="button" onclick="filtrarPedidosPorEstado('pendiente')" class="tab-filtro px-3.5 py-1.5 rounded-full text-xs font-bold transition-all bg-slate-100 text-slate-600 hover:bg-slate-200 shrink-0 cursor-pointer" data-filter="pendiente">
                            Pendientes
                        </button>
                        <button type="button" onclick="filtrarPedidosPorEstado('pagado')" class="tab-filtro px-3.5 py-1.5 rounded-full text-xs font-bold transition-all bg-slate-100 text-slate-600 hover:bg-slate-200 shrink-0 cursor-pointer" data-filter="pagado">
                            Pagados / Verificados
                        </button>
                        <button type="button" onclick="filtrarPedidosPorEstado('en_ruta')" class="tab-filtro px-3.5 py-1.5 rounded-full text-xs font-bold transition-all bg-slate-100 text-slate-600 hover:bg-slate-200 shrink-0 cursor-pointer" data-filter="en_ruta">
                            En Despacho / Ruta
                        </button>
                        <button type="button" onclick="filtrarPedidosPorEstado('entregado')" class="tab-filtro px-3.5 py-1.5 rounded-full text-xs font-bold transition-all bg-slate-100 text-slate-600 hover:bg-slate-200 shrink-0 cursor-pointer" data-filter="entregado">
                            Entregados
                        </button>
                        <button type="button" onclick="filtrarPedidosPorEstado('cancelado')" class="tab-filtro px-3.5 py-1.5 rounded-full text-xs font-bold transition-all bg-slate-100 text-slate-600 hover:bg-slate-200 shrink-0 cursor-pointer" data-filter="cancelado">
                            Cancelados
                        </button>
                    </div>
                </div>

                {{-- BANNER INFORMATIVO: POLÍTICA DE DEVOLUCIONES --}}
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/80 rounded-2xl p-4 sm:p-5 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div class="flex items-start gap-3">
                        <div class="size-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
                            <span class="material-symbols-outlined text-[20px]">assignment_return</span>
                        </div>
                        <div>
                            <h4 class="font-black text-amber-950 text-xs">Información sobre Cambios y Devoluciones</h4>
                            <p class="text-amber-900/80 text-[11px] mt-0.5 leading-relaxed">
                                Para compras web y pedidos con delivery: las devoluciones y reclamos de garantía se gestionan presencialmente en nuestra tienda física con tu comprobante y el producto para su verificación.
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center justify-center sm:justify-end gap-2 w-full sm:w-auto shrink-0 pt-1 sm:pt-0">
                        <a href="{{ \App\Models\ConfiguracionTienda::whatsappUrl('Hola, deseo consultar sobre una devolución o garantía de mi pedido.') }}" 
                           target="_blank" 
                           class="inline-flex items-center justify-center gap-1.5 px-4 py-2 sm:px-3 sm:py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-2xs transition-colors">
                            <span class="material-symbols-outlined text-[15px]">chat</span>
                            <span>WhatsApp Tienda</span>
                        </a>
                        @if(\App\Models\ConfiguracionTienda::telefono())
                        <a href="tel:{{ \App\Models\ConfiguracionTienda::telefono() }}" 
                           class="inline-flex items-center justify-center gap-1.5 px-4 py-2 sm:px-3 sm:py-1.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold text-xs shadow-2xs transition-colors">
                            <span class="material-symbols-outlined text-[15px]">call</span>
                            <span>Llamar: {{ \App\Models\ConfiguracionTienda::telefono() }}</span>
                        </a>
                        @endif
                    </div>
                </div>

                {{-- ALERTA DE ÉXITO DE SESIÓN --}}
                @if(session('success'))
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200/90 text-emerald-800 shadow-2xs flex items-center gap-3 font-semibold text-xs sm:text-sm">
                        <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                {{-- LISTADO DE PEDIDOS --}}
                @if($pedidos->count() > 0)
                    <div class="space-y-4" id="contenedor-pedidos">
                        @foreach($pedidos as $pedido)
                            @php
                                $estadoPill = match($pedido->estado) {
                                    'pendiente' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200/80', 'icono' => 'hourglass_empty', 'texto' => 'Pendiente de Pago'],
                                    'pagado', 'aprobado', 'completado_caja' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80', 'icono' => 'verified', 'texto' => 'Pago Verificado'],
                                    'revision' => ['bg' => 'bg-sky-50 text-sky-700 border-sky-200/80', 'icono' => 'search', 'texto' => 'En Verificación'],
                                    'en_ruta' => ['bg' => 'bg-purple-50 text-purple-700 border-purple-200/80', 'icono' => 'local_shipping', 'texto' => 'En Ruta de Entrega'],
                                    'preparacion' => ['bg' => 'bg-indigo-50 text-indigo-700 border-indigo-200/80', 'icono' => 'inventory', 'texto' => 'En Preparación'],
                                    'entregado' => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200', 'icono' => 'package_2', 'texto' => 'Entregado con Éxito'],
                                    'rechazado', 'cancelado', 'devuelto' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200/80', 'icono' => 'cancel', 'texto' => 'Cancelado / Rechazado'],
                                    default => ['bg' => 'bg-slate-50 text-slate-600 border-slate-200', 'icono' => 'info', 'texto' => ucfirst(str_replace('_', ' ', $pedido->estado))]
                                };

                                $metodoPago = $pedido->pago ? $pedido->pago->metodo : null;
                                $iconoMetodo = match($metodoPago) {
                                    'pago_movil' => 'phone_iphone',
                                    'zelle' => 'payments',
                                    'transferencia' => 'account_balance',
                                    'efectivo_usd', 'efectivo_bs' => 'attach_money',
                                    'binance' => 'currency_bitcoin',
                                    'punto_venta', 'biopago' => 'point_of_sale',
                                    default => 'payments'
                                };

                                $esDelivery = $pedido->es_delivery;
                            @endphp

                            <article class="pedido-card bg-white rounded-3xl shadow-xs border border-slate-200/90 overflow-hidden hover:border-[#3492cf]/60 hover:shadow-sm transition-all group" 
                                     data-estado="{{ $pedido->estado }}" 
                                     data-id="{{ $pedido->id }}">
                                
                                {{-- Cabecera de la Orden --}}
                                <div class="bg-slate-50/70 px-5 sm:px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <div class="flex items-center gap-2">
                                            <span class="size-2 rounded-full bg-[#1e549e]"></span>
                                            <span class="font-mono font-black text-slate-900 text-base sm:text-lg">
                                                #{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </div>

                                        <span class="text-slate-300">|</span>

                                        <div class="text-xs text-slate-500 font-medium flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-[16px] text-slate-400">calendar_month</span>
                                            <span>{{ \Carbon\Carbon::parse($pedido->creado_at)->format('d M Y, h:i A') }}</span>
                                        </div>

                                        {{-- Badge Delivery / Pickup --}}
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full {{ $esDelivery ? 'text-[#1e549e] bg-[#e5f2fb]' : 'text-emerald-700 bg-emerald-50' }}">
                                            <span class="material-symbols-outlined text-[13px]">{{ $esDelivery ? 'local_shipping' : 'storefront' }}</span>
                                            <span>{{ $esDelivery ? 'Delivery' : 'Retiro en Tienda' }}</span>
                                        </span>
                                    </div>

                                    {{-- Estado del Pedido --}}
                                    <div class="self-start sm:self-auto inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider border {{ $estadoPill['bg'] }} shadow-2xs">
                                        <span class="material-symbols-outlined text-[16px]">{{ $estadoPill['icono'] }}</span>
                                        <span>{{ $estadoPill['texto'] }}</span>
                                    </div>
                                </div>

                                {{-- Contenido de la Orden --}}
                                <div class="p-5 sm:p-6">
                                    <div class="flex flex-col lg:flex-row gap-6 lg:items-center justify-between">
                                        
                                        {{-- Lista Resumida de Productos --}}
                                        <div class="flex-1 space-y-3 min-w-0">
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                @foreach($pedido->detalles->take(2) as $detalle)
                                                    <div class="flex items-center gap-3 p-2.5 rounded-2xl bg-slate-50/80 border border-slate-100 group-hover:bg-[#e5f2fb]/20 transition-colors">
                                                        <div class="size-14 rounded-xl bg-white border border-slate-200 overflow-hidden shrink-0 relative flex items-center justify-center">
                                                            @if($detalle->producto && $detalle->producto->imagen_url)
                                                                <img src="{{ asset($detalle->producto->imagen_url) }}" 
                                                                     alt="{{ $detalle->producto->nombre ?? 'Insumo' }}"
                                                                     class="size-full object-cover">
                                                            @else
                                                                <span class="material-symbols-outlined text-slate-300 text-[22px]">inventory_2</span>
                                                            @endif
                                                            <span class="absolute top-0.5 right-0.5 size-4 rounded-full bg-[#1e549e] text-white text-[9px] font-black flex items-center justify-center">
                                                                {{ (int)$detalle->cantidad_solicitada }}
                                                            </span>
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <h5 class="font-bold text-xs sm:text-sm text-slate-800 truncate leading-snug">
                                                                {{ $detalle->producto ? $detalle->producto->nombre : 'Insumo Agropecuario' }}
                                                            </h5>
                                                            <p class="text-[11px] font-semibold text-slate-400 mt-0.5">
                                                                ${{ number_format($detalle->precio_historico_usd, 2) }} c/u
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            @if($pedido->detalles->count() > 2)
                                                <div class="pt-1">
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-[#1e549e] bg-[#e5f2fb] px-2.5 py-0.5 rounded-full">
                                                        <span class="material-symbols-outlined text-[14px]">more_horiz</span>
                                                        +{{ $pedido->detalles->count() - 2 }} artículo(s) más en esta orden
                                                    </span>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Panel Financiero y Acciones --}}
                                        <div class="lg:w-80 shrink-0 lg:pl-6 lg:border-l lg:border-slate-100 flex flex-col justify-between gap-4">
                                            <div>
                                                <div class="flex items-baseline justify-between mb-1">
                                                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Orden</span>
                                                    <div class="text-right">
                                                        <span class="text-xl sm:text-2xl font-black text-[#1e549e] leading-none">
                                                            ${{ number_format($pedido->total_usd, 2) }}
                                                        </span>
                                                        <span class="text-[10px] font-bold text-slate-400 uppercase ml-1">USD</span>
                                                    </div>
                                                </div>

                                                <div class="flex items-center justify-between text-xs text-slate-600 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-100">
                                                    <span class="text-[10px] font-black uppercase text-slate-400">Equivalente BCV</span>
                                                    <span class="font-bold text-slate-800">
                                                        Bs. {{ number_format($pedido->total_ves_calculado, 2, ',', '.') }}
                                                    </span>
                                                </div>

                                                @if($pedido->pago)
                                                    <div class="mt-2.5 flex items-center justify-between text-xs text-slate-600 px-1">
                                                        <span class="flex items-center gap-1.5 font-semibold text-slate-700 truncate">
                                                            <span class="material-symbols-outlined text-[16px] text-[#1e549e]">{{ $iconoMetodo }}</span>
                                                            <span class="truncate">{{ ucfirst(str_replace('_', ' ', $pedido->pago->metodo)) }}</span>
                                                        </span>
                                                        @if($pedido->pago->referencia_bancaria)
                                                            <span class="font-mono text-[11px] font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">
                                                                Ref: {{ Str::limit($pedido->pago->referencia_bancaria, 10) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Botones de Acción --}}
                                            <div class="pt-2">
                                                <a href="{{ route('perfil.pedido.detalle', $pedido->id) }}" 
                                                   class="w-full py-2.5 px-4 bg-[#1e549e] hover:bg-[#3492cf] text-white font-bold text-xs rounded-xl shadow-xs transition-all flex items-center justify-center gap-1.5 active:scale-95 group/btn">
                                                    <span>Ver Detalle</span>
                                                    <span class="material-symbols-outlined text-[16px] group-hover/btn:translate-x-0.5 transition-transform">arrow_forward</span>
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    {{-- Paginación Elegante --}}
                    <div class="pt-6 flex justify-center">
                        {{ $pedidos->links() }}
                    </div>

                @else
                    {{-- Estado Vacío --}}
                    <div class="bg-white rounded-3xl shadow-xs border-2 border-dashed border-slate-200 p-12 text-center">
                        <div class="size-20 bg-[#e5f2fb] text-[#1e549e] rounded-full flex items-center justify-center mx-auto mb-4 shadow-2xs">
                            <span class="material-symbols-outlined text-[40px]">shopping_bag</span>
                        </div>
                        <h3 class="text-xl font-black text-slate-900 mb-1.5">No tienes pedidos registrados</h3>
                        <p class="text-xs sm:text-sm text-slate-500 max-w-sm mx-auto mb-6 leading-relaxed">
                            Aún no has realizado ninguna compra en nuestra plataforma. Explora el catálogo de insumos agrícolas y haz tu primer pedido.
                        </p>
                        <a href="{{ route('catalogo') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-[#1e549e] hover:bg-[#3492cf] text-white font-black text-xs sm:text-sm shadow-xs transition-all active:scale-95">
                            <span class="material-symbols-outlined text-[18px]">storefront</span>
                            <span>Explorar Catálogo</span>
                        </a>
                    </div>
                @endif

            </main>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Filtro interactivo en cliente de pedidos
    function filtrarPedidosPorEstado(estado) {
        // Actualizar estilos de tabs
        document.querySelectorAll('.tab-filtro').forEach(btn => {
            if (btn.dataset.filter === estado) {
                btn.classList.remove('bg-slate-100', 'text-slate-600');
                btn.classList.add('bg-[#1e549e]', 'text-white', 'shadow-2xs');
            } else {
                btn.classList.remove('bg-[#1e549e]', 'text-white', 'shadow-2xs');
                btn.classList.add('bg-slate-100', 'text-slate-600');
            }
        });

        // Filtrar tarjetas
        const cards = document.querySelectorAll('.pedido-card');
        cards.forEach(card => {
            const cardEstado = card.dataset.estado;
            if (estado === 'todos') {
                card.style.display = 'block';
            } else if (estado === 'pagado' && ['pagado', 'aprobado', 'completado_caja'].includes(cardEstado)) {
                card.style.display = 'block';
            } else if (estado === 'en_ruta' && ['en_ruta', 'preparacion', 'revision'].includes(cardEstado)) {
                card.style.display = 'block';
            } else if (estado === 'cancelado' && ['cancelado', 'rechazado', 'devuelto'].includes(cardEstado)) {
                card.style.display = 'block';
            } else if (cardEstado === estado) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Buscador interactivo
    document.getElementById('buscar-pedidos-input')?.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        const cards = document.querySelectorAll('.pedido-card');
        
        cards.forEach(card => {
            const text = card.innerText.toLowerCase();
            if (text.includes(query)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>
@endpush
@endsection