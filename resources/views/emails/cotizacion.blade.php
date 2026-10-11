<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización Oficial #{{ $cotizacion->numero_cotizacion }} - Agrosuministros Rodríguez</title>
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

                    <!-- 2. BANNER DE ESTADO: COTIZACIÓN EMITIDA -->
                    <tr>
                        <td style="padding: 32px 32px 16px 32px; text-align: center;">
                            <div style="display: inline-block; background-color: #f0fdf4; border: 1.5px solid #16a34a; color: #15803d; font-size: 11px; font-weight: 800; padding: 4px 14px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 14px;">
                                📄 COTIZACIÓN DE PRODUCTOS
                            </div>
                            <h2 style="margin: 0; font-size: 22px; font-weight: 900; color: #0b1d38; letter-spacing: -0.3px;">
                                Presupuesto Comercial Oficial
                            </h2>
                            <p style="margin: 10px auto 0 auto; max-width: 480px; font-size: 13.5px; color: #334155; line-height: 1.5;">
                                Estimado(a) <strong>{{ $cotizacion->cliente_nombre }}</strong>, le presentamos la cotización formal de los insumos agropecuarios solicitados. Adjunto a este correo encontrará el documento en formato <strong>PDF oficial</strong> listo para su descarga o impresión.
                            </p>
                        </td>
                    </tr>

                    <!-- 3. DETALLES DE LA COTIZACIÓN -->
                    <tr>
                        <td style="padding: 12px 32px 20px 32px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px;">
                                <tr>
                                    <td colspan="2" style="padding-bottom: 10px; border-bottom: 1px solid #e2e8f0;">
                                        <span style="font-size: 11px; font-weight: 800; color: #0b1d38; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Datos del Presupuesto
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0 4px 0; font-size: 12px; color: #475569; font-weight: 600;">Cotización Nº:</td>
                                    <td align="right" style="padding: 8px 0 4px 0; font-size: 13px; color: #1e549e; font-weight: 800; font-family: monospace;">{{ $cotizacion->numero_cotizacion }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px 0; font-size: 12px; color: #475569; font-weight: 600;">Fecha de Emisión:</td>
                                    <td align="right" style="padding: 4px 0; font-size: 12px; color: #0f172a; font-weight: 700;">{{ \Carbon\Carbon::parse($cotizacion->fecha_emision)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px 0; font-size: 12px; color: #475569; font-weight: 600;">Validez de la Oferta:</td>
                                    <td align="right" style="padding: 4px 0; font-size: 12px; color: #b45309; font-weight: 800;">
                                        {{ $cotizacion->validez_dias }} días (Hasta {{ $cotizacion->fecha_vencimiento ? \Carbon\Carbon::parse($cotizacion->fecha_vencimiento)->format('d/m/Y') : 'N/A' }})
                                    </td>
                                </tr>
                                @if($cotizacion->cliente_documento)
                                <tr>
                                    <td style="padding: 4px 0; font-size: 12px; color: #475569; font-weight: 600;">Cédula / RIF Cliente:</td>
                                    <td align="right" style="padding: 4px 0; font-size: 12px; color: #0f172a; font-weight: 700; font-family: monospace;">{{ $cotizacion->cliente_documento }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding: 4px 0; font-size: 12px; color: #475569; font-weight: 600;">Tasa de Referencia BCV:</td>
                                    <td align="right" style="padding: 4px 0; font-size: 12px; color: #0f172a; font-weight: 700;">Bs. {{ number_format($cotizacion->valor_tasa_bcv, 4) }} / USD</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- 4. TABLA DE ARTÍCULOS COTIZADOS -->
                    <tr>
                        <td style="padding: 10px 32px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th align="left" style="padding: 8px 4px; font-size: 10.5px; font-weight: 800; color: #0b1d38; text-transform: uppercase; border-bottom: 2px solid #0b1d38;">
                                            Ítem / Descripción
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
                                    @if($cotizacion->detalles && count($cotizacion->detalles) > 0)
                                        @foreach($cotizacion->detalles as $det)
                                        <tr style="border-bottom: 1px solid #e2e8f0;">
                                            <td style="padding: 10px 4px; font-size: 12.5px; color: #0f172a; font-weight: 700;">
                                                <div>{{ $det->descripcion }}</div>
                                                <div style="font-size: 10px; color: #64748b; font-weight: 500; margin-top: 2px;">
                                                    P. Unitario: ${{ number_format($det->precio_unitario_usd, 2) }}
                                                </div>
                                            </td>
                                            <td align="center" style="padding: 10px 4px; font-size: 12.5px; color: #0f172a; font-weight: 700;">
                                                {{ (float) $det->cantidad }} <span style="font-size: 10px; color: #475569;">{{ $det->unidad_medida ?: 'ud' }}</span>
                                            </td>
                                            <td align="right" style="padding: 10px 4px; font-size: 13px; color: #0b1d38; font-weight: 800;">
                                                ${{ number_format($det->total_usd, 2) }}
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
                                        ${{ number_format($cotizacion->subtotal_usd, 2) }} USD
                                    </td>
                                </tr>
                                @if($cotizacion->iva_usd > 0)
                                <tr>
                                    <td style="padding: 3px 0; font-size: 12px; color: #475569; font-weight: 600;">IVA ({{ (float)$cotizacion->iva_porcentaje }}%):</td>
                                    <td align="right" style="padding: 3px 0; font-size: 12.5px; color: #0f172a; font-weight: 700;">
                                        ${{ number_format($cotizacion->iva_usd, 2) }} USD
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding: 6px 0 2px 0; font-size: 14px; font-weight: 900; color: #0b1d38; border-top: 1.5px solid #0b1d38;">
                                        TOTAL COTIZADO (USD):
                                    </td>
                                    <td align="right" style="padding: 6px 0 2px 0; font-size: 17px; font-weight: 900; color: #0b1d38; border-top: 1.5px solid #0b1d38;">
                                        ${{ number_format($cotizacion->total_usd, 2) }} USD
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 2px 0; font-size: 11px; color: #1e549e; font-weight: 700;">
                                        Equivalente en Moneda Nacional (BCV):
                                    </td>
                                    <td align="right" style="padding: 2px 0; font-size: 13px; color: #1e549e; font-weight: 800;">
                                        Bs. {{ number_format($cotizacion->total_ves, 2) }}
                                    </td>
                                </tr>
                            </table>
                            
                            @if($cotizacion->observaciones)
                            <div style="margin-top: 16px; background-color: #f1f5f9; border-left: 3px solid #1e549e; padding: 10px 14px; border-radius: 4px; font-size: 11px; color: #334155; line-height: 1.4;">
                                <strong>Observaciones y Condiciones:</strong><br>
                                {{ $cotizacion->observaciones }}
                            </div>
                            @endif

                            <div style="margin-top: 14px; background-color: #eff6ff; border: 1px solid #bfdbfe; padding: 10px 14px; border-radius: 8px; text-align: center; font-size: 11.5px; color: #1e40af; font-weight: 600;">
                                📎 El documento formal en PDF se encuentra adjunto a este correo.
                            </div>
                        </td>
                    </tr>

                    <!-- 6. PIE DE PÁGINA Y CONTACTO -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 24px 32px; text-align: center;">
                            <p style="margin: 0; font-size: 11.5px; font-weight: 700; color: #334155;">
                                {{ $config->nombre_comercio ?? $config->nombre_empresa ?? 'Agrosuministros Rodríguez C.A.' }}
                            </p>
                            <p style="margin: 4px 0 0 0; font-size: 11px; color: #64748b;">
                                {{ $config->direccion_fisica ?? 'Calle Principal, Sector Centro, Barinitas, Estado Barinas, Venezuela' }}
                            </p>
                            <p style="margin: 8px 0 0 0; font-size: 11px; color: #475569; font-weight: 600;">
                                Para aprobar esta cotización o consultar disponibilidad, contáctenos directamente:
                            </p>
                            <p style="margin: 3px 0 0 0; font-size: 11px; color: #1e549e; font-weight: 700;">
                                WhatsApp: {{ $config->telefono_contacto ?? '+58 412-9614615' }} • {{ $config->email_contacto ?? 'agrosuministrosrodriguez@gmail.com' }}
                            </p>
                            <p style="margin: 12px 0 0 0; font-size: 9.5px; color: #94a3b8; line-height: 1.4;">
                                Cotización generada automáticamente por el sistema de Agrosuministros Rodríguez C.A.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
