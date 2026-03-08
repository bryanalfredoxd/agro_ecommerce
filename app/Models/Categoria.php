<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Categoria extends Model
{
    use SoftDeletes; // <-- BORRADO LÓGICO

    protected $table = 'categorias';
    public $timestamps = false;
    
    // Indicamos la columna personalizada
    const DELETED_AT = 'eliminado_at';

    protected $fillable = [
        'nombre',
        'imagen_url',
        'categoria_padre_id'
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }

    public function padre()
    {
        return $this->belongsTo(Categoria::class, 'categoria_padre_id');
    }

    public function subcategorias()
    {
        return $this->hasMany(Categoria::class, 'categoria_padre_id');
    }
}