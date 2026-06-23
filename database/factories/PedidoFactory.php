<?php

namespace Database\Factories;

use App\Models\Pedido;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pedido>
 */
class PedidoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'total' => fake()->randomFloat(2, 1000, 10000),
            'estado' => fake()->randomElement(['pendiente', 'confirmado', 'en_preparacion', 'listo', 'cancelado']),
            'observaciones' => fake()->optional()->sentence(),
        ];
    }
}
