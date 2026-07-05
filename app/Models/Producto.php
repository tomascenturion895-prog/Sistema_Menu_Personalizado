<?php

namespace App\Models;

use Database\Factories\ProductoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['categoria_id', 'nombre', 'descripcion', 'precio', 'imagen', 'activo'])]
class Producto extends Model
{
    /** @use HasFactory<ProductoFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'activo' => 'boolean',
        ];
    }

    /**
     * Trae varios productos por id, con sus ingredientes ya precargados,
     * indexados por id (->get($id)) para recalcular precios sin N+1.
     * Reusado por PedidoController@repetir y MiPedido@prepararItemsConPreciosActuales,
     * que necesitaban exactamente la misma consulta.
     *
     * @param  iterable<int>  $ids
     */
    public static function conIngredientesPorIds(iterable $ids): Collection
    {
        return static::with('ingredientes')->whereIn('id', $ids)->get()->keyBy('id');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function ingredientes(): BelongsToMany
    {
        return $this->belongsToMany(Ingrediente::class, 'ingrediente_producto');
    }

    public function itemPedidos(): HasMany
    {
        return $this->hasMany(ItemPedido::class);
    }
}
