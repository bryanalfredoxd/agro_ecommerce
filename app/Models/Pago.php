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

    // ==========================================
    // HELPERS Y ACCESSORS
    // ==========================================
    public function esPresencial(): bool
    {
        return in_array($this->metodo, ['efectivo_usd', 'efectivo_bs', 'punto_venta', 'biopago']);
    }

    public function getNombreMetodoAttribute(): string
    {
        return match($this->metodo) {
            'pago_movil' => 'Pago Móvil',
            'zelle' => 'Zelle',
            'binance' => 'Binance Pay',
            'transferencia' => 'Transferencia Bancaria',
            'efectivo_usd' => 'Efectivo ($ USD)',
            'efectivo_bs' => 'Efectivo (Bs)',
            'punto_venta' => 'Punto de Venta',
            'biopago' => 'Biopago BDV',
            default => ucfirst(str_replace('_', ' ', $this->metodo ?? 'Otro'))
        };
    }

    public function getUrlComprobanteAttribute()
    {
        if (empty($this->captura_pago_url)) {
            return null;
        }

        $url = trim($this->captura_pago_url);

        // Si ya es URL completa absoluta
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        // Si empieza con 'img/' o '/img/' (ej: img/upload/pagos/...)
        if (str_starts_with($url, 'img/') || str_starts_with($url, '/img/')) {
            return asset(ltrim($url, '/'));
        }

        // Si el archivo físico existe dentro de public/img/upload/pagos/
        $basename = basename($url);
        if (file_exists(public_path('img/upload/pagos/' . $basename))) {
            return asset('img/upload/pagos/' . $basename);
        }

        // Si empieza con 'storage/' o '/storage/'
        if (str_starts_with($url, 'storage/') || str_starts_with($url, '/storage/')) {
            return asset(ltrim($url, '/'));
        }

        // Si existe directamente en public
        if (file_exists(public_path(ltrim($url, '/')))) {
            return asset(ltrim($url, '/'));
        }

        // Fallback estándar
        return asset($url);
    }
}