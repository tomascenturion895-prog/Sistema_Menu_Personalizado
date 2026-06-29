<x-app-layout>
    {{-- Encabezado de la pagina, usando el slot "header" del layout principal --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel de Administración') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Esta vista solo es accesible por usuarios con rol "admin" gracias al middleware --}}
                    <p class="mb-4">Bienvenido, {{ auth()->user()->name }}. Esta es el área administrativa de Capa8Burger.</p>

                    <div class="space-x-4">
                        <a href="{{ route('admin.categorias') }}" wire:navigate class="text-indigo-600 hover:text-indigo-900 underline">
                            Gestionar categorías →
                        </a>
                        <a href="{{ route('admin.productos') }}" wire:navigate class="text-indigo-600 hover:text-indigo-900 underline">
                            Gestionar productos →
                        </a>
                        <a href="{{ route('admin.ingredientes') }}" wire:navigate class="text-indigo-600 hover:text-indigo-900 underline">
                            Gestionar ingredientes →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
