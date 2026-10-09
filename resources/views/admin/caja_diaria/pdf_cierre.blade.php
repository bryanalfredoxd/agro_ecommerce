<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Cierre de Caja - Turno #{{ $sesion->id }}</title>
    <style>
        @page {
            margin: 28px 32px 35px 32px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: middle;
        }
        .empresa-title {
            font-size: 16px;
            font-weight: bold;
            color: #1e549e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .empresa-info {
            font-size: 9.5px;
            color: #64748b;
            margin-top: 2px;
            line-height: 1.3;
        }
        .doc-badge-table {
            text-align: right;
        }
        .doc-badge-box {
            display: inline-block;
            border: 1.5px solid #1e549e;
            border-radius: 6px;
            padding: 8px 14px;
            background-color: #f8fafc;
            text-align: right;
        }
        .doc-badge-title {
            font-size: 12px;
            font-weight: bold;
            color: #1e549e;
            text-transform: uppercase;
        }
        .doc-badge-number {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 2px;
        }
        .section-header {
            background-color: #f1f5f9;
            border-left: 3.5px solid #1e549e;
            padding: 5px 8px;
            font-size: 10.5px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            margin-top: 14px;
            margin-bottom: 6px;
        }
        .info-grid {
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .info-grid td {
            padding: 5px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .info-label {
            color: #64748b;
            font-weight: bold;
            width: 20%;
        }
        .info-value {
            color: #0f172a;
            width: 30%;
        }
        .data-table {
            border: 1px solid #cbd5e1;
            margin-top: 6px;
            margin-bottom: 12px;
        }
        .data-table th {
            background-color: #1e549e;
            color: #ffffff;
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 7px;
            border: 1px solid #1e549e;
        }
        .data-table td {
            padding: 5px 7px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9.5px;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .data-table tfoot td {
            background-color: #f1f5f9;
            font-weight: bold;
            border-top: 1.5px solid #94a3b8;
            padding: 6px 7px;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .cuadre-box {
            padding: 8px 12px;
            border-radius: 4px;
            margin-top: 6px;
            margin-bottom: 10px;
            font-size: 10.5px;
        }
        .cuadre-exacto {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }
        .cuadre-sobrante {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
        }
        .cuadre-faltante {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }
        .signatures-table {
            margin-top: 36px;
            page-break-inside: avoid;
        }
        .signature-line {
            border-top: 1px solid #64748b;
            width: 80%;
            margin: 0 auto;
            padding-top: 5px;
            text-align: center;
            font-size: 9.5px;
            color: #334155;
            font-weight: bold;
        }
        .signature-sub {
            font-size: 8.5px;
            color: #64748b;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- ENCABEZADO OFICIAL DE LA EMPRESA --}}
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <h1 class="empresa-title">{{ $config ? $config->nombre_comercial : 'Agrosuministros Rodríguez C.A.' }}</h1>
                <div class="empresa-info">
                    <strong>RIF:</strong> {{ $config ? $config->rif : 'J-50000000-0' }}<br>
                    <strong>Dirección:</strong> {{ $config ? $config->direccion : 'Venezuela' }}<br>
                    <strong>Teléfono:</strong> {{ $config ? $config->telefono_atencion : '+58 424-0000000' }} | <strong>Email:</strong> {{ $config ? $config->email_contacto : 'info@agrosuministros.com' }}
                </div>
            </td>
            <td style="width: 40%;" class="doc-badge-table">
                <div class="doc-badge-box">
                    <div class="doc-badge-title">Reporte de Cierre Z</div>
                    <div class="doc-badge-number">TURNO #{{ str_pad($sesion->id, 6, '0', STR_PAD_LEFT) }}</div>
                    <div style="font-size: 8.5px; color: #64748b; margin-top: 3px;">
                        Emisión: {{ now()->format('d/m/Y h:i A') }}
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- INFORMACIÓN GENERAL DEL TURNO --}}
    <div class="section-header">1. Identificación del Turno y Terminal</div>
    <table class="info-grid">
        <tr>
            <td class="info-label">Caja Registradora:</td>
            <td class="info-value"><strong>{{ $sesion->caja ? $sesion->caja->nombre : 'Caja Principal' }}</strong></td>
            <td class="info-label">Cajero Responsable:</td>
            <td class="info-value">
                <strong>{{ $sesion->cajero ? trim($sesion->cajero->nombre . ' ' . $sesion->cajero->apellido) : 'No asignado' }}</strong>
                @if($sesion->cajero && $sesion->cajero->documento_identidad)
                    <span style="color: #64748b;">({{ $sesion->cajero->documento_identidad }})</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="info-label">Fecha / Hora Apertura:</td>
            <td class="info-value">{{ \Carbon\Carbon::parse($sesion->fecha_apertura)->format('d/m/Y h:i:s A') }}</td>
            <td class="info-label">Fecha / Hora Cierre:</td>
            <td class="info-value">
                @if($sesion->fecha_cierre)
                    {{ \Carbon\Carbon::parse($sesion->fecha_cierre)->format('d/m/Y h:i:s A') }}
                @else
                    <span style="color: #059669; font-weight: bold;">[EN OPERACIÓN]</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="info-label">Estatus del Turno:</td>
            <td class="info-value">
                <strong>{{ $sesion->fecha_cierre ? 'CERRADO Y AUDITADO' : 'ABIERTO EN MOSTRADOR' }}</strong>
            </td>
            <td class="info-label">Tasa BCV Aplicada:</td>
            <td class="info-value">
                <strong>Bs. {{ $tasaCambio ? number_format($tasaCambio->valor_tasa, 2) : '1.00' }}</strong> por USD
            </td>
        </tr>
    </table>

    {{-- RESUMEN FINANCIERO Y ARQUEO FÍSICO --}}
    <div class="section-header">2. Conciliación y Arqueo de Gaveta Física (Efectivo)</div>
    <table class="info-grid">
        <tr>
            <td class="info-label">Base Inicial de Apertura:</td>
            <td class="info-value"><strong>${{ number_format($sesion->monto_inicial_usd, 2) }}</strong></td>
            <td class="info-label">Ventas Cobradas en Efectivo:</td>
            <td class="info-value" style="color: #059669;"><strong>+${{ number_format($totalEfectivoUsd, 2) }}</strong></td>
        </tr>
        <tr>
            <td class="info-label">Efectivo Físico Esperado:</td>
            <td class="info-value"><strong>${{ number_format($esperadoEfectivoUsd, 2) }}</strong></td>
            <td class="info-label">Efectivo Real Declarado:</td>
            <td class="info-value">
                @if($sesion->fecha_cierre)
                    <strong>${{ number_format($sesion->dinero_real_en_caja_usd, 2) }}</strong>
                @else
                    <span style="color: #64748b;">Pendiente al cierre</span>
                @endif
            </td>
        </tr>
    </table>

    @if($sesion->fecha_cierre)
        @php
            $dif = (float) $sesion->diferencia_usd;
            $claseCuadre = abs($dif) < 0.01 ? 'cuadre-exacto' : ($dif > 0 ? 'cuadre-sobrante' : 'cuadre-faltante');
        @endphp
        <div class="cuadre-box {{ $claseCuadre }}">
            <strong>Resultado del Cuadre de Gaveta: </strong>
            @if(abs($dif) < 0.01)
                CUADRE PERFECTO ($0.00 de diferencia). El efectivo físico coincide al 100% con los cobros del sistema.
            @elseif($dif > 0)
                SOBRANTE EN CAJA (+${{ number_format($dif, 2) }}). Existe más efectivo en gaveta del registrado en ventas.
            @else
                FALTANTE EN CAJA (-${{ number_format(abs($dif), 2) }}). Hay un déficit de efectivo físico respecto a las ventas del sistema.
            @endif

            @if($sesion->observaciones_cierre)
                <div style="margin-top: 4px; font-style: italic; font-size: 9.5px;">
                    <strong>Observación del cajero:</strong> "{{ $sesion->observaciones_cierre }}"
                </div>
            @endif
        </div>
    @endif

    {{-- DESGLOSE DE INGRESOS POR MÉTODO DE PAGO --}}
    <div class="section-header">3. Desglose de Ventas por Método de Pago</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 35%;">Método de Pago</th>
                <th class="text-center" style="width: 15%;">Operaciones</th>
                <th class="text-right" style="width: 20%;">Total USD ($)</th>
                <th class="text-right" style="width: 20%;">Total Bolívares (VES)</th>
                <th class="text-right" style="width: 10%;">% Part.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($desgloseMetodos as $dm)
            <tr>
                <td><strong>{{ $dm['metodo'] }}</strong></td>
                <td class="text-center">{{ $dm['cantidad'] }}</td>
                <td class="text-right"><strong>${{ number_format($dm['total_usd'], 2) }}</strong></td>
                <td class="text-right">Bs. {{ number_format($dm['total_ves'], 2) }}</td>
                <td class="text-right">{{ $dm['porcentaje'] }}%</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center" style="color: #64748b; font-style: italic; padding: 12px;">Sin registros de pago en este turno.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td>TOTALES DEL SISTEMA</td>
                <td class="text-center">{{ $pagos->count() }}</td>
                <td class="text-right" style="color: #1e549e;">${{ number_format($totalRecaudadoUsd, 2) }}</td>
                <td class="text-right">Bs. {{ number_format($totalRecaudadoVes, 2) }}</td>
                <td class="text-right">100%</td>
            </tr>
        </tfoot>
    </table>

    {{-- LISTADO DE FACTURAS Y DOCUMENTOS EMITIDOS --}}
    <div class="section-header">4. Detalle de Documentos / Facturas Emitidas ({{ $pedidos->count() }})</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 18%;">Documento</th>
                <th style="width: 12%;">Hora</th>
                <th style="width: 30%;">Cliente / Razón Social</th>
                <th style="width: 16%;">Cédula / RIF</th>
                <th class="text-right" style="width: 12%;">Total USD</th>
                <th class="text-right" style="width: 12%;">Total Bs.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pedidos as $p)
            <tr>
                <td>
                    <strong>{{ $p->factura ? $p->factura->numero_factura : 'Ticket #' . $p->id }}</strong>
                </td>
                <td>{{ \Carbon\Carbon::parse($p->creado_at)->format('h:i:s A') }}</td>
                <td>{{ $p->factura ? $p->factura->nombre_razon_social : ($p->usuario ? $p->usuario->nombre . ' ' . $p->usuario->apellido : 'Consumidor Final') }}</td>
                <td>{{ $p->factura ? ($p->factura->cedula_rif_cliente ?: 'N/A') : ($p->usuario && $p->usuario->documento_identidad ? $p->usuario->documento_identidad : 'V-00000000') }}</td>
                <td class="text-right"><strong>${{ number_format($p->total_usd, 2) }}</strong></td>
                <td class="text-right">Bs. {{ number_format($p->total_ves > 0 ? $p->total_ves : ($p->total_ves_calculado ?: ($p->factura ? $p->factura->total_ves : 0)), 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="color: #64748b; font-style: italic; padding: 12px;">Sin ventas registradas durante el turno.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FIRMAS DE RESPONSABILIDAD --}}
    <table class="signatures-table">
        <tr>
            <td style="width: 50%;">
                <div class="signature-line">
                    {{ $sesion->cajero ? trim($sesion->cajero->nombre . ' ' . $sesion->cajero->apellido) : 'Cajero Responsable' }}
                </div>
                <div class="signature-sub">
                    Firma del Cajero • C.I: {{ $sesion->cajero && $sesion->cajero->documento_identidad ? $sesion->cajero->documento_identidad : '____________________' }}
                </div>
            </td>
            <td style="width: 50%;">
                <div class="signature-line">
                    Administrador / Auditor de Caja
                </div>
                <div class="signature-sub">
                    Firma de Conformidad y Recepción de Arqueo
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
