<div class="ticket-view-wrapper relative bg-white text-black p-4 sm:p-5 rounded-md shadow-md max-w-[340px] mx-auto select-text" style="font-family: 'Courier New', Courier, monospace; font-size: 11.5px; line-height: 1.35;">
    
    @if($factura->estado === 'anulada')
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-10">
            <div class="border-4 border-red-600 text-red-600 font-black text-3xl px-5 py-2 rotate-[-25deg] opacity-75 tracking-widest uppercase bg-white/85 rounded-lg shadow-sm">
                ANULADA
            </div>
        </div>
    @elseif($factura->estado === 'reembolsada')
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-10">
            <div class="border-4 border-amber-600 text-amber-600 font-black text-2xl px-4 py-2 rotate-[-20deg] opacity-80 tracking-widest uppercase bg-white/90 rounded-lg shadow-sm">
                REEMBOLSADA
            </div>
        </div>
    @endif

    {{-- Logo y Cabecera --}}
    <div class="text-center mb-2">
        <img src="{{ asset('img/logo.png') }}" alt="{{ $config->nombre_comercio ?? $config->nombre_empresa ?? \App\Models\ConfiguracionTienda::nombre() }}" class="w-24 h-auto mx-auto mb-2.5 block object-contain" style="filter: grayscale(100%) contrast(150%);">
        <h2 class="font-black text-[13px] tracking-wide uppercase leading-tight">{{ strtoupper($config->nombre_comercio ?? $config->nombre_empresa ?? \App\Models\ConfiguracionTienda::nombre()) }}</h2>
        <p class="text-[10px] mt-0.5 font-bold">RIF: {{ $config->rif ?? \App\Models\ConfiguracionTienda::rif() }}</p>
        <p class="text-[10px] text-gray-800">{{ $config->direccion_fisica ?? $config->direccion ?? \App\Models\ConfiguracionTienda::direccion() }}</p>
        <p class="text-[10px] text-gray-800">Tel / WhatsApp: {{ $config->telefono_contacto ?? $config->telefono ?? \App\Models\ConfiguracionTienda::telefono() }}</p>
    </div>

    {{-- Separador discontinuo --}}
    <div class="border-t border-dashed border-black my-2"></div>

    <div class="text-center font-black text-xs tracking-wider uppercase mb-2">
        TICKET DE VENTA
    </div>

    {{-- Datos de Factura y Cliente --}}
    <div class="space-y-0.5 text-[10.5px]">
        <div class="flex justify-between">
            <span class="font-bold">Factura Nº:</span>
            <span class="font-bold">{{ $factura->numero_factura }}</span>
        </div>
        <div class="flex justify-between">
            <span class="font-bold">Pedido ID:</span>
            <span>#{{ $factura->pedido_id }}</span>
        </div>
        <div class="flex justify-between">
            <span class="font-bold">Fecha:</span>
            <span>{{ \Carbon\Carbon::parse($factura->fecha_emision)->format('d/m/Y H:i:s') }}</span>
        </div>
        <div class="flex justify-between">
            <span class="font-bold">Cliente:</span>
            <span class="text-right truncate max-w-[190px]">{{ $factura->nombre_razon_social ?: 'Consumidor Final' }}</span>
        </div>
        <div class="flex justify-between">
            <span class="font-bold">Doc/RIF:</span>
            <span>{{ $factura->cedula_rif_cliente ?: 'V-00000000' }}</span>
        </div>
        @if($factura->pedido && $factura->pedido->canal_venta)
        <div class="flex justify-between">
            <span class="font-bold">Canal:</span>
            <span>{{ strtoupper(in_array($factura->pedido->canal_venta, ['pos', 'tienda_fisica']) ? 'MOSTRADOR POS' : 'TIENDA ONLINE') }}</span>
        </div>
        @endif
    </div>

    {{-- Separador discontinuo --}}
    <div class="border-t border-dashed border-black my-2"></div>

    {{-- Tabla de Productos --}}
    <table class="w-full text-[10.5px] border-collapse">
        <thead>
            <tr class="border-b border-dashed border-black text-[9.5px] uppercase">
                <th class="text-left py-1 font-bold" style="width: 52%;">DESC.</th>
                <th class="text-center py-1 font-bold" style="width: 18%;">CANT</th>
                <th class="text-right py-1 font-bold" style="width: 30%;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @if($factura->pedido && $factura->pedido->detalles)
                @foreach($factura->pedido->detalles as $det)
                <tr>
                    <td class="py-1 text-left align-top leading-tight">
                        <div class="truncate max-w-[150px]">{{ $det->producto ? $det->producto->nombre : 'Producto' }}</div>
                        <div class="text-[9px] text-gray-600">@ ${{ number_format($det->precio_historico_usd, 2) }}</div>
                    </td>
                    <td class="py-1 text-center align-top">{{ (float) $det->cantidad_solicitada }}</td>
                    <td class="py-1 text-right align-top font-bold">${{ number_format($det->precio_historico_usd * $det->cantidad_solicitada, 2) }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3" class="text-center py-2 text-gray-500 italic">Sin detalles registrados</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- Separador discontinuo --}}
    <div class="border-t border-dashed border-black my-2"></div>

    {{-- Totales --}}
    <div class="space-y-1 text-[10.5px]">
        <div class="flex justify-between">
            <span>SUBTOTAL USD:</span>
            <span>${{ number_format($factura->subtotal_usd, 2) }}</span>
        </div>
        @if($factura->pedido && $factura->pedido->descuento_usd > 0)
        <div class="flex justify-between text-red-600">
            <span>DESCUENTO:</span>
            <span>-${{ number_format($factura->pedido->descuento_usd, 2) }}</span>
        </div>
        @endif
        @if($factura->pedido && $factura->pedido->costo_delivery_usd > 0)
        <div class="flex justify-between">
            <span>DELIVERY:</span>
            <span>${{ number_format($factura->pedido->costo_delivery_usd, 2) }}</span>
        </div>
        @endif
        @if($factura->pedido && $factura->pedido->recargo_usd > 0)
        <div class="flex justify-between">
            <span>RECARGO ({{ (float)$factura->pedido->recargo_porcentaje }}%):</span>
            <span>${{ number_format($factura->pedido->recargo_usd, 2) }}</span>
        </div>
        @endif
        @php
            $subtotalNeto = (float) ($factura->subtotal_usd ?? 0);
            $impuestoMonto = (float) ($factura->impuesto_usd ?? ($factura->monto_iva_usd ?? 0));
            $porcentajeIva = (float) ($config->iva_porcentaje ?? ($factura->iva_porcentaje ?? 16.0));
            if ($porcentajeIva <= 0) $porcentajeIva = 16.0;
            $recargoMonto = (float) ($factura->pedido->recargo_usd ?? 0);
            if ($recargoMonto > 0 && abs($impuestoMonto - round($subtotalNeto * ($porcentajeIva / 100), 2)) > 0.05) {
                $montoIvaEst = round($subtotalNeto * ($porcentajeIva / 100), 2);
                if (abs(($montoIvaEst + $recargoMonto) - $impuestoMonto) < 0.05) {
                    $impuestoMonto = $montoIvaEst;
                }
            }
        @endphp
        <div class="flex justify-between">
            <span>IVA ({{ (float)$porcentajeIva }}%):</span>
            <span>${{ number_format($impuestoMonto, 2) }}</span>
        </div>

        <div class="border-t border-dashed border-black pt-1.5 flex justify-between items-baseline font-black text-sm">
            <span>TOTAL USD:</span>
            <span class="text-base">${{ number_format($factura->total_usd, 2) }}</span>
        </div>

        <div class="flex justify-between text-[10px] text-gray-700 pt-0.5">
            <span>TASA BCV:</span>
            <span>Bs. {{ number_format($factura->valor_tasa_bcv, 2) }}</span>
        </div>
        <div class="flex justify-between font-black text-[12px]">
            <span>TOTAL VES:</span>
            <span>Bs. {{ number_format($factura->total_ves, 2) }}</span>
        </div>

        @if($factura->pedido && $factura->pedido->pago)
        <div class="flex justify-between text-[10px] pt-1.5 border-t border-dashed border-gray-400">
            <span>FORMA DE PAGO:</span>
            <span class="font-bold">{{ strtoupper(str_replace('_', ' ', $factura->pedido->pago->metodo)) }}</span>
        </div>
        @if($factura->pedido->pago->referencia_bancaria)
        <div class="flex justify-between text-[9.5px]">
            <span>REF:</span>
            <span class="font-bold">{{ $factura->pedido->pago->referencia_bancaria }}</span>
        </div>
        @endif
        @endif
    </div>

    @if($factura->devoluciones && $factura->devoluciones->count() > 0)
    {{-- Registro de Devoluciones Procesadas --}}
    <div class="border-t border-dashed border-black my-2"></div>
    <div class="text-left bg-amber-50 p-2 border border-dashed border-amber-400 rounded text-[9.5px]" style="background-color: #fffbeb; border-color: #f59e0b;">
        <div class="font-bold text-amber-950 uppercase tracking-wide flex items-center justify-between" style="color: #451a03;">
            <span>DEVOLUCIONES ACREDITADAS:</span>
            <span class="font-mono">{{ $factura->devoluciones->count() }}</span>
        </div>
        @foreach($factura->devoluciones as $dev)
        <div class="mt-1 pt-1 border-t border-dashed border-amber-300 flex justify-between" style="border-color: #fcd34d;">
            <div>
                <span class="font-bold text-amber-950" style="color: #451a03;">{{ $dev->codigo_devolucion }}</span>
                <span class="text-[8.5px] text-amber-900 block" style="color: #78350f;">{{ \Carbon\Carbon::parse($dev->fecha_devolucion)->format('d/m/Y') }} • {{ str_replace('_', ' ', $dev->metodo_reembolso) }}</span>
            </div>
            <div class="text-right font-black text-rose-700" style="color: #be123c;">
                -${{ number_format($dev->monto_total_usd, 2) }}
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Separador discontinuo --}}
    <div class="border-t border-dashed border-black my-2.5"></div>

    {{-- Pie de página del Ticket --}}
    <div class="text-center text-[9.5px] space-y-1">
        <p class="font-black text-[10px]">¡GRACIAS POR SU COMPRA!</p>
        <p class="text-gray-700">Insumos y servicios para el desarrollo agropecuario.</p>
        <p class="text-gray-500 text-[8.5px] pt-0.5">*** Comprobante de Entrega ***</p>
        <p class="text-gray-500 text-[8px]">Sistema ERP Agrosuministros Rodríguez</p>
    </div>

</div>