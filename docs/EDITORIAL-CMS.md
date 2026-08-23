# Editorial — el "CMS" interno de Pindoor

## ¿Cómo se llama esto?

El término genérico de la industria es **CMS** (*Content Management System*). Pindoor
no tiene un CMS único: tiene **cuatro implementaciones independientes del mismo
patrón**, escritas por separado para Revival, Blog/Guías, Recomienda y (a medias)
Rutas. Cada una repite ~80% del mismo código: traducciones, portada, editor Quill,
galería con imágenes intercaladas en el texto, y un carrusel con zoom al final.

**Nombre propuesto para ese patrón compartido: `Editorial`.**

Una palabra, sin ambigüedad con nada que ya exista en el código (no hay ningún
`Editorial*` en `app/`), y describe bien lo que es: el motor de creación de
entradas de contenido editorial (reseñas, guías, recomendaciones, re-vivals).
Si en algún momento se extrae a algo reusable, `app/Editorial/`,
`HasEditorialContent` (trait) o `editorial-grid.blade.php` (partial) serían
nombres naturales.

## El patrón "Editorial" — qué comparten las 4 entradas

Presente (con variaciones) en **Revival, Blog/Post y Recomienda**; Rutas solo
tiene una parte:

| Pieza | Qué hace |
|---|---|
| `Spatie\Translatable\HasTranslations` | Título + contenido (+ resumen en Blog/Recomienda) en ES/EN/FR, con fallback a ES |
| `imagen_portada` | Imagen de portada única, comprimida con `ImagenComprimida::guardar()` |
| Slug | Helper estático `generarSlug()` en el modelo, único por tabla |
| `publicado` / `publicado_en` | Toggle borrador/publicado; `publicado_en` se fija al primer publish |
| Editor Quill | Div `#{modulo}-editor` + partial `_editor-scripts.blade.php` propio por módulo |
| `uploadImagen()` | Endpoint AJAX que sube una imagen suelta y la devuelve para insertarla inline en Quill |
| Imágenes intercaladas | Cada imagen de la galería puede llevar un nº de párrafo (`posicion`) — el contenido HTML se corta en bloques y la imagen se inserta como `<figure>` después del párrafo indicado; sin posición → reparto automático |
| Galería + zoom | Al final del artículo público: carrusel Alpine.js con todas las imágenes + lightbox de pantalla completa |
| `partials/_share_panel.blade.php` | Botón de compartir (WhatsApp / nativo / copiar enlace) reusado tal cual |

## Comparación de las 4 implementaciones

| | **Revival** | **Blog / Guías** | **Recomienda** | **Rutas** |
|---|---|---|---|---|
| Traducible | título, contenido | título, resumen, contenido | título, resumen, contenido | título, descripción |
| Editor Quill | ✅ `#revival-editor` | ✅ `#blog-editor` | ✅ `#recomendacion-editor` | ❌ ninguno — `<textarea>` plano |
| Galería | JSON `imagenes` (array en la tabla) | JSON `imagenes` (array en la tabla) | Tabla real `recomendacion_imagenes` (relación `hasMany`) | ❌ no existe |
| Imágenes intercaladas | `<figure class="blog-fig">` | `<figure class="blog-fig">` | `<figure class="resena-fig">` | ❌ n/a |
| **Reordenar galería** | ✅ **drag-and-drop** (único) | ❌ solo nº de posición, nuevas siempre al final | ❌ solo nº de posición | ❌ n/a |
| Extra propio | Video de YouTube embebido | Relaciones a `lugares` y `rutas` (pivots con `orden`) | Campo `plan`, negocio/rubro/whatsapp/enlace, video local | Auto-traducir ES→EN/FR vía API MyMemory; relación a `puntos`, `operadores`, `guias` |

## Inconsistencias que vale la pena mirar

1. **Solo Revival tiene reordenamiento por arrastre.** Blog y Recomienda dependen
   de que el admin calcule a mano en qué posición quedará cada foto; las nuevas
   siempre se agregan al final del array sin forma de moverlas antes.
2. **Dos modelos de datos distintos para "galería"**: Revival/Blog usan un array
   JSON de tamaño fijo (20 slots numerados en el form: `imagen_nueva_1`…`imagen_nueva_20`);
   Recomienda usa una tabla relacional real (`recomendacion_imagenes`). La tabla
   relacional escala mejor (sin límite artificial de 20, permite borrar una imagen
   sin recalcular índices) — sería la base más sana si se unifica.
3. **Mismo concepto, dos nombres de clase CSS** (`blog-fig` vs `resena-fig`) para
   exactamente la misma figura intercalada — puramente accidental, no aporta nada.
4. **Rutas es el caso especial**: no tiene editor enriquecido ni galería, solo
   `<textarea>` traducibles + auto-traducción por API. Puede ser intencional
   (una ruta es más corta que una reseña) o puede ser el próximo candidato a
   subir de nivel si algún día necesita fotos por parada.

## Si se decide unificar

No es una tarea urgente ni se hizo en esta sesión — queda como referencia para
cuando se repita el patrón una quinta vez y valga la pena extraerlo:

- **Trait de modelo** `HasEditorialContent`: centraliza `HasTranslations` +
  `generarSlug()` + el helper de publicar/despublicar. Los 3-4 modelos dejan de
  reimplementar lo mismo con nombres de método idénticos.
- **Partial de admin** para la grilla de galería con drag-and-drop (hoy solo
  vive en `admin/revival/_form.blade.php`) — llevarlo a Blog y Recomienda es
  la mejora de UX más directa y ya probada en producción.
- **Partial público** para "contenido con imágenes intercaladas + galería +
  zoom" (hoy triplicado casi línea por línea en `revival/show.blade.php`,
  `blog/show.blade.php` y `recomienda/show.blade.php`).
- Migrar la galería de Revival/Blog de array-JSON-de-20-slots a una tabla
  relacional como la de Recomienda, si en algún momento el límite de 20 fotos
  o la falta de borrado individual se vuelve un problema real.

## En una frase

Pindoor no tiene "un CMS" — tiene el mismo mini-CMS reescrito tres veces y
medio. `Editorial` es el nombre para ese patrón compartido si alguna vez se
decide extraerlo en vez de seguir copiándolo.
