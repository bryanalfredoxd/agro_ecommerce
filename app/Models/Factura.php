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

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    // ==========================================
    // MÉTODO GENERADOR DE FACTURAS
    // ==========================================
    public static function emitirParaPedido(Pedido $pedido)
    {
        // 1. Llamar a tu Procedimiento Almacenado SQL para el número correlativo
        $pdo = DB::getPdo();
        $pdo->exec("CALL sp_generar_numero_factura('F', @numero)");
        $resultado = $pdo->query("SELECT @numero AS numero")->fetch();
        $numeroFactura = $resultado['numero'];

        // Fallback de seguridad por si falla la BD
        if (!$numeroFactura) {
            $numeroFactura = 'F-' . str_pad($pedido->id, 7, '0', STR_PAD_LEFT);
        }

        $serieUsada = explode('-', $numeroFactura)[0];

        // 2. Extraer datos del cliente
        $cliente = $pedido->usuario;
        $nombre = $cliente ? trim($cliente->nombre . ' ' . $cliente->apellido) : 'Consumidor Final';
        $documento = $cliente ? $cliente->documento_identidad : 'V-00000000';
        $direccion = $pedido->direccion_texto ?? 'Compra en Tienda Física';

        // 3. Extraer cálculos monetarios
        // Si el pedido tiene subtotal (como los del POS), lo usamos. Si no, asumimos que el total es el subtotal.
        $subtotal = $pedido->subtotal_usd ?? $pedido->total_usd;
        $impuesto = $pedido->total_usd - $subtotal;
        
        // Calcular la tasa exacta aplicada en ese momento
        $tasa = $pedido->total_usd > 0 ? ($pedido->total_ves_calculado / $pedido->total_usd) : 1;

        // 4. Insertar en la tabla facturas
        self::create([
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