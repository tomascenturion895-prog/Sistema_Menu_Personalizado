<?php

namespace Tests\Feature;

use App\Services\Geocodificador;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeocodificadorTest extends TestCase
{
    public function test_ubica_una_direccion_y_devuelve_sus_coordenadas(): void
    {
        Cache::flush();

        $this->fakeHttp([
            'nominatim.openstreetmap.org/*' => Http::response([
                ['lat' => '-26.1849', 'lon' => '-58.1731', 'boundingbox' => ['-26.1949', '-26.1749', '-58.1831', '-58.1631']],
            ], 200),
        ]);

        $ubicacion = (new Geocodificador)->ubicar('Av. 25 de Mayo 625, Formosa, Formosa, Argentina');

        $this->assertSame(-26.1849, $ubicacion['lat']);
        $this->assertSame(-58.1731, $ubicacion['lon']);
        $this->assertSame([-26.1949, -26.1749, -58.1831, -58.1631], $ubicacion['boundingbox']);
    }

    public function test_devuelve_null_si_nominatim_no_encuentra_la_direccion(): void
    {
        Cache::flush();

        $this->fakeHttp([
            'nominatim.openstreetmap.org/*' => Http::response([], 200),
        ]);

        $this->assertNull((new Geocodificador)->ubicar('una direccion que no existe en ningun lado'));
    }

    public function test_devuelve_null_si_nominatim_responde_con_error(): void
    {
        Cache::flush();

        $this->fakeHttp([
            'nominatim.openstreetmap.org/*' => Http::response([], 503),
        ]);

        $this->assertNull((new Geocodificador)->ubicar('cualquier direccion'));
    }

    public function test_la_segunda_consulta_de_la_misma_direccion_no_repite_el_request(): void
    {
        Cache::flush();

        $this->fakeHttp([
            'nominatim.openstreetmap.org/*' => Http::response([
                ['lat' => '-26.1849', 'lon' => '-58.1731', 'boundingbox' => ['-26.1949', '-26.1749', '-58.1831', '-58.1631']],
            ], 200),
        ]);

        $geocodificador = new Geocodificador;
        $geocodificador->ubicar('Av. 25 de Mayo 625, Formosa');
        $geocodificador->ubicar('Av. 25 de Mayo 625, Formosa');

        // Nominatim pide como maximo 1 request por segundo: la cache evita el segundo llamado
        Http::assertSentCount(1);
    }
}
