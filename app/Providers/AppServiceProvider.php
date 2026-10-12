<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Models\ConfiguracionTienda;

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

        // Compartir la configuración global de la tienda con todas las vistas Blade
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
    }
}
