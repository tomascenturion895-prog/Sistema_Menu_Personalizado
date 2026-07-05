<?php

namespace App\Http\Controllers\Api\Cliente;

use App\Http\Controllers\Controller;
use App\Http\Resources\PedidoResource;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Version API del historial de pedidos del cliente logueado (equivalente a
 * /mis-pedidos en la web). Protegido con auth:sanctum en routes/api.php.
 */
class PedidoController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return PedidoResource::collection(
            // Igual que la web: SOLO los pedidos del usuario autenticado
            $request->user()->pedidos()
                ->with('items.producto.ingredientes')
                ->latest()
                ->paginate(10)
        );
    }

    public function show(Request $request, Pedido $pedido): PedidoResource
    {
        // Misma proteccion IDOR que el controlador web: un cliente no puede
        // ver el pedido de otro usuario cambiando el id en la URL
        abort_unless($pedido->user_id === $request->user()->id, 403);

        return new PedidoResource($pedido->load('items.producto.ingredientes'));
    }
}
