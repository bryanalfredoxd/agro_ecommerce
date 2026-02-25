<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB; // <-- IMPORTANTE: Agregado para hacer consultas crudas rápidas

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
        return $this->belongsTo(Role::class, 'rol_id'); // Asegúrate que tu modelo se llame Role o Rol
    }

    /* * ========================================
     * SEGURIDAD Y PERMISOS (RBAC)
     * ========================================
     */

    /**
     * Valida si el usuario actual tiene un permiso específico.
     * Evalúa primero excepciones (permisos_extra_usuario) y luego su Rol general.
     */
    public function tienePermiso($nombre_permiso)
    {
        // 1. Buscamos el ID del permiso solicitado
        $permiso = DB::table('permisos')
                    ->where('nombre', $nombre_permiso)
                    ->first();

        if (!$permiso) {
            return false; // El permiso no existe en la tabla
        }

        // 2. Revisamos si el usuario tiene una EXCEPCIÓN manual
        $permisoExtra = DB::table('permisos_extra_usuario')
                        ->where('usuario_id', $this->id)
                        ->where('permiso_id', $permiso->id)
                        ->first();

        if ($permisoExtra) {
            // Si la acción dice 'permitir', devuelve true. Si dice 'denegar', devuelve false.
            return $permisoExtra->accion === 'permitir';
        }

        // 3. Si no hay excepciones, verificamos si su ROL tiene asignado el permiso
        $tienePermisoPorRol = DB::table('rol_permisos')
                                ->where('rol_id', $this->rol_id)
                                ->where('permiso_id', $permiso->id)
                                ->exists();

        return $tienePermisoPorRol;
    }
}