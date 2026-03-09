<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50/50">
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Apertura / Turno</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Cajero</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Monto Inicial</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Ventas Sistema</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Cuadre (Diferencia)</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Detalles</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($sesiones as $sesion)
            <tr class="hover:bg-gray-50/50 transition-colors group">
                
                {{-- Columna 1: Fechas y Estado --}}
                <td class="px-6 py-4">
                    <div class="flex flex-col gap-1">
                        @if(is_null($sesion->fecha_cierre))
                            <span class="inline-flex items-center gap-1 text-[10px] font-black text-green-600 bg-green-50 px-2 py-0.5 rounded border border-green-100 uppercase w-fit"><span class="material-symbols-outlined text-[14px] animate-pulse">lock_open</span> Turno Activo</span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[10px] font-black text-gray-500 bg-gray-100 px-2 py-0.5 rounded border border-gray-200 uppercase w-fit"><span class="material-symbols-outlined text-[14px]">lock</span> Cerrada</span>
                        @endif
                        <span class="text-xs text-gray-800 font-bold mt-1">Abre: {{ \Carbon\Carbon::parse($sesion->fecha_apertura)->format('d M, h:i A') }}</span>
                        @if($sesion->fecha_cierre)
                            <span class="text-[10px] text-gray-400 font-bold">Cierra: {{ \Carbon\Carbon::parse($sesion->fecha_cierre)->format('d M, h:i A') }}</span>
                        @endif
                    </div>
                </td>

                {{-- Columna 2: Cajero y Caja --}}
                <td class="px-6 py-4">
                    <p class="font-bold text-gray-800 text-sm capitalize">{{ $sesion->cajero->nombre ?? 'N/A' }} {{ $sesion->cajero->apellido ?? '' }}</p>
                    <p class="text-[10px] font-bold text-teal-600 mt-0.5 uppercase tracking-wider">{{ $sesion->caja->nombre ?? 'Caja Única' }}</p>
                </td>

                {{-- Columna 3: Monto Inicial --}}
                <td class="px-6 py-4 font-black text-gray-600">
                    ${{ number_format($sesion->monto_inicial_usd, 2) }}
                </td>

                {{-- Columna 4: Ventas del Sistema --}}
                <td class="px-6 py-4">
                    <p class="font-black text-agro-dark text-sm">${{ number_format($sesion->total_ventas_sistema_usd, 2) }}</p>
                    <p class="text-[10px] font-bold text-gray-400">Bs. {{ number_format($sesion->total_ventas_sistema_ves, 2) }}</p>
                </td>

                {{-- Columna 5: Cuadre y Diferencia --}}
                <td class="px-6 py-4 text-center">
                    @if(is_null($sesion->fecha_cierre))
                        <span class="text-xs text-gray-400 italic">Esperando cierre...</span>
                    @else
                        @if($sesion->diferencia_usd == 0)
                            <span class="inline-flex items-center gap-1 text-[11px] font-black text-green-600 bg-green-50 px-3 py-1 rounded-lg border border-green-200">
                                <span class="material-symbols-outlined text-[16px]">balance</span> CUADRE EXACTO
                            </span>
                        @elseif($sesion->diferencia_usd > 0)
                            <span class="inline-flex items-center gap-1 text-[11px] font-black text-blue-600 bg-blue-50 px-3 py-1 rounded-lg border border-blue-200">
                                <span class="material-symbols-outlined text-[16px]">arrow_upward</span> SOBRÓ ${{ number_format($sesion->diferencia_usd, 2) }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[11px] font-black text-red-600 bg-red-50 px-3 py-1 rounded-lg border border-red-200">
                                <span class="material-symbols-outlined text-[16px]">arrow_downward</span> FALTÓ ${{ number_format(abs($sesion->diferencia_usd), 2) }}
                            </span>
                        @endif
                    @endif
                </td>

                {{-- Columna 6: Acciones --}}
                <td class="px-6 py-4 text-right">
                    <button onclick="verMovimientos({{ $sesion->id }})" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-teal-600 hover:border-teal-200 hover:bg-teal-50 flex items-center justify-center transition-all shadow-sm ml-auto" title="Ver Movimientos">
                        <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-16 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                            <span class="material-symbols-outlined text-3xl text-gray-400">store</span>
                        </div>
                        <p class="font-bold text-gray-600">No hay historial de cajas</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="px-6 py-4 border-t border-gray-100 flex justify-center ajax-pagination">
    {{ $sesiones->links('pagination::tailwind') }}
</div>