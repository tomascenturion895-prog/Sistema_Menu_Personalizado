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
            <div class="tarjeta overflow-hidden">
                <div class="bg-terminal-900 px-8 py-10 sm:px-12 grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-8 items-center">
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
                            class="inline-flex items-center mt-6 px-5 py-2.5 bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold rounded-md transition">
                            Ver el menú →
                        </a>
                    </div>

                    {{-- La hamburguesa de 8 capas, insignia de la marca --}}
                    <x-burger-capas class="hidden sm:block w-40" />
                </div>
            </div>

            {{-- Accesos rapidos --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <a href="{{ route('menu.index') }}" wire:navigate class="tarjeta p-5 hover:border-brand-500 transition group">
                    <svg class="w-6 h-6 text-brand-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                    <p class="font-medium text-gray-900 mt-2 group-hover:text-brand-600">Menú</p>
                    <p class="text-sm text-gray-500 mt-1">Mirá todas las hamburguesas y filtrá por tu dieta.</p>
                </a>

                <a href="{{ route('menu.mi-pedido') }}" wire:navigate class="tarjeta p-5 hover:border-brand-500 transition group">
                    <svg class="w-6 h-6 text-brand-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" /></svg>
                    <p class="font-medium text-gray-900 mt-2 group-hover:text-brand-600">
                        Mi pedido
                        @if (count(session('carrito', [])) > 0)
                            <span class="ml-1 bg-brand-500 text-white text-xs font-mono font-semibold rounded-full px-1.5">{{ count(session('carrito', [])) }}</span>
                        @endif
                    </p>
                    <p class="text-sm text-gray-500 mt-1">Revisá lo que elegiste y confirmá tu pedido.</p>
                </a>

                <a href="{{ route('profile') }}" wire:navigate class="tarjeta p-5 hover:border-brand-500 transition group">
                    <svg class="w-6 h-6 text-brand-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                    <p class="font-medium text-gray-900 mt-2 group-hover:text-brand-600">Mi perfil</p>
                    <p class="text-sm text-gray-500 mt-1">Actualizá tus datos y tu contraseña.</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
