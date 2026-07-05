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
        return static::with('ingredientes', 'categoria')->whereIn('id', $ids)->get()->keyBy('id');
    }

    /**
     * Recalcula el precio vigente de ESTE producto (ya con sus ingredientes
     * precargados) contra una lista de ingredientes elegidos, descartando los
     * que ya no pertenecen al producto o dejaron de estar activos (por ejemplo,
     * se quedaron sin stock). Devuelve null si el producto en si ya no esta
     * disponible.
     *
     * Centraliza el calculo que antes estaba repetido en MiPedido@confirmarPedido,
     * PedidoController@repetir y el checkout de la API.
     *
     * @param  array<int>  $ingredientesElegidos
     * @return array{precio_unitario: float, ingredientes: Collection<int, Ingrediente>}|null
     */
    public function calcularItemVigente(array $ingredientesElegidos): ?array
    {
        // Un producto de una categoria desactivada no deberia poder comprarse,
        // aunque el producto en si siga marcado como activo (ej. el admin
        // apaga toda la categoria "Veganas" de una sola vez)
        if (! $this->activo || ! $this->categoria->activo) {
            return null;
        }

        $vigentes = $this->ingredientes
            ->whereIn('id', $ingredientesElegidos)
            ->where('activo', true);

        return [
            'precio_unitario' => (float) $this->precio + (float) $vigentes->sum('precio_extra'),
            'ingredientes' => $vigentes,
        ];
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

    /**
     * Si el producto ya fue vendido alguna vez, no se puede borrar (la FK de
     * item_pedidos.producto_id es restrictOnDelete): borrarlo destruiria el
     * historial de esos pedidos. Para dar de baja un producto vendido se usa
     * "activo=false", nunca un hard delete.
     */
    public function tieneVentasAsociadas(): bool
    {
        return $this->itemPedidos()->exists();
    }
}
