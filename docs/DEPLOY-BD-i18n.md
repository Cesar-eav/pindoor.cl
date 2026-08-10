# Estrategia de actualización de BD en producción — i18n

## Estado actual

| Lugar | Panoramas | Columnas | Traducciones EN |
|-------|-----------|----------|-----------------|
| Local | IDs 1–206 | JSON `{"es":"...","en":"..."}` | ✅ hasta ID 205 |
| Producción | IDs 1–295 | texto plano | ❌ ninguna |

---

## El problema

La migración (`convert_columns_to_translatable`) convierte las columnas `titulo` y `ubicacion`
de texto plano a JSON con solo la clave `es`:

```sql
-- Resultado tras la migración
{"es": "Festival de Jazz del Puerto"}
```

El `TranslationsSeeder` luego agrega la clave `en` para los IDs 1–205.
Pero los panoramas 213–295 (creados solo en producción) quedan **sin traducción EN**.

---

## Herramientas disponibles

### `TranslationsSeeder`
- Cubre panoramas IDs 1–205 + categorías + otras tablas
- Usa `JSON_SET(titulo, '$.en', ?)` — **requiere que la migración ya haya corrido**
- Es idempotente: si un ID no existe, simplemente no afecta filas

### Migración `2026_06_14_000001_convert_columns_to_translatable`
- Convierte columnas a JSON en: `puntosinteres`, `panoramas`, `posts`, `experiencias`, `categorias`
- Solo puede correr una vez (Laravel la marca como ejecutada)
- Es **irreversible** en producción — hacer backup antes

---

## Plan paso a paso

### Paso 1 — Exportar panoramas 213–295 desde producción

En phpMyAdmin o con SSH:

```sql
SELECT id, titulo, ubicacion FROM panoramas WHERE id >= 213 ORDER BY id;
```

Copiar el resultado para usarlo en el siguiente paso.

### Paso 2 — Crear `PanoramasNewTranslationsSeeder`

Con los títulos y ubicaciones extraídos, crear un nuevo seeder que traduzca al inglés:

```php
// database/seeders/PanoramasNewTranslationsSeeder.php
DB::update("UPDATE panoramas SET titulo = JSON_SET(titulo, '$.en', ?) WHERE id = ?", ["Jazz Festival of the Port", 213]);
DB::update("UPDATE panoramas SET titulo = JSON_SET(titulo, '$.en', ?) WHERE id = ?", ["...", 214]);
// ... hasta ID 295
```

> Este seeder también usa `JSON_SET` — **solo funciona después de la migración**.

### Paso 3 — Subir archivos a producción por FTP

Ver listado completo en `DEPLOY-i18n.md`.

### Paso 4 — Ejecutar en producción (en este orden)

```bash
# 1. Backup obligatorio
mysqldump -u usuario -p nombre_bd > backup_antes_i18n.sql

# 2. Correr la migración (convierte columnas a JSON)
php artisan migrate

# 3. Agregar traducciones EN para IDs 1–205
php artisan db:seed --class=TranslationsSeeder

# 4. Agregar traducciones EN para IDs 213–295
php artisan db:seed --class=PanoramasNewTranslationsSeeder

# 5. Limpiar caché
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

---

## ¿Qué pasa con los panoramas sin traducción EN?

El modelo usa `spatie/laravel-translatable`. Si la clave `en` no existe,
devuelve el valor `es` como fallback automático. Es decir:
- Un usuario en inglés verá el título en español hasta que se traduzca
- No hay error ni campo vacío

---

## Estado

- [x] Exportar panoramas 207–295 desde producción
- [x] Crear `PanoramasNewTranslationsSeeder` con las traducciones EN (IDs 207–295)
- [ ] Subir archivos por FTP (ver `DEPLOY-i18n.md` + agregar `PanoramasNewTranslationsSeeder.php`)
- [ ] Ejecutar los comandos del Paso 4 en producción
