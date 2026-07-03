<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Categoria>
 */
class CategoriaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->randomElement(['Hamburguesas', 'Bebidas', 'Papas Fritas', 'Postres']),
            'descripcion' => fake()->sentence(),
            'imagen' => null,
            'tipo_dieta' => fake()->randomElement(['normal', 'vegetariano', 'vegano', 'celiaco']),
            'activo' => true,
        ];
    }
}
