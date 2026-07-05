<?php

use App\Http\Middleware\EsAdmin;
use App\Http\Middleware\EsCliente;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias para usar como middleware('admin') / middleware('cliente') en las rutas
        $middleware->alias([
            'admin' => EsAdmin::class,
            'cliente' => EsCliente::class,
        ]);

        // El grupo "api" de Laravel 13 ya no trae throttle por defecto (antes si);
        // sin esto, hasta los endpoints publicos del menu quedaban sin limite de
        // requests. El limiter "api" se define en AppServiceProvider::boot()
        $middleware->api(prepend: [
            'throttle:api',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
