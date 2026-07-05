<?php

namespace App\Http\Requests\Api\Admin;

use App\Models\Ingrediente;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIngredienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'tipo' => ['required', Rule::in(array_keys(Ingrediente::TIPOS))],
            'precio_extra' => ['required', 'numeric', 'min:0'],
            // Opcional: si no se manda, la columna default (0) se encarga
            'stock' => ['nullable', 'integer', 'min:0'],
            'es_vegetariano' => ['boolean'],
            'es_vegano' => ['boolean'],
            'sin_gluten' => ['boolean'],
            'activo' => ['boolean'],
        ];
    }
}
