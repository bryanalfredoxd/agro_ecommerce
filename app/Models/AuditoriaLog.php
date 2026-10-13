<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Carbon\Carbon;

class AuditoriaLog extends Model
{
    protected $table = 'auditoria_logs';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'evento',
        'descripcion',
        'tabla_afectada',
        'registro_id',
        'valores_anteriores',
        'valores_nuevos',
        'direccion_ip',
        'url',
        'metodo_http',
        'user_agent',
        'fecha'
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    /**
     * Usuario que ejecutó la acción auditada
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Helper centralizado para registrar cualquier evento de auditoría de forma manual o programática.
     */
    public static function registrar(
        string $evento,
        string $tabla,
        ?int $registroId = null,
        array|string|null $valoresAnteriores = null,
        array|string|null $valoresNuevos = null,
        ?string $descripcion = null,
        ?int $usuarioId = null
    ): self {
        $ip = Request::ip() ?? '127.0.0.1';
        $url = Request::fullUrl() ?? '';
        $metodo = Request::method() ?? 'CLI';
        $agent = Request::userAgent() ?? 'Sistema / Consola';
        $userId = $usuarioId ?? (Auth::check() ? Auth::id() : null);

        // Convertir arrays a JSON legible
        $valAntJson = is_array($valoresAnteriores) 
            ? json_encode($valoresAnteriores, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) 
            : $valoresAnteriores;

        $valNueJson = is_array($valoresNuevos) 
            ? json_encode($valoresNuevos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) 
            : $valoresNuevos;

        return self::create([
            'usuario_id' => $userId,
            'evento' => strtoupper(trim($evento)),
            'descripcion' => $descripcion,
            'tabla_afectada' => strtolower(trim($tabla)),
            'registro_id' => $registroId,
            'valores_anteriores' => $valAntJson,
            'valores_nuevos' => $valNueJson,
            'direccion_ip' => $ip,
            'url' => substr($url, 0, 500),
            'metodo_http' => substr($metodo, 0, 10),
            'user_agent' => $agent,
            'fecha' => Carbon::now()
        ]);
    }

    /**
     * Retorna los valores anteriores deserializados
     */
    public function getValoresAnterioresArrayAttribute(): ?array
    {
        if (empty($this->valores_anteriores)) return null;
        $decoded = json_decode($this->valores_anteriores, true);
        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Retorna los valores nuevos deserializados
     */
    public function getValoresNuevosArrayAttribute(): ?array
    {
        if (empty($this->valores_nuevos)) return null;
        $decoded = json_decode($this->valores_nuevos, true);
        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Nombre amigable del módulo basado en la tabla afectada
     */
    public function getModuloNombreAttribute(): string
    {
        return match($this->tabla_afectada) {
            'pedidos', 'pedido_detalles' => 'Ventas y Pedidos',
            'pagos' => 'Auditoría de Pagos',
            'facturas', 'factura_ajustes' => 'Facturación Fiscal',
            'devoluciones', 'devoluciones_detalle' => 'Devoluciones y Reembolsos',
            'productos', 'producto_imagenes', 'productos_compuestos' => 'Catálogo de Productos',
            'inventario_lotes' => 'Inventario y Lotes',
            'categorias' => 'Categorías',
            'marcas' => 'Marcas',
            'usuarios', 'direcciones_usuarios' => 'Usuarios y Clientes',
            'roles', 'rol_permisos', 'permisos_extra_usuario' => 'Roles y Permisos (RBAC)',
            'sesiones_caja', 'movimientos_caja', 'cajas_fisicas' => 'Caja Diaria y Turnos',
            'configuracion_tienda', 'configuracion_api' => 'Configuración de Tienda',
            'tasas_cambio' => 'Tasas de Cambio (BCV)',
            'cuentas_bancarias' => 'Cuentas Bancarias',
            'zonas_delivery' => 'Zonas de Delivery',
            'horarios_semanales', 'horarios_especiales' => 'Horarios de Atención',
            'cotizaciones', 'cotizacion_detalles' => 'Cotizaciones y Presupuestos',
            'solicitudes_productos' => 'Solicitudes de Insumos',
            'historico_precios_productos' => 'Histórico de Precios',
            default => ucfirst(str_replace('_', ' ', $this->tabla_afectada ?? 'Sistema'))
        };
    }

    /**
     * Color de la insignia según el tipo de evento
     */
    public function getBadgeColorAttribute(): string
    {
        $ev = strtoupper($this->evento ?? '');
        if (str_contains($ev, 'CREAR') || str_contains($ev, 'INSERT') || str_contains($ev, 'NUEVO') || str_contains($ev, 'REGISTRO')) {
            return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        }
        if (str_contains($ev, 'ELIMIN') || str_contains($ev, 'DELETE') || str_contains($ev, 'ANULAR') || str_contains($ev, 'RECHAZ')) {
            return 'bg-rose-50 text-rose-700 border-rose-200';
        }
        if (str_contains($ev, 'ROL') || str_contains($ev, 'PERMISO') || str_contains($ev, 'AUTH') || str_contains($ev, 'SEGURIDAD')) {
            return 'bg-purple-50 text-purple-700 border-purple-200';
        }
        if (str_contains($ev, 'PAGO') || str_contains($ev, 'CAJA') || str_contains($ev, 'VENTA') || str_contains($ev, 'DEVOLUCION')) {
            return 'bg-teal-50 text-teal-700 border-teal-200';
        }
        if (str_contains($ev, 'ESTADO') || str_contains($ev, 'DESPACHO') || str_contains($ev, 'RUTA')) {
            return 'bg-orange-50 text-orange-700 border-orange-200';
        }
        return 'bg-blue-50 text-blue-700 border-blue-200';
    }

    /**
     * Ícono según el tipo de evento
     */
    public function getIconoAttribute(): string
    {
        $ev = strtoupper($this->evento ?? '');
        if (str_contains($ev, 'CREAR') || str_contains($ev, 'INSERT')) return 'add_circle';
        if (str_contains($ev, 'ELIMIN') || str_contains($ev, 'DELETE')) return 'delete';
        if (str_contains($ev, 'ANULAR')) return 'cancel';
        if (str_contains($ev, 'APROBAR')) return 'verified';
        if (str_contains($ev, 'RECHAZ')) return 'block';
        if (str_contains($ev, 'ROL') || str_contains($ev, 'PERMISO')) return 'admin_panel_settings';
        if (str_contains($ev, 'LOGIN')) return 'login';
        if (str_contains($ev, 'LOGOUT')) return 'logout';
        if (str_contains($ev, 'CAJA')) return 'point_of_sale';
        if (str_contains($ev, 'TASA') || str_contains($ev, 'BCV')) return 'currency_exchange';
        if (str_contains($ev, 'ESTADO')) return 'sync_alt';
        if (str_contains($ev, 'PRECIO')) return 'price_change';
        if (str_contains($ev, 'STOCK') || str_contains($ev, 'INVENTARIO')) return 'inventory_2';
        return 'edit_note';
    }

    /**
     * Nombre amigable y entendible de la acción (sin tecnicismos de programación)
     */
    public function getEventoAmigableAttribute(): string
    {
        $ev = strtoupper($this->evento ?? '');

        // Casos comunes directos
        return match($ev) {
            'MODIFICAR_PRECIO_PRODUCTO' => 'Cambio de Precio',
            'AJUSTE_STOCK_PRODUCTO' => 'Ajuste de Inventario',
            'CAMBIO_ESTADO_PEDIDO' => 'Cambio de Estado',
            'APROBAR_PAGO' => 'Pago Aprobado',
            'RECHAZAR_PAGO' => 'Pago Rechazado',
            'ANULAR_FACTURA' => 'Factura Anulada',
            'INICIO_SESION' => 'Inicio de Sesión',
            'CIERRE_SESION' => 'Cierre de Sesión',
            'CREAR_PRODUCTOS' => 'Nuevo Producto Creado',
            'ACTUALIZAR_PRODUCTOS' => 'Producto Modificado',
            'ELIMINAR_PRODUCTOS' => 'Producto Eliminado',
            'CREAR_CATEGORIAS' => 'Nueva Categoría Creada',
            'ACTUALIZAR_CATEGORIAS' => 'Categoría Modificada',
            'ELIMINAR_CATEGORIAS' => 'Categoría Eliminada',
            'CREAR_MARCAS' => 'Nueva Marca Creada',
            'ACTUALIZAR_MARCAS' => 'Marca Modificada',
            'ELIMINAR_MARCAS' => 'Marca Eliminada',
            'CREAR_PEDIDOS' => 'Nuevo Pedido Registrado',
            'ACTUALIZAR_PEDIDOS' => 'Pedido Actualizado',
            'ELIMINAR_PEDIDOS' => 'Pedido Eliminado',
            'CREAR_SESIONES_CAJA' => 'Apertura de Caja',
            'ACTUALIZAR_SESIONES_CAJA' => 'Cierre o Arqueo de Caja',
            'CREAR_MOVIMIENTOS_CAJA' => 'Movimiento de Dinero',
            'ACTUALIZAR_TASAS_CAMBIO' => 'Actualización de Tasa BCV',
            'CREAR_TASAS_CAMBIO' => 'Registro de Tasa BCV',
            'ACTUALIZAR_CONFIGURACION_TIENDA' => 'Cambio en Ajustes de Empresa',
            'ASIGNACION_PERMISOS_USUARIO' => 'Cambio de Permisos',
            'ACTUALIZAR_ROLES' => 'Rol Modificado',
            default => (function() use ($ev) {
                if (str_starts_with($ev, 'CREAR_')) {
                    return 'Creó ' . ucfirst(strtolower(str_replace('CREAR_', '', $ev)));
                }
                if (str_starts_with($ev, 'ACTUALIZAR_')) {
                    return 'Modificó ' . ucfirst(strtolower(str_replace('ACTUALIZAR_', '', $ev)));
                }
                if (str_starts_with($ev, 'ELIMINAR_')) {
                    return 'Eliminó ' . ucfirst(strtolower(str_replace('ELIMINAR_', '', $ev)));
                }
                return ucwords(strtolower(str_replace('_', ' ', $ev)));
            })()
        };
    }

    /**
     * Identificación amigable del dispositivo (computadora, teléfono, etc.)
     */
    public function getDispositivoAmigableAttribute(): string
    {
        $ua = $this->user_agent ?? '';
        if (empty($ua) || str_contains($ua, 'CLI') || str_contains($ua, 'Sistema') || str_contains($ua, 'Consola')) {
            return 'Acción interna del sistema';
        }

        $sistema = 'Dispositivo desconocido';
        if (str_contains($ua, 'Windows')) $sistema = 'Computadora con Windows';
        elseif (str_contains($ua, 'Macintosh') || str_contains($ua, 'Mac OS')) $sistema = 'Computadora Apple Mac';
        elseif (str_contains($ua, 'iPhone')) $sistema = 'Teléfono iPhone (Apple)';
        elseif (str_contains($ua, 'iPad')) $sistema = 'Tablet iPad (Apple)';
        elseif (str_contains($ua, 'Android')) $sistema = 'Teléfono / Tablet Android';
        elseif (str_contains($ua, 'Linux')) $sistema = 'Computadora con Linux';

        $navegador = '';
        if (str_contains($ua, 'Edg')) $navegador = ' (Edge)';
        elseif (str_contains($ua, 'Chrome')) $navegador = ' (Google Chrome)';
        elseif (str_contains($ua, 'Firefox')) $navegador = ' (Mozilla Firefox)';
        elseif (str_contains($ua, 'Safari')) $navegador = ' (Safari)';

        return $sistema . $navegador;
    }

    /**
     * Nombre amigable de la pantalla donde ocurrió la acción
     */
    public function getPantallaAmigableAttribute(): string
    {
        $url = $this->url ?? '';
        if (str_contains($url, '/admin/pos')) return 'Punto de Venta / Caja (Terminal POS)';
        if (str_contains($url, '/admin/productos')) return 'Catálogo de Productos';
        if (str_contains($url, '/admin/pedidos')) return 'Gestión de Pedidos y Ventas';
        if (str_contains($url, '/admin/pagos')) return 'Verificación de Pagos';
        if (str_contains($url, '/admin/facturas')) return 'Facturas y Comprobantes';
        if (str_contains($url, '/admin/devoluciones')) return 'Devoluciones y Reembolsos';
        if (str_contains($url, '/admin/caja-diaria')) return 'Arqueo de Caja y Turnos';
        if (str_contains($url, '/admin/tasas-cambio')) return 'Tasas de Cambio (BCV)';
        if (str_contains($url, '/admin/configuracion-tienda')) return 'Datos de la Empresa y Ajustes';
        if (str_contains($url, '/admin/cuentas-bancarias')) return 'Cuentas Bancarias';
        if (str_contains($url, '/admin/zonas-delivery')) return 'Zonas de Delivery (Mapa)';
        if (str_contains($url, '/admin/horarios')) return 'Horarios de Atención';
        if (str_contains($url, '/admin/usuarios')) return 'Administración de Usuarios';
        if (str_contains($url, '/admin/roles')) return 'Roles y Permisos de Acceso';
        if (str_contains($url, '/checkout')) return 'Tienda Virtual (Compra de Cliente)';
        if (str_contains($url, '/login')) return 'Inicio de Sesión';

        return 'Módulo de ' . $this->modulo_nombre;
    }

    /**
     * Diccionario de traducción de nombres de campos técnicos a español claro
     */
    public static function traducirCampo(string $campo): string
    {
        $map = [
            'precio_venta_usd' => 'Precio de Venta ($)',
            'precio_oferta_usd' => 'Precio de Oferta ($)',
            'costo_promedio_usd' => 'Costo Promedio del Producto ($)',
            'precio_incluye_iva' => '¿Precio incluye IVA?',
            'stock_total' => 'Cantidad en Inventario (Stock)',
            'stock_minimo_alerta' => 'Alerta de Stock Mínimo',
            'venta_minima' => 'Compra Mínima Permitida',
            'paso_venta' => 'Incremento de Venta',
            'nombre' => 'Nombre',
            'apellido' => 'Apellido',
            'descripcion' => 'Descripción',
            'email' => 'Correo Electrónico',
            'telefono' => 'Número de Teléfono',
            'cedula_rif' => 'Cédula o RIF',
            'estado' => 'Estado',
            'metodo_pago' => 'Forma de Pago',
            'monto_total' => 'Monto Total ($)',
            'monto_pagado' => 'Monto Pagado',
            'saldo_pendiente' => 'Saldo Pendiente',
            'monto_usd' => 'Monto en Dólares ($)',
            'monto_bs' => 'Monto en Bolívares (Bs)',
            'tasa_bcv' => 'Tasa de Cambio Oficial (BCV)',
            'referencia' => 'Número de Referencia Bancaria',
            'banco_origen' => 'Banco del Cliente',
            'banco_destino' => 'Banco de la Empresa',
            'numero_factura' => 'Número de Factura',
            'numero_control' => 'Número de Control Fiscal',
            'activo' => '¿Habilitado / Activo en el Sistema?',
            'destacado' => '¿Destacado en Portada de la Tienda?',
            'es_controlado' => '¿Es Producto Regulado / Controlado?',
            'es_combo' => '¿Es un Combo?',
            'categoria_id' => 'Categoría Asignada',
            'marca_id' => 'Marca Asignada',
            'rol_id' => 'Rol / Cargo del Usuario',
            'monto_apertura_usd' => 'Monto de Apertura de Caja ($)',
            'monto_apertura_bs' => 'Monto de Apertura de Caja (Bs)',
            'monto_cierre_usd' => 'Monto de Cierre de Caja ($)',
            'monto_cierre_bs' => 'Monto de Cierre de Caja (Bs)',
            'diferencia_usd' => 'Diferencia en Caja ($)',
            'diferencia_bs' => 'Diferencia en Caja (Bs)',
            'motivo' => 'Motivo / Explicación',
            'observaciones' => 'Notas u Observaciones',
            'direccion' => 'Dirección',
            'sku' => 'Código de Producto (SKU)',
            'codigo_barras' => 'Código de Barras',
            'unidad_medida' => 'Unidad de Medida',
            'contenido_neto' => 'Contenido Neto',
            'tipo' => 'Tipo',
            'tipo_movimiento' => 'Tipo de Movimiento',
        ];

        return $map[$campo] ?? ucwords(str_replace('_', ' ', $campo));
    }

    /**
     * Formatea un valor técnico a un texto amigable y comprensible
     */
    public static function formatearValor(string $campo, mixed $valor): string
    {
        if ($valor === null) return '(Sin asignar / Vacío)';
        if ($valor === true || $valor === 1 || $valor === '1') {
            if (str_contains($campo, 'activo') || str_contains($campo, 'destacado') || str_contains($campo, 'iva') || str_contains($campo, 'es_')) {
                return 'Sí';
            }
        }
        if ($valor === false || $valor === 0 || $valor === '0') {
            if (str_contains($campo, 'activo') || str_contains($campo, 'destacado') || str_contains($campo, 'iva') || str_contains($campo, 'es_')) {
                return 'No';
            }
        }

        // Estados de pedidos y procesos
        $valorStr = (string)$valor;
        $estados = [
            'pendiente' => 'Pendiente por cobrar o verificar',
            'en_preparacion' => 'En preparación (armando pedido)',
            'en_ruta' => 'En camino con el repartidor (Delivery)',
            'completado_caja' => 'Cobrado y entregado en caja',
            'completado' => 'Completado con éxito',
            'cancelado' => 'Cancelado',
            'anulado' => 'Anulado',
            'abierta' => 'Abierta (Turno en curso)',
            'cerrada' => 'Cerrada (Turno finalizado)',
            'pago_movil' => 'Pago Móvil',
            'transferencia' => 'Transferencia Bancaria',
            'efectivo_usd' => 'Efectivo en Dólares ($)',
            'efectivo_bs' => 'Efectivo en Bolívares (Bs)',
            'punto_venta' => 'Punto de Venta',
            'zelle' => 'Zelle',
            'ingreso' => 'Ingreso de dinero',
            'egreso' => 'Retiro / Salida de dinero'
        ];

        if (isset($estados[$valorStr])) {
            return $estados[$valorStr];
        }

        // Monedas / Precios
        if (is_numeric($valorStr)) {
            if (str_contains($campo, 'precio') || str_contains($campo, 'costo') || str_contains($campo, 'usd') || str_contains($campo, 'total') || str_contains($campo, 'pagado') || str_contains($campo, 'saldo')) {
                return '$' . number_format((float)$valor, 2, '.', ',');
            }
            if (str_contains($campo, 'bs')) {
                return 'Bs. ' . number_format((float)$valor, 2, '.', ',');
            }
        }

        return $valorStr;
    }

    /**
     * Lista amigable de cambios para mostrar en la interfaz sin tecnicismos
     */
    public function getListaCambiosAmigablesAttribute(): array
    {
        $anteriores = $this->valores_anteriores_array ?? [];
        $nuevos = $this->valores_nuevos_array ?? [];

        $cambios = [];
        $todosCampos = array_unique(array_merge(array_keys($anteriores), array_keys($nuevos)));

        foreach ($todosCampos as $campo) {
            // Omitir campos técnicos irrelevantes para personas no técnicas
            if (in_array($campo, ['id', 'created_at', 'updated_at', 'eliminado_at', 'deleted_at', 'actualizado_at'])) {
                continue;
            }

            $tieneAnt = array_key_exists($campo, $anteriores);
            $tieneNue = array_key_exists($campo, $nuevos);

            $valAnt = $tieneAnt ? $anteriores[$campo] : null;
            $valNue = $tieneNue ? $nuevos[$campo] : null;

            // Determinar tipo
            $tipo = 'modificado';
            if (!$tieneAnt && $tieneNue) $tipo = 'nuevo';
            if ($tieneAnt && !$tieneNue) $tipo = 'eliminado';

            $cambios[] = [
                'campo' => $campo,
                'nombre' => self::traducirCampo($campo),
                'antes' => $tieneAnt ? self::formatearValor($campo, $valAnt) : null,
                'despues' => $tieneNue ? self::formatearValor($campo, $valNue) : null,
                'tipo' => $tipo
            ];
        }

        return $cambios;
    }
}