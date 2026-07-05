<?php

namespace Tests\Feature\Api\Admin;

use App\Models\Ingrediente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class IngredientesTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_admin_puede_crear_un_ingrediente(): void
    {
        Sanctum::actingAs(User::factory()->create(['rol' => 'admin']));

        $response = $this->postJson('/api/v1/admin/ingredientes', [
            'nombre' => 'Panceta',
            'tipo' => 'topping',
            'precio_extra' => 800,
        ]);

        $response->assertCreated()->assertJsonFragment(['nombre' => 'Panceta']);
    }

    public function test_un_tipo_invalido_falla_la_validacion(): void
    {
        Sanctum::actingAs(User::factory()->create(['rol' => 'admin']));

        $response = $this->postJson('/api/v1/admin/ingredientes', [
            'nombre' => 'Panceta',
            'tipo' => 'no-existe',
            'precio_extra' => 800,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('tipo');
    }

    public function test_un_admin_puede_actualizar_un_ingrediente(): void
    {
        Sanctum::actingAs(User::factory()->create(['rol' => 'admin']));
        $ingrediente = Ingrediente::factory()->create(['activo' => true]);

        $response = $this->patchJson("/api/v1/admin/ingredientes/{$ingrediente->id}", [
            'activo' => false,
        ]);

        $response->assertOk()->assertJsonFragment(['activo' => false]);
    }

    public function test_un_admin_puede_eliminar_un_ingrediente(): void
    {
        Sanctum::actingAs(User::factory()->create(['rol' => 'admin']));
        $ingrediente = Ingrediente::factory()->create();

        $this->deleteJson("/api/v1/admin/ingredientes/{$ingrediente->id}")->assertNoContent();
    }
}
