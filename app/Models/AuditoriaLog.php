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
}