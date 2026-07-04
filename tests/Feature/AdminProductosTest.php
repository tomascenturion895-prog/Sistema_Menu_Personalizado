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

        // El producto quedo creado con una ruta de imagen, y el archivo existe en el disco
        $this->assertNotNull($producto->imagen);
        Storage::disk('public')->assertExists($producto->imagen);
    }

    public function test_editar_sin_subir_foto_conserva_la_imagen_existente(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);
        $categoria = Categoria::factory()->create();
        $producto = Producto::factory()->create([
            'categoria_id' => $categoria->id,
            'imagen' => 'productos/foto-original.jpg',
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
            'imagen' => 'productos/foto-original.jpg',
        ]);
    }
}
