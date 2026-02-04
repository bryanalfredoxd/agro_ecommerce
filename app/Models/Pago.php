<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';

    // CORRECCIÓN CRÍTICA:
    // 1. Según tu SQL, la fecha de creación se llama 'fecha_pago'
    const CREATED_AT = 'fecha_pago';
    
    // 2. Tu tabla 'pagos' NO tiene columna de actualización, la desactivamos
    const UPDATED_AT = null;

    protected $fillable = [
        'pedido_id', 
        'metodo', 
        'monto_usd', 
        'monto_ves', 
        'referencia_bancaria', 
        'captura_pago_url', // Asegúrate que en tu BD sea 'captura_pago_url' (si es 'captura_pago_url' corrígelo aquí también)
        'estado',
        'verificado_por_usuario_id' // Agregado por si acaso lo necesitas llenar después
    ];
}