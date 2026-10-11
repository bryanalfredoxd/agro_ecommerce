<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Cotizacion extends Model
{
    protected $table = 'cotizaciones';
    
    const CREATED_AT = 'creado_at';
    const UPDATED_AT = 'actualizado_at';

    protected $fillable = [
        'numero_cotizacion',
        'vendedor_id',
        'cliente_id',
        'cliente_nombre',
        'cliente_documento',
        'cliente_telefono',
        'cliente_email',
        'cliente_direccion',
        'tasa_cambio_id',
        'valor_tasa_bcv',
        'subtotal_usd',
        'iva_porcentaje',
        'iva_usd',
        'total_usd',
        'total_ves',
        'validez_dias',
        'fecha_emision',
        'fecha_vencimiento',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'valor_tasa_bcv' => 'decimal:4',
        'subtotal_usd' => 'decimal:2',
        'iva_porcentaje' => 'decimal:2',
        'iva_usd' => 'decimal:2',
        'total_usd' => 'decimal:2',
        'total_ves' => 'decimal:2',
        'validez_dias' => 'integer',
        'fecha_emision' => 'datetime',
        'fecha_vencimiento' => 'datetime',
        'creado_at' => 'datetime',
        'actualizado_at' => 'datetime',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================
    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function tasaCambio()
    {
        return $this->belongsTo(TasaCambio::class, 'tasa_cambio_id');
    }

    public function detalles()
    {
        return $this->hasMany(CotizacionDetalle::class, 'cotizacion_id');
    }

    // ==========================================
    // HELPERS
    // ==========================================
    public static function generarSiguienteNumero(): string
    {
        $ultimoId = self::max('id') ?? 0;
        $siguiente = $ultimoId + 1;
        return 'COT-' . str_pad($siguiente, 6, '0', STR_PAD_LEFT);
    }

    public function getEstaVencidaAttribute(): bool
    {
        if ($this->estado === 'aprobada' || $this->estado === 'rechazada') {
            return false;
        }
        return $this->fecha_vencimiento && Carbon::now()->greaterThan($this->fecha_vencimiento);
    }

    public function getEstadoBadgeAttribute(): array
    {
        if ($this->esta_vencida && $this->estado === 'pendiente') {
            return [
                'label' => 'Vencida',
                'bg' => 'bg-amber-100 text-amber-800 border-amber-300',
                'dot' => 'bg-amber-500'
            ];
        }

        return match ($this->estado) {
            'aprobada' => [
                'label' => 'Aprobada',
                'bg' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                'dot' => 'bg-emerald-500'
            ],
            'rechazada' => [
                'label' => 'Rechazada',
                'bg' => 'bg-rose-100 text-rose-800 border-rose-300',
                'dot' => 'bg-rose-500'
            ],
            'vencida' => [
                'label' => 'Vencida',
                'bg' => 'bg-amber-100 text-amber-800 border-amber-300',
                'dot' => 'bg-amber-500'
            ],
            default => [
                'label' => 'Pendiente',
                'bg' => 'bg-blue-100 text-blue-800 border-blue-300',
                'dot' => 'bg-blue-500'
            ],
        };
    }
}
