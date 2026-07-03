<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-6">
        <span class="eyebrow">// admin / pedidos</span>
        <h3 class="text-2xl font-semibold text-gray-900">Pedidos</h3>
    </div>

    {{-- Filtro por estado del pedido --}}
    <div class="flex flex-wrap gap-2 mb-6">
        @foreach (['todos' => 'Todos', 'pendiente' => 'Pendientes', 'confirmado' => 'Confirmados', 'en_preparacion' => 'En preparación', 'listo' => 'Listos', 'cancelado' => 'Cancelados'] as $valor => $etiqueta)
            <button
                wire:click="filtrarPor('{{ $valor }}')"
                class="px-3 py-1.5 rounded-full text-sm font-medium transition
                    {{ $estado === $valor ? 'bg-brand-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
            >
                {{ $etiqueta }}
            </button>
        @endforeach
    </div>

    {{-- Esta tabla relaciona TRES modelos: Pedido + User (cliente) + ItemPedido con su Producto --}}
    <div class="space-y-4">
        @forelse ($pedidos as $pedido)
            <div class="tarjeta p-4" wire:key="pedido-{{ $pedido->id }}">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                    <div>
                        {{-- Numero de pedido con fuente mono, estilo "ticket" --}}
                        <span class="font-mono font-semibold text-terminal-900">#{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}</span>
                        <span class="text-sm text-gray-600 ml-2">{{ $pedido->user->name }}</span>
                        <span class="text-xs text-gray-400 ml-2">{{ $pedido->created_at->format('d/m/Y H:i') }}</span>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="precio">${{ number_format($pedido->total, 0, ',', '.') }}</span>

                        {{-- Select para cambiar el estado: wire:change dispara el metodo al elegir otra opcion --}}
                        <select
                            wire:change="cambiarEstado({{ $pedido->id }}, $event.target.value)"
                            class="text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500"
                        >
                            @foreach (\App\Livewire\Admin\Pedidos::ESTADOS as $opcion)
                                <option value="{{ $opcion }}" @selected($pedido->estado === $opcion)>
                                    {{ str_replace('_', ' ', ucfirst($opcion)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Detalle de los items del pedido --}}
                <ul class="text-sm text-gray-600 space-y-1 border-t border-gray-100 pt-3">
                    @foreach ($pedido->items as $item)
                        <li wire:key="item-{{ $item->id }}">
                            {{ $item->cantidad }}x {{ $item->producto->nombre }}
                            <span class="precio text-xs">${{ number_format($item->precio_unitario * $item->cantidad, 0, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>

                @if ($pedido->observaciones)
                    <p class="text-sm text-gray-600 mt-2 border-l-2 border-cheddar-400 pl-2">
                        <span class="font-mono text-xs text-gray-400">obs:</span> {{ $pedido->observaciones }}
                    </p>
                @endif
            </div>
        @empty
            <div class="tarjeta p-8 text-center text-gray-500">
                No hay pedidos con este filtro.
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $pedidos->links() }}
    </div>
</div>
