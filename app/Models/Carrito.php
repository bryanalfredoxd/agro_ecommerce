<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    use HasFactory;

    protected $table = 'carrito'; // Tu tabla en la BD
    public $timestamps = false; // La tabla tiene 'actualizado_at' pero no 'created_at', lo manejaremos manual o dejamos que la BD use el default

    protected $fillable = [
        'usuario_id',
        'producto_id',
        'cantidad',
        'observaciones'
    ];

    // Relación con Producto (para saber qué es lo que agregamos)
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}