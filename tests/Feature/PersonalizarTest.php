<?php

namespace Tests\Feature;

use App\Livewire\Menu\Index;
use App\Livewire\Menu\Personalizar;
use App\Models\Ingrediente;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PersonalizarTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Crea un producto con N ingredientes de un tipo dado, ya asociados.
     */
    private function productoConIngredientes(string $tipo, int $cantidad): Producto
    {
        $producto = Producto::factory()->create(['precio' => 5000, 'activo' => true]);

        $ingredientes = Ingrediente::factory()->count($cantidad)->create(['tipo' => $tipo, 'activo' => true]);
        $producto->ingredientes()->sync($ingredientes->pluck('id'));

        return $producto;
    }

    public function test_no_permite_superar_el_limite_de_toppings(): void
    {
        $user = User::factory()->create();
        // Producto con 7 toppings disponibles (el limite es 5)
        $producto = $this->productoConIngredientes('topping', 7);
        $ids = $producto->ingredientes->pluck('id')->all();

        $componente = Livewire::actingAs($user)->test(Personalizar::class, ['producto' => $producto]);

        // Marcamos 6 toppings: el hook updatedSeleccionMultiple debe deshacer el sexto
        $componente->set('seleccionMultiple', array_slice($ids, 0, 6));

        $this->assertCount(5, $componente->get('seleccionMultiple'));
        $this->assertStringContainsString('hasta 5', $componente->get('error'));
    }

    public function test_la_cantidad_no_supera_el_maximo(): void
    {
        $user = User::factory()->create();
        $producto = $this->productoConIngredientes('topping', 1);

        $componente = Livewire::actingAs($user)->test(Personalizar::class, ['producto' => $producto]);

        // Intentamos incrementar 15 veces: debe frenar en MAX_CANTIDAD (10)
        for ($i = 0; $i < 15; $i++) {
            $componente->call('incrementar');
        }

        $this->assertSame(Personalizar::MAX_CANTIDAD, $componente->get('cantidad'));
    }

    public function test_ignora_ingredientes_que_no_pertenecen_al_producto(): void
    {
        $user = User::factory()->create();
        $producto = $this->productoConIngredientes('topping', 2);

        // Ingrediente ajeno: existe pero NO esta asociado a este producto
        $ajeno = Ingrediente::factory()->create(['tipo' => 'topping', 'activo' => true]);

        Livewire::actingAs($user)
            ->test(Personalizar::class, ['producto' => $producto])
            ->set('seleccionMultiple', [$ajeno->id])
            ->call('agregarAlPedido');

        // El item se agrego, pero el ingrediente ajeno quedo filtrado por seguridad
        $carrito = session('carrito');
        $this->assertNotContains($ajeno->id, $carrito[0]['ingredientes_elegidos']);
    }

    public function test_agregar_combo_directo_al_carrito(): void
    {
        $user = User::factory()->create();
        $producto = Producto::factory()->create(['precio' => 6500, 'activo' => true]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('agregarCombo', $producto->id)
            // El evento que actualiza el contador de la navbar en vivo
            ->assertDispatched('carrito-actualizado');

        $carrito = session('carrito');
        $this->assertCount(1, $carrito);
        $this->assertSame($producto->id, $carrito[0]['producto_id']);
        $this->assertSame(6500.0, $carrito[0]['precio_unitario']);
        $this->assertSame(['Receta de la casa'], $carrito[0]['ingredientes_nombres']);
    }
}
