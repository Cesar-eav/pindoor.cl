# CLAUDE.md — Pindoor

Guía turística digital de Valparaíso, Chile. App web pública + panel de gestión para negocios locales.

## Stack

- **Laravel 10.50** / **PHP 8.3** / MySQL
- **Blade** + **Alpine.js** + **Tailwind CSS v4** (sin `tailwind.config.js` — clases JIT directas)
- **Vite** para assets (`npm run dev` / `npm run build`)
- Apache en producción — URL pública: `https://pindoor.cl`
- Imágenes en `storage/` (disk `local`, symlink público)
- Mail: Mailgun. Queue: sync. Cache: file.
- Auth: Laravel Breeze + Google OAuth (Socialite)

## Roles de usuario (`users.type`)

| Valor | Acceso |
|-------|--------|
| `admin` | Panel `/admin/*` — gestión global |
| `cliente` | Panel `/cliente/*` — gestiona su propio negocio |
| `artista` | Panel `/artista/*` — perfil artístico |

Middleware de rol personalizado: `role:admin`, `role:cliente`, `role:artista`.

## Modelo central: `PuntoInteres` (`puntosinteres`)

Tabla principal. Representa cualquier lugar: atractivo turístico o negocio cliente.

### Campos clave

```
id, user_id, title, slug, category (string legacy), categoria_id (FK),
sector, direccion, lat, lng, ciudad (default Valparaíso),
description, tags (json), video_url, enlace, horario, autor,
modulos_habilitados (json array), carta (json), alojamiento (json),
activo, eliminado (soft delete lógico)
```

### Categorías y sus módulos

| ID | Nombre | Módulos por defecto |
|----|--------|---------------------|
| 1 | Miradores | — |
| 2 | Cafeterías | oferta_del_dia, menu_del_dia, carta |
| 3 | Street Art | — |
| 4 | Monumentos | — |
| 5 | Cultura | oferta_del_dia, agenda |
| 6 | Naturaleza | — |
| 7 | Museos | oferta_del_dia, entradas, exposiciones |
| 8 | Picadas | oferta_del_dia, menu_del_dia, carta |
| 9 | Arquitectura | — |
| 10 | Comer | oferta_del_dia, menu_del_dia, carta |
| 11 | Alojar | oferta_del_dia, habitaciones, servicios, politicas |
| 12 | Estatuas | — |
| 13 | Tiendas | cliente — catálogo productos |
| 14 | Artesanía | cliente — catálogo productos |
| 15 | Ascensores | — |

### Helpers del modelo

```php
$punto->moduloActivo('exposiciones')   // bool
$punto->esAlojamiento()                // cat 11
$punto->esGastronomia()                // cat 2,8,10
$punto->esMuseo()                      // cat 7
$punto->esCultura()                    // cat 5
$punto->esEstatua()                    // cat 12
$punto->dato('carta')                  // ModuloDato->datos json
$punto->tienePromocion()
$punto->tieneMenu()
$punto->tieneCarta()
```

## Modelos secundarios

| Modelo | Tabla | Uso |
|--------|-------|-----|
| `ModuloDato` | `modulo_datos` | Datos JSON por módulo (carta, alojamiento, entradas…) |
| `ModuloItem` | `modulo_items` | Ítems de lista (exposiciones, eventos, habitaciones) |
| `ImagenPunto` | `imagenes_punto` | Galería del lugar |
| `PuntoProducto` | `punto_productos` | Catálogo tiendas/artesanía |
| `Panorama` | `panoramas` | Eventos/actividades con fecha (La Brújula) |
| `Post` | `posts` | Blog editorial |
| `Experiencia` | `experiencias` | Experiencias propuestas por comunidad |
| `Artista` | `artistas` | Directorio de artistas locales |
| `Categoria` | `categorias` | Categorías de atractivos |
| `Configuracion` | `configuraciones` | Pares clave/valor globales |
| `LeadPublicita` | `leads_publicita` | Leads del formulario de registro negocios |
| `LeadContacto` | `leads_contacto` | Mensajes del formulario de contacto |

## Estructura de vistas

```
layouts/pindoor.blade.php   — Layout principal (navbar, FAB móvil, Alpine.js, footer)
puntos/
  show.blade.php             — Ficha pública del lugar
  exposicion.blade.php       — Detalle de una exposición (con lightbox Alpine.js)
  partials/
    _listado_mobile.blade.php  — Vista home móvil (panoramas, blog, grid atractivos)
    _card_mobile.blade.php     — Tarjeta atractivo en grid
cliente/
  perfil-editar.blade.php    — Formulario edición negocio (cliente)
admin/
  partials/_sector-select.blade.php  — Select reutilizable de sectores de Valparaíso
```

## Sectores disponibles

Cerros de Valparaíso (Alegre, Concepción, Bellavista, Artillería, etc.) + sectores del Plan.
El partial `_sector-select.blade.php` se incluye con:
```blade
@include('admin.partials._sector-select', ['selected' => old('sector', $punto->sector)])
```

## FAB móvil (layout principal)

En `layouts/pindoor.blade.php`. Botón flotante `+` que despliega 3 tarjetas horizontales:
- GPS / Cerca de ti
- Experiencias (rojo `#fc5648`)
- Contacto

JS: `toggleFab()` y `closeFab()` — agregan/quitan clase `flex` dinámicamente (nunca en HTML estático para evitar conflicto con `hidden`).

## Convenciones de estilo

- **Tailwind v4** — sin archivo de config. Usar clases canónicas (`z-999`, `min-w-19`, no `z-[999]`, no `min-w-[76px]`).
- Color principal: `#fc5648` (rojo Pindoor). Fondo suave: `#fff0ef`.
- Cards con `rounded-2xl`, `shadow-sm`, `border border-gray-100`.
- Tipografía: `font-extrabold` para títulos, `text-[11px]` para etiquetas pequeñas.
- `whitespace-pre-line` para descripciones con saltos de línea.
- Alpine.js para interactividad ligera (lightbox, toggles, FAB).
- No usar comentarios en código salvo que el WHY sea no obvio.

## Rutas clave

```
GET  /                          puntos.index       — Home (móvil: _listado_mobile)
GET  /lugar/{slug}              puntos.show        — Ficha pública del lugar
GET  /lugar/{slug}/exposicion/{item}  puntos.exposicion — Detalle exposición museo
GET  /lugar/{slug}/producto/{producto} puntos.producto — Detalle producto tienda
GET  /explorar                  puntos.explorar    — Listado paginado completo
GET  /panoramas                 atractivos.panoramas — La Brújula (agenda)
GET  /experiencias              experiencias.index
GET  /blog                      blog.index
GET  /cliente/perfil/{punto}/editar  cliente.perfil.editar
GET  /admin/stats               admin.stats
```

## Controladores principales

| Controlador | Responsabilidad |
|-------------|-----------------|
| `PuntoInteresController` | Vistas públicas (index, show, exposición, producto, panoramas, experiencias) |
| `ClienteController` | Panel cliente: perfil, galería, módulos rápidos |
| `ClienteMuseoController` | Gestión exposiciones y entradas |
| `ClienteEventosController` | Gestión agenda cultural |
| `ClienteProductosController` | Catálogo productos |
| `AdminController` | Panel admin: stats, usuarios, clientes, leads |
| `BlogController` | Blog público |

## Variables que el HomeController pasa a `_listado_mobile`

- `$atractivos` — LengthAwarePaginator de PuntoInteres
- `$proximosPanoramas` — Colección de Panoramas futuros (se oculta si hay filtro activo)
- `$ultimoPost` — último Post publicado (se oculta si hay filtro activo)
- `$panoramas` — Panoramas relacionados a búsqueda activa
- `$hayFiltros` — bool (lat, category o search activos)
- `$categorias` — todas las categorías activas agrupadas

## NativePHP Mobile

- App ID: `cl.pindoor.app`
- Proyecto Android en `nativephp/android/`
- Keystore en `/home/cesar/pindoor.keystore` (credenciales en `nativephp/android/gradle.properties`)
- Splash screen: `nativephp/android/app/src/main/res/drawable/splash.xml` (fondo rojo `#fc5648`)

### Compilar APK

```bash
cd /var/www/html/pindoor/nativephp/android && JAVA_HOME=/usr/lib/jvm/java-17-openjdk-amd64 ./gradlew clean assembleRelease
```

El APK firmado queda en:
```
nativephp/android/app/build/outputs/apk/release/app-release.apk
```

> **Importante:** Usar siempre `clean` antes de `assembleRelease`. Sin él el APK puede no funcionar correctamente.
