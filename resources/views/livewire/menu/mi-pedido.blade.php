<div class="max-w-3xl mx-auto px-4 sm:px-6 py-10">
    <a href="{{ route('menu.index') }}" wire:navigate class="text-sm text-brand-600 hover:underline">&larr; Seguir eligiendo</a>

    <div class="mt-3 mb-6">
        <span class="eyebrow">// mi pedido</span>
        <h2 class="text-3xl font-semibold text-gray-900">Mi pedido</h2>
    </div>

    @if (empty($this->carrito))
        <div class="tarjeta p-8 text-center text-gray-500">
            Tu pedido está vacío. ¡Andá al menú y armá tu hamburguesa!
        </div>
    @else
        <div class="tarjeta divide-y divide-gray-100">
            @foreach ($this->carrito as $indice => $item)
                {{-- Usamos el indice del array como clave: no hay id de BD porque el carrito vive en la sesion --}}
                <div class="p-4 flex items-start justify-between gap-4" wire:key="item-{{ $indice }}">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-gray-900">{{ $item['nombre'] }}</span>
                            <span class="text-sm text-gray-500">x{{ $item['cantidad'] }}</span>
                        </div>

                        @if (!empty($item['ingredientes_nombres']))
                            <p class="text-sm text-gray-500 mt-1">
                                {{ implode(', ', $item['ingredientes_nombres']) }}
                            </p>
                        @endif
                    </div>

                    <div class="text-right">
                        <span class="precio">${{ number_format($item['precio_unitario'] * $item['cantidad'], 0, ',', '.') }}</span>

                        <button wire:click="quitarItem({{ $indice }})" class="block mt-1 text-xs text-tomate-500 hover:text-tomate-700">
                            Quitar
                        </button>
                    </div>
                </div>
            @endforeach

            <div class="p-4 flex items-center justify-between bg-gray-50">
                <span class="font-medium text-gray-800">Total</span>
                <span class="precio text-xl">${{ number_format($this->total, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="mt-6">
            <x-input-label for="observaciones" value="Observaciones para la cocina (opcional)" />
            <textarea wire:model="observaciones" id="observaciones" rows="2"
                class="block mt-1 w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm"
                placeholder="Ej: sin sal en las papas, cortar la hamburguesa al medio..."></textarea>
        </div>

        <x-primary-button wire:click="confirmarPedido" class="w-full justify-center mt-6">
            Confirmar pedido
        </x-primary-button>
    @endif
</div>
