<?php

namespace Tests\Feature\Api;

use App\Models\Categoria;
use App\Models\Ingrediente;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_categorias_activas_son_publicas(): void
    {
        Categoria::factory()->create(['nombre' => 'Hamburguesas', 'activo' => true]);
        Categoria::factory()->create(['nombre' => 'Descontinuada', 'activo' => false]);

        $response = $this->getJson('/api/v1/categorias');

        $response
            ->assertOk()
            ->assertJsonFragment(['nombre' => 'Hamburguesas'])
            ->assertJsonMissing(['nombre' => 'Descontinuada']);
    }

    public function test_productos_activos_son_publicos_y_traen_su_categoria(): void
    {
        $categoria = Categoria::factory()->create();
        Producto::factory()->create(['categoria_id' => $categoria->id, 'nombre' => 'Doble Deploy', 'activo' => true]);
        Producto::factory()->create(['nombre' => 'Descontinuada', 'activo' => false]);

        $response = $this->getJson('/api/v1/productos');

        $response
            ->assertOk()
            ->assertJsonFragment(['nombre' => 'Doble Deploy'])
            ->assertJsonMissing(['nombre' => 'Descontinuada']);
    }

    public function test_el_filtro_de_dieta_solo_devuelve_productos_de_esa_categoria(): void
    {
        $vegana = Categoria::factory()->create(['tipo_dieta' => 'vegano']);
        $normal = Categoria::factory()->create(['tipo_dieta' => 'normal']);

        Producto::factory()->create(['categoria_id' => $vegana->id, 'nombre' => 'Veggie Refactor']);
        Producto::factory()->create(['categoria_id' => $normal->id, 'nombre' => 'Clásica Capa8']);

        $response = $this->getJson('/api/v1/productos?dieta=vegano');

        $response
            ->assertOk()
            ->assertJsonFragment(['nombre' => 'Veggie Refactor'])
            ->assertJsonMissing(['nombre' => 'Clásica Capa8']);
    }

    public function test_el_detalle_de_un_producto_incluye_sus_ingredientes(): void
    {
        $producto = Producto::factory()->create();
        $ingrediente = Ingrediente::factory()->create(['nombre' => 'Cheddar extra']);
        $producto->ingredientes()->attach($ingrediente);

        $response = $this->getJson("/api/v1/productos/{$producto->id}");

        $response
            ->assertOk()
            ->assertJsonFragment(['nombre' => 'Cheddar extra']);
    }

    public function test_un_producto_desactivado_devuelve_404(): void
    {
        $producto = Producto::factory()->create(['activo' => false]);

        $response = $this->getJson("/api/v1/productos/{$producto->id}");

        $response->assertNotFound();
    }
}
