<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        // Los registros nuevos siempre son clientes: van directo al menu a hacer su primer pedido
        $this->redirect(route('menu.index', absolute: false), navigate: true);
    }
}; ?>

<div>
    {{-- Titulo de la tarjeta --}}
    <div class="mb-6">
        <h1 class="font-display text-2xl uppercase text-terminal-950">Crear cuenta</h1>
        <p class="text-sm text-gray-500 mt-1">Registrate para armar tu primera burger.</p>
    </div>

    <form wire:submit="register">
        <!-- Nombre -->
        <div>
            <x-input-label for="name" value="Nombre" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Correo -->
        <div class="mt-4">
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Contraseña -->
        <div class="mt-4">
            <x-input-label for="password" value="Contraseña" />

            <div class="mt-1">
                <x-input-password wire:model="password" id="password" name="password" required autocomplete="new-password" />
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirmar contraseña -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmar contraseña" />

            <div class="mt-1">
                <x-input-password wire:model="password_confirmation" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" />
            </div>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500" href="{{ route('login') }}" wire:navigate>
                ¿Ya tenés cuenta?
            </a>

            <x-primary-button class="ms-4">
                Registrarme
            </x-primary-button>
        </div>
    </form>
</div>
