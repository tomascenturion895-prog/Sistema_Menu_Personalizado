<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario no esta logueado o no tiene rol "admin", se le niega el acceso (403)
        if (! $request->user() || ! $request->user()->esAdmin()) {
            abort(403, 'No tenes permiso para acceder a esta sección.');
        }

        // Si pasa la verificacion, la request continua su camino normal
        return $next($request);
    }
}
