<div class="max-w-3xl mx-auto">
    <a href="{{ route('menu.index') }}" wire:navigate class="text-sm text-brand-600 hover:underline">&larr; Volver al menú</a>

    <div class="mt-2 mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">{{ $producto->nombre }}</h2>
        @if ($producto->descripcion)
            <p class="text-gray-600 mt-1">{{ $producto->descripcion }}</p>
        @endif
    </div>

    @php
        // Etiquetas en español para mostrar en lugar del valor crudo de la columna "tipo"
        $etiquetas = [
            'pan' => 'Tipo de pan',
            'medallon' => 'Medallones',
            'papas' => 'Papas fritas',
            'bebida' => 'Bebida',
            'topping' => 'Toppings',
            'salsa' => 'Salsas',
            'extra' => 'Extras',
        ];
    @endphp

    {{-- .tarjeta es la clase de componente definida en app.css (radio, sombra y borde estandar) --}}
    <div class="tarjeta p-6 space-y-6">
        @foreach ($this->ingredientesPorTipo as $tipo => $opciones)
            <div wire:key="grupo-{{ $tipo }}">
                <h3 class="font-medium text-gray-800 mb-2">{{ $etiquetas[$tipo] ?? $tipo }}</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach ($opciones as $ingrediente)
                        <label class="flex items-center justify-between text-sm border border-gray-200 rounded-md px-3 py-2 cursor-pointer hover:bg-gray-50" wire:key="ingrediente-{{ $ingrediente->id }}">
                            <span class="flex items-center">
                                @if (in_array($tipo, ['pan', 'medallon', 'papas', 'bebida']))
                                    {{-- Eleccion unica: todos los radios de este grupo comparten el mismo
                                         indice del array seleccionUnica, asi que marcar uno desmarca el anterior --}}
                                    <input type="radio" wire:model="seleccionUnica.{{ $tipo }}" value="{{ $ingrediente->id }}" class="mr-2">
                                @else
                                    {{-- Eleccion multiple: cada checkbox agrega/quita su id del array seleccionMultiple --}}
                                    <input type="checkbox" wire:model="seleccionMultiple" value="{{ $ingrediente->id }}" class="mr-2 rounded">
                                @endif
                                {{ $ingrediente->nombre }}
                            </span>

                            @if ($ingrediente->precio_extra > 0)
                                <span class="text-gray-500">+${{ number_format($ingrediente->precio_extra, 2) }}</span>
                            @endif
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach

        @if ($error)
            <p class="text-sm text-tomate-500">{{ $error }}</p>
        @endif

        <div class="border-t border-gray-200 pt-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button type="button" wire:click="decrementar" class="w-8 h-8 rounded-full border border-gray-300 text-gray-600 hover:bg-gray-100">-</button>
                <span class="font-medium">{{ $cantidad }}</span>
                <button type="button" wire:click="incrementar" class="w-8 h-8 rounded-full border border-gray-300 text-gray-600 hover:bg-gray-100">+</button>
            </div>

            {{-- $this->precioTotal se recalcula automaticamente cada vez que cambia una seleccion,
                 porque Livewire vuelve a renderizar el componente en cada interaccion --}}
            <span class="precio text-xl">${{ number_format($this->precioTotal, 2) }}</span>
        </div>

        <x-primary-button wire:click="agregarAlPedido" class="w-full justify-center">
            Agregar al pedido
        </x-primary-button>
    </div>
</div>
