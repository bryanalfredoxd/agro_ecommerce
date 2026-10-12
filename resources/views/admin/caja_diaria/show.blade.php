@extends('layouts.admin')

@section('title', 'Auditoría de Turno #' . $sesion->id . ' - Agrosuministros Rodríguez')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up pb-16">
            
            {{-- NAVEGACIÓN Y ENCABEZADO --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-3.5">
                    <a href="{{ route('admin.caja_diaria.index') }}" class="w-10 h-10 flex-shrink-0 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:text-[#1e549e] hover:border-blue-200 transition-colors shadow-2xs" title="Regresar al listado de cajas">
                        <span class="material-symbols-outlined">arrow_back</span>
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-md bg-blue-50 text-[#1e549e] font-bold text-xs border border-blue-100">
                                Turno #{{ $sesion->id }}
                            </span>
                            @if(is_null($sesion->fecha_cierre))
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                    <span class="size-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                                    Turno en Operación
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-medium border border-slate-200">
                                    <span class="material-symbols-outlined text-[13px]">lock</span>
                                    Turno Cerrado
                                </span>
                            @endif
                        </div>
                        <h2 class="text-lg sm:text-2xl font-bold text-gray-900 mt-1 flex flex-wrap items-center gap-1.5 sm:gap-2">
                            <span>{{ $sesion->caja ? $sesion->caja->nombre : 'Caja Principal' }}</span>
                            <span class="text-gray-300 font-normal">|</span>
                            <span class="text-gray-600 font-medium text-sm sm:text-lg">Cajero: {{ $sesion->cajero ? trim($sesion->cajero->nombre . ' ' . $sesion->cajero->apellido) : 'No asignado' }}</span>
                        </h2>
                    </div>
                </div>

                {{-- BOTONES DE ACCIÓN (PDF) --}}
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('admin.caja_diaria.pdf', $sesion->id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-sm transition-all hover:scale-105 active:scale-95" title="Generar Informe Oficial en PDF para imprimir o archivar">
                        <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
                        Informe PDF (Cierre Z)
                    </a>
                </div>
            </div>

            {{-- RESUMEN DE TIEMPO Y TASA --}}
            @php
                $apertura = \Carbon\Carbon::parse($sesion->fecha_apertura);
                $cierre = $sesion->fecha_cierre ? \Carbon\Carbon::parse($sesion->fecha_cierre) : null;
                $duracion = $cierre ? $apertura->diffForHumans($cierre, true) : $apertura->diffForHumans(now(), true);
            @endphp
            <div class="bg-white rounded-2xl border border-gray-100 p-4 mb-6 shadow-2xs flex flex-wrap items-center justify-between gap-4 text-xs">
                <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-gray-400 text-[18px]">login</span>
                        <div>
                            <span class="text-gray-400 font-medium block text-[10px] uppercase">Apertura</span>
                            <span class="font-bold text-gray-800">{{ $apertura->format('d/m/Y - h:i:s A') }}</span>
                        </div>
                    </div>

                    <div class="h-8 w-px bg-gray-100"></div>

                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-gray-400 text-[18px]">logout</span>
                        <div>
                            <span class="text-gray-400 font-medium block text-[10px] uppercase">Cierre</span>
                            @if($cierre)
                                <span class="font-bold text-gray-800">{{ $cierre->format('d/m/Y - h:i:s A') }}</span>
                            @else
                                <span class="font-semibold text-emerald-600">Turno abierto actualmente</span>
                            @endif
                        </div>
                    </div>

                    <div class="h-8 w-px bg-gray-100 hidden sm:block"></div>

                    <div class="hidden sm:flex items-center gap-2">
                        <span class="material-symbols-outlined text-gray-400 text-[18px]">schedule</span>
                        <div>
                            <span class="text-gray-400 font-medium block text-[10px] uppercase">Duración</span>
                            <span class="font-bold text-gray-800">{{ $duracion }}</span>
                        </div>
                    </div>
                </div>

                @if($tasaCambio)
                <div class="flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-xl border border-gray-100">
                    <span class="material-symbols-outlined text-blue-600 text-[17px]">currency_exchange</span>
                    <span class="text-gray-500 font-medium">Tasa Oficial BCV:</span>
                    <strong class="font-bold text-gray-900">Bs. {{ number_format($tasaCambio->valor_tasa, 2) }}</strong>
                </div>
                @endif
            </div>

            {{-- KPI METRICS CARDS DEL TURNO --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
                
                {{-- Base Inicial --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Fondo Inicial (Base)</span>
                        <div class="size-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span>
                        </div>
                    </div>
                    <div class="mt-2 flex flex-col">
                        <span class="text-2xl font-black text-gray-900 leading-none">${{ number_format($sesion->monto_inicial_usd, 2) }}</span>
                        <span class="text-xs text-gray-400 font-medium mt-1">Fondo de apertura para cambio</span>
                    </div>
                </div>

                {{-- Total Ventas Sistema --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Ventas Sistema</span>
                        <div class="size-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">payments</span>
                        </div>
                    </div>
                    <div class="mt-2 flex flex-col">
                        <span class="text-2xl font-black text-[#1e549e] leading-none">${{ number_format($sesion->total_ventas_sistema_usd, 2) }}</span>
                        <span class="text-xs text-gray-500 font-medium mt-1">Bs. {{ number_format($sesion->total_ventas_sistema_ves, 2) }}</span>
                    </div>
                </div>

                {{-- Operaciones Emitidas --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Operaciones Emitidas</span>
                        <div class="size-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-black text-gray-900 leading-none">{{ $pedidos->count() }}</span>
                        <span class="text-xs text-gray-400 font-medium">tickets ({{ $clientesUnicos }} clientes)</span>
                    </div>
                </div>

                {{-- Estado del Cuadre / Arqueo --}}
                @php
                    $diferencia = (float) $sesion->diferencia_usd;
                @endphp
                <div class="bg-white p-4 sm:p-5 rounded-2xl border {{ is_null($sesion->fecha_cierre) ? 'border-amber-200 bg-amber-50/20' : (abs($diferencia) < 0.01 ? 'border-emerald-200 bg-emerald-50/20' : ($diferencia > 0 ? 'border-blue-200 bg-blue-50/20' : 'border-rose-200 bg-rose-50/20')) }} shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold {{ is_null($sesion->fecha_cierre) ? 'text-amber-800' : (abs($diferencia) < 0.01 ? 'text-emerald-800' : ($diferencia > 0 ? 'text-blue-800' : 'text-rose-800')) }} uppercase tracking-wider">
                            Arqueo de Gaveta
                        </span>
                        <div class="size-8 rounded-xl {{ is_null($sesion->fecha_cierre) ? 'bg-amber-100 text-amber-700' : (abs($diferencia) < 0.01 ? 'bg-emerald-100 text-emerald-700' : ($diferencia > 0 ? 'bg-blue-100 text-blue-700' : 'bg-rose-100 text-rose-700')) }} flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">
                                {{ is_null($sesion->fecha_cierre) ? 'hourglass_top' : (abs($diferencia) < 0.01 ? 'verified' : ($diferencia > 0 ? 'add_circle' : 'warning')) }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-2 flex flex-col">
                        <span class="text-2xl font-black {{ is_null($sesion->fecha_cierre) ? 'text-amber-800' : (abs($diferencia) < 0.01 ? 'text-emerald-700' : ($diferencia > 0 ? 'text-blue-700' : 'text-rose-700')) }} leading-none">
                            @if(is_null($sesion->fecha_cierre))
                                Pendiente
                            @elseif(abs($diferencia) < 0.01)
                                Cuadrada ($0.00)
                            @elseif($diferencia > 0)
                                +${{ number_format($diferencia, 2) }}
                            @else
                                -${{ number_format(abs($diferencia), 2) }}
                            @endif
                        </span>
                        <span class="text-[11px] text-gray-500 font-medium mt-1">
                            @if(is_null($sesion->fecha_cierre))
                                Se calculará al cerrar caja
                            @elseif(abs($diferencia) < 0.01)
                                Sin discrepancias de efectivo
                            @elseif($diferencia > 0)
                                Sobrante en conteo físico
                            @else
                                Faltante en conteo físico
                            @endif
                        </span>
                    </div>
                </div>

            </div>

            {{-- SECCIÓN CENTRAL: 2 COLUMNAS (MÉTODOS DE PAGO Y ARQUEO FÍSICO) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
                
                {{-- Columna 1: Desglose por Método de Pago (7 cols) --}}
                <div class="lg:col-span-7 bg-white rounded-2xl border border-gray-100 shadow-2xs p-5">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                        <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#1e549e] text-[20px]">payments</span>
                            <span>Desglose por Métodos de Pago</span>
                        </h3>
                        <span class="text-xs text-gray-400 font-medium">{{ count($desgloseMetodos) }} métodos utilizados</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-gray-50/75 border-y border-gray-100 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                    <th class="py-2.5 px-3">Método de Pago</th>
                                    <th class="py-2.5 px-3 text-center">Transacciones</th>
                                    <th class="py-2.5 px-3 text-right">Monto USD</th>
                                    <th class="py-2.5 px-3 text-right">Monto Bs.</th>
                                    <th class="py-2.5 px-3 text-right">Participación</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-xs">
                                @forelse($desgloseMetodos as $d)
                                <tr class="hover:bg-blue-50/20">
                                    <td class="py-3 px-3 text-gray-800 font-semibold flex items-center gap-2">
                                        <span class="size-2 rounded-full bg-blue-600"></span>
                                        <span>{{ $d['metodo'] }}</span>
                                    </td>
                                    <td class="py-3 px-3 text-center text-gray-600 font-medium">{{ $d['cantidad'] }}</td>
                                    <td class="py-3 px-3 text-right text-gray-900 font-bold">${{ number_format($d['total_usd'], 2) }}</td>
                                    <td class="py-3 px-3 text-right text-gray-500 font-medium">Bs. {{ number_format($d['total_ves'], 2) }}</td>
                                    <td class="py-3 px-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <span class="font-bold text-gray-700 text-[11px]">{{ $d['porcentaje'] }}%</span>
                                            <div class="w-12 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-blue-600 rounded-full" style="width: {{ $d['porcentaje'] }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-400 italic font-normal">No se registraron transacciones de pago en este turno.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(count($desgloseMetodos) > 0)
                            <tfoot class="bg-gray-50 font-bold border-t border-gray-200">
                                <tr>
                                    <td class="py-2.5 px-3 text-gray-900 uppercase text-[11px]">Total Recaudado</td>
                                    <td class="py-2.5 px-3 text-center text-gray-700">{{ $pagos->count() }}</td>
                                    <td class="py-2.5 px-3 text-right text-[#1e549e] text-sm">${{ number_format($totalRecaudadoUsd, 2) }}</td>
                                    <td class="py-2.5 px-3 text-right text-gray-700">Bs. {{ number_format($totalRecaudadoVes, 2) }}</td>
                                    <td class="py-2.5 px-3 text-right text-gray-900 text-xs">100%</td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- Columna 2: Arqueo y Conciliación Física de Gaveta (5 cols) --}}
                <div class="lg:col-span-5 bg-white rounded-2xl border border-gray-100 shadow-2xs p-5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                <span class="material-symbols-outlined text-[#1e549e] text-[20px]">point_of_sale</span>
                                <span>Arqueo Físico de Gaveta</span>
                            </h3>
                            <span class="text-xs text-gray-400 font-medium">Solo Efectivo ($)</span>
                        </div>

                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-gray-50/75 rounded-xl border border-gray-100 text-xs">
                                <div>
                                    <span class="font-medium text-gray-600 block">Fondo Inicial de Apertura</span>
                                    <span class="text-[10px] text-gray-400">Base entregada al inicio</span>
                                </div>
                                <span class="font-bold text-gray-800 text-sm">${{ number_format($sesion->monto_inicial_usd, 2) }}</span>
                            </div>

                            <div class="flex justify-between items-center p-3 bg-gray-50/75 rounded-xl border border-gray-100 text-xs">
                                <div>
                                    <span class="font-medium text-gray-600 block">+ Ventas en Efectivo USD</span>
                                    <span class="text-[10px] text-gray-400">Ingresado por cobro</span>
                                </div>
                                <span class="font-bold text-emerald-600 text-sm">+${{ number_format($totalEfectivoUsd, 2) }}</span>
                            </div>

                            <div class="flex justify-between items-center p-3.5 bg-blue-50/40 rounded-xl border border-blue-100 text-xs">
                                <div>
                                    <span class="font-bold text-gray-900 block">= Efectivo Esperado en Caja</span>
                                    <span class="text-[10px] text-blue-600 font-medium">Debe existir físicamente</span>
                                </div>
                                <span class="font-black text-gray-900 text-base">${{ number_format($esperadoEfectivoUsd, 2) }}</span>
                            </div>

                            @if($sesion->fecha_cierre)
                                <div class="flex justify-between items-center p-3.5 bg-slate-50 rounded-xl border border-gray-200 text-xs">
                                    <div>
                                        <span class="font-bold text-gray-800 block">Efectivo Real Contado</span>
                                        <span class="text-[10px] text-gray-400">Declarado por el cajero</span>
                                    </div>
                                    <span class="font-black text-gray-900 text-base">${{ number_format($sesion->dinero_real_en_caja_usd, 2) }}</span>
                                </div>

                                <div class="p-3.5 rounded-xl border {{ abs($diferencia) < 0.01 ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : ($diferencia > 0 ? 'bg-blue-50 border-blue-200 text-blue-900' : 'bg-rose-50 border-rose-200 text-rose-900') }}">
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="font-bold uppercase tracking-wider text-[11px]">Diferencia de Cuadre:</span>
                                        <span class="font-black text-base">
                                            @if(abs($diferencia) < 0.01)
                                                $0.00 (Cuadrada)
                                            @elseif($diferencia > 0)
                                                +${{ number_format($diferencia, 2) }} (Sobrante)
                                            @else
                                                -${{ number_format(abs($diferencia), 2) }} (Faltante)
                                            @endif
                                        </span>
                                    </div>
                                    <p class="text-[11px] mt-1 opacity-80">
                                        @if(abs($diferencia) < 0.01)
                                            El conteo físico coincide con exactitud con los registros del sistema.
                                        @elseif($diferencia > 0)
                                            Hay un exceso de dinero en la gaveta física con respecto a las ventas del sistema.
                                        @else
                                            Falta dinero en la gaveta física respecto a las ventas registradas.
                                        @endif
                                    </p>
                                </div>
                            @else
                                <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900 flex items-center gap-2.5">
                                    <span class="material-symbols-outlined text-amber-600 text-[20px]">info</span>
                                    <p class="font-medium">El turno sigue abierto. El arqueo final y cálculo de diferencia se registrará automáticamente cuando el cajero finalice su turno.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($sesion->observaciones_cierre)
                        <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                            <span class="text-[10px] font-bold text-gray-500 uppercase block mb-1">Observaciones registradas por el cajero:</span>
                            <p class="italic text-gray-700">"{{ $sesion->observaciones_cierre }}"</p>
                        </div>
                    @endif
                </div>

            </div>

            {{-- SECCIÓN INFERIOR: HISTORIAL COMPLETO DE VENTAS EMITIDAS --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-2xs overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#1e549e] text-[22px]">receipt</span>
                            <span>Registro de Ventas y Facturas Emitidas ({{ $pedidos->count() }})</span>
                        </h3>
                        <p class="text-xs text-gray-400 font-medium mt-0.5">Listado detallado de todas las transacciones procesadas durante este turno de caja.</p>
                    </div>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-gray-50/75 border-b border-gray-100 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="py-3 px-5">Documento</th>
                                <th class="py-3 px-5">Hora</th>
                                <th class="py-3 px-5">Cliente</th>
                                <th class="py-3 px-5">Cédula / RIF</th>
                                <th class="py-3 px-5">Ítems Vendidos</th>
                                <th class="py-3 px-5">Método(s) de Pago</th>
                                <th class="py-3 px-5 text-right">Total USD</th>
                                <th class="py-3 px-5 text-right">Total Bs.</th>
                                <th class="py-3 px-5 text-right">Comprobante</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs bg-white">
                            @forelse($pedidos as $p)
                            <tr class="hover:bg-blue-50/20 transition-colors">
                                {{-- Número de Factura / Ticket --}}
                                <td class="py-3 px-5 font-bold text-[#1e549e] whitespace-nowrap">
                                    @if($p->factura)
                                        <span class="font-mono">{{ $p->factura->numero_factura }}</span>
                                    @else
                                        <span class="font-mono text-gray-700">Ticket #{{ str_pad($p->id, 6, '0', STR_PAD_LEFT) }}</span>
                                    @endif
                                </td>

                                {{-- Hora --}}
                                <td class="py-3 px-5 text-gray-500 whitespace-nowrap font-medium">
                                    {{ \Carbon\Carbon::parse($p->creado_at)->format('h:i:s A') }}
                                </td>

                                {{-- Cliente --}}
                                <td class="py-3 px-5 text-gray-900 font-semibold">
                                    {{ $p->factura ? $p->factura->nombre_razon_social : ($p->usuario ? $p->usuario->nombre . ' ' . $p->usuario->apellido : 'Consumidor Final') }}
                                </td>

                                {{-- Cédula / RIF --}}
                                <td class="py-3 px-5 text-gray-500 font-medium">
                                    {{ $p->factura ? ($p->factura->cedula_rif_cliente ?: 'N/A') : ($p->usuario && $p->usuario->documento_identidad ? $p->usuario->documento_identidad : 'V-00000000') }}
                                </td>

                                {{-- Ítems --}}
                                <td class="py-3 px-5 text-gray-600">
                                    @if($p->detalles && $p->detalles->count() > 0)
                                        <span class="font-medium" title="{{ $p->detalles->pluck('producto.nombre')->implode(', ') }}">
                                            {{ $p->detalles->count() }} producto(s) ({{ $p->detalles->sum('cantidad') }} uds)
                                        </span>
                                    @else
                                        <span class="text-gray-400">1 operación</span>
                                    @endif
                                </td>

                                {{-- Métodos de pago --}}
                                <td class="py-3 px-5">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($p->pagos as $pg)
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[10px] font-semibold">
                                                {{ strtoupper(str_replace('_', ' ', $pg->metodo)) }}
                                            </span>
                                        @empty
                                            <span class="text-gray-400 text-[11px]">Efectivo</span>
                                        @endforelse
                                    </div>
                                </td>

                                {{-- Montos --}}
                                <td class="py-3 px-5 text-right text-gray-900 font-bold whitespace-nowrap">
                                    ${{ number_format($p->total_usd, 2) }}
                                </td>
                                <td class="py-3 px-5 text-right text-gray-500 font-medium whitespace-nowrap">
                                    Bs. {{ number_format($p->total_ves > 0 ? $p->total_ves : ($p->total_ves_calculado ?: ($p->factura ? $p->factura->total_ves : 0)), 2) }}
                                </td>

                                {{-- Acción --}}
                                <td class="py-3 px-5 text-right whitespace-nowrap">
                                    @if($p->factura)
                                        <a href="{{ route('admin.facturas.imprimir', $p->factura->id) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs text-[#1e549e] hover:text-[#153e77] bg-blue-50 hover:bg-blue-100 rounded-lg font-semibold transition-colors border border-blue-200/60" title="Ver e imprimir Factura Fiscal Oficial (Formato A4)">
                                            <span class="material-symbols-outlined text-[15px]">description</span>
                                            <span>Factura Fiscal</span>
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-[11px]">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-gray-400 italic font-normal">
                                    No se registraron ventas en este turno de caja.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>
@endsection
