{{-- wire:poll.15s="$refresh": cada 15 segundos vuelve a consultar el pedido y
     re-renderiza el badge. Asi el cliente ve el avance de su pedido en vivo --}}
<span wire:poll.15s="$refresh">
    <x-badge-estado :estado="$pedido->estado" />
</span>
