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
        'unidad_medida', 'contenido_neto', 'unidad_contenido', 
        'es_controlado', 'atributos_json', 'stock_total', 
        'stock_minimo_alerta', 'venta_minima', 'paso_venta', 
        'es_combo', 'destacado'
    ];

    protected $casts = [
        'es_controlado' => 'boolean',
        'es_combo' => 'boolean',
        'destacado' => 'boolean',
        'atributos_json' => 'array',
        'precio_venta_usd' => 'decimal:2',
        'stock_total' => 'decimal:3',
        'stock_minimo_alerta' => 'decimal:3',
    ];

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
}