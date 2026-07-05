<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Controlador del historial de pedidos del cliente ("Mis pedidos").
 *
 * MVC clasico completo:
 *  - Modelo: Pedido (con sus ItemPedido y Productos relacionados)
 *  - Vista: resources/views/cliente/pedidos/index.blade.php y show.blade.php
 *  - Controlador: esta clase, que consulta y filtra por el usuario logueado
 */
class PedidoController extends Controller
{
    /**
     * Lista los pedidos del usuario logueado, del mas reciente al mas viejo.
     * Ademas destaca el ultimo pedido aparte, para que la vista principal
     * responda primero "como va mi pedido" y el historial completo quede abajo.
     */
    public function index(Request $request): View
    {
        $pedidoActual = $request->user()->pedidos()->latest()->first();

        return view('cliente.pedidos.index', [
            'pedidoActual' => $pedidoActual,

            // Se piden SOLO los pedidos del usuario autenticado (nunca los de otros),
            // con eager loading anidado para evitar el problema N+1. Se excluye el
            // pedido actual: ya se destaca arriba, no hace falta repetirlo en la lista
            'pedidos' => $request->user()->pedidos()
                ->when($pedidoActual, fn ($query) => $query->whereKeyNot($pedidoActual->id))
                ->with('items.producto')
                ->latest()
                ->paginate(10),
        ]);
    }

    /**
     * Muestra el detalle de un pedido puntual.
     */
    public function show(Request $request, Pedido $pedido): View
    {
        // Autorizacion: un cliente solo puede ver SUS pedidos. Si intenta abrir
        // el pedido de otro usuario (cambiando el id de la URL), recibe un 403
        abort_unless($pedido->user_id === $request->user()->id, 403);

        return view('cliente.pedidos.show', [
            // La vista traduce ingredientes_elegidos (ids) a nombres leyendo
            // producto.ingredientes: si no se precarga aca, cada item del pedido
            // dispara su propia consulta al renderizar (N+1 real, ya detectado)
            'pedido' => $pedido->load('items.producto.ingredientes'),
        ]);
    }

    /**
     * Pantalla de exito: se muestra una sola vez, justo despues de confirmar.
     * El momento mas importante de la compra merece su propia pagina.
     */
    public function exito(Request $request, Pedido $pedido): View
    {
        abort_unless($pedido->user_id === $request->user()->id, 403);

        return view('cliente.pedidos.exito', [
            'pedido' => $pedido,
        ]);
    }

    /**
     * Cancela un pedido, solo si sigue pendiente (la cocina aun no lo tomo).
     */
    public function cancelar(Request $request, Pedido $pedido): RedirectResponse
    {
        // Misma proteccion IDOR que en show(): solo el dueño puede cancelar
        abort_unless($pedido->user_id === $request->user()->id, 403);

        // Regla de negocio: una vez que la cocina lo confirmo, ya no se puede cancelar
        abort_unless($pedido->esCancelable(), 403, 'Este pedido ya está en preparación y no se puede cancelar.');

        $pedido->update(['estado' => 'cancelado']);

        return redirect()
            ->route('cliente.pedidos.show', $pedido)
            ->with('mensaje', 'Tu pedido fue cancelado.');
    }

    /**
     * Vuelve a cargar en el carrito los items de un pedido anterior,
     * con los precios e ingredientes VIGENTES (no los del pedido viejo).
     */
    public function repetir(Request $request, Pedido $pedido): RedirectResponse
    {
        abort_unless($pedido->user_id === $request->user()->id, 403);

        $carrito = session('carrito', []);
        $agregados = 0;

        // Una sola consulta para TODOS los productos del pedido (antes se pedia
        // uno por uno dentro del foreach: N consultas para un pedido de N items)
        $productos = Producto::with('ingredientes')
            ->whereIn('id', $pedido->items->pluck('producto_id'))
            ->get()
            ->keyBy('id');

        foreach ($pedido->items as $item) {
            $producto = $productos->get($item->producto_id);

            // Los productos eliminados o desactivados desde aquel pedido se saltean
            if (! $producto || ! $producto->activo) {
                continue;
            }

            // Se conservan solo los ingredientes que el producto sigue ofreciendo
            $ingredientesVigentes = $producto->ingredientes
                ->whereIn('id', $item->ingredientes_elegidos ?? [])
                ->where('activo', true);

            $carrito[] = [
                'producto_id' => $producto->id,
                'nombre' => $producto->nombre,
                'cantidad' => $item->cantidad,
                // Precio recalculado al valor de HOY, no al del pedido original
                'precio_unitario' => (float) $producto->precio + (float) $ingredientesVigentes->sum('precio_extra'),
                'ingredientes_elegidos' => $ingredientesVigentes->pluck('id')->values()->all(),
                'ingredientes_nombres' => $ingredientesVigentes->pluck('nombre')->values()->all() ?: ['Receta de la casa'],
            ];

            $agregados++;
        }

        if ($agregados === 0) {
            return redirect()
                ->route('cliente.pedidos.show', $pedido)
                ->with('mensaje', 'Los productos de este pedido ya no están disponibles.');
        }

        session(['carrito' => $carrito]);

        return redirect()
            ->route('menu.mi-pedido')
            ->with('mensaje', 'Cargamos tu pedido anterior en el carrito. Revisalo y confirmá.');
    }
}
