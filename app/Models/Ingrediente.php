<?php

namespace App\Models;

use Database\Factories\IngredienteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['nombre', 'tipo', 'precio_extra', 'es_vegetariano', 'es_vegano', 'sin_gluten', 'activo'])]
class Ingrediente extends Model
{
    /** @use HasFactory<IngredienteFactory> */
    use HasFactory;

    // Tipos de ingrediente con su etiqueta legible. Unica fuente de verdad:
    // el select del admin, la validacion y la factory leen de aca
    // (el enum de la migracion es la unica copia inevitable: define el esquema)
    public const TIPOS = [
        'pan' => 'Pan',
        'medallon' => 'Medallón',
        'topping' => 'Topping',
        'salsa' => 'Salsa',
        'papas' => 'Papas',
        'bebida' => 'Bebida',
        'extra' => 'Extra',
    ];

    protected function casts(): array
    {
        return [
            'precio_extra' => 'decimal:2',
            'es_vegetariano' => 'boolean',
            'es_vegano' => 'boolean',
            'sin_gluten' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'ingrediente_producto');
    }
}
