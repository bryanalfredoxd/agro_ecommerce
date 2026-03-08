<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesionCaja extends Model
{
    protected $table = 'sesiones_caja';
    public $timestamps = false;
    const CREATED_AT = 'fecha_apertura';

    protected $fillable = [
        'caja_id', 'cajero_usuario_id', 'fecha_apertura', 'fecha_cierre',
        'monto_inicial_usd', 'total_ventas_sistema_usd', 'total_ventas_sistema_ves',
        'dinero_real_en_caja_usd', 'dinero_real_en_caja_ves', 'diferencia_usd', 'observaciones_cierre'
    ];

    public function caja()
    {
        return $this->belongsTo(CajaFisica::class, 'caja_id');
    }

    public function cajero()
    {
        return $this->belongsTo(User::class, 'cajero_usuario_id');
    }
}