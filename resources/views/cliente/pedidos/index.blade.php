<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="eyebrow">// mis pedidos</span>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Historial de pedidos
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Lista editorial: filas separadas por lineas, sin encajonar cada pedido.
                 El hover tiñe la fila para marcar que es clickeable --}}
            @forelse ($pedidos as $pedido)
                <a href="{{ route('cliente.pedidos.show', $pedido) }}" wire:navigate
                    class="flex flex-wrap items-center justify-between gap-3 py-5 px-2 -mx-2 border-b border-gray-200 hover:bg-white rounded-lg transition group">
                    <div>
                        {{-- Numero de pedido estilo ticket, con la fuente mono de la marca --}}
                        <span class="font-mono font-semibold text-terminal-950 group-hover:text-brand-600 transition">{{ $pedido->numero }}</span>
                        <span class="text-sm text-gray-500 ml-2">{{ $pedido->created_at->format('d/m/Y H:i') }}</span>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ $pedido->items->sum('cantidad') }} {{ $pedido->items->sum('cantidad') === 1 ? 'producto' : 'productos' }}
                        </p>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <span class="precio text-lg">@precio($pedido->total)</span>
                            <x-badge-estado :estado="$pedido->estado" class="block mt-1" />
                        </div>
                        <span class="text-gray-300 group-hover:text-brand-600 group-hover:translate-x-1 transition" aria-hidden="true">→</span>
                    </div>
                </a>
            @empty
                <div class="tarjeta shadow-retro p-10 text-center">
                    <p class="font-display text-xl uppercase text-terminal-950">Todavía no hiciste ningún pedido</p>
                    <p class="text-sm text-gray-500 mt-2">Tu primera burger te está esperando en el menú.</p>
                    <a href="{{ route('menu.index') }}" wire:navigate class="btn-retro mt-5 px-5 py-2.5 bg-brand-500 text-terminal-950 text-sm">
                        Ir al menú →
                    </a>
                </div>
            @endforelse

            <div class="mt-6">
                {{ $pedidos->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
