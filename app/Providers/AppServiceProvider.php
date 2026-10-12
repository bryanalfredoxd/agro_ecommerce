<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Models\ConfiguracionTienda;
use App\Models\AuditoriaLog;
use App\Observers\AuditObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();

        // 1. Compartir la configuración global de la tienda con todas las vistas Blade
        View::composer('*', function ($view) {
            static $configTienda = null;
            if ($configTienda === null) {
                try {
                    $configTienda = ConfiguracionTienda::first();
                } catch (\Throwable $e) {
                    $configTienda = null;
                }
            }
            $view->with('configTienda', $configTienda);
        });

        // 2. Registro del observador universal de auditoría para captura automática de cambios en BD
        $modelosAuditables = [
            \App\Models\Pedido::class,
            \App\Models\Pago::class,
            \App\Models\Factura::class,
            \App\Models\Devolucion::class,
            \App\Models\Producto::class,
            \App\Models\InventarioLote::class,
            \App\Models\Categoria::class,
            \App\Models\Marca::class,
            \App\Models\User::class,
            \App\Models\Role::class,
            \App\Models\PermisoExtraUsuario::class,
            \App\Models\SesionCaja::class,
            \App\Models\MovimientoCaja::class,
            \App\Models\CajaFisica::class,
            \App\Models\ConfiguracionTienda::class,
            \App\Models\TasaCambio::class,
            \App\Models\CuentaBancaria::class,
            \App\Models\ZonaDelivery::class,
            \App\Models\HorarioSemanal::class,
            \App\Models\Cotizacion::class,
            \App\Models\SolicitudProducto::class,
            \App\Models\HistoricoPrecioProducto::class,
            \App\Models\DireccionUsuario::class,
        ];

        foreach ($modelosAuditables as $modelo) {
            $modelo::observe(AuditObserver::class);
        }

        // 3. Auditoría de inicio y cierre de sesión de usuarios
        Event::listen(Login::class, function ($event) {
            try {
                if ($event->user) {
                    AuditoriaLog::registrar(
                        evento: 'INICIO_SESION',
                        tabla: 'usuarios',
                        registroId: $event->user->id,
                        valoresAnteriores: null,
                        valoresNuevos: [
                            'email' => $event->user->email,
                            'rol' => $event->user->rol->nombre ?? 'N/A'
                        ],
                        descripcion: "Inicio de sesión exitoso del usuario {$event->user->nombre} {$event->user->apellido} ({$event->user->email})",
                        usuarioId: $event->user->id
                    );
                }
            } catch (\Throwable $e) {
                // Silenciar para no interrumpir el flujo de autenticación
            }
        });

        Event::listen(Logout::class, function ($event) {
            try {
                if ($event->user) {
                    AuditoriaLog::registrar(
                        evento: 'CIERRE_SESION',
                        tabla: 'usuarios',
                        registroId: $event->user->id,
                        valoresAnteriores: null,
                        valoresNuevos: null,
                        descripcion: "Cierre de sesión del usuario {$event->user->nombre} {$event->user->apellido}",
                        usuarioId: $event->user->id
                    );
                }
            } catch (\Throwable $e) {
                // Silenciar
            }
        });
    }
}
