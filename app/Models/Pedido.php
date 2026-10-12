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
    'modalidad_pago',
    'subtotal_usd', 'costo_delivery_usd', 'descuento_usd', 
    'recargo_porcentaje', 'recargo_usd',
    'total_usd', 'total_ves_calculado', 'estado', 'direccion_texto', 'geo_latitud', 
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

    public function devoluciones()
    {
        return $this->hasMany(Devolucion::class, 'pedido_id');
    }

    public function factura()
    {
        return $this->hasOne(Factura::class, 'pedido_id');
    }

    public function zonaDelivery()
    {
        return $this->belongsTo(ZonaDelivery::class, 'zona_delivery_id');
    }

    public function tasaCambio()
    {
        return $this->belongsTo(TasaCambio::class, 'tasa_cambio_id');
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
        if ($this->canal_venta === 'tienda_fisica' && (float) $this->costo_delivery_usd <= 0 && $this->zona_delivery_id === null) {
            return false;
        }
        if ($this->zona_delivery_id !== null) {
            return true;
        }
        if ((float) $this->costo_delivery_usd > 0) {
            return true;
        }
        if (!empty($this->direccion_texto)) {
            $dirLower = mb_strtolower(trim($this->direccion_texto));
            if (str_contains($dirLower, 'retiro') || str_contains($dirLower, 'tienda') || str_contains($dirLower, 'sede') || str_contains($dirLower, 'mostrador')) {
                return false;
            }
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

    /**
     * Valida de forma estricta si un usuario específico tiene autorización
     * para consultar o interactuar con este pedido (protección contra IDOR y RBAC granular).
     */
    public function puedeSerVistoPor(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        // 1. Administrador principal: Acceso total e irrestricto
        if ($user->esAdmin()) {
            return true;
        }

        // 2. Clientes convencionales (Rol Cliente / Web):
        // Únicamente tienen permiso para consultar pedidos propios
        if (!$user->esPersonal()) {
            return $this->usuario_id !== null && (int) $this->usuario_id === (int) $user->id;
        }

        // 3. Personal operativo / administrativo de la empresa:
        // Si tiene permiso para ver todos los pedidos sin distinción de estado
        if ($user->tienePermiso('ver_pedidos_todos')) {
            return true;
        }

        // Si el pago está en revisión y el usuario tiene permisos de auditoría de pago
        if ($this->pago && $this->pago->estado === 'revision' && ($user->tienePermiso('ver_pedidos_por_verificar') || $user->tienePermiso('gestionar_pagos'))) {
            return true;
        }

        // Pedidos listos para preparación y despacho
        if (in_array($this->estado, ['pagado', 'preparacion']) && $user->tienePermiso('ver_pedidos_despacho')) {
            return true;
        }

        // Pedidos en ruta de reparto logístico
        if ($this->estado === 'en_ruta' && $user->tienePermiso('ver_pedidos_en_ruta')) {
            return true;
        }

        // Pedidos completados y entregados
        if (in_array($this->estado, ['entregado', 'completado_caja']) && $user->tienePermiso('ver_pedidos_completados')) {
            return true;
        }

        // Pedidos devueltos o cancelados
        if (in_array($this->estado, ['devuelto', 'cancelado']) && $user->tienePermiso('ver_pedidos_cancelados')) {
            return true;
        }

        // Si cuenta con el permiso general ver_pedidos y no tiene restricciones granulares
        if ($user->tienePermiso('ver_pedidos') && 
            !$user->tienePermiso('ver_pedidos_despacho') && 
            !$user->tienePermiso('ver_pedidos_en_ruta') && 
            !$user->tienePermiso('ver_pedidos_por_verificar')) {
            return true;
        }

        return false;
    }
}