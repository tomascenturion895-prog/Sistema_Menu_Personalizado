<?php

namespace App\Models;

use Database\Factories\PedidoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'total', 'estado', 'observaciones'])]
class Pedido extends Model
{
    /** @use HasFactory<PedidoFactory> */
    use HasFactory;

    // Estados posibles del pedido con su etiqueta legible. Unica fuente de verdad:
    // el enum de la migracion, el panel admin y los badges de las vistas leen de aca
    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'confirmado' => 'Confirmado',
        'en_preparacion' => 'En preparación',
        'listo' => 'Listo',
        'cancelado' => 'Cancelado',
    ];

    /**
     * Un pedido solo se puede cancelar mientras la cocina no lo haya tomado.
     */
    public function esCancelable(): bool
    {
        return $this->estado === 'pendiente';
    }

    /**
     * Numero de ticket legible (#00042). Accessor de Eloquent: se usa como
     * $pedido->numero en las vistas, definiendo el formato UNA sola vez.
     */
    protected function numero(): Attribute
    {
        return Attribute::get(fn (): string => '#'.str_pad((string) $this->id, 5, '0', STR_PAD_LEFT));
    }

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ItemPedido::class);
    }
}
