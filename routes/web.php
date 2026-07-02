<?php

use App\Livewire\Admin\Categorias;
use App\Livewire\Admin\Ingredientes;
use App\Livewire\Admin\Pedidos;
use App\Livewire\Admin\Productos;
use App\Livewire\Menu\Index as MenuIndex;
use App\Livewire\Menu\MiPedido;
use App\Livewire\Menu\Personalizar;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Menu publico: cualquier usuario autenticado (cliente o admin) puede verlo y armar su pedido
Route::get('menu', MenuIndex::class)
    ->middleware(['auth', 'verified'])
    ->name('menu.index');

// {producto} se resuelve automaticamente a una instancia de Producto gracias al
// route model binding: Laravel busca el id en la URL y lo inyecta en el componente
Route::get('menu/productos/{producto}', Personalizar::class)
    ->middleware(['auth', 'verified'])
    ->name('menu.personalizar');

// Carrito del cliente: revisa lo elegido y confirma el pedido (lo guarda en la BD)
Route::get('mi-pedido', MiPedido::class)
    ->middleware(['auth', 'verified'])
    ->name('menu.mi-pedido');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Grupo de rutas exclusivo para administradores.
// El middleware 'auth' exige estar logueado, y 'admin' exige tener rol admin (ver EsAdmin).
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('dashboard', 'admin.dashboard')->name('dashboard');

    // Route::get con un componente Livewire como segundo argumento renderiza ese
    // componente como pagina completa (no hace falta crear una vista Blade aparte)
    Route::get('categorias', Categorias::class)->name('categorias');
    Route::get('productos', Productos::class)->name('productos');
    Route::get('ingredientes', Ingredientes::class)->name('ingredientes');
    Route::get('pedidos', Pedidos::class)->name('pedidos');
});

require __DIR__.'/auth.php';
