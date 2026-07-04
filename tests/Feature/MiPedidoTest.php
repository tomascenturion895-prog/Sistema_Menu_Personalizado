<?php

namespace Tests\Feature;

use App\Livewire\Menu\MiPedido;
use App\Models\Ingrediente;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MiPedidoTest extends TestCase
{
    // RefreshDatabase corre las migraciones en la BD de prueba (SQLite en memoria)
    // y deshace todo al final de cada test, para que los tests no se contaminen entre si
    use RefreshDatabase;

    public function test_confirmar_pedido_crea_el_pedido_con_sus_items(): void
    {
        $user = User::factory()->create();
        $producto = Producto::factory()->create(['precio' => 5000, 'activo' => true]);
        $ingrediente = Ingrediente::factory()->create(['precio_extra' => 500, 'activo' => true]);
        $producto->ingredientes()->sync([$ingrediente->id]);

        // Simulamos un carrito en la sesion, igual al que arma la pantalla de personalizar
        session()->put('carrito', [
            [
                'producto_id' => $producto->id,
                'nombre' => $producto->nombre,
                'cantidad' => 2,
                'precio_unitario' => 5500.0,
                'ingredientes_elegidos' => [$ingrediente->id],
                'ingredientes_nombres' => [$ingrediente->nombre],
            ],
        ]);

        $componente = Livewire::actingAs($user)
            ->test(MiPedido::class)
            ->set('observaciones', 'Sin sal')
            ->call('confirmarPedido');

        // Al confirmar, se redirige a la pantalla de exito del pedido recien creado
        $componente->assertRedirect(route('cliente.pedidos.exito', Pedido::first(), absolute: false));

        // El pedido quedo guardado con el total correcto: (5000 + 500) x 2
        $this->assertDatabaseHas('pedidos', [
            'user_id' => $user->id,
            'total' => 11000,
            'estado' => 'pendiente',
            'observaciones' => 'Sin sal',
        ]);

        // Y su item con la personalizacion elegida
        $this->assertDatabaseHas('item_pedidos', [
            'producto_id' => $producto->id,
            'cantidad' => 2,
            'precio_unitario' => 5500,
        ]);

        // El carrito de la sesion quedo vacio despues de confirmar
        $this->assertEmpty(session('carrito'));
    }

    public function test_confirmar_recalcula_el_precio_vigente_del_producto(): void
    {
        $user = User::factory()->create();
        $producto = Producto::factory()->create(['precio' => 8000, 'activo' => true]);

        // El carrito guarda un precio viejo (el admin subio el precio despues de agregarlo)
        session()->put('carrito', [
            [
                'producto_id' => $producto->id,
                'nombre' => $producto->nombre,
                'cantidad' => 1,
                'precio_unitario' => 5000.0,
                'ingredientes_elegidos' => [],
                'ingredientes_nombres' => [],
            ],
        ]);

        Livewire::actingAs($user)
            ->test(MiPedido::class)
            ->call('confirmarPedido');

        // Se cobra el precio ACTUAL de la base (8000), no el viejo del carrito (5000)
        $this->assertDatabaseHas('pedidos', ['total' => 8000]);
        $this->assertDatabaseHas('item_pedidos', ['precio_unitario' => 8000]);
    }

    public function test_confirmar_descarta_productos_desactivados(): void
    {
        $user = User::factory()->create();
        $inactivo = Producto::factory()->create(['activo' => false]);

        session()->put('carrito', [
            [
                'producto_id' => $inactivo->id,
                'nombre' => $inactivo->nombre,
                'cantidad' => 1,
                'precio_unitario' => 5000.0,
                'ingredientes_elegidos' => [],
                'ingredientes_nombres' => [],
            ],
        ]);

        Livewire::actingAs($user)
            ->test(MiPedido::class)
            ->call('confirmarPedido');

        // No se crea un pedido con productos que ya no estan a la venta
        $this->assertDatabaseCount('pedidos', 0);
        $this->assertEmpty(session('carrito'));
    }

    public function test_confirmar_con_carrito_vacio_no_crea_nada(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(MiPedido::class)
            ->call('confirmarPedido');

        $this->assertDatabaseCount('pedidos', 0);
    }

    public function test_cambiar_cantidad_actualiza_el_total_en_la_misma_interaccion(): void
    {
        // Regresion: los #[Computed] de Livewire se cachean por request; si no se
        // invalida el cache al mutar la sesion, el total renderizado queda viejo
        $user = User::factory()->create();

        session()->put('carrito', [
            ['producto_id' => 1, 'nombre' => 'A', 'cantidad' => 1, 'precio_unitario' => 7500.0, 'ingredientes_elegidos' => [], 'ingredientes_nombres' => []],
        ]);

        Livewire::actingAs($user)
            ->test(MiPedido::class)
            ->call('incrementarItem', 0)
            // El HTML de ESTA respuesta ya debe mostrar el total nuevo (7500 x 2)
            ->assertSee('15.000')
            // Y avisa a la navbar para que actualice su contador en vivo
            ->assertDispatched('carrito-actualizado');
    }

    public function test_se_puede_cambiar_la_cantidad_de_un_item(): void
    {
        $user = User::factory()->create();

        session()->put('carrito', [
            ['producto_id' => 1, 'nombre' => 'A', 'cantidad' => 1, 'precio_unitario' => 100.0, 'ingredientes_elegidos' => [], 'ingredientes_nombres' => []],
        ]);

        $componente = Livewire::actingAs($user)->test(MiPedido::class);

        $componente->call('incrementarItem', 0);
        $componente->call('incrementarItem', 0);
        $this->assertSame(3, session('carrito')[0]['cantidad']);

        $componente->call('decrementarItem', 0);
        $this->assertSame(2, session('carrito')[0]['cantidad']);

        // Nunca baja de 1: para sacar el item esta el boton "Quitar"
        $componente->call('decrementarItem', 0);
        $componente->call('decrementarItem', 0);
        $this->assertSame(1, session('carrito')[0]['cantidad']);
    }

    public function test_quitar_item_lo_saca_del_carrito(): void
    {
        $user = User::factory()->create();

        session()->put('carrito', [
            ['producto_id' => 1, 'nombre' => 'A', 'cantidad' => 1, 'precio_unitario' => 100.0, 'ingredientes_elegidos' => [], 'ingredientes_nombres' => []],
            ['producto_id' => 2, 'nombre' => 'B', 'cantidad' => 1, 'precio_unitario' => 200.0, 'ingredientes_elegidos' => [], 'ingredientes_nombres' => []],
        ]);

        Livewire::actingAs($user)
            ->test(MiPedido::class)
            ->call('quitarItem', 0);

        // Solo queda el segundo item, reindexado en la posicion 0
        $carrito = session('carrito');
        $this->assertCount(1, $carrito);
        $this->assertSame('B', $carrito[0]['nombre']);
    }
}
