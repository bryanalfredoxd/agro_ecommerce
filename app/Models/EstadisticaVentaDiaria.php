<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadisticaVentaDiaria extends Model
{
    use HasFactory;

    // 1. Nombre exacto de la tabla
    protected $table = 'estadisticas_ventas_diarias';

    // 2. No usamos los timestamps automáticos de Laravel
    public $timestamps = false;

    // 3. Campos asignables
    protected $fillable = [
        'fecha_reporte',
        'total_pedidos',
        'total_ingresos_usd',
        'total_ingresos_ves',
        'unidades_vendidas'
    ];

    // 4. Casteos para asegurar que los montos sean decimales en PHP
    protected $casts = [
        'fecha_reporte' => 'date',
        'total_ingresos_usd' => 'decimal:2',
        'total_ingresos_ves' => 'decimal:2',
        'unidades_vendidas' => 'decimal:3',
    ];
}