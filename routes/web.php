<?php

use App\Http\Controllers\Admin\PanelController as AdminPanelController;
use App\Http\Controllers\Cliente\PedidoController;
use App\Http\Controllers\InicioController;
use App\Livewire\Admin\Categorias;
use App\Livewire\Admin\Ingredientes;
use App\Livewire\Admin\Pedidos;
use App\Livewire\Admin\Productos;
use App\Livewire\Menu\Index as MenuIndex;
use App\Livewire\Menu\MiPedido;
use App\Livewire\Menu\Personalizar;
use Illuminate\Support\Facades\Route;

// Pagina de inicio: MISMO controlador y MISMA vista en ambas URLs, para que
// un visitante (en "/") y un cliente logueado (en "/inicio") vean exactamente
// lo mismo. La unica diferencia posible (el banner del ultimo pedido) la
// resuelve la propia vista con @auth, no una vista distinta.
Route::get('/', InicioController::class)->name('home');

// La URL es /inicio (en español, como pide la consigna) pero el nombre interno
// sigue siendo "dashboard" porque Breeze y sus tests lo referencian asi.
// Sin 'verified': el .env no tiene un mailer real (MAIL_MAILER=log), asi que
// exigir el correo verificado dejaria a cualquier cliente nuevo sin poder
// comprar. 'auth' alcanza: sigue exigiendo estar logueado.
Route::get('inicio', InicioController::class)
    ->middleware(['auth'])
    ->name('dashboard');

// Historial de pedidos del cliente (controlador clasico, patron MVC completo)
Route::middleware(['auth'])->prefix('mis-pedidos')->name('cliente.pedidos.')->group(function () {
    Route::get('/', [PedidoController::class, 'index'])->name('index');

    // La ruta fija va ANTES que la variable {pedido}, para que "exito" no se
    // interprete como un id de pedido
    Route::get('{pedido}/exito', [PedidoController::class, 'exito'])->name('exito');
    Route::get('{pedido}', [PedidoController::class, 'show'])->name('show');

    // Acciones sobre un pedido: cancelar (solo pendiente) y volver a pedirlo.
    // Son PATCH/POST porque MODIFICAN estado: nunca se cambia nada con un GET
    Route::patch('{pedido}/cancelar', [PedidoController::class, 'cancelar'])->name('cancelar');
    Route::post('{pedido}/repetir', [PedidoController::class, 'repetir'])->name('repetir');
});

// Menu PUBLICO: cualquiera puede ver la carta y armar su hamburguesa sin registrarse.
// El login se exige recien al momento de agregar al pedido (dentro de los componentes)
Route::get('menu', MenuIndex::class)->name('menu.index');

// {producto} se resuelve automaticamente a una instancia de Producto gracias al
// route model binding: Laravel busca el id en la URL y lo inyecta en el componente
Route::get('menu/productos/{producto}', Personalizar::class)->name('menu.personalizar');

// Carrito del cliente: revisa lo elegido y confirma el pedido (lo guarda en la BD).
// Esto si requiere estar logueado: aca ya se esta comprando (sin 'verified',
// por la misma razon que las rutas de arriba)
Route::get('mi-pedido', MiPedido::class)
    ->middleware(['auth'])
    ->name('menu.mi-pedido');

Route::view('perfil', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Grupo de rutas exclusivo para administradores.
// El middleware 'auth' exige estar logueado, y 'admin' exige tener rol admin (ver EsAdmin).
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [AdminPanelController::class, 'dashboard'])->name('dashboard');

    // Route::get con un componente Livewire como segundo argumento renderiza ese
    // componente como pagina completa (no hace falta crear una vista Blade aparte)
    Route::get('categorias', Categorias::class)->name('categorias');
    Route::get('productos', Productos::class)->name('productos');
    Route::get('ingredientes', Ingredientes::class)->name('ingredientes');
    Route::get('pedidos', Pedidos::class)->name('pedidos');
});

require __DIR__.'/auth.php';
