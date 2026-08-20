# Sitemap y Google Search Console

## Qué se actualizó (2026-08-18)

El sitemap (`/sitemap.xml`) solo incluía `PuntoInteres` y `Categoria`. Blog,
Rutas y Pindoor Recomienda tenían páginas públicas indexables pero **nunca
estuvieron en el sitemap** — Google podía encontrarlas por rastreo de enlaces
internos (desde la home, tarjetas, etc.), pero sin la señal explícita del
sitemap la indexación es más lenta y menos completa.

Se agregaron al sitemap:

| Sección | Modelo | Scope usado | Ruta pública |
|---|---|---|---|
| Blog | `Post` | `publicados()` | `blog.show` |
| Rutas Pindoor | `Ruta` | `publicadas()` | `rutas.show` |
| Pindoor Recomienda | `Recomendacion` | `publicadas()` | `recomienda.show` |

Cada entrada usa `publicado_en` como `<lastmod>` cuando existe.
`changefreq: monthly`, `priority: 0.6` — mismo criterio que ya se usaba para
`/panoramas`, por debajo de las fichas de `PuntoInteres` (0.8) y la home (1.0).

### Archivos tocados

- `app/Http/Controllers/SitemapController.php` — agrega las 3 queries.
- `resources/views/sitemap.blade.php` — agrega los 3 `@foreach`.

Verificado localmente: XML válido (`DOMDocument::loadXML`), 150 URLs totales
tras el cambio, incluyendo ejemplos reales de `/blog/`, `/rutas/` y
`/recomienda/`.

---

## Qué falta hacer en producción

1. **Deploy manual por FTP** — este repo no está conectado a producción
   (`pindoor.cl`, cPanel `int15`). Subir los 2 archivos de arriba.
2. **Reenviar el sitemap en Search Console** — una vez desplegado:
   - Search Console → Sitemaps → confirmar que `sitemap.xml` sigue
     apuntando bien (no cambia la URL, solo el contenido).
   - No hace falta "reenviar" explícitamente para que Google lo vuelva a
     leer — lo rastrea periódicamente solo — pero forzar una relectura
     ahí mismo acelera que tome las URLs nuevas.
3. **Opcional: Inspección de URL** — para las notas de Recomendaciones que
   quieras indexar ya (no esperar el rastreo natural), usar en Search
   Console "Inspección de URLs" → pegar la URL → "Solicitar indexación".
   Con 3-10 páginas nuevas puntuales, esto es más rápido que esperar al
   sitemap.

## Qué NO cambia con esto

- Las recomendaciones en borrador (`publicado = false`) siguen sin
  aparecer — el sitemap usa el mismo scope `publicadas()` que ya filtra
  eso en el resto del sitio.
- El endpoint de vista previa admin (`/admin/recomendaciones/{id}/preview`)
  **no** se agrega al sitemap — es intencional, esa página no debe
  indexarse (no tiene `noindex` explícito todavía; ver sección siguiente
  si se quiere reforzar).

## Pendiente / mejora futura (no implementado)

La vista previa de Recomendaciones ahora es pública (sin login) para poder
compartirla con el cliente antes de publicar. No tiene meta `noindex`, así
que en teoría un bot podría indexarla si encuentra el link por fuera del
sitemap. Es un riesgo bajo (no está enlazada desde ningún lugar público),
pero si se quiere cerrar del todo: agregar
`<meta name="robots" content="noindex">` a la respuesta de
`RecomendacionController::preview()`.
