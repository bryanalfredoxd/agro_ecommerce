<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Permiso;
use App\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $permisosNuevos = [
            [
                'nombre' => 'ver_pedidos_todos',
                'descripcion' => 'Permite ver el listado completo y la pestaña "Todos los Pedidos" sin restricción de estado.'
            ],
            [
                'nombre' => 'ver_pedidos_por_verificar',
                'descripcion' => 'Permite acceder a la pestaña de pedidos en espera de confirmación y auditoría de pago.'
            ],
            [
                'nombre' => 'ver_pedidos_despacho',
                'descripcion' => 'Permite acceder a la pestaña de pedidos pagados y en preparación para empaque.'
            ],
            [
                'nombre' => 'ver_pedidos_en_ruta',
                'descripcion' => 'Permite acceder a la pestaña de pedidos en ruta con motorizado o transportista.'
            ],
            [
                'nombre' => 'ver_pedidos_completados',
                'descripcion' => 'Permite consultar pedidos finalizados y entregados con éxito en tienda o domicilio.'
            ],
            [
                'nombre' => 'ver_pedidos_cancelados',
                'descripcion' => 'Permite ver el historial de pedidos anulados, rechazados o devueltos.'
            ],
            [
                'nombre' => 'ver_cotizaciones',
                'descripcion' => 'Permite consultar el listado de presupuestos y cotizaciones emitidas.'
            ],
            [
                'nombre' => 'gestionar_cotizaciones',
                'descripcion' => 'Permite crear nuevas cotizaciones, cambiar estados, enviar correos y anular.'
            ],
            [
                'nombre' => 'ver_estadisticas_analitica',
                'descripcion' => 'Permite acceder al módulo completo de analítica, reportes y métricas avanzadas.'
            ],
            [
                'nombre' => 'ver_devoluciones',
                'descripcion' => 'Permite consultar el listado e historial de devoluciones y reembolsos procesados.'
            ],
        ];

        foreach ($permisosNuevos as $p) {
            DB::table('permisos')->updateOrInsert(
                ['nombre' => $p['nombre']],
                ['descripcion' => $p['descripcion'], 'creado_at' => now()]
            );
        }

        // Obtener IDs
        $permisosDb = DB::table('permisos')->pluck('id', 'nombre')->toArray();

        // 1. Rol Admin (ID 1): Asignar TODOS los nuevos permisos
        $admin = Role::find(1);
        if ($admin) {
            $admin->permisos()->syncWithoutDetaching(array_values($permisosDb));
        }

        // 2. Rol Repartidor (ID 5): Asignar permisos específicos de despacho y ruta
        $repartidor = Role::find(5);
        if ($repartidor) {
            $permisosRepartidor = array_filter([
                $permisosDb['ver_pedidos_despacho'] ?? null,
                $permisosDb['ver_pedidos_en_ruta'] ?? null,
            ]);
            $repartidor->permisos()->syncWithoutDetaching($permisosRepartidor);
        }

        // 3. Rol Almacenista (ID 3): Asignar despacho y ruta
        $almacenista = Role::find(3);
        if ($almacenista) {
            $permisosAlmacen = array_filter([
                $permisosDb['ver_pedidos_despacho'] ?? null,
                $permisosDb['ver_pedidos_en_ruta'] ?? null,
            ]);
            $almacenista->permisos()->syncWithoutDetaching($permisosAlmacen);
        }

        // 4. Rol Vendedor (ID 2): Asignar cotizaciones, pedidos todos, verificación y completados
        $vendedor = Role::find(2);
        if ($vendedor) {
            $permisosVendedor = array_filter([
                $permisosDb['ver_cotizaciones'] ?? null,
                $permisosDb['gestionar_cotizaciones'] ?? null,
                $permisosDb['ver_pedidos_todos'] ?? null,
                $permisosDb['ver_pedidos_por_verificar'] ?? null,
                $permisosDb['ver_pedidos_completados'] ?? null,
                $permisosDb['ver_devoluciones'] ?? null,
            ]);
            $vendedor->permisos()->syncWithoutDetaching($permisosVendedor);
        }
    }

    public function down(): void
    {
        $nombres = [
            'ver_pedidos_todos',
            'ver_pedidos_por_verificar',
            'ver_pedidos_despacho',
            'ver_pedidos_en_ruta',
            'ver_pedidos_completados',
            'ver_pedidos_cancelados',
            'ver_cotizaciones',
            'gestionar_cotizaciones',
            'ver_estadisticas_analitica',
            'ver_devoluciones',
        ];

        $ids = DB::table('permisos')->whereIn('nombre', $nombres)->pluck('id');
        DB::table('rol_permisos')->whereIn('permiso_id', $ids)->delete();
        DB::table('permisos_extra_usuario')->whereIn('permiso_id', $ids)->delete();
        DB::table('permisos')->whereIn('id', $ids)->delete();
    }
};
