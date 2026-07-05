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
    public string $apellido = '';
    public string $email = '';
    public string $telefono = '';
    public string $fecha_nacimiento = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $terminos = false;

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{L}\s]+$/u',
            ],
            'apellido' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{L}\s]+$/u',
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:'.User::class,
            ],
            'telefono' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9\-\+\s()]{6,20}$/',
            ],
            'fecha_nacimiento' => [
                'required',
                'date',
                'before:-13 years',
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Rules\Password::defaults(),
            ],
            'terminos' => ['accepted'],
        ], [
            'name.regex' => 'El nombre solo puede contener letras y espacios.',
            'apellido.regex' => 'El apellido solo puede contener letras y espacios.',
            'email.email' => 'El correo debe tener un formato válido, por ejemplo usuario@gmail.com.',
            'telefono.regex' => 'Ingresá un teléfono válido (solo números, espacios, guiones o paréntesis).',
            'fecha_nacimiento.before' => 'Tenés que ser mayor de 13 años para registrarte.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'terminos.accepted' => 'Tenés que aceptar los términos y condiciones para registrarte.',
        ]);

        // "terminos" no es una columna de la base: se reemplaza por la fecha real
        // de aceptacion, que si se guarda en el usuario
        unset($validated['terminos']);
        $validated['terminos_aceptados_en'] = now();

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
        <!-- Nombre y apellido -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="name" value="Nombre" />
                <x-text-input
                    wire:model="name"
                    id="name"
                    class="block mt-1 w-full"
                    type="text"
                    name="name"
                    required
                    autofocus
                    autocomplete="given-name"
                />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="apellido" value="Apellido" />
                <x-text-input
                    wire:model="apellido"
                    id="apellido"
                    class="block mt-1 w-full"
                    type="text"
                    name="apellido"
                    required
                    autocomplete="family-name"
                />
                <x-input-error :messages="$errors->get('apellido')" class="mt-2" />
            </div>
        </div>

        <!-- Correo -->
        <div class="mt-4">
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input
                wire:model="email"
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                required
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Telefono y fecha de nacimiento -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
            <div>
                <x-input-label for="telefono" value="Teléfono" />
                <x-text-input
                    wire:model="telefono"
                    id="telefono"
                    class="block mt-1 w-full"
                    type="tel"
                    name="telefono"
                    required
                    placeholder="3644-123456"
                    autocomplete="tel"
                />
                <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="fecha_nacimiento" value="Fecha de nacimiento" />
                <x-text-input
                    wire:model="fecha_nacimiento"
                    id="fecha_nacimiento"
                    class="block mt-1 w-full"
                    type="date"
                    name="fecha_nacimiento"
                    required
                />
                <x-input-error :messages="$errors->get('fecha_nacimiento')" class="mt-2" />
            </div>
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

        <!-- Terminos y condiciones -->
        <div class="mt-4" x-data="{ mostrarTerminos: false }">
            <label for="terminos" class="inline-flex items-start gap-2">
                <input
                    wire:model="terminos"
                    id="terminos"
                    type="checkbox"
                    class="rounded border-gray-300 text-brand-600 shadow-sm focus:ring-brand-500 mt-0.5"
                    name="terminos"
                >
                <span class="text-sm text-gray-600">
                    Acepto los
                    {{-- Boton en vez de link: despliega el texto aca abajo, sin sacar
                         al usuario de la pantalla de registro --}}
                    <button
                        type="button"
                        @click="mostrarTerminos = ! mostrarTerminos"
                        class="font-semibold text-brand-600 hover:underline"
                    >
                        términos y condiciones
                        <span x-text="mostrarTerminos ? '▴' : '▾'"></span>
                    </button>
                </span>
            </label>

            {{-- Panel desplegable con scroll propio: se puede leer sin navegar a otra pagina --}}
            <div
                x-show="mostrarTerminos"
                x-transition
                x-cloak
                class="mt-3 max-h-48 overflow-y-auto border-2 border-terminal-950/10 rounded-md p-4 bg-brand-50 text-sm"
            >
                <x-terminos-contenido />
            </div>

            <x-input-error :messages="$errors->get('terminos')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a
                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500"
                href="{{ route('login') }}"
                wire:navigate
            >
                ¿Ya tenés cuenta?
            </a>

            <x-primary-button class="ms-4">
                Registrarme
            </x-primary-button>
        </div>
    </form>
</div>
