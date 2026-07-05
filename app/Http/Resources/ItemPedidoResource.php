<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemPedidoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'producto' => new ProductoResource($this->whenLoaded('producto')),
            // Snapshot del nombre al momento de la compra: no cambia aunque el
            // producto se renombre despues (a diferencia de producto.nombre)
            'nombre_producto' => $this->nombre_producto,
            'cantidad' => $this->cantidad,
            'precio_unitario' => (float) $this->precio_unitario,
            'subtotal' => (float) $this->precio_unitario * $this->cantidad,
            // Traduce los ids elegidos a nombres, igual que hace la vista Blade del detalle.
            // Necesita 'producto.ingredientes' precargado; si no, devuelve solo los ids
            'ingredientes_elegidos' => $this->ingredientes_elegidos,
            'ingredientes_nombres' => $this->whenLoaded('producto', fn () => $this->producto->relationLoaded('ingredientes')
                ? $this->producto->ingredientes->whereIn('id', $this->ingredientes_elegidos ?? [])->pluck('nombre')->values()
                : []
            ),
        ];
    }
}
