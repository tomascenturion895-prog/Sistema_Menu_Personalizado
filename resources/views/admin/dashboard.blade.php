<x-app-layout>
    {{-- Encabezado de la pagina, usando el slot "header" del layout principal --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel de Administración') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Bienvenida --}}
            <div class="tarjeta p-6">
                <p class="text-gray-900 font-medium">Bienvenido, {{ auth()->user()->name }} 👋</p>
                <p class="text-sm text-gray-500 mt-1">Desde acá administrás todo el menú de Capa8Burger.</p>
            </div>

            {{-- Tarjetas de estadisticas: los contadores vienen de la ruta (routes/web.php) --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="tarjeta p-5">
                    <p class="text-sm text-gray-500">Pedidos activos</p>
                    <p class="font-mono text-3xl font-semibold text-brand-600 mt-1">{{ $pedidosPendientes }}</p>
                </div>
                <div class="tarjeta p-5">
                    <p class="text-sm text-gray-500">Productos</p>
                    <p class="font-mono text-3xl font-semibold text-terminal-900 mt-1">{{ $totalProductos }}</p>
                </div>
                <div class="tarjeta p-5">
                    <p class="text-sm text-gray-500">Categorías</p>
                    <p class="font-mono text-3xl font-semibold text-terminal-900 mt-1">{{ $totalCategorias }}</p>
                </div>
                <div class="tarjeta p-5">
                    <p class="text-sm text-gray-500">Ingredientes</p>
                    <p class="font-mono text-3xl font-semibold text-terminal-900 mt-1">{{ $totalIngredientes }}</p>
                </div>
            </div>

            {{-- Accesos a cada seccion de gestion, como tarjetas clickeables --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('admin.pedidos') }}" wire:navigate class="tarjeta p-5 hover:border-brand-500 transition group">
                    <span class="text-2xl">🧾</span>
                    <p class="font-medium text-gray-900 mt-2 group-hover:text-brand-600">Pedidos</p>
                    <p class="text-sm text-gray-500 mt-1">Ver pedidos entrantes y cambiar su estado.</p>
                </a>

                <a href="{{ route('admin.productos') }}" wire:navigate class="tarjeta p-5 hover:border-brand-500 transition group">
                    <span class="text-2xl">🍔</span>
                    <p class="font-medium text-gray-900 mt-2 group-hover:text-brand-600">Productos</p>
                    <p class="text-sm text-gray-500 mt-1">Hamburguesas del menú, precios e ingredientes.</p>
                </a>

                <a href="{{ route('admin.categorias') }}" wire:navigate class="tarjeta p-5 hover:border-brand-500 transition group">
                    <span class="text-2xl">🗂️</span>
                    <p class="font-medium text-gray-900 mt-2 group-hover:text-brand-600">Categorías</p>
                    <p class="text-sm text-gray-500 mt-1">Grupos del menú y tipos de dieta.</p>
                </a>

                <a href="{{ route('admin.ingredientes') }}" wire:navigate class="tarjeta p-5 hover:border-brand-500 transition group">
                    <span class="text-2xl">🥬</span>
                    <p class="font-medium text-gray-900 mt-2 group-hover:text-brand-600">Ingredientes</p>
                    <p class="text-sm text-gray-500 mt-1">Panes, medallones, toppings, salsas y más.</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
