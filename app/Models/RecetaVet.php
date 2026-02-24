<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecetaVet extends Model
{
    use HasFactory;

    protected $table = 'recetas_veterinarias';

    // AÑADIDO: Mapeamos la fecha de creación y anulamos la de actualización
    const CREATED_AT = 'creado_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'pedido_id',
        'cliente_usuario_id',
        'veterinario_nombre',
        'veterinario_matricula',
        'cliente_animal_tipo',
        'cliente_animal_cantidad',
        'fecha_prescription',
        'fecha_vencimiento_receta',
        'archivo_url',
        'estado',
        'observaciones',
        'usuario_revisa_id',
        'fecha_revision',
    ];

    protected $casts = [
        'fecha_prescription' => 'date',
        'fecha_vencimiento_receta' => 'date',
        'fecha_revision' => 'datetime',
        'cliente_animal_cantidad' => 'integer',
    ];

    // Relaciones
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function cliente(): BelongsTo
    {
        // Recuerda que aquí usamos User::class, asegurándonos de que esté importado
        // Si tu modelo de usuario es otro, ajusta esto.
        return $this->belongsTo(User::class, 'cliente_usuario_id');
    }

    public function revisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_revisa_id');
    }

    public function recetaProductos(): HasMany
    {
        return $this->hasMany(RecetaProducto::class, 'receta_id');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'aprobada');
    }

    public function scopeRechazadas($query)
    {
        return $query->where('estado', 'rechazada');
    }

    public function scopeExpiradas($query)
    {
        return $query->where('estado', 'expirada');
    }

    public function scopePorVencer($query)
    {
        return $query->where('fecha_vencimiento_receta', '<=', now()->addDays(30))
                    ->where('estado', 'aprobada');
    }

    // Métodos útiles
    public function estaVencida(): bool
    {
        return $this->fecha_vencimiento_receta && $this->fecha_vencimiento_receta->isPast();
    }

    public function estaPorVencer(): bool
    {
        return $this->fecha_vencimiento_receta &&
               $this->fecha_vencimiento_receta->diffInDays(now()) <= 30 &&
               !$this->estaVencida();
    }

    public function puedeSerRevisada(): bool
    {
        return $this->estado === 'pendiente';
    }
}