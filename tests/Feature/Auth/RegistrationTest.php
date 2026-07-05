<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.register');
    }

    public function test_new_users_can_register(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test')
            ->set('apellido', 'User')
            ->set('email', 'test@gmail.com')
            ->set('telefono', '3644-123456')
            ->set('fecha_nacimiento', '2000-01-01')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('terminos', true);

        $component->call('register');

        $component->assertRedirect(route('menu.index', absolute: false));

        $this->assertAuthenticated();
    }

    public function test_no_se_puede_registrar_sin_aceptar_los_terminos(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test')
            ->set('apellido', 'User')
            ->set('email', 'test@gmail.com')
            ->set('telefono', '3644-123456')
            ->set('fecha_nacimiento', '2000-01-01')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('terminos', false);

        $component->call('register');

        $component->assertHasErrors(['terminos' => 'accepted']);
        $this->assertGuest();
    }

    public function test_no_se_puede_registrar_siendo_menor_de_13_anos(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test')
            ->set('apellido', 'User')
            ->set('email', 'test@gmail.com')
            ->set('telefono', '3644-123456')
            ->set('fecha_nacimiento', now()->subYears(10)->format('Y-m-d'))
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('terminos', true);

        $component->call('register');

        $component->assertHasErrors(['fecha_nacimiento' => 'before']);
        $this->assertGuest();
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function nombresInvalidosProvider(): array
    {
        return [
            'con numeros' => ['Tom4s'],
            'con simbolos' => ['Tomás!'],
            'con guion bajo' => ['Tomas_Centurion'],
        ];
    }

    #[DataProvider('nombresInvalidosProvider')]
    public function test_no_se_puede_registrar_con_nombre_invalido(string $nombreInvalido): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', $nombreInvalido)
            ->set('apellido', 'User')
            ->set('email', 'test@gmail.com')
            ->set('telefono', '3644-123456')
            ->set('fecha_nacimiento', '2000-01-01')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('terminos', true);

        $component->call('register');

        $component->assertHasErrors(['name' => 'regex']);
        $this->assertGuest();
    }

    public function test_no_se_puede_registrar_con_apellido_invalido(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test')
            ->set('apellido', 'User123')
            ->set('email', 'test@gmail.com')
            ->set('telefono', '3644-123456')
            ->set('fecha_nacimiento', '2000-01-01')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('terminos', true);

        $component->call('register');

        $component->assertHasErrors(['apellido' => 'regex']);
        $this->assertGuest();
    }

    public function test_no_se_puede_registrar_con_un_correo_de_dominio_desconocido(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test')
            ->set('apellido', 'User')
            ->set('email', 'test@correo-random.com')
            ->set('telefono', '3644-123456')
            ->set('fecha_nacimiento', '2000-01-01')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('terminos', true);

        $component->call('register');

        $component->assertHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_no_se_puede_registrar_con_una_contrasena_de_menos_de_8_caracteres(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test')
            ->set('apellido', 'User')
            ->set('email', 'test@gmail.com')
            ->set('telefono', '3644-123456')
            ->set('fecha_nacimiento', '2000-01-01')
            ->set('password', 'abc123')
            ->set('password_confirmation', 'abc123')
            ->set('terminos', true);

        $component->call('register');

        $component->assertHasErrors(['password']);
        $this->assertGuest();
    }

    public function test_no_se_puede_registrar_con_una_contrasena_solo_de_letras(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test')
            ->set('apellido', 'User')
            ->set('email', 'test@gmail.com')
            ->set('telefono', '3644-123456')
            ->set('fecha_nacimiento', '2000-01-01')
            ->set('password', 'passwordsololetras')
            ->set('password_confirmation', 'passwordsololetras')
            ->set('terminos', true);

        $component->call('register');

        $component->assertHasErrors(['password']);
        $this->assertGuest();
    }
}
