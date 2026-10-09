<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura {{ $factura->numero_factura }} - Agrosuministros Rodríguez</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            font-size: 12.5px;
            line-height: 1.45;
            color: #1e293b;
            background-color: #f1f5f9;
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Barra de acciones superior */
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
            border-radius: 10px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
        }
        .btn-print {
            background-color: #1e549e;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(30, 84, 158, 0.25);
        }
        .btn-print:hover {
            background-color: #164079;
        }
        .btn-ticket {
            background-color: #0b1d38;
            color: #ffffff;
        }
        .btn-ticket:hover {
            background-color: #162a4a;
        }
        .btn-back {
            background-color: #e2e8f0;
            color: #334155;
        }
        .btn-back:hover {
            background-color: #cbd5e1;
        }

        /* Hoja A4 Simétrica */
        .invoice-sheet {
            width: 210mm;
            min-height: 297mm;
            background: #ffffff;
            padding: 20mm;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border-radius: 6px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Sello de anulación de agua */
        .watermark-anulada {
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            border: 6px solid rgba(220, 38, 38, 0.55);
            color: rgba(220, 38, 38, 0.55);
            font-size: 64px;
            font-weight: 900;
            letter-spacing: 12px;
            padding: 12px 40px;
            border-radius: 16px;
            pointer-events: none;
            z-index: 50;
            text-transform: uppercase;
        }

        /* Encabezado Corporativo Simétrico */
        .invoice-header-grid {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 20px;
            align-items: stretch;
            padding-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
        }
        .company-identity {
            display: flex;
            gap: 16px;
            align-items: center;
        }
        .company-logo {
            width: 105px;
            height: auto;
            object-fit: contain;
            flex-shrink: 0;
        }
        .company-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .company-title {
            font-size: 16px;
            font-weight: 900;
            color: #0b1d38;
            letter-spacing: -0.3px;
            line-height: 1.25;
            margin-bottom: 3px;
        }
        .company-rif {
            font-size: 12px;
            font-weight: 800;
            color: #1e549e;
            margin-bottom: 4px;
        }
        .company-text {
            font-size: 11px;
            color: #475569;
            line-height: 1.4;
        }

        /* Tarjeta de Control Fiscal (Lado Derecho Encabezado) */
        .fiscal-control-card {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            overflow: hidden;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .fiscal-card-banner {
            background-color: #0b1d38;
            color: #ffffff;
            text-align: center;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
            padding: 6px 10px;
            text-transform: uppercase;
        }
        .fiscal-card-body {
            padding: 10px 14px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .fiscal-num-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            border-bottom: 1px dashed #e2e8f0;
            padding-bottom: 6px;
        }
        .fiscal-num-label {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
        }
        .fiscal-num-value {
            font-size: 17px;
            font-weight: 900;
            color: #1e549e;
            letter-spacing: 0.5px;
        }
        .fiscal-meta-subgrid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 12px;
            font-size: 11px;
        }
        .fiscal-meta-item .sub-label {
            font-size: 9.5px;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
        }
        .fiscal-meta-item .sub-value {
            font-weight: 800;
            color: #0f172a;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            background-color: #e0f2fe;
            color: #0284c7;
        }
        .status-badge.anulada {
            background-color: #fee2e2;
            color: #dc2626;
        }

        /* Rejilla de Información Cliente / Operación (50% / 50% Simétrica) */
        .symmetric-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 16px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }
        .info-card-header {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            color: #1e549e;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 5px;
            font-size: 11.5px;
            line-height: 1.35;
        }
        .info-row:last-child {
            margin-bottom: 0;
        }
        .info-label {
            width: 105px;
            font-weight: 600;
            color: #64748b;
            flex-shrink: 0;
        }
        .info-value {
            font-weight: 700;
            color: #0f172a;
            flex: 1;
            text-align: right;
            word-break: break-word;
        }

        /* Tabla de Productos Simétrica */
        .items-section {
            margin-bottom: 20px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
        }
        .items-table thead th {
            background-color: #0b1d38;
            color: #ffffff;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            text-align: left;
        }
        .items-table thead th:first-child {
            border-top-left-radius: 6px;
            border-bottom-left-radius: 6px;
        }
        .items-table thead th:last-child {
            border-top-right-radius: 6px;
            border-bottom-right-radius: 6px;
        }
        .items-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }
        .items-table tbody tr:nth-child(even) {
            background-color: #fafbfc;
        }
        .items-table tbody td {
            padding: 8px 10px;
            vertical-align: middle;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Resumen y Liquidación Simétricos (50% / 50%) */
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            align-items: stretch;
            margin-top: 10px;
        }
        .payment-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 16px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .payment-box-title {
            font-size: 11px;
            font-weight: 800;
            color: #1e549e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
        }
        .legal-clause-box {
            margin-top: 10px;
            padding: 8px 10px;
            background: #ffffff;
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            font-size: 10px;
            color: #475569;
            line-height: 1.4;
        }

        .totals-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 16px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .total-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 0;
            font-size: 11.5px;
            border-bottom: 1px solid #f1f5f9;
        }
        .total-line .t-label {
            color: #64748b;
            font-weight: 600;
        }
        .total-line .t-val {
            font-weight: 700;
            color: #0f172a;
        }

        .total-highlight-usd {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            margin-top: 6px;
            background-color: #0b1d38;
            color: #ffffff;
            border-radius: 6px;
            font-weight: 900;
        }
        .total-highlight-usd .t-usd-title {
            font-size: 11.5px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .total-highlight-usd .t-usd-amount {
            font-size: 16px;
            letter-spacing: 0.5px;
        }

        .total-highlight-ves {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 7px 12px;
            margin-top: 6px;
            background-color: #e5f2fb;
            color: #1e549e;
            border: 1px solid #bae6fd;
            border-radius: 6px;
            font-weight: 900;
        }
        .total-highlight-ves .t-ves-title {
            font-size: 11px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .total-highlight-ves .t-ves-amount {
            font-size: 14px;
        }

        /* Pie de página Simétrico */
        .invoice-footer-grid {
            margin-top: 30px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            align-items: flex-end;
        }
        .warranty-box {
            font-size: 10px;
            color: #64748b;
            line-height: 1.45;
        }
        .warranty-box strong {
            color: #334155;
            display: block;
            margin-bottom: 2px;
            text-transform: uppercase;
            font-size: 10.5px;
        }
        
        .signatures-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            text-align: center;
        }
        .sign-card {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .sign-line {
            width: 100%;
            border-bottom: 1px dashed #64748b;
            height: 38px;
            margin-bottom: 6px;
        }
        .sign-caption {
            font-size: 9.5px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .bottom-audit-bar {
            margin-top: 14px;
            padding-top: 8px;
            border-top: 1px solid #f1f5f9;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            letter-spacing: 0.3px;
        }

        /* Modos de Impresión */
        @media print {
            body {
                background: none;
                padding: 0;
                margin: 0;
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
            .info-card, .payment-box, .totals-box, .fiscal-control-card {
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <!-- Barra de acciones en pantalla -->
    <div class="actions-bar">
        <div class="btn-group">
            <button class="btn btn-print" onclick="window.print()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                Imprimir Factura (A4)
            </button>
            <a href="{{ route('factura.ticket', $factura->id) }}" class="btn btn-ticket">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"></path><line x1="8" y1="8" x2="16" y2="8"></line><line x1="8" y1="12" x2="16" y2="12"></line><line x1="8" y1="16" x2="12" y2="16"></line></svg>
                Formato Ticket (80mm)
            </a>
        </div>
        <button class="btn btn-back" onclick="window.history.back()">
            ✕ Cerrar / Volver
        </button>
    </div>

    <!-- Hoja A4 Symmetrical Document -->
    <div class="invoice-sheet">

        @if($factura->estado === 'anulada')
            <div class="watermark-anulada">ANULADA</div>
        @endif

        <div>
            <!-- 1. Encabezado Corporativo y Control Fiscal (Simétrico) -->
            <div class="invoice-header-grid">
                <!-- Columna Izquierda: Identidad de Empresa -->
                <div class="company-identity">
                    <img src="{{ asset('img/logo.png') }}" alt="Agrosuministros Rodríguez" class="company-logo">
                    <div class="company-info">
                        <h1 class="company-title">Agrosuministros Rodríguez C.A.</h1>
                        <div class="company-rif">RIF: J-50493821-4</div>
                        <div class="company-text">Calle Principal, Sector Centro, Barinitas, Edo. Barinas</div>
                        <div class="company-text">Teléfono / WhatsApp: +58 412-9614615</div>
                        <div class="company-text">Email: agrosuministrosrodriguez@gmail.com</div>
                    </div>
                </div>

                <!-- Columna Derecha: Tarjeta de Control Fiscal -->
                <div class="fiscal-control-card">
                    <div class="fiscal-card-banner">FACTURA FISCAL DIGITAL</div>
                    <div class="fiscal-card-body">
                        <div class="fiscal-num-row">
                            <span class="fiscal-num-label">FACTURA N°:</span>
                            <span class="fiscal-num-value">{{ $factura->numero_factura }}</span>
                        </div>
                        <div class="fiscal-meta-subgrid">
                            <div class="fiscal-meta-item">
                                <div class="sub-label">Control Interno</div>
                                <div class="sub-value">FAC-{{ str_pad($factura->id, 8, '0', STR_PAD_LEFT) }}</div>
                            </div>
                            <div class="fiscal-meta-item">
                                <div class="sub-label">Estado</div>
                                <div>
                                    <span class="status-badge {{ $factura->estado === 'anulada' ? 'anulada' : '' }}">
                                        {{ strtoupper($factura->estado) }}
                                    </span>
                                </div>
                            </div>
                            <div class="fiscal-meta-item">
                                <div class="sub-label">Emisión</div>
                                <div class="sub-value">{{ \Carbon\Carbon::parse($factura->fecha_emision)->format('d/m/Y h:i A') }}</div>
                            </div>
                            <div class="fiscal-meta-item">
                                <div class="sub-label">Canal</div>
                                <div class="sub-value">{{ strtoupper(in_array($factura->pedido->canal_venta ?? '', ['pos', 'tienda_fisica']) ? 'Mostrador POS' : 'Tienda Online') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Información del Adquiriente y de la Operación (Simétrico 50% / 50%) -->
            <div class="symmetric-grid">
                <!-- Columna Izquierda: Datos del Cliente -->
                <div class="info-card">
                    <div class="info-card-header">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        Datos del Adquiriente / Cliente
                    </div>
                    <div class="info-row">
                        <span class="info-label">Razón Social:</span>
                        <span class="info-value">{{ $factura->nombre_razon_social ?: 'Consumidor Final' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Cédula / RIF:</span>
                        <span class="info-value">{{ $factura->cedula_rif_cliente ?: 'V-00000000' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Teléfono:</span>
                        <span class="info-value">{{ $factura->telefono_cliente ?: 'No indicado' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Dirección:</span>
                        <span class="info-value">{{ $factura->direccion_cliente ?: 'Barinitas, Estado Barinas' }}</span>
                    </div>
                </div>

                <!-- Columna Derecha: Información de la Operación -->
                <div class="info-card">
                    <div class="info-card-header">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        Detalles de la Operación
                    </div>
                    <div class="info-row">
                        <span class="info-label">Pedido N°:</span>
                        <span class="info-value">#{{ $factura->pedido_id }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Condición:</span>
                        <span class="info-value">Venta de Contado</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tasa Oficial BCV:</span>
                        <span class="info-value">Bs. {{ number_format($factura->valor_tasa_bcv, 2) }} / USD</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Moneda Base:</span>
                        <span class="info-value">USD (Dólares) / VES (Bolívares)</span>
                    </div>
                </div>
            </div>

            <!-- 3. Tabla de Artículos e Insumos -->
            <div class="items-section">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 6%;">#</th>
                            <th style="width: 48%;">Descripción del Producto / Insumo</th>
                            <th class="text-center" style="width: 10%;">Cant.</th>
                            <th class="text-right" style="width: 18%;">Precio Unit. ($)</th>
                            <th class="text-right" style="width: 18%;">Total ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($factura->pedido && $factura->pedido->detalles && count($factura->pedido->detalles) > 0)
                            @foreach($factura->pedido->detalles as $index => $det)
                            <tr>
                                <td class="text-center" style="color: #64748b;">{{ $index + 1 }}</td>
                                <td>
                                    <div style="font-weight: 700; color: #0f172a;">{{ $det->producto ? $det->producto->nombre : 'Producto' }}</div>
                                    @if($det->producto && $det->producto->categoria)
                                        <div style="font-size: 10px; color: #64748b; margin-top: 1px;">Categoría: {{ $det->producto->categoria->nombre }}</div>
                                    @endif
                                </td>
                                <td class="text-center" style="font-weight: 700;">
                                    {{ (float) $det->cantidad_solicitada }} {{ $det->producto && $det->producto->unidad_medida ? $det->producto->unidad_medida : '' }}
                                </td>
                                <td class="text-right">
                                    ${{ number_format($det->precio_historico_usd, 2) }}
                                    @if($det->producto && $det->producto->unidad_medida)
                                        <span style="font-size: 10px; color: #64748b;">/ {{ $det->producto->unidad_medida }}</span>
                                    @endif
                                </td>
                                <td class="text-right" style="font-weight: 800; color: #0b1d38;">${{ number_format($det->precio_historico_usd * $det->cantidad_solicitada, 2) }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center" style="padding: 16px; color: #64748b; font-style: italic;">
                                    No hay ítems registrados en esta factura.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- 4. Resumen y Liquidación Financiera (Simétrico 50% / 50%) -->
            <div class="summary-grid">
                <!-- Columna Izquierda: Método de Pago y Disposición Legal -->
                <div class="payment-box">
                    <div>
                        <div class="payment-box-title">Método de Liquidación y Pago</div>
                        @if($factura->pedido && $factura->pedido->pago)
                            <div class="info-row">
                                <span class="info-label" style="width: 95px;">Medio de Pago:</span>
                                <span class="info-value">{{ strtoupper(str_replace('_', ' ', $factura->pedido->pago->metodo)) }}</span>
                            </div>
                            @if($factura->pedido->pago->referencia_bancaria)
                            <div class="info-row">
                                <span class="info-label" style="width: 95px;">Referencia:</span>
                                <span class="info-value">{{ $factura->pedido->pago->referencia_bancaria }}</span>
                            </div>
                            @endif
                            <div class="info-row">
                                <span class="info-label" style="width: 95px;">Monto Verificado:</span>
                                <span class="info-value">${{ number_format($factura->pedido->pago->monto_usd, 2) }} (Bs. {{ number_format($factura->pedido->pago->monto_ves, 2) }})</span>
                            </div>
                        @else
                            <div class="info-row">
                                <span class="info-label" style="width: 95px;">Modalidad:</span>
                                <span class="info-value">Contado contra entrega / Caja</span>
                            </div>
                        @endif
                    </div>

                    <div class="legal-clause-box">
                        <em>El monto expresado en Bolívares (VES) se liquida a la tasa oficial del Banco Central de Venezuela (BCV) vigente a la fecha de la transacción, dando estricto cumplimiento al marco tributario nacional.</em>
                    </div>
                </div>

                <!-- Columna Derecha: Totales y Liquidación Fiscal -->
                <div class="totals-box">
                    <div>
                        <div class="total-line">
                            <span class="t-label">Subtotal Neto:</span>
                            <span class="t-val">${{ number_format($factura->subtotal_usd, 2) }}</span>
                        </div>
                        @if($factura->pedido && $factura->pedido->costo_delivery_usd > 0)
                        <div class="total-line">
                            <span class="t-label">Servicio de Delivery / Envío:</span>
                            <span class="t-val">${{ number_format($factura->pedido->costo_delivery_usd, 2) }}</span>
                        </div>
                        @endif
                        <div class="total-line">
                            <span class="t-label">IVA ({{ number_format(($config->iva_porcentaje ?? 16), 0) }}%):</span>
                            <span class="t-val">${{ number_format($factura->impuesto_usd, 2) }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="total-highlight-usd">
                            <span class="t-usd-title">TOTAL A PAGAR (USD)</span>
                            <span class="t-usd-amount">${{ number_format($factura->total_usd, 2) }}</span>
                        </div>
                        <div class="total-highlight-ves">
                            <span class="t-ves-title">TOTAL EQUIVALENTE (VES)</span>
                            <span class="t-ves-amount">Bs. {{ number_format($factura->total_ves, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Pie de Página y Firmas (Simétrico) -->
        <div>
            <div class="invoice-footer-grid">
                <!-- Columna Izquierda: Garantías y Términos -->
                <div class="warranty-box">
                    <strong>Términos y Condiciones de Entrega</strong>
                    <p>Comprobante generado por el sistema ERP de Agrosuministros Rodríguez C.A. Para devoluciones o reclamos de insumos agrícolas o pecuarios, conserve este documento. Plazo máximo de reclamación: 3 días hábiles desde la fecha de emisión.</p>
                </div>

                <!-- Columna Derecha: Bloque de Firmas Gemelas -->
                <div class="signatures-wrapper">
                    <div class="sign-card">
                        <div class="sign-line"></div>
                        <div class="sign-caption">Firma y Sello Emisor</div>
                    </div>
                    <div class="sign-card">
                        <div class="sign-line"></div>
                        <div class="sign-caption">Recibido Conforme</div>
                    </div>
                </div>
            </div>

            <div class="bottom-audit-bar">
                Agrosuministros Rodríguez C.A. • RIF: J-50493821-4 • Barinitas, Edo. Barinas • Sistema de Gestión y Facturación Electrónica ERP
            </div>
        </div>

    </div>

</body>
</html>
