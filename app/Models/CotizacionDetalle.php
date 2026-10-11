<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CotizacionDetalle extends Model
{
    protected $table = 'cotizacion_detalles';
    
    const CREATED_AT = 'creado_at';
    const UPDATED_AT = null; // No updated_at column

    protected $fillable = [
        'cotizacion_id',
        'producto_id',
        'descripcion',
        'cantidad',
        'unidad_medida',
        'precio_unitario_usd',
        'total_usd',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario_usd' => 'decimal:2',
        'total_usd' => 'decimal:2',
        'creado_at' => 'datetime',
    ];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class, 'cotizacion_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
