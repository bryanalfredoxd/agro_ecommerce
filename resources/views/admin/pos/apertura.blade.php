@extends('layouts.admin')
@section('title', 'Apertura de Caja - Corpo Agrícola')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">
    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col h-screen justify-center items-center p-4">
        
        <div class="w-full max-w-md bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden animate-fade-in-up">
            
            <div class="bg-agro-dark p-6 text-center relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                <span class="material-symbols-outlined text-white text-5xl mb-2 relative z-10">point_of_sale</span>
                <h2 class="text-2xl font-black text-white relative z-10">Apertura de Turno</h2>
                <p class="text-white/80 text-sm mt-1 relative z-10">Ingresa la base de efectivo para comenzar</p>
            </div>

            <form action="{{ route('admin.pos.abrirCaja') }}" method="POST" class="p-6 sm:p-8 space-y-6">
                @csrf

                <div>
                    <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-2 ml-1">Selecciona tu Caja</label>
                    <select name="caja_id" required class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white focus:ring-2 focus:ring-green-500/20 transition-all font-bold text-agro-dark outline-none cursor-pointer">
                        @foreach($cajas as $caja)
                            <option value="{{ $caja->id }}">{{ $caja->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-2 ml-1">Efectivo Base (USD)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3.5 font-bold text-gray-700">$</span>
                        <input type="number" step="0.01" name="monto_inicial_usd" required value="0.00" class="w-full h-12 pl-8 pr-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white text-xl font-black text-green-600 outline-none">
                    </div>
                    <p class="text-[10px] text-gray-700 font-bold mt-2 ml-1">El monto con el que recibes la caja para dar vueltos.</p>
                </div>

                <button type="submit" class="w-full h-12 rounded-xl font-black text-white bg-green-600 hover:bg-green-700 shadow-lg shadow-green-600/30 transition-all flex items-center justify-center gap-2 text-lg">
                    <span class="material-symbols-outlined">lock_open</span> Iniciar Turno de Ventas
                </button>
            </form>
        </div>
    </main>
</div>
@endsection