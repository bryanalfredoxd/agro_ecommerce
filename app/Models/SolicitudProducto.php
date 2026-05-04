<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudProducto extends Model
{
    protected $table = 'solicitudes_productos';

    // Mapeamos las constantes de Laravel a los nombres de tus columnas
    const CREATED_AT = 'fecha_peticion';
    const UPDATED_AT = 'actualizado_at';

    protected $fillable = [
        'usuario_id',
        'nombre_producto',
        'descripcion_adicional',
        'estado'
    ];

    public function usuario(): BelongsTo
    {
        // Enlaza con la tabla usuarios si el cliente inició sesión
        return $this->belongsTo(User::class, 'usuario_id'); 
    }
}