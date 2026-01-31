<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    // Definimos el nombre de la tabla si no sigue el estándar plural de Laravel
    protected $table = 'categorias';

    // No tienes timestamps (created_at, updated_at) en tu SQL, así que los desactivamos
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'imagen_url',
        'categoria_padre_id'
    ];

    /**
     * Relación: Obtener la categoría superior.
     */
    public function padre()
    {
        return $this->belongsTo(Categoria::class, 'categoria_padre_id');
    }

    /**
     * Relación: Obtener las subcategorías.
     */
    public function hijos()
    {
        return $this->hasMany(Categoria::class, 'categoria_padre_id');
    }
}