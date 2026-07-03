<?php

namespace Tests\Feature;

use App\Livewire\Menu\MiPedido;
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
        $producto = Producto::factory()->create(['precio' => 5000]);

        // Simulamos un carrito en la sesion, igual al que arma la pantalla de personalizar
        session()->put('carrito', [
            [
                'producto_id' => $producto->id,
                'nombre' => $producto->nombre,
                'cantidad' => 2,
                'precio_unitario' => 5500.0,
                'ingredientes_elegidos' => [1, 2],
                'ingredientes_nombres' => ['Pan clasico', 'Cheddar'],
            ],
        ]);

        Livewire::actingAs($user)
            ->test(MiPedido::class)
            ->set('observaciones', 'Sin sal')
            ->call('confirmarPedido');

        // El pedido quedo guardado con el total correcto (5500 x 2)
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

    public function test_confirmar_con_carrito_vacio_no_crea_nada(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(MiPedido::class)
            ->call('confirmarPedido');

        $this->assertDatabaseCount('pedidos', 0);
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
