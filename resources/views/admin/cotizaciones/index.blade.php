@extends('layouts.admin')

@section('title', 'Cotizaciones y Presupuestos - Agrosuministros Rodríguez')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up pb-10">
            
            {{-- Mensajes Flash --}}
            @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-emerald-600 text-[26px]">check_circle</span>
                    <span class="text-sm font-bold">{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            @endif

            @if(session('warning'))
            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-amber-600 text-[26px]">warning</span>
                    <span class="text-sm font-bold">{{ session('warning') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-amber-500 hover:text-amber-700">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-rose-600 text-[26px]">error</span>
                    <span class="text-sm font-bold">{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            @endif

            <!-- ENCABEZADO DE PÁGINA -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.dashboard') }}" class="w-10 h-10 flex-shrink-0 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:text-[#1e549e] hover:border-blue-200 transition-colors shadow-sm">
                        <span class="material-symbols-outlined">arrow_back</span>
                    </a>
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#1e549e] text-[32px]">request_quote</span>
                            Cotizaciones y Presupuestos
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Crea presupuestos formales para clientes, imprímelos en PDF o envíalos directo a su correo.</p>
                    </div>
                </div>

                <div>
                    <a href="{{ route('admin.cotizaciones.create') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-[#0b1d38] hover:bg-[#162a4a] text-white font-extrabold text-sm rounded-xl shadow-lg shadow-[#0b1d38]/20 transition-all hover:scale-[1.02] active:scale-95">
                        <span class="material-symbols-outlined text-[20px]">add_circle</span>
                        Nueva Cotización
                    </a>
                </div>
            </div>

            <!-- TARJETAS KPIS RÁPIDAS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[28px]">description</span>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Emitidas</div>
                        <div class="text-2xl font-black text-slate-800">{{ $totalCotizaciones }}</div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[28px]">pending</span>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pendientes</div>
                        <div class="text-2xl font-black text-slate-800">{{ $pendientesCount }}</div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[28px]">task_alt</span>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Aprobadas</div>
                        <div class="text-2xl font-black text-slate-800">{{ $aprobadasCount }}</div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[28px]">attach_money</span>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Volumen Cotizado</div>
                        <div class="text-2xl font-black text-slate-800">${{ number_format($montoTotalUsd, 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- FILTROS Y BÚSQUEDA -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-2 mb-6">
                <form action="{{ route('admin.cotizaciones.index') }}" method="GET" class="space-y-3">
                    <div class="flex overflow-x-auto custom-scrollbar border-b border-gray-100 px-4 pt-2 gap-4">
                        @php $filtroActual = request('filtro_estado', 'todas'); @endphp
                        <a href="{{ route('admin.cotizaciones.index', array_merge(request()->except('filtro_estado', 'page'), ['filtro_estado' => 'todas'])) }}" 
                           class="pb-3 text-sm font-bold border-b-2 whitespace-nowrap {{ $filtroActual === 'todas' ? 'border-[#1e549e] text-[#1e549e] font-black' : 'border-transparent text-gray-500 hover:text-slate-800' }}">
                            Todas ({{ $totalCotizaciones }})
                        </a>
                        <a href="{{ route('admin.cotizaciones.index', array_merge(request()->except('filtro_estado', 'page'), ['filtro_estado' => 'pendiente'])) }}" 
                           class="pb-3 text-sm font-bold border-b-2 whitespace-nowrap {{ $filtroActual === 'pendiente' ? 'border-amber-500 text-amber-700 font-black' : 'border-transparent text-gray-500 hover:text-slate-800' }}">
                            Pendientes ({{ $pendientesCount }})
                        </a>
                        <a href="{{ route('admin.cotizaciones.index', array_merge(request()->except('filtro_estado', 'page'), ['filtro_estado' => 'aprobada'])) }}" 
                           class="pb-3 text-sm font-bold border-b-2 whitespace-nowrap {{ $filtroActual === 'aprobada' ? 'border-emerald-500 text-emerald-700 font-black' : 'border-transparent text-gray-500 hover:text-slate-800' }}">
                            Aprobadas ({{ $aprobadasCount }})
                        </a>
                        <a href="{{ route('admin.cotizaciones.index', array_merge(request()->except('filtro_estado', 'page'), ['filtro_estado' => 'rechazada'])) }}" 
                           class="pb-3 text-sm font-bold border-b-2 whitespace-nowrap {{ $filtroActual === 'rechazada' ? 'border-rose-500 text-rose-700 font-black' : 'border-transparent text-gray-500 hover:text-slate-800' }}">
                            Rechazadas
                        </a>
                    </div>

                    <div class="p-3 bg-gray-50/50 rounded-b-2xl flex flex-col sm:flex-row gap-3">
                        <div class="flex-1 relative">
                            <span class="material-symbols-outlined absolute left-3 top-3 text-gray-400">search</span>
                            <input type="text" name="buscar" value="{{ request('buscar') }}" 
                                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-[#1e549e] transition-all outline-none font-medium text-slate-800" 
                                   placeholder="Buscar por Nº Cotización, Nombre del Cliente, Cédula/RIF o Correo...">
                        </div>
                        <input type="hidden" name="filtro_estado" value="{{ $filtroActual }}">
                        <button type="submit" class="px-5 py-2.5 bg-[#0b1d38] hover:bg-[#162a4a] text-white font-bold text-sm rounded-xl transition-colors">
                            Buscar
                        </button>
                        @if(request('buscar') || request('filtro_estado') !== 'todas')
                        <a href="{{ route('admin.cotizaciones.index') }}" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-slate-700 font-bold text-sm rounded-xl transition-colors flex items-center justify-center">
                            Limpiar
                        </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- TABLA DE RESULTADOS -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-gray-100 text-[11px] font-black uppercase text-gray-400 tracking-wider">
                                <th class="py-4 px-6">Nº Cotización</th>
                                <th class="py-4 px-6">Cliente</th>
                                <th class="py-4 px-4 text-center">Fecha / Validez</th>
                                <th class="py-4 px-4 text-center">Ítems</th>
                                <th class="py-4 px-6 text-right">Monto Total</th>
                                <th class="py-4 px-4 text-center">Estado</th>
                                <th class="py-4 px-6 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($cotizaciones as $cot)
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <!-- NÚMERO -->
                                <td class="py-4 px-6 font-mono font-black text-slate-800">
                                    <a href="{{ route('admin.cotizaciones.show', $cot->id) }}" class="hover:text-[#1e549e] hover:underline flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[18px] text-gray-400">description</span>
                                        {{ $cot->numero_cotizacion }}
                                    </a>
                                    <div class="text-[10px] text-gray-400 font-sans font-medium mt-0.5">
                                        Por: {{ $cot->vendedor ? $cot->vendedor->nombre : 'Sistema' }}
                                    </div>
                                </td>

                                <!-- CLIENTE -->
                                <td class="py-4 px-6">
                                    <div class="font-extrabold text-slate-800">{{ $cot->cliente_nombre }}</div>
                                    <div class="text-xs text-gray-500 font-mono">
                                        {{ $cot->cliente_documento ?: 'Sin Documento' }}
                                    </div>
                                    @if($cot->cliente_email)
                                    <div class="text-[11px] text-gray-400 mt-0.5 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[13px]">mail</span>
                                        {{ $cot->cliente_email }}
                                    </div>
                                    @endif
                                </td>

                                <!-- FECHA Y VIGENCIA -->
                                <td class="py-4 px-4 text-center">
                                    <div class="text-xs font-bold text-slate-700">
                                        {{ \Carbon\Carbon::parse($cot->fecha_emision)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-[11px] {{ $cot->esta_vencida ? 'text-amber-600 font-bold' : 'text-gray-400' }} mt-0.5">
                                        Vence: {{ $cot->fecha_vencimiento ? \Carbon\Carbon::parse($cot->fecha_vencimiento)->format('d/m/Y') : 'N/A' }}
                                    </div>
                                </td>

                                <!-- ÍTEMS -->
                                <td class="py-4 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                        {{ $cot->detalles->count() }} prod.
                                    </span>
                                </td>

                                <!-- TOTAL -->
                                <td class="py-4 px-6 text-right">
                                    <div class="text-base font-black text-slate-900">
                                        ${{ number_format($cot->total_usd, 2) }}
                                    </div>
                                    <div class="text-xs font-bold text-[#1e549e] font-mono">
                                        Bs. {{ number_format($cot->total_ves, 2) }}
                                    </div>
                                </td>

                                <!-- ESTADO -->
                                <td class="py-4 px-4 text-center">
                                    @php $badge = $cot->estado_badge; @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-extrabold border {{ $badge['bg'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                        {{ $badge['label'] }}
                                    </span>
                                </td>

                                <!-- ACCIONES -->
                                <td class="py-4 px-6 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- VER DETALLE -->
                                        <a href="{{ route('admin.cotizaciones.show', $cot->id) }}" 
                                           class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-blue-100 text-slate-600 hover:text-[#1e549e] flex items-center justify-center transition-colors" 
                                           title="Ver Detalle">
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        </a>

                                        <!-- IMPRIMIR A4 -->
                                        <a href="{{ route('admin.cotizaciones.imprimir', $cot->id) }}" target="_blank"
                                           class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 flex items-center justify-center transition-colors" 
                                           title="Imprimir / Vista Previa A4">
                                            <span class="material-symbols-outlined text-[18px]">print</span>
                                        </a>

                                        <!-- DESCARGAR PDF -->
                                        <a href="{{ route('admin.cotizaciones.pdf', $cot->id) }}" 
                                           class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-sky-100 text-slate-600 hover:text-sky-700 flex items-center justify-center transition-colors" 
                                           title="Descargar PDF">
                                            <span class="material-symbols-outlined text-[18px]">download</span>
                                        </a>

                                        <!-- ENVIAR CORREO -->
                                        @if($cot->cliente_email)
                                        <form action="{{ route('admin.cotizaciones.enviar_correo', $cot->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('¿Enviar cotización por correo a {{ $cot->cliente_email }}?');">
                                            @csrf
                                            <button type="submit" class="w-8 h-8 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 hover:text-emerald-800 flex items-center justify-center transition-colors" title="Enviar por Correo">
                                                <span class="material-symbols-outlined text-[18px]">forward_to_inbox</span>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-12 px-6 text-center text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <span class="material-symbols-outlined text-5xl text-gray-300 mb-2">request_quote</span>
                                        <p class="font-bold text-base text-slate-600">No se encontraron cotizaciones</p>
                                        <p class="text-xs text-gray-400 mt-1">Crea la primera cotización para presupuestar insumos a tus clientes.</p>
                                        <a href="{{ route('admin.cotizaciones.create') }}" class="mt-4 px-4 py-2 bg-[#0b1d38] text-white text-xs font-bold rounded-xl hover:bg-[#162a4a]">
                                            Crear Cotización
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($cotizaciones->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $cotizaciones->links() }}
                </div>
                @endif
            </div>

        </div>
    </main>
</div>
@endsection
