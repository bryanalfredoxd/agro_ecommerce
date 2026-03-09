<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaAjuste extends Model
{
    use HasFactory;

    protected $table = 'factura_ajustes';
    public $timestamps = false;

    protected $fillable = [
        'serie',
        'proximo_numero',
        'porcentaje_iva',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'porcentaje_iva' => 'decimal:2',
    ];
}