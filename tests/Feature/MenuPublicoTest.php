<?php

namespace Tests\Feature;

use App\Livewire\Menu\Index;
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

    public function test_un_visitante_que_agrega_un_combo_es_enviado_al_login(): void
    {
        $producto = Producto::factory()->create(['activo' => true]);

        Livewire::test(Index::class)
            ->call('agregarCombo', $producto->id)
            ->assertRedirect(route('login', absolute: false));

        // El carrito sigue vacio: no se agrego nada sin autenticacion
        $this->assertEmpty(session('carrito', []));
    }

    public function test_un_visitante_que_confirma_su_burger_es_enviado_al_login(): void
    {
        $producto = Producto::factory()->create(['activo' => true]);

        Livewire::test(Personalizar::class, ['producto' => $producto])
            ->call('agregarAlPedido')
            ->assertRedirect(route('login', absolute: false));

        $this->assertEmpty(session('carrito', []));
    }

    public function test_el_carrito_de_mi_pedido_sigue_requiriendo_login(): void
    {
        $response = $this->get('/mi-pedido');

        $response->assertRedirect(route('login', absolute: false));
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
