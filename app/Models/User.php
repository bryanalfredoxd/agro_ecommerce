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

    // Relación: Favoritos
    public function favoritos()
    {
        return $this->hasMany(Favorito::class, 'usuario_id');
    }

    // Relación: Pedidos
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'usuario_id');
    }

    public function productosFavoritos()
    {
        return $this->belongsToMany(Producto::class, 'favoritos', 'usuario_id', 'producto_id');
    }

    /* * ========================================
     * SEGURIDAD Y PERMISOS (RBAC)
     * ========================================
     */

    /**
     * Cache en memoria de permisos para evitar decenas de consultas repetitivas.
     */
    protected ?array $permisosCache = null;

    /**
     * Comprueba si el usuario tiene rol de Administrador.
     */
    public function esAdmin(): bool
    {
        return (int)$this->rol_id === 1 || ($this->rol && strtolower($this->rol->nombre) === 'admin');
    }

    /**
     * Comprueba si el usuario pertenece al personal operativo/administrativo de la empresa.
     * Permite acceder al panel administrativo /admin si está activo y tiene rol de empleado
     * o permisos especiales concedidos.
     */
    public function esPersonal(): bool
    {
        if ($this->esAdmin()) {
            return true;
        }

        if (!$this->activo) {
            return false;
        }

        // Si es rol Cliente (ID 4), no es personal a menos que tenga permisos extra específicos
        if ((int)$this->rol_id === 4) {
            return DB::table('permisos_extra_usuario')
                ->where('usuario_id', $this->id)
                ->where('accion', 'permitir')
                ->exists();
        }

        // Cualquier otro rol (Vendedor, Almacenista, Repartidor o Roles personalizados) es personal
        return !empty($this->rol_id);
    }

    /**
     * Determina si el usuario tiene privilegios para visualizar el botón de acceso al panel.
     */
    public function puedeAccederPanelAdmin(): bool
    {
        return $this->esAdmin() || $this->esPersonal();
    }

    /**
     * Retorna el nombre personalizado del botón de acceso al panel según el rol del usuario.
     */
    public function getNombrePanelAttribute(): string
    {
        if ($this->esAdmin()) {
            return 'Panel Administrativo';
        }

        $rolNombre = $this->rol ? mb_strtolower(trim($this->rol->nombre)) : '';

        if (str_contains($rolNombre, 'cajer') || str_contains($rolNombre, 'vendedor') || $this->tienePermiso('crear_venta_manual')) {
            return 'Panel Caja';
        }

        if (str_contains($rolNombre, 'almacen') || str_contains($rolNombre, 'bodega') || str_contains($rolNombre, 'inventario')) {
            return 'Panel Almacén';
        }

        if (str_contains($rolNombre, 'repart') || str_contains($rolNombre, 'delivery')) {
            return 'Panel Reparto';
        }

        if ($this->rol && !empty($this->rol->nombre)) {
            return 'Panel ' . ucfirst($this->rol->nombre);
        }

        return 'Panel Operativo';
    }

    /**
     * Retorna el ícono adecuado para el botón de acceso al panel según el rol del usuario.
     */
    public function getIconoPanelAttribute(): string
    {
        if ($this->esAdmin()) {
            return 'admin_panel_settings';
        }

        $rolNombre = $this->rol ? mb_strtolower(trim($this->rol->nombre)) : '';

        if (str_contains($rolNombre, 'cajer') || str_contains($rolNombre, 'vendedor') || $this->tienePermiso('crear_venta_manual')) {
            return 'point_of_sale';
        }

        if (str_contains($rolNombre, 'almacen') || str_contains($rolNombre, 'bodega') || str_contains($rolNombre, 'inventario')) {
            return 'inventory_2';
        }

        if (str_contains($rolNombre, 'repart') || str_contains($rolNombre, 'delivery')) {
            return 'local_shipping';
        }

        return 'dashboard_customize';
    }

    /**
     * Retorna la ruta inicial directa más adecuada para el usuario al ingresar a su panel.
     */
    public function getRutaPanelAttribute(): string
    {
        if ($this->esAdmin() || $this->tienePermiso('ver_dashboard')) {
            return route('admin.dashboard');
        }

        $rolNombre = $this->rol ? mb_strtolower(trim($this->rol->nombre)) : '';

        // Si es rol de almacén / bodega, priorizar el catálogo e inventario
        if ((str_contains($rolNombre, 'almacen') || str_contains($rolNombre, 'bodega')) && $this->tienePermiso('ver_productos')) {
            return route('admin.productos.index');
        }

        // Si es rol de ventas / mostrador / caja, priorizar terminal POS
        if ($this->tienePermiso('crear_venta_manual')) {
            return route('admin.pos.index');
        }

        // Si es rol de despacho / reparto, priorizar zonas de delivery o pedidos
        if (str_contains($rolNombre, 'repart') || str_contains($rolNombre, 'delivery')) {
            if ($this->tienePermiso('gestionar_zonas_delivery') || $this->tienePermiso('ver_logistica')) {
                return route('admin.zonas_delivery.index');
            }
            if ($this->tieneAlgunPermiso(['ver_pedidos', 'ver_pedidos_despacho', 'ver_pedidos_en_ruta', 'ver_pedidos_todos'])) {
                return route('admin.pedidos.index');
            }
        }

        if ($this->tieneAlgunPermiso(['ver_pedidos', 'ver_pedidos_todos', 'ver_pedidos_por_verificar', 'ver_pedidos_despacho', 'ver_pedidos_en_ruta', 'ver_pedidos_completados', 'ver_pedidos_cancelados', 'gestionar_pagos'])) {
            return route('admin.pedidos.index');
        }

        if ($this->tienePermiso('ver_productos')) {
            return route('admin.productos.index');
        }

        if ($this->tienePermiso('gestionar_caja_diaria')) {
            return route('admin.caja_diaria.index');
        }

        if ($this->tienePermiso('gestionar_zonas_delivery') || $this->tienePermiso('ver_logistica')) {
            return route('admin.zonas_delivery.index');
        }

        if ($this->tienePermiso('ver_facturas')) {
            return route('admin.facturas.index');
        }

        if ($this->tienePermiso('ver_usuarios')) {
            return route('admin.usuarios.index');
        }

        return route('admin.dashboard');
    }

    /**
     * Valida si el usuario actual tiene un permiso específico.
     * Evalúa primero su Rol general y luego las excepciones (permisos_extra_usuario).
     */
    public function tienePermiso($nombre_permiso): bool
    {
        // Los administradores principales tienen acceso completo
        if ($this->esAdmin()) {
            return true;
        }

        // Cargar todos los permisos del usuario en memoria una sola vez
        if ($this->permisosCache === null) {
            $this->cargarPermisosEnMemoria();
        }

        return $this->permisosCache[$nombre_permiso] ?? false;
    }

    /**
     * Valida si el usuario actual tiene al menos uno de los permisos indicados en la lista.
     */
    public function tieneAlgunPermiso(array $nombresPermisos): bool
    {
        if ($this->esAdmin()) {
            return true;
        }

        foreach ($nombresPermisos as $permiso) {
            if ($this->tienePermiso($permiso)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Limpia la caché en memoria de permisos (útil tras actualizar permisos o roles).
     */
    public function limpiarPermisosCache(): void
    {
        $this->permisosCache = null;
    }

    /**
     * Carga de forma eficiente los permisos del rol y excepciones del usuario.
     */
    protected function cargarPermisosEnMemoria(): void
    {
        $this->permisosCache = [];

        // 1. Permisos asignados al Rol
        if ($this->rol_id) {
            $rolPermisos = DB::table('rol_permisos')
                ->join('permisos', 'rol_permisos.permiso_id', '=', 'permisos.id')
                ->where('rol_permisos.rol_id', $this->rol_id)
                ->pluck('permisos.nombre')
                ->toArray();

            foreach ($rolPermisos as $nombre) {
                $this->permisosCache[$nombre] = true;
            }
        }

        // 2. Sobrescrituras manuales (Excepciones)
        $permisosExtra = DB::table('permisos_extra_usuario')
            ->join('permisos', 'permisos_extra_usuario.permiso_id', '=', 'permisos.id')
            ->where('permisos_extra_usuario.usuario_id', $this->id)
            ->select('permisos.nombre', 'permisos_extra_usuario.accion')
            ->get();

        foreach ($permisosExtra as $extra) {
            $this->permisosCache[$extra->nombre] = ($extra->accion === 'permitir');
        }
    }

    public function cajaAsignada()
    {
        return $this->hasOne(CajaFisica::class, 'cajero_asignado_id');
    }

    public function cajasAsignadas()
    {
        return $this->hasMany(CajaFisica::class, 'cajero_asignado_id');
    }
}