<?php

namespace App\Models;

use Database\Factories\IngredienteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['nombre', 'tipo', 'precio_extra', 'stock', 'es_vegetariano', 'es_vegano', 'sin_gluten', 'activo'])]
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
            'stock' => 'integer',
            'es_vegetariano' => 'boolean',
            'es_vegano' => 'boolean',
            'sin_gluten' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    /**
     * Sin stock cargado: el admin todavia no lo controla, o se agoto de verdad.
     */
    public function sinStock(): bool
    {
        return $this->stock <= 0;
    }

    /**
     * Regla de negocio: sin stock, el ingrediente NUNCA puede quedar activo
     * (no se le puede ofrecer a un cliente algo que no hay para preparar).
     * Se fuerza aca (evento del modelo) y no en el formulario del admin para
     * que se cumpla sin importar desde donde se actualice el ingrediente
     * (panel admin o API).
     */
    protected static function booted(): void
    {
        static::saving(function (self $ingrediente): void {
            if ($ingrediente->stock <= 0) {
                $ingrediente->activo = false;
            }
        });
    }

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'ingrediente_producto');
    }
}
