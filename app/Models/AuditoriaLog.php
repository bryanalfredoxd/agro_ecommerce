<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditoriaLog extends Model
{
    protected $table = 'auditoria_logs';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id', 'evento', 'tabla_afectada', 'registro_id', 
        'valores_anteriores', 'valores_nuevos', 'direccion_ip', 'fecha'
    ];
}