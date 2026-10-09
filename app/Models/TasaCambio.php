<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TasaCambio extends Model
{
    protected $table = 'tasas_cambio';
    public $timestamps = false;
    
    protected $fillable = [
        'codigo_moneda',
        'moneda_base', 
        'valor_tasa',
        'fuente',
        'usuario_editor_id',
        'creado_at'
    ];
    
    protected $casts = [
        'valor_tasa' => 'decimal:4',
        'creado_at' => 'datetime'
    ];
    
    public function editor()
    {
        return $this->belongsTo(User::class, 'usuario_editor_id');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'tasa_cambio_id');
    }

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