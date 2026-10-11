<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Digital {{ $factura->numero_factura }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #e2e8f0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #e2e8f0; padding: 25px 10px;">
        <tr>
            <td align="center">
                
                {{-- Encabezado informativo del correo --}}
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 360px; margin-bottom: 12px; text-align: center;">
                    <tr>
                        <td align="center">
                            <p style="margin: 0; font-size: 13px; font-weight: 700; color: #0b1d38; text-transform: uppercase; letter-spacing: 0.5px;">
                                Agrosuministros Rodríguez
                            </p>
                            <p style="margin: 3px 0 0 0; font-size: 11px; color: #64748b;">
                                Tu comprobante digital de compra
                            </p>
                        </td>
                    </tr>
                </table>

                {{-- CONTENEDOR TICKET 80MM (TÉRMICO) --}}
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 350px; background-color: #ffffff; border-radius: 6px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border: 1px solid #cbd5e1; padding: 16px 14px; font-family: 'Courier New', Courier, monospace; color: #0f172a; font-size: 11px; line-height: 1.35;">
                    
                    {{-- Encabezado del Comercio --}}
                    <tr>
                        <td align="center" style="padding-bottom: 8px;">
                            <div style="font-size: 13px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; color: #0b1d38;">
                                {{ $config->nombre_comercio ?? 'Agrosuministros Rodríguez' }}
                            </div>
                            <div style="font-size: 10px; color: #334155; margin-top: 2px;">
                                RIF: {{ $config->rif ?? 'J-50493821-4' }}
                            </div>
                            <div style="font-size: 9.5px; color: #475569; margin-top: 1px;">
                                {{ $config->direccion_fisica ?? 'Barinitas, Estado Barinas, Venezuela' }}
                            </div>
                            <div style="font-size: 9.5px; color: #475569; margin-top: 1px;">
                                Tel: {{ $config->telefono_contacto ?? '+58 412-9614615' }}
                            </div>
                        </td>
                    </tr>

                    {{-- Línea Divisoria --}}
                    <tr>
                        <td style="border-top: 1px dashed #475569; padding-top: 6px; padding-bottom: 6px; text-align: center;">
                            <div style="font-size: 12px; font-weight: 900; letter-spacing: 1px; color: #0b1d38;">
                                TICKET DE VENTA (80mm)
                            </div>
                        </td>
                    </tr>

                    {{-- Datos Fiscales de la Factura --}}
                    <tr>
                        <td style="padding-bottom: 8px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="font-size: 10.5px; line-height: 1.4;">
                                <tr>
                                    <td style="font-weight: bold; color: #334155;">Factura Nº:</td>
                                    <td align="right" style="font-weight: 900; color: #0b1d38;">{{ $factura->numero_factura }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; color: #334155;">Pedido ID:</td>
                                    <td align="right" style="font-weight: bold;">#{{ $factura->pedido_id }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; color: #334155;">Fecha:</td>
                                    <td align="right">{{ \Carbon\Carbon::parse($factura->fecha_emision)->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; color: #334155;">Cliente:</td>
                                    <td align="right" style="font-weight: bold; color: #0b1d38;">{{ Str::limit($factura->nombre_razon_social, 20) }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; color: #334155;">Cédula / RIF:</td>
                                    <td align="right">{{ $factura->cedula_rif_cliente ?: 'V-00000000' }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; color: #334155;">Canal:</td>
                                    <td align="right">
                                        @if($factura->pedido && $factura->pedido->canal_venta === 'web')
                                            TIENDA WEB
                                        @elseif($factura->pedido && $factura->pedido->canal_venta === 'whatsapp')
                                            PEDIDO WHATSAPP
                                        @else
                                            MOSTRADOR POS
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Tabla de Productos --}}
                    <tr>
                        <td style="border-top: 1px dashed #475569; border-bottom: 1px dashed #475569; padding: 6px 0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="font-size: 10px; width: 100%;">
                                <thead>
                                    <tr style="font-weight: 900; text-transform: uppercase;">
                                        <th align="left" style="padding-bottom: 4px; width: 50%;">Desc.</th>
                                        <th align="center" style="padding-bottom: 4px; width: 22%;">Cant</th>
                                        <th align="right" style="padding-bottom: 4px; width: 28%;">Total</th>
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
                                            <td style="padding: 3px 0; vertical-align: top;">
                                                <div style="font-weight: bold; color: #0b1d38;">{{ Str::limit($det->producto ? $det->producto->nombre : 'Producto', 16) }}</div>
                                                <div style="font-size: 8.5px; color: #64748b;">@ ${{ number_format($det->precio_historico_usd, 2) }}/{{ $uLabel }}</div>
                                            </td>
                                            <td align="center" style="padding: 3px 0; vertical-align: top; font-weight: bold;">
                                                {{ (float)$det->cantidad_solicitada }} {{ $uLabel }}
                                            </td>
                                            <td align="right" style="padding: 3px 0; vertical-align: top; font-weight: bold; color: #0b1d38;">
                                                ${{ number_format($det->precio_historico_usd * $det->cantidad_solicitada, 2) }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    {{-- Sección de Totales --}}
                    <tr>
                        <td style="padding-top: 8px; padding-bottom: 8px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="font-size: 10.5px; line-height: 1.45;">
                                <tr>
                                    <td style="color: #475569;">Subtotal Neto:</td>
                                    <td align="right" style="font-weight: bold;">${{ number_format($factura->subtotal_usd, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="color: #475569;">IVA ({{ (float)$factura->iva_porcentaje }}%):</td>
                                    <td align="right" style="font-weight: bold;">${{ number_format($factura->monto_iva_usd, 2) }}</td>
                                </tr>
                                
                                {{-- Total Dólares --}}
                                <tr>
                                    <td colspan="2" style="border-top: 1px dashed #475569; padding-top: 5px; margin-top: 4px;"></td>
                                </tr>
                                <tr>
                                    <td style="font-size: 12px; font-weight: 900; color: #0b1d38;">TOTAL USD:</td>
                                    <td align="right" style="font-size: 13px; font-weight: 900; color: #059669;">
                                        ${{ number_format($factura->total_usd, 2) }}
                                    </td>
                                </tr>

                                {{-- Tasa y Total Bolívares --}}
                                @php
                                    $tasaAplicada = (float)($factura->tasa_cambio_historica ?: ($factura->pedido && $factura->pedido->tasaCambio ? $factura->pedido->tasaCambio->valor_tasa : 1));
                                    $totalVesCalc = (float)($factura->total_ves ?: ($factura->total_usd * $tasaAplicada));
                                @endphp
                                <tr>
                                    <td style="font-size: 9.5px; color: #64748b;">Tasa BCV Aplicada:</td>
                                    <td align="right" style="font-size: 9.5px; font-weight: bold; color: #475569;">
                                        Bs. {{ number_format($tasaAplicada, 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size: 12px; font-weight: 900; color: #0b1d38;">TOTAL BS (VES):</td>
                                    <td align="right" style="font-size: 13px; font-weight: 900; color: #1e549e;">
                                        Bs. {{ number_format($totalVesCalc, 2) }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Formas de Pago --}}
                    @if($factura->pedido && $factura->pedido->pagos && $factura->pedido->pagos->count() > 0)
                    <tr>
                        <td style="border-top: 1px dashed #475569; padding-top: 6px; padding-bottom: 6px;">
                            <div style="font-size: 9.5px; font-weight: 900; text-transform: uppercase; margin-bottom: 3px; color: #334155;">
                                FORMAS DE PAGO:
                            </div>
                            @foreach($factura->pedido->pagos as $p)
                            <div style="font-size: 9.5px; display: flex; justify-content: space-between; margin-bottom: 1px;">
                                <span>- {{ strtoupper(str_replace('_', ' ', $p->metodo)) }}:</span>
                                <span style="font-weight: bold;">
                                    ${{ number_format($p->monto_usd, 2) }}
                                    @if($p->referencia_bancaria)
                                        <span style="font-size: 8.5px; color: #64748b;">(Ref: {{ $p->referencia_bancaria }})</span>
                                    @endif
                                </span>
                            </div>
                            @endforeach
                        </td>
                    </tr>
                    @endif

                    {{-- Pie del Ticket --}}
                    <tr>
                        <td style="border-top: 1px dashed #475569; padding-top: 10px; text-align: center; font-size: 9.5px; color: #475569; line-height: 1.4;">
                            <div style="font-weight: 900; text-transform: uppercase; color: #0b1d38; margin-bottom: 2px;">
                                ¡Gracias por su compra!
                            </div>
                            <div>Conserve este ticket digital como comprobante de garantía.</div>
                            <div style="margin-top: 4px; font-size: 8.5px; color: #94a3b8;">
                                No se realizan devoluciones después de 48 horas continuas.
                            </div>
                        </td>
                    </tr>



                </table>

                {{-- Pie del Email --}}
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 360px; margin-top: 14px; text-align: center; font-size: 10px; color: #94a3b8;">
                    <tr>
                        <td>
                            Enviado automáticamente por Agrosuministros Rodríguez<br>
                            &copy; {{ date('Y') }} Agrosuministros Rodríguez. Todos los derechos reservados.
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>
</html>
