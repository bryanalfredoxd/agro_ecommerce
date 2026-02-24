<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecetaProducto extends Model
{
    use HasFactory;

    protected $table = 'receta_productos';

    // AÑADIDO: Desactivamos los timestamps porque tu tabla no tiene estas columnas
    public $timestamps = false;

    protected $fillable = [
        'receta_id',
        'producto_id',
        'cantidad_prescrita',
        'dosis_instrucciones',
        'autorizado',
    ];

    protected $casts = [
        'cantidad_prescrita' => 'decimal:3',
        'autorizado' => 'boolean',
    ];

    // Relaciones
    public function receta(): BelongsTo
    {
        return $this->belongsTo(RecetaVet::class, 'receta_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}