<?php

namespace Tests\Feature\Api\Admin;

use App\Models\Categoria;
use App\Models\Ingrediente;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductosTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_admin_puede_crear_un_producto_con_ingredientes(): void
    {
        Sanctum::actingAs(User::factory()->create(['rol' => 'admin']));
        $categoria = Categoria::factory()->create();
        $ingrediente = Ingrediente::factory()->create();

        $response = $this->postJson('/api/v1/admin/productos', [
            'categoria_id' => $categoria->id,
            'nombre' => 'Doble Deploy',
            'precio' => 9800,
            'ingredientes' => [$ingrediente->id],
        ]);

        $response->assertCreated()->assertJsonFragment(['nombre' => 'Doble Deploy']);

        $producto = Producto::where('nombre', 'Doble Deploy')->firstOrFail();
        $this->assertTrue($producto->ingredientes->contains($ingrediente));
    }

    public function test_un_admin_puede_actualizar_solo_el_precio_sin_tocar_los_ingredientes(): void
    {
        Sanctum::actingAs(User::factory()->create(['rol' => 'admin']));
        $producto = Producto::factory()->create(['precio' => 5000]);
        $ingrediente = Ingrediente::factory()->create();
        $producto->ingredientes()->attach($ingrediente);

        $response = $this->patchJson("/api/v1/admin/productos/{$producto->id}", [
            'precio' => 6000,
        ]);

        $response->assertOk()->assertJsonFragment(['precio' => 6000]);
        // Los ingredientes no se tocaron porque la request no mando esa clave
        $this->assertTrue($producto->fresh()->ingredientes->contains($ingrediente));
    }

    public function test_ingrediente_inexistente_falla_la_validacion(): void
    {
        Sanctum::actingAs(User::factory()->create(['rol' => 'admin']));
        $categoria = Categoria::factory()->create();

        $response = $this->postJson('/api/v1/admin/productos', [
            'categoria_id' => $categoria->id,
            'nombre' => 'Producto x',
            'precio' => 5000,
            'ingredientes' => [999999],
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('ingredientes.0');
    }

    public function test_un_admin_puede_eliminar_un_producto(): void
    {
        Sanctum::actingAs(User::factory()->create(['rol' => 'admin']));
        $producto = Producto::factory()->create();

        $this->deleteJson("/api/v1/admin/productos/{$producto->id}")->assertNoContent();
    }
}
