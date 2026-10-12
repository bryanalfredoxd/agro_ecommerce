<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevolucionDetalle extends Model
{
    use HasFactory;

    protected $table = 'devoluciones_detalle';

    public $timestamps = true;

    protected $fillable = [
        'devolucion_id',
        'pedido_id',
        'pedido_detalle_id',
        'producto_id',
        'inventario_lote_id',
        'cantidad_devuelta',
        'precio_unitario_usd',
        'monto_subtotal_usd',
        'motivo_devolucion',
        'estado_producto',
        'costo_reposicion_usd',
        'observaciones',
        'usuario_recibe_id',
        'fecha_recepcion',
        'usuario_procesa_id',
        'fecha_procesamiento'
    ];

    protected $casts = [
        'cantidad_devuelta' => 'float',
        'precio_unitario_usd' => 'float',
        'monto_subtotal_usd' => 'float',
        'costo_reposicion_usd' => 'float',
        'fecha_recepcion' => 'datetime',
        'fecha_procesamiento' => 'datetime',
    ];

    public function devolucion()
    {
        return $this->belongsTo(Devolucion::class, 'devolucion_id');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function pedidoDetalle()
    {
        return $this->belongsTo(DetallePedido::class, 'pedido_detalle_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function lote()
    {
        return $this->belongsTo(InventarioLote::class, 'inventario_lote_id');
    }

    public function usuarioRecibe()
    {
        return $this->belongsTo(User::class, 'usuario_recibe_id');
    }

    public function usuarioProcesa()
    {
        return $this->belongsTo(User::class, 'usuario_procesa_id');
    }
}
