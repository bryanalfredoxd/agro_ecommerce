<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DireccionUsuario extends Model
{
    use HasFactory;

    // Tu tabla en la BD
    protected $table = 'direcciones_usuarios';

    // Desactivamos timestamps si tu tabla no tiene created_at/updated_at 
    // (En tu SQL veo que la tabla direcciones_usuarios NO tiene timestamps, así que esto es vital)
    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'alias',
        'direccion_texto',
        'referencia_punto',
        'geo_latitud',
        'geo_longitud',
        'es_principal'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}