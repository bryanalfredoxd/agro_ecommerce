<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // <-- Importamos tu modelo User
// Asegúrate de importar también ZonaDelivery si no lo tienes en el mismo namespace
// use App\Models\ZonaDelivery; 

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';
    
    // En tu SQL vi que usas 'creado_at' en lugar de 'created_at'
    const CREATED_AT = 'creado_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'canal_venta', 'usuario_id', 'zona_delivery_id', 'tasa_cambio_id', 
        'subtotal_usd', 'costo_delivery_usd', 'descuento_usd', 'total_usd', 
        'total_ves_calculado', 'estado', 'direccion_texto', 'geo_latitud', 
        'geo_longitud', 'instrucciones_entrega'
    ];

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'pedido_id');
    }

    public function pago()
    {
        return $this->hasOne(Pago::class, 'pedido_id');
    }

    // AQUI ESTA LA CORRECCIÓN: Apuntamos al modelo User::class
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function zonaDelivery()
    {
        return $this->belongsTo(ZonaDelivery::class, 'zona_delivery_id');
    }
}