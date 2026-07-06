<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    /**
     * La pagina de inicio geocodifica la direccion del negocio contra
     * Nominatim (InicioController@ubicarDireccionDelNegocio). Sin este fake
     * por defecto, CUALQUIER test que visite "/" o "/inicio" dispararia un
     * request real a internet: lento, dependiente de un servicio externo, y
     * potencialmente bloqueado por el rate limit de Nominatim.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->fakeHttp([
            'nominatim.openstreetmap.org/*' => Http::response([
                ['lat' => '-26.1849', 'lon' => '-58.1731', 'boundingbox' => ['-26.1949', '-26.1749', '-58.1831', '-58.1631']],
            ], 200),
        ]);
    }

    /**
     * Reemplaza POR COMPLETO los fakes de Http registrados hasta ahora (los de
     * setUp() incluidos). Http::fake() normal ACUMULA reglas y, ante una URL
     * que matchea dos reglas, gana la PRIMERA registrada (la de setUp) en vez
     * de la mas nueva - por eso un test no puede simplemente volver a llamar
     * a Http::fake() para pisar el fake por defecto de este TestCase. Este
     * helper resetea el resolvedor de la fachada para que el nuevo fake
     * arranque de cero.
     *
     * @param  array<string, mixed>  $fakes
     */
    protected function fakeHttp(array $fakes): void
    {
        $this->app->forgetInstance(HttpFactory::class);
        Http::clearResolvedInstance(HttpFactory::class);

        Http::fake($fakes);
    }
}
