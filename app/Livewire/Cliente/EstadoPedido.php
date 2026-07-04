<?php

namespace App\Livewire\Cliente;

use App\Models\Pedido;
use Livewire\Component;

/**
 * Badge de estado "en vivo" para el detalle del pedido del cliente.
 *
 * La vista usa wire:poll: cada 15 segundos Livewire re-renderiza el componente,
 * y como las propiedades de tipo modelo se re-consultan desde la base en cada
 * request, el cliente ve el cambio de estado (ej: "En preparación" -> "Listo")
 * sin recargar la pagina, apenas la cocina lo actualiza.
 */
class EstadoPedido extends Component
{
    public Pedido $pedido;

    public function render()
    {
        return view('livewire.cliente.estado-pedido');
    }
}
