<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de Contraseña - {{ $config->nombre_comercio ?? $config->nombre_empresa ?? 'Agrosuministros Rodríguez' }}</title>
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
                                        <img src="{{ $logoUrl }}" alt="Logo" width="65" height="65" style="display: block; margin: 0 auto 12px auto; background-color: #ffffff; border-radius: 50%; padding: 3px; box-shadow: 0 2px 8px rgba(0,0,0,0.25);">
                                        <h1 style="margin: 0; color: #ffffff; font-size: 20px; font-weight: 900; letter-spacing: 0.5px; text-transform: uppercase;">
                                            {{ $config->nombre_comercio ?? $config->nombre_empresa ?? 'Agrosuministros Rodríguez C.A.' }}
                                        </h1>
                                        <p style="margin: 4px 0 0 0; color: #93c5fd; font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">
                                            Seguridad & Acceso de Usuarios
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- 2. BANNER DE ESTADO -->
                    <tr>
                        <td style="padding: 32px 32px 16px 32px; text-align: center;">
                            <div style="display: inline-block; background-color: #eff6ff; border: 1.5px solid #3b82f6; color: #1d4ed8; font-size: 11px; font-weight: 800; padding: 4px 14px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 14px;">
                                🔐 SOLICITUD DE RESTABLECIMIENTO
                            </div>
                            <h2 style="margin: 0; font-size: 22px; font-weight: 900; color: #0b1d38; letter-spacing: -0.3px;">
                                ¿Olvidaste tu contraseña?
                            </h2>
                            <p style="margin: 12px auto 0 auto; max-width: 480px; font-size: 14px; color: #334155; line-height: 1.6;">
                                Hola <strong>{{ $user->nombre }} {{ $user->apellido }}</strong>, hemos recibido una solicitud para restablecer la contraseña asociada a tu cuenta (<strong>{{ $user->email }}</strong>).
                            </p>
                        </td>
                    </tr>

                    <!-- 3. BOTÓN DE ACCIÓN PRINCIPAL -->
                    <tr>
                        <td style="padding: 16px 32px 28px 32px; text-align: center;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto;">
                                <tr>
                                    <td align="center" style="background-color: #1e549e; border-radius: 12px; box-shadow: 0 4px 14px rgba(30, 84, 158, 0.35);">
                                        <a href="{{ $resetUrl }}" target="_blank" style="display: inline-block; padding: 14px 32px; font-size: 14px; font-weight: 800; color: #ffffff; text-decoration: none; border-radius: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Restablecer Mi Contraseña &rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin: 16px 0 0 0; font-size: 12px; color: #64748b;">
                                Este enlace es seguro y expirará en <strong>60 minutos</strong>.
                            </p>
                        </td>
                    </tr>

                    <!-- 4. ENLACE DIRECTO DE RESPALDO -->
                    <tr>
                        <td style="padding: 0 32px 24px 32px;">
                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 16px; font-size: 12px; color: #475569; word-break: break-all;">
                                <p style="margin: 0 0 6px 0; font-weight: 700; color: #1e293b;">
                                    ¿El botón no funciona? Copia y pega esta dirección en tu navegador:
                                </p>
                                <a href="{{ $resetUrl }}" style="color: #2563eb; text-decoration: underline; font-size: 11px;">
                                    {{ $resetUrl }}
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- 5. AVISO DE SEGURIDAD -->
                    <tr>
                        <td style="padding: 0 32px 28px 32px;">
                            <div style="border-left: 3px solid #f59e0b; background-color: #fffbeb; padding: 12px 14px; border-radius: 0 8px 8px 0; font-size: 12px; color: #92400e; line-height: 1.5;">
                                <strong>Importante:</strong> Si tú no solicitaste este cambio, no te preocupes. Puedes ignorar este correo de forma segura; nadie más tiene acceso a tu cuenta y tu contraseña actual seguirá funcionando.
                            </div>
                        </td>
                    </tr>

                    <!-- 6. PIE DE PÁGINA INSTITUCIONAL -->
                    <tr>
                        <td style="background-color: #0b1d38; padding: 24px 32px; text-align: center; border-top: 1px solid #1e293b; color: #94a3b8; font-size: 11px; line-height: 1.6;">
                            <p style="margin: 0 0 4px 0; font-weight: 700; color: #f1f5f9; text-transform: uppercase; letter-spacing: 0.5px;">
                                {{ $config->nombre_comercio ?? $config->nombre_empresa ?? 'Agrosuministros Rodríguez C.A.' }}
                            </p>
                            <p style="margin: 0 0 8px 0;">
                                RIF: {{ $config->rif ?? 'J-50493821-4' }} • {{ $config->direccion ?? 'Barinitas, Estado Barinas, Venezuela' }}
                            </p>
                            <p style="margin: 0; color: #64748b;">
                                Teléfono: {{ $config->telefono ?? '+58 412-9614615' }} • Correo: {{ $config->email ?? 'contacto@agrosuministros.com' }}
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
