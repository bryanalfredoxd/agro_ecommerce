<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Hemos recibido tu Pedido #{{ $pedido->id }}! - Agrosuministros Rodríguez</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #0f172a; -webkit-font-smoothing: antialiased;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f1f5f9; padding: 32px 12px;">
        <tr>
            <td align="center">
                <!-- CONTENEDOR PRINCIPAL DEL EMAIL -->
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(11, 29, 56, 0.08); border: 1px solid #e2e8f0;">
                    
                    <!-- 1. ENCABEZADO CORPORATIVO -->
                    <tr>
                        <td style="background-color: #0b1d38; padding: 28px 32px; text-align: center; border-bottom: 3px solid #1e549e;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center">
                                        @php
                                            $appUrl = config('app.url', '');
                                            $isLocal = empty($appUrl) || str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1');
                                            $logoUrl = $isLocal 
                                                ? 'https://raw.githubusercontent.com/bryanalfredoxd/agro_ecommerce/main/public/favicon.ico' 
                                                : asset('favicon.ico');
                                        @endphp
                                        <img src="{{ $logoUrl }}" alt="Agrosuministros Rodríguez" width="70" height="70" style="display: block; margin: 0 auto 12px auto; background-color: #ffffff; border-radius: 50%; padding: 3px; box-shadow: 0 2px 8px rgba(0,0,0,0.25);">
                                        <h1 style="margin: 0; color: #ffffff; font-size: 20px; font-weight: 900; letter-spacing: 0.5px; text-transform: uppercase;">
                                            {{ $config->nombre_comercio ?? $config->nombre_empresa ?? 'Agrosuministros Rodríguez C.A.' }}
                                        </h1>
                                        <p style="margin: 4px 0 0 0; color: #93c5fd; font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">
                                            RIF: {{ $config->rif ?? 'J-50493821-4' }} • Barinitas, Edo. Barinas
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- 2. BANNER DE ESTADO: PEDIDO RECIBIDO -->
                    <tr>
                        <td style="padding: 32px 32px 16px 32px; text-align: center;">
                            <div style="display: inline-block; background-color: #eff6ff; border: 1.5px solid #3b82f6; color: #1d4ed8; font-size: 11px; font-weight: 800; padding: 4px 14px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 14px;">
                                🕒 ORDEN REGISTRADA & PAGO EN REVISIÓN
                            </div>
                            <h2 style="margin: 0; font-size: 22px; font-weight: 900; color: #0b1d38; letter-spacing: -0.3px;">
                                ¡Hemos recibido tu orden con éxito!
                            </h2>
                            <p style="margin: 10px auto 0 auto; max-width: 480px; font-size: 13.5px; color: #334155; line-height: 1.5;">
                                Hola <strong>{{ $pedido->usuario ? $pedido->usuario->nombre . ' ' . $pedido->usuario->apellido : 'Estimado Cliente' }}</strong>, gracias por tu compra en nuestra tienda virtual. Tu pedido <strong>#{{ $pedido->id }}</strong> ha sido registrado en el sistema y nuestro equipo administrativo está validando tu comprobante de pago.
                            </p>
                        </td>
                    </tr>

                    <!-- 3. DETALLES DE LA ORDEN -->
                    <tr>
                        <td style="padding: 12px 32px 20px 32px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px;">
                                <tr>
                                    <td colspan="2" style="padding-bottom: 10px; border-bottom: 1px solid #e2e8f0;">
                                        <span style="font-size: 11px; font-weight: 800; color: #0b1d38; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Información del Pedido
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0 4px 0; font-size: 12px; color: #475569; font-weight: 600;">Número de Pedido:</td>
                                    <td align="right" style="padding: 8px 0 4px 0; font-size: 13px; color: #0b1d38; font-weight: 800; font-family: monospace;">#{{ $pedido->id }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px 0; font-size: 12px; color: #475569; font-weight: 600;">Fecha de Compra:</td>
                                    <td align="right" style="padding: 4px 0; font-size: 12px; color: #0f172a; font-weight: 700;">{{ \Carbon\Carbon::parse($pedido->creado_at)->format('d/m/Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px 0; font-size: 12px; color: #475569; font-weight: 600;">Estado Actual:</td>
                                    <td align="right" style="padding: 4px 0; font-size: 12px; color: #1e549e; font-weight: 800;">
                                        En espera de verificación de pago
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px 0; font-size: 12px; color: #475569; font-weight: 600;">Modalidad de Despacho:</td>
                                    <td align="right" style="padding: 4px 0; font-size: 12px; color: #047857; font-weight: 800;">
                                        {{ $pedido->zona_delivery_id ? '🚚 Delivery a Domicilio' : '🏬 Retiro en Tienda Física' }}
                                    </td>
                                </tr>
                                @if($pedido->direccion_texto)
                                <tr>
                                    <td style="padding: 4px 0 0 0; font-size: 12px; color: #475569; font-weight: 600; vertical-align: top;">Destino:</td>
                                    <td align="right" style="padding: 4px 0 0 0; font-size: 12px; color: #0f172a; font-weight: 600; max-width: 280px; word-break: break-word;">
                                        {{ $pedido->direccion_texto }}
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </td>
                    </tr>

                    <!-- 4. TABLA DE ARTÍCULOS COMPRADOS -->
                    <tr>
                        <td style="padding: 10px 32px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th align="left" style="padding: 8px 4px; font-size: 10.5px; font-weight: 800; color: #0b1d38; text-transform: uppercase; border-bottom: 2px solid #0b1d38;">
                                            Descripción del Producto
                                        </th>
                                        <th align="center" style="padding: 8px 4px; font-size: 10.5px; font-weight: 800; color: #0b1d38; text-transform: uppercase; border-bottom: 2px solid #0b1d38; width: 60px;">
                                            Cant.
                                        </th>
                                        <th align="right" style="padding: 8px 4px; font-size: 10.5px; font-weight: 800; color: #0b1d38; text-transform: uppercase; border-bottom: 2px solid #0b1d38; width: 90px;">
                                            Total ($)
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($pedido->detalles && count($pedido->detalles) > 0)
                                        @foreach($pedido->detalles as $det)
                                        <tr style="border-bottom: 1px solid #e2e8f0;">
                                            <td style="padding: 10px 4px; font-size: 12.5px; color: #0f172a; font-weight: 700;">
                                                <div>{{ $det->producto ? $det->producto->nombre : 'Artículo Agropecuario' }}</div>
                                                @if($det->producto && $det->producto->categoria)
                                                    <div style="font-size: 10px; color: #475569; font-weight: 500; margin-top: 2px;">
                                                        Categoría: {{ $det->producto->categoria->nombre }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td align="center" style="padding: 10px 4px; font-size: 12.5px; color: #0f172a; font-weight: 700;">
                                                {{ (float) $det->cantidad_solicitada }}
                                                @if($det->producto && $det->producto->unidad_medida)
                                                    <span style="font-size: 10px; color: #475569;">{{ $det->producto->unidad_medida }}</span>
                                                @endif
                                            </td>
                                            <td align="right" style="padding: 10px 4px; font-size: 13px; color: #0b1d38; font-weight: 800;">
                                                ${{ number_format($det->precio_historico_usd * $det->cantidad_solicitada, 2) }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    <!-- 5. LIQUIDACIÓN DE TOTALES -->
                    <tr>
                        <td style="padding: 10px 32px 32px 32px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-top: 1.5px solid #cbd5e1; padding-top: 10px;">
                                <tr>
                                    <td style="padding: 3px 0; font-size: 12px; color: #475569; font-weight: 600;">Subtotal Neto:</td>
                                    <td align="right" style="padding: 3px 0; font-size: 12.5px; color: #0f172a; font-weight: 700;">
                                        ${{ number_format($pedido->subtotal_usd, 2) }} USD
                                    </td>
                                </tr>
                                @if($pedido->costo_delivery_usd > 0)
                                <tr>
                                    <td style="padding: 3px 0; font-size: 12px; color: #475569; font-weight: 600;">Flete / Delivery:</td>
                                    <td align="right" style="padding: 3px 0; font-size: 12.5px; color: #0f172a; font-weight: 700;">
                                        ${{ number_format($pedido->costo_delivery_usd, 2) }} USD
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding: 6px 0 2px 0; font-size: 14px; font-weight: 900; color: #0b1d38; border-top: 1.5px solid #0b1d38;">
                                        TOTAL ORDEN (USD):
                                    </td>
                                    <td align="right" style="padding: 6px 0 2px 0; font-size: 17px; font-weight: 900; color: #0b1d38; border-top: 1.5px solid #0b1d38;">
                                        ${{ number_format($pedido->total_usd, 2) }} USD
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 2px 0; font-size: 11px; color: #1e549e; font-weight: 700;">
                                        Equivalente en Moneda Nacional (BCV):
                                    </td>
                                    <td align="right" style="padding: 2px 0; font-size: 13px; color: #1e549e; font-weight: 800;">
                                        Bs. {{ number_format($pedido->total_ves_calculado, 2) }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>



                    <!-- 7. PIE DE PÁGINA -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 24px 32px; text-align: center;">
                            <p style="margin: 0; font-size: 11.5px; font-weight: 700; color: #334155;">
                                {{ $config->nombre_comercio ?? $config->nombre_empresa ?? 'Agrosuministros Rodríguez C.A.' }}
                            </p>
                            <p style="margin: 4px 0 0 0; font-size: 11px; color: #64748b;">
                                {{ $config->direccion_fisica ?? 'Calle Principal, Sector Centro, Barinitas, Estado Barinas, Venezuela' }}
                            </p>
                            <p style="margin: 8px 0 0 0; font-size: 11px; color: #475569; font-weight: 600;">
                                ¿Tienes alguna pregunta con respecto a tu pago?
                            </p>
                            <p style="margin: 3px 0 0 0; font-size: 11px; color: #1e549e; font-weight: 700;">
                                WhatsApp de Atención: {{ $config->telefono_contacto ?? '+58 412-9614615' }}
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
