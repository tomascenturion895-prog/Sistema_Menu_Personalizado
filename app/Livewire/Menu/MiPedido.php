<?php

namespace App\Livewire\Menu;

use App\Models\ItemPedido;
use App\Models\Pedido;
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
     * Quita un item del carrito por su posicion en el array.
     */
    public function quitarItem(int $indice): void
    {
        $carrito = $this->carrito;
        unset($carrito[$indice]);

        // array_values reindexa el array (0,1,2...) para que no queden "huecos" en los indices
        Session::put('carrito', array_values($carrito));
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

        // DB::transaction agrupa todas las escrituras: si algo falla a mitad de camino,
        // se deshace todo (no queda un Pedido sin items, ni items sueltos)
        DB::transaction(function () {
            $pedido = Pedido::create([
                'user_id' => auth()->id(),
                'total' => $this->total,
                'estado' => 'pendiente',
                'observaciones' => $this->observaciones,
            ]);

            foreach ($this->carrito as $item) {
                ItemPedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'ingredientes_elegidos' => $item['ingredientes_elegidos'],
                ]);
            }
        });

        // Vaciamos el carrito de la sesion: el pedido ya vive en la base de datos
        Session::forget('carrito');

        session()->flash('mensaje', '¡Tu pedido fue confirmado! Lo estamos preparando.');

        $this->redirect(route('menu.index', absolute: false), navigate: true);
    }
}
