<div class="max-w-3xl mx-auto px-4 sm:px-6 py-10">
    <x-mensaje-flash />

    <a href="{{ route('menu.index') }}" wire:navigate class="text-sm text-brand-600 hover:underline">&larr; Seguir eligiendo</a>

    <div class="mt-3 mb-6">
        <span class="eyebrow">// carrito</span>
        <h2 class="text-3xl font-semibold text-gray-900">Carrito</h2>
    </div>

    @if (empty($this->carrito))
        <div class="tarjeta shadow-retro p-10 text-center">
            <p class="font-display text-xl uppercase text-terminal-950">Tu carrito está vacío</p>
            <p class="text-sm text-gray-500 mt-2">Andá al menú y elegí tu primera burger.</p>
            <a href="{{ route('menu.index') }}" wire:navigate
                class="btn-retro mt-5 px-5 py-2.5 bg-brand-500 text-terminal-950 text-sm">
                Ir al menú →
            </a>
        </div>
    @else
        <div class="tarjeta shadow-retro divide-y divide-gray-100 overflow-hidden">
            @foreach ($this->carrito as $indice => $item)
                {{-- Usamos el indice del array como clave: no hay id de BD porque el carrito vive en la sesion --}}
                <div class="p-4 flex items-start justify-between gap-4" wire:key="item-{{ $indice }}">
                    <div class="flex-1">
                        <span class="font-medium text-gray-900">{{ $item['nombre'] }}</span>

                        @if (!empty($item['ingredientes_nombres']))
                            <p class="text-sm text-gray-500 mt-1">
                                {{ implode(', ', $item['ingredientes_nombres']) }}
                            </p>
                        @endif

                        {{-- Controles de cantidad: se puede ajustar sin rearmar la hamburguesa --}}
                        {{-- wire:loading.attr="disabled" scopeado con wire:target: evita clicks
                             repetidos sobre EL MISMO item mientras su request esta en curso --}}
                        <div class="flex items-center gap-2.5 mt-2">
                            <button wire:click="decrementarItem({{ $indice }})" wire:loading.attr="disabled" wire:target="decrementarItem({{ $indice }}), incrementarItem({{ $indice }}), quitarItem({{ $indice }})" class="w-7 h-7 rounded-md border-2 border-gray-300 text-gray-600 hover:border-terminal-950 transition text-sm">&minus;</button>
                            <span class="font-mono font-semibold text-sm w-5 text-center">{{ $item['cantidad'] }}</span>
                            <button wire:click="incrementarItem({{ $indice }})" wire:loading.attr="disabled" wire:target="decrementarItem({{ $indice }}), incrementarItem({{ $indice }}), quitarItem({{ $indice }})" class="w-7 h-7 rounded-md border-2 border-gray-300 text-gray-600 hover:border-terminal-950 transition text-sm">+</button>
                        </div>
                    </div>

                    <div class="text-right">
                        <span class="precio">@precio($item['precio_unitario'] * $item['cantidad'])</span>

                        <button wire:click="quitarItem({{ $indice }})" wire:loading.attr="disabled" wire:target="decrementarItem({{ $indice }}), incrementarItem({{ $indice }}), quitarItem({{ $indice }})" class="block mt-1 text-xs text-tomate-700 hover:underline">
                            Quitar
                        </button>
                    </div>
                </div>
            @endforeach

            <div class="p-4 flex items-center justify-between bg-gray-50">
                <span class="font-medium text-gray-800">Total</span>
                <span class="precio text-xl">@precio($this->total)</span>
            </div>
        </div>

        <div class="mt-6">
            <x-input-label for="observaciones" value="Observaciones para la cocina (opcional)" />
            <textarea wire:model="observaciones" id="observaciones" rows="2"
                class="block mt-1 w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm"
                placeholder="Ej: sin sal en las papas, cortar la hamburguesa al medio..."></textarea>
        </div>

        {{-- Confirmar es la accion mas importante: se deshabilita mientras procesa
             para dar feedback y evitar doble click (que crearia dos pedidos) --}}
        <x-primary-button wire:click="confirmarPedido" wire:loading.attr="disabled" wire:target="confirmarPedido"
            class="w-full justify-center mt-6 disabled:opacity-50">
            <span wire:loading.remove wire:target="confirmarPedido">Confirmar pedido</span>
            <span wire:loading wire:target="confirmarPedido">Confirmando tu pedido…</span>
        </x-primary-button>
    @endif
</div>
