<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cierre de Turno #{{ $sesion->id }} - Agrosuministros Rodríguez</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Courier New', Courier, monospace;
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
            padding: 12px 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-radius: 4px;
        }
        .header { text-align: center; margin-bottom: 8px; }
        .logo {
            max-width: 120px;
            height: auto;
            margin: 0 auto 6px auto;
            display: block;
            filter: grayscale(100%) contrast(150%);
        }
        .company-name { font-size: 13px; font-weight: 900; text-transform: uppercase; }
        .company-info { font-size: 10px; margin-top: 1px; }
        .divider { border-top: 1px dashed #000; margin: 7px 0; }
        .ticket-title {
            text-align: center;
            font-size: 13px;
            font-weight: 900;
            margin: 4px 0;
            letter-spacing: 0.5px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 10.5px;
            margin-bottom: 2px;
        }
        .info-label { font-weight: bold; }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            margin-top: 4px;
        }
        .table-data th {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 3px 0;
            text-align: left;
            font-size: 9.5px;
            text-transform: uppercase;
        }
        .table-data td { padding: 3px 0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .highlight-box {
            border: 1px dashed #000;
            padding: 6px 8px;
            margin: 6px 0;
            font-size: 11px;
        }
        .signatures {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            text-align: center;
            font-size: 9px;
        }
        .sign-col { flex: 1; }
        .sign-line { border-bottom: 1px dashed #000; height: 35px; margin-bottom: 4px; }
        .actions-bar {
            margin-bottom: 15px;
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 12px;
            cursor: pointer;
            border: none;
        }
        .btn-print { background-color: #1e549e; color: #fff; }
        .btn-close { background-color: #e2e8f0; color: #334155; }
        @media print {
            body { background: none; padding: 0; margin: 0; }
            .actions-bar { display: none !important; }
            .ticket-container { box-shadow: none; border-radius: 0; width: 100%; padding: 4px 6px; }
            @page { size: 80mm auto; margin: 0; }
        }
    </style>
</head>
<body>

    <div class="actions-bar">
        <button class="btn btn-print" onclick="window.print()">🖨️ Imprimir Reporte (80mm)</button>
        <button class="btn btn-close" onclick="window.close(); window.history.back();">✕ Cerrar</button>
    </div>

    <div class="ticket-container">
        <div class="header">
            <img src="{{ asset('img/logo.png') }}" alt="Agrosuministros Rodríguez" class="logo">
            <div class="company-name">Agrosuministros Rodríguez</div>
            <div class="company-info">RIF: J-50493821-4</div>
            <div class="company-info">Barinitas, Estado Barinas, Venezuela</div>
        </div>

        <div class="divider"></div>

        <div class="ticket-title">CIERRE DE TURNO / REPORTE Z</div>

        <div class="info-row">
            <span class="info-label">Turno ID:</span>
            <span>#{{ $sesion->id }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Caja:</span>
            <span>{{ $sesion->caja ? $sesion->caja->nombre : 'Caja Principal' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Cajero:</span>
            <span>{{ $sesion->cajero ? trim($sesion->cajero->nombre . ' ' . $sesion->cajero->apellido) : 'Cajero' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Apertura:</span>
            <span>{{ \Carbon\Carbon::parse($sesion->fecha_apertura)->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Cierre:</span>
            <span>{{ $sesion->fecha_cierre ? \Carbon\Carbon::parse($sesion->fecha_cierre)->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tickets Emitidos:</span>
            <span style="font-weight: bold;">{{ $totalPedidos }}</span>
        </div>

        <div class="divider"></div>

        <div style="font-weight: bold; font-size: 11px; margin-bottom: 3px;">DESGLOSE POR MÉTODO DE PAGO</div>
        <table class="table-data">
            <thead>
                <tr>
                    <th>MÉTODO</th>
                    <th class="text-center">OP</th>
                    <th class="text-right">TOTAL USD</th>
                </tr>
            </thead>
            <tbody>
                @forelse($desgloseMetodos as $d)
                <tr>
                    <td>{{ $d['metodo'] }}</td>
                    <td class="text-center">{{ $d['cantidad'] }}</td>
                    <td class="text-right" style="font-weight: bold;">${{ number_format($d['total_usd'], 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center" style="font-style: italic; padding: 4px 0;">Sin ventas en el turno</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="divider"></div>

        <div class="info-row">
            <span>Fondo Inicial Base:</span>
            <span>${{ number_format($sesion->monto_inicial_usd, 2) }}</span>
        </div>
        <div class="info-row">
            <span>Ventas en Efectivo USD:</span>
            <span>${{ number_format($totalEfectivoUsd, 2) }}</span>
        </div>
        <div class="info-row" style="font-weight: bold;">
            <span>TOTAL VENTAS SISTEMA:</span>
            <span>${{ number_format($sesion->total_ventas_sistema_usd, 2) }}</span>
        </div>
        <div class="info-row" style="font-size: 10px; color: #333;">
            <span>Equivalente en Bs:</span>
            <span>Bs. {{ number_format($sesion->total_ventas_sistema_ves, 2) }}</span>
        </div>

        <div class="divider"></div>

        <!-- Arqueo de Gaveta Física -->
        <div class="highlight-box">
            <div class="info-row">
                <span class="info-label">Efectivo Físico Esperado:</span>
                <span style="font-weight: bold;">${{ number_format($sesion->monto_inicial_usd + $totalEfectivoUsd, 2) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Efectivo Físico Contado:</span>
                <span style="font-weight: bold;">${{ number_format($sesion->dinero_real_en_caja_usd, 2) }}</span>
            </div>
            <div class="divider" style="margin: 4px 0;"></div>
            <div class="info-row" style="font-size: 11.5px; font-weight: bold;">
                <span>DIFERENCIA (ARQUEO):</span>
                <span>
                    @if(abs($sesion->diferencia_usd) < 0.01)
                        $0.00 (CUADRADA)
                    @elseif($sesion->diferencia_usd > 0)
                        +${{ number_format($sesion->diferencia_usd, 2) }} (SOBRANTE)
                    @else
                        -${{ number_format(abs($sesion->diferencia_usd), 2) }} (FALTANTE)
                    @endif
                </span>
            </div>
        </div>

        @if($sesion->observaciones_cierre)
        <div style="font-size: 10px; margin-top: 5px;">
            <strong>Observaciones:</strong> {{ $sesion->observaciones_cierre }}
        </div>
        @endif

        <div class="signatures">
            <div class="sign-col">
                <div class="sign-line"></div>
                <div>Firma Cajero</div>
            </div>
            <div class="sign-col">
                <div class="sign-line"></div>
                <div>Firma Supervisor</div>
            </div>
        </div>

        <div class="divider" style="margin-top: 15px;"></div>
        <div style="text-align: center; font-size: 8.5px; color: #555;">
            Sistema ERP Agrosuministros Rodríguez<br>
            Comprobante Oficial de Cuadre de Caja
        </div>
    </div>

</body>
</html>
