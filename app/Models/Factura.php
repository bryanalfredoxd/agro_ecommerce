<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Factura extends Model
{
    use HasFactory;

    protected $table = 'facturas';

    const CREATED_AT = 'fecha_emision';
    const UPDATED_AT = null;

    protected $fillable = [
        'pedido_id', 'serie_usada', 'numero_factura', 'cedula_rif_cliente',
        'nombre_razon_social', 'direccion_fiscal', 'subtotal_usd',
        'impuesto_usd', 'total_usd', 'valor_tasa_bcv', 'total_ves', 'estado'
    ];

    protected $appends = [
        'monto_iva_usd',
        'iva_porcentaje',
        'tasa_cambio_historica'
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function devoluciones()
    {
        return $this->hasMany(Devolucion::class, 'factura_id');
    }

    public function devolucion()
    {
        return $this->hasOne(Devolucion::class, 'factura_id')->latestOfMany('id');
    }

    // ==========================================
    // ACCESORES DE COMPATIBILIDAD Y AUDITORÍA
    // ==========================================
    public function getMontoIvaUsdAttribute(): float
    {
        if ($this->impuesto_usd !== null) {
            return (float) $this->impuesto_usd;
        }
        return (float) max(0, (float)($this->total_usd ?? 0) - (float)($this->subtotal_usd ?? 0));
    }

    public function getIvaPorcentajeAttribute(): float
    {
        $subtotal = (float) ($this->subtotal_usd ?? 0);
        $montoIva = $this->monto_iva_usd;

        if ($subtotal > 0 && $montoIva > 0) {
            return (float) round(($montoIva / $subtotal) * 100, 1);
        }

        if ($montoIva == 0 && $subtotal > 0) {
            return 0.0;
        }

        $config = ConfiguracionTienda::first();
        return (float) ($config->iva_porcentaje ?? 16.00);
    }

    public function getTasaCambioHistoricaAttribute(): float
    {
        return (float) ($this->valor_tasa_bcv ?? 1.0);
    }

    // ==========================================
    // MÉTODO GENERADOR DE FACTURAS
    // ==========================================
    public static function emitirParaPedido(Pedido $pedido, array $datosCliente = [])
    {
        // 1. Obtener la serie activa de facturación
        $ajuste = FacturaAjuste::where('activo', 1)->first();
        $serie = $ajuste ? $ajuste->serie : 'F';

        // 2. Llamar al Procedimiento Almacenado SQL para el correlativo de la serie activa
        $numeroFactura = null;
        try {
            $pdo = DB::getPdo();
            $pdo->exec("CALL sp_generar_numero_factura('{$serie}', @numero)");
            $resultado = $pdo->query("SELECT @numero AS numero")->fetch();
            $numeroFactura = $resultado['numero'] ?? null;
        } catch (\Throwable $e) {
            $numeroFactura = null;
        }

        // Fallback de seguridad por si el procedimiento retorna null o no encuentra la serie
        if (!$numeroFactura) {
            if ($ajuste) {
                $proximo = (int) $ajuste->proximo_numero;
                $numeroFactura = $serie . '-' . str_pad($proximo, 7, '0', STR_PAD_LEFT);
                $ajuste->increment('proximo_numero');
            } else {
                $numeroFactura = 'F-' . str_pad($pedido->id, 7, '0', STR_PAD_LEFT);
            }
        }

        $serieUsada = explode('-', $numeroFactura)[0];

        // 2. Extraer datos del cliente (prioriza datosCliente pasados explícitamente)
        $cliente = $pedido->usuario;
        $nombre = !empty($datosCliente['nombre_razon_social']) 
            ? trim($datosCliente['nombre_razon_social']) 
            : ($cliente ? trim($cliente->nombre . ' ' . $cliente->apellido) : 'Consumidor Final');

        $documento = !empty($datosCliente['cedula_rif_cliente']) 
            ? trim($datosCliente['cedula_rif_cliente']) 
            : ($cliente ? $cliente->documento_identidad : 'V-00000000');

        $direccion = !empty($datosCliente['direccion_fiscal']) 
            ? trim($datosCliente['direccion_fiscal']) 
            : ($pedido->direccion_texto ?? 'Compra en Tienda Física');

        // 3. Extraer cálculos monetarios
        $subtotal = (float) ($pedido->subtotal_usd ?? $pedido->total_usd);
        $recargo = (float) ($pedido->recargo_usd ?? 0);
        $delivery = (float) ($pedido->costo_delivery_usd ?? 0);
        $descuento = (float) ($pedido->descuento_usd ?? 0);

        // El impuesto (IVA) puro corresponde al total menos (subtotal + recargo + delivery - descuento)
        $impuesto = max(0, (float)$pedido->total_usd - $subtotal - $delivery - $recargo + $descuento);
        
        // Calcular la tasa exacta aplicada en ese momento
        $tasa = $pedido->total_usd > 0 ? ($pedido->total_ves_calculado / $pedido->total_usd) : 1;

        // 4. Insertar y retornar la factura creada
        return self::create([
            'pedido_id' => $pedido->id,
            'serie_usada' => $serieUsada,
            'numero_factura' => $numeroFactura,
            'cedula_rif_cliente' => $documento,
            'nombre_razon_social' => $nombre,
            'direccion_fiscal' => $direccion,
            'subtotal_usd' => $subtotal,
            'impuesto_usd' => $impuesto,
            'total_usd' => $pedido->total_usd,
            'valor_tasa_bcv' => $tasa,
            'total_ves' => $pedido->total_ves_calculado,
            'estado' => 'emitida'
        ]);
    }
}