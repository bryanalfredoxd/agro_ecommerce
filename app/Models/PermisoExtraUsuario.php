<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermisoExtraUsuario extends Model
{
    protected $table = 'permisos_extra_usuario';
    public $timestamps = false; // Esta tabla no usa creado_at

    protected $fillable = [
        'usuario_id',
        'permiso_id',
        'accion' // 'permitir' o 'denegar'
    ];
}