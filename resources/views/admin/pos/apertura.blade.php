@extends('layouts.admin')
@section('title', 'Apertura de Caja - Agrosuministros Rodríguez')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">
    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col h-screen justify-center items-center p-4 overflow-y-auto">
        
        <div class="w-full max-w-md bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden animate-fade-in-up">
            
            <div class="bg-gradient-to-r from-[#0b1d38] to-[#1e549e] p-6 text-center relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                <span class="material-symbols-outlined text-white text-5xl mb-2 relative z-10">point_of_sale</span>
                <h2 class="text-2xl font-black text-white relative z-10">Apertura de Turno</h2>
                <p class="text-white/80 text-sm mt-1 relative z-10">Ingresa la base de efectivo para comenzar</p>
            </div>

            @if(session('error'))
                <div class="m-6 mb-0 p-3.5 bg-rose-50 border border-rose-200 rounded-2xl flex items-center gap-2 text-rose-700 text-xs font-bold">
                    <span class="material-symbols-outlined text-[20px] text-rose-500 shrink-0">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form action="{{ route('admin.pos.abrirCaja') }}" method="POST" class="p-6 sm:p-8 space-y-6">
                @csrf

                <div>
                    <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-2 ml-1">Caja Registradora</label>
                    @if(isset($cajaAsignada) && $cajaAsignada)
                        <div class="p-3 bg-teal-50 border border-teal-200 rounded-2xl flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-teal-600 text-[22px]">badge</span>
                                <div>
                                    <p class="text-xs font-black text-teal-900">{{ $cajaAsignada->nombre }}</p>
                                    <p class="text-[10px] text-teal-700 font-bold">Caja asignada a tu usuario</p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 text-[9px] font-black bg-teal-200/60 text-teal-800 rounded-md">Asignada</span>
                        </div>
                        <input type="hidden" name="caja_id" value="{{ $cajaAsignada->id }}">
                    @else
                        <select name="caja_id" required class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-secondary focus:bg-white focus:ring-2 focus:ring-secondary/20 transition-all font-bold text-agro-dark outline-none cursor-pointer">
                            @forelse($cajas as $caja)
                                @php
                                    $sesionOcupada = $caja->sesionActiva;
                                @endphp
                                <option value="{{ $caja->id }}" {{ $sesionOcupada ? 'disabled class=text-gray-400' : '' }}>
                                    {{ $caja->nombre }} 
                                    @if($caja->cajeroAsignado) • [Asig: {{ $caja->cajeroAsignado->nombre }}] @endif
                                    @if($sesionOcupada) (Ocupada por {{ $sesionOcupada->cajero ? $sesionOcupada->cajero->nombre : 'Cajero' }}) @else (Disponible) @endif
                                </option>
                            @empty
                                <option value="" disabled selected>No hay cajas registradoras activas</option>
                            @endforelse
                        </select>
                    @endif
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-2 ml-1">Efectivo Base de Gaveta (USD)</label>
                    <div class="relative">
                        
                        <input type="number" step="0.01" name="monto_inicial_usd" required value="0.00" class="w-full h-12 pl-9 pr-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-secondary focus:bg-white text-xl font-black text-primary outline-none">
                    </div>
                    <p class="text-[10px] text-gray-700 font-bold mt-2 ml-1">Monto inicial en físico con el que comienzas el turno para dar cambio.</p>
                </div>

                <button type="submit" class="btn-primary w-full h-12 rounded-xl text-base justify-center shadow-lg shadow-primary/25">
                    <span class="material-symbols-outlined">lock_open</span>
                    <span>Iniciar Turno de Ventas</span>
                </button>
            </form>
        </div>
    </main>
</div>
@endsection