# Problema: Upload de imágenes falla en local con UPLOAD_ERR_INI_SIZE

## Síntoma

Al subir imágenes desde el panel de cliente (galería del negocio), cualquier imagen mayor a ~2.2 MB genera un error 500. El log de Laravel muestra:

```
[SUBIR] content_length=2620118
[SUBIR] FILES_raw={"imagenes":{"name":["imagen.jpg"],"type":[""],"tmp_name":[""],"error":[1],"size":[0]}}
[SUBIR] upload_max=2M post_max=8M
```

`error: 1` = `UPLOAD_ERR_INI_SIZE` — PHP rechazó el archivo antes de que llegue al controlador.

## Causa raíz identificada

El entorno local corre con **`php artisan serve`** (puerto 8000), que usa el servidor built-in de PHP. Este proceso usa el **php.ini del CLI**, no el de Apache.

| php.ini | Ruta | upload_max_filesize | post_max_size |
|---------|------|---------------------|---------------|
| Apache  | `/etc/php/8.3/apache2/php.ini` | **10M** (ya corregido) | **15M** (ya corregido) |
| CLI     | `/etc/php/8.3/cli/php.ini`     | **2M** (default — SIN corregir) | **8M** (default — SIN corregir) |

Procesos activos relevantes:
```
php artisan serve           → php8.3 -S 127.0.0.1:8000 ...  ← este sirve la app
php8.3 (Apache mod_php)     → sirve /var/www/html via :80
```

## Por qué nos confundió

- `public/phpcheck.php` fue probado vía **Apache** (`:80`) → mostraba 10M ✓
- Los uploads van por **artisan serve** (`:8000`) → usa CLI php.ini → 2M ✗
- `public/.htaccess` y `public/.user.ini` con `upload_max_filesize = 10M` **no aplican** al servidor built-in de PHP ni a `upload_max_filesize` (directiva `PHP_INI_SYSTEM`, no puede cambiarse vía `.user.ini`)
- El php.ini de Apache fue editado correctamente pero no afecta artisan serve

## Lo que se intentó (sin éxito)

1. Editar `/etc/php/8.2/apache2/php.ini` → incorrecto (Apache usa PHP 8.3)
2. Editar `/etc/php/8.3/apache2/php.ini` → correcto, pero no es el que usa artisan serve
3. Agregar `php_value` en `public/.htaccess` → ignorado (AllowOverride None + directiva SYSTEM)
4. Crear `public/.user.ini` con `upload_max_filesize = 10M` → ignorado (directiva SYSTEM)
5. `sudo systemctl restart apache2` → correcto para Apache, irrelevante para artisan serve

## Solución pendiente

Editar `/etc/php/8.3/cli/php.ini` (requiere `sudo`):

```
# línea 865
upload_max_filesize = 10M

# línea 713
post_max_size = 15M
```

```bash
sudo nano /etc/php/8.3/cli/php.ini
# o
sudo mousepad /etc/php/8.3/cli/php.ini
```

Después reiniciar artisan serve (Ctrl+C y `php artisan serve` de nuevo).

### Verificación

Crear `public/phpcheck2.php` **y probarlo en el puerto 8000** (no en Apache):

```php
<?php
header('Content-Type: text/plain');
echo 'upload_max: ' . ini_get('upload_max_filesize') . "\n";
echo 'post_max: ' . ini_get('post_max_size') . "\n";
echo 'SAPI: ' . php_sapi_name() . "\n";
```

Acceder a `http://127.0.0.1:8000/phpcheck2.php` — debe mostrar `cli-server` como SAPI y 10M.

## Estado del código

El controlador `ClienteController.php` tiene logs de debug activos que deben eliminarse una vez resuelto el problema:

```php
// subirImagen() — líneas ~277-279
\Log::error('[SUBIR] content_length=...');
\Log::error('[SUBIR] FILES_raw=...');
\Log::error('[SUBIR] upload_max=...');

// guardarImagenComprimida() — múltiples \Log::error("[IMG] ...")
```
