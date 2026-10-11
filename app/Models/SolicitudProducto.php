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
        'nombre_contacto',
        'telefono_contacto',
        'nombre_producto',
        'descripcion_adicional',
        'estado'
    ];

    protected $casts = [
        'fecha_peticion' => 'datetime',
        'actualizado_at' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        // Enlaza con la tabla usuarios si el cliente inició sesión
        return $this->belongsTo(User::class, 'usuario_id'); 
    }

    /**
     * Nombre del cliente solicitante (usuario registrado o contacto ingresado)
     */
    public function getNombreClienteAttribute(): string
    {
        if ($this->usuario) {
            return trim($this->usuario->nombre . ' ' . ($this->usuario->apellido ?? ''));
        }
        return $this->nombre_contacto ?: 'Cliente Invitado';
    }

    /**
     * Teléfono del cliente para contacto directo o WhatsApp
     */
    public function getTelefonoClienteAttribute(): ?string
    {
        if ($this->usuario && !empty($this->usuario->telefono)) {
            return $this->usuario->telefono;
        }
        return $this->telefono_contacto;
    }

    /**
     * Email del cliente
     */
    public function getEmailClienteAttribute(): ?string
    {
        return $this->usuario?->email;
    }

    /**
     * Badge visual del estado para el panel administrativo
     */
    public function getBadgeEstadoAttribute(): array
    {
        return match($this->estado) {
            'pendiente' => [
                'texto' => 'Pendiente',
                'bg' => 'bg-amber-100 text-amber-800 border-amber-200',
                'icono' => 'schedule'
            ],
            'revisado' => [
                'texto' => 'En Revisión / Cotizando',
                'bg' => 'bg-blue-100 text-blue-800 border-blue-200',
                'icono' => 'search_check'
            ],
            'agregado' => [
                'texto' => 'Agregado al Inventario',
                'bg' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                'icono' => 'check_circle'
            ],
            'rechazado' => [
                'texto' => 'Descartado / No Disponible',
                'bg' => 'bg-rose-100 text-rose-800 border-rose-200',
                'icono' => 'cancel'
            ],
            default => [
                'texto' => ucfirst($this->estado ?? 'Pendiente'),
                'bg' => 'bg-slate-100 text-slate-800 border-slate-200',
                'icono' => 'help'
            ]
        };
    }
}