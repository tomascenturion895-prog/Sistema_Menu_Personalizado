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
                <div class="shrink-0 flex items-center gap-2">
                    <img src="{{ asset('images/Navbar_Capa8Burger.png') }}" alt="Capa8Burger" class="h-12 w-auto">
                    <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" wire:navigate>
                        {{-- Sobre fondo blanco el acento naranja del logo resalta solo --}}
                        <x-application-logo class="text-lg text-terminal-950" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-2 sm:ms-10 sm:flex sm:items-center">
                    {{-- Visible para todos (invitado o logueado): "/" y "/inicio" son la
                         misma pagina, asi que ambos casos llevan al mismo lugar --}}
                    <x-nav-link :href="auth()->check() ? route('dashboard') : route('home')" :active="request()->routeIs('home') || request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Inicio') }}
                    </x-nav-link>

                    {{-- Menu/Carrito/Mis pedidos son cosas de CLIENTE: un admin no compra
                         para si mismo, asi que ni siquiera ve estos links (ademas de estar
                         bloqueados por el middleware 'cliente' si entra por URL directa) --}}
                    @unless (auth()->user()?->esAdmin())
                        <x-nav-link :href="route('menu.index')" :active="request()->routeIs('menu.index') || request()->routeIs('menu.personalizar')" wire:navigate>
                            {{ __('Menú') }}
                        </x-nav-link>
                    @endunless

                    @auth
                        @unless (auth()->user()->esAdmin())
                            {{-- Acceso directo al estado del pedido + historial, sin pasar por el dropdown --}}
                            <x-nav-link :href="route('cliente.pedidos.index')" :active="request()->routeIs('cliente.pedidos.*')" wire:navigate>
                                {{ __('Mis pedidos') }}
                            </x-nav-link>
                        @endunless

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
                {{-- Publico, visible para todos: va pegado al bloque de perfil/login, no en los links principales.
                     Mismo componente x-nav-link que Menu/Carrito/Mis pedidos, para que tenga el mismo diseño de pildora --}}
                <x-nav-link :href="route('equipo')" :active="request()->routeIs('equipo')" wire:navigate>
                    {{ __('Equipo') }}
                </x-nav-link>

                {{-- Icono de carrito (en vez del texto "Carrito"): es la forma que la
                     gente ya conoce de cualquier tienda online. Al hacer click despliega
                     una vista previa (mini-carrito) en vez de navegar directo, para poder
                     chequear que hay cargado sin perder la pagina en la que estabas.
                     Visible tambien para invitados: el carrito vive en la sesion desde
                     antes de loguearse, asi que un invitado con productos cargados
                     tiene que poder verlos igual (el boton "Finalizar compra" lo manda
                     a /mi-pedido, que si le exige loguearse, y lo trae de vuelta aca
                     despues gracias a la URL intended) --}}
                @unless (auth()->user()?->esAdmin())
                    @php
                        $itemsCarrito = collect(session('carrito', []));
                    @endphp

                    <x-dropdown align="right" width="w-80" content-classes="bg-white">
                        <x-slot name="trigger">
                            <button type="button" aria-label="Carrito"
                                class="relative inline-flex items-center p-2 rounded-md text-terminal-950 hover:bg-terminal-950/5 {{ request()->routeIs('menu.mi-pedido') ? 'bg-brand-100' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.98-4.706 2.545-7.187.075-.323-.154-.65-.483-.65H5.25M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                                </svg>

                                {{-- Contador de items del carrito, solo se muestra si hay algo cargado --}}
                                @if ($itemsCarrito->isNotEmpty())
                                    <span
                                        class="absolute -top-1 -right-1 bg-brand-500 text-terminal-950 text-xs font-mono font-semibold rounded-full h-4 min-w-4 px-1 flex items-center justify-center">{{ $itemsCarrito->count() }}</span>
                                @endif
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="p-4">
                                @if ($itemsCarrito->isEmpty())
                                    <p class="text-sm text-gray-500 text-center py-2">Tu carrito está vacío.</p>
                                @else
                                    <ul class="space-y-3 max-h-72 overflow-y-auto">
                                        @foreach ($itemsCarrito as $item)
                                            <li class="flex items-start justify-between gap-3 text-sm">
                                                <div>
                                                    <p class="font-semibold text-terminal-950">{{ $item['nombre'] }}</p>
                                                    <p class="text-gray-500 font-mono text-xs">{{ $item['cantidad'] }} x @precio($item['precio_unitario'])</p>
                                                </div>
                                                <span class="precio text-sm shrink-0">@precio($item['precio_unitario'] * $item['cantidad'])</span>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
                                        <span class="text-sm font-semibold text-terminal-950">Total</span>
                                        <span class="precio text-base">@precio($itemsCarrito->sum(fn (array $item) => $item['precio_unitario'] * $item['cantidad']))</span>
                                    </div>

                                    <a href="{{ route('menu.mi-pedido') }}" wire:navigate
                                        class="btn-retro mt-4 w-full flex items-center justify-center px-4 py-2 bg-brand-500 text-terminal-950 text-sm">
                                        Finalizar compra
                                    </a>
                                @endif
                            </div>
                        </x-slot>
                    </x-dropdown>
                @endunless

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
            <x-responsive-nav-link :href="auth()->check() ? route('dashboard') : route('home')" :active="request()->routeIs('home') || request()->routeIs('dashboard')" wire:navigate>
                {{ __('Inicio') }}
            </x-responsive-nav-link>

            @unless (auth()->user()?->esAdmin())
                <x-responsive-nav-link :href="route('menu.index')" :active="request()->routeIs('menu.index') || request()->routeIs('menu.personalizar')" wire:navigate>
                    {{ __('Menú') }}
                </x-responsive-nav-link>
            @endunless

            @auth
                @unless (auth()->user()->esAdmin())
                    <x-responsive-nav-link :href="route('cliente.pedidos.index')" :active="request()->routeIs('cliente.pedidos.*')" wire:navigate>
                        {{ __('Mis pedidos') }}
                    </x-responsive-nav-link>
                @endunless

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

            {{-- Icono de carrito, mismo criterio que en desktop: entre Equipo y el bloque de
                 perfil, visible tambien para invitados con productos ya cargados en la sesion --}}
            @unless (auth()->user()?->esAdmin())
                <x-responsive-nav-link :href="route('menu.mi-pedido')" :active="request()->routeIs('menu.mi-pedido')" wire:navigate>
                    <span class="inline-flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.98-4.706 2.545-7.187.075-.323-.154-.65-.483-.65H5.25M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                        </svg>
                        {{ __('Carrito') }}
                        @if (count(session('carrito', [])) > 0)
                            <span class="bg-brand-500 text-terminal-950 text-xs font-mono font-semibold rounded-full h-4 min-w-4 px-1 flex items-center justify-center">{{ count(session('carrito', [])) }}</span>
                        @endif
                    </span>
                </x-responsive-nav-link>
            @endunless

            @auth
                <div class="px-1">
                    <div class="font-semibold text-base text-terminal-950"
                        x-data="{{ json_encode(['name' => auth()->user()->nombre_completo]) }}" x-text="name"
                        x-on:profile-updated.window="name = $event.detail.name"></div>
                    <div class="font-medium text-sm text-terminal-950/60">{{ auth()->user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1.5">
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