<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

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
        // Directiva @precio($valor): formatea montos al estilo argentino ($7.500).
        // Definir el formato UNA sola vez aca evita repetir number_format(...) en cada vista
        Blade::directive('precio', function (string $expression): string {
            return "<?php echo '$'.number_format((float) ({$expression}), 0, ',', '.'); ?>";
        });

        // Vista de paginacion propia (retro/editorial) en vez de la generica de
        // Tailwind: se aplica a TODAS las tablas paginadas de una sola vez
        Paginator::defaultView('vendor.pagination.capa8');
    }
}
