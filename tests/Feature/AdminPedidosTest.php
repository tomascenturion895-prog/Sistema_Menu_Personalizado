<?php

namespace Tests\Feature;

use App\Livewire\Admin\Pedidos;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPedidosTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_admin_puede_avanzar_el_estado_de_un_pedido(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);
        $pedido = Pedido::factory()->create(['estado' => 'pendiente']);

        Livewire::actingAs($admin)
            ->test(Pedidos::class)
            ->call('cambiarEstado', $pedido->id, 'en_preparacion');

        $this->assertDatabaseHas('pedidos', ['id' => $pedido->id, 'estado' => 'en_preparacion']);
    }

    public function test_no_se_puede_revivir_un_pedido_cancelado(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);
        $pedido = Pedido::factory()->create(['estado' => 'cancelado']);

        Livewire::actingAs($admin)
            ->test(Pedidos::class)
            ->call('cambiarEstado', $pedido->id, 'pendiente');

        // El estado no cambio: la transicion invalida se ignoro
        $this->assertDatabaseHas('pedidos', ['id' => $pedido->id, 'estado' => 'cancelado']);
    }

    public function test_no_se_puede_retroceder_de_listo_a_en_preparacion(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);
        $pedido = Pedido::factory()->create(['estado' => 'listo']);

        Livewire::actingAs($admin)
            ->test(Pedidos::class)
            ->call('cambiarEstado', $pedido->id, 'en_preparacion');

        $this->assertDatabaseHas('pedidos', ['id' => $pedido->id, 'estado' => 'listo']);
    }
}
