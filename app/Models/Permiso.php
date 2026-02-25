<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $table = 'permisos';
    public $timestamps = false; 

    protected $fillable = ['nombre', 'descripcion'];

    // Relación: Un permiso pertenece a muchos roles
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'rol_permisos', 'permiso_id', 'rol_id');
    }
}