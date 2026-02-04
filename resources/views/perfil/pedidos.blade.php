@extends('layouts.app')

@section('title', 'Mis Pedidos - Agropecuaria Venezuela')

@section('content')
<div class="bg-gray-50 min-h-screen font-sans pb-20 relative animate-fade-in-up">

    {{-- Encabezado decorativo --}}
    <div class="relative bg-agro-dark h-40 lg:h-48 overflow-hidden transition-all">
        <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute -bottom-10 -right-10 w-40 h-40 lg:w-64 lg:h-64 rounded-full bg-primary/20 blur-3xl animate-pulse-slow"></div>
        
        <div class="container mx-auto px-4 relative z-10 h-full flex items-center">
            <h1 class="text-3xl lg:text-4xl font-black text-white tracking-tight drop-shadow-md flex items-center gap-3">
                <span class="material-symbols-outlined text-4xl">history_edu</span>
                Historial de Pedidos
            </h1>
        </div>
    </div>

    {{-- Contenedor Principal --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 -mt-10 lg:-mt-16 relative z-10">
        
        <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">

            {{-- ASIDE: Menú Lateral (Estandarizado) --}}
            <aside class="w-full lg:w-72 flex-shrink-0">
                <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden lg:sticky lg:top-6">
                    
                    {{-- User Mini Info --}}
                    <div class="p-6 bg-gradient-to-b from-white to-gray-50 text-center border-b border-gray-100">
                        <div class="w-16 h-16 mx-auto bg-primary/10 text-primary rounded-full flex items-center justify-center text-2xl font-black mb-3">
                            {{ substr(auth()->user()->nombre, 0, 1) }}{{ substr(auth()->user()->apellido, 0, 1) }}
                        </div>
                        <h2 class="font-bold text-gray-800">{{ auth()->user()->nombre }}</h2>
                        <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                    </div>

                    <nav class="flex flex-col p-2">
                        <a href="{{ route('perfil') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-primary transition-all font-medium group">
                            <span class="material-symbols-outlined text-xl">person</span>
                            Mi Perfil
                        </a>
                        
                        <a href="{{ route('perfil') }}#direcciones" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-primary transition-all font-medium group">
                            <span class="material-symbols-outlined text-xl">location_on</span>
                            Direcciones
                        </a>

                        <a href="{{ route('perfil') }}#seguridad" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-primary transition-all font-medium group">
                            <span class="material-symbols-outlined text-xl">lock</span>
                            Seguridad
                        </a>

                        {{-- MODIFICADO: Enlace normal, sin estilos de "activo" fijo --}}
                        <a href="{{ route('perfil.pedidos') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-primary transition-all font-medium group">
                            <span class="material-symbols-outlined text-xl">receipt_long</span>
                            Mis Pedidos
                        </a>
                        
                        <div class="h-px bg-gray-100 my-2"></div>
                        
                        <a href="{{ route('catalogo') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-primary transition-all font-medium group">
                            <span class="material-symbols-outlined text-xl">store</span>
                            Ir al Catálogo
                        </a>
                    </nav>
                </div>
            </aside>

            {{-- MAIN: Lista de Pedidos --}}
            <main class="flex-1 min-w-0">

                @if(session('success'))
                    <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-2xl mb-6 flex items-center gap-2 animate-fade-in-up">
                        <span class="material-symbols-outlined">check_circle</span>
                        {{ session('success') }}
                    </div>
                @endif

                @if($pedidos->count() > 0)
                    <div class="space-y-6">
                        @foreach($pedidos as $pedido)
                            @php
                                $estadoColor = match($pedido->estado) {
                                    'pendiente' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'pagado', 'aprobado', 'completado_caja' => 'bg-green-100 text-green-800 border-green-200',
                                    'revision' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'rechazado', 'cancelado', 'devuelto' => 'bg-red-100 text-red-800 border-red-200',
                                    'en_ruta', 'preparacion' => 'bg-purple-100 text-purple-800 border-purple-200',
                                    'entregado' => 'bg-gray-100 text-gray-800 border-gray-200',
                                    default => 'bg-gray-50 text-gray-600 border-gray-200'
                                };
                                $estadoIcono = match($pedido->estado) {
                                    'pendiente' => 'hourglass_empty',
                                    'pagado', 'aprobado', 'completado_caja' => 'verified',
                                    'revision' => 'search',
                                    'rechazado', 'cancelado', 'devuelto' => 'cancel',
                                    'en_ruta' => 'local_shipping',
                                    'entregado' => 'package_2',
                                    'preparacion' => 'inventory',
                                    default => 'info'
                                };
                            @endphp

                            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all">
                                
                                {{-- Cabecera del Pedido --}}
                                <div class="bg-gray-50/80 px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
                                    <div class="flex items-center gap-4">
                                        <div class="bg-white p-2 rounded-xl border border-gray-200 shadow-sm text-center min-w-[60px]">
                                            <span class="block text-[10px] text-gray-400 font-bold uppercase">Pedido</span>
                                            <span class="block text-lg font-black text-agro-dark">#{{ $pedido->id }}</span>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 font-bold uppercase flex items-center gap-1">
                                                <span class="material-symbols-outlined text-sm">calendar_month</span> Fecha
                                            </p>
                                            <p class="text-sm font-medium text-gray-800">{{ \Carbon\Carbon::parse($pedido->creado_at)->format('d M Y, h:i A') }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="px-3 py-1.5 rounded-xl border {{ $estadoColor }} flex items-center gap-1.5 shadow-sm">
                                        <span class="material-symbols-outlined text-lg">{{ $estadoIcono }}</span>
                                        <span class="text-xs font-black uppercase tracking-wide">{{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}</span>
                                    </div>
                                </div>

                                {{-- Cuerpo del Pedido --}}
                                <div class="p-6">
                                    <div class="flex flex-col lg:flex-row gap-6">
                                        
                                        {{-- Lista de Productos (Resumida) --}}
                                        <div class="flex-1 space-y-4">
                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 pb-2 mb-2">Detalle de Artículos</h4>
                                            
                                            <div class="space-y-3">
                                                @foreach($pedido->detalles->take(3) as $detalle)
                                                    <div class="flex items-start gap-3">
                                                        <div class="w-12 h-12 rounded-lg bg-gray-50 border border-gray-100 overflow-hidden flex-shrink-0 relative">
                                                            @if($detalle->producto && $detalle->producto->imagenes->first())
                                                                <img src="{{ $detalle->producto->imagenes->first()->url_imagen }}" class="w-full h-full object-cover">
                                                            @else
                                                                <div class="w-full h-full flex items-center justify-center text-gray-300"><span class="material-symbols-outlined text-lg">image</span></div>
                                                            @endif
                                                            <div class="absolute bottom-0 right-0 bg-agro-dark text-white text-[9px] font-bold px-1 rounded-tl-md">x{{ (int)$detalle->cantidad_solicitada }}</div>
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <p class="text-sm font-bold text-gray-800 truncate">{{ $detalle->producto ? $detalle->producto->nombre : 'Producto Eliminado' }}</p>
                                                            <p class="text-xs text-gray-500 font-medium">
                                                                ${{ number_format($detalle->precio_historico_usd, 2) }} c/u
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            
                                            @if($pedido->detalles->count() > 3)
                                                <div class="text-center pt-2">
                                                    <span class="text-xs text-primary font-bold bg-primary/5 px-3 py-1 rounded-full cursor-help" title="Ver detalle completo">
                                                        + {{ $pedido->detalles->count() - 3 }} artículos más
                                                    </span>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Info de Pago y Totales --}}
                                        <div class="lg:w-72 flex-shrink-0 bg-gray-50 rounded-2xl p-5 border border-gray-100 flex flex-col justify-between">
                                            <div class="space-y-4">
                                                <div class="flex justify-between items-end">
                                                    <p class="text-xs font-bold text-gray-500 uppercase">Total USD</p>
                                                    <p class="text-xl font-black text-agro-dark">${{ number_format($pedido->total_usd, 2) }}</p>
                                                </div>
                                                
                                                <div class="flex justify-between items-end border-b border-gray-200 pb-4">
                                                    <p class="text-xs font-bold text-gray-500 uppercase">Total VES</p>
                                                    <p class="text-sm font-bold text-gray-600">Bs {{ number_format($pedido->total_ves_calculado, 2) }}</p>
                                                </div>

                                                <div>
                                                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Método de Pago</p>
                                                    @if($pedido->pago)
                                                        <div class="flex items-center gap-2 text-sm font-bold text-gray-700 bg-white px-3 py-2 rounded-lg border border-gray-200 shadow-sm">
                                                            @php
                                                                $iconoPago = match($pedido->pago->metodo) {
                                                                    'pago_movil' => 'phone_iphone',
                                                                    'zelle' => 'attach_money',
                                                                    'efectivo_usd', 'efectivo_bs' => 'payments',
                                                                    'transferencia' => 'account_balance',
                                                                    default => 'credit_card'
                                                                };
                                                            @endphp
                                                            <span class="material-symbols-outlined text-primary">{{ $iconoPago }}</span>
                                                            <span class="truncate">{{ ucfirst(str_replace('_', ' ', $pedido->pago->metodo)) }}</span>
                                                        </div>
                                                        @if($pedido->pago->referencia_bancaria)
                                                            <p class="text-[10px] text-gray-400 mt-1 pl-1">Ref: {{ Str::limit($pedido->pago->referencia_bancaria, 15) }}</p>
                                                        @endif
                                                    @else
                                                        <span class="text-xs text-red-500 font-bold flex items-center gap-1">
                                                            <span class="material-symbols-outlined text-sm">warning</span> Sin pago registrado
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            {{-- Botón de Acción Activado --}}
                                            <a href="{{ route('perfil.pedido.detalle', $pedido->id) }}" class="w-full mt-4 py-2 bg-white border border-gray-300 text-gray-600 font-bold rounded-xl text-xs hover:bg-primary hover:text-white hover:border-primary transition-all flex items-center justify-center gap-1">
                                                <span class="material-symbols-outlined text-sm">visibility</span> Ver Detalle Completo
                                            </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Paginación --}}
                        <div class="mt-8">
                            {{ $pedidos->links() }} 
                        </div>

                    </div>
                @else
                    {{-- Estado Vacío --}}
                    <div class="flex flex-col items-center justify-center py-20 bg-white rounded-3xl shadow-sm border border-dashed border-gray-300 text-center animate-fade-in-up">
                        <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                            <span class="material-symbols-outlined text-5xl text-gray-300">shopping_bag</span>
                        </div>
                        <h3 class="text-2xl font-black text-gray-800 mb-2">Aún no tienes pedidos</h3>
                        <p class="text-gray-500 max-w-sm mx-auto mb-8 font-medium">Parece que aún no has realizado ninguna compra. Explora nuestro catálogo y encuentra los mejores insumos.</p>
                        <a href="{{ route('catalogo') }}" class="px-8 py-3.5 bg-agro-dark text-white rounded-xl font-bold hover:bg-primary transition-all shadow-lg shadow-agro-dark/20 flex items-center gap-2">
                            <span class="material-symbols-outlined">storefront</span>
                            Ir al Catálogo
                        </a>
                    </div>
                @endif

            </main>
        </div>
    </div>
</div>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.5s ease-out forwards;
    }
    @keyframes pulseSlow {
        0%, 100% { opacity: 0.3; transform: scale(1); }
        50% { opacity: 0.15; transform: scale(1.1); }
    }
    .animate-pulse-slow {
        animation: pulseSlow 8s infinite ease-in-out;
    }
</style>
@endsection