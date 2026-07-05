<?php

namespace App\Http\Requests\Api\Admin;

use App\Models\Ingrediente;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIngredienteRequest extends FormRequest
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
            'nombre' => ['sometimes', 'required', 'string', 'max:255'],
            'tipo' => ['sometimes', 'required', Rule::in(array_keys(Ingrediente::TIPOS))],
            'precio_extra' => ['sometimes', 'required', 'numeric', 'min:0'],
            'es_vegetariano' => ['sometimes', 'boolean'],
            'es_vegano' => ['sometimes', 'boolean'],
            'sin_gluten' => ['sometimes', 'boolean'],
            'activo' => ['sometimes', 'boolean'],
        ];
    }
}
