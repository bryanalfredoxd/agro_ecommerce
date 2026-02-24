<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // 1. Apuntar a tu tabla personalizada
    protected $table = 'usuarios';

    // 2. Mapear los timestamps en español
    const CREATED_AT = 'creado_at';
    const UPDATED_AT = 'actualizado_at';

    // 3. Campos que se pueden escribir (Mass Assignment)
    protected $fillable = [
        'rol_id',
        'nombre',
        'apellido',
        'email',
        'password_hash',
        'telefono',
        'documento_identidad',
        'tipo_cliente',
        'activo',
    ];

    // 4. Ocultar datos sensibles
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'password_hash' => 'hashed',
    ];

    // 5. ¡CRUCIAL! Sobrescribir el nombre del campo password
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // 6. Sobrescribir el identificador de autenticación
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /* * ========================================
     * RELACIONES
     * ========================================
     */

    // Relación: Un usuario tiene muchas direcciones
    public function direcciones()
    {
        return $this->hasMany(DireccionUsuario::class, 'usuario_id');
    }

    // Relación: Un usuario pertenece a un Rol
    public function rol()
    {
        // Si tu modelo de roles se llama Role, déjalo así. Si se llama Rol, cámbialo a Rol::class
        return $this->belongsTo(Role::class, 'rol_id');
    }

    // ¡NUEVA RELACIÓN! Un usuario (cliente) tiene muchos pedidos
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'usuario_id');
    }
}