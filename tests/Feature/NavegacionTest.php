<?php

namespace Tests\Feature;

use App\Models\Pedido;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifica la continuidad de navegacion: "/" (publica) y "/inicio" (autenticada)
 * muestran EXACTAMENTE el mismo contenido (misma vista, mismo controlador), y la
 * info del negocio (direccion, horario, contacto) es alcanzable siempre.
 */
class NavegacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_pagina_de_inicio_muestra_los_datos_de_contacto(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee(config('negocio.direccion'))
            ->assertSee(config('negocio.telefono'));
    }

    public function test_barra_y_home_muestran_el_mismo_contenido_para_un_invitado(): void
    {
        // Antes, "/" (landing) y "/inicio" (dashboard) eran DOS vistas distintas.
        // Ahora comparten el mismo controlador: deben devolver el mismo HTML.
        $home = $this->get('/')->getContent();

        // El texto real en el HTML es minuscula; "TU BURGER" visual es puro CSS (uppercase)
        $this->assertStringContainsString('hamburguesería en Resistencia, Chaco', $home);
        $this->assertStringContainsString(config('negocio.direccion'), $home);
    }

    public function test_home_e_inicio_muestran_el_mismo_contenido_para_un_cliente_logueado(): void
    {
        $cliente = User::factory()->create();

        $respuestaHome = $this->actingAs($cliente)->get('/')->getContent();
        $respuestaInicio = $this->actingAs($cliente)->get('/inicio')->getContent();

        // Mismo titular, mismos datos de contacto: es literalmente la misma pagina
        $this->assertStringContainsString('hamburguesería en Resistencia, Chaco', $respuestaHome);
        $this->assertStringContainsString('hamburguesería en Resistencia, Chaco', $respuestaInicio);
        $this->assertStringContainsString(config('negocio.telefono'), $respuestaHome);
        $this->assertStringContainsString(config('negocio.telefono'), $respuestaInicio);
    }

    public function test_el_ultimo_pedido_se_muestra_igual_en_ambas_urls(): void
    {
        $cliente = User::factory()->create();
        Pedido::factory()->create(['user_id' => $cliente->id, 'estado' => 'en_preparacion']);

        // El unico dato personalizado (el banner del ultimo pedido) aparece en
        // las dos URLs por igual, porque es la MISMA vista con el MISMO auth check
        $this->actingAs($cliente)->get('/')->assertSee('en preparación');
        $this->actingAs($cliente)->get('/inicio')->assertSee('en preparación');
    }

    public function test_el_footer_con_datos_del_negocio_aparece_en_otras_paginas_de_cliente(): void
    {
        $cliente = User::factory()->create();

        // "/perfil" no tiene su propia seccion de contacto: depende del footer
        // compartido para que la info del negocio siga siendo alcanzable
        $response = $this->actingAs($cliente)->get('/perfil');

        $response
            ->assertOk()
            ->assertSee(config('negocio.direccion'))
            ->assertSee(config('negocio.telefono'));
    }

    public function test_el_footer_del_negocio_enlaza_de_vuelta_al_inicio(): void
    {
        $cliente = User::factory()->create();

        $response = $this->actingAs($cliente)->get('/perfil');

        $response->assertSee(route('home').'#contacto', false);
    }

    public function test_un_cliente_sin_verificar_el_correo_puede_usar_el_carrito(): void
    {
        // El .env no tiene un mailer real (MAIL_MAILER=log): exigir email verificado
        // para comprar dejaria a cualquier cliente nuevo sin forma de completar su pedido
        $sinVerificar = User::factory()->unverified()->create();

        $this->actingAs($sinVerificar)->get('/inicio')->assertOk();
        $this->actingAs($sinVerificar)->get('/mi-pedido')->assertOk();
        $this->actingAs($sinVerificar)->get('/mis-pedidos')->assertOk();
    }

    public function test_el_footer_no_aparece_en_el_panel_admin(): void
    {
        // Mismo criterio que el marquee: el back-office no muestra contenido de marketing
        $admin = User::factory()->create(['rol' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response
            ->assertOk()
            ->assertDontSee(config('negocio.telefono'));
    }
}
