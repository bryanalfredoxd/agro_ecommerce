<?php

namespace App\Observers;

use App\Models\AuditoriaLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class AuditObserver
{
    /**
     * Campos que nunca deben guardarse en los logs por razones de privacidad y seguridad
     */
    protected array $sensitiveFields = [
        'password',
        'password_hash',
        'remember_token',
        'token',
        'api_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Campos irrelevantes para auditoría de negocio si solo ellos cambiaron
     */
    protected array $ignoredFields = [
        'actualizado_at',
        'updated_at',
    ];

    /**
     * Maneja el evento Eloquent "created".
     */
    public function created(Model $model): void
    {
        try {
            $table = $model->getTable();
            if ($table === 'auditoria_logs' || $table === 'sessions' || $table === 'cache') {
                return;
            }

            $newValues = $this->filterAttributes($model->getAttributes());
            $descripcion = $this->generarDescripcionCreacion($model);
            $evento = 'CREAR_' . strtoupper($table);

            AuditoriaLog::registrar(
                evento: $evento,
                tabla: $table,
                registroId: $model->getKey(),
                valoresAnteriores: null,
                valoresNuevos: $newValues,
                descripcion: $descripcion
            );
        } catch (\Throwable $e) {
            Log::warning("Error registrando auditoría de creación en {$model->getTable()}: " . $e->getMessage());
        }
    }

    /**
     * Maneja el evento Eloquent "updated".
     */
    public function updated(Model $model): void
    {
        try {
            $table = $model->getTable();
            if ($table === 'auditoria_logs' || $table === 'sessions' || $table === 'cache') {
                return;
            }

            $changes = $this->filterAttributes($model->getChanges());

            // Ignorar cambios si solo cambió la fecha de actualización
            $keysChanged = array_diff(array_keys($changes), $this->ignoredFields);
            if (empty($keysChanged)) {
                return;
            }

            // Obtener únicamente los valores originales de las columnas que cambiaron
            $original = [];
            foreach ($keysChanged as $key) {
                $original[$key] = $model->getOriginal($key);
            }
            $cleanChanges = array_intersect_key($changes, array_flip($keysChanged));

            $evento = 'ACTUALIZAR_' . strtoupper($table);
            $descripcion = $this->generarDescripcionActualizacion($model, $original, $cleanChanges, $evento);

            AuditoriaLog::registrar(
                evento: $evento,
                tabla: $table,
                registroId: $model->getKey(),
                valoresAnteriores: $original,
                valoresNuevos: $cleanChanges,
                descripcion: $descripcion
            );
        } catch (\Throwable $e) {
            Log::warning("Error registrando auditoría de actualización en {$model->getTable()}: " . $e->getMessage());
        }
    }

    /**
     * Maneja el evento Eloquent "deleted".
     */
    public function deleted(Model $model): void
    {
        try {
            $table = $model->getTable();
            if ($table === 'auditoria_logs' || $table === 'sessions' || $table === 'cache') {
                return;
            }

            $oldValues = $this->filterAttributes($model->getAttributes());
            $descripcion = $this->generarDescripcionEliminacion($model);
            $evento = 'ELIMINAR_' . strtoupper($table);

            AuditoriaLog::registrar(
                evento: $evento,
                tabla: $table,
                registroId: $model->getKey(),
                valoresAnteriores: $oldValues,
                valoresNuevos: null,
                descripcion: $descripcion
            );
        } catch (\Throwable $e) {
            Log::warning("Error registrando auditoría de eliminación en {$model->getTable()}: " . $e->getMessage());
        }
    }

    /**
     * Filtra atributos confidenciales
     */
    protected function filterAttributes(array $attributes): array
    {
        foreach ($this->sensitiveFields as $field) {
            if (array_key_exists($field, $attributes)) {
                $attributes[$field] = '******** (Oculto)';
            }
        }
        return $attributes;
    }

    /**
     * Genera una descripción legible al crear registros
     */
    protected function generarDescripcionCreacion(Model $model): string
    {
        $table = $model->getTable();
        return match ($table) {
            'pedidos' => "Se registró el pedido #{$model->id} por monto de \${$model->total_usd} ({$model->canal_venta})",
            'pagos' => "Se reportó un pago por \${$model->monto_usd} (Método: " . ucfirst(str_replace('_', ' ', $model->metodo ?? 'N/A')) . ", Ref: " . ($model->referencia_bancaria ?? 'S/R') . ")",
            'facturas' => "Se emitió la factura fiscal #{$model->numero_factura} para el pedido #{$model->pedido_id}",
            'devoluciones' => "Se procesó la devolución {$model->codigo_devolucion} por \${$model->monto_total_usd}",
            'productos' => "Se agregó al catálogo el producto \"{$model->nombre}\" (Precio: \${$model->precio_venta_usd}, Stock: {$model->stock_total})",
            'categorias' => "Se creó la categoría de productos \"{$model->nombre}\"",
            'marcas' => "Se registró la marca \"{$model->nombre}\"",
            'usuarios' => "Se registró el usuario {$model->nombre} {$model->apellido} ({$model->email})",
            'roles' => "Se creó el rol de seguridad \"{$model->nombre}\"",
            'sesiones_caja' => "Se abrió un turno de caja (#{$model->id}) con monto inicial de \${$model->monto_apertura_usd}",
            'movimientos_caja' => "Movimiento de caja ({$model->tipo}): {$model->concepto} por \${$model->monto_usd}",
            'zonas_delivery' => "Se configuró la zona de delivery \"{$model->nombre}\" con tarifa de \${$model->precio_delivery_usd}",
            'cuentas_bancarias' => "Se añadió la cuenta bancaria \"{$model->nombre_titular}\" (" . ucfirst(str_replace('_', ' ', $model->tipo_metodo ?? '')) . ")",
            'cotizaciones' => "Se emitió la cotización #{$model->codigo_cotizacion} para cliente {$model->nombre_cliente}",
            'solicitudes_productos' => "Nueva solicitud de insumo: \"{$model->nombre_producto}\"",
            default => "Nuevo registro creado en la tabla " . ucfirst(str_replace('_', ' ', $table)) . " (#" . $model->getKey() . ")"
        };
    }

    /**
     * Genera una descripción legible al actualizar registros y contextualiza el evento
     */
    protected function generarDescripcionActualizacion(Model $model, array $original, array $changes, string &$evento): string
    {
        $table = $model->getTable();

        if ($table === 'pedidos') {
            if (isset($changes['estado'])) {
                $evento = 'CAMBIO_ESTADO_PEDIDO';
                return "Pedido #{$model->id}: Estado cambiado de '" . strtoupper($original['estado'] ?? 'N/A') . "' a '" . strtoupper($changes['estado']) . "'";
            }
            return "Pedido #{$model->id}: Actualización de datos generales (" . implode(', ', array_keys($changes)) . ")";
        }

        if ($table === 'pagos') {
            if (isset($changes['estado'])) {
                $estado = $changes['estado'];
                $evento = $estado === 'aprobado' ? 'APROBACION_PAGO' : ($estado === 'rechazado' ? 'RECHAZO_PAGO' : 'CAMBIO_ESTADO_PAGO');
                return "Pago #{$model->id} (Ref: " . ($model->referencia_bancaria ?? 'S/R') . "): Marcado como " . strtoupper($estado);
            }
            return "Pago #{$model->id}: Modificación de datos (" . implode(', ', array_keys($changes)) . ")";
        }

        if ($table === 'facturas') {
            if (isset($changes['estado']) && $changes['estado'] === 'anulada') {
                $evento = 'ANULACION_FACTURA';
                return "Factura fiscal #{$model->numero_factura} fue ANULADA en el sistema";
            }
            return "Factura fiscal #{$model->numero_factura} actualizada";
        }

        if ($table === 'productos') {
            if (isset($changes['precio_venta_usd'])) {
                $evento = 'MODIFICAR_PRECIO_PRODUCTO';
                return "Producto \"{$model->nombre}\": Precio actualizado de \${$original['precio_venta_usd']} a \${$changes['precio_venta_usd']}";
            }
            if (isset($changes['stock_total'])) {
                $evento = 'AJUSTE_STOCK_PRODUCTO';
                return "Producto \"{$model->nombre}\": Stock ajustado de {$original['stock_total']} a {$changes['stock_total']}";
            }
            if (isset($changes['destacado'])) {
                $evento = 'TOGGLE_DESTACADO_PRODUCTO';
                return "Producto \"{$model->nombre}\": " . ($changes['destacado'] ? 'Marcado como Destacado' : 'Desmarcado de Destacados');
            }
            return "Producto \"{$model->nombre}\": Datos actualizados (" . implode(', ', array_keys($changes)) . ")";
        }

        if ($table === 'usuarios') {
            if (isset($changes['rol_id'])) {
                $evento = 'CAMBIO_ROL_USUARIO';
                return "Usuario {$model->nombre} {$model->apellido}: Rol asignado modificado a Rol #{$changes['rol_id']}";
            }
            if (isset($changes['activo'])) {
                $evento = $changes['activo'] ? 'REACTIVAR_USUARIO' : 'SUSPENDER_USUARIO';
                return "Usuario {$model->nombre} {$model->apellido}: Cuenta " . ($changes['activo'] ? 'reactivada' : 'suspendida');
            }
            return "Usuario {$model->nombre} {$model->apellido}: Perfil actualizado (" . implode(', ', array_keys($changes)) . ")";
        }

        if ($table === 'roles') {
            $evento = 'MODIFICAR_ROL';
            return "Rol \"{$model->nombre}\": Nombre o configuración modificada";
        }

        if ($table === 'tasas_cambio') {
            $evento = 'ACTUALIZAR_TASA_BCV';
            return "Tasa de cambio {$model->moneda_origen}/{$model->moneda_destino}: Actualizada de Bs. " . ($original['valor_tasa'] ?? '0') . " a Bs. {$changes['valor_tasa']}";
        }

        if ($table === 'configuracion_tienda') {
            $evento = 'ACTUALIZAR_CONFIGURACION_TIENDA';
            if (isset($changes['delivery_activo'])) {
                return "Servicio de Delivery " . ($changes['delivery_activo'] ? 'HABILITADO' : 'PAUSADO') . " en la tienda";
            }
            return "Parámetros globales de la tienda actualizados (" . implode(', ', array_keys($changes)) . ")";
        }

        if ($table === 'sesiones_caja') {
            if (isset($changes['estado_cierre'])) {
                $evento = 'CIERRE_CAJA';
                return "Cierre de turno de caja #{$model->id}: Efectivo final \${$model->monto_cierre_usd}";
            }
        }

        return "Modificación en " . ucfirst(str_replace('_', ' ', $table)) . " (#" . $model->getKey() . "): Campos modificados [" . implode(', ', array_keys($changes)) . "]";
    }

    /**
     * Genera una descripción legible al eliminar registros
     */
    protected function generarDescripcionEliminacion(Model $model): string
    {
        $table = $model->getTable();
        return match ($table) {
            'productos' => "Se eliminó del catálogo el producto \"{$model->nombre}\" (#{$model->id})",
            'categorias' => "Se eliminó la categoría \"{$model->nombre}\" (#{$model->id})",
            'marcas' => "Se eliminó la marca \"{$model->nombre}\" (#{$model->id})",
            'roles' => "Se eliminó el rol \"{$model->nombre}\" (#{$model->id})",
            'usuarios' => "Se eliminó la cuenta del usuario {$model->nombre} {$model->apellido} ({$model->email})",
            'cotizaciones' => "Se eliminó la cotización #{$model->id}",
            'zonas_delivery' => "Se eliminó la zona de delivery \"{$model->nombre}\"",
            'cuentas_bancarias' => "Se eliminó la cuenta bancaria \"{$model->nombre_titular}\"",
            default => "Se eliminó el registro #" . $model->getKey() . " de la tabla " . ucfirst(str_replace('_', ' ', $table))
        };
    }
}
