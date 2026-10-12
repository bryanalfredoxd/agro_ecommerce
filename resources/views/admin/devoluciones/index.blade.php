@extends('layouts.admin')

@section('title', 'Devoluciones y Reembolsos - Agrosuministros Rodríguez')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up pb-10">
            
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.facturas.index') }}" class="w-10 h-10 flex-shrink-0 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:text-amber-600 hover:border-amber-200 transition-colors shadow-sm">
                        <span class="material-symbols-outlined">arrow_back</span>
                    </a>
                    <div>
                        <h2 class="text-2xl font-black text-agro-dark flex items-center gap-2">
                            <span class="material-symbols-outlined text-amber-600 text-[32px]">assignment_return</span>
                            Devoluciones y Reembolsos
                        </h2>
                        <p class="text-sm text-gray-500 mt-0.5">Historial y auditoría de garantías, devoluciones de mercancía y reembolsos monetarios.</p>
                    </div>
                </div>

                <a href="{{ route('admin.facturas.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-sm transition-all self-start sm:self-auto">
                    <span class="material-symbols-outlined text-[18px]">receipt</span>
                    <span>Ir a Facturas para Devolver</span>
                </a>
            </div>

            {{-- Tarjetas KPI Rápidas --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-3 -top-3 w-16 h-16 bg-amber-50 rounded-full group-hover:scale-150 transition-transform duration-300"></div>
                    <div class="relative z-10">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Total Devoluciones</span>
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">assignment_return</span>
                            </div>
                        </div>
                        <h3 class="text-2xl font-black text-agro-dark">{{ $cantidadDevoluciones }}</h3>
                        <p class="text-[11px] text-gray-500 font-medium mt-1">Registros procesados</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-3 -top-3 w-16 h-16 bg-rose-50 rounded-full group-hover:scale-150 transition-transform duration-300"></div>
                    <div class="relative z-10">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Dinero Reembolsado ($)</span>
                            <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">payments</span>
                            </div>
                        </div>
                        <h3 class="text-2xl font-black text-agro-dark">${{ number_format($totalDevueltoUsd, 2) }}</h3>
                        <p class="text-[11px] text-rose-600 font-bold mt-1">Total egresado en divisas</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-3 -top-3 w-16 h-16 bg-emerald-50 rounded-full group-hover:scale-150 transition-transform duration-300"></div>
                    <div class="relative z-10">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Dinero Reembolsado (Bs)</span>
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span>
                            </div>
                        </div>
                        <h3 class="text-2xl font-black text-agro-dark">Bs. {{ number_format($totalDevueltoVes, 2) }}</h3>
                        <p class="text-[11px] text-emerald-600 font-bold mt-1">Total en bolívares</p>
                    </div>
                </div>
            </div>

            {{-- Filtros y Buscador --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-4 mb-6">
                <form method="GET" action="{{ route('admin.devoluciones.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div class="sm:col-span-2 relative">
                        <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-[20px]">search</span>
                        <input type="text" name="buscar" value="{{ request('buscar') }}" class="w-full pl-10 pr-4 py-2 bg-white border border-slate-300 text-slate-900 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none" placeholder="Buscar por código (DEV-001), Factura, Cliente o Motivo...">
                    </div>

                    <div>
                        <select name="metodo_reembolso" class="w-full px-3 py-2 bg-white border border-slate-300 text-slate-900 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
                            <option value="todos">Todos los Métodos de Pago</option>
                            <option value="efectivo_usd" {{ request('metodo_reembolso') == 'efectivo_usd' ? 'selected' : '' }}>Efectivo Divisas ($)</option>
                            <option value="efectivo_bs" {{ request('metodo_reembolso') == 'efectivo_bs' ? 'selected' : '' }}>Efectivo Bolívares (Bs)</option>
                            <option value="pago_movil" {{ request('metodo_reembolso') == 'pago_movil' ? 'selected' : '' }}>Pago Móvil</option>
                            <option value="transferencia" {{ request('metodo_reembolso') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                            <option value="punto_venta" {{ request('metodo_reembolso') == 'punto_venta' ? 'selected' : '' }}>Punto de Venta</option>
                            <option value="nota_credito" {{ request('metodo_reembolso') == 'nota_credito' ? 'selected' : '' }}>Nota de Crédito</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold transition-colors shadow-2xs" style="background-color: #d97706; color: #ffffff;">
                            Filtrar
                        </button>
                        <a href="{{ route('admin.devoluciones.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-colors border border-slate-300 flex items-center justify-center" title="Limpiar Filtros">
                            <span class="material-symbols-outlined text-[18px]">refresh</span>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Tabla de Devoluciones --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/60">
                                <th class="px-6 py-4 text-[10px] font-black text-gray-500 uppercase tracking-widest">Código / Fecha</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-500 uppercase tracking-widest">Factura / Cliente</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-500 uppercase tracking-widest">Monto Reembolsado</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-500 uppercase tracking-widest">Método / Tipo</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-500 uppercase tracking-widest">Motivo</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-500 uppercase tracking-widest text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-xs">
                            @forelse($devoluciones as $dev)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-mono font-black text-amber-700 text-sm flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px]">assignment_return</span>
                                        {{ $dev->codigo_devolucion }}
                                    </div>
                                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-0.5">
                                        {{ \Carbon\Carbon::parse($dev->fecha_devolucion)->format('d M Y, h:i A') }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">
                                        {{ $dev->factura ? $dev->factura->numero_factura : 'Factura N/A' }}
                                    </div>
                                    <div class="text-[11px] text-gray-600 truncate max-w-[200px]">
                                        {{ $dev->factura ? ($dev->factura->nombre_razon_social ?: 'Consumidor Final') : 'N/A' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-black text-agro-dark text-sm">${{ number_format($dev->monto_total_usd, 2) }}</div>
                                    <div class="text-[10px] text-gray-500 font-bold">Bs. {{ number_format($dev->monto_total_ves, 2) }}</div>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                                        {{ str_replace('_', ' ', $dev->metodo_reembolso) }}
                                    </span>
                                    <div class="text-[10px] text-gray-500 font-medium mt-0.5">
                                        {{ $dev->tipo_devolucion === 'total' ? 'Devolución Total' : 'Devolución Parcial' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-gray-800 font-medium truncate max-w-[220px]" title="{{ $dev->motivo_general }}">
                                        {{ $dev->motivo_general }}
                                    </div>
                                    <div class="text-[10px] text-gray-500 flex items-center gap-1 mt-0.5">
                                        <span class="material-symbols-outlined text-[12px] {{ $dev->reingresar_stock ? 'text-emerald-500' : 'text-gray-400' }}">inventory_2</span>
                                        <span>{{ $dev->reingresar_stock ? 'Stock reincorporado' : 'Sin reincorporar' }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button onclick="verDevolucionModal({{ $dev->id }})" class="w-8 h-8 rounded-lg bg-white border border-slate-300 text-slate-700 hover:text-amber-700 hover:border-amber-300 hover:bg-amber-50 flex items-center justify-center transition-all shadow-2xs" title="Ver Detalles">
                                            <span class="material-symbols-outlined text-[17px] text-amber-600">visibility</span>
                                        </button>
                                        <a href="{{ route('admin.devoluciones.comprobante', $dev->id) }}" target="_blank" class="w-8 h-8 rounded-lg bg-white border border-slate-300 text-slate-700 hover:text-teal-700 hover:border-teal-300 hover:bg-teal-50 flex items-center justify-center transition-all shadow-2xs" title="Imprimir Ticket Térmico">
                                            <span class="material-symbols-outlined text-[17px] text-teal-600">print</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <span class="material-symbols-outlined text-3xl text-gray-400">assignment_return</span>
                                    </div>
                                    <p class="font-bold text-gray-600 text-sm">No hay devoluciones registradas</p>
                                    <p class="text-xs text-gray-400 mt-1">Las devoluciones procesadas desde las facturas aparecerán aquí.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($devoluciones->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $devoluciones->links('pagination::tailwind') }}
                </div>
                @endif
            </div>

        </div>
    </main>
</div>

{{-- MODAL VISOR DE DEVOLUCIÓN --}}
<div id="devolucionModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity opacity-0" id="devolucionBackdrop" onclick="cerrarDevolucionModal()"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-center p-3 sm:p-4">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-xl max-h-[92vh] flex flex-col opacity-0 scale-95" id="devolucionPanel">
            
            <div class="px-5 py-3.5 border-b border-slate-200 flex justify-between items-center bg-slate-100 sticky top-0 z-20">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-600 text-2xl">assignment_return</span>
                    <h3 class="text-sm font-black text-slate-900">Auditoría de Devolución</h3>
                </div>
                <button type="button" onclick="cerrarDevolucionModal()" class="text-slate-500 hover:text-slate-800 p-1.5 rounded-xl hover:bg-slate-200 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <div class="overflow-y-auto custom-scrollbar flex-1 p-5 relative bg-white">
                <div id="devolucionLoader" class="absolute inset-0 bg-white/95 z-10 flex flex-col items-center justify-center">
                    <span class="material-symbols-outlined animate-spin text-amber-600 text-3xl mb-2">sync</span>
                    <p class="font-bold text-xs text-slate-600">Cargando devolución...</p>
                </div>
                <div id="devolucionContent"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function verDevolucionModal(id) {
        const modal = document.getElementById('devolucionModal');
        const backdrop = document.getElementById('devolucionBackdrop');
        const panel = document.getElementById('devolucionPanel');
        const loader = document.getElementById('devolucionLoader');
        const content = document.getElementById('devolucionContent');

        content.innerHTML = '';
        loader.classList.remove('hidden');
        modal.classList.remove('hidden');

        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);

        fetch(`/admin/devoluciones/${id}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.text())
        .then(html => {
            content.innerHTML = html;
            loader.classList.add('hidden');
        })
        .catch(err => {
            content.innerHTML = '<div class="p-4 text-center text-rose-600 text-xs font-bold">Error al cargar la información.</div>';
            loader.classList.add('hidden');
        });
    }

    function cerrarDevolucionModal() {
        const modal = document.getElementById('devolucionModal');
        const backdrop = document.getElementById('devolucionBackdrop');
        const panel = document.getElementById('devolucionPanel');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 250);
    }
</script>
@endpush
@endsection
