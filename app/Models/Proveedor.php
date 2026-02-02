<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';
    public $timestamps = false; // Tu tabla proveedores no tiene timestamps

    protected $fillable = [
        'razon_social',
        'rif',
        'telefono',
        'persona_contacto',
        'activo'
    ];

    public function lotes()
    {
        return $this->hasMany(InventarioLote::class, 'proveedor_id');
    }
}