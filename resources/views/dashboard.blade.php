<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="eyebrow">// inicio</span>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Hola, {{ auth()->user()->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Hero de bienvenida: presenta el negocio y lleva directo al menu --}}
            <div class="tarjeta shadow-retro overflow-hidden">
                <div class="bg-terminal-950 px-8 py-10 sm:px-12 grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-8 items-center">
                    <div>
                        <p class="font-mono text-sm text-brand-400 mb-3">$ hamburguesas --personalizadas</p>
                        <h3 class="font-display text-3xl sm:text-4xl uppercase text-white leading-tight">
                            Armala <span class="text-brand-500">a tu manera</span>
                        </h3>
                        <p class="text-terminal-300 mt-3 max-w-xl">
                            Elegí el pan, los medallones, los toppings, las salsas y los acompañamientos.
                            Con opciones para todas las dietas: vegetariana, vegana y sin TACC.
                        </p>

                        <a href="{{ route('menu.index') }}" wire:navigate
                            class="inline-flex items-center mt-6 px-5 py-2.5 bg-brand-500 hover:bg-brand-600 text-terminal-950 text-sm font-semibold rounded-md transition">
                            Ver el menú →
                        </a>
                    </div>

                    {{-- La hamburguesa de 8 capas, insignia de la marca --}}
                    <x-burger-capas class="hidden sm:block w-40" />
                </div>
            </div>

            {{-- Estado del ultimo pedido, si existe (dato que pasa el PanelController) --}}
            @if ($ultimoPedido)
                <a href="{{ route('cliente.pedidos.show', $ultimoPedido) }}" wire:navigate
                    class="tarjeta tarjeta-hover p-4 flex items-center justify-between gap-3 block">
                    <div class="text-sm text-gray-600">
                        Tu último pedido
                        <span class="font-mono font-semibold text-terminal-950">{{ $ultimoPedido->numero }}</span>
                        está <span class="font-semibold text-brand-600">{{ strtolower(\App\Models\Pedido::ESTADOS[$ultimoPedido->estado] ?? $ultimoPedido->estado) }}</span>
                    </div>
                    <span class="text-sm font-semibold text-brand-600">Ver detalle →</span>
                </a>
            @endif

            {{-- Accesos rapidos: columnas editoriales con regla superior negra.
                 Sin cajas: la linea y el espacio ordenan, la flecha marca lo clickeable --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-8 gap-y-6 pt-2">
                @foreach ([
                    ['ruta' => route('menu.index'), 'titulo' => 'Menú', 'detalle' => 'Mirá todas las hamburguesas y filtrá por tu dieta.', 'contador' => null],
                    ['ruta' => route('menu.mi-pedido'), 'titulo' => 'Mi pedido', 'detalle' => 'Revisá lo que elegiste y confirmá tu pedido.', 'contador' => count(session('carrito', [])) ?: null],
                    ['ruta' => route('cliente.pedidos.index'), 'titulo' => 'Mis pedidos', 'detalle' => 'Mirá tu historial y el estado de cada pedido.', 'contador' => null],
                    ['ruta' => route('profile'), 'titulo' => 'Mi perfil', 'detalle' => 'Actualizá tus datos y tu contraseña.', 'contador' => null],
                ] as $acceso)
                    <a href="{{ $acceso['ruta'] }}" wire:navigate class="border-t-2 border-terminal-950 pt-4 group">
                        <p class="font-semibold text-terminal-950 flex items-center gap-1.5">
                            {{ $acceso['titulo'] }}
                            @if ($acceso['contador'])
                                <span class="bg-brand-500 text-terminal-950 text-xs font-mono font-semibold rounded-full px-1.5 border border-terminal-950">{{ $acceso['contador'] }}</span>
                            @endif
                            <span class="ml-auto text-gray-300 group-hover:text-brand-600 group-hover:translate-x-1 transition" aria-hidden="true">→</span>
                        </p>
                        <p class="text-sm text-gray-500 mt-1.5 leading-relaxed">{{ $acceso['detalle'] }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
