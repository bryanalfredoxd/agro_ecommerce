<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuentaBancaria extends Model
{
    use HasFactory;

    // Nombre exacto de la tabla que creamos en SQL
    protected $table = 'cuentas_bancarias';

    // Campos que permitimos llenar (Mass Assignment)
    protected $fillable = [
        'nombre_titular',
        'banco_entidad',
        'numero_cuenta',
        'telefono',
        'identificacion',
        'email',
        'tipo_metodo', // El ENUM/STRING (pago_movil, zelle, cashea, etc)
        'modalidad_pago', // divisas, bolivares, cashea
        'instrucciones_adicionales',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * ACCESOR: getNombreAttribute
     * Esto nos permite usar $cuenta->nombre en la vista
     * Convierte 'pago_movil' en 'Pago Móvil'
     */
    public function getNombreAttribute()
    {
        return match($this->tipo_metodo) {
            'pago_movil' => 'Pago Móvil',
            'zelle' => 'Zelle',
            'efectivo_usd' => 'Efectivo Divisas',
            'efectivo_bs' => 'Efectivo Bolívares',
            'transferencia' => 'Transferencia Bancaria',
            'punto_venta' => 'Punto de Venta',
            'binance' => 'Binance Pay',
            'biopago' => 'Biopago',
            'cashea' => 'Cashea (Cuotas)',
            default => ucfirst(str_replace('_', ' ', $this->tipo_metodo)),
        };
    }

    /**
     * Retorna el texto legible de la modalidad de precio ('divisas', 'bolivares', 'cashea').
     */
    public function getModalidadTextoAttribute(): string
    {
        return match($this->modalidad_pago) {
            'divisas' => 'Divisas (USD / USDT)',
            'cashea' => 'Cashea (Cuotas)',
            default => 'Bolívares (VES)',
        };
    }

    /**
     * Retorna el recargo vigente para esta cuenta según el canal ('web' o 'pos').
     */
    public function getRecargoPorcentaje(string $canal = 'web'): float
    {
        $modalidad = $this->modalidad_pago ?: 'bolivares';
        return ConfiguracionTienda::recargoPorcentaje($modalidad, $canal);
    }

    /**
     * ACCESOR: getInfoAttribute
     * Esto nos permite usar $cuenta->info en la vista (igual que en el array manual anterior)
     * Genera el texto con los datos bancarios
     */
    public function getInfoAttribute()
    {
        $datos = [];

        if ($this->banco_entidad) $datos[] = $this->banco_entidad;
        if ($this->numero_cuenta) $datos[] = "Cta: " . $this->numero_cuenta;
        if ($this->telefono) $datos[] = "Tlf: " . $this->telefono;
        if ($this->identificacion) $datos[] = "ID: " . $this->identificacion;
        if ($this->email) $datos[] = "Email: " . $this->email;
        if ($this->nombre_titular) $datos[] = "Titular: " . $this->nombre_titular;
        
        // Unimos todo con guiones o saltos de línea
        $textoBase = implode(' - ', $datos);

        if ($this->instrucciones_adicionales) {
            $textoBase .= " (" . $this->instrucciones_adicionales . ")";
        }

        return $textoBase ?: 'Consultar en caja';
    }
    
    /**
     * ACCESOR: datos_bancarios_json
     * Para compatibilidad con el script JS de la vista que espera un JSON
     */
    public function getDatosBancariosJsonAttribute()
    {
        // Retornamos el texto formateado como un string simple para que el JS lo muestre
        return $this->info;
    }
}