<?php

namespace App\Policies;

use App\Models\Pedido;
use App\Models\User;

/**
 * Centraliza la regla de "es dueño del pedido" que antes estaba repetida
 * (abort_unless($pedido->user_id === ...)) en 5 lugares distintos:
 * PedidoController@show/exito/cancelar/repetir (web) y Api\Cliente\PedidoController@show.
 */
class PedidoPolicy
{
    /**
     * Un cliente solo puede ver (o actuar sobre) SUS PROPIOS pedidos.
     * Se usa para consultar el detalle, cancelar y repetir un pedido.
     *
     * Esta policy es SOLO para el lado cliente: el panel admin (web y API)
     * consulta y modifica pedidos de cualquier usuario a proposito, y usa sus
     * propios controladores en vez de este Gate::authorize('view', ...).
     */
    public function view(User $user, Pedido $pedido): bool
    {
        return $pedido->user_id === $user->id;
    }
}
