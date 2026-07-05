<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $apellido = '';
    public string $email = '';
    public string $telefono = '';

    // Los campos arrancan bloqueados: hay que apretar "Modificar" antes de poder
    // tocarlos, para evitar ediciones accidentales al entrar a la pantalla
    public bool $editando = false;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->apellido = Auth::user()->apellido ?? '';
        $this->email = Auth::user()->email;
        $this->telefono = Auth::user()->telefono ?? '';
    }

    /**
     * Desbloquea los campos para poder editarlos.
     */
    public function habilitarEdicion(): void
    {
        $this->editando = true;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[\p{L}\s]+$/u'],
            'apellido' => ['required', 'string', 'max:255', 'regex:/^[\p{L}\s]+$/u'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'telefono' => ['required', 'string', 'max:20', 'regex:/^[0-9\-\+\s()]{6,20}$/'],
        ], [
            'name.regex' => 'El nombre solo puede contener letras y espacios.',
            'apellido.regex' => 'El apellido solo puede contener letras y espacios.',
            'telefono.regex' => 'Ingresá un teléfono válido (solo números, espacios, guiones o paréntesis).',
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Guardado: los campos se vuelven a bloquear hasta la proxima vez que se apriete "Modificar"
        $this->editando = false;

        $this->dispatch('profile-updated', name: $user->nombre_completo);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Datos del perfil
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Actualizá tus datos personales y tu dirección de correo.
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="name" value="Nombre" />
                <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus autocomplete="given-name" :disabled="! $editando" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="apellido" value="Apellido" />
                <x-text-input wire:model="apellido" id="apellido" name="apellido" type="text" class="mt-1 block w-full" required autocomplete="family-name" :disabled="! $editando" />
                <x-input-error class="mt-2" :messages="$errors->get('apellido')" />
            </div>
        </div>

        <div>
            <x-input-label for="telefono" value="Teléfono" />
            <x-text-input wire:model="telefono" id="telefono" name="telefono" type="tel" class="mt-1 block w-full" required placeholder="3644-123456" autocomplete="tel" :disabled="! $editando" />
            <x-input-error class="mt-2" :messages="$errors->get('telefono')" />
        </div>

        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required autocomplete="username" :disabled="! $editando" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        Tu correo todavía no está verificado.

                        <button wire:click.prevent="sendVerification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500">
                            Hacé clic acá para reenviar el correo de verificación.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-exito-700">
                            Te enviamos un nuevo enlace de verificación a tu correo.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            {{-- Sin apretar "Modificar" antes, los campos de arriba estan bloqueados
                 y no hay boton de guardar: evita ediciones accidentales al entrar --}}
            @if ($editando)
                <x-primary-button>Guardar cambios</x-primary-button>
            @else
                <x-secondary-button type="button" wire:click="habilitarEdicion">Modificar</x-secondary-button>
            @endif

            <x-action-message class="me-3" on="profile-updated">
                Guardado.
            </x-action-message>
        </div>
    </form>
</section>
