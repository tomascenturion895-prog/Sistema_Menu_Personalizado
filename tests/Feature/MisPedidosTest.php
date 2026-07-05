<?php

namespace Tests\Feature;

use App\Livewire\Cliente\EstadoPedido;
use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MisPedidosTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_historial_requiere_estar_logueado(): void
    {
        $response = $this->get('/mis-pedidos');

        $response->assertRedirect(route('login', absolute: false));
    }

    public function test_el_cliente_ve_solo_sus_propios_pedidos(): void
    {
        $cliente = User::factory()->create();
        $otro = User::factory()->create();

        $pedidoPropio = Pedido::factory()->create(['user_id' => $cliente->id, 'total' => 9500]);
        $pedidoAjeno = Pedido::factory()->create(['user_id' => $otro->id, 'total' => 4200]);

        $response = $this->actingAs($cliente)->get('/mis-pedidos');

        // Ve el numero de SU pedido pero no el del otro usuario
        $response
            ->assertOk()
            ->assertSee(str_pad($pedidoPropio->id, 5, '0', STR_PAD_LEFT))
            ->assertDontSee(str_pad($pedidoAjeno->id, 5, '0', STR_PAD_LEFT));
    }

    public function test_el_detalle_muestra_los_items_del_pedido(): void
    {
        $cliente = User::factory()->create();
        $pedido = Pedido::factory()->create(['user_id' => $cliente->id]);
        $item = ItemPedido::factory()->create(['pedido_id' => $pedido->id, 'cantidad' => 2]);

        $response = $this->actingAs($cliente)->get(route('cliente.pedidos.show', $pedido));

        $response
            ->assertOk()
            ->assertSee($item->producto->nombre)
            // El badge de estado es un componente Livewire con wire:poll (estado en vivo)
            ->assertSeeLivewire(EstadoPedido::class);
    }

    public function test_no_se_puede_ver_el_pedido_de_otro_usuario(): void
    {
        $cliente = User::factory()->create();
        $otro = User::factory()->create();

        $pedidoAjeno = Pedido::factory()->create(['user_id' => $otro->id]);

        // Intenta abrir el pedido ajeno cambiando el id en la URL: debe recibir 403
        $response = $this->actingAs($cliente)->get(route('cliente.pedidos.show', $pedidoAjeno));

        $response->assertForbidden();
    }

    public function test_el_historial_destaca_el_pedido_mas_reciente_y_no_lo_repite_en_la_lista(): void
    {
        $cliente = User::factory()->create();

        $viejo = Pedido::factory()->create(['user_id' => $cliente->id, 'created_at' => now()->subDay()]);
        $reciente = Pedido::factory()->create(['user_id' => $cliente->id, 'created_at' => now()]);

        $response = $this->actingAs($cliente)->get('/mis-pedidos');

        $response
            ->assertOk()
            // El mas reciente se muestra en la tarjeta destacada, con estado en vivo
            ->assertSee($reciente->numero)
            ->assertSeeLivewire(EstadoPedido::class)
            // El viejo sigue apareciendo en el historial de abajo
            ->assertSee($viejo->numero);

        // El numero del pedido reciente aparece UNA sola vez (destacado arriba),
        // no se repite tambien en la lista de historial
        $this->assertSame(1, substr_count($response->getContent(), $reciente->numero));
    }

    public function test_el_inicio_muestra_el_estado_del_ultimo_pedido(): void
    {
        $cliente = User::factory()->create();
        Pedido::factory()->create(['user_id' => $cliente->id, 'estado' => 'en_preparacion']);

        $response = $this->actingAs($cliente)->get('/inicio');

        $response
            ->assertOk()
            ->assertSee('en preparación');
    }

    public function test_la_pantalla_de_exito_muestra_el_numero_del_pedido(): void
    {
        $cliente = User::factory()->create();
        $pedido = Pedido::factory()->create(['user_id' => $cliente->id]);

        $response = $this->actingAs($cliente)->get(route('cliente.pedidos.exito', $pedido));

        $response
            ->assertOk()
            ->assertSee('Pedido confirmado')
            ->assertSee($pedido->numero);
    }

    public function test_la_pantalla_de_exito_de_otro_usuario_devuelve_403(): void
    {
        $cliente = User::factory()->create();
        $otro = User::factory()->create();
        $pedidoAjeno = Pedido::factory()->create(['user_id' => $otro->id]);

        $response = $this->actingAs($cliente)->get(route('cliente.pedidos.exito', $pedidoAjeno));

        $response->assertForbidden();
    }

    public function test_un_pedido_pendiente_se_puede_cancelar(): void
    {
        $cliente = User::factory()->create();
        $pedido = Pedido::factory()->create(['user_id' => $cliente->id, 'estado' => 'pendiente']);

        $response = $this->actingAs($cliente)->patch(route('cliente.pedidos.cancelar', $pedido));

        $response->assertRedirect(route('cliente.pedidos.show', $pedido));
        $this->assertDatabaseHas('pedidos', ['id' => $pedido->id, 'estado' => 'cancelado']);
    }

    public function test_un_pedido_en_preparacion_no_se_puede_cancelar(): void
    {
        $cliente = User::factory()->create();
        $pedido = Pedido::factory()->create(['user_id' => $cliente->id, 'estado' => 'en_preparacion']);

        $response = $this->actingAs($cliente)->patch(route('cliente.pedidos.cancelar', $pedido));

        // La cocina ya lo tomo: la cancelacion se rechaza y el estado no cambia
        $response->assertForbidden();
        $this->assertDatabaseHas('pedidos', ['id' => $pedido->id, 'estado' => 'en_preparacion']);
    }

    public function test_no_se_puede_cancelar_el_pedido_de_otro_usuario(): void
    {
        $cliente = User::factory()->create();
        $otro = User::factory()->create();
        $pedidoAjeno = Pedido::factory()->create(['user_id' => $otro->id, 'estado' => 'pendiente']);

        $response = $this->actingAs($cliente)->patch(route('cliente.pedidos.cancelar', $pedidoAjeno));

        $response->assertForbidden();
    }

    public function test_repetir_un_pedido_carga_el_carrito_con_precios_actuales(): void
    {
        $cliente = User::factory()->create();
        $pedido = Pedido::factory()->create(['user_id' => $cliente->id]);

        // El item se pidio a 5000, pero el producto hoy vale 7000
        $item = ItemPedido::factory()->create([
            'pedido_id' => $pedido->id,
            'cantidad' => 2,
            'precio_unitario' => 5000,
        ]);
        $item->producto->update(['precio' => 7000, 'activo' => true]);

        $response = $this->actingAs($cliente)->post(route('cliente.pedidos.repetir', $pedido));

        $response->assertRedirect(route('menu.mi-pedido'));

        // El carrito quedo cargado con el precio VIGENTE, no el historico
        $carrito = session('carrito');
        $this->assertCount(1, $carrito);
        $this->assertSame(2, $carrito[0]['cantidad']);
        $this->assertSame(7000.0, $carrito[0]['precio_unitario']);
    }
}
