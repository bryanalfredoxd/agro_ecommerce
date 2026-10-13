<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura {{ $factura->numero_factura }} - Agrosuministros Rodríguez</title>
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
            color: #0f172a; /* Color base nítido y de alto contraste */
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

        .btn-ticket {
            background-color: #ffffff;
            color: #0b1d38;
            border: 1px solid #cbd5e1;
        }

        .btn-ticket:hover {
            background-color: #f8fafc;
            border-color: #94a3b8;
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

        /* Sello de anulación */
        .watermark-anulada {
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            border: 5px solid rgba(220, 38, 38, 0.55);
            color: rgba(220, 38, 38, 0.55);
            font-size: 60px;
            font-weight: 900;
            letter-spacing: 10px;
            padding: 10px 36px;
            border-radius: 12px;
            pointer-events: none;
            z-index: 50;
            text-transform: uppercase;
        }

        /* 1. Header: Logo e información corporativa */
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
            width: 90px;
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
            color: #334155; /* Texto corporativo 35% más oscuro */
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
            font-size: 19px;
            font-weight: 900;
            color: #0b1d38;
            letter-spacing: 1.2px;
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
            color: #1e293b; /* Texto oscuro y legible */
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

        .status-pill.anulada {
            border-color: #ef4444;
            color: #991b1b;
            background-color: #fef2f2;
        }

        /* 2. Desglose de información (Sin cuadros pesados ni colores) */
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
            color: #0f172a; /* Título oscuro bien visible */
            margin-bottom: 3px;
        }

        .info-row {
            display: flex;
            align-items: baseline;
            font-size: 11px;
            line-height: 1.4;
        }

        .info-label {
            width: 105px;
            color: #334155; /* Etiquetas oscurecidas */
            font-weight: 700;
            flex-shrink: 0;
        }

        .info-val {
            color: #020617; /* Valores en negro azabache de máxima legibilidad */
            font-weight: 700;
            flex: 1;
        }

        /* 3. Tabla de Productos Limpia y Sobria */
        .items-section {
            padding: 16px 0 10px 0;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .items-table thead th {
            border-top: 2px solid #0f172a;
            border-bottom: 2px solid #0f172a;
            padding: 7px 6px;
            font-weight: 900;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #020617;
            background: transparent;
        }

        .items-table tbody tr {
            border-bottom: 1px solid #cbd5e1;
        }

        .items-table tbody td {
            padding: 7px 6px;
            vertical-align: middle;
            color: #0f172a;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }

        .item-name {
            font-weight: 800;
            color: #020617;
        }

        .item-category {
            font-size: 9.5px;
            color: #334155; /* Categoría en gris oscuro visible */
            font-weight: 600;
        }

        /* 4. Resumen Financiero y Liquidación (Sencillo, sin tarjetas pesadas) */
        .summary-section {
            padding-top: 12px;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 24px;
            align-items: start;
        }

        .payment-notes {
            display: flex;
            flex-direction: column;
            gap: 5px;
            font-size: 10.5px;
            color: #1e293b;
        }

        .payment-notes .notes-title {
            font-size: 10.5px;
            font-weight: 900;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .payment-notes .bcv-text {
            margin-top: 6px;
            font-size: 9.5px;
            color: #334155; /* Nota BCV más oscura y legible */
            line-height: 1.4;
            font-style: italic;
            border-left: 2px solid #64748b;
            padding-left: 8px;
            font-weight: 500;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .totals-table tr td {
            padding: 3.5px 0;
        }

        .totals-table tr.border-subtle td {
            border-bottom: 1px solid #cbd5e1;
        }

        .totals-label {
            color: #1e293b;
            font-weight: 700;
            text-align: left;
        }

        .totals-val {
            text-align: right;
            font-weight: 800;
            color: #020617;
        }

        .totals-row-total-usd td {
            padding-top: 8px !important;
            border-top: 2px solid #0f172a;
            font-size: 13px;
            font-weight: 900;
            color: #020617;
        }

        .totals-row-total-ves td {
            padding-top: 3px;
            font-size: 11.5px;
            font-weight: 800;
            color: #0f3b75; /* Azul oscuro con excelente contraste */
        }

        /* 5. Footer (Conservado: Términos, Firmas y Auditoría) */
        .invoice-footer {
            margin-top: 24px;
            padding-top: 14px;
            border-top: 1.5px solid #cbd5e1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            align-items: flex-end;
        }

        .warranty-box {
            font-size: 9.5px;
            color: #1e293b; /* Términos oscurecidos */
            line-height: 1.45;
            font-weight: 500;
        }

        .warranty-box strong {
            color: #020617;
            display: block;
            margin-bottom: 2px;
            text-transform: uppercase;
            font-size: 10px;
            font-weight: 900;
        }

        .signatures-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            text-align: center;
        }

        .sign-card {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sign-line {
            width: 100%;
            border-bottom: 1.5px dashed #334155; /* Línea de firma más visible */
            height: 34px;
            margin-bottom: 5px;
        }

        .sign-caption {
            font-size: 9.5px;
            font-weight: 800;
            color: #020617;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .bottom-audit-bar {
            margin-top: 12px;
            padding-top: 6px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 9px;
            color: #475569; /* Auditoría en tono visible */
            letter-spacing: 0.3px;
            font-weight: 600;
        }

        /* Modos de Impresión */
        @media print {
            body {
                background: none;
                padding: 0;
                margin: 0;
                color: #000000;
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
                margin: 10mm 14mm;
            }
        }
    </style>
</head>
<body>

    <!-- Barra de acciones en pantalla -->
    <div class="actions-bar">
        <div class="btn-group">
            <button class="btn btn-print" onclick="window.print()">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                Imprimir Factura (A4)
            </button>
            <a href="{{ route('factura.ticket', $factura->id) }}" class="btn btn-ticket">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"></path><line x1="8" y1="8" x2="16" y2="8"></line><line x1="8" y1="12" x2="16" y2="12"></line><line x1="8" y1="16" x2="12" y2="16"></line></svg>
                Formato Ticket (80mm)
            </a>
        </div>
        <button class="btn btn-back" onclick="window.history.back()">
            ✕ Cerrar / Volver
        </button>
    </div>

    <!-- Hoja A4 -->
    <div class="invoice-sheet">

        @if($factura->estado === 'anulada')
            <div class="watermark-anulada">ANULADA</div>
        @endif

        <div>
            <!-- 1. Encabezado: Logo e Información de la Empresa -->
            <header class="invoice-header">
                <div class="company-block">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo Agrosuministros Rodríguez" class="company-logo">
                    <div class="company-details">
                        <h1>{{ $config->nombre_comercio ?? $config->nombre_empresa ?? 'Agrosuministros Rodríguez C.A.' }}</h1>
                        <div class="company-rif">RIF: {{ $config->rif ?? 'J-50493821-4' }}</div>
                        <p>{{ $config->direccion_fisica ?? 'Calle Principal, Sector Centro, Barinitas, Edo. Barinas' }}</p>
                        <p>Teléfono / WhatsApp: {{ $config->telefono_contacto ?? '+58 412-9614615' }}</p>
                        <p>Email: {{ $config->email_contacto ?? 'agrosuministrosrodriguez@gmail.com' }}</p>
                    </div>
                </div>

                <div class="invoice-meta">
                    <span class="title">FACTURA</span>
                    <span class="invoice-number">N° {{ $factura->numero_factura }}</span>
                    <div class="meta-item">
                        Fecha: <strong>{{ \Carbon\Carbon::parse($factura->fecha_emision)->format('d/m/Y h:i A') }}</strong>
                    </div>
                    <div class="meta-item">
                        Pedido: <strong>#{{ $factura->pedido_id }}</strong>
                    </div>
                    @if($factura->estado === 'anulada')
                        <span class="status-pill anulada">ANULADA</span>
                    @endif
                </div>
            </header>

            <!-- 2. Desglose de Datos: Cliente y Operación (Sin Dirección Fiscal del Cliente) -->
            <section class="client-section">
                <!-- Columna Izquierda: Adquiriente -->
                <div class="info-col">
                    <div class="info-title">Datos del Adquiriente / Cliente</div>
                    <div class="info-row">
                        <span class="info-label">Cliente:</span>
                        <span class="info-val">{{ $factura->nombre_razon_social ?: 'Consumidor Final' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Cédula / RIF:</span>
                        <span class="info-val">{{ $factura->cedula_rif_cliente ?: 'V-00000000' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Teléfono:</span>
                        <span class="info-val">{{ $factura->telefono_cliente ?: ($factura->pedido->usuario->telefono ?? 'No indicado') }}</span>
                    </div>
                </div>

                <!-- Columna Derecha: Datos de Venta -->
                <div class="info-col">
                    <div class="info-title">Condiciones de la Venta</div>
                    <div class="info-row">
                        <span class="info-label">Condición:</span>
                        <span class="info-val">Venta de Contado</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Canal:</span>
                        <span class="info-val">{{ in_array($factura->pedido->canal_venta ?? '', ['pos', 'tienda_fisica']) ? 'Mostrador (Tienda Física)' : 'Tienda en Línea' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tasa Oficial BCV:</span>
                        <span class="info-val">Bs. {{ number_format($factura->valor_tasa_bcv, 2) }} / USD</span>
                    </div>
                </div>
            </section>

            <!-- 3. Detalle de Productos / Insumos -->
            <section class="items-section">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 5%;">#</th>
                            <th class="text-left" style="width: 50%;">Descripción del Producto</th>
                            <th class="text-center" style="width: 12%;">Cant.</th>
                            <th class="text-right" style="width: 16%;">Precio Unit. ($)</th>
                            <th class="text-right" style="width: 17%;">Total ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($factura->pedido && $factura->pedido->detalles && count($factura->pedido->detalles) > 0)
                            @foreach($factura->pedido->detalles as $index => $det)
                            <tr>
                                <td class="text-center" style="color: #334155; font-weight: 600;">{{ $index + 1 }}</td>
                                <td class="text-left">
                                    <div class="item-name">{{ $det->producto ? $det->producto->nombre : 'Producto' }}</div>
                                    @if($det->producto && $det->producto->categoria)
                                        <div class="item-category">{{ $det->producto->categoria->nombre }}</div>
                                    @endif
                                </td>
                                <td class="text-center" style="font-weight: 700; color: #020617;">
                                    {{ (float) $det->cantidad_solicitada }} {{ $det->producto && $det->producto->unidad_medida ? $det->producto->unidad_medida : '' }}
                                </td>
                                <td class="text-right" style="color: #0f172a;">
                                    ${{ number_format($det->precio_historico_usd, 2) }}
                                    @if($det->producto && $det->producto->unidad_medida)
                                        <span style="font-size: 9.5px; color: #334155;">/{{ $det->producto->unidad_medida }}</span>
                                    @endif
                                </td>
                                <td class="text-right" style="font-weight: 800; color: #020617;">
                                    ${{ number_format($det->precio_historico_usd * $det->cantidad_solicitada, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center" style="padding: 16px; color: #334155; font-style: italic;">
                                    No hay ítems registrados en esta factura.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </section>

            <!-- 4. Resumen y Liquidación de Totales (Limpio, sin cuadros oscuros ni fondos saturados) -->
            <section class="summary-section">
                <!-- Columna Izquierda: Información de Pago y Nota Legal BCV -->
                <div class="payment-notes">
                    <div class="notes-title">Forma de Pago</div>
                    @if($factura->pedido && $factura->pedido->pago)
                        <div><strong>Medio:</strong> {{ strtoupper(str_replace('_', ' ', $factura->pedido->pago->metodo)) }}</div>
                        @if($factura->pedido->pago->referencia_bancaria)
                            <div><strong>Ref:</strong> {{ $factura->pedido->pago->referencia_bancaria }}</div>
                        @endif
                    @else
                        <div><strong>Modalidad:</strong> Pago de Contado / Caja</div>
                    @endif

                    <div class="bcv-text">
                        Monto en Bolívares (VES) calculado según la tasa oficial publicada por el Banco Central de Venezuela (BCV) a la fecha de la transacción.
                    </div>
                </div>

                <!-- Columna Derecha: Totales -->
                <div>
                    <table class="totals-table">
                        <tr class="border-subtle">
                            <td class="totals-label">Subtotal:</td>
                            <td class="totals-val">${{ number_format($factura->subtotal_usd, 2) }}</td>
                        </tr>
                        @if($factura->pedido && $factura->pedido->descuento_usd > 0)
                        <tr class="border-subtle" style="color: #dc2626;">
                            <td class="totals-label">Descuento:</td>
                            <td class="totals-val">-${{ number_format($factura->pedido->descuento_usd, 2) }}</td>
                        </tr>
                        @endif
                        @if($factura->pedido && $factura->pedido->costo_delivery_usd > 0)
                        <tr class="border-subtle">
                            <td class="totals-label">Flete / Delivery:</td>
                            <td class="totals-val">${{ number_format($factura->pedido->costo_delivery_usd, 2) }}</td>
                        </tr>
                        @endif
                        @if($factura->pedido && $factura->pedido->recargo_usd > 0)
                        <tr class="border-subtle">
                            <td class="totals-label">Recargo ({{ (float)$factura->pedido->recargo_porcentaje }}%):</td>
                            <td class="totals-val">${{ number_format($factura->pedido->recargo_usd, 2) }}</td>
                        </tr>
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
                        <tr class="border-subtle">
                            <td class="totals-label">IVA ({{ (float)$porcentajeIva }}%):</td>
                            <td class="totals-val">${{ number_format($impuestoMonto, 2) }}</td>
                        </tr>
                        <tr class="totals-row-total-usd">
                            <td>TOTAL USD:</td>
                            <td class="totals-val">${{ number_format($factura->total_usd, 2) }}</td>
                        </tr>
                        <tr class="totals-row-total-ves">
                            <td>TOTAL VES:</td>
                            <td class="totals-val">Bs. {{ number_format($factura->total_ves, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </section>
        </div>

        <!-- 5. Footer (Conservado: Términos, Firmas y Auditoría) -->
        <footer class="invoice-footer">
            <!-- Columna Izquierda: Garantías y Términos -->
            <div class="warranty-box">
                <strong>Términos y Condiciones de Entrega</strong>
                <p>Comprobante generado por el sistema de Agrosuministros Rodríguez C.A. Para devoluciones o reclamos de insumos agrícolas o pecuarios, conserve este documento. Plazo máximo de reclamación: 3 días hábiles desde la fecha de emisión.</p>
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
        </footer>

        <div class="bottom-audit-bar">
            Agrosuministros Rodríguez C.A. • RIF: J-50493821-4 • Barinitas, Edo. Barinas • Sistema de Gestión y Facturación
        </div>

    </div>

</body>
</html>
