@php
    use App\Livewire\Menu\Personalizar;
@endphp

<div class="max-w-3xl mx-auto px-4 sm:px-6 py-10 pb-24">
    <a href="{{ route('menu.index') }}" wire:navigate class="text-sm text-brand-600 hover:underline">&larr; Volver al menú</a>

    <div class="mt-3 mb-6">
        <span class="eyebrow">// personalizar</span>
        <h2 class="font-display text-3xl sm:text-4xl uppercase text-terminal-900">{{ $producto->nombre }}</h2>
        @if ($producto->descripcion)
            <p class="text-gray-600 mt-2">{{ $producto->descripcion }}</p>
        @endif
    </div>

    {{-- Foto del producto (solo si el admin cargo una: aca el fallback seria redundante) --}}
    @if ($producto->imagen)
        <div class="tarjeta overflow-hidden mb-6">
            <x-foto-producto :producto="$producto" alto="h-48" />
        </div>
    @endif

    {{-- Tarjeta principal estilo cartel retro, consistente con el resto de la marca --}}
    <div class="tarjeta shadow-retro p-6 space-y-7">
        @foreach ($this->ingredientesPorTipo as $tipo => $opciones)
            <div wire:key="grupo-{{ $tipo }}">
                {{-- Encabezado del grupo con su regla de seleccion visible --}}
                <div class="flex items-baseline justify-between mb-2">
                    <h3 class="font-semibold text-gray-900">{{ Personalizar::ETIQUETAS[$tipo] ?? $tipo }}</h3>

                    @if (array_key_exists($tipo, Personalizar::LIMITES_MULTIPLES))
                        {{-- Contador en vivo: cuantas opciones lleva marcadas de su tope --}}
                        <span class="font-mono text-xs text-gray-400">
                            {{ $this->contarSeleccionadosDeTipo($tipo) }} de {{ Personalizar::LIMITES_MULTIPLES[$tipo] }} máx.
                        </span>
                    @else
                        <span class="font-mono text-xs text-gray-400">elegí 1</span>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach ($opciones as $ingrediente)
                        <label class="flex items-center justify-between text-sm border border-gray-200 rounded-md px-3 py-2.5 cursor-pointer hover:border-brand-500 transition" wire:key="ingrediente-{{ $ingrediente->id }}">
                            <span class="flex items-center">
                                @if (! array_key_exists($tipo, Personalizar::LIMITES_MULTIPLES))
                                    {{-- Eleccion unica: todos los radios del grupo comparten el mismo indice
                                         del array seleccionUnica, asi marcar uno desmarca el anterior.
                                         .live sincroniza al instante para que el precio se actualice en vivo --}}
                                    <input type="radio" wire:model.live="seleccionUnica.{{ $tipo }}" value="{{ $ingrediente->id }}" class="mr-2 text-brand-500 focus:ring-brand-500">
                                @else
                                    {{-- Eleccion multiple: cada checkbox agrega/quita su id del array.
                                         .live permite que el tope se controle en el momento (updatedSeleccionMultiple) --}}
                                    <input type="checkbox" wire:model.live="seleccionMultiple" value="{{ $ingrediente->id }}" class="mr-2 rounded text-brand-500 focus:ring-brand-500">
                                @endif
                                {{ $ingrediente->nombre }}
                            </span>

                            @if ($ingrediente->precio_extra > 0)
                                <span class="font-mono text-xs text-gray-500">+@precio($ingrediente->precio_extra)</span>
                            @endif
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach

        @if ($error)
            <p class="text-sm text-tomate-500 bg-tomate-100 border border-tomate-500/30 rounded-md px-3 py-2">{{ $error }}</p>
        @endif

        <div class="border-t border-gray-200 pt-5 flex items-center justify-between">
            <div>
                <span class="block text-xs text-gray-400 mb-1">Cantidad (máx. {{ Personalizar::MAX_CANTIDAD }})</span>
                <div class="flex items-center gap-3">
                    <button type="button" wire:click="decrementar" class="w-8 h-8 rounded-md border border-gray-300 text-gray-600 hover:border-terminal-900 transition">&minus;</button>
                    <span class="font-mono font-semibold w-6 text-center">{{ $cantidad }}</span>
                    <button type="button" wire:click="incrementar" class="w-8 h-8 rounded-md border border-gray-300 text-gray-600 hover:border-terminal-900 transition">+</button>
                </div>
            </div>

            <div class="text-right">
                <span class="block text-xs text-gray-400 mb-1">Total</span>
                {{-- Se recalcula en cada interaccion porque Livewire re-renderiza el componente --}}
                <span class="precio text-2xl">@precio($this->precioTotal)</span>
            </div>
        </div>

        {{-- wire:loading da feedback inmediato: el boton se deshabilita y cambia el texto --}}
        <x-primary-button wire:click="agregarAlPedido" wire:loading.attr="disabled" wire:target="agregarAlPedido"
            class="w-full justify-center py-3 disabled:opacity-50">
            <span wire:loading.remove wire:target="agregarAlPedido">Agregar al pedido</span>
            <span wire:loading wire:target="agregarAlPedido">Agregando…</span>
        </x-primary-button>
    </div>

    {{-- Barra flotante del pedido en curso, la misma que acompaña en el menu --}}
    <x-barra-carrito />
</div>
