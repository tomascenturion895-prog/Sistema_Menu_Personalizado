<?php

namespace Tests\Feature;

use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * El admin gestiona el negocio, no compra hamburguesas para si mismo: verifica
 * que las rutas de compra del cliente lo redirijan de vuelta al dashboard,
 * sin importar si entra a proposito o por un link viejo.
 */
class AdminNoPuedeComprarTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_admin_no_puede_ver_el_menu(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);

        $this->actingAs($admin)->get('/menu')->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_un_admin_no_puede_personalizar_un_producto(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);
        $producto = Producto::factory()->create();

        $this->actingAs($admin)->get(route('menu.personalizar', $producto))
            ->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_un_admin_no_puede_ver_su_carrito(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);

        $this->actingAs($admin)->get('/mi-pedido')->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_un_admin_no_puede_ver_mis_pedidos(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);

        $this->actingAs($admin)->get('/mis-pedidos')->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_un_admin_no_puede_ver_el_detalle_de_un_pedido_propio(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);
        $pedido = Pedido::factory()->create(['user_id' => $admin->id]);

        $this->actingAs($admin)->get(route('cliente.pedidos.show', $pedido))
            ->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_un_invitado_sigue_viendo_el_menu_normalmente(): void
    {
        // El middleware no debe afectar a nadie que no sea admin
        $this->get('/menu')->assertOk();
    }

    public function test_un_cliente_normal_sigue_pudiendo_comprar(): void
    {
        $cliente = User::factory()->create(['rol' => 'cliente']);

        $this->actingAs($cliente)->get('/menu')->assertOk();
        $this->actingAs($cliente)->get('/mi-pedido')->assertOk();
        $this->actingAs($cliente)->get('/mis-pedidos')->assertOk();
    }

    public function test_el_admin_no_ve_menu_carrito_ni_mis_pedidos_en_la_navbar(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);

        // "/" es una de las pocas paginas de cliente a las que el admin SI puede
        // entrar (no esta bloqueada por el middleware 'cliente'), asi que sirve
        // para confirmar que la navbar compartida le oculta estos links
        $this->actingAs($admin)->get('/')
            ->assertOk()
            ->assertDontSee(__('Menú'))
            ->assertDontSee(__('Carrito'))
            ->assertDontSee(__('Mis pedidos'))
            ->assertSee('Panel Admin');
    }

    public function test_un_cliente_normal_si_ve_menu_carrito_y_mis_pedidos_en_la_navbar(): void
    {
        $cliente = User::factory()->create(['rol' => 'cliente']);

        $this->actingAs($cliente)->get('/')
            ->assertOk()
            ->assertSee(__('Menú'))
            ->assertSee(__('Carrito'))
            ->assertSee(__('Mis pedidos'));
    }
}
