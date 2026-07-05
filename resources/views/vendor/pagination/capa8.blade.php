{{-- Paginacion propia del sistema, con el mismo lenguaje visual que el resto
     (btn-retro, font-mono, pildora naranja para la pagina activa) en vez de la
     vista Tailwind generica que trae Laravel por defecto.
     Se usa en TODAS las tablas paginadas (Categorias, Ingredientes, Pedidos del
     admin, Mis pedidos del cliente): un solo lugar para que la paginacion se
     vea igual en toda la app.

     Enlaces normales (href + wire:navigate) en vez de wire:click: esta vista
     se usa tanto dentro de componentes Livewire (Categorias, Ingredientes)
     como en una vista Blade clasica sin Livewire (cliente/pedidos/index), y
     un href funciona en los dos casos por igual. --}}
@if ($paginator->hasPages())
    {{-- Centrado (no justify-between): texto arriba, botones abajo, los dos
         centrados como un solo bloque en vez de repartidos a los extremos --}}
    <nav role="navigation" aria-label="Paginación" class="flex flex-col items-center gap-3">
        <p class="text-sm text-gray-500">
            Mostrando <span class="font-mono font-semibold text-terminal-950">{{ $paginator->firstItem() }}</span>
            a <span class="font-mono font-semibold text-terminal-950">{{ $paginator->lastItem() }}</span>
            de <span class="font-mono font-semibold text-terminal-950">{{ $paginator->total() }}</span> resultados
        </p>

        <div class="flex items-center gap-1.5">
            {{-- Anterior --}}
            @if ($paginator->onFirstPage())
                <span class="btn-retro w-9 h-9 bg-white text-gray-300 cursor-not-allowed" aria-disabled="true" aria-label="Anterior">&laquo;</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" wire:navigate rel="prev" class="btn-retro w-9 h-9 bg-white text-terminal-950 text-sm" aria-label="Anterior">&laquo;</a>
            @endif

            {{-- Numeros de pagina --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="w-9 h-9 flex items-center justify-center text-gray-400 font-mono text-sm">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="btn-retro w-9 h-9 bg-brand-500 text-terminal-950 font-mono text-sm" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" wire:navigate class="btn-retro w-9 h-9 bg-white text-terminal-950 font-mono text-sm">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Siguiente --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" wire:navigate rel="next" class="btn-retro w-9 h-9 bg-white text-terminal-950 text-sm" aria-label="Siguiente">&raquo;</a>
            @else
                <span class="btn-retro w-9 h-9 bg-white text-gray-300 cursor-not-allowed" aria-disabled="true" aria-label="Siguiente">&raquo;</span>
            @endif
        </div>
    </nav>
@endif
