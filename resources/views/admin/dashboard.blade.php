<x-app-layout>
    {{-- Encabezado de la pagina, usando el slot "header" del layout principal --}}
    <x-slot name="header">
        <div>
            <span class="eyebrow">// admin</span>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Panel de Administración
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Estadisticas estilo editorial: numeros grandes con regla superior,
                 sin encajonar (los contadores vienen del PanelController) --}}
            <div>
                <span class="eyebrow">// resumen</span>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-8 gap-y-6 mt-3">
                    @foreach ([
                        ['etiqueta' => 'Pedidos activos', 'valor' => $pedidosPendientes, 'destacado' => true],
                        ['etiqueta' => 'Productos', 'valor' => $totalProductos, 'destacado' => false],
                        ['etiqueta' => 'Categorías', 'valor' => $totalCategorias, 'destacado' => false],
                        ['etiqueta' => 'Ingredientes', 'valor' => $totalIngredientes, 'destacado' => false],
                    ] as $stat)
                        <div class="border-t-2 border-terminal-950 pt-3">
                            <p class="font-mono text-xs uppercase tracking-wider text-gray-500">{{ $stat['etiqueta'] }}</p>
                            <p class="font-mono text-4xl font-semibold {{ $stat['destacado'] ? 'text-brand-600' : 'text-terminal-950' }} mt-1">{{ $stat['valor'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Accesos a cada seccion de gestion, como tarjetas clickeables --}}
            <div>
                <span class="eyebrow">// gestión</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-2">
                    <a href="{{ route('admin.pedidos') }}" wire:navigate class="tarjeta tarjeta-hover p-5 group">
                        {{-- Iconos SVG inline (Heroicons): livianos y consistentes con el diseño --}}
                        <svg class="w-6 h-6 text-brand-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        <p class="font-medium text-gray-900 mt-2 group-hover:text-brand-600">Pedidos</p>
                        <p class="text-sm text-gray-500 mt-1">Ver pedidos entrantes y cambiar su estado.</p>
                    </a>

                    <a href="{{ route('admin.productos') }}" wire:navigate class="tarjeta tarjeta-hover p-5 group">
                        <svg class="w-6 h-6 text-brand-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" /></svg>
                        <p class="font-medium text-gray-900 mt-2 group-hover:text-brand-600">Productos</p>
                        <p class="text-sm text-gray-500 mt-1">Hamburguesas del menú, precios e ingredientes.</p>
                    </a>

                    <a href="{{ route('admin.categorias') }}" wire:navigate class="tarjeta tarjeta-hover p-5 group">
                        <svg class="w-6 h-6 text-brand-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" /></svg>
                        <p class="font-medium text-gray-900 mt-2 group-hover:text-brand-600">Categorías</p>
                        <p class="text-sm text-gray-500 mt-1">Grupos del menú y tipos de dieta.</p>
                    </a>

                    <a href="{{ route('admin.ingredientes') }}" wire:navigate class="tarjeta tarjeta-hover p-5 group">
                        <svg class="w-6 h-6 text-brand-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                        <p class="font-medium text-gray-900 mt-2 group-hover:text-brand-600">Ingredientes</p>
                        <p class="text-sm text-gray-500 mt-1">Panes, medallones, toppings, salsas y más.</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
