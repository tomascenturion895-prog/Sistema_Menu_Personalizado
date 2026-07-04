<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="eyebrow">// mis pedidos / detalle</span>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Pedido <span class="font-mono">{{ $pedido->numero }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-mensaje-flash />

            <a href="{{ route('cliente.pedidos.index') }}" wire:navigate class="text-sm text-brand-600 hover:underline">&larr; Volver a mis pedidos</a>

            <div class="tarjeta shadow-retro mt-4 divide-y divide-gray-100 overflow-hidden">
                {{-- Cabecera del ticket: fecha y estado --}}
                <div class="p-4 flex items-center justify-between bg-brand-50">
                    <span class="text-sm text-gray-600">{{ $pedido->created_at->format('d/m/Y H:i') }} hs</span>
                    {{-- Badge en vivo: se actualiza solo cuando la cocina cambia el estado --}}
                    <livewire:cliente.estado-pedido :pedido="$pedido" />
                </div>

                {{-- Detalle de cada item con su personalizacion --}}
                @foreach ($pedido->items as $item)
                    <div class="p-4 flex items-start justify-between gap-4">
                        <div>
                            <span class="font-medium text-gray-900">{{ $item->cantidad }}x {{ $item->producto->nombre }}</span>

                            @if (! empty($item->ingredientes_elegidos))
                                <p class="text-sm text-gray-500 mt-1">
                                    {{-- Los ids elegidos se traducen a nombres consultando los ingredientes del producto --}}
                                    {{ $item->producto->ingredientes->whereIn('id', $item->ingredientes_elegidos)->pluck('nombre')->implode(', ') }}
                                </p>
                            @else
                                <p class="text-sm text-gray-500 mt-1">Receta de la casa</p>
                            @endif
                        </div>

                        <span class="precio">@precio($item->precio_unitario * $item->cantidad)</span>
                    </div>
                @endforeach

                @if ($pedido->observaciones)
                    <div class="p-4 text-sm text-gray-600">
                        <span class="font-mono text-xs text-gray-400">obs:</span> {{ $pedido->observaciones }}
                    </div>
                @endif

                {{-- Total del ticket --}}
                <div class="p-4 flex items-center justify-between bg-terminal-950">
                    <span class="font-semibold text-white">Total</span>
                    <span class="precio text-xl text-white">@precio($pedido->total)</span>
                </div>
            </div>

            {{-- Acciones sobre el pedido --}}
            <div class="flex flex-wrap gap-3 mt-6">
                {{-- Volver a pedirlo: recarga los items en el carrito con precios actuales --}}
                <form method="POST" action="{{ route('cliente.pedidos.repetir', $pedido) }}">
                    @csrf
                    <x-primary-button>Pedir de nuevo</x-primary-button>
                </form>

                {{-- Cancelar: solo mientras la cocina no lo haya tomado --}}
                @if ($pedido->esCancelable())
                    <form method="POST" action="{{ route('cliente.pedidos.cancelar', $pedido) }}"
                        onsubmit="return confirm('¿Seguro que querés cancelar este pedido?');">
                        @csrf
                        @method('PATCH')
                        <x-danger-button>Cancelar pedido</x-danger-button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
