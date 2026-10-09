<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Confirmado - Agrosuministros Rodríguez</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f1f5f9; padding: 30px 10px;">
        <tr>
            <td align="center">
                <!-- CONTENEDOR PRINCIPAL -->
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 600px; background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(11,29,56,0.08); border: 1px solid #e2e8f0;">
                    
                    <!-- ENCABEZADO CON DEGRADADO CORPORATIVO -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0b1d38 0%, #1e549e 60%, #3492cf 100%); padding: 36px 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 900; letter-spacing: -0.5px;">
                                Agrosuministros Rodríguez
                            </h1>
                            <p style="margin: 6px 0 0 0; color: #aad6f3; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                                Soluciones Integrales para el Campo
                            </p>
                        </td>
                    </tr>

                    <!-- BANNER DE CONFIRMACIÓN -->
                    <tr>
                        <td style="padding: 30px 30px 15px 30px; text-align: center;">
                            <div style="display: inline-block; width: 60px; height: 60px; line-height: 60px; border-radius: 50%; background-color: #ecfdf5; border: 2px solid #a7f3d0; color: #059669; font-size: 30px; margin-bottom: 12px;">
                                ✓
                            </div>
                            <h2 style="margin: 0; font-size: 22px; font-weight: 800; color: #0b1d38;">
                                ¡Tu pago ha sido aprobado!
                            </h2>
                            <p style="margin: 8px 0 0 0; font-size: 14px; color: #64748b; line-height: 1.5;">
                                Hola <strong>{{ $pedido->usuario ? $pedido->usuario->nombre : 'Estimado Cliente' }}</strong>, hemos verificado tu pago y tu orden ya está en proceso de preparación.
                            </p>
                        </td>
                    </tr>

                    <!-- TARJETA CON DETALLES DE LA ORDEN -->
                    <tr>
                        <td style="padding: 10px 30px 20px 30px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f8fafc; border-radius: 14px; border: 1px solid #e2e8f0; padding: 16px;">
                                <tr>
                                    <td style="padding: 6px 12px; font-size: 12px; color: #64748b; font-weight: 700; text-transform: uppercase;">Número de Orden:</td>
                                    <td style="padding: 6px 12px; font-size: 14px; color: #1e549e; font-weight: 800; text-align: right;">#{{ $pedido->id }}</td>
                                </tr>
                                @if($factura)
                                <tr>
                                    <td style="padding: 6px 12px; font-size: 12px; color: #64748b; font-weight: 700; text-transform: uppercase;">Factura Emitida:</td>
                                    <td style="padding: 6px 12px; font-size: 14px; color: #0b1d38; font-weight: 800; text-align: right;">{{ $factura->numero_factura }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding: 6px 12px; font-size: 12px; color: #64748b; font-weight: 700; text-transform: uppercase;">Método de Entrega:</td>
                                    <td style="padding: 6px 12px; font-size: 13px; color: #0b1d38; font-weight: 700; text-align: right;">
                                        {{ $pedido->zona_delivery_id ? 'Delivery a Domicilio' : 'Retiro en Tienda Física' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 12px; font-size: 12px; color: #64748b; font-weight: 700; text-transform: uppercase;">Dirección / Punto:</td>
                                    <td style="padding: 6px 12px; font-size: 13px; color: #334155; text-align: right;">{{ $pedido->direccion_texto }}</td>
                                </tr>
                                @if($pedido->pago && $pedido->pago->referencia_bancaria)
                                <tr>
                                    <td style="padding: 6px 12px; font-size: 12px; color: #64748b; font-weight: 700; text-transform: uppercase;">Ref. de Pago:</td>
                                    <td style="padding: 6px 12px; font-size: 13px; color: #334155; font-family: monospace; font-weight: 700; text-align: right;">{{ $pedido->pago->referencia_bancaria }}</td>
                                </tr>
                                @endif
                            </table>
                        </td>
                    </tr>

                    <!-- TABLA DE ARTÍCULOS -->
                    <tr>
                        <td style="padding: 10px 30px;">
                            <h3 style="margin: 0 0 12px 0; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #0b1d38;">
                                Resumen de Artículos
                            </h3>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 2px solid #e2e8f0;">
                                        <th align="left" style="padding: 8px 0; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Producto</th>
                                        <th align="center" style="padding: 8px 0; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Cant.</th>
                                        <th align="right" style="padding: 8px 0; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Total USD</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pedido->detalles as $det)
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding: 12px 0; font-size: 13px; color: #1e293b; font-weight: 600;">
                                            {{ $det->producto ? $det->producto->nombre : 'Artículo' }}
                                        </td>
                                        <td align="center" style="padding: 12px 0; font-size: 13px; color: #64748b;">
                                            {{ (float) $det->cantidad_solicitada }}
                                        </td>
                                        <td align="right" style="padding: 12px 0; font-size: 13px; color: #1e549e; font-weight: 700;">
                                            ${{ number_format($det->precio_historico_usd * $det->cantidad_solicitada, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    <!-- TOTALES -->
                    <tr>
                        <td style="padding: 10px 30px 30px 30px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-top: 2px dashed #e2e8f0; padding-top: 14px; margin-top: 8px;">
                                <tr>
                                    <td style="padding: 4px 0; font-size: 13px; color: #64748b;">Subtotal:</td>
                                    <td align="right" style="padding: 4px 0; font-size: 13px; color: #334155; font-weight: 700;">${{ number_format($pedido->subtotal_usd, 2) }} USD</td>
                                </tr>
                                @if($pedido->costo_delivery_usd > 0)
                                <tr>
                                    <td style="padding: 4px 0; font-size: 13px; color: #64748b;">Costo Delivery:</td>
                                    <td align="right" style="padding: 4px 0; font-size: 13px; color: #334155; font-weight: 700;">${{ number_format($pedido->costo_delivery_usd, 2) }} USD</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding: 8px 0; font-size: 16px; font-weight: 800; color: #0b1d38;">TOTAL PAGADO:</td>
                                    <td align="right" style="padding: 8px 0; font-size: 20px; font-weight: 900; color: #1e549e;">${{ number_format($pedido->total_usd, 2) }} USD</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td align="right" style="font-size: 12px; color: #64748b; font-weight: 600;">Bs. {{ number_format($pedido->total_ves_calculado, 2) }}</td>
                                </tr>
                            </table>

                            <div style="margin-top: 24px; text-align: center;">
                                <a href="{{ url('/perfil/pedidos/' . $pedido->id) }}" style="display: inline-block; background-color: #1e549e; color: #ffffff; text-decoration: none; padding: 14px 28px; border-radius: 12px; font-size: 14px; font-weight: 800; box-shadow: 0 4px 14px rgba(30,84,158,0.3);">
                                    Ver Detalle en Mi Cuenta
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- PIE DE PÁGINA -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 24px 30px; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #64748b;">
                                Agrosuministros Rodríguez C.A. &bull; Barinitas, Estado Barinas
                            </p>
                            <p style="margin: 6px 0 0 0; font-size: 11px; color: #94a3b8;">
                                ¿Tienes alguna pregunta? Contáctanos por WhatsApp al +58 412-9614615
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
