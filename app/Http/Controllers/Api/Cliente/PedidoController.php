<?php

namespace App\Http\Controllers\Api\Cliente;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePedidoRequest;
use App\Http\Resources\PedidoResource;
use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

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

    public function show(Pedido $pedido): PedidoResource
    {
        // Misma PedidoPolicy que usa el controlador web: un cliente no puede
        // ver el pedido de otro usuario cambiando el id en la URL
        Gate::authorize('view', $pedido);

        return new PedidoResource($pedido->load('items.producto.ingredientes'));
    }

    /**
     * Equivalente API de MiPedido@confirmarPedido: recibe los items directo
     * en el body (la API no tiene un carrito de sesion) y los persiste con
     * los precios e ingredientes VIGENTES, no los que mando el cliente.
     */
    public function store(StorePedidoRequest $request): JsonResponse
    {
        // Candado por usuario: un reintento automatico del cliente API (o dos
        // requests casi simultaneas) no debe poder crear el mismo pedido dos veces
        $candado = Cache::lock('checkout:'.$request->user()->id, 10);

        if (! $candado->get()) {
            return response()->json([
                'mensaje' => 'Ya hay una confirmación de pedido en curso, esperá un momento.',
            ], 409);
        }

        try {
            return $this->confirmar($request);
        } finally {
            $candado->release();
        }
    }

    private function confirmar(StorePedidoRequest $request): JsonResponse
    {
        $itemsSolicitados = $request->validated('items');

        $productos = Producto::conIngredientesPorIds(
            collect($itemsSolicitados)->pluck('producto_id')
        );

        $items = [];

        foreach ($itemsSolicitados as $itemSolicitado) {
            $producto = $productos->get($itemSolicitado['producto_id']);

            // calcularItemVigente() descarta el producto (null) si esta desactivado,
            // y filtra los ingredientes que se quedaron sin stock o ya no existen
            $vigente = $producto?->calcularItemVigente($itemSolicitado['ingredientes_elegidos'] ?? []);

            if (! $vigente) {
                continue;
            }

            $items[] = [
                'producto_id' => $producto->id,
                // Snapshot del nombre AL MOMENTO DE CONFIRMAR: si el admin lo
                // renombra despues, este pedido no debe mostrar el nombre nuevo
                'nombre_producto' => $producto->nombre,
                'cantidad' => $itemSolicitado['cantidad'],
                'precio_unitario' => $vigente['precio_unitario'],
                'ingredientes_elegidos' => $vigente['ingredientes']->pluck('id')->values()->all(),
            ];
        }

        if (empty($items)) {
            return response()->json([
                'mensaje' => 'Los productos elegidos ya no están disponibles.',
            ], 422);
        }

        $total = array_sum(array_map(
            fn (array $item) => $item['precio_unitario'] * $item['cantidad'],
            $items
        ));

        $pedido = DB::transaction(function () use ($request, $items, $total): Pedido {
            $pedido = Pedido::create([
                'user_id' => $request->user()->id,
                'total' => $total,
                'estado' => 'pendiente',
                'observaciones' => $request->validated('observaciones'),
            ]);

            foreach ($items as $item) {
                ItemPedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $item['producto_id'],
                    'nombre_producto' => $item['nombre_producto'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'ingredientes_elegidos' => $item['ingredientes_elegidos'],
                ]);
            }

            return $pedido;
        });

        return (new PedidoResource($pedido->load('items.producto.ingredientes')))
            ->response()
            ->setStatusCode(201);
    }
}
