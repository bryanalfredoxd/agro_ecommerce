<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización {{ $cotizacion->numero_cotizacion }} - Agrosuministros Rodríguez</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            font-size: 11.5px;
            line-height: 1.4;
            color: #0f172a;
            background-color: #f1f5f9;
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Barra de acciones en pantalla */
        .actions-bar {
            width: 210mm;
            max-width: 100%;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-group {
            display: flex;
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-print {
            background-color: #0b1d38;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(11, 29, 56, 0.2);
        }

        .btn-print:hover {
            background-color: #162a4a;
        }

        .btn-pdf {
            background-color: #0284c7;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);
        }

        .btn-pdf:hover {
            background-color: #0369a1;
        }

        .btn-email {
            background-color: #10b981;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .btn-email:hover {
            background-color: #059669;
        }

        .btn-back {
            background-color: #e2e8f0;
            color: #1e293b;
            font-weight: 700;
        }

        .btn-back:hover {
            background-color: #cbd5e1;
        }

        /* Hoja A4 Limpia y Minimalista */
        .invoice-sheet {
            width: 210mm;
            min-height: 297mm;
            background: #ffffff;
            padding: 16mm 18mm;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border-radius: 4px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Header: Logo e información corporativa */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 16px;
            border-bottom: 2px solid #0f172a;
        }

        .company-block {
            display: flex;
            align-items: center;
            gap: 16px;
            max-width: 60%;
        }

        .company-logo {
            width: 85px;
            height: auto;
            max-height: 75px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .company-details h1 {
            font-size: 15px;
            font-weight: 900;
            color: #0b1d38;
            line-height: 1.25;
            text-transform: uppercase;
            letter-spacing: -0.2px;
        }

        .company-rif {
            font-size: 11px;
            font-weight: 800;
            color: #0f172a;
            margin: 2px 0 3px 0;
        }

        .company-details p {
            font-size: 10.5px;
            color: #334155;
            line-height: 1.35;
            font-weight: 500;
        }

        .invoice-meta {
            text-align: right;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            gap: 3px;
        }

        .invoice-meta .title {
            font-size: 18px;
            font-weight: 900;
            color: #0b1d38;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .invoice-meta .invoice-number {
            font-size: 14px;
            font-weight: 800;
            color: #020617;
            font-family: monospace;
            margin-bottom: 4px;
        }

        .invoice-meta .meta-item {
            font-size: 10.5px;
            color: #1e293b;
            font-weight: 600;
        }

        .invoice-meta .meta-item strong {
            color: #020617;
            font-weight: 800;
        }

        .status-pill {
            display: inline-block;
            align-self: flex-end;
            margin-top: 4px;
            padding: 2px 8px;
            font-size: 9.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 4px;
            border: 1px solid #94a3b8;
            color: #0f172a;
            background-color: #f1f5f9;
        }

        /* 2. Desglose de información */
        .client-section {
            padding: 14px 0;
            border-bottom: 1.5px solid #cbd5e1;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 24px;
        }

        .info-col {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .info-title {
            font-size: 10.5px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
            margin-bottom: 3px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 10.5px;
            line-height: 1.4;
        }

        .info-label {
            color: #334155;
            font-weight: 700;
        }

        .info-value {
            color: #020617;
            font-weight: 800;
            text-align: right;
        }

        /* 3. Tabla de Productos */
        .items-section {
            padding: 16px 0 20px 0;
            flex-grow: 1;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table th {
            text-align: left;
            font-size: 9.5px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #020617;
            padding: 8px 6px;
            border-bottom: 2px solid #0f172a;
        }

        .items-table th.text-center { text-align: center; }
        .items-table th.text-right { text-align: right; }

        .items-table td {
            padding: 9px 6px;
            font-size: 10.5px;
            border-bottom: 1px solid #cbd5e1;
            color: #0f172a;
            vertical-align: top;
        }

        .items-table td.text-center { text-align: center; }
        .items-table td.text-right { text-align: right; }

        .item-name {
            font-weight: 800;
            color: #020617;
        }

        /* 4. Resumen y Cuentas Bancarias */
        .summary-section {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 24px;
            padding: 14px 0 16px 0;
            border-top: 1.5px solid #cbd5e1;
            border-bottom: 1.5px solid #cbd5e1;
        }

        .notes-box {
            font-size: 10px;
            color: #1e293b;
            line-height: 1.45;
        }

        .notes-title {
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #020617;
            margin-bottom: 4px;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 3px 0;
            font-size: 11px;
            color: #1e293b;
        }

        .totals-table td.label {
            font-weight: 700;
        }

        .totals-table td.value {
            text-align: right;
            font-weight: 800;
            color: #020617;
        }

        .totals-table tr.total-row td {
            padding-top: 6px;
            padding-bottom: 2px;
            border-top: 1.5px solid #0f172a;
            font-size: 13.5px;
            font-weight: 900;
            color: #0b1d38;
        }

        .totals-table tr.ves-row td {
            font-size: 11px;
            font-weight: 800;
            color: #1e549e;
        }

        /* 5. Footer con firmas y términos */
        .invoice-footer {
            padding-top: 14px;
        }

        .footer-terms {
            font-size: 9px;
            color: #334155;
            line-height: 1.35;
            margin-bottom: 20px;
        }

        .signatures-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 16px;
            padding-top: 10px;
        }

        .sign-box {
            text-align: center;
        }

        .sign-line {
            width: 75%;
            margin: 0 auto 6px auto;
            border-bottom: 1px solid #475569;
        }

        .sign-title {
            font-size: 9.5px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sign-subtitle {
            font-size: 8.5px;
            color: #475569;
            margin-top: 1px;
        }

        /* Reglas de Impresión */
        @media print {
            body {
                background: none;
                padding: 0;
            }

            .actions-bar {
                display: none !important;
            }

            .invoice-sheet {
                box-shadow: none;
                border-radius: 0;
                padding: 0;
                width: 100%;
                min-height: auto;
            }

            @page {
                size: A4 portrait;
                margin: 12mm 15mm;
            }
        }
    </style>
</head>
<body>

    <!-- BARRA DE ACCIONES EN PANTALLA -->
    <div class="actions-bar">
        <a href="{{ route('admin.cotizaciones.index') }}" class="btn btn-back">
            ← Volver a Cotizaciones
        </a>
        <div class="btn-group">
            <button onclick="window.print()" class="btn btn-print">
                🖨️ Imprimir / Guardar PDF
            </button>
            <a href="{{ route('admin.cotizaciones.pdf', $cotizacion->id) }}" class="btn btn-pdf">
                📥 Descargar PDF
            </a>
            @if($cotizacion->cliente_email)
            <form action="{{ route('admin.cotizaciones.enviar_correo', $cotizacion->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('¿Desea enviar esta cotización al correo {{ $cotizacion->cliente_email }}?');">
                @csrf
                <button type="submit" class="btn btn-email">
                    ✉️ Enviar por Correo
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- HOJA FORMATO A4 DE COTIZACIÓN -->
    <div class="invoice-sheet">
        
        <div>
            <!-- 1. HEADER CORPORATIVO -->
            <div class="invoice-header">
                <div class="company-block">
                    <img src="{{ asset('favicon.ico') }}" alt="Logo" class="company-logo">
                    <div class="company-details">
                        <h1>{{ $config->nombre_comercio ?? $config->nombre_empresa ?? 'Agrosuministros Rodríguez C.A.' }}</h1>
                        <div class="company-rif">RIF: {{ $config->rif ?? 'J-50493821-4' }}</div>
                        <p>{{ $config->direccion_fisica ?? 'Calle Principal, Sector Centro, Barinitas, Estado Barinas, Venezuela' }}</p>
                        <p>Tel: {{ $config->telefono_contacto ?? '+58 412-9614615' }} • {{ $config->email_contacto ?? 'agrosuministrosrodriguez@gmail.com' }}</p>
                    </div>
                </div>

                <div class="invoice-meta">
                    <div class="title">COTIZACIÓN</div>
                    <div class="invoice-number">{{ $cotizacion->numero_cotizacion }}</div>
                    <div class="meta-item">Fecha de Emisión: <strong>{{ \Carbon\Carbon::parse($cotizacion->fecha_emision)->format('d/m/Y') }}</strong></div>
                    <div class="meta-item">Validez Oferta: <strong>{{ $cotizacion->validez_dias }} días</strong></div>
                    <div class="meta-item">Vence: <strong>{{ $cotizacion->fecha_vencimiento ? \Carbon\Carbon::parse($cotizacion->fecha_vencimiento)->format('d/m/Y') : 'N/A' }}</strong></div>
                    
                    @php $badge = $cotizacion->estado_badge; @endphp
                    <span class="status-pill">{{ $badge['label'] }}</span>
                </div>
            </div>

            <!-- 2. DESGLOSE CLIENTE Y CONDICIONES -->
            <div class="client-section">
                <div class="info-col">
                    <div class="info-title">Datos del Cliente</div>
                    <div class="info-row">
                        <span class="info-label">Nombre / Razón Social:</span>
                        <span class="info-value">{{ $cotizacion->cliente_nombre }}</span>
                    </div>
                    @if($cotizacion->cliente_documento)
                    <div class="info-row">
                        <span class="info-label">Cédula / RIF:</span>
                        <span class="info-value">{{ $cotizacion->cliente_documento }}</span>
                    </div>
                    @endif
                    @if($cotizacion->cliente_telefono)
                    <div class="info-row">
                        <span class="info-label">Teléfono:</span>
                        <span class="info-value">{{ $cotizacion->cliente_telefono }}</span>
                    </div>
                    @endif
                    @if($cotizacion->cliente_email)
                    <div class="info-row">
                        <span class="info-label">Correo Electrónico:</span>
                        <span class="info-value">{{ $cotizacion->cliente_email }}</span>
                    </div>
                    @endif
                </div>

                <div class="info-col">
                    <div class="info-title">Condiciones Comerciales</div>
                    <div class="info-row">
                        <span class="info-label">Asesor Comercial:</span>
                        <span class="info-value">{{ $cotizacion->vendedor ? $cotizacion->vendedor->nombre . ' ' . ($cotizacion->vendedor->apellido ?? '') : 'Ventas Central' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Moneda de Presupuesto:</span>
                        <span class="info-value">Dólares Estadounidenses (USD)</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tasa Oficial BCV:</span>
                        <span class="info-value">Bs. {{ number_format($cotizacion->valor_tasa_bcv, 4) }} / USD</span>
                    </div>
                    @if($cotizacion->cliente_direccion)
                    <div class="info-row">
                        <span class="info-label">Destino / Entrega:</span>
                        <span class="info-value" style="max-width: 180px; word-break: break-word;">{{ $cotizacion->cliente_direccion }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- 3. TABLA DE PRODUCTOS -->
            <div class="items-section">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 6%;">#</th>
                            <th style="width: 50%;">Descripción del Producto / Servicio</th>
                            <th style="width: 14%;" class="text-center">Cantidad</th>
                            <th style="width: 15%;" class="text-right">Precio Unit. ($)</th>
                            <th style="width: 15%;" class="text-right">Total ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cotizacion->detalles as $idx => $item)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>
                                <div class="item-name">{{ $item->descripcion }}</div>
                                @if($item->producto && $item->producto->categoria)
                                    <div style="font-size: 9px; color: #475569;">{{ $item->producto->categoria->nombre }}</div>
                                @endif
                            </td>
                            <td class="text-center">
                                {{ (float)$item->cantidad }} {{ $item->unidad_medida ?: 'ud' }}
                            </td>
                            <td class="text-right">
                                ${{ number_format($item->precio_unitario_usd, 2) }}
                            </td>
                            <td class="text-right" style="font-weight: 800;">
                                ${{ number_format($item->total_usd, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <!-- 4. RESUMEN Y CUENTAS -->
            <div class="summary-section">
                <div class="notes-box">
                    <div class="notes-title">Observaciones y Condiciones</div>
                    <p style="margin-bottom: 6px;">
                        {{ $cotizacion->observaciones ?: '• Los precios cotizados están expresados en Dólares Americanos (USD) y se liquidan en Bolívares (VES) a la tasa oficial BCV vigente el día de su pago.' }}
                    </p>
                    <p style="margin-bottom: 6px;">
                        • Validez de la oferta: {{ $cotizacion->validez_dias }} días continuos a partir de la fecha de emisión.
                    </p>
                    <p>
                        • Para confirmar su pedido, comuníquese a nuestro WhatsApp al {{ $config->telefono_contacto ?? '+58 412-9614615' }}.
                    </p>
                </div>

                <div>
                    <table class="totals-table">
                        <tr>
                            <td class="label">Subtotal Neto:</td>
                            <td class="value">${{ number_format($cotizacion->subtotal_usd, 2) }} USD</td>
                        </tr>
                        @if($cotizacion->iva_usd > 0)
                        <tr>
                            <td class="label">IVA ({{ (float)$cotizacion->iva_porcentaje }}%):</td>
                            <td class="value">${{ number_format($cotizacion->iva_usd, 2) }} USD</td>
                        </tr>
                        @endif
                        <tr class="total-row">
                            <td class="label">TOTAL COTIZADO:</td>
                            <td class="value">${{ number_format($cotizacion->total_usd, 2) }} USD</td>
                        </tr>
                        <tr class="ves-row">
                            <td class="label">Total en Bolívares (BCV):</td>
                            <td class="value">Bs. {{ number_format($cotizacion->total_ves, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- 5. FIRMAS Y TÉRMINOS -->
            <div class="invoice-footer">
                <div class="signatures-grid">
                    <div class="sign-box">
                        <div class="sign-line"></div>
                        <div class="sign-title">{{ $config->nombre_comercio ?? 'Agrosuministros Rodríguez C.A.' }}</div>
                        <div class="sign-subtitle">Firma Autorizada / Sello Comercial</div>
                    </div>
                    <div class="sign-box">
                        <div class="sign-line"></div>
                        <div class="sign-title">{{ $cotizacion->cliente_nombre }}</div>
                        <div class="sign-subtitle">Aceptación del Cliente</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
