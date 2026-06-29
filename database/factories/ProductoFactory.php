<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Producto>
 */
class ProductoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'categoria_id' => Categoria::factory(),
            'nombre' => fake()->words(2, true),
            'descripcion' => fake()->sentence(),
            'precio' => fake()->randomFloat(2, 1000, 8000),
            'imagen' => null,
            'activo' => true,
        ];
    }
}
