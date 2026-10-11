<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización {{ $cotizacion->numero_cotizacion }}</title>
    <style>
        @page {
            margin: 12mm 15mm;
            size: A4 portrait;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10.5px;
            color: #0f172a;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 12px;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            color: #0b1d38;
            text-transform: uppercase;
        }

        .company-meta {
            font-size: 9.5px;
            color: #334155;
            line-height: 1.3;
        }

        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            color: #0b1d38;
            text-align: right;
            text-transform: uppercase;
        }

        .invoice-number {
            font-size: 13px;
            font-weight: bold;
            color: #020617;
            text-align: right;
            font-family: monospace;
            margin-bottom: 4px;
        }

        .meta-right {
            font-size: 9.5px;
            text-align: right;
            color: #1e293b;
        }

        .section-title {
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 3px;
            margin-bottom: 5px;
        }

        .client-table {
            margin-bottom: 14px;
        }

        .client-table td {
            vertical-align: top;
            font-size: 9.5px;
            padding: 2px 4px;
        }

        .label {
            font-weight: bold;
            color: #334155;
            width: 40%;
        }

        .value {
            color: #020617;
            font-weight: bold;
            text-align: right;
        }

        .items-table {
            margin-top: 6px;
            margin-bottom: 16px;
        }

        .items-table th {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: #020617;
            background-color: #f1f5f9;
            padding: 6px 4px;
            border-bottom: 1.5px solid #0f172a;
            border-top: 1px solid #cbd5e1;
        }

        .items-table td {
            font-size: 9.5px;
            padding: 7px 4px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .summary-table {
            margin-top: 8px;
            border-top: 1.5px solid #cbd5e1;
            border-bottom: 1.5px solid #cbd5e1;
            padding: 10px 0;
        }

        .totals-table td {
            font-size: 10px;
            padding: 2.5px 0;
        }

        .total-highlight {
            font-size: 12.5px !important;
            font-weight: bold;
            color: #0b1d38;
            border-top: 1.5px solid #0f172a;
            padding-top: 5px !important;
        }

        .ves-highlight {
            font-size: 10.5px !important;
            font-weight: bold;
            color: #1e549e;
        }

        .notes-box {
            font-size: 8.5px;
            color: #334155;
            line-height: 1.35;
            padding-right: 14px;
        }

        .signatures-table {
            margin-top: 35px;
            width: 100%;
        }

        .signatures-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 25px;
        }

        .sign-line {
            border-bottom: 1px solid #475569;
            margin-bottom: 5px;
        }

        .sign-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
        }

        .sign-sub {
            font-size: 8px;
            color: #475569;
        }
    </style>
</head>
<body>

    <!-- 1. HEADER -->
    <table class="header-table">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <div class="company-name">{{ $config->nombre_comercio ?? $config->nombre_empresa ?? 'Agrosuministros Rodríguez C.A.' }}</div>
                <div class="company-meta" style="margin-top: 2px; font-weight: bold;">RIF: {{ $config->rif ?? 'J-50493821-4' }}</div>
                <div class="company-meta">{{ $config->direccion_fisica ?? 'Calle Principal, Sector Centro, Barinitas, Estado Barinas, Venezuela' }}</div>
                <div class="company-meta">Tel: {{ $config->telefono_contacto ?? '+58 412-9614615' }} • {{ $config->email_contacto ?? 'agrosuministrosrodriguez@gmail.com' }}</div>
            </td>
            <td style="width: 40%; vertical-align: top; text-align: right;">
                <div class="invoice-title">COTIZACIÓN</div>
                <div class="invoice-number">{{ $cotizacion->numero_cotizacion }}</div>
                <div class="meta-right">Emisión: <strong>{{ \Carbon\Carbon::parse($cotizacion->fecha_emision)->format('d/m/Y') }}</strong></div>
                <div class="meta-right">Validez: <strong>{{ $cotizacion->validez_dias }} días</strong> (Vence: {{ $cotizacion->fecha_vencimiento ? \Carbon\Carbon::parse($cotizacion->fecha_vencimiento)->format('d/m/Y') : 'N/A' }})</div>
                <div class="meta-right">Estado: <strong>{{ strtoupper($cotizacion->estado) }}</strong></div>
            </td>
        </tr>
    </table>

    <!-- 2. DATOS CLIENTE Y CONDICIONES -->
    <table class="client-table">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 12px;">
                <div class="section-title">Datos del Cliente</div>
                <table>
                    <tr>
                        <td class="label">Cliente:</td>
                        <td class="value">{{ $cotizacion->cliente_nombre }}</td>
                    </tr>
                    @if($cotizacion->cliente_documento)
                    <tr>
                        <td class="label">Cédula / RIF:</td>
                        <td class="value">{{ $cotizacion->cliente_documento }}</td>
                    </tr>
                    @endif
                    @if($cotizacion->cliente_telefono)
                    <tr>
                        <td class="label">Teléfono:</td>
                        <td class="value">{{ $cotizacion->cliente_telefono }}</td>
                    </tr>
                    @endif
                    @if($cotizacion->cliente_email)
                    <tr>
                        <td class="label">Correo:</td>
                        <td class="value">{{ $cotizacion->cliente_email }}</td>
                    </tr>
                    @endif
                </table>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 12px;">
                <div class="section-title">Condiciones Comerciales</div>
                <table>
                    <tr>
                        <td class="label">Asesor Comercial:</td>
                        <td class="value">{{ $cotizacion->vendedor ? $cotizacion->vendedor->nombre . ' ' . ($cotizacion->vendedor->apellido ?? '') : 'Ventas Central' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Moneda:</td>
                        <td class="value">Dólares (USD)</td>
                    </tr>
                    <tr>
                        <td class="label">Tasa BCV Aplicada:</td>
                        <td class="value">Bs. {{ number_format($cotizacion->valor_tasa_bcv, 4) }}</td>
                    </tr>
                    @if($cotizacion->cliente_direccion)
                    <tr>
                        <td class="label">Entrega:</td>
                        <td class="value">{{ $cotizacion->cliente_direccion }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <!-- 3. TABLA DE PRODUCTOS -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">#</th>
                <th style="width: 55%; text-align: left;">Descripción del Producto / Servicio</th>
                <th style="width: 12%;" class="text-center">Cant.</th>
                <th style="width: 14%;" class="text-right">P. Unit ($)</th>
                <th style="width: 14%;" class="text-right">Total ($)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cotizacion->detalles as $idx => $item)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>
                    <div style="font-weight: bold; color: #020617;">{{ $item->descripcion }}</div>
                    @if($item->producto && $item->producto->categoria)
                        <div style="font-size: 8px; color: #64748b;">{{ $item->producto->categoria->nombre }}</div>
                    @endif
                </td>
                <td class="text-center">
                    {{ (float)$item->cantidad }} {{ $item->unidad_medida ?: 'ud' }}
                </td>
                <td class="text-right">
                    ${{ number_format($item->precio_unitario_usd, 2) }}
                </td>
                <td class="text-right" style="font-weight: bold;">
                    ${{ number_format($item->total_usd, 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- 4. RESUMEN Y OBSERVACIONES -->
    <table class="summary-table">
        <tr>
            <td style="width: 55%; vertical-align: top;" class="notes-box">
                <div style="font-weight: bold; text-transform: uppercase; margin-bottom: 3px; color: #020617;">Condiciones y Observaciones:</div>
                <div>{{ $cotizacion->observaciones ?: '• Precios expresados en Dólares Americanos (USD), pagaderos en Bolívares (VES) a la tasa oficial del Banco Central de Venezuela (BCV) del día de pago.' }}</div>
                <div style="margin-top: 4px;">• Validez de la oferta: {{ $cotizacion->validez_dias }} días continuos desde la fecha de emisión.</div>
                <div style="margin-top: 4px;">• Para confirmar disponibilidad o concretar compra: WhatsApp {{ $config->telefono_contacto ?? '+58 412-9614615' }}.</div>
            </td>
            <td style="width: 45%; vertical-align: top;">
                <table class="totals-table">
                    <tr>
                        <td style="font-weight: bold; color: #334155;">Subtotal Neto:</td>
                        <td class="text-right" style="font-weight: bold;">${{ number_format($cotizacion->subtotal_usd, 2) }} USD</td>
                    </tr>
                    @if($cotizacion->iva_usd > 0)
                    <tr>
                        <td style="font-weight: bold; color: #334155;">IVA ({{ (float)$cotizacion->iva_porcentaje }}%):</td>
                        <td class="text-right" style="font-weight: bold;">${{ number_format($cotizacion->iva_usd, 2) }} USD</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="total-highlight">TOTAL COTIZADO:</td>
                        <td class="text-right total-highlight">${{ number_format($cotizacion->total_usd, 2) }} USD</td>
                    </tr>
                    <tr>
                        <td class="ves-highlight">Equivalente BCV:</td>
                        <td class="text-right ves-highlight">Bs. {{ number_format($cotizacion->total_ves, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 5. FIRMAS -->
    <table class="signatures-table">
        <tr>
            <td>
                <div class="sign-line"></div>
                <div class="sign-title">{{ $config->nombre_comercio ?? 'Agrosuministros Rodríguez C.A.' }}</div>
                <div class="sign-sub">Firma Autorizada / Sello</div>
            </td>
            <td>
                <div class="sign-line"></div>
                <div class="sign-title">{{ $cotizacion->cliente_nombre }}</div>
                <div class="sign-sub">Aceptación y Conformidad</div>
            </td>
        </tr>
    </table>

</body>
</html>
