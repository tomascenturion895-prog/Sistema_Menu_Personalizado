<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductoRequest extends FormRequest
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
            'categoria_id' => ['sometimes', 'required', 'exists:categorias,id'],
            'nombre' => ['sometimes', 'required', 'string', 'max:255'],
            'descripcion' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'precio' => ['sometimes', 'required', 'numeric', 'min:0'],
            'activo' => ['sometimes', 'boolean'],
            'ingredientes' => ['sometimes', 'array'],
            'ingredientes.*' => ['integer', 'exists:ingredientes,id'],
        ];
    }
}
