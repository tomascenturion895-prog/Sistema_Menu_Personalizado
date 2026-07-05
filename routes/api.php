<?php

use App\Http\Controllers\Api\Admin\CategoriaController as AdminCategoriaController;
use App\Http\Controllers\Api\Admin\IngredienteController as AdminIngredienteController;
use App\Http\Controllers\Api\Admin\PedidoController as AdminPedidoController;
use App\Http\Controllers\Api\Admin\ProductoController as AdminProductoController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\Cliente\PedidoController as ClientePedidoController;
use App\Http\Controllers\Api\ProductoController;
use Illuminate\Support\Facades\Route;

// Todo bajo /api/v1: versionar desde el arranque evita romper a quien ya
// consuma la API el dia que haya que hacer un cambio incompatible (v2)
Route::prefix('v1')->name('api.')->group(function () {

    // Login publico: devuelve un token Sanctum para usar en las rutas de abajo
    Route::post('login', [AuthController::class, 'login'])->name('login');

    // Menu publico: mismos datos que ve un visitante sin loguearse en /menu
    Route::get('categorias', [CategoriaController::class, 'index'])->name('categorias.index');
    Route::get('productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::get('productos/{producto}', [ProductoController::class, 'show'])->name('productos.show');

    // Rutas que requieren un token valido (Authorization: Bearer {token})
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        // Historial de pedidos del cliente autenticado (equivalente a /mis-pedidos)
        Route::get('pedidos', [ClientePedidoController::class, 'index'])->name('pedidos.index');
        Route::get('pedidos/{pedido}', [ClientePedidoController::class, 'show'])->name('pedidos.show');

        // Administracion: mismo middleware 'admin' que usa el panel Livewire
        Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
            Route::apiResource('categorias', AdminCategoriaController::class);
            Route::apiResource('ingredientes', AdminIngredienteController::class);
            Route::apiResource('productos', AdminProductoController::class);

            // Los pedidos no se crean ni se borran por API: solo se consultan y
            // se avanza su estado (misma regla que el panel admin)
            Route::get('pedidos', [AdminPedidoController::class, 'index'])->name('pedidos.index');
            Route::get('pedidos/{pedido}', [AdminPedidoController::class, 'show'])->name('pedidos.show');
            Route::patch('pedidos/{pedido}/estado', [AdminPedidoController::class, 'actualizarEstado'])->name('pedidos.estado');
        });
    });
});
