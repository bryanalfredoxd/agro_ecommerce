<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';

    const CREATED_AT = 'fecha_pago';
    const UPDATED_AT = null;

    protected $fillable = [
        'pedido_id', 
        'metodo', 
        'monto_usd', 
        'monto_ves', 
        'referencia_bancaria', 
        'captura_pago_url', 
        'estado',
        'verificado_por_usuario_id' 
    ];

    protected $casts = [
        'monto_usd' => 'decimal:2',
        'monto_ves' => 'decimal:2',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function verificador()
    {
        // El usuario admin/cajero que aprobó o rechazó el pago
        return $this->belongsTo(User::class, 'verificado_por_usuario_id');
    }
}