<?php

use App\Livewire\Actions\Logout;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect(route('home', absolute: false), navigate: true);
    }

    /**
     * Cuando otro componente agrega o quita items del carrito, dispara el evento
     * "carrito-actualizado"; este listener re-renderiza la navbar para que el
     * contador de "Mi pedido" se actualice EN VIVO, sin recargar la pagina.
     */
    #[On('carrito-actualizado')]
    public function refrescarContadorCarrito(): void
    {
        // No necesita logica: con re-renderizar alcanza (el contador lee la sesion)
    }
}; ?>

{{-- Navbar blanca con borde negro: se separa visualmente del marquee (negro) y del
contenido, y hace que los acentos naranjas (logo, tab activa, boton registrarme)
resalten de verdad. Funciona para visitantes y logueados via @auth / @guest --}}
<nav x-data="{ open: false }" class="bg-white border-b-2 border-terminal-950">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center">
                <!-- Logo: para visitantes lleva a la landing, para logueados al inicio -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" wire:navigate>
                        {{-- Sobre fondo blanco el acento naranja del logo resalta solo --}}
                        <x-application-logo class="text-lg text-terminal-950" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-2 sm:ms-10 sm:flex sm:items-center">
                    @auth
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                            {{ __('Inicio') }}
                        </x-nav-link>
                    @endauth

                    <x-nav-link :href="route('menu.index')" :active="request()->routeIs('menu.index') || request()->routeIs('menu.personalizar')" wire:navigate>
                        {{ __('Menú') }}
                    </x-nav-link>

                    @auth
                        <x-nav-link :href="route('menu.mi-pedido')" :active="request()->routeIs('menu.mi-pedido')"
                            wire:navigate>
                            {{ __('Mi pedido') }}
                            {{-- Contador de items del carrito, solo se muestra si hay algo cargado --}}
                            @if (count(session('carrito', [])) > 0)
                                <span
                                    class="ml-1.5 bg-terminal-950 text-white text-xs font-mono font-semibold rounded-full px-1.5">{{ count(session('carrito', [])) }}</span>
                            @endif
                        </x-nav-link>

                        {{-- Este link solo se muestra si el usuario logueado tiene rol admin --}}
                        @if (auth()->user()->esAdmin())
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')" wire:navigate>
                                {{ __('Panel Admin') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Lado derecho: menu de usuario (logueado) o accesos de entrada (visitante) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 sm:gap-3">
                {{-- Publico, visible para todos: va pegado al bloque de perfil/login, no en los links principales --}}
                <a href="{{ route('equipo') }}" wire:navigate class="text-sm font-semibold text-terminal-600 hover:text-terminal-950 hover:underline underline-offset-4 {{ request()->routeIs('equipo') ? 'text-terminal-950 underline' : '' }}">
                    {{ __('Equipo') }}
                </a>

                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="btn-retro rounded-full gap-1 px-4 py-1.5 bg-white text-sm text-terminal-950 focus:outline-none">
                                <div x-data="{{ json_encode(['name' => auth()->user()->nombre_completo]) }}" x-text="name"
                                    x-on:profile-updated.window="name = $event.detail.name"></div>

                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('cliente.pedidos.index')" wire:navigate>
                                {{ __('Mis pedidos') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('profile')" wire:navigate>
                                {{ __('Perfil') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <button wire:click="logout" class="w-full text-start">
                                <x-dropdown-link>
                                    {{ __('Cerrar sesión') }}
                                </x-dropdown-link>
                            </button>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" wire:navigate
                        class="text-sm font-semibold text-terminal-600 hover:text-terminal-950 hover:underline underline-offset-4">
                        Iniciar sesión
                    </a>
                    @if (Route::has('register'))
                        {{-- Naranja sobre navbar blanca: el CTA principal resalta de verdad --}}
                        <a href="{{ route('register') }}" wire:navigate
                            class="btn-retro px-4 py-1.5 bg-brand-500 text-terminal-950 text-sm">
                            Registrarme
                        </a>
                    @endif
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md border-2 border-terminal-950 bg-white text-terminal-950 shadow-retro-sm focus:outline-none transition">
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t-2 border-terminal-950">
        <div class="pt-3 pb-3 space-y-1.5 px-4">
            @auth
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Inicio') }}
                </x-responsive-nav-link>
            @endauth

            <x-responsive-nav-link :href="route('menu.index')" :active="request()->routeIs('menu.index') || request()->routeIs('menu.personalizar')" wire:navigate>
                {{ __('Menú') }}
            </x-responsive-nav-link>

            @auth
                <x-responsive-nav-link :href="route('menu.mi-pedido')" :active="request()->routeIs('menu.mi-pedido')"
                    wire:navigate>
                    {{ __('Mi pedido') }}
                </x-responsive-nav-link>

                @if (auth()->user()->esAdmin())
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')"
                        wire:navigate>
                        {{ __('Panel Admin') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-4 border-t-2 border-terminal-950/20 px-4">
            {{-- Publico, visible para todos: va junto al bloque de perfil/login, no en los links principales --}}
            <x-responsive-nav-link :href="route('equipo')" :active="request()->routeIs('equipo')" wire:navigate>
                {{ __('Equipo') }}
            </x-responsive-nav-link>

            @auth
                <div class="px-1">
                    <div class="font-semibold text-base text-terminal-950"
                        x-data="{{ json_encode(['name' => auth()->user()->nombre_completo]) }}" x-text="name"
                        x-on:profile-updated.window="name = $event.detail.name"></div>
                    <div class="font-medium text-sm text-terminal-950/60">{{ auth()->user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1.5">
                    <x-responsive-nav-link :href="route('cliente.pedidos.index')" wire:navigate>
                        {{ __('Mis pedidos') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('profile')" wire:navigate>
                        {{ __('Perfil') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <button wire:click="logout" class="w-full text-start">
                        <x-responsive-nav-link>
                            {{ __('Cerrar sesión') }}
                        </x-responsive-nav-link>
                    </button>
                </div>
            @else
                <div class="space-y-1.5">
                    <x-responsive-nav-link :href="route('login')" wire:navigate>
                        Iniciar sesión
                    </x-responsive-nav-link>

                    @if (Route::has('register'))
                        <x-responsive-nav-link :href="route('register')" wire:navigate>
                            Registrarme
                        </x-responsive-nav-link>
                    @endif
                </div>
            @endauth
        </div>
    </div>
</nav>