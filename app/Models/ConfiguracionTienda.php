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
}