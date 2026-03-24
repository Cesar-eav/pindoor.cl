<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PuntoInteresController; // Importante crearlo luego
use App\Http\Controllers\AdminController;        // Importante crearlo luego
use Illuminate\Support\Facades\Route;

/* --- RUTAS PÚBLICAS (TURISTAS) --- */
Route::get('/', function () {
    return view('welcome'); // Aquí irá tu mapa de Valpo
})->name('home');

// El turista busca y ve, pero no edita
Route::get('/buscar', [PuntoInteresController::class, 'index'])->name('puntos.index');
Route::get('/lugar/{slug}', [PuntoInteresController::class, 'show'])->name('puntos.show');


/* --- RUTAS PROTEGIDAS (BREEZE) --- */
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


/* --- RUTAS ADMINISTRADOR --- */
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/stats', [AdminController::class, 'index'])->name('stats');
    
    // Gestión de Usuarios
    Route::get('/usuarios', [AdminController::class, 'usuarios'])->name('usuarios');
    
    // Puntos de Interés General (Creados por Admin)
    Route::get('/puntos/crear', [AdminController::class, 'createPunto'])->name('puntos.create');
    Route::post('/puntos/guardar', [AdminController::class, 'storePunto'])->name('puntos.store');
});

/* --- RUTAS CLIENTES (NEGOCIOS) --- */
Route::middleware(['auth', 'role:cliente'])->prefix('cliente')->name('cliente.')->group(function () {
    // Aquí el cliente gestiona sus propios puntos
    Route::get('/mis-puntos', [PuntoInteresController::class, 'misPuntos'])->name('mis-puntos');
    Route::get('/nuevo-punto', [PuntoInteresController::class, 'create'])->name('create');
    Route::post('/guardar-punto', [PuntoInteresController::class, 'store'])->name('store');
});

require __DIR__.'/auth.php';