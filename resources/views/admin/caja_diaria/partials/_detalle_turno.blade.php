<div class="p-6 bg-slate-50 space-y-5">

    {{-- Encabezado del Turno --}}
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-2xs">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pb-4 border-b border-gray-100">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-md bg-blue-50 text-[#1e549e] font-bold text-xs">
                        Turno #{{ $sesion->id }}
                    </span>
                    @if(is_null($sesion->fecha_cierre))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold">
                            <span class="size-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                            En Operación
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-medium">
                            <span class="material-symbols-outlined text-[13px]">lock</span>
                            Cerrado
                        </span>
                    @endif
                </div>
                <h3 class="text-base font-bold text-gray-900 mt-1.5">
                    {{ $sesion->caja ? $sesion->caja->nombre : 'Caja Principal' }} • {{ $sesion->cajero ? trim($sesion->cajero->nombre . ' ' . $sesion->cajero->apellido) : 'Cajero' }}
                </h3>
            </div>

            @if($sesion->fecha_cierre)
                <a href="{{ route('admin.pos.imprimirCierre', $sesion->id) }}" target="_blank" class="px-3.5 py-2 bg-[#1e549e] hover:bg-[#153e77] text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-2xs transition-all">
                    <span class="material-symbols-outlined text-[16px]">receipt</span>
                    Imprimir Ticket Z
                </a>
            @endif
        </div>

        {{-- Métricas Clave del Turno --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-4 text-xs">
            <div class="bg-slate-50 p-3 rounded-xl border border-gray-100">
                <span class="text-gray-400 font-bold block text-[10px] uppercase tracking-wider">Base Inicial</span>
                <strong class="text-gray-900 font-bold text-sm block mt-0.5">${{ number_format($sesion->monto_inicial_usd, 2) }}</strong>
            </div>
            <div class="bg-blue-50/50 p-3 rounded-xl border border-blue-100">
                <span class="text-[#1e549e] font-bold block text-[10px] uppercase tracking-wider">Total Ventas POS</span>
                <strong class="text-[#1e549e] font-bold text-sm block mt-0.5">${{ number_format($sesion->total_ventas_sistema_usd, 2) }}</strong>
            </div>
            <div class="bg-slate-50 p-3 rounded-xl border border-gray-100">
                <span class="text-gray-400 font-bold block text-[10px] uppercase tracking-wider">Operaciones</span>
                <strong class="text-gray-900 font-bold text-sm block mt-0.5">{{ $pedidos->count() }} ventas</strong>
            </div>
            <div class="p-3 rounded-xl border {{ is_null($sesion->fecha_cierre) ? 'bg-amber-50 border-amber-200' : (abs($sesion->diferencia_usd) < 0.01 ? 'bg-emerald-50 border-emerald-200' : ($sesion->diferencia_usd > 0 ? 'bg-blue-50 border-blue-200' : 'bg-rose-50 border-rose-200')) }}">
                <span class="font-bold block text-[10px] uppercase tracking-wider {{ is_null($sesion->fecha_cierre) ? 'text-amber-800' : (abs($sesion->diferencia_usd) < 0.01 ? 'text-emerald-800' : ($sesion->diferencia_usd > 0 ? 'text-blue-800' : 'text-rose-800')) }}">Arqueo de Gaveta</span>
                <strong class="font-bold text-sm block mt-0.5 {{ is_null($sesion->fecha_cierre) ? 'text-amber-900' : (abs($sesion->diferencia_usd) < 0.01 ? 'text-emerald-700' : ($sesion->diferencia_usd > 0 ? 'text-blue-700' : 'text-rose-700')) }}">
                    @if(is_null($sesion->fecha_cierre))
                        Pendiente
                    @elseif(abs($sesion->diferencia_usd) < 0.01)
                        Cuadrada ($0.00)
                    @elseif($sesion->diferencia_usd > 0)
                        +${{ number_format($sesion->diferencia_usd, 2) }} (Sobrante)
                    @else
                        -${{ number_format(abs($sesion->diferencia_usd), 2) }} (Faltante)
                    @endif
                </strong>
            </div>
        </div>
    </div>

    {{-- Desglose por Método de Pago --}}
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-2xs">
        <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[#1e549e] text-[18px]">payments</span>
            Desglose de Ingresos por Método de Pago
        </h4>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-50/75 border-y border-gray-100 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                        <th class="py-2.5 px-3">Método de Pago</th>
                        <th class="py-2.5 px-3 text-center">Operaciones</th>
                        <th class="py-2.5 px-3 text-right">Total USD</th>
                        <th class="py-2.5 px-3 text-right">Total Bs.</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    @forelse($desgloseMetodos as $d)
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-2.5 px-3 text-gray-800 font-medium flex items-center gap-2">
                            <span class="size-2 rounded-full bg-blue-600"></span>
                            <span>{{ $d['metodo'] }}</span>
                        </td>
                        <td class="py-2.5 px-3 text-center text-gray-600 font-medium">{{ $d['cantidad'] }}</td>
                        <td class="py-2.5 px-3 text-right text-gray-900 font-bold">${{ number_format($d['total_usd'], 2) }}</td>
                        <td class="py-2.5 px-3 text-right text-gray-500 font-medium">Bs. {{ number_format($d['total_ves'], 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-gray-400 font-normal italic">Sin operaciones de pago en este turno.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Arqueo Físico de Gaveta (Si está cerrada) --}}
    @if($sesion->fecha_cierre)
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-2xs">
        <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[#1e549e] text-[18px]">balance</span>
            Auditoría de Gaveta Física (Arqueo Z)
        </h4>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs mb-3">
            <div class="p-3 bg-gray-50/75 rounded-xl border border-gray-100">
                <span class="text-gray-400 font-bold text-[10px] uppercase tracking-wider block">Efectivo Físico Esperado</span>
                <span class="text-sm font-bold text-gray-900 block mt-0.5">${{ number_format($sesion->monto_inicial_usd + $totalEfectivoUsd, 2) }}</span>
                <p class="text-[10px] text-gray-400 mt-1">Base (${{ number_format($sesion->monto_inicial_usd, 2) }}) + Ventas Efectivo (${{ number_format($totalEfectivoUsd, 2) }})</p>
            </div>
            <div class="p-3 bg-gray-50/75 rounded-xl border border-gray-100">
                <span class="text-gray-400 font-bold text-[10px] uppercase tracking-wider block">Efectivo Físico Contado</span>
                <span class="text-sm font-bold text-gray-900 block mt-0.5">${{ number_format($sesion->dinero_real_en_caja_usd, 2) }}</span>
                <p class="text-[10px] text-gray-400 mt-1">Declarado por el cajero al cerrar</p>
            </div>
            <div class="p-3 rounded-xl border {{ abs($sesion->diferencia_usd) < 0.01 ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : ($sesion->diferencia_usd > 0 ? 'bg-blue-50 border-blue-200 text-blue-800' : 'bg-rose-50 border-rose-200 text-rose-800') }}">
                <span class="font-bold text-[10px] uppercase tracking-wider block">Diferencia Final</span>
                <span class="text-sm font-bold block mt-0.5">
                    @if(abs($sesion->diferencia_usd) < 0.01)
                        $0.00 (Cuadrada)
                    @elseif($sesion->diferencia_usd > 0)
                        +${{ number_format($sesion->diferencia_usd, 2) }} (Sobrante)
                    @else
                        -${{ number_format(abs($sesion->diferencia_usd), 2) }} (Faltante)
                    @endif
                </span>
                <p class="text-[10px] mt-1">Resultado del conteo físico</p>
            </div>
        </div>

        @if($sesion->observaciones_cierre)
            <div class="p-3 bg-amber-50/75 border border-amber-200 rounded-xl text-xs text-amber-900">
                <span class="text-[10px] uppercase font-bold text-amber-700 block mb-0.5">Observaciones declaradas por el cajero:</span>
                "{{ $sesion->observaciones_cierre }}"
            </div>
        @endif
    </div>
    @endif

    {{-- Lista de Ventas / Tickets Procesados --}}
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-2xs">
        <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[#1e549e] text-[18px]">receipt_long</span>
            Historial de Ventas Emitidas en el Turno ({{ $pedidos->count() }})
        </h4>

        <div class="overflow-x-auto max-h-60 custom-scrollbar">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="sticky top-0 bg-gray-50/90 border-y border-gray-100 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="py-2.5 px-3">Factura</th>
                        <th class="py-2.5 px-3">Cliente</th>
                        <th class="py-2.5 px-3">Hora</th>
                        <th class="py-2.5 px-3 text-right">Total USD</th>
                        <th class="py-2.5 px-3 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    @forelse($pedidos as $p)
                    <tr class="hover:bg-blue-50/20">
                        <td class="py-2.5 px-3 font-semibold text-[#1e549e]">
                            {{ $p->factura ? $p->factura->numero_factura : '#' . $p->id }}
                        </td>
                        <td class="py-2.5 px-3 text-gray-800 font-medium">
                            {{ $p->factura ? $p->factura->nombre_razon_social : ($p->usuario ? $p->usuario->nombre : 'Consumidor Final') }}
                        </td>
                        <td class="py-2.5 px-3 text-gray-500 text-[11px]">
                            {{ \Carbon\Carbon::parse($p->creado_at)->format('h:i:s A') }}
                        </td>
                        <td class="py-2.5 px-3 text-right text-gray-900 font-bold">
                            ${{ number_format($p->total_usd, 2) }}
                        </td>
                        <td class="py-2.5 px-3 text-right">
                            @if($p->factura)
                                <a href="{{ route('factura.ticket', $p->factura->id) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-[11px] font-semibold hover:underline">
                                    Ticket
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-gray-400 italic font-normal">Sin ventas registradas en este turno.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
