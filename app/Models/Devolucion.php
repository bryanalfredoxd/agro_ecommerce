<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    use HasFactory;

    protected $table = 'devoluciones';

    protected $fillable = [
        'codigo_devolucion',
        'factura_id',
        'pedido_id',
        'cliente_id',
        'usuario_id',
        'tipo_devolucion',
        'monto_total_usd',
        'monto_total_ves',
        'tasa_bcv',
        'metodo_reembolso',
        'motivo_general',
        'observaciones',
        'reingresar_stock',
        'estado',
        'fecha_devolucion',
    ];

    protected $casts = [
        'monto_total_usd' => 'float',
        'monto_total_ves' => 'float',
        'tasa_bcv' => 'float',
        'reingresar_stock' => 'boolean',
        'fecha_devolucion' => 'datetime',
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'factura_id');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detalles()
    {
        return $this->hasMany(DevolucionDetalle::class, 'devolucion_id');
    }

    /**
     * Generar correlativo amigable tipo DEV-000001
     */
    public static function generarCodigo(): string
    {
        $ultimo = self::max('id') ?? 0;
        $siguiente = $ultimo + 1;
        return 'DEV-' . str_pad($siguiente, 6, '0', STR_PAD_LEFT);
    }
}
