<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TasaCambioadmin extends Model
{
    protected $table = 'tasas_cambio';
    public $timestamps = false; // Usa creado_at

    protected $fillable = [
        'codigo_moneda', 'moneda_base', 'valor_tasa', 'fuente', 'usuario_editor_id', 'creado_at'
    ];

    // Saber quién modificó la tasa (si fue manual)
    public function editor()
    {
        return $this->belongsTo(User::class, 'usuario_editor_id');
    }

    // Para saber el impacto: ¿Cuántos pedidos se hicieron con esta tasa?
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'tasa_cambio_id');
    }
}