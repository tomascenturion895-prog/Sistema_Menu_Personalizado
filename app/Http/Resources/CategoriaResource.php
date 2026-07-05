<?php

namespace App\Http\Resources;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoriaResource extends JsonResource
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
            'imagen' => $this->imagen,
            'tipo_dieta' => $this->tipo_dieta,
            'tipo_dieta_legible' => Categoria::TIPOS_DIETA[$this->tipo_dieta] ?? $this->tipo_dieta,
            'activo' => $this->activo,
        ];
    }
}
