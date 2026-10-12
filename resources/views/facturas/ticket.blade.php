<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket {{ $factura->numero_factura }} - Agrosuministros Rodríguez</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace, 'Segoe UI', Tahoma, sans-serif;
            font-size: 12px;
            line-height: 1.35;
            color: #000;
            background-color: #f1f5f9;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .ticket-container {
            width: 80mm;
            max-width: 100%;
            background: #fff;
            padding: 12px 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-radius: 4px;
        }
        .header {
            text-align: center;
            margin-bottom: 8px;
        }
        .logo {
            max-width: 130px;
            height: auto;
            margin: 0 auto 6px auto;
            display: block;
            filter: grayscale(100%) contrast(150%);
        }
        .company-name {
            font-size: 14px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .company-info {
            font-size: 10px;
            margin-top: 2px;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .ticket-title {
            text-align: center;
            font-size: 13px;
            font-weight: 900;
            margin: 4px 0;
            letter-spacing: 1px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 10.5px;
            margin-bottom: 2px;
        }
        .info-label {
            font-weight: bold;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 11px;
        }
        .items-table th {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 4px 0;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-section {
            margin-top: 6px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 3px;
        }
        .total-highlight {
            font-size: 14px;
            font-weight: 900;
            margin-top: 4px;
            padding-top: 4px;
            border-top: 1px dashed #000;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 12px;
            line-height: 1.4;
        }
        .actions-bar {
            margin-bottom: 15px;
            display: flex;
            gap: 10px;
        }
        .btn-print {
            background-color: #1e549e;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 13px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(30,84,158,0.3);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-a4 {
            background-color: #059669;
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 13px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(5,150,105,0.25);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: background 0.15s ease;
        }
        .btn-a4:hover {
            background-color: #047857;
        }
        .btn-close {
            background-color: #e2e8f0;
            color: #334155;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 13px;
            cursor: pointer;
        }

        @media print {
            body {
                background: none;
                padding: 0;
                margin: 0;
            }
            .actions-bar {
                display: none !important;
            }
            .ticket-container {
                box-shadow: none;
                border-radius: 0;
                width: 100%;
                padding: 4px 6px;
            }
            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <div class="actions-bar">
        <button class="btn-print" onclick="window.print()">
            🖨️ Imprimir Ticket (80mm)
        </button>
        <a href="{{ route('factura.imprimir', $factura->id) }}" class="btn-a4">
            📄 Factura A4 (PDF)
        </a>
        <button class="btn-close" onclick="window.history.back()">
            ✕ Volver
        </button>
    </div>

    <div class="ticket-container">
        <div class="header">
            <img src="{{ asset('img/logo.png') }}" alt="{{ $config->nombre_comercio ?? $config->nombre_empresa ?? \App\Models\ConfiguracionTienda::nombre() }}" class="logo">
            <div class="company-name">{{ $config->nombre_comercio ?? $config->nombre_empresa ?? \App\Models\ConfiguracionTienda::nombre() }}</div>
            <div class="company-info">RIF: {{ $config->rif ?? \App\Models\ConfiguracionTienda::rif() }}</div>
            <div class="company-info">{{ $config->direccion_fisica ?? $config->direccion ?? \App\Models\ConfiguracionTienda::direccion() }}</div>
            <div class="company-info">Tel / WhatsApp: {{ $config->telefono_contacto ?? $config->telefono ?? \App\Models\ConfiguracionTienda::telefono() }}</div>
        </div>

        <div class="divider"></div>

        <div class="ticket-title">TICKET DE VENTA</div>

        <div class="info-row">
            <span class="info-label">Factura Nº:</span>
            <span>{{ $factura->numero_factura }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Pedido ID:</span>
            <span>#{{ $factura->pedido_id }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha:</span>
            <span>{{ \Carbon\Carbon::parse($factura->fecha_emision)->format('d/m/Y H:i:s') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Cliente:</span>
            <span>{{ Str::limit($factura->nombre_razon_social, 22) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Doc/RIF:</span>
            <span>{{ $factura->cedula_rif_cliente ?: 'V-00000000' }}</span>
        </div>
        @if($factura->pedido && $factura->pedido->canal_venta)
        <div class="info-row">
            <span class="info-label">Canal:</span>
            <span>{{ strtoupper(in_array($factura->pedido->canal_venta, ['pos', 'tienda_fisica']) ? 'MOSTRADOR POS' : 'TIENDA ONLINE') }}</span>
        </div>
        @endif

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Desc.</th>
                    <th class="text-center" style="width: 20%;">Cant</th>
                    <th class="text-right" style="width: 30%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @if($factura->pedido && $factura->pedido->detalles)
                    @foreach($factura->pedido->detalles as $det)
                    @php
                        $uRaw = $det->producto && $det->producto->unidad_medida ? $det->producto->unidad_medida : 'ud';
                        $uLabel = match(strtolower(trim($uRaw))) {
                            'unidad' => 'ud',
                            'litro' => 'Lt',
                            default => strtolower(trim($uRaw))
                        };
                    @endphp
                    <tr>
                        <td>
                            {{ Str::limit($det->producto ? $det->producto->nombre : 'Producto', 18) }}
                            <div style="font-size: 9px; color: #555;">@ ${{ number_format($det->precio_historico_usd, 2) }}/{{ $uLabel }}</div>
                        </td>
                        <td class="text-center">{{ (float) $det->cantidad_solicitada }} {{ $uLabel }}</td>
                        <td class="text-right">${{ number_format($det->precio_historico_usd * $det->cantidad_solicitada, 2) }}</td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <div class="divider"></div>

        <div class="totals-section">
            <div class="total-row">
                <span>SUBTOTAL USD:</span>
                <span>${{ number_format($factura->subtotal_usd, 2) }}</span>
            </div>
            @if($factura->pedido && $factura->pedido->costo_delivery_usd > 0)
            <div class="total-row">
                <span>DELIVERY:</span>
                <span>${{ number_format($factura->pedido->costo_delivery_usd, 2) }}</span>
            </div>
            @endif
            <div class="total-row">
                <span>IVA ({{ (float)($config->iva_porcentaje ?? 16) }}%):</span>
                <span>${{ number_format($factura->impuesto_usd, 2) }}</span>
            </div>
            <div class="total-row total-highlight">
                <span>TOTAL USD:</span>
                <span>${{ number_format($factura->total_usd, 2) }}</span>
            </div>
            <div class="total-row" style="font-size: 10px; color: #444; margin-top: 4px;">
                <span>TASA BCV:</span>
                <span>Bs. {{ number_format($factura->valor_tasa_bcv, 2) }}</span>
            </div>
            <div class="total-row" style="font-size: 12px; font-weight: bold;">
                <span>TOTAL VES:</span>
                <span>Bs. {{ number_format($factura->total_ves, 2) }}</span>
            </div>
            @if($factura->pedido && $factura->pedido->pago)
            <div class="total-row" style="font-size: 10px; margin-top: 4px;">
                <span>FORMA DE PAGO:</span>
                <span>{{ strtoupper(str_replace('_', ' ', $factura->pedido->pago->metodo)) }}</span>
            </div>
            @if($factura->pedido->pago->referencia_bancaria)
            <div class="info-row" style="font-size: 9.5px;">
                <span>REF:</span>
                <span>{{ $factura->pedido->pago->referencia_bancaria }}</span>
            </div>
            @endif
            @endif
        </div>

        <div class="divider"></div>

        <div class="footer">
            <p><strong>¡GRACIAS POR SU COMPRA!</strong></p>
            <p style="margin-top: 4px;">Insumos y servicios para el desarrollo agropecuario.</p>
            <p style="margin-top: 4px; font-size: 9px; color: #555;">*** Comprobante de Entrega ***</p>
            <p style="margin-top: 2px; font-size: 8.5px; color: #777;">Sistema ERP Agrosuministros Rodríguez</p>
        </div>
    </div>

</body>
</html>
