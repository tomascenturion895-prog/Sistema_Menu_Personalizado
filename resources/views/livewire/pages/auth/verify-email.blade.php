<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <div class="mb-6">
        <h1 class="font-display text-2xl uppercase text-terminal-950">Verificá tu correo</h1>
        <p class="text-sm text-gray-500 mt-2">
            ¡Gracias por registrarte! Antes de empezar, verificá tu dirección de correo
            haciendo clic en el enlace que te enviamos. Si no lo recibiste, te mandamos otro.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-exito-700">
            Te enviamos un nuevo enlace de verificación al correo con el que te registraste.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <x-primary-button wire:click="sendVerification">
            Reenviar correo
        </x-primary-button>

        <button wire:click="logout" type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500">
            Cerrar sesión
        </button>
    </div>
</div>
