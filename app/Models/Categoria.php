<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    // 1. Nombre de la tabla
    protected $table = 'categorias';

    // 2. Desactivamos timestamps si tu tabla no tiene columnas created_at y updated_at
    // (Según tus INSERT SQL, no las tenías, así que es mejor dejar esto en false)
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'imagen_url',
        'categoria_padre_id'
    ];

    // --- RELACIONES ---

    // Esta es la VITAL para tu CatalogoController
    // Permite usar: Categoria::has('productos')
    public function productos()
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }

    // Relación: Obtener la categoría superior (Ej: Antibióticos -> Veterinaria)
    public function padre()
    {
        return $this->belongsTo(Categoria::class, 'categoria_padre_id');
    }

    // Relación: Obtener las subcategorías (Ej: Veterinaria -> [Antibióticos, Vacunas...])
    public function hijos()
    {
        return $this->hasMany(Categoria::class, 'categoria_padre_id');
    }
}