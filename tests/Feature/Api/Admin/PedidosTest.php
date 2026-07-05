<?php

namespace Tests\Feature\Api\Admin;

use App\Models\Pedido;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PedidosTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_admin_ve_los_pedidos_de_todos_los_clientes(): void
    {
        Sanctum::actingAs(User::factory()->create(['rol' => 'admin']));

        Pedido::factory()->create(['user_id' => User::factory()->create()->id]);
        Pedido::factory()->create(['user_id' => User::factory()->create()->id]);

        $response = $this->getJson('/api/v1/admin/pedidos');

        $response->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_un_admin_puede_avanzar_el_estado_de_un_pedido(): void
    {
        Sanctum::actingAs(User::factory()->create(['rol' => 'admin']));
        $pedido = Pedido::factory()->create(['estado' => 'pendiente']);

        $response = $this->patchJson("/api/v1/admin/pedidos/{$pedido->id}/estado", [
            'estado' => 'en_preparacion',
        ]);

        $response->assertOk()->assertJsonFragment(['estado' => 'en_preparacion']);
        $this->assertDatabaseHas('pedidos', ['id' => $pedido->id, 'estado' => 'en_preparacion']);
    }

    public function test_un_estado_invalido_falla_la_validacion(): void
    {
        Sanctum::actingAs(User::factory()->create(['rol' => 'admin']));
        $pedido = Pedido::factory()->create(['estado' => 'pendiente']);

        $response = $this->patchJson("/api/v1/admin/pedidos/{$pedido->id}/estado", [
            'estado' => 'no-existe',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('estado');
    }

    public function test_un_cliente_no_admin_no_puede_administrar_pedidos(): void
    {
        Sanctum::actingAs(User::factory()->create(['rol' => 'cliente']));
        $pedido = Pedido::factory()->create();

        $this->getJson("/api/v1/admin/pedidos/{$pedido->id}")->assertForbidden();
    }
}
