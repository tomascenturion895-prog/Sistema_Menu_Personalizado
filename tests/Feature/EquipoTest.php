<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifica la pagina publica de perfiles de desarrolladores (requisito de
 * la consigna del proyecto).
 */
class EquipoTest extends TestCase
{
    // La pagina de inicio (que enlaza a /equipo) consulta productos destacados,
    // asi que el test que la visita necesita las tablas migradas
    use RefreshDatabase;

    public function test_la_pagina_de_equipo_es_publica_y_muestra_a_los_tres_desarrolladores(): void
    {
        $response = $this->get('/equipo');

        $response
            ->assertOk()
            ->assertSee('Centurion Tomas')
            ->assertSee('Backend')
            ->assertSee('Benitez Apolo')
            ->assertSee('Frontend')
            ->assertSee('Benitez Antonia')
            ->assertSee('Base de datos');
    }

    public function test_sin_link_real_no_arma_un_href_roto(): void
    {
        // Ninguno de los 3 tiene github/linkedin cargado todavia (config/equipo.php);
        // la vista no debe generar un <a href=""> apuntando a ningun lado
        $response = $this->get('/equipo');

        $response->assertDontSee('href=""', false);
    }

    public function test_el_inicio_enlaza_a_la_pagina_de_equipo(): void
    {
        $this->get('/')->assertSee(route('equipo'), false);
    }
}
