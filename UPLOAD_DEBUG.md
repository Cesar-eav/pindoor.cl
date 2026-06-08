# Upload de imágenes: diagnóstico y solución ✅

## Síntoma

Al subir imágenes desde el panel de cliente, cualquier imagen mayor a ~2.1 MB fallaba silenciosamente: el servidor respondía con un redirect (302) pero la imagen no se guardaba. El log de Laravel mostraba:

```
[SUBIR] FILES_raw={"imagenes":{"name":["foto.jpg"],"type":[""],"tmp_name":[""],"error":[1],"size":[0]}}
[SUBIR] upload_max=2M post_max=8M
```

`error: 1` = `UPLOAD_ERR_INI_SIZE` — PHP rechazó el archivo antes de que llegara al controlador.

---

## Causa raíz (local)

**El entorno de desarrollo corre con `php artisan serve`**, que internamente lanza el servidor built-in de PHP (`php -S 127.0.0.1:8000`). Este proceso usa el **php.ini del CLI**, completamente separado del php.ini de Apache.

| SAPI | php.ini | upload_max_filesize |
|------|---------|---------------------|
| Apache (`:80`) | `/etc/php/8.3/apache2/php.ini` | 10M (corregido antes) |
| artisan serve (`:8000`) | `/etc/php/8.3/cli/php.ini` | **2M** ← el problema |

El diagnóstico se confirmó con la ruta `/debug-php`:
```json
{"sapi":"cli-server","ini":"/etc/php/8.3/cli/php.ini","upload":"2M","post":"8M"}
```

---

## Por qué costó encontrarlo

- El archivo `phpcheck.php` fue probado vía Apache (`:80`) y mostraba 10M ✓ — daba falsa sensación de que todo estaba bien
- Los uploads reales van por artisan serve (`:8000`) → usaba CLI php.ini → 2M ✗
- `upload_max_filesize` es directiva `PHP_INI_SYSTEM`: **no se puede cambiar** via `.user.ini`, `php_value` en `.htaccess`, ni `ini_set()` en código. Solo el php.ini o `php_admin_value` en httpd.conf
- Editar el php.ini de Apache no tiene ningún efecto sobre artisan serve

---

## Solución aplicada

```bash
# 1. Editar CLI php.ini
sudo sed -i 's/^upload_max_filesize = 2M/upload_max_filesize = 10M/' /etc/php/8.3/cli/php.ini
sudo sed -i 's/^post_max_size = 8M/post_max_size = 15M/' /etc/php/8.3/cli/php.ini

# 2. Matar el proceso de artisan serve (ver PID con: ss -tlnp | grep 8000)
kill <PID>

# 3. Relanzar
cd /var/www/html/pindoor && php artisan serve
```

Verificar con `http://127.0.0.1:8000/debug-php` → debe mostrar `upload: 10M`.

---

## Estado: ✅ RESUELTO en local

- `/etc/php/8.3/cli/php.ini` → `upload_max_filesize = 10M`, `post_max_size = 15M`
- Logs de debug eliminados de `ClienteController.php`
- UI actualizada a "máx. 10 MB c/u"

---

## Producción (pindoor.cl, cPanel + LiteSpeed)

El servidor de producción muestra límites correctos:
```json
{"sapi":"litespeed","ini":"/opt/cpanel/ea-php82/root/etc/php.ini","upload":"10M","post":"12M","memory":"2G"}
```

El problema en producción **no es el tamaño** — es que **Intervention Image no está instalada** en el vendor del servidor compartido. Pendiente de resolver.
