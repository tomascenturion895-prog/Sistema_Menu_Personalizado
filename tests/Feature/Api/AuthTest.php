<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_con_credenciales_correctas_devuelve_un_token(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure(['usuario' => ['id', 'email'], 'token']);
    }

    public function test_login_con_credenciales_incorrectas_falla(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'incorrecta',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_el_token_permite_acceder_a_una_ruta_protegida(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/pedidos');

        $response->assertOk();
    }

    public function test_sin_token_no_se_puede_acceder_a_una_ruta_protegida(): void
    {
        $response = $this->getJson('/api/v1/pedidos');

        $response->assertUnauthorized();
    }

    public function test_logout_revoca_el_token_usado(): void
    {
        $user = User::factory()->create();
        $tokenNuevo = $user->createToken('test');

        $this->withHeader('Authorization', "Bearer {$tokenNuevo->plainTextToken}")
            ->postJson('/api/v1/logout')
            ->assertOk();

        // El registro del token ya no existe en la base: no se puede volver a usar
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $tokenNuevo->accessToken->id]);
    }
}
