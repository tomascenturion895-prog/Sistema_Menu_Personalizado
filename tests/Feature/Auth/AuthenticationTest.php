<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.login');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password');

        $component->call('login');

        // Los usuarios con rol cliente (el default de la factory) van al menu al loguearse
        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('menu.index', absolute: false));

        $this->assertAuthenticated();
    }

    public function test_recordarme_genera_el_token_de_sesion_persistente(): void
    {
        $user = User::factory()->create();

        // Antes de loguearse, el usuario no tiene token de "recordarme"...
        // (la factory le pone uno random, asi que lo limpiamos para la prueba)
        $user->forceFill(['remember_token' => null])->save();

        Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->set('form.remember', true)
            ->call('login');

        // Con "Recordarme" marcado, Laravel genera y guarda el remember_token:
        // es la mitad servidor de la cookie de larga duracion que re-autentica
        // al usuario cuando su sesion normal (120 min) ya expiro
        $this->assertNotNull($user->fresh()->remember_token);
    }

    public function test_sin_recordarme_no_se_genera_token_persistente(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['remember_token' => null])->save();

        Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->set('form.remember', false)
            ->call('login');

        // Sin el checkbox, el login es solo por sesion: no queda token persistente
        $this->assertNull($user->fresh()->remember_token);
    }

    public function test_admins_are_redirected_to_the_admin_panel(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $admin->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.dashboard', absolute: false));

        $this->assertAuthenticated();
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'wrong-password');

        $component->call('login');

        $component
            ->assertHasErrors()
            ->assertNoRedirect();

        $this->assertGuest();
    }

    public function test_navigation_menu_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/inicio');

        $response
            ->assertOk()
            ->assertSeeVolt('layout.navigation');
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('layout.navigation');

        $component->call('logout');

        $component
            ->assertHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
