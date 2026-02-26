@extends('layouts.app')

@section('title', 'Mis Pedidos - Corpo Agrícola')

@section('content')
<div class="bg-gray-50 min-h-screen font-sans pb-20 relative">

    {{-- Encabezado decorativo (Idéntico a Perfil) --}}
    <div class="relative bg-agro-dark h-40 lg:h-48 overflow-hidden transition-all">
        <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute -bottom-10 -right-10 w-40 h-40 lg:w-64 lg:h-64 rounded-full bg-primary/20 blur-3xl animate-pulse-slow"></div>
    </div>

    {{-- Contenedor Principal con layout-container --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 -mt-10 lg:-mt-16 relative z-10">
        
        <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">

            {{-- ASIDE: Menú Lateral (Sincronizado con Perfil) --}}
            <aside class="w-full lg:w-80 xl:w-80 flex-shrink-0">
                <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.06)] border border-gray-100 overflow-hidden lg:sticky lg:top-24 transition-all">
                    
                    {{-- Perfil Header --}}
                    <div class="p-6 lg:p-8 text-center bg-white relative">
                        <div class="absolute top-0 left-0 w-full h-16 bg-gray-50/50 rounded-t-3xl"></div>
                        
                        <div class="relative w-24 h-24 lg:w-28 lg:h-28 mx-auto bg-primary/10 text-primary rounded-full flex items-center justify-center text-3xl lg:text-4xl font-black mb-4 border-4 border-white shadow-sm">
                            {{ substr(auth()->user()->nombre, 0, 1) }}{{ substr(auth()->user()->apellido, 0, 1) }}
                        </div>
                        
                        <h2 class="text-xl lg:text-2xl font-black text-agro-dark mb-1 truncate leading-tight">
                            {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}
                        </h2>
                        <p class="text-sm text-gray-500 font-medium mb-4 truncate">{{ auth()->user()->email }}</p>
                        
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-50 border border-gray-100 text-gray-600 text-[11px] font-bold uppercase tracking-wider shadow-sm">
                            @if(auth()->user()->tipo_cliente === 'juridico')
                                <span class="material-symbols-outlined text-[16px] text-primary">domain</span> Empresa
                            @elseif(auth()->user()->tipo_cliente === 'finca_productor')
                                <span class="material-symbols-outlined text-[16px] text-primary">agriculture</span> Productor
                            @else
                                <span class="material-symbols-outlined text-[16px] text-primary">person</span> Particular
                            @endif
                        </div>
                    </div>

                    {{-- Menú de Navegación --}}
                    <div class="border-t border-gray-100 bg-white p-3">
                        <nav class="flex flex-col space-y-1">
                            {{-- Enlaces hacia Perfil --}}
                            <a href="{{ route('perfil') }}#datos-personales" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-agro-dark font-medium transition-all group">
                                <span class="material-symbols-outlined text-[22px] text-gray-400 group-hover:text-agro-dark transition-colors">person</span>
                                Información Personal
                            </a>
                            
                            <a href="{{ route('perfil') }}#direcciones" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-agro-dark font-medium transition-all group">
                                <span class="material-symbols-outlined text-[22px] text-gray-400 group-hover:text-agro-dark transition-colors">location_on</span>
                                Direcciones de Envío
                            </a>

                            <a href="{{ route('perfil') }}#seguridad" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-agro-dark font-medium transition-all group">
                                <span class="material-symbols-outlined text-[22px] text-gray-400 group-hover:text-agro-dark transition-colors">lock</span>
                                Seguridad y Contraseña
                            </a>

                            <div class="h-px bg-gray-100 my-2 mx-4"></div>
                            
                            {{-- Elemento Activo --}}
                            <a href="{{ route('perfil.pedidos') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-primary/5 text-primary font-bold transition-all group">
                                <span class="material-symbols-outlined text-[22px]">receipt_long</span>
                                Historial de Pedidos
                            </a>
                        </nav>
                    </div>
                </div>
            </aside>

            {{-- MAIN: Lista de Pedidos --}}
            <main class="flex-1 min-w-0 animate-fade-in-up">
                
                {{-- Cabecera del Main --}}
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-6 flex items-center justify-between">
                    <h3 class="text-xl lg:text-2xl font-black text-agro-dark flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-[32px]">history_edu</span>
                        Mis Pedidos
                    </h3>
                </div>

                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-800 px-5 py-4 rounded-2xl mb-6 flex items-center gap-3 font-medium shadow-sm">
                        <span class="material-symbols-outlined text-green-600">check_circle</span>
                        {{ session('success') }}
                    </div>
                @endif

                @if($pedidos->count() > 0)
                    <div class="space-y-6">
                        @foreach($pedidos as $pedido)
                            @php
                                $estadoColor = match($pedido->estado) {
                                    'pendiente' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'pagado', 'aprobado', 'completado_caja' => 'bg-green-50 text-green-700 border-green-200',
                                    'revision' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'rechazado', 'cancelado', 'devuelto' => 'bg-red-50 text-red-700 border-red-200',
                                    'en_ruta', 'preparacion' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    'entregado' => 'bg-gray-100 text-gray-800 border-gray-300',
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

                            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all group">
                                
                                {{-- Cabecera del Pedido --}}
                                <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-4">
                                        <div class="bg-white px-4 py-2 rounded-xl border border-gray-200 shadow-sm text-center min-w-[80px]">
                                            <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider">Orden</span>
                                            <span class="block text-lg font-black text-agro-dark">#{{ $pedido->id }}</span>
                                        </div>
                                        <div>
                                            <p class="text-[11px] text-gray-500 font-bold uppercase flex items-center gap-1.5 mb-0.5">
                                                <span class="material-symbols-outlined text-[14px]">calendar_month</span> Fecha de compra
                                            </p>
                                            <p class="text-sm font-bold text-gray-800">{{ \Carbon\Carbon::parse($pedido->creado_at)->format('d M Y, h:i A') }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="px-3 py-1.5 rounded-lg border {{ $estadoColor }} flex items-center gap-1.5 shadow-sm self-start sm:self-auto">
                                        <span class="material-symbols-outlined text-[18px]">{{ $estadoIcono }}</span>
                                        <span class="text-[11px] font-black uppercase tracking-wider">{{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}</span>
                                    </div>
                                </div>

                                {{-- Cuerpo del Pedido --}}
                                <div class="p-6">
                                    <div class="flex flex-col lg:flex-row gap-8">
                                        
                                        {{-- Lista de Productos (Resumida) --}}
                                        <div class="flex-1">
                                            <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 pb-2 mb-4">
                                                Artículos del Pedido
                                            </h4>
                                            
                                            <div class="space-y-3">
                                                @foreach($pedido->detalles->take(3) as $detalle)
                                                    <div class="flex items-center gap-4 group/item">
                                                        <div class="w-14 h-14 bg-gray-50 border border-gray-100 overflow-hidden flex-shrink-0 relative">
                                                            @if($detalle->producto && $detalle->producto->imagen_url)
                                                                <img src="{{ asset($detalle->producto->imagen_url) }}" 
                                                                    alt="{{ $detalle->producto->nombre ?? 'Producto' }}"
                                                                    class="w-full h-full object-cover">
                                                            @else
                                                                <div class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                                                                    <span class="material-symbols-outlined text-[20px]">image</span>
                                                                    <span class="text-[8px] font-bold uppercase tracking-widest">Sin Imagen</span>
                                                                </div>
                                                            @endif
                                                            <div class="absolute -top-1 -right-1 bg-agro-dark text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-white shadow-sm">
                                                                {{ (int)$detalle->cantidad_solicitada }}
                                                            </div>
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <p class="text-sm font-bold text-gray-800 truncate group-hover/item:text-primary transition-colors">
                                                                {{ $detalle->producto ? $detalle->producto->nombre : 'Producto Eliminado' }}
                                                            </p>
                                                            <p class="text-xs text-gray-500 font-medium mt-0.5">
                                                                ${{ number_format($detalle->precio_historico_usd, 2) }} <span class="text-[10px] uppercase">c/u</span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            
                                            @if($pedido->detalles->count() > 3)
                                                <div class="mt-4">
                                                    <span class="inline-block text-xs text-primary font-bold bg-primary/10 px-3 py-1.5 rounded-lg">
                                                        + {{ $pedido->detalles->count() - 3 }} artículos más
                                                    </span>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Info de Pago y Totales --}}
                                        <div class="w-full lg:w-72 flex-shrink-0 bg-gray-50 rounded-2xl p-5 border border-gray-100 flex flex-col justify-between shadow-inner">
                                            <div class="space-y-4">
                                                
                                                <div class="flex justify-between items-end">
                                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total USD</p>
                                                    <p class="text-2xl font-black text-agro-dark leading-none">${{ number_format($pedido->total_usd, 2) }}</p>
                                                </div>
                                                
                                                <div class="flex justify-between items-end border-b border-gray-200 pb-4">
                                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total VES</p>
                                                    <p class="text-sm font-bold text-gray-600">Bs {{ number_format($pedido->total_ves_calculado, 2) }}</p>
                                                </div>

                                                <div>
                                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Método de Pago</p>
                                                    @if($pedido->pago)
                                                        <div class="flex items-center gap-2 text-sm font-bold text-agro-dark bg-white px-3 py-2.5 rounded-xl border border-gray-200 shadow-sm">
                                                            @php
                                                                $iconoPago = match($pedido->pago->metodo) {
                                                                    'pago_movil' => 'phone_iphone',
                                                                    'zelle' => 'attach_money',
                                                                    'efectivo_usd', 'efectivo_bs' => 'payments',
                                                                    'transferencia' => 'account_balance',
                                                                    default => 'credit_card'
                                                                };
                                                            @endphp
                                                            <span class="material-symbols-outlined text-[20px] text-primary">{{ $iconoPago }}</span>
                                                            <span class="truncate">{{ ucfirst(str_replace('_', ' ', $pedido->pago->metodo)) }}</span>
                                                        </div>
                                                        @if($pedido->pago->referencia_bancaria)
                                                            <p class="text-[11px] text-gray-500 mt-1.5 pl-1 flex items-center gap-1 font-medium">
                                                                <span class="material-symbols-outlined text-[14px]">tag</span> 
                                                                Ref: {{ Str::limit($pedido->pago->referencia_bancaria, 15) }}
                                                            </p>
                                                        @endif
                                                    @else
                                                        <div class="flex items-center gap-2 text-sm font-bold text-red-700 bg-red-50 px-3 py-2.5 rounded-xl border border-red-100 shadow-sm">
                                                            <span class="material-symbols-outlined text-[20px]">warning</span>
                                                            Sin pago registrado
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            {{-- Botón de Acción --}}
                                            <a href="{{ route('perfil.pedido.detalle', $pedido->id) }}" class="w-full mt-6 py-3 bg-white border border-gray-200 text-agro-dark font-bold rounded-xl text-sm hover:bg-primary hover:text-white hover:border-primary transition-all flex items-center justify-center gap-2 shadow-sm group/btn">
                                                <span class="material-symbols-outlined text-[18px] group-hover/btn:scale-110 transition-transform">visibility</span> 
                                                Ver Detalle
                                            </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Paginación --}}
                        <div class="mt-10 flex justify-center">
                            {{ $pedidos->links('pagination::tailwind') }} 
                        </div>

                    </div>
                @else
                    {{-- Estado Vacío (Rediseñado) --}}
                    <div class="flex flex-col items-center justify-center py-20 bg-white rounded-3xl shadow-sm border border-dashed border-gray-300 text-center">
                        <div class="relative mb-6">
                            <div class="absolute inset-0 bg-gray-100 rounded-full blur-xl"></div>
                            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center relative z-10 border-4 border-white shadow-sm">
                                <span class="material-symbols-outlined text-[48px] text-gray-300">shopping_bag</span>
                            </div>
                        </div>
                        <h3 class="text-2xl font-black text-agro-dark mb-2">Aún no tienes pedidos</h3>
                        <p class="text-gray-500 max-w-sm mx-auto mb-8 font-medium text-sm leading-relaxed">
                            Parece que aún no has realizado ninguna compra. Explora nuestro catálogo y encuentra los mejores insumos para tu campo.
                        </p>
                        <a href="{{ route('catalogo') }}" class="px-8 py-3.5 bg-primary text-agro-dark rounded-xl font-black hover:bg-primary/90 transition-all shadow-lg shadow-primary/30 flex items-center gap-2 transform hover:-translate-y-1">
                            <span class="material-symbols-outlined">storefront</span>
                            Explorar Catálogo
                        </a>
                    </div>
                @endif

            </main>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Si no las tienes de forma global, puedes dejarlas aquí */
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
@endpush
@endsection