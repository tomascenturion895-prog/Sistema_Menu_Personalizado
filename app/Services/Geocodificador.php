<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Geocodifica direcciones contra Nominatim (API publica de OpenStreetMap, sin
 * API key). Centralizado aca -en vez de vivir adentro de un controlador
 * puntual- para que cualquier feature futura que necesite coordenadas (un
 * mapa en el panel admin, calculo de distancia de envio, etc.) reuse la misma
 * llamada, el mismo cache y el mismo manejo de errores, sin duplicarlo.
 */
class Geocodificador
{
    /**
     * Devuelve las coordenadas de una direccion, o null si Nominatim no
     * responde o no la encuentra. Se cachea 30 dias por direccion: Nominatim
     * pide como maximo 1 request por segundo, y una direccion real practicamente
     * nunca cambia de un dia para el otro.
     *
     * @return array{lat: float, lon: float, boundingbox: array<int, float>}|null
     */
    public function ubicar(string $direccion): ?array
    {
        return Cache::remember('geocodificacion:'.md5($direccion), now()->addDays(30), function () use ($direccion) {
            try {
                $respuesta = Http::withHeaders([
                    // Nominatim exige un User-Agent identificable, si no rechaza el request
                    'User-Agent' => 'Capa8Burger/1.0 (proyecto universitario UTN)',
                ])->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $direccion,
                    'format' => 'json',
                    'limit' => 1,
                ]);

                $resultado = $respuesta->json(0);

                if (! $respuesta->successful() || ! $resultado) {
                    return null;
                }

                return [
                    'lat' => (float) $resultado['lat'],
                    'lon' => (float) $resultado['lon'],
                    // boundingbox de Nominatim viene como [sur, norte, oeste, este]
                    'boundingbox' => array_map(fn (string $valor): float => (float) $valor, $resultado['boundingbox']),
                ];
            } catch (Throwable $e) {
                // Si Nominatim esta caido o sin internet, quien llame a este
                // servicio debe poder seguir funcionando sin mapa (recibe null)
                Log::warning('No se pudo geocodificar una direccion.', ['direccion' => $direccion, 'error' => $e->getMessage()]);

                return null;
            }
        });
    }
}
