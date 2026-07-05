<?php

namespace Tests\Feature;

use App\Livewire\Admin\Ingredientes;
use App\Models\Ingrediente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminIngredientesTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_admin_puede_crear_un_ingrediente_con_stock(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);

        Livewire::actingAs($admin)
            ->test(Ingredientes::class)
            ->set('nombre', 'Cheddar extra')
            ->set('tipo', 'topping')
            ->set('precio_extra', '500')
            ->set('stock', '25')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('ingredientes', ['nombre' => 'Cheddar extra', 'stock' => 25]);
    }

    public function test_el_stock_no_puede_ser_negativo(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);

        Livewire::actingAs($admin)
            ->test(Ingredientes::class)
            ->set('nombre', 'Panceta')
            ->set('tipo', 'topping')
            ->set('precio_extra', '500')
            ->set('stock', '-5')
            ->call('guardar')
            ->assertHasErrors(['stock']);
    }

    public function test_editar_un_ingrediente_precarga_su_stock_actual(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);
        $ingrediente = Ingrediente::factory()->create(['stock' => 42]);

        Livewire::actingAs($admin)
            ->test(Ingredientes::class)
            ->call('abrirModalEditar', $ingrediente->id)
            ->assertSet('stock', '42');
    }

    public function test_un_ingrediente_sin_stock_se_marca_visualmente(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);
        Ingrediente::factory()->create(['nombre' => 'Salsa agotada', 'stock' => 0]);

        $response = $this->actingAs($admin)->get(route('admin.ingredientes'));

        $response->assertOk()->assertSee('Sin stock');
    }

    public function test_la_paginacion_usa_el_diseño_propio_del_sistema(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);
        // La pagina admin de ingredientes pagina de a 8: con 10 se fuerza una segunda pagina
        Ingrediente::factory()->count(10)->create();

        $response = $this->actingAs($admin)->get(route('admin.ingredientes'));

        // La vista custom usa btn-retro y "Mostrando X a Y de Z resultados",
        // en vez del "Previous"/"Next" generico de la paginacion default de Laravel
        $response
            ->assertOk()
            ->assertSee('Mostrando')
            ->assertSee('resultados')
            ->assertDontSee('Previous')
            ->assertDontSee('Next');
    }
}
