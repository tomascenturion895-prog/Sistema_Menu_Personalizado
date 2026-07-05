<x-app-layout>
    <div class="py-16">
        <div class="max-w-xl mx-auto px-4 sm:px-6 text-center">
            <div class="tarjeta shadow-retro p-10">
                {{-- Tilde de confirmacion --}}
                <span class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-exito-100 border-2 border-terminal-950">
                    <svg class="w-7 h-7 text-exito-700" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                </span>

                <h1 class="font-display text-3xl sm:text-4xl uppercase text-terminal-950 mt-5">¡Pedido confirmado!</h1>

                {{-- El numero de ticket bien grande, para que lo tenga a mano al retirar --}}
                <p class="font-mono text-4xl font-semibold text-brand-600 mt-3">{{ $pedido->numero }}</p>

                <p class="text-gray-600 mt-4">
                    Ya lo mandamos a la cocina. El tiempo estimado de preparación es de
                    <span class="font-semibold text-terminal-950">20 a 30 minutos</span>.
                </p>

                <div class="flex items-center justify-center gap-2 mt-4 text-sm text-gray-500">
                    Estado:
                    {{-- Badge en vivo: va avanzando solo a medida que la cocina trabaja --}}
                    <livewire:cliente.estado-pedido :pedido="$pedido" />
                </div>

                <div class="flex flex-wrap justify-center gap-3 mt-8">
                    <a href="{{ route('cliente.pedidos.show', $pedido) }}" wire:navigate
                        class="btn-retro px-5 py-2.5 bg-brand-500 text-terminal-950 text-sm">
                        Seguir mi pedido
                    </a>
                    <a href="{{ route('menu.index') }}" wire:navigate
                        class="btn-retro px-5 py-2.5 bg-white text-sm">
                        Volver al menú
                    </a>
                </div>
            </div>

            <p class="font-mono text-xs text-terminal-500 mt-6">// gracias por elegirnos. sos la capa 8.</p>
        </div>
    </div>
</x-app-layout>
