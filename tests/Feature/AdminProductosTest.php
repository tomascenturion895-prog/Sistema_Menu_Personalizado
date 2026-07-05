<?php

namespace Tests\Feature;

use App\Livewire\Admin\Productos;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminProductosTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_admin_puede_crear_un_producto_con_foto(): void
    {
        // Storage::fake reemplaza el disco "public" por uno en memoria:
        // el test no escribe archivos reales en storage/
        Storage::fake('public');

        $admin = User::factory()->create(['rol' => 'admin']);
        $categoria = Categoria::factory()->create();

        Livewire::actingAs($admin)
            ->test(Productos::class)
            ->set('categoria_id', $categoria->id)
            ->set('nombre', 'La Fotogénica')
            ->set('precio', '7900')
            ->set('foto', UploadedFile::fake()->image('burger.jpg'))
            ->call('guardar')
            ->assertHasNoErrors();

        $producto = Producto::where('nombre', 'La Fotogénica')->first();

        // El producto quedo creado con una ruta de imagen (prefijada "storage/", el
        // formato que asset() necesita), y el archivo existe en el disco real
        $this->assertNotNull($producto->imagen);
        $this->assertStringStartsWith('storage/', $producto->imagen);
        Storage::disk('public')->assertExists(str($producto->imagen)->after('storage/')->toString());
    }

    public function test_editar_sin_subir_foto_conserva_la_imagen_existente(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);
        $categoria = Categoria::factory()->create();
        $producto = Producto::factory()->create([
            'categoria_id' => $categoria->id,
            'imagen' => 'storage/productos/foto-original.jpg',
        ]);

        Livewire::actingAs($admin)
            ->test(Productos::class)
            ->call('abrirModalEditar', $producto->id)
            ->set('nombre', 'Nombre nuevo')
            ->call('guardar')
            ->assertHasNoErrors();

        // Se actualizo el nombre pero la imagen original no se toco
        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'nombre' => 'Nombre nuevo',
            'imagen' => 'storage/productos/foto-original.jpg',
        ]);
    }

    public function test_los_productos_se_agrupan_por_categoria(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);
        $clasicas = Categoria::factory()->create(['nombre' => 'Clásicas']);
        $sinTacc = Categoria::factory()->create(['nombre' => 'Sin TACC']);

        Producto::factory()->create(['categoria_id' => $clasicas->id, 'nombre' => 'La Clásica Capa8']);
        Producto::factory()->create(['categoria_id' => $sinTacc->id, 'nombre' => 'Sin Gluten Sin Bugs']);

        $response = $this->actingAs($admin)->get(route('admin.productos'));

        // Cada producto aparece dentro de la seccion de su propia categoria
        $response
            ->assertOk()
            ->assertSeeInOrder(['Clásicas', 'La Clásica Capa8', 'Sin TACC', 'Sin Gluten Sin Bugs']);
    }

    public function test_una_categoria_sin_productos_muestra_el_estado_vacio(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);
        Categoria::factory()->create(['nombre' => 'Veganas']);

        $response = $this->actingAs($admin)->get(route('admin.productos'));

        $response
            ->assertOk()
            ->assertSee('Todavía no hay productos en esta categoría.');
    }

    public function test_el_boton_de_una_categoria_precarga_esa_categoria_en_el_formulario(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);
        $sinTacc = Categoria::factory()->create();

        Livewire::actingAs($admin)
            ->test(Productos::class)
            ->call('abrirModalCrear', $sinTacc->id)
            ->assertSet('categoria_id', $sinTacc->id);
    }
}
