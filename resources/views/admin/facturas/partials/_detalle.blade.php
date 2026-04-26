<div id="documentoFactura" class="bg-white max-w-2xl mx-auto relative overflow-hidden" style="min-height: 297mm; font-family: 'Courier New', Courier, monospace; padding: 20px;">
       
    @if($factura->estado === 'anulada')
        <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 -rotate-45 opacity-20 pointer-events-none">
            <h1 class="text-8xl font-black text-red-600 border-8 border-red-600 p-4 tracking-widest uppercase">ANULADA</h1>
        </div>
    @endif

    {{-- Encabezado Factura --}}
    <div class="p-8 border-b-2 border-dashed border-gray-300">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-2xl font-black text-gray-900 tracking-wider">CORPO AGRÍCOLA C.A.</h2>
                <p class="text-xs text-gray-600 mt-1">RIF: J-12345678-9</p>
                <p class="text-xs text-gray-600">Barinitas, Estado Barinas</p>
            </div>
            <div class="text-right">
                <h1 class="text-3xl font-black text-teal-700 tracking-widest">FACTURA</h1>
                <p class="text-lg font-bold text-gray-800 mt-1">Nº {{ $factura->numero_factura }}</p>
                <p class="text-xs text-gray-500 font-bold mt-1">FECHA: {{ \Carbon\Carbon::parse($factura->fecha_emision)->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 text-sm">
            <div class="grid grid-cols-3 gap-2">
                <div class="col-span-2">
                    <span class="font-bold text-gray-500">CLIENTE:</span>
                    <span class="font-bold text-gray-900 uppercase ml-2">{{ $factura->nombre_razon_social ?? 'CONSUMIDOR FINAL' }}</span>
                </div>
                <div>
                    <span class="font-bold text-gray-500">CI/RIF:</span>
                    <span class="font-bold text-gray-900 uppercase ml-2">{{ $factura->cedula_rif_cliente ?? 'N/A' }}</span>
                </div>
                <div class="col-span-3">
                    <span class="font-bold text-gray-500">DIRECCIÓN:</span>
                    <span class="text-gray-900 ml-2 uppercase">{{ $factura->direccion_fiscal ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Cuerpo / Productos --}}
    <div class="p-8 min-h-[400px]">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b-2 border-gray-800">
                    <th class="py-2 text-left w-16">CANT</th>
                    <th class="py-2 text-left">DESCRIPCIÓN</th>
                    <th class="py-2 text-right">P.UNIT (USD)</th>
                    <th class="py-2 text-right">TOTAL (USD)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @if($factura->pedido && $factura->pedido->detalles)
                    @foreach($factura->pedido->detalles as $detalle)
                    <tr>
                        <td class="py-3 font-bold">{{ number_format($detalle->cantidad_solicitada, 2) }}</td>
                        <td class="py-3 pr-4 uppercase text-gray-800">{{ $detalle->producto->nombre ?? 'Producto Genérico' }}</td>
                        <td class="py-3 text-right text-gray-600">${{ number_format($detalle->precio_historico_usd, 2) }}</td>
                        <td class="py-3 text-right font-bold text-gray-900">${{ number_format($detalle->cantidad_solicitada * $detalle->precio_historico_usd, 2) }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="py-8 text-center text-gray-700 italic">No hay detalles de productos asociados.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- Totales --}}
    <div class="p-8 border-t-2 border-gray-800 bg-gray-50">
        <div class="flex justify-end">
            <div class="w-64 space-y-2 text-sm">
                <div class="flex justify-between items-center text-gray-600">
                    <span>SUBTOTAL:</span>
                    <span>${{ number_format($factura->subtotal_usd, 2) }}</span>
                </div>
                <div class="flex justify-between items-center text-gray-600">
                    <span>I.V.A (16%):</span>
                    <span>${{ number_format($factura->impuesto_usd, 2) }}</span>
                </div>
                <div class="flex justify-between items-end pt-2 border-t border-gray-300">
                    <span class="font-bold text-gray-800 text-base">TOTAL USD:</span>
                    <span class="font-black text-xl text-gray-900">${{ number_format($factura->total_usd, 2) }}</span>
                </div>
                <div class="flex justify-between items-end mt-2 pt-2 border-t-2 border-dashed border-gray-300">
                    <span class="font-bold text-gray-500">TASA BCV:</span>
                    <span class="font-bold text-gray-500">Bs. {{ number_format($factura->valor_tasa_bcv, 4) }}</span>
                </div>
                <div class="flex justify-between items-end bg-gray-200 p-2 rounded mt-2">
                    <span class="font-bold text-gray-800 text-base">TOTAL VES:</span>
                    <span class="font-black text-xl text-gray-900">Bs. {{ number_format($factura->total_ves, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="mt-8 text-center text-[10px] text-gray-700 font-bold uppercase tracking-widest">
            *** Copia Contable Sin Derecho a Crédito Fiscal *** <br>
            Sistema ERP Corpo Agrícola
        </div>
    </div>
</div>