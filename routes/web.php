<?php

use App\Livewire\Admin\Categorias;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

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
});

require __DIR__.'/auth.php';
