<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricoPrecioProducto extends Model
{
    use HasFactory;

    protected $table = 'historico_precios_productos';

    const CREATED_AT = 'creado_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'producto_id',
        'precio_anterior_usd',
        'precio_nuevo_usd',
        'motivo_cambio',
        'usuario_editor_id'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'usuario_editor_id');
    }
}