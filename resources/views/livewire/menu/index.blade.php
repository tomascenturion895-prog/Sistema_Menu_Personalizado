<div>
    {{-- session()->flash() guarda un mensaje que solo dura UNA request: aparece despues de
         agregar un producto al pedido, y desaparece solo si recargas la pagina de nuevo --}}
    @if (session('mensaje'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-md text-sm">
            {{ session('mensaje') }}
        </div>
    @endif

    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Nuestro menú</h2>
        <p class="text-gray-600">Elegí una categoría y armá tu hamburguesa como quieras.</p>
    </div>

    {{-- Tabs de filtro por tipo de dieta. La clase cambia segun $dieta para resaltar la activa --}}
    <div class="flex flex-wrap gap-2 mb-8 border-b border-gray-200 pb-4">
        @foreach (['todos' => 'Todo', 'normal' => 'Normal', 'vegetariano' => 'Vegetariano', 'vegano' => 'Vegano', 'celiaco' => 'Celíaco'] as $valor => $etiqueta)
            <button
                wire:click="filtrarPor('{{ $valor }}')"
                class="px-4 py-2 rounded-full text-sm font-medium transition
                    {{ $dieta === $valor ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
            >
                {{ $etiqueta }}
            </button>
        @endforeach
    </div>

    @forelse ($categorias as $categoria)
        <div class="mb-10" wire:key="categoria-{{ $categoria->id }}">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $categoria->nombre }}</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($categoria->productos as $producto)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 flex flex-col" wire:key="producto-{{ $producto->id }}">
                        <h4 class="font-medium text-gray-900">{{ $producto->nombre }}</h4>

                        @if ($producto->descripcion)
                            <p class="text-sm text-gray-500 mt-1 flex-1">{{ $producto->descripcion }}</p>
                        @endif

                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-lg font-semibold text-gray-900">${{ number_format($producto->precio, 2) }}</span>

                            <a href="{{ route('menu.personalizar', $producto) }}" wire:navigate
                                class="text-sm bg-indigo-600 text-white px-3 py-1.5 rounded-md hover:bg-indigo-700">
                                Personalizar
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <p class="text-gray-500">No hay productos disponibles para este filtro todavía.</p>
    @endforelse
</div>
