<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    use HasFactory;

    // CORRECCIÓN CRÍTICA: Definir el nombre exacto de tu tabla en BD
    protected $table = 'pedido_detalles';

    // Tu tabla de detalles no tiene timestamps (created_at, updated_at)
    public $timestamps = false;

    protected $fillable = [
        'pedido_id',
        'producto_id',
        'inventario_lote_id',
        'cantidad_solicitada',      // En tu BD es 'cantidad_solicitada', no 'cantidad'
        'cantidad_real_despachada',
        'precio_historico_usd',     // En tu BD es 'precio_historico_usd'
        'observaciones'
    ];

    /**
     * Relación con el Producto
     * Necesaria para la vista: $detalle->producto->nombre
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    /**
     * Accesor para compatibilidad con código antiguo
     * Si en alguna vista usaste $detalle->cantidad, esto lo redirige a cantidad_solicitada
     */
    public function getCantidadAttribute()
    {
        return $this->cantidad_solicitada;
    }
}