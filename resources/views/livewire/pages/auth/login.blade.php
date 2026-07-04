<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        // Segun el rol del usuario logueado, lo mandamos a una pantalla distinta:
        // admin -> panel de administracion, cliente -> menu publico para armar su pedido
        $destino = auth()->user()->esAdmin()
            ? route('admin.dashboard', absolute: false)
            : route('menu.index', absolute: false);

        $this->redirectIntended(default: $destino, navigate: true);
    }
}; ?>

<div>
    {{-- Titulo de la tarjeta --}}
    <div class="mb-6">
        <h1 class="font-display text-2xl uppercase text-terminal-950">Iniciar sesión</h1>
        <p class="text-sm text-gray-500 mt-1">Entrá a tu cuenta para pedir tu burger.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        <!-- Correo -->
        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Contraseña -->
        <div class="mt-4">
            <x-input-label for="password" value="Contraseña" />

            {{-- Campo con boton de mostrar/ocultar (componente propio con Alpine) --}}
            <div class="mt-1">
                <x-input-password wire:model="form.password" id="password" name="password" required autocomplete="current-password" />
            </div>

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Recordarme -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-brand-600 shadow-sm focus:ring-brand-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">Recordarme</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500" href="{{ route('password.request') }}" wire:navigate>
                    ¿Olvidaste tu contraseña?
                </a>
            @endif

            <x-primary-button class="ms-3">
                Entrar
            </x-primary-button>
        </div>

        @if (Route::has('register'))
            <p class="text-sm text-gray-500 mt-6 pt-4 border-t border-gray-100">
                ¿Todavía no tenés cuenta?
                <a href="{{ route('register') }}" wire:navigate class="font-semibold text-brand-600 hover:underline">Registrate</a>
            </p>
        @endif
    </form>
</div>
