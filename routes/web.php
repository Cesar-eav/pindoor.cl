<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PuntoInteresController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ClienteMuseoController;
use App\Http\Controllers\ClienteEventosController;
use App\Http\Controllers\ClienteProductosController;
use App\Http\Controllers\PublicitaController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\CategoriaGrupoController;
use App\Http\Controllers\Admin\CategoriaEventoController;
use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Admin\PanoramaController;
use App\Http\Controllers\ArtistaController;
use App\Http\Controllers\OperadorController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ExperienciaController;
use App\Http\Controllers\Admin\DistritoController;
use App\Http\Controllers\Admin\RecomendacionController;
use App\Http\Controllers\RecomiendaController;
use Illuminate\Support\Facades\Route;


/* --- IDIOMA --- */
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['es', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

/* --- RUTAS PÚBLICAS (TURISTAS) --- */
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/offline', fn() => view('offline'))->name('offline');

// En la app nativa (WebView carga desde 127.x.x.x en puerto no estándar) redirige via JS al sitio real
// Excluye puertos de desarrollo local (artisan serve = 8000, vite = 5173, etc.)
$devPorts = [8000, 8080, 3000, 5173];
if (str_starts_with(request()->getHost(), '127.') && !in_array(request()->getPort(), $devPorts)) {
    Route::get('/{any?}', function () {
        $url = 'https://pindoor.cl' . request()->getRequestUri();
        return response("<script>window.location.replace(" . json_encode($url) . ");</script>");
    })->where('any', '.*');
} else {
    Route::get('/', [PuntoInteresController::class, 'index'])->name('puntos.index');
}

// El turista busca y ve, pero no edita
Route::get('/buscar', [PuntoInteresController::class, 'index'])->name('puntos.buscar');
Route::get('/lugar/{slug}', [PuntoInteresController::class, 'show'])->name('puntos.show');
Route::get('/lugar/{slug}/producto/{producto}', [PuntoInteresController::class, 'showProducto'])->name('puntos.producto');
Route::get('/lugar/{slug}/exposicion/{item}', [PuntoInteresController::class, 'showExposicion'])->name('puntos.exposicion');
Route::get('/lugar/{slug}/evento/{item}', [PuntoInteresController::class, 'showEvento'])->name('puntos.evento');
Route::get('/lugar/{slug}/activar', [PuntoInteresController::class, 'activar'])->name('puntos.activar');


Route::get('/labrujula', fn() => redirect('/', 301));
// Redirect 301 de URLs antiguas a la URL canónica /lugar/{slug}
Route::get('/atractivos/{atractivo}', fn($slug) => redirect()->route('puntos.show', $slug, 301))->name('atractivos.show');
Route::get('/atractivos/categoria/{categoria}', [PuntoInteresController::class, 'filtrarPorCategoria'])->name('atractivos.categoria');
Route::get('/atractivos/ciudad/{ciudad}', [PuntoInteresController::class, 'filtrarPorCiudad'])->name('atractivos.ciudad');
Route::get('/explorar', [PuntoInteresController::class, 'explorar'])->name('puntos.explorar');
Route::get('/buscar/sugerencias', [PuntoInteresController::class, 'sugerencias'])->name('puntos.sugerencias');
Route::get('/categorias', [PuntoInteresController::class, 'buscar'])->name('puntos.buscar.vista');
Route::get('/info', fn() => view('puntos.info'))->name('puntos.info');
Route::get('/privacidad', fn() => view('legal.privacidad'))->name('legal.privacidad');
Route::post('/api/share', function () {
    $text = request('text', '');
    $url  = request('url', '');
    \Native\Mobile\Facades\Share::url('Pindoor', $text, $url);
    return response()->json(['ok' => true]);
})->name('api.share');
Route::get('/api/distritos', [DistritoController::class, 'json'])->name('api.distritos');
Route::get('/panoramas', [PuntoInteresController::class, 'panoramas'])->name('atractivos.panoramas');
Route::get('/panoramas/{panorama}', [PuntoInteresController::class, 'showPanorama'])->name('panoramas.show');
Route::get('/experiencias', [PuntoInteresController::class, 'experiencias'])->name('experiencias.index');
Route::get('/experiencias/proponer', [PuntoInteresController::class, 'proponerForm'])->name('experiencias.proponer');
Route::post('/experiencias/proponer', [PuntoInteresController::class, 'proponerStore'])->name('experiencias.proponer.store');
Route::get('/experiencias/{experiencia}', [PuntoInteresController::class, 'showExperiencia'])->name('experiencias.show');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/recomienda/{slug}', [RecomiendaController::class, 'show'])->name('recomienda.show');

Route::get('/registro', [PublicitaController::class, 'index'])->name('publicita.index');
Route::post('/publicita', [PublicitaController::class, 'store'])->name('publicita.store');

// Contacto clientes y artistas
Route::get('/contacto', [ContactoController::class, 'index'])->name('contacto.index');
Route::post('/contacto', [ContactoController::class, 'store'])->name('contacto.store');

// Registro artista
Route::get('/registro-artista',  [ArtistaController::class, 'showRegister'])->name('artista.register');
Route::post('/registro-artista', [ArtistaController::class, 'register'])->name('artista.register.store');

// Directorio público de artistas
Route::get('/la-escena', [ArtistaController::class, 'directorio'])->name('artista.index');

// Registro operador turístico
Route::get('/registro-operador',  [OperadorController::class, 'showRegister'])->name('operador.register');
Route::post('/registro-operador', [OperadorController::class, 'register'])->name('operador.register.store');



/* --- RUTAS PROTEGIDAS (BREEZE) --- */
Route::get('/dashboard', function () {
    $type = auth()->user()->type ?? '';
    if ($type === 'admin')    return redirect()->route('admin.stats');
    if ($type === 'cliente')  return redirect()->route('cliente.perfil');
    if ($type === 'artista')  return redirect()->route('artista.perfil');
    if ($type === 'operador') return redirect()->route('operador.perfil');
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
    Route::get('/geocodificar', [ClienteController::class, 'geocodificar'])->name('geocodificar');
    Route::get('/geocodificar-inverso', [ClienteController::class, 'geocodificarInverso'])->name('geocodificar-inverso');

    // Categorías
    Route::resource('categorias', CategoriaController::class)->except(['show']);
    Route::resource('categoria-grupos', CategoriaGrupoController::class)
        ->except(['show'])
        ->parameters(['categoria-grupos' => 'categoriaGrupo']);
    Route::resource('categoria-eventos', CategoriaEventoController::class)
        ->except(['show'])
        ->parameters(['categoria-eventos' => 'categoriaEvento']);

    // Blog
    Route::post('/blog/imagen', [PostController::class, 'uploadImagen'])->name('blog.imagen');
    Route::get('/blog/{blog}/preview', [PostController::class, 'preview'])->name('blog.preview');
    Route::resource('blog', PostController::class)->except(['show']);

    // Panoramas — La Brújula
    Route::get('/panoramas/ubicaciones', [PanoramaController::class, 'ubicaciones'])->name('panoramas.ubicaciones');
    Route::resource('panoramas', PanoramaController::class)->except(['show']);
    Route::patch('/panoramas/{panorama}/toggle', [PanoramaController::class, 'toggle'])->name('panoramas.toggle');
    Route::patch('/panoramas/{panorama}/categoria', [PanoramaController::class, 'actualizarCategoria'])->name('panoramas.categoria');
    Route::delete('/panoramas/imagenes/{imagen}', [PanoramaController::class, 'destroyImagen'])->name('panoramas.imagenes.destroy');
    Route::post('/panoramas/configuracion', [PanoramaController::class, 'configuracion'])->name('panoramas.configuracion');
    Route::get('/passline', [AdminController::class, 'passline'])->name('passline');
    Route::post('/passline', [AdminController::class, 'passlineImportar'])->name('passline.importar');
    Route::get('/portaldisc', [AdminController::class, 'portaldisc'])->name('portaldisc');
    Route::post('/portaldisc', [AdminController::class, 'portaldiscImportar'])->name('portaldisc.importar');

    // Distritos
    Route::resource('distritos', DistritoController::class)->except(['show','create','edit']);
    Route::get('distritos/editor', [DistritoController::class, 'index'])->name('distritos.editor');

    // Experiencias
    Route::resource('experiencias', ExperienciaController::class)->except(['show']);
    Route::patch('/experiencias/{experiencia}/toggle', [ExperienciaController::class, 'toggle'])->name('experiencias.toggle');
    Route::patch('/experiencias/{experiencia}/aprobar', [ExperienciaController::class, 'aprobar'])->name('experiencias.aprobar');
    Route::patch('/experiencias/{experiencia}/rechazar', [ExperienciaController::class, 'rechazar'])->name('experiencias.rechazar');
    Route::delete('/experiencias/imagenes/{imagen}', [ExperienciaController::class, 'destroyImagen'])->name('experiencias.imagenes.destroy');

    // Pindoor Recomienda
    Route::post('/recomendaciones/imagen', [RecomendacionController::class, 'uploadImagen'])->name('recomendaciones.imagen');
    Route::resource('recomendaciones', RecomendacionController::class)
        ->except(['show'])
        ->parameters(['recomendaciones' => 'recomendacion']);
    Route::patch('/recomendaciones/{recomendacion}/toggle', [RecomendacionController::class, 'toggle'])->name('recomendaciones.toggle');
    Route::patch('/recomendaciones/{recomendacion}/publicar', [RecomendacionController::class, 'togglePublicado'])->name('recomendaciones.publicar');
    Route::delete('/recomendaciones/imagenes/{imagen}', [RecomendacionController::class, 'destroyImagen'])->name('recomendaciones.imagenes.destroy');

    // Artistas
    Route::get('/artistas', [\App\Http\Controllers\AdminController::class, 'artistas'])->name('artistas');
    Route::patch('/artistas/{artista}/toggle', [\App\Http\Controllers\AdminController::class, 'toggleArtista'])->name('artistas.toggle');
    Route::delete('/artistas/{artista}', [\App\Http\Controllers\AdminController::class, 'destroyArtista'])->name('artistas.destroy');

    // Operadores turísticos
    Route::get('/operadores', [\App\Http\Controllers\AdminController::class, 'operadores'])->name('operadores');
    Route::patch('/operadores/{operador}/toggle', [\App\Http\Controllers\AdminController::class, 'toggleOperador'])->name('operadores.toggle');
    Route::delete('/operadores/{operador}', [\App\Http\Controllers\AdminController::class, 'destroyOperador'])->name('operadores.destroy');

    // Leads de Publicita
    Route::get('/leads', [AdminController::class, 'leads'])->name('leads');
    Route::patch('/leads/{lead}/toggle', [AdminController::class, 'toggleLead'])->name('leads.toggle');

    // Gestión de Clientes (negocios)
    Route::get('/clientes', [AdminController::class, 'clientes'])->name('clientes');
    Route::get('/puntos/{punto}/activar-cliente', [AdminController::class, 'mostrarActivarCliente'])->name('clientes.activar.form');
    Route::post('/puntos/{punto}/activar-cliente', [AdminController::class, 'activarCliente'])->name('clientes.activar');
    Route::patch('/puntos/{punto}/desactivar-cliente', [AdminController::class, 'desactivarCliente'])->name('clientes.desactivar');
    Route::patch('/puntos/{punto}/aprobar-cliente', [AdminController::class, 'aprobarCliente'])->name('clientes.aprobar');
    Route::patch('/puntos/{punto}/rechazar-cliente', [AdminController::class, 'rechazarCliente'])->name('clientes.rechazar');
    Route::get('/puntos/{punto}/modulos', [AdminController::class, 'editarModulos'])->name('clientes.modulos.form');
    Route::put('/puntos/{punto}/modulos', [AdminController::class, 'actualizarModulos'])->name('clientes.modulos');

    // Configuración general
    Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
    Route::post('/configuracion', [ConfiguracionController::class, 'actualizar'])->name('configuracion.actualizar');
    Route::post('/configuracion/excluidos', [ConfiguracionController::class, 'actualizarExcluidos'])->name('configuracion.excluidos');
    Route::post('/configuracion/ascensores', [ConfiguracionController::class, 'actualizarAscensores'])->name('configuracion.ascensores');
});

/* --- RUTAS CLIENTES (NEGOCIOS) --- */
Route::middleware(['auth', 'verified', 'role:cliente'])->prefix('cliente')->name('cliente.')->group(function () {
    // Dashboard: redirige a perfil
    Route::get('/mis-puntos', fn() => redirect()->route('cliente.perfil'))->name('mis-puntos');

    // Alta propia de negocio
    Route::get('/nuevo',  [ClienteController::class, 'onboarding'])->name('nuevo');
    Route::post('/nuevo', [ClienteController::class, 'crearNegocio'])->name('crear');
    Route::get('/geocodificar', [ClienteController::class, 'geocodificar'])->name('geocodificar');
    Route::get('/geocodificar-inverso', [ClienteController::class, 'geocodificarInverso'])->name('geocodificar-inverso');

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

    // Módulo museo — se gestiona embebido en el dashboard (cliente.perfil.ver#museo)
    Route::post('/museo/{punto}/entradas', [ClienteMuseoController::class, 'guardarEntradas'])->name('museo.entradas.guardar');
    Route::post('/museo/{punto}/exposicion', [ClienteMuseoController::class, 'guardarExposicion'])->name('museo.exposicion.guardar');
    Route::delete('/museo/{punto}/exposicion/{exposicion}', [ClienteMuseoController::class, 'eliminarExposicion'])->name('museo.exposicion.eliminar');

    // Módulo agenda cultural (categoría 5) — embebido en el dashboard (cliente.perfil.ver#eventos)
    Route::post('/eventos/{punto}/guardar', [ClienteEventosController::class, 'guardarEvento'])->name('eventos.guardar');
    Route::delete('/eventos/{punto}/{evento}', [ClienteEventosController::class, 'eliminarEvento'])->name('eventos.eliminar');

    // Catálogo de productos (tiendas / artesanía) — embebido en el dashboard (cliente.perfil.ver#catalogo)
    Route::post('/productos',                       [ClienteProductosController::class, 'store'])->name('productos.store');
    Route::post('/productos/{producto}',            [ClienteProductosController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{producto}',          [ClienteProductosController::class, 'destroy'])->name('productos.destroy');
});

/* --- RUTAS ARTISTAS --- */
Route::middleware(['auth', 'verified', 'role:artista'])->prefix('artista')->name('artista.')->group(function () {
    Route::get('/nuevo',  [ArtistaController::class, 'onboarding'])->name('nuevo');
    Route::post('/nuevo', [ArtistaController::class, 'crearPerfil'])->name('crear');

    Route::get('/perfil',              [ArtistaController::class, 'perfil'])->name('perfil');
    Route::put('/perfil/actualizar',   [ArtistaController::class, 'actualizarPerfil'])->name('perfil.actualizar');

    Route::post('/perfil/imagenes',           [ArtistaController::class, 'subirImagen'])->name('imagen.subir');
    Route::delete('/perfil/imagenes/{imagen}', [ArtistaController::class, 'eliminarImagen'])->name('imagen.eliminar');
});

// Perfil público artista — debe ir DESPUÉS del grupo protegido para que /artista/nuevo no sea capturado como slug
Route::get('/artista/{slug}', [ArtistaController::class, 'show'])->name('artista.show');

/* --- RUTAS OPERADORES TURÍSTICOS --- */
Route::middleware(['auth', 'verified', 'role:operador'])->prefix('operador')->name('operador.')->group(function () {
    Route::get('/nuevo',  [OperadorController::class, 'onboarding'])->name('nuevo');
    Route::post('/nuevo', [OperadorController::class, 'crearPerfil'])->name('crear');

    Route::get('/perfil',              [OperadorController::class, 'perfil'])->name('perfil');
    Route::put('/perfil/actualizar',   [OperadorController::class, 'actualizarPerfil'])->name('perfil.actualizar');
    Route::put('/perfil/lugares',      [OperadorController::class, 'guardarPuntos'])->name('perfil.lugares');
});

// Perfil público operador — debe ir DESPUÉS del grupo protegido, mismo motivo que artista/{slug}
Route::get('/operador/{slug}', [OperadorController::class, 'show'])->name('operador.show');

require __DIR__.'/auth.php';