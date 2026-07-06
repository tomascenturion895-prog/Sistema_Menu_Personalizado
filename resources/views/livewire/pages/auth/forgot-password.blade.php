<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div>
    <div class="mb-6">
        <h1 class="font-display text-2xl uppercase text-terminal-950">Recuperar contraseña</h1>
        <p class="text-sm text-gray-500 mt-2">
            ¿Olvidaste tu contraseña? No hay problema. Dejanos tu correo y te enviamos
            un enlace para que elijas una nueva.
        </p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 text-sm font-medium text-exito-600">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="sendPasswordResetLink">
        <!-- Correo -->
        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('login') }}" wire:navigate class="underline text-sm text-gray-600 hover:text-gray-900">
                Volver a iniciar sesión
            </a>

            <x-primary-button>
                Enviar enlace
            </x-primary-button>
        </div>
    </form>
</div>
