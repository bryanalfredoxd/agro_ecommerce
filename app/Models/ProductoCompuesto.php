<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductoCompuesto extends Model
{
    protected $table = 'productos_compuestos';
    public $timestamps = false;

    protected $fillable = [
        'producto_padre_id',
        'producto_hijo_id',
        'cantidad_requerida'
    ];

    protected $casts = [
        'cantidad_requerida' => 'decimal:3'
    ];

    public function productoPadre(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_padre_id');
    }

    public function productoSon(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_hijo_id');
    }
}