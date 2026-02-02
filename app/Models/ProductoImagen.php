<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoImagen extends Model
{
    // 1. Definimos la tabla exacta de tu SQL
    protected $table = 'producto_imagenes';

    // 2. IMPORTANTE: Tu tabla no tiene 'created_at' ni 'updated_at'
    public $timestamps = false;

    protected $fillable = [
        'producto_id',
        'url_imagen',
        'es_principal',
        'orden'
    ];

    protected $casts = [
        'es_principal' => 'boolean',
        'orden' => 'integer',
    ];

    // Relación inversa: Una imagen pertenece a un producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}