<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ActualizarEstadoPedidoRequest;
use App\Http\Resources\PedidoResource;
use App\Models\Pedido;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Vista de administracion de pedidos por API, equivalente al panel admin
 * Livewire (App\Livewire\Admin\Pedidos). Sin destroy: un pedido nunca se
 * borra, solo cambia de estado (misma regla que el panel).
 */
class PedidoController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return PedidoResource::collection(
            Pedido::with(['user', 'items.producto.ingredientes'])
                ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->string('estado')))
                ->latest()
                ->paginate(15)
        );
    }

    public function show(Pedido $pedido): PedidoResource
    {
        return new PedidoResource($pedido->load(['user', 'items.producto.ingredientes']));
    }

    /**
     * Avanza (o cambia) el estado del pedido. Misma lista blanca de estados
     * que el panel admin Livewire (Pedido::ESTADOS), validada en el FormRequest,
     * mas la misma regla de transicion valida (no se puede "revivir" un pedido
     * cancelado, ni retroceder de "listo" a un estado anterior).
     */
    public function actualizarEstado(ActualizarEstadoPedidoRequest $request, Pedido $pedido): PedidoResource|JsonResponse
    {
        $nuevoEstado = $request->validated('estado');

        if (! $pedido->puedeTransicionarA($nuevoEstado)) {
            return response()->json([
                'mensaje' => 'Ese cambio de estado no es válido para este pedido.',
            ], 422);
        }

        $pedido->update(['estado' => $nuevoEstado]);

        return new PedidoResource($pedido->load(['user', 'items.producto.ingredientes']));
    }
}
