<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoricoPrecioProducto extends Model
{
    protected $table = 'historico_precios_productos';
    public $timestamps = false;

    protected $fillable = [
        'producto_id',
        'precio_anterior_usd',
        'precio_nuevo_usd',
        'motivo_cambio',
        'usuario_editor_id'
    ];

    protected $casts = [
        'precio_anterior_usd' => 'decimal:2',
        'precio_nuevo_usd' => 'decimal:2'
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function usuarioEditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_editor_id');
    }
}