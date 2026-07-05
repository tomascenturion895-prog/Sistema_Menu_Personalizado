<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EsCliente
{
    /**
     * Bloquea las rutas de compra (menu, carrito, mis pedidos) para un admin:
     * su cuenta es 100% de gestion, no compra hamburguesas para si mismo.
     * Un invitado o cliente normal pasa de largo ($request->user() es null
     * para un invitado, y "?->" evita el error al llamar esCliente() sobre null).
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->esAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
