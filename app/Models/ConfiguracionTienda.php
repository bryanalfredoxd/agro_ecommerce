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
        'rif',
        'slogan',
        'descripcion_empresa',
        'telefono',
        'whatsapp',
        'email',
        'direccion',
        'horario_atencion',
        'red_facebook',
        'red_instagram',
        'red_tiktok',
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
    // ACCESORES DE COMPATIBILIDAD
    // ==========================================

    public function getNombreComercioAttribute(): string
    {
        return $this->nombre_empresa ?: 'Agrosuministros Rodríguez C.A.';
    }

    public function getDireccionFisicaAttribute(): string
    {
        return $this->direccion ?: 'Barinitas, Estado Barinas, Venezuela';
    }

    public function getTelefonoContactoAttribute(): string
    {
        return $this->telefono ?: '+58 412-9614615';
    }

    public function getEmailContactoAttribute(): string
    {
        return $this->email ?: 'fitnessbigworld@gmail.com';
    }

    public function getHorarioAtencionAttribute($value): string
    {
        return \App\Models\HorarioSemanal::obtenerResumenSemanal();
    }

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
        return $config && !empty($config->nombre_empresa) ? $config->nombre_empresa : 'Agrosuministros Rodríguez C.A.';
    }

    public static function rif(): string
    {
        $config = static::first();
        return $config && !empty($config->rif) ? $config->rif : 'J-50493821-4';
    }

    public static function slogan(): string
    {
        $config = static::first();
        return $config && !empty($config->slogan) ? $config->slogan : 'Insumos & Soluciones para el Campo';
    }

    public static function descripcion(): string
    {
        $config = static::first();
        return $config && !empty($config->descripcion_empresa) 
            ? $config->descripcion_empresa 
            : 'Tu aliado estratégico en el desarrollo agrícola y pecuario. Ofrecemos fertilizantes, semillas certificadas, implementos y farmacología veterinaria con entrega confiable a nivel regional y nacional.';
    }

    public static function telefono(): string
    {
        $config = static::first();
        return $config && !empty($config->telefono) ? $config->telefono : '+58 412-9614615';
    }

    public static function whatsapp(): string
    {
        $config = static::first();
        return $config && !empty($config->whatsapp) ? $config->whatsapp : '+58 412-9614615';
    }

    /**
     * Retorna solo dígitos limpios con código de país para usar en URLs wa.me
     * Ejemplo: "+58 412-9614615" o "0412-9614615" -> "584129614615"
     */
    public static function whatsappNumeroLimpio(): string
    {
        $numero = static::whatsapp();
        $soloDigitos = preg_replace('/[^0-9]/', '', $numero);
        if (str_starts_with($soloDigitos, '0')) {
            $soloDigitos = '58' . substr($soloDigitos, 1);
        }
        return $soloDigitos ?: '584129614615';
    }

    public static function whatsappUrl(?string $mensaje = null): string
    {
        $numero = static::whatsappNumeroLimpio();
        if ($mensaje) {
            return "https://wa.me/{$numero}?text=" . urlencode($mensaje);
        }
        return "https://wa.me/{$numero}";
    }

    public static function email(): string
    {
        $config = static::first();
        return $config && !empty($config->email) ? $config->email : 'fitnessbigworld@gmail.com';
    }

    public static function direccion(): string
    {
        $config = static::first();
        return $config && !empty($config->direccion) ? $config->direccion : 'Barinitas, Estado Barinas, Venezuela';
    }

    public static function horario(): string
    {
        return \App\Models\HorarioSemanal::obtenerResumenSemanal();
    }

    public static function facebook(): ?string
    {
        $config = static::first();
        return $config && !empty($config->red_facebook) ? $config->red_facebook : 'https://facebook.com';
    }

    public static function instagram(): ?string
    {
        $config = static::first();
        return $config && !empty($config->red_instagram) ? $config->red_instagram : 'https://instagram.com';
    }

    public static function tiktok(): ?string
    {
        $config = static::first();
        return $config && !empty($config->red_tiktok) ? $config->red_tiktok : null;
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