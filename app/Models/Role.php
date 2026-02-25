<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    public $timestamps = false; // La tabla no tiene timestamps

    protected $fillable = ['nombre'];

    // Relación: Un rol tiene muchos permisos
    public function permisos()
    {
        // Pasa por la tabla intermedia 'rol_permisos'
        return $this->belongsToMany(Permiso::class, 'rol_permisos', 'rol_id', 'permiso_id');
    }

    // Relación: Un rol tiene muchos usuarios
    public function usuarios()
    {
        return $this->hasMany(User::class, 'rol_id');
    }
}