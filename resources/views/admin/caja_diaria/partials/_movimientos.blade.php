<div class="p-6 sm:p-8 bg-gray-50">
    
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Efectivo Inicial</p>
            <p class="text-xl font-black text-gray-800">${{ number_format($sesion->monto_inicial_usd, 2) }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Ventas del Turno</p>
            <p class="text-xl font-black text-teal-600">${{ number_format($sesion->total_ventas_sistema_usd, 2) }}</p>
        </div>
    </div>

    @if($sesion->fecha_cierre)
        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm mb-6 flex justify-between items-center {{ $sesion->diferencia_usd < 0 ? 'border-red-300 bg-red-50' : '' }}">
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Efectivo Declarado al Cerrar</p>
                <p class="text-xl font-black text-gray-800">${{ number_format($sesion->dinero_real_en_caja_usd, 2) }}</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Diferencia</p>
                <p class="text-xl font-black {{ $sesion->diferencia_usd < 0 ? 'text-red-600' : 'text-green-600' }}">
                    ${{ number_format($sesion->diferencia_usd, 2) }}
                </p>
            </div>
        </div>

        @if($sesion->observaciones_cierre)
            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-800">
                <span class="font-bold block mb-1">Nota del Cajero:</span>
                {{ $sesion->observaciones_cierre }}
            </div>
        @endif
    @endif

    <h4 class="text-sm font-black text-agro-dark mb-3">Historial de Movimientos</h4>
    
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-4 py-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Hora</th>
                    <th class="px-4 py-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Motivo</th>
                    <th class="px-4 py-2 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">USD</th>
                    <th class="px-4 py-2 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">VES</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($sesion->movimientos as $mov)
                <tr>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ \Carbon\Carbon::parse($mov->creado_at)->format('H:i:s') }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $mov->motivo }}
                        @if($mov->tipo == 'ingreso')
                            <span class="inline-block ml-2 px-1.5 py-0.5 rounded text-[9px] font-bold bg-green-100 text-green-700 uppercase">Ingreso</span>
                        @else
                            <span class="inline-block ml-2 px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-100 text-red-700 uppercase">Egreso</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right font-black {{ $mov->tipo == 'ingreso' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $mov->tipo == 'ingreso' ? '+' : '-' }}${{ number_format($mov->monto_usd, 2) }}
                    </td>
                    <td class="px-4 py-3 text-right text-xs font-bold text-gray-400">
                        {{ number_format($mov->monto_ves, 2) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-gray-400 italic">No se registraron movimientos en este turno.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>