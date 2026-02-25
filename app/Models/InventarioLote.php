<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventarioLote extends Model
{
    protected $table = 'inventario_lotes';
    public $timestamps = false;

    protected $fillable = [
        'producto_id',
        'proveedor_id',
        'numero_lote',
        'fecha_vencimiento',
        'cantidad_inicial',
        'cantidad_restante',
        'costo_unitario_usd',
        'ubicacion_almacen',
        'activo'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'activo' => 'boolean',
        'cantidad_inicial' => 'decimal:3',
        'cantidad_restante' => 'decimal:3',
        'costo_unitario_usd' => 'decimal:4'
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    /**
     * Verifica si el lote está próximo a vencer (30 días o menos)
     */
    public function getProximoVencerAttribute(): bool
    {
        return $this->fecha_vencimiento <= now()->addDays(30);
    }

    /**
     * Días restantes para el vencimiento
     */
    public function getDiasRestantesAttribute(): int
    {
        return now()->diffInDays($this->fecha_vencimiento, false);
    }
}