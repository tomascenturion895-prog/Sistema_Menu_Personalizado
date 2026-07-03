<?php

use App\Livewire\Admin\Categorias;
use App\Livewire\Admin\Ingredientes;
use App\Livewire\Admin\Pedidos;
use App\Livewire\Admin\Productos;
use App\Livewire\Menu\Index as MenuIndex;
use App\Livewire\Menu\MiPedido;
use App\Livewire\Menu\Personalizar;
use App\Models\Categoria;
use App\Models\Ingrediente;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Support\Facades\Route;

// Landing publica: ademas del hero, muestra una seleccion real de productos destacados
Route::get('/', function () {
    return view('welcome', [
        'destacados' => Producto::where('activo', true)->with('categoria')->take(3)->get(),
    ]);
})->name('home');

// La URL es /inicio (en español, como pide la consigna) pero el nombre interno
// sigue siendo "dashboard" porque Breeze y sus tests lo referencian asi
Route::view('inicio', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Menu PUBLICO: cualquiera puede ver la carta y armar su hamburguesa sin registrarse.
// El login se exige recien al momento de agregar al pedido (dentro de los componentes)
Route::get('menu', MenuIndex::class)->name('menu.index');

// {producto} se resuelve automaticamente a una instancia de Producto gracias al
// route model binding: Laravel busca el id en la URL y lo inyecta en el componente
Route::get('menu/productos/{producto}', Personalizar::class)->name('menu.personalizar');

// Carrito del cliente: revisa lo elegido y confirma el pedido (lo guarda en la BD).
// Esto si requiere estar logueado: aca ya se esta comprando
Route::get('mi-pedido', MiPedido::class)
    ->middleware(['auth', 'verified'])
    ->name('menu.mi-pedido');

Route::view('perfil', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Grupo de rutas exclusivo para administradores.
// El middleware 'auth' exige estar logueado, y 'admin' exige tener rol admin (ver EsAdmin).
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Usamos un closure (en vez de Route::view) para poder pasarle a la vista
    // los contadores que muestran las tarjetas de estadisticas del panel
    Route::get('dashboard', function () {
        return view('admin.dashboard', [
            'totalCategorias' => Categoria::count(),
            'totalProductos' => Producto::count(),
            'totalIngredientes' => Ingrediente::count(),
            'pedidosPendientes' => Pedido::whereIn('estado', ['pendiente', 'confirmado', 'en_preparacion'])->count(),
        ]);
    })->name('dashboard');

    // Route::get con un componente Livewire como segundo argumento renderiza ese
    // componente como pagina completa (no hace falta crear una vista Blade aparte)
    Route::get('categorias', Categorias::class)->name('categorias');
    Route::get('productos', Productos::class)->name('productos');
    Route::get('ingredientes', Ingredientes::class)->name('ingredientes');
    Route::get('pedidos', Pedidos::class)->name('pedidos');
});

require __DIR__.'/auth.php';
