<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductoImagen extends Model
{
    protected $table = 'producto_imagenes';
    public $timestamps = false;

    protected $fillable = [
        'producto_id',
        'url_imagen',
        'es_principal',
        'orden'
    ];

    protected $casts = [
        'es_principal' => 'boolean',
        'orden' => 'integer'
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}