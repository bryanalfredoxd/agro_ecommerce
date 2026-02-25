<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionApi extends Model
{
    protected $table = 'configuracion_api';
    public $timestamps = false;

    protected $fillable = ['nombre_api', 'url_base', 'api_key', 'api_secret', 'activo', 'actualizado_at'];
}