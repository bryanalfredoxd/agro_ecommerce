<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZonaDelivery extends Model
{
    protected $table = 'zonas_delivery';
    public $timestamps = false;

    protected $fillable = [
        'nombre_zona',
        'precio_delivery_usd',
        'tiempo_estimado_minutos',
        'requiere_vehiculo_carga',
        'activa',
        'latitud',
        'longitud',
        'radio_km',
        'color_mapa',
        'descripcion'
    ];

    protected $casts = [
        'precio_delivery_usd' => 'decimal:2',
        'requiere_vehiculo_carga' => 'boolean',
        'activa' => 'boolean',
        'latitud' => 'float',
        'longitud' => 'float',
        'radio_km' => 'float'
    ];

    public function direcciones()
    {
        return $this->hasMany(DireccionUsuario::class, 'zona_delivery_id');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'zona_delivery_id');
    }

    /**
     * Calcula la distancia en kilómetros entre dos coordenadas usando la fórmula de Haversine
     */
    public static function calcularDistanciaKm($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371.0; // Radio medio de la Tierra en km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    /**
     * Encuentra la zona de delivery activa óptima que cubre el punto geográfico dado.
     * Retorna null si está fuera del radio de todas las zonas activas.
     */
    public static function encontrarZonaParaCoordenadas($lat, $lng): ?self
    {
        if ($lat === null || $lng === null) {
            return null;
        }

        $lat = (float) $lat;
        $lng = (float) $lng;

        $zonasActivas = self::where('activa', 1)->get();
        $mejorZona = null;

        foreach ($zonasActivas as $zona) {
            if ($zona->latitud === null || $zona->longitud === null || $zona->radio_km === null) {
                continue;
            }

            $distancia = self::calcularDistanciaKm(
                $lat,
                $lng,
                (float) $zona->latitud,
                (float) $zona->longitud
            );

            // Si está dentro del radio_km de esta zona
            if ($distancia <= (float) $zona->radio_km) {
                // Si aún no tenemos zona o esta zona tiene un radio menor (más específica)
                if ($mejorZona === null || (float) $zona->radio_km < (float) $mejorZona->radio_km) {
                    $mejorZona = $zona;
                }
            }
        }

        return $mejorZona;
    }
}
