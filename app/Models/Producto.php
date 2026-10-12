<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Producto extends Model
{
    use SoftDeletes; // Habilita el borrado lógico

    protected $table = 'productos';
    public $timestamps = false; 
    
    const DELETED_AT = 'eliminado_at'; 

    protected $fillable = [
        'categoria_id', 'marca_id', 'proveedor_defecto_id', 'nombre', 
        'descripcion', 'imagen_url', 'sku', 'codigo_barras', 
        'costo_promedio_usd', 'precio_venta_usd', 'precio_oferta_usd', 
        'precio_incluye_iva',
        'unidad_medida', 'contenido_neto', 'unidad_contenido', 
        'es_controlado', 'atributos_json', 'stock_total', 
        'stock_minimo_alerta', 'venta_minima', 'paso_venta', 
        'es_combo', 'destacado'
    ];

    protected $casts = [
        'es_controlado' => 'boolean',
        'es_combo' => 'boolean',
        'destacado' => 'boolean',
        'precio_incluye_iva' => 'boolean',
        'atributos_json' => 'array',
        'precio_venta_usd' => 'decimal:2',
        'precio_oferta_usd' => 'decimal:2',
        'costo_promedio_usd' => 'decimal:2',
        'contenido_neto' => 'decimal:3',
        'stock_total',  
        'stock_minimo_alerta', 
        'venta_minima' => 'decimal:3',   
        'paso_venta' => 'decimal:3',    
    ];

    protected $appends = [
        'precio_base_pvp',
        'precio_final_pvp',
        'precio_regular_pvp',
    ];

    /**
     * Calcula el PVP base de venta al público (con IVA) antes de aplicar recargos de modalidad.
     */
    public function getPrecioBasePvpAttribute(): float
    {
        $iva = \App\Models\ConfiguracionTienda::iva();
        $precioBase = $this->precio_oferta_usd ?: $this->precio_venta_usd;
        return round($precioBase * (1 + ($iva / 100)), 2);
    }

    /**
     * Calcula el precio de venta en Dólares (Divisas).
     * Siempre muestra el precio correspondiente a Dólares (con su % de recargo si está configurado).
     */
    public function getPrecioFinalPvpAttribute(): float
    {
        $base = $this->getPrecioBasePvpAttribute();
        $recargoDivisasPct = \App\Models\ConfiguracionTienda::recargoPorcentaje('divisas', 'web');
        return round($base * (1 + ($recargoDivisasPct / 100)), 2);
    }

    /**
     * Alias compatible de precio final PVP.
     */
    public function getPrecioVentaPvpAttribute(): float
    {
        return $this->getPrecioFinalPvpAttribute();
    }

    /**
     * Calcula el PVP del precio regular (antes de oferta) en Dólares con recargo de Divisas si aplica.
     */
    public function getPrecioRegularPvpAttribute(): float
    {
        $iva = \App\Models\ConfiguracionTienda::iva();
        $baseRegular = round($this->precio_venta_usd * (1 + ($iva / 100)), 2);
        $recargoDivisasPct = \App\Models\ConfiguracionTienda::recargoPorcentaje('divisas', 'web');
        return round($baseRegular * (1 + ($recargoDivisasPct / 100)), 2);
    }

    /**
     * Retorna el PVP de la oferta con IVA y recargo de Divisas si existe.
     */
    public function getPrecioOfertaPvpAttribute(): ?float
    {
        if (!$this->precio_oferta_usd) return null;
        $iva = \App\Models\ConfiguracionTienda::iva();
        $baseOferta = round($this->precio_oferta_usd * (1 + ($iva / 100)), 2);
        $recargoDivisasPct = \App\Models\ConfiguracionTienda::recargoPorcentaje('divisas', 'web');
        return round($baseOferta * (1 + ($recargoDivisasPct / 100)), 2);
    }

    /**
     * Retorna el precio según la modalidad solicitada ('divisas', 'bolivares', 'cashea').
     */
    public function getPrecioModalidad(string $modalidad = 'divisas', string $canal = 'web'): float
    {
        $base = $this->getPrecioBasePvpAttribute();
        $pct = \App\Models\ConfiguracionTienda::recargoPorcentaje($modalidad, $canal);
        return round($base * (1 + ($pct / 100)), 2);
    }

    /**
     * Retorna los 3 precios (Divisas, Bolívares y Cashea) con sus recargos y equivalencias.
     */
    public function getPreciosModalidades(string $canal = 'web', ?float $tasaBcv = null): array
    {
        if ($tasaBcv === null) {
            $tasaBcv = (float) \App\Models\TasaCambio::obtenerValorUSD();
        }
        $base = $this->getPrecioBasePvpAttribute();

        $pctDivisas = \App\Models\ConfiguracionTienda::recargoPorcentaje('divisas', $canal);
        $pctBolivares = \App\Models\ConfiguracionTienda::recargoPorcentaje('bolivares', $canal);
        $pctCashea = \App\Models\ConfiguracionTienda::recargoPorcentaje('cashea', $canal);

        $precioDivisas = round($base * (1 + ($pctDivisas / 100)), 2);
        $precioBolivaresUsd = round($base * (1 + ($pctBolivares / 100)), 2);
        $precioBolivaresVes = round($precioBolivaresUsd * $tasaBcv, 2);
        $precioCashea = round($base * (1 + ($pctCashea / 100)), 2);

        return [
            'base_pvp' => $base,
            'divisas' => [
                'usd' => $precioDivisas,
                'recargo_pct' => $pctDivisas,
                'tiene_recargo' => $pctDivisas > 0,
            ],
            'bolivares' => [
                'usd_ref' => $precioBolivaresUsd,
                'ves' => $precioBolivaresVes,
                'recargo_pct' => $pctBolivares,
                'tiene_recargo' => $pctBolivares > 0,
            ],
            'cashea' => [
                'usd' => $precioCashea,
                'cuota_inicial_usd' => round($precioCashea / 2, 2),
                'cuota_restante_usd' => round($precioCashea / 6, 2),
                'recargo_pct' => $pctCashea,
                'tiene_recargo' => $pctCashea > 0,
            ],
        ];
    }

    // ==========================================
    // RELACIONES
    // ==========================================
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'marca_id');
    }

    public function imagenes()
    {
        return $this->hasMany(ProductoImagen::class, 'producto_id');
    }

    public function favoritos()
    {
        return $this->hasMany(Favorito::class, 'producto_id');
    }
}