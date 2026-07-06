<?php

namespace Tests\Feature;

use App\Livewire\Menu\Index;
use App\Livewire\Menu\MiPedido;
use App\Livewire\Menu\Personalizar;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MenuPublicoTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_visitante_puede_ver_el_menu_sin_registrarse(): void
    {
        $response = $this->get('/menu');

        $response->assertOk();
    }

    public function test_un_visitante_puede_ver_la_pagina_de_personalizacion(): void
    {
        $producto = Producto::factory()->create(['activo' => true]);

        $response = $this->get(route('menu.personalizar', $producto));

        $response->assertOk();
    }

    public function test_un_visitante_puede_agregar_un_combo_sin_loguearse(): void
    {
        $producto = Producto::factory()->create(['activo' => true]);

        Livewire::test(Index::class)
            ->call('agregarCombo', $producto->id)
            ->assertNoRedirect();

        // Armar el carrito es libre: no hace falta cuenta para esto
        $this->assertCount(1, session('carrito', []));
    }

    public function test_un_visitante_puede_armar_su_burger_sin_loguearse(): void
    {
        $producto = Producto::factory()->create(['activo' => true]);

        Livewire::test(Personalizar::class, ['producto' => $producto])
            ->call('agregarAlPedido')
            ->assertRedirect(route('menu.index', absolute: false));

        $this->assertCount(1, session('carrito', []));
    }

    public function test_un_visitante_puede_ver_su_carrito_sin_loguearse(): void
    {
        $response = $this->get('/mi-pedido');

        $response->assertOk();
    }

    public function test_un_visitante_que_confirma_su_pedido_es_enviado_al_login(): void
    {
        // Armar y revisar el carrito es libre, pero confirmar la compra exige cuenta
        $this->withSession(['carrito' => [
            ['producto_id' => 1, 'nombre' => 'A', 'cantidad' => 1, 'precio_unitario' => 100.0, 'ingredientes_elegidos' => [], 'ingredientes_nombres' => []],
        ]]);

        Livewire::test(MiPedido::class)
            ->call('confirmarPedido')
            ->assertRedirect(route('login', absolute: false));

        // El carrito sigue intacto: nada se perdio al mandarlo a loguearse
        $this->assertCount(1, session('carrito'));
        $this->assertSame(route('menu.mi-pedido', absolute: false), parse_url(session('url.intended'), PHP_URL_PATH));
    }

    public function test_la_landing_muestra_productos_destacados(): void
    {
        $producto = Producto::factory()->create(['activo' => true, 'nombre' => 'La Clásica Capa8']);

        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('La Clásica Capa8');
    }

    public function test_la_busqueda_filtra_los_productos_del_menu(): void
    {
        $categoria = Categoria::factory()->create(['activo' => true]);
        Producto::factory()->create(['categoria_id' => $categoria->id, 'activo' => true, 'nombre' => 'Veggie Refactor']);
        Producto::factory()->create(['categoria_id' => $categoria->id, 'activo' => true, 'nombre' => 'Doble Deploy']);

        Livewire::test(Index::class)
            ->set('busqueda', 'veggie')
            ->assertSee('Veggie Refactor')
            ->assertDontSee('Doble Deploy');
    }
}
