<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';
    
    // En tu SQL vi que usas 'creado_at' en lugar de 'created_at'
    const CREATED_AT = 'creado_at';
    const UPDATED_AT = null;

protected $fillable = [
    'canal_venta', 'sesion_caja_id', 'usuario_id', 'zona_delivery_id', 'tasa_cambio_id', 
    'subtotal_usd', 'costo_delivery_usd', 'descuento_usd', 'total_usd', 
    'total_ves_calculado', 'estado', 'direccion_texto', 'geo_latitud', 
    'geo_longitud', 'instrucciones_entrega'
];

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'pedido_id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'pedido_id')->orderBy('id', 'desc');
    }

    public function pago()
    {
        return $this->hasOne(Pago::class, 'pedido_id')->latestOfMany('id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function factura()
    {
        return $this->hasOne(Factura::class, 'pedido_id');
    }

    public function zonaDelivery()
    {
        return $this->belongsTo(ZonaDelivery::class, 'zona_delivery_id');
    }

    /**
     * Devuelve el monto total en Bolívares (VES)
     */
    public function getTotalVesAttribute(): float
    {
        if ($this->factura && (float)$this->factura->total_ves > 0) {
            return (float) $this->factura->total_ves;
        }
        return (float) ($this->total_ves_calculado ?? 0);
    }

    /**
     * Determina si el pedido corresponde a envío a domicilio (delivery) o retiro en sede
     */
    public function getEsDeliveryAttribute(): bool
    {
        if ($this->zona_delivery_id !== null) {
            return true;
        }
        if ((float) $this->costo_delivery_usd > 0) {
            return true;
        }
        if (!empty($this->direccion_texto) && !in_array(trim($this->direccion_texto), ['Retiro en Tienda', 'Retiro en Sede', 'Retiro en Tienda Física', 'Retiro en Sede Física'])) {
            return true;
        }
        return false;
    }

    /**
     * Etiqueta humana para el tipo de entrega
     */
    public function getTipoEntregaTextoAttribute(): string
    {
        return $this->es_delivery ? 'Envío a Domicilio (Delivery)' : 'Retiro en Sede';
    }
}