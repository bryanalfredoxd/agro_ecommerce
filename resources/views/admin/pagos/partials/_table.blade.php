<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50/50">
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Pedido / Cliente</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Método / Ref.</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Monto Reportado</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Estado / Verificador</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($pagos as $pago)
            <tr class="hover:bg-gray-50/50 transition-colors group">
                
                {{-- Columna 1: Pedido y Cliente --}}
                <td class="px-6 py-4">
                    <div class="flex flex-col gap-1">
                        {{-- ENLACE CORREGIDO: Llama a verDetallePedido() --}}
                        <a href="#" onclick="verDetallePedido({{ $pago->pedido_id }}); return false;" class="font-black text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1 transition-colors" title="Ver detalles del pedido">
                            #{{ str_pad($pago->pedido_id, 6, '0', STR_PAD_LEFT) }}
                            <span class="material-symbols-outlined text-[14px]">visibility</span>
                        </a>
                        <span class="text-[11px] text-gray-800 font-bold capitalize mt-1 truncate max-w-[180px]">
                            {{ $pago->pedido->usuario->nombre ?? 'N/A' }} {{ $pago->pedido->usuario->apellido ?? '' }}
                        </span>
                        <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d M Y, h:i A') }}</span>
                    </div>
                </td>

                {{-- Columna 2: Método y Referencia --}}
                <td class="px-6 py-4">
                    @php
                        $metodoBadge = match($pago->metodo) {
                            'pago_movil' => 'bg-purple-50 text-purple-700 border-purple-200',
                            'zelle' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                            'binance' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                            'transferencia' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'efectivo_usd', 'efectivo_bs' => 'bg-green-50 text-green-700 border-green-200',
                            default => 'bg-gray-50 text-gray-700 border-gray-200',
                        };
                    @endphp
                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider border {{ $metodoBadge }}">
                        {{ str_replace('_', ' ', $pago->metodo) }}
                    </span>
                    <div class="mt-1.5 flex items-center gap-2">
                        <span class="text-xs text-gray-400 font-bold">REF:</span>
                        <span class="font-mono text-sm font-black text-agro-dark bg-gray-100 px-1.5 py-0.5 rounded">{{ $pago->referencia_bancaria ?? 'S/R' }}</span>
                    </div>
                </td>

                {{-- Columna 3: Monto --}}
                <td class="px-6 py-4">
                    <p class="font-black text-agro-dark text-base">${{ number_format($pago->monto_usd, 2) }}</p>
                    <p class="text-[10px] font-bold text-gray-400">Bs. {{ number_format($pago->monto_ves, 2) }}</p>
                </td>

                {{-- Columna 4: Estado --}}
                <td class="px-6 py-4 text-center">
                    @php
                        $estadoBadge = match($pago->estado) {
                            'revision' => 'bg-amber-50 text-amber-600 border-amber-200',
                            'aprobado' => 'bg-green-50 text-green-600 border-green-200',
                            'rechazado' => 'bg-red-50 text-red-600 border-red-200',
                        };
                        $estadoIcon = match($pago->estado) {
                            'revision' => 'pending',
                            'aprobado' => 'check_circle',
                            'rechazado' => 'cancel',
                        };
                    @endphp
                    <div class="flex flex-col items-center gap-1">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg border text-[10px] font-black uppercase tracking-widest {{ $estadoBadge }}">
                            <span class="material-symbols-outlined text-[14px]">{{ $estadoIcon }}</span>
                            {{ $pago->estado }}
                        </span>
                        @if($pago->verificador)
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">Por: {{ $pago->verificador->nombre }}</span>
                        @endif
                    </div>
                </td>

                {{-- Columna 5: Acciones (Revisar, Aprobar, Rechazar) --}}
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        
                        {{-- Botón Ver Comprobante --}}
                        @if($pago->captura_pago_url)
                            <button onclick="verComprobante('{{ asset('storage/' . $pago->captura_pago_url) }}', '{{ $pago->referencia_bancaria }}')" class="w-8 h-8 rounded-lg bg-gray-100 border border-gray-200 text-gray-600 hover:bg-gray-200 flex items-center justify-center transition-all shadow-sm" title="Ver Comprobante">
                                <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                            </button>
                        @endif

                        {{-- Botones de Acción (Solo si está en revisión) --}}
                        @if($pago->estado === 'revision')
                            <button onclick="aprobarPago({{ $pago->id }}, '{{ $pago->referencia_bancaria }}')" class="w-8 h-8 rounded-lg bg-green-50 border border-green-200 text-green-600 hover:bg-green-600 hover:text-white flex items-center justify-center transition-all shadow-sm" title="Aprobar Pago">
                                <span class="material-symbols-outlined text-[18px]">check</span>
                            </button>
                            <button onclick="rechazarPago({{ $pago->id }}, '{{ $pago->referencia_bancaria }}')" class="w-8 h-8 rounded-lg bg-red-50 border border-red-200 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all shadow-sm" title="Rechazar Pago">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-16 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                            <span class="material-symbols-outlined text-3xl text-gray-400">fact_check</span>
                        </div>
                        <p class="font-bold text-gray-600">No hay pagos para mostrar</p>
                        <p class="text-xs mt-1">Prueba seleccionando otra pestaña.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="px-6 py-4 border-t border-gray-100 flex justify-center ajax-pagination">
    {{ $pagos->links('pagination::tailwind') }}
</div>