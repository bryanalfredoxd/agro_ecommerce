<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TasaCambio extends Model
{
    protected $table = 'tasas_cambio';
    
    protected $fillable = [
        'codigo_moneda',
        'moneda_base', 
        'valor_tasa',
        'fuente',
        'usuario_editor_id'
    ];
    
    protected $casts = [
        'valor_tasa' => 'decimal:4',
    ];
    
    // Obtener la última tasa de cambio USD
    public static function obtenerTasaUSD()
    {
        return self::where('codigo_moneda', 'USD')
            ->latest('creado_at')
            ->first();
    }
    
    // Obtener el valor formateado
    public static function obtenerValorUSD()
    {
        $tasa = self::obtenerTasaUSD();
        return $tasa ? number_format($tasa->valor_tasa, 2, '.', '') : '0.00';
    }
}