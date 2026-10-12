<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionTienda extends Model
{
    use HasFactory;

    // 1. Nombre de la tabla
    protected $table = 'configuracion_tienda';

    // 2. Desactivamos los timestamps automáticos de Laravel. 
    // La base de datos ya se encarga de actualizar 'actualizado_at' automáticamente.
    public $timestamps = false;

    // 3. Campos que se pueden llenar masivamente
    protected $fillable = [
        'nombre_empresa',
        'iva_porcentaje',
        'recargo_divisas_pct',
        'recargo_divisas_aplica',
        'recargo_bolivares_pct',
        'recargo_bolivares_aplica',
        'recargo_cashea_pct',
        'recargo_cashea_aplica',
        'modo_operativo',
        'mensaje_cierre_emergencia',
        'latitud',
        'longitud',
        'delivery_activo',
        'ultimo_editor_id'
    ];

    // 4. Casteos para asegurar tipos de datos correctos
    protected $casts = [
        'iva_porcentaje' => 'decimal:2',
        'recargo_divisas_pct' => 'decimal:2',
        'recargo_bolivares_pct' => 'decimal:2',
        'recargo_cashea_pct' => 'decimal:2',
        'delivery_activo' => 'boolean',
        'latitud' => 'float',
        'longitud' => 'float',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================
    
    /**
     * Relación para saber qué administrador modificó la configuración por última vez
     */
    public function ultimoEditor()
    {
        return $this->belongsTo(User::class, 'ultimo_editor_id');
    }

    public static function obtener(): ?self
    {
        return static::first();
    }

    public static function iva(): float
    {
        $config = static::first();
        return $config ? (float) $config->iva_porcentaje : 16.00;
    }

    public static function nombre(): string
    {
        $config = static::first();
        return $config && !empty($config->nombre_empresa) ? $config->nombre_empresa : 'Agrosuministros Rodríguez';
    }

    public static function deliveryHabilitado(): bool
    {
        $config = static::first();
        return $config ? (bool) ($config->delivery_activo ?? true) : true;
    }

    /**
     * Retorna el porcentaje de recargo aplicable a una modalidad ('divisas', 'bolivares', 'cashea')
     * evaluando si su ámbito cubre el canal especificado ('web', 'pos').
     */
    public static function recargoPorcentaje(string $modalidad, string $canal = 'web'): float
    {
        $config = static::first();
        if (!$config) return 0.00;

        $modalidad = strtolower(trim($modalidad));
        if (!in_array($modalidad, ['divisas', 'bolivares', 'cashea'])) {
            $modalidad = 'divisas';
        }

        $campoPct = "recargo_{$modalidad}_pct";
        $campoAplica = "recargo_{$modalidad}_aplica";

        $pct = (float) ($config->{$campoPct} ?? 0.00);
        $aplica = (string) ($config->{$campoAplica} ?? 'ambas');

        // Si aplica a 'ambas' o coincide con el canal consultado ('web' o 'pos')
        if ($aplica === 'ambas' || $aplica === $canal) {
            return max(0.00, $pct);
        }

        return 0.00;
    }

    /**
     * Retorna un mapa con los recargos configurados para un canal ('web' o 'pos').
     */
    public static function recargos(string $canal = 'web'): array
    {
        $config = static::first();
        return [
            'divisas' => [
                'porcentaje' => static::recargoPorcentaje('divisas', $canal),
                'porcentaje_raw' => $config ? (float)($config->recargo_divisas_pct ?? 0) : 0,
                'aplica' => $config ? ($config->recargo_divisas_aplica ?? 'ambas') : 'ambas',
                'activo' => static::recargoPorcentaje('divisas', $canal) > 0,
            ],
            'bolivares' => [
                'porcentaje' => static::recargoPorcentaje('bolivares', $canal),
                'porcentaje_raw' => $config ? (float)($config->recargo_bolivares_pct ?? 0) : 0,
                'aplica' => $config ? ($config->recargo_bolivares_aplica ?? 'ambas') : 'ambas',
                'activo' => static::recargoPorcentaje('bolivares', $canal) > 0,
            ],
            'cashea' => [
                'porcentaje' => static::recargoPorcentaje('cashea', $canal),
                'porcentaje_raw' => $config ? (float)($config->recargo_cashea_pct ?? 0) : 0,
                'aplica' => $config ? ($config->recargo_cashea_aplica ?? 'ambas') : 'ambas',
                'activo' => static::recargoPorcentaje('cashea', $canal) > 0,
            ],
        ];
    }
}