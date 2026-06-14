# Despliegue i18n — Pindoor

## Cuándo hacer esto
Cuando el branch `feature/i18n` esté listo para producción.

---

## Pasos (en el servidor de producción)

```bash
# 1. Poner sitio en mantenimiento
php artisan down

# 2. Subir archivos por FTP
#    — todo el branch feature/i18n

# 3. Instalar dependencia nueva (si no está)
composer require spatie/laravel-translatable

# 4. Correr la migración (convierte columnas texto → JSON con {"es": valor_original})
php artisan migrate

# 5. Correr el seeder (agrega {"en": "..."} a cada registro)
php artisan db:seed --class=TranslationsSeeder

# 6. Levantar el sitio
php artisan up
```

---

## Problema: panoramas nuevos creados después del dump

El seeder tiene traducciones para los **191 panoramas** que existían cuando se
exportó la base de datos local. Si durante el día se suben panoramas nuevos a
producción, esos registros quedarán sin traducción al inglés (solo `es`).

**No rompe el sitio:** `spatie/laravel-translatable` cae al español si falta `en`.

### Cómo traducir panoramas nuevos después del despliegue

**Paso 1 — Exportar los pendientes en producción:**

```sql
SELECT id,
  JSON_UNQUOTE(JSON_EXTRACT(titulo, '$.es')) AS titulo_es,
  JSON_UNQUOTE(JSON_EXTRACT(ubicacion, '$.es')) AS ubicacion_es
FROM panoramas
WHERE JSON_EXTRACT(titulo, '$.en') IS NULL
ORDER BY id;
```

**Paso 2 — Pegar el resultado en ChatGPT / Claude con el prompt:**

```
Traduce al inglés estos panoramas de Valparaíso (columnas separadas por |):
ID|titulo_es|ubicacion_es

[pegar filas aquí]

Devuelve SQL así:
UPDATE panoramas SET titulo = JSON_SET(titulo, '$.en', 'EN TITLE') WHERE id = N;
UPDATE panoramas SET ubicacion = JSON_SET(ubicacion, '$.en', 'EN LOCATION') WHERE id = N;
```

**Paso 3 — Ejecutar el SQL resultante en producción.**

---

## Mismo proceso para otras tablas

| Tabla | Columna pendiente | Query para detectar sin EN |
|-------|-------------------|---------------------------|
| `panoramas` | `titulo`, `ubicacion` | `JSON_EXTRACT(titulo, '$.en') IS NULL` |
| `puntosinteres` | `description` | `JSON_EXTRACT(description, '$.en') IS NULL` |
| `posts` | `titulo`, `resumen`, `contenido` | `JSON_EXTRACT(titulo, '$.en') IS NULL` |
| `experiencias` | `titulo`, `descripcion` | `JSON_EXTRACT(titulo, '$.en') IS NULL` |

---

## Verificación post-despliegue

```sql
-- Resumen de cobertura por tabla
SELECT 'puntosinteres' AS tabla,
  SUM(JSON_EXTRACT(description, '$.en') IS NOT NULL) AS con_en,
  COUNT(*) AS total
FROM puntosinteres WHERE eliminado = 0 AND activo = 1
UNION ALL
SELECT 'panoramas',
  SUM(JSON_EXTRACT(titulo, '$.en') IS NOT NULL),
  COUNT(*)
FROM panoramas
UNION ALL
SELECT 'categorias',
  SUM(JSON_EXTRACT(nombre, '$.en') IS NOT NULL),
  COUNT(*)
FROM categorias;
```
