{{-- Barra flotante del pedido en curso: acompaña al cliente en todo el flujo de compra
     (menu y personalizar). Lee el carrito directo de la sesion, asi cualquier vista
     puede incluirla sin logica extra. Solo aparece si hay items cargados. --}}
@php
    $itemsCarrito = collect(session('carrito', []));
    $cantidadItems = $itemsCarrito->count();
    $totalCarrito = $itemsCarrito->sum(fn (array $item) => $item['precio_unitario'] * $item['cantidad']);
@endphp

@if ($cantidadItems > 0)
    <div class="fixed bottom-0 inset-x-0 z-20 bg-terminal-950 border-t-2 border-terminal-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-4">
            <div class="text-sm text-terminal-300">
                <span class="font-mono font-semibold text-white">{{ $cantidadItems }}</span>
                {{ $cantidadItems === 1 ? 'producto' : 'productos' }}
                · <span class="precio text-white">@precio($totalCarrito)</span>
            </div>

            <a href="{{ route('menu.mi-pedido') }}" wire:navigate
                class="btn-retro border-white/20 px-5 py-2 bg-brand-500 text-terminal-950 text-sm">
                Ver mi pedido →
            </a>
        </div>
    </div>
@endif
