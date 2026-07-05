<?php

namespace App\Livewire\Menu;

use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class MiPedido extends Component
{
    // Observaciones opcionales que el cliente puede dejar para la cocina
    public string $observaciones = '';

    public function render()
    {
        return view('livewire.menu.mi-pedido');
    }

    /**
     * Lee el carrito actual desde la sesion. Es #[Computed] para que la vista
     * siempre vea la version mas reciente sin mantener una copia duplicada.
     */
    #[Computed]
    public function carrito(): array
    {
        return Session::get('carrito', []);
    }

    #[Computed]
    public function total(): float
    {
        // array_reduce recorre el carrito acumulando subtotales (precio x cantidad de cada item)
        return array_reduce(
            $this->carrito,
            fn (float $acumulado, array $item) => $acumulado + ($item['precio_unitario'] * $item['cantidad']),
            0.0
        );
    }

    /**
     * Suma una unidad a un item del carrito (con el mismo tope que en Personalizar).
     */
    public function incrementarItem(int $indice): void
    {
        $carrito = $this->carrito;

        if (isset($carrito[$indice]) && $carrito[$indice]['cantidad'] < Personalizar::MAX_CANTIDAD) {
            $carrito[$indice]['cantidad']++;
            $this->guardarCarrito($carrito);
        }
    }

    /**
     * Resta una unidad a un item del carrito (minimo 1: para sacarlo esta "Quitar").
     */
    public function decrementarItem(int $indice): void
    {
        $carrito = $this->carrito;

        if (isset($carrito[$indice]) && $carrito[$indice]['cantidad'] > 1) {
            $carrito[$indice]['cantidad']--;
            $this->guardarCarrito($carrito);
        }
    }

    /**
     * Quita un item del carrito por su posicion en el array.
     */
    public function quitarItem(int $indice): void
    {
        $carrito = $this->carrito;
        unset($carrito[$indice]);

        // array_values reindexa el array (0,1,2...) para que no queden "huecos" en los indices
        $this->guardarCarrito(array_values($carrito));
    }

    /**
     * Persiste el carrito en la sesion e invalida el cache de los computed.
     *
     * Los #[Computed] de Livewire se cachean durante TODO el request: si mutamos
     * la sesion sin hacer unset(), el render() de esta misma interaccion seguiria
     * mostrando la version vieja del carrito (y el total desactualizado).
     */
    private function guardarCarrito(array $carrito): void
    {
        Session::put('carrito', $carrito);

        unset($this->carrito, $this->total);

        // La navbar escucha este evento para actualizar su contador en vivo
        $this->dispatch('carrito-actualizado');
    }

    /**
     * Convierte el carrito de la sesion en registros reales de la base de datos:
     * un Pedido con sus ItemPedido. Recien aca se persiste todo en MySQL.
     */
    public function confirmarPedido(): void
    {
        if (empty($this->carrito)) {
            return;
        }

        // Antes de cobrar, se recalcula cada precio contra la base de datos ACTUAL:
        // si el admin cambio un precio (o desactivo un producto) mientras el carrito
        // esperaba, el pedido se guarda con los valores vigentes, no con los viejos
        $items = $this->prepararItemsConPreciosActuales();

        if (empty($items)) {
            session()->flash('mensaje', 'Los productos de tu pedido ya no están disponibles.');
            Session::forget('carrito');

            return;
        }

        $total = array_sum(array_map(
            fn (array $item) => $item['precio_unitario'] * $item['cantidad'],
            $items
        ));

        // DB::transaction agrupa todas las escrituras: si algo falla a mitad de camino,
        // se deshace todo (no queda un Pedido sin items, ni items sueltos).
        // El closure DEVUELVE el pedido creado para poder redirigir a su pantalla de exito
        $pedido = DB::transaction(function () use ($items, $total): Pedido {
            $pedido = Pedido::create([
                'user_id' => auth()->id(),
                'total' => $total,
                'estado' => 'pendiente',
                'observaciones' => $this->observaciones,
            ]);

            foreach ($items as $item) {
                ItemPedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'ingredientes_elegidos' => $item['ingredientes_elegidos'],
                ]);
            }

            return $pedido;
        });

        // Vaciamos el carrito de la sesion: el pedido ya vive en la base de datos
        Session::forget('carrito');
        $this->dispatch('carrito-actualizado');

        // El momento mas importante de la compra merece su propia pantalla de exito
        $this->redirect(route('cliente.pedidos.exito', $pedido, absolute: false), navigate: true);
    }

    /**
     * Recorre el carrito y devuelve los items con el precio recalculado desde la BD.
     * Los productos que ya no existen o fueron desactivados se descartan.
     *
     * @return array<int, array{producto_id: int, cantidad: int, precio_unitario: float, ingredientes_elegidos: array<int>}>
     */
    private function prepararItemsConPreciosActuales(): array
    {
        // Una sola consulta para TODOS los productos del carrito (antes se pedia
        // uno por uno dentro del foreach: N consultas para un carrito de N items,
        // justo en el paso critico de confirmar el pedido)
        $productos = Producto::conIngredientesPorIds(collect($this->carrito)->pluck('producto_id'));

        $items = [];

        foreach ($this->carrito as $item) {
            $producto = $productos->get($item['producto_id']);

            if (! $producto || ! $producto->activo) {
                continue;
            }

            // Precio vigente = precio base actual + extras actuales de los ingredientes elegidos
            $extras = $producto->ingredientes
                ->whereIn('id', $item['ingredientes_elegidos'])
                ->sum('precio_extra');

            $items[] = [
                'producto_id' => $producto->id,
                'cantidad' => $item['cantidad'],
                'precio_unitario' => (float) $producto->precio + (float) $extras,
                'ingredientes_elegidos' => $item['ingredientes_elegidos'],
            ];
        }

        return $items;
    }
}
