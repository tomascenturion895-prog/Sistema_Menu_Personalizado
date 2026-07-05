<?php

namespace App\Http\Resources;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PedidoResource extends JsonResource
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
            'numero' => $this->numero,
            'estado' => $this->estado,
            'estado_legible' => Pedido::ESTADOS[$this->estado] ?? $this->estado,
            'es_cancelable' => $this->esCancelable(),
            'total' => (float) $this->total,
            'observaciones' => $this->observaciones,
            'creado_en' => $this->created_at,
            'usuario' => new UserResource($this->whenLoaded('user')),
            'items' => ItemPedidoResource::collection($this->whenLoaded('items')),
        ];
    }
}
