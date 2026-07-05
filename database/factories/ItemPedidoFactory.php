<?php

namespace Database\Factories;

use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ItemPedido>
 */
class ItemPedidoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pedido_id' => Pedido::factory(),
            'producto_id' => Producto::factory(),
            'nombre_producto' => fake()->words(3, true),
            'cantidad' => fake()->numberBetween(1, 3),
            'precio_unitario' => fake()->randomFloat(2, 1000, 8000),
            'ingredientes_elegidos' => null,
        ];
    }
}
