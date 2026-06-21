# Estrategia de mejora de búsqueda — Pindoor

## Diagnóstico del problema

La búsqueda actual usa `LIKE '%término%'` sobre 4 campos:

```php
->where('title', 'like', "%{$search}%")
->orWhere('description', 'like', "%{$search}%")          // ← raíz del problema
->orWhere('descripcion_busqueda', 'like', "%{$search}%")
->orWhere('tags', 'like', "%{$search}%");
```

El campo `description` es texto editorial libre (escrito para el visitante), no para búsqueda. Por eso:

- `"completo"` devuelve lugares donde la descripción dice *"menú completo"*, *"información completa"*, *"horario completo"*, etc.
- `"café"` devuelve lugares donde la descripción dice *"ambiente de café"*, *"color café"*, *"frente al café"*, etc.

El mismo bug existe en **3 lugares** del código:
- `PuntoInteresController::index()` — línea 39
- `PuntoInteresController::explorar()` — línea 192
- `AtractivosGrid` (Livewire) — línea 57

---

## Campos disponibles para búsqueda

| Campo | Naturaleza | Apto para búsqueda |
|-------|-----------|-------------------|
| `title` | Nombre oficial del lugar | ✅ sí |
| `description` | Texto descriptivo largo (editorial) | ❌ no — demasiado ruido |
| `descripcion_busqueda` | Campo curado de palabras clave | ✅ sí — pero poco poblado |
| `tags` | JSON array de etiquetas | ✅ sí |
| `sector` | Barrio / cerro | ✅ sí |
| `categoria.nombre` | Nombre de categoría (JOIN) | ✅ útil para "café", "museo", etc. |

---

## Plan de mejora — 3 pasos

### Paso 1 — Rápido: eliminar `description` de la búsqueda

**Cambio mínimo, máximo impacto.**

```php
$q->where('title', 'like', "%{$search}%")
  ->orWhere('descripcion_busqueda', 'like', "%{$search}%")
  ->orWhere('tags', 'like', "%{$search}%");
  // description ELIMINADO
```

Riesgo: algunos lugares que solo tenían resultados por `description` dejan de aparecer.
Mitigación: esos lugares deberían tener `descripcion_busqueda` bien rellenado.

Aplicar en los 3 puntos del código.

---

### Paso 2 — Medio: añadir búsqueda por categoría y sector

Cuando el usuario escribe "café", "museo" o "Bellavista", debería encontrar resultados aunque el nombre del lugar no lo diga.

```php
->orWhereHas('categoria', fn($q) => $q->where('nombre', 'like', "%{$search}%"))
->orWhere('sector', 'like', "%{$search}%")
```

Adicionalmente, poblar `descripcion_busqueda` para los lugares sin dueño cliente (miradores, street art, monumentos) desde el panel admin. Ese campo es el que debe absorber sinónimos y palabras clave.

---

### Paso 3 — Completo: relevancia con FULLTEXT MySQL

Para cuando el volumen de lugares crezca y el orden de resultados importe.

**Migration:**
```php
$table->fullText(['title', 'descripcion_busqueda', 'sector']);
```

**Query con score:**
```php
->selectRaw("*, MATCH(title, descripcion_busqueda, sector) AGAINST(? IN BOOLEAN MODE) as score", [$search])
->whereRaw("MATCH(title, descripcion_busqueda, sector) AGAINST(? IN BOOLEAN MODE)", [$search])
->orderByDesc('score')
```

Ventajas frente a LIKE:
- Ignora palabras muy frecuentes (stopwords)
- Ordena por relevancia real
- Soporta búsqueda booleana (`+café -museo`)

Limitación: FULLTEXT no hace prefijo con `%`. Para términos cortos (<3 chars) hay que combinar con LIKE como fallback.

---

## Orden de implementación recomendado

1. **Hoy** — Paso 1: quitar `description` de los 3 lugares. Soluciona el 90% del problema.
2. **Esta semana** — Paso 2: añadir categoría y sector. Mejora "café" → cafeterías.
3. **Cuando haya +200 lugares activos** — Paso 3: FULLTEXT para relevancia ordenada.

---

## Estado de `descripcion_busqueda`

- Editable por el cliente (business owner) desde su panel.
- Los lugares sin cliente asignado lo tienen vacío → admin debería rellenarlo.
- Concepto: debe contener sinónimos, platos, características clave. Ej. para un mirador: *"vista panorámica cerro valparaíso atardecer fotografía"*.
