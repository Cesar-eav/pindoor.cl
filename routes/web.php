<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PuntoInteresController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;

/* --- RUTAS PÚBLICAS (TURISTAS) --- */
Route::get('/', [PuntoInteresController::class, 'index'])->name('puntos.index');

// El turista busca y ve, pero no edita
Route::get('/buscar', [PuntoInteresController::class, 'index'])->name('puntos.index');
Route::get('/lugar/{slug}', [PuntoInteresController::class, 'show'])->name('puntos.show');


Route::get('/labrujula', [PuntoInteresController::class, 'index'])->name('atractivos.index');
Route::get('/atractivos/{atractivo}', [PuntoInteresController::class, 'show'])->name('atractivos.show');
Route::get('/atractivos/categoria/{categoria}', [PuntoInteresController::class, 'filtrarPorCategoria'])->name('atractivos.categoria');
Route::get('/atractivos/ciudad/{ciudad}', [PuntoInteresController::class, 'filtrarPorCiudad'])->name('atractivos.ciudad');
Route::get('/panoramas', [PuntoInteresController::class, 'panoramas'])->name('atractivos.panoramas');



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
    Route::get('/puntos/{punto}/editar', [AdminController::class, 'editPunto'])->name('puntos.edit');
    Route::put('/puntos/{punto}/actualizar', [AdminController::class, 'updatePunto'])->name('puntos.update');
    Route::patch('/puntos/{punto}/toggle', [AdminController::class, 'togglePunto'])->name('puntos.toggle');

    // Gestión de Clientes (negocios)
    Route::get('/clientes', [AdminController::class, 'clientes'])->name('clientes');
    Route::get('/puntos/{punto}/activar-cliente', [AdminController::class, 'mostrarActivarCliente'])->name('clientes.activar.form');
    Route::post('/puntos/{punto}/activar-cliente', [AdminController::class, 'activarCliente'])->name('clientes.activar');
    Route::patch('/puntos/{punto}/desactivar-cliente', [AdminController::class, 'desactivarCliente'])->name('clientes.desactivar');
    Route::get('/puntos/{punto}/modulos', [AdminController::class, 'editarModulos'])->name('clientes.modulos.form');
    Route::put('/puntos/{punto}/modulos', [AdminController::class, 'actualizarModulos'])->name('clientes.modulos');
});

/* --- RUTAS CLIENTES (NEGOCIOS) --- */
Route::middleware(['auth', 'role:cliente'])->prefix('cliente')->name('cliente.')->group(function () {
    // Dashboard: redirige a perfil
    Route::get('/mis-puntos', fn() => redirect()->route('cliente.perfil'))->name('mis-puntos');

    // Perfil del negocio
    Route::get('/perfil', [ClienteController::class, 'perfil'])->name('perfil');
    Route::get('/perfil/editar', [ClienteController::class, 'editarPerfil'])->name('perfil.editar');
    Route::put('/perfil/actualizar', [ClienteController::class, 'actualizarPerfil'])->name('perfil.actualizar');

    // Actualización rápida: oferta del día y menú del día
    Route::patch('/oferta', [ClienteController::class, 'actualizarOferta'])->name('oferta.actualizar');
    Route::patch('/menu', [ClienteController::class, 'actualizarMenu'])->name('menu.actualizar');
});

require __DIR__.'/auth.php';