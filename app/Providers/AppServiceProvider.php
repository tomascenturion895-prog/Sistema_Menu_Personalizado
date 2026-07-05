<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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

        // Limite general de la API (60 req/min por usuario logueado, o por IP si
        // es un endpoint publico como el menu). Usado por el middleware
        // "throttle:api" que se prepend-ea al grupo "api" en bootstrap/app.php
        RateLimiter::for('api', function ($request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Politica de contraseñas para TODO el sistema (registro web y donde se
        // use Password::defaults()): minimo 8 caracteres, mezclando letras y
        // numeros, para que no se puedan crear cuentas con contraseñas debiles
        Password::defaults(fn (): Password => Password::min(8)->letters()->numbers());
    }
}
