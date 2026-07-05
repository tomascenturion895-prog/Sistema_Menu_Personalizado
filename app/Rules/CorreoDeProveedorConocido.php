<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Exige que el correo sea de un proveedor conocido (Gmail, Hotmail, etc.),
 * en vez de aceptar cualquier dominio: evita registros con correos inventados
 * o de un dominio propio que nadie puede verificar.
 */
class CorreoDeProveedorConocido implements ValidationRule
{
    /**
     * @var array<int, string>
     */
    private const PROVEEDORES = [
        'gmail.com',
        'hotmail.com',
        'outlook.com',
        'outlook.com.ar',
        'live.com',
        'yahoo.com',
        'yahoo.com.ar',
        'icloud.com',
    ];

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $dominio = Str::lower(Str::after((string) $value, '@'));

        if (! in_array($dominio, self::PROVEEDORES, true)) {
            $fail('El correo debe ser de un proveedor conocido (Gmail, Hotmail, Outlook, Yahoo o iCloud).');
        }
    }
}
