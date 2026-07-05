<?php

namespace App\Http\Resources;

use App\Models\Ingrediente;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IngredienteResource extends JsonResource
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
            'tipo' => $this->tipo,
            'tipo_legible' => Ingrediente::TIPOS[$this->tipo] ?? $this->tipo,
            'precio_extra' => (float) $this->precio_extra,
            'es_vegetariano' => $this->es_vegetariano,
            'es_vegano' => $this->es_vegano,
            'sin_gluten' => $this->sin_gluten,
            'activo' => $this->activo,
        ];
    }
}
