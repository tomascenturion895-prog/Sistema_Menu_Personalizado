<?php

namespace Tests\Feature\Api\Cliente;

use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
