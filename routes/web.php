<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PuntoInteresController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ClienteMuseoController;
use App\Http\Controllers\ClienteEventosController;
use App\Http\Controllers\PublicitaController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\PanoramaController;
use Illuminate\Support\Facades\Route;

/* --- RUTAS PÚBLICAS (TURISTAS) --- */
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/', [PuntoInteresController::class, 'index'])->name('puntos.index');

// El turista busca y ve, pero no edita
Route::get('/buscar', [PuntoInteresController::class, 'index'])->name('puntos.buscar');
Route::get('/lugar/{slug}', [PuntoInteresController::class, 'show'])->name('puntos.show');


Route::get('/labrujula', fn() => redirect('/', 301));
// Redirect 301 de URLs antiguas a la URL canónica /lugar/{slug}
Route::get('/atractivos/{atractivo}', fn($slug) => redirect()->route('puntos.show', $slug, 301))->name('atractivos.show');
Route::get('/atractivos/categoria/{categoria}', [PuntoInteresController::class, 'filtrarPorCategoria'])->name('atractivos.categoria');
Route::get('/atractivos/ciudad/{ciudad}', [PuntoInteresController::class, 'filtrarPorCiudad'])->name('atractivos.ciudad');
Route::get('/panoramas', [PuntoInteresController::class, 'panoramas'])->name('atractivos.panoramas');
Route::get('/registro', [PublicitaController::class, 'index'])->name('publicita.index');
Route::post('/publicita', [PublicitaController::class, 'store'])->name('publicita.store');



/* --- RUTAS PROTEGIDAS (BREEZE) --- */
Route::get('/dashboard', function () {
    $type = auth()->user()->type ?? '';
    if ($type === 'admin')   return redirect()->route('admin.stats');
    if ($type === 'cliente') return redirect()->route('cliente.perfil');
    return redirect()->route('puntos.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


/* --- RUTAS ADMINISTRADOR --- */
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/stats', [AdminController::class, 'index'])->name('stats');
    
    // Gestión de Usuarios
    Route::get('/usuarios', [AdminController::class, 'usuarios'])->name('usuarios');
    
    // Puntos de Interés General (Creados por Admin)
    Route::get('/puntos/crear', [AdminController::class, 'createPunto'])->name('puntos.create');
    Route::post('/puntos/guardar', [AdminController::class, 'storePunto'])->name('puntos.store');
    Route::get('/puntos/{punto}/editar', [AdminController::class, 'editPunto'])->name('puntos.edit');
    Route::put('/puntos/{punto}/actualizar', [AdminController::class, 'updatePunto'])->name('puntos.update');
    Route::patch('/puntos/{punto}/toggle', [AdminController::class, 'togglePunto'])->name('puntos.toggle');

    // Categorías
    Route::resource('categorias', CategoriaController::class)->except(['show']);

    // Panoramas — La Brújula
    Route::resource('panoramas', PanoramaController::class)->except(['show']);
    Route::patch('/panoramas/{panorama}/toggle', [PanoramaController::class, 'toggle'])->name('panoramas.toggle');

    // Leads de Publicita
    Route::get('/leads', [AdminController::class, 'leads'])->name('leads');
    Route::patch('/leads/{lead}/toggle', [AdminController::class, 'toggleLead'])->name('leads.toggle');

    // Gestión de Clientes (negocios)
    Route::get('/clientes', [AdminController::class, 'clientes'])->name('clientes');
    Route::get('/puntos/{punto}/activar-cliente', [AdminController::class, 'mostrarActivarCliente'])->name('clientes.activar.form');
    Route::post('/puntos/{punto}/activar-cliente', [AdminController::class, 'activarCliente'])->name('clientes.activar');
    Route::patch('/puntos/{punto}/desactivar-cliente', [AdminController::class, 'desactivarCliente'])->name('clientes.desactivar');
    Route::get('/puntos/{punto}/modulos', [AdminController::class, 'editarModulos'])->name('clientes.modulos.form');
    Route::put('/puntos/{punto}/modulos', [AdminController::class, 'actualizarModulos'])->name('clientes.modulos');
});

/* --- RUTAS CLIENTES (NEGOCIOS) --- */
Route::middleware(['auth', 'verified', 'role:cliente'])->prefix('cliente')->name('cliente.')->group(function () {
    // Dashboard: redirige a perfil
    Route::get('/mis-puntos', fn() => redirect()->route('cliente.perfil'))->name('mis-puntos');

    // Alta propia de negocio
    Route::get('/nuevo',  [ClienteController::class, 'onboarding'])->name('nuevo');
    Route::post('/nuevo', [ClienteController::class, 'crearNegocio'])->name('crear');

    // Perfil del negocio
    Route::get('/perfil', [ClienteController::class, 'perfil'])->name('perfil');                          // lista de negocios
    Route::get('/perfil/{punto}', [ClienteController::class, 'verPerfil'])->name('perfil.ver');           // detalle de uno
    Route::get('/perfil/{punto}/editar', [ClienteController::class, 'editarPerfil'])->name('perfil.editar');
    Route::put('/perfil/{punto}/actualizar', [ClienteController::class, 'actualizarPerfil'])->name('perfil.actualizar');

    // Galería de imágenes
    Route::post('/imagenes/{punto}',             [ClienteController::class, 'subirImagen'])->name('imagenes.subir');
    Route::delete('/imagenes/{punto}/{imagen}',  [ClienteController::class, 'eliminarImagen'])->name('imagenes.eliminar');

    // Actualización rápida: módulos transversales
    Route::patch('/oferta/{punto}',     [ClienteController::class, 'actualizarOferta'])->name('oferta.actualizar');
    Route::patch('/menu/{punto}',       [ClienteController::class, 'actualizarMenu'])->name('menu.actualizar');
    Route::patch('/aviso/{punto}',      [ClienteController::class, 'actualizarAviso'])->name('aviso.actualizar');
    Route::patch('/promocion/{punto}',  [ClienteController::class, 'actualizarPromocion'])->name('promocion.actualizar');

    // Módulo museo
    Route::get('/museo/{punto}', [ClienteMuseoController::class, 'index'])->name('museo');
    Route::post('/museo/{punto}/entradas', [ClienteMuseoController::class, 'guardarEntradas'])->name('museo.entradas.guardar');
    Route::post('/museo/{punto}/exposicion', [ClienteMuseoController::class, 'guardarExposicion'])->name('museo.exposicion.guardar');
    Route::delete('/museo/{punto}/exposicion/{exposicion}', [ClienteMuseoController::class, 'eliminarExposicion'])->name('museo.exposicion.eliminar');

    // Módulo agenda cultural (categoría 5)
    Route::get('/eventos/{punto}', [ClienteEventosController::class, 'index'])->name('eventos');
    Route::post('/eventos/{punto}/guardar', [ClienteEventosController::class, 'guardarEvento'])->name('eventos.guardar');
    Route::delete('/eventos/{punto}/{evento}', [ClienteEventosController::class, 'eliminarEvento'])->name('eventos.eliminar');
});

require __DIR__.'/auth.php';