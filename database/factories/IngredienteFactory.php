<?php

namespace Database\Factories;

use App\Models\Ingrediente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ingrediente>
 */
class IngredienteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->word(),
            'tipo' => fake()->randomElement(['pan', 'medallon', 'topping', 'salsa', 'papas', 'bebida', 'extra']),
            'precio_extra' => fake()->randomFloat(2, 0, 1500),
            'es_vegetariano' => fake()->boolean(),
            'es_vegano' => fake()->boolean(),
            'sin_gluten' => fake()->boolean(),
            'activo' => true,
        ];
    }
}
