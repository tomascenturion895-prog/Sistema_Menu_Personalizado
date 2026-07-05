<?php

namespace Tests\Feature;

use App\Models\Pedido;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_invitado_es_redirigido_al_login(): void
    {
        $this->get('/admin/dashboard')->assertRedirect(route('login', absolute: false));
    }

    public function test_un_cliente_no_admin_no_puede_ver_el_dashboard(): void
    {
        $cliente = User::factory()->create(['rol' => 'cliente']);

        $this->actingAs($cliente)->get('/admin/dashboard')->assertForbidden();
    }

    public function test_las_ventas_canceladas_no_cuentan_como_venta(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);

        Pedido::factory()->create(['estado' => 'confirmado', 'total' => 5000]);
        Pedido::factory()->create(['estado' => 'cancelado', 'total' => 9000]);

        // Solo se suma el pedido confirmado ($5.000); el cancelado ($9.000) queda afuera
        $this->actingAs($admin)->get('/admin/dashboard')
            ->assertOk()
            ->assertViewHas('ventasTotales', 5000.0);
    }

    public function test_muestra_la_cantidad_y_tasa_de_cancelados_de_hoy(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);

        // Sin fecha explicita, la factory los crea "ahora" (hoy): entran en la metrica
        Pedido::factory()->create(['estado' => 'confirmado']);
        Pedido::factory()->create(['estado' => 'cancelado']);
        Pedido::factory()->create(['estado' => 'cancelado']);

        // 2 de 3 pedidos de HOY cancelados = 66.7%
        $this->actingAs($admin)->get('/admin/dashboard')
            ->assertOk()
            ->assertViewHas('pedidosCanceladosHoy', 2)
            ->assertViewHas('tasaCancelacionHoy', 66.7);
    }

    public function test_un_pedido_cancelado_ayer_no_entra_en_la_metrica_de_hoy(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);

        $pedidoDeAyer = Pedido::factory()->create(['estado' => 'cancelado']);
        // Se fuerza la fecha de actualizacion a ayer, simulando que se cancelo el dia anterior
        $pedidoDeAyer->forceFill(['updated_at' => now()->subDay()])->saveQuietly();

        Pedido::factory()->create(['estado' => 'confirmado']);

        // Ningun pedido de HOY esta cancelado: 0 cancelados, 0% de tasa
        $this->actingAs($admin)->get('/admin/dashboard')
            ->assertOk()
            ->assertViewHas('pedidosCanceladosHoy', 0)
            ->assertViewHas('tasaCancelacionHoy', 0.0);
    }

    public function test_el_admin_ve_el_sidebar_y_no_la_navbar_del_cliente(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response
            ->assertOk()
            ->assertSee('Ver el sitio')
            ->assertDontSee('Carrito')
            ->assertDontSee('Mis pedidos');
    }
}
