<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarioLote extends Model
{
    protected $table = 'inventario_lotes';

    // CONFIGURACIÓN DE FECHAS (IMPORTANTE: Tu tabla usa 'creado_at')
    const CREATED_AT = 'creado_at'; 
    const UPDATED_AT = null; // Tu tabla no tiene columna de actualización

    protected $fillable = [
        'producto_id',
        'proveedor_id',
        'numero_lote',
        'fecha_vencimiento',
        'cantidad_inicial',
        'cantidad_restante',
        'costo_unitario_usd',
        'ubicacion_almacen',
        'activo'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'cantidad_inicial' => 'decimal:3',
        'cantidad_restante' => 'decimal:3',
        'costo_unitario_usd' => 'decimal:4',
        'activo' => 'boolean',
    ];

    // Relación con Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // Relación con Proveedor
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }
    
    // Accesor para saber si está vencido fácilmente en Blade: $lote->esta_vencido
    public function getEstaVencidoAttribute()
    {
        return $this->fecha_vencimiento < now();
    }
}