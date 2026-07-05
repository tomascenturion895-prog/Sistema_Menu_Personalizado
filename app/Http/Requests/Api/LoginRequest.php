<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Cualquiera puede intentar loguearse: no requiere estar autenticado antes
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
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            // Nombre libre para identificar el token en la lista de tokens del usuario
            // (ej: "postman", "app-movil"). Si no se manda, el controlador pone uno por defecto
            'nombre_dispositivo' => ['nullable', 'string', 'max:255'],
        ];
    }
}
