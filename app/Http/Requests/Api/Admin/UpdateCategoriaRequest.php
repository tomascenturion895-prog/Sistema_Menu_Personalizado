<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoriaRequest extends FormRequest
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
            // 'sometimes': un PATCH puede mandar solo el campo que cambia,
            // no hace falta reenviar toda la categoria para actualizar un dato
            'nombre' => ['sometimes', 'required', 'string', 'max:255'],
            'descripcion' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'tipo_dieta' => ['sometimes', 'required', 'in:normal,vegetariano,vegano,celiaco'],
            'activo' => ['sometimes', 'boolean'],
        ];
    }
}
