<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['nombre', 'tipo', 'precio_extra', 'es_vegetariano', 'es_vegano', 'sin_gluten', 'activo'])]
class Ingrediente extends Model
{
    /** @use HasFactory<\Database\Factories\IngredienteFactory> */
    use HasFactory;

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
