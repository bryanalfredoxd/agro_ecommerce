<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecetaVeterinaria extends Model
{
    protected $table = 'recetas_veterinarias';
    public $timestamps = false;

    protected $fillable = [
        'estado', 'creado_at'
    ];
}