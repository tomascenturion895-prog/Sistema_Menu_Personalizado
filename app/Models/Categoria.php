<?php

namespace App\Models;

use Database\Factories\CategoriaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nombre', 'descripcion', 'imagen', 'tipo_dieta', 'activo'])]
class Categoria extends Model
{
    /** @use HasFactory<CategoriaFactory> */
    use HasFactory;

    // Etiquetas en español de cada tipo de dieta. Es la unica fuente de verdad:
    // las vistas (menu, filtros, formularios del admin) leen de aca en vez de
    // repetir el mismo mapa en cada archivo
    public const TIPOS_DIETA = [
        'normal' => 'Normal',
        'vegetariano' => 'Vegetariano',
        'vegano' => 'Vegano',
        'celiaco' => 'Celíaco',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }
}
