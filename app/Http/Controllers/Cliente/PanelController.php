<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Controlador del inicio del cliente (la pantalla que ve al loguearse).
 *
 * Antes esta pagina era un Route::view plano; con el controlador podemos
 * pasarle datos reales, como el ultimo pedido para mostrar su estado.
 */
class PanelController extends Controller
{
    public function inicio(Request $request): View
    {
        return view('dashboard', [
            // El ultimo pedido del usuario logueado (o null si nunca pidio):
            // latest() ordena por fecha de creacion descendente y first() toma el primero
            'ultimoPedido' => $request->user()->pedidos()->latest()->first(),
        ]);
    }
}
