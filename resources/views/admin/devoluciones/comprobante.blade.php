<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante Devolución {{ $devolucion->codigo_devolucion }} - Agrosuministros Rodríguez</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Courier New', Courier, monospace, 'Segoe UI', sans-serif;
            font-size: 11.5px;
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
            padding: 14px 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-radius: 4px;
        }
        .header { text-align: center; margin-bottom: 8px; }
        .logo { max-width: 120px; height: auto; margin: 0 auto 6px; display: block; filter: grayscale(100%) contrast(150%); }
        .company-name { font-size: 13px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; }
        .company-info { font-size: 10px; margin-top: 2px; }
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        .ticket-title {
            text-align: center; font-size: 13px; font-weight: 900;
            padding: 4px 0; text-transform: uppercase; letter-spacing: 1px;
            border-top: 1px dashed #000; border-bottom: 1px dashed #000; margin: 6px 0;
        }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 2px; font-size: 10.5px; }
        .info-label { font-weight: bold; }
        .table-items { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .table-items th { font-size: 9.5px; text-transform: uppercase; border-bottom: 1px dashed #000; padding: 3px 0; }
        .table-items td { font-size: 10px; padding: 4px 0; vertical-align: top; }
        .total-box { margin-top: 6px; }
        .total-row { display: flex; justify-content: space-between; margin-bottom: 2px; font-size: 11px; }
        .total-final {
            font-size: 13px; font-weight: 900; border-top: 1px dashed #000;
            border-bottom: 1px dashed #000; padding: 5px 0; margin-top: 4px;
        }
        .badge-box {
            background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 4px;
            padding: 6px; margin: 8px 0; font-size: 10px;
        }
        .signature-area { margin-top: 25px; text-align: center; font-size: 9.5px; }
        .signature-line { border-top: 1px solid #000; width: 80%; margin: 30px auto 4px; }
        .no-print-bar {
            width: 80mm; max-width: 100%; margin-bottom: 15px; display: flex; gap: 8px; justify-content: center;
        }
        .btn-print {
            background: #1e549e; color: white; border: none; padding: 8px 16px;
            border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 12px;
        }
        .btn-close {
            background: #e2e8f0; color: #334155; border: none; padding: 8px 16px;
            border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 12px;
        }
        @media print {
            body { background: white; padding: 0; }
            .no-print-bar { display: none !important; }
            .ticket-container { box-shadow: none; padding: 0; width: 100%; }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <button class="btn-print" onclick="window.print()">Imprimir Comprobante</button>
        <button class="btn-close" onclick="window.close()">Cerrar Ventana</button>
    </div>

    <div class="ticket-container">
        <div class="header">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo">
            <div class="company-name">{{ strtoupper($config->nombre_comercio ?? $config->nombre_empresa ?? \App\Models\ConfiguracionTienda::nombre()) }}</div>
            <div class="company-info">RIF: {{ $config->rif ?? \App\Models\ConfiguracionTienda::rif() }}</div>
            <div class="company-info">{{ $config->direccion_fisica ?? $config->direccion ?? \App\Models\ConfiguracionTienda::direccion() }}</div>
            <div class="company-info">Tel: {{ $config->telefono_contacto ?? $config->telefono ?? \App\Models\ConfiguracionTienda::telefono() }}</div>
        </div>

        <div class="ticket-title">
            COMPROBANTE DE DEVOLUCIÓN
        </div>

        <div class="info-row">
            <span class="info-label">CÓDIGO:</span>
            <span style="font-weight: 900;">{{ $devolucion->codigo_devolucion }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">FECHA:</span>
            <span>{{ \Carbon\Carbon::parse($devolucion->fecha_devolucion)->format('d/m/Y h:i A') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">FACTURA AFECTADA:</span>
            <span>{{ $devolucion->factura ? $devolucion->factura->numero_factura : 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">CLIENTE:</span>
            <span style="max-width: 170px; text-align: right; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                {{ $devolucion->factura ? ($devolucion->factura->nombre_razon_social ?: 'Consumidor Final') : 'Cliente' }}
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">DOC / RIF:</span>
            <span>{{ $devolucion->factura ? ($devolucion->factura->cedula_rif_cliente ?: 'V-00000000') : 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">PROCESADO POR:</span>
            <span>{{ $devolucion->usuario ? $devolucion->usuario->nombre . ' ' . $devolucion->usuario->apellido : 'Cajero' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">TIPO:</span>
            <span style="font-weight: bold; text-transform: uppercase;">{{ $devolucion->tipo_devolucion === 'total' ? 'Devolución Total' : 'Devolución Parcial' }}</span>
        </div>

        <div class="divider"></div>

        <table class="table-items">
            <thead>
                <tr>
                    <th style="text-align: left; width: 55%;">PRODUCTO</th>
                    <th style="text-align: center; width: 15%;">CANT</th>
                    <th style="text-align: right; width: 30%;">TOTAL $</th>
                </tr>
            </thead>
            <tbody>
                @foreach($devolucion->detalles as $det)
                <tr>
                    <td style="text-align: left;">
                        <div>{{ $det->producto ? $det->producto->nombre : 'Producto' }}</div>
                        <div style="font-size: 8.5px; color: #555;">@ ${{ number_format($det->precio_unitario_usd, 2) }} | {{ ucfirst(str_replace('_', ' ', $det->motivo_devolucion)) }}</div>
                    </td>
                    <td style="text-align: center;">{{ (float)$det->cantidad_devuelta }}</td>
                    <td style="text-align: right; font-weight: bold;">${{ number_format($det->monto_subtotal_usd, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <div class="total-box">
            <div class="total-row">
                <span>MÉTODO REEMBOLSO:</span>
                <span style="font-weight: bold; text-transform: uppercase;">{{ str_replace('_', ' ', $devolucion->metodo_reembolso) }}</span>
            </div>
            <div class="total-row">
                <span>TASA BCV APLICADA:</span>
                <span>Bs. {{ number_format($devolucion->tasa_bcv, 2) }}</span>
            </div>
            <div class="total-row total-final">
                <span>TOTAL REEMBOLSADO USD:</span>
                <span>${{ number_format($devolucion->monto_total_usd, 2) }}</span>
            </div>
            <div class="total-row" style="font-size: 12px; font-weight: 900; margin-top: 3px;">
                <span>TOTAL REEMBOLSADO VES:</span>
                <span>Bs. {{ number_format($devolucion->monto_total_ves, 2) }}</span>
            </div>
        </div>

        @if($devolucion->motivo_general || $devolucion->observaciones)
        <div class="badge-box">
            <strong>MOTIVO:</strong> {{ $devolucion->motivo_general }}<br>
            @if($devolucion->observaciones)
            <div style="margin-top: 3px; font-size: 9.5px; color: #333;"><strong>OBSERVACIONES:</strong> {{ $devolucion->observaciones }}</div>
            @endif
        </div>
        @endif

        <div class="info-row" style="font-size: 9.5px; margin-top: 4px;">
            <span>REINGRESO A INVENTARIO:</span>
            <span>{{ $devolucion->reingresar_stock ? 'SÍ (Stock reincorporado)' : 'NO (Descarte/Garantía)' }}</span>
        </div>

        <div class="signature-area">
            <div class="signature-line"></div>
            <div>FIRMA Y HUELLA DEL CLIENTE</div>
            <div style="font-size: 8.5px; color: #666; margin-top: 1px;">Conforme con el reembolso recibido</div>
        </div>

        <div class="divider" style="margin-top: 15px;"></div>

        <div style="text-align: center; font-size: 9px; color: #444; margin-top: 6px;">
            <p>Sistema ERP - Agrosuministros Rodríguez</p>
            <p>Comprobante de auditoría y respaldo fiscal</p>
        </div>
    </div>

</body>
</html>
