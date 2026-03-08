<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    use HasFactory;

    // 1. Nombre exacto de la tabla en tu BD
    protected $table = 'movimientos_caja';

    // 2. Configuración de Timestamps personalizados (según tu SQL)
    const CREATED_AT = 'creado_at';
    const UPDATED_AT = null; // No hay columna de actualización en esta tabla

    // 3. Campos que se pueden llenar masivamente
    protected $fillable = [
        'sesion_caja_id',
        'tipo', // 'ingreso' o 'egreso'
        'motivo',
        'monto_usd',
        'monto_ves'
    ];

    // 4. Casteo para asegurar que los montos se manejen como decimales matemáticos
    protected $casts = [
        'monto_usd' => 'decimal:2',
        'monto_ves' => 'decimal:2',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================
    
    /**
     * Un movimiento pertenece a una sesión de caja específica.
     */
    public function sesion()
    {
        return $this->belongsTo(SesionCaja::class, 'sesion_caja_id');
    }
}