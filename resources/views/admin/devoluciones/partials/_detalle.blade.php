<div class="space-y-4 text-slate-800">
    {{-- Header Banner --}}
    <div class="bg-gradient-to-r from-amber-500 to-amber-600 -mx-5 -mt-5 p-5 text-white shadow-sm" style="background: linear-gradient(135deg, #d97706, #b45309); color: #ffffff;">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-xs text-white">
                    <span class="material-symbols-outlined text-2xl">assignment_return</span>
                </div>
                <div>
                    <h3 class="text-base font-black tracking-tight flex items-center gap-2 text-white">
                        <span>Devolución</span>
                        <span class="font-mono bg-white/20 px-2 py-0.5 rounded-lg text-xs text-white font-bold">{{ $devolucion->codigo_devolucion }}</span>
                    </h3>
                    <p class="text-xs text-amber-100 font-medium">
                        Fecha: {{ \Carbon\Carbon::parse($devolucion->fecha_devolucion)->format('d/m/Y h:i A') }}
                    </p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-white text-slate-900 shadow-sm border border-slate-200">
                    {{ $devolucion->tipo_devolucion === 'total' ? 'Devolución Total' : 'Devolución Parcial' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Info Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Factura Asociada</span>
            <span class="font-mono font-black text-slate-900 text-sm">
                {{ $devolucion->factura ? $devolucion->factura->numero_factura : 'Factura no vinculada' }}
            </span>
            <span class="text-[11px] text-slate-600 block mt-0.5 font-bold">
                Total Factura: ${{ number_format($devolucion->factura->total_usd ?? 0, 2) }}
            </span>
        </div>

        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Cliente Beneficiario</span>
            <span class="font-bold text-slate-900 text-sm truncate block">
                {{ $devolucion->factura ? ($devolucion->factura->nombre_razon_social ?: 'Consumidor Final') : 'Cliente' }}
            </span>
            <span class="text-[11px] text-slate-600 block mt-0.5 font-mono font-semibold">
                Doc/RIF: {{ $devolucion->factura ? ($devolucion->factura->cedula_rif_cliente ?: 'V-00000000') : 'N/A' }}
            </span>
        </div>

        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Método de Reembolso</span>
            <span class="font-black text-slate-900 text-sm capitalize flex items-center gap-1.5 mt-0.5">
                <span class="material-symbols-outlined text-[16px] text-amber-600">payments</span>
                {{ str_replace('_', ' ', $devolucion->metodo_reembolso) }}
            </span>
            <span class="text-[11px] text-slate-600 block mt-0.5 font-semibold">
                Tasa BCV: Bs. {{ number_format($devolucion->tasa_bcv, 2) }}
            </span>
        </div>

        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Atendido / Procesado Por</span>
            <span class="font-bold text-slate-900 text-sm block">
                {{ $devolucion->usuario ? ($devolucion->usuario->nombre . ' ' . $devolucion->usuario->apellido) : 'Cajero / Administrador' }}
            </span>
            <span class="text-[11px] {{ $devolucion->reingresar_stock ? 'text-emerald-700 font-extrabold' : 'text-rose-700 font-extrabold' }} block mt-0.5 flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">{{ $devolucion->reingresar_stock ? 'check_circle' : 'cancel' }}</span>
                {{ $devolucion->reingresar_stock ? 'Stock reingresado a tienda' : 'Sin reingreso a stock (Descarte)' }}
            </span>
        </div>
    </div>

    {{-- Tabla de Productos Devueltos --}}
    <div class="border border-slate-200 rounded-xl overflow-hidden shadow-2xs">
        <div class="bg-slate-100 px-4 py-2 border-b border-slate-200 flex justify-between items-center">
            <span class="text-xs font-black text-slate-800 uppercase tracking-wider">Productos Reintegrados / Devueltos</span>
            <span class="text-[10px] font-bold text-slate-600">{{ count($devolucion->detalles) }} ítem(s)</span>
        </div>
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] text-slate-600 uppercase tracking-wider font-bold">
                    <th class="px-4 py-2">Producto</th>
                    <th class="px-3 py-2 text-center">Cant.</th>
                    <th class="px-3 py-2 text-right">Precio Unit.</th>
                    <th class="px-4 py-2 text-right">Subtotal $</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @foreach($devolucion->detalles as $det)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="px-4 py-2.5">
                        <div class="font-bold text-slate-900 text-xs">{{ $det->producto ? $det->producto->nombre : 'Producto' }}</div>
                        <div class="text-[10px] text-slate-600 flex items-center gap-2 mt-0.5">
                            <span class="font-mono">SKU: {{ $det->producto ? $det->producto->sku : 'N/A' }}</span>
                            <span>•</span>
                            <span class="bg-amber-100 text-amber-900 border border-amber-200 px-1.5 py-0.5 rounded font-bold">{{ ucfirst(str_replace('_', ' ', $det->motivo_devolucion)) }}</span>
                        </div>
                    </td>
                    <td class="px-3 py-2.5 text-center font-black text-slate-900">
                        {{ (float)$det->cantidad_devuelta }}
                    </td>
                    <td class="px-3 py-2.5 text-right font-bold text-slate-700">
                        ${{ number_format($det->precio_unitario_usd, 2) }}
                    </td>
                    <td class="px-4 py-2.5 text-right font-black text-slate-950 text-xs">
                        ${{ number_format($det->monto_subtotal_usd, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Motivo y Observaciones --}}
    <div class="p-3.5 bg-amber-50 rounded-xl border border-amber-300 text-xs">
        <div class="font-black text-amber-950 mb-1 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[16px] text-amber-700">notes</span>
            <span>Motivo y Justificación de la Devolución:</span>
        </div>
        <p class="font-bold text-slate-900">{{ $devolucion->motivo_general }}</p>
        @if($devolucion->observaciones)
            <p class="text-slate-700 mt-1 pl-2 border-l-2 border-amber-500 italic font-medium">{{ $devolucion->observaciones }}</p>
        @endif
    </div>

    {{-- Resumen Total Reembolsado --}}
    <div class="bg-slate-900 text-white p-4 rounded-xl flex items-center justify-between shadow-md" style="background-color: #0f172a; color: #ffffff;">
        <div>
            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Dinero Reembolsado al Cliente</span>
            <span class="text-xs text-amber-400 font-bold">Equivalente a tasa oficial BCV</span>
        </div>
        <div class="text-right">
            <div class="text-2xl font-black text-emerald-400">${{ number_format($devolucion->monto_total_usd, 2) }}</div>
            <div class="text-xs font-bold text-slate-300">Bs. {{ number_format($devolucion->monto_total_ves, 2) }}</div>
        </div>
    </div>

    {{-- Acciones --}}
    <div class="pt-2 flex justify-end gap-2.5">
        <button type="button" onclick="cerrarDevolucionModal()" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 border-2 border-slate-300 rounded-xl font-bold text-xs transition-all shadow-2xs">
            Cerrar
        </button>
        <a href="{{ route('admin.devoluciones.comprobante', $devolucion->id) }}" target="_blank" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all" style="background-color: #d97706; color: #ffffff;">
            <span class="material-symbols-outlined text-[16px]">print</span>
            <span>Imprimir Comprobante 80mm</span>
        </a>
    </div>
</div>
