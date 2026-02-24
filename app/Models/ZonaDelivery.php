<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZonaDelivery extends Model
{
    use HasFactory;

    protected $table = 'zonas_delivery';

    protected $fillable = [
        'nombre_zona',
        'precio_delivery_usd',
        'tiempo_estimado_minutos',
        'requiere_vehiculo_carga',
        'activa',
    ];

    protected $casts = [
        'precio_delivery_usd' => 'decimal:2',
        'tiempo_estimado_minutos' => 'integer',
        'requiere_vehiculo_carga' => 'boolean',
        'activa' => 'boolean',
    ];

    // Relación con Pedido (un pedido pertenece a una zona de delivery)
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'zona_delivery_id');
    }
}