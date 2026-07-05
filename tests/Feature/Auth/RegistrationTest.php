<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
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
            ->set('email', 'test@example.com')
            ->set('telefono', '3644-123456')
            ->set('fecha_nacimiento', '2000-01-01')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
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
            ->set('email', 'test@example.com')
            ->set('telefono', '3644-123456')
            ->set('fecha_nacimiento', '2000-01-01')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
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
            ->set('email', 'test@example.com')
            ->set('telefono', '3644-123456')
            ->set('fecha_nacimiento', now()->subYears(10)->format('Y-m-d'))
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('terminos', true);

        $component->call('register');

        $component->assertHasErrors(['fecha_nacimiento' => 'before']);
        $this->assertGuest();
    }
}
