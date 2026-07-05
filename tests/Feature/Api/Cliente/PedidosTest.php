<?php

namespace Tests\Feature\Api\Cliente;

use App\Models\Ingrediente;
use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PedidosTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_cliente_ve_solo_sus_propios_pedidos(): void
    {
        $cliente = User::factory()->create();
        $otro = User::factory()->create();

        Pedido::factory()->create(['user_id' => $cliente->id, 'total' => 9500]);
        Pedido::factory()->create(['user_id' => $otro->id, 'total' => 4200]);

        Sanctum::actingAs($cliente);

        $response = $this->getJson('/api/v1/pedidos');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_no_puede_ver_el_pedido_de_otro_usuario(): void
    {
        $cliente = User::factory()->create();
        $otro = User::factory()->create();
        $pedidoAjeno = Pedido::factory()->create(['user_id' => $otro->id]);

        Sanctum::actingAs($cliente);

        $response = $this->getJson("/api/v1/pedidos/{$pedidoAjeno->id}");

        $response->assertForbidden();
    }

    public function test_el_detalle_incluye_los_items_del_pedido(): void
    {
        $cliente = User::factory()->create();
        $pedido = Pedido::factory()->create(['user_id' => $cliente->id]);
        $item = ItemPedido::factory()->create(['pedido_id' => $pedido->id]);

        Sanctum::actingAs($cliente);

        $response = $this->getJson("/api/v1/pedidos/{$pedido->id}");

        $response
            ->assertOk()
            ->assertJsonFragment(['nombre' => $item->producto->nombre]);
    }

    public function test_puede_confirmar_un_pedido_con_items(): void
    {
        $cliente = User::factory()->create();
        $producto = Producto::factory()->create(['precio' => 5000, 'activo' => true]);
        $ingrediente = Ingrediente::factory()->create(['precio_extra' => 500, 'activo' => true]);
        $producto->ingredientes()->sync([$ingrediente->id]);

        Sanctum::actingAs($cliente);

        $response = $this->postJson('/api/v1/pedidos', [
            'items' => [
                [
                    'producto_id' => $producto->id,
                    'cantidad' => 2,
                    'ingredientes_elegidos' => [$ingrediente->id],
                ],
            ],
            'observaciones' => 'Sin sal',
        ]);

        // (5000 + 500) x 2
        $response->assertCreated()->assertJsonFragment(['total' => 11000.0]);

        $this->assertDatabaseHas('pedidos', [
            'user_id' => $cliente->id,
            'total' => 11000,
            'estado' => 'pendiente',
            'observaciones' => 'Sin sal',
        ]);
    }

    public function test_descarta_ingredientes_sin_stock_al_confirmar_por_api(): void
    {
        $cliente = User::factory()->create();
        $producto = Producto::factory()->create(['precio' => 5000, 'activo' => true]);
        $ingrediente = Ingrediente::factory()->create(['precio_extra' => 500, 'stock' => 0]);
        $producto->ingredientes()->sync([$ingrediente->id]);

        Sanctum::actingAs($cliente);

        $response = $this->postJson('/api/v1/pedidos', [
            'items' => [
                ['producto_id' => $producto->id, 'cantidad' => 1, 'ingredientes_elegidos' => [$ingrediente->id]],
            ],
        ]);

        // Se cobra solo el precio base, sin el extra del ingrediente agotado
        $response->assertCreated()->assertJsonFragment(['total' => 5000.0]);
    }

    public function test_no_confirma_un_pedido_con_producto_desactivado(): void
    {
        $cliente = User::factory()->create();
        $inactivo = Producto::factory()->create(['activo' => false]);

        Sanctum::actingAs($cliente);

        $response = $this->postJson('/api/v1/pedidos', [
            'items' => [
                ['producto_id' => $inactivo->id, 'cantidad' => 1],
            ],
        ]);

        $response->assertUnprocessable();
        $this->assertDatabaseCount('pedidos', 0);
    }

    public function test_no_confirma_sin_items(): void
    {
        $cliente = User::factory()->create();
        Sanctum::actingAs($cliente);

        $response = $this->postJson('/api/v1/pedidos', ['items' => []]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('items');
    }

    public function test_no_crea_un_segundo_pedido_si_ya_hay_una_confirmacion_en_curso(): void
    {
        $cliente = User::factory()->create();
        $producto = Producto::factory()->create(['precio' => 5000, 'activo' => true]);

        Sanctum::actingAs($cliente);

        $candado = Cache::lock('checkout:'.$cliente->id, 10);
        $candado->get();

        try {
            $response = $this->postJson('/api/v1/pedidos', [
                'items' => [['producto_id' => $producto->id, 'cantidad' => 1]],
            ]);

            $response->assertStatus(409);
            $this->assertDatabaseCount('pedidos', 0);
        } finally {
            $candado->release();
        }
    }

    public function test_requiere_autenticacion_para_confirmar_un_pedido(): void
    {
        $producto = Producto::factory()->create();

        $response = $this->postJson('/api/v1/pedidos', [
            'items' => [['producto_id' => $producto->id, 'cantidad' => 1]],
        ]);

        $response->assertUnauthorized();
    }
}
