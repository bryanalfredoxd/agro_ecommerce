<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CajaFisica extends Model
{
    protected $table = 'cajas_fisicas';
    public $timestamps = false;

    protected $fillable = ['nombre', 'activa', 'cajero_asignado_id'];

    public function cajeroAsignado()
    {
        return $this->belongsTo(User::class, 'cajero_asignado_id');
    }

    public function sesiones()
    {
        return $this->hasMany(SesionCaja::class, 'caja_id');
    }

    public function sesionActiva()
    {
        return $this->hasOne(SesionCaja::class, 'caja_id')->whereNull('fecha_cierre');
    }
}