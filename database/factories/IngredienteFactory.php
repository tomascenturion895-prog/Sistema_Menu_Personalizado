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
            'tipo' => fake()->randomElement(array_keys(Ingrediente::TIPOS)),
            'precio_extra' => fake()->randomFloat(2, 0, 1500),
            // Arranca en 1 (no 0): "sin stock" es un estado que los tests fuerzan
            // a proposito, no algo que deberia salir al azar y desactivar el
            // ingrediente por sorpresa (Ingrediente::booted() lo fuerza a inactivo)
            'stock' => fake()->numberBetween(1, 100),
            'es_vegetariano' => fake()->boolean(),
            'es_vegano' => fake()->boolean(),
            'sin_gluten' => fake()->boolean(),
            'activo' => true,
        ];
    }
}
