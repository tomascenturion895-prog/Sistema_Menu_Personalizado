<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Autenticacion de la API via tokens (Laravel Sanctum).
 *
 * MVC: el "Modelo" es User (con el trait HasApiTokens), la "Vista" es el JSON
 * armado con UserResource, y este controlador es la "C" que valida y autentica.
 */
class AuthController extends Controller
{
    /**
     * Recibe email + password, devuelve un token Bearer para usar en las
     * rutas protegidas ("Authorization: Bearer {token}").
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $this->asegurarSinDemasiadosIntentos($request);

        $user = User::where('email', $request->validated('email'))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            RateLimiter::hit($this->throttleKey($request));

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey($request));

        $nombreToken = $request->validated('nombre_dispositivo') ?? 'api';
        $token = $user->createToken($nombreToken);

        return response()->json([
            'usuario' => new UserResource($user),
            'token' => $token->plainTextToken,
        ]);
    }

    /**
     * Revoca UNICAMENTE el token usado en esta request (no todos los del usuario):
     * cerrar sesion en un dispositivo no debe desloguear los demas.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['mensaje' => 'Sesión cerrada.']);
    }

    /**
     * Mismo criterio de rate limiting que el login web (LoginForm):
     * 5 intentos fallidos por combinacion email+ip antes de bloquear.
     */
    private function asegurarSinDemasiadosIntentos(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        event(new Lockout($request));

        $segundos = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $segundos,
                'minutes' => ceil($segundos / 60),
            ]),
        ]);
    }

    private function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->string('email')).'|'.$request->ip());
    }
}
