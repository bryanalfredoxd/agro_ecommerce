<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos'; // Definimos la tabla explícitamente

    protected $fillable = [
        'categoria_id',
        'marca_id',
        'proveedor_defecto_id',
        'nombre',
        'descripcion',
        'sku',
        'codigo_barras',
        'costo_promedio_usd',
        'precio_venta_usd',
        'precio_oferta_usd',
        'unidad_medida',
        'contenido_neto',
        'unidad_contenido',
        'es_controlado',
        'atributos_json',
        'stock_total',
        'stock_minimo_alerta',
        'venta_minima',
        'paso_venta',
        'es_combo',
        'destacado',
        'eliminado_at'
    ];

    // Casteamos el JSON automáticamente a array
    protected $casts = [
        'atributos_json' => 'array',
        'es_controlado' => 'boolean',
        'es_combo' => 'boolean',
        'destacado' => 'boolean',
        'precio_venta_usd' => 'decimal:2',
    ];

    // Relación: Un producto pertenece a una Categoría
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    // Relación: Un producto pertenece a una Marca
    public function marca()
    {
        return $this->belongsTo(Marca::class, 'marca_id');
    }

    // Relación: Imágenes (Nota: Tu controlador la pide, así que la defino aquí)
    // Si aún no tienes la tabla 'producto_imagenes', esto dará error al usar 'with'.
    // Si no tienes la tabla, comenta esta función por ahora.
    public function imagenes()
    {
        return $this->hasMany(ProductoImagen::class, 'producto_id');
    }
}