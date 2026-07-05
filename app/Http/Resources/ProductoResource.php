<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
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
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'precio' => (float) $this->precio,
            'imagen' => $this->imagen,
            'activo' => $this->activo,
            // whenLoaded evita una consulta extra: si el controlador no precargo la
            // relacion (with('categoria')), esta clave directamente no aparece en el JSON
            'categoria' => new CategoriaResource($this->whenLoaded('categoria')),
            'ingredientes' => IngredienteResource::collection($this->whenLoaded('ingredientes')),
        ];
    }
}
