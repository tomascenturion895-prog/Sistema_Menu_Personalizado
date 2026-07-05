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

    // Orden del flujo de cocina: el admin puede saltar etapas hacia ADELANTE
    // (ej. marcar "listo" directo sin pasar por "en_preparacion"), pero nunca
    // retroceder. "listo" y "cancelado" quedan fuera de este orden: son estados
    // terminales, no forman parte del avance normal.
    private const ORDEN_FLUJO = ['pendiente', 'confirmado', 'en_preparacion', 'listo'];

    // Estados desde los que ya no se puede cambiar a NINGUN otro estado
    private const ESTADOS_TERMINALES = ['listo', 'cancelado'];

    /**
     * Un pedido solo se puede cancelar mientras la cocina no lo haya tomado.
     */
    public function esCancelable(): bool
    {
        return $this->estado === 'pendiente';
    }

    /**
     * Valida que el cambio de estado respete el flujo de cocina: no se puede
     * "revivir" un pedido ya cancelado o entregado, ni retroceder a una etapa
     * anterior (ej. de "listo" de vuelta a "en_preparacion"). Se puede
     * avanzar salteando etapas (ej. "pendiente" directo a "listo").
     * Usado tanto por el panel admin Livewire como por la API.
     */
    public function puedeTransicionarA(string $nuevoEstado): bool
    {
        if (in_array($this->estado, self::ESTADOS_TERMINALES, true)) {
            return false;
        }

        if ($nuevoEstado === 'cancelado') {
            return true;
        }

        $posicionActual = array_search($this->estado, self::ORDEN_FLUJO, true);
        $posicionNueva = array_search($nuevoEstado, self::ORDEN_FLUJO, true);

        return $posicionActual !== false && $posicionNueva !== false && $posicionNueva > $posicionActual;
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
