<?php

namespace Tests\Feature\Api\Admin;

use App\Models\Categoria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CategoriasTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_cliente_no_admin_no_puede_administrar_categorias(): void
    {
        Sanctum::actingAs(User::factory()->create(['rol' => 'cliente']));

        $this->getJson('/api/v1/admin/categorias')->assertForbidden();
    }

    public function test_un_admin_puede_crear_una_categoria(): void
    {
        Sanctum::actingAs(User::factory()->create(['rol' => 'admin']));

        $response = $this->postJson('/api/v1/admin/categorias', [
            'nombre' => 'Postres',
            'tipo_dieta' => 'normal',
        ]);

        $response
            ->assertCreated()
            ->assertJsonFragment(['nombre' => 'Postres']);

        $this->assertDatabaseHas('categorias', ['nombre' => 'Postres']);
    }

    public function test_un_admin_puede_actualizar_una_categoria(): void
    {
        Sanctum::actingAs(User::factory()->create(['rol' => 'admin']));
        $categoria = Categoria::factory()->create(['nombre' => 'Viejo nombre']);

        $response = $this->patchJson("/api/v1/admin/categorias/{$categoria->id}", [
            'nombre' => 'Nombre nuevo',
        ]);

        $response->assertOk()->assertJsonFragment(['nombre' => 'Nombre nuevo']);
    }

    public function test_un_admin_puede_eliminar_una_categoria(): void
    {
        Sanctum::actingAs(User::factory()->create(['rol' => 'admin']));
        $categoria = Categoria::factory()->create();

        $this->deleteJson("/api/v1/admin/categorias/{$categoria->id}")->assertNoContent();

        $this->assertDatabaseMissing('categorias', ['id' => $categoria->id]);
    }

    public function test_crear_una_categoria_sin_nombre_falla_la_validacion(): void
    {
        Sanctum::actingAs(User::factory()->create(['rol' => 'admin']));

        $this->postJson('/api/v1/admin/categorias', ['tipo_dieta' => 'normal'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('nombre');
    }
}
