<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionTienda extends Model
{
    use HasFactory;

    // 1. Nombre de la tabla
    protected $table = 'configuracion_tienda';

    // 2. Desactivamos los timestamps automáticos de Laravel. 
    // La base de datos ya se encarga de actualizar 'actualizado_at' automáticamente.
    public $timestamps = false;

    // 3. Campos que se pueden llenar masivamente
    protected $fillable = [
        'nombre_empresa',
        'iva_porcentaje',
        'modo_operativo',
        'mensaje_cierre_emergencia',
        'ultimo_editor_id'
    ];

    // 4. Casteos para asegurar tipos de datos correctos
    protected $casts = [
        'iva_porcentaje' => 'decimal:2',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================
    
    /**
     * Relación para saber qué administrador modificó la configuración por última vez
     */
    public function ultimoEditor()
    {
        return $this->belongsTo(User::class, 'ultimo_editor_id'); // Asegúrate de que tu modelo de usuario se llame User (o cambialo a Usuario si así lo llamaste)
    }
}