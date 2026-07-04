{{-- Badge de estado de pedido: traduce el estado crudo de la BD a su etiqueta
     legible (desde Pedido::ESTADOS) con el color que le corresponde.
     Se usa en "Mis pedidos" (listado y detalle) y donde haga falta mostrar estado. --}}
@props(['estado'])

@php
    $color = match ($estado) {
        'pendiente' => 'bg-cheddar-100 text-terminal-950',
        'confirmado', 'en_preparacion' => 'bg-brand-100 text-brand-800',
        'listo' => 'bg-exito-100 text-exito-700',
        'cancelado' => 'bg-tomate-100 text-tomate-700',
        default => 'bg-gray-100 text-gray-700',
    };
@endphp

<span {{ $attributes->merge(['class' => "badge {$color}"]) }}>
    {{ \App\Models\Pedido::ESTADOS[$estado] ?? $estado }}
</span>
