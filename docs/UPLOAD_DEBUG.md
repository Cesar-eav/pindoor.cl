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

El servidor de producción corre PHP nativo (no un ea-phpXX seleccionable), así que
**"Seleccionar Versión PHP" → PHP Options no está disponible** — cPanel avisa que hay
que cambiar a una versión no-nativa para desbloquear esa pantalla. Tampoco hay
`.user.ini` ni `php.ini` propios en `public_html` ni en el home del usuario.

**El límite real se controla desde `public/.htaccess`**, con directivas `php_value`:
```apache
php_value upload_max_filesize 20M
php_value post_max_size 25M
```

Bajo LiteSpeed (a diferencia de Apache + mod_php en el entorno local), `php_value` en
`.htaccess` **sí funciona** para directivas `PHP_INI_PERDIR` como estas — contradice lo
que se creía antes (ver nota más abajo).

### Cómo diagnosticar los límites reales de la web (no de la terminal)

La terminal (`php -f archivo.php` por SSH) usa el PHP **CLI** de la cuenta, que en
hosting compartido puede tener un `php.ini` totalmente distinto (más permisivo) al que
usa LiteSpeed para servir la web. Para ver lo que realmente aplica a los uploads del
sitio, hay que pegarle por HTTP:
```bash
curl -s https://pindoor.cl/limits.php
# upload: 20M | post: 25M | mem: 2G
```
`public/limits.php` es un script de debug que imprime `ini_get('upload_max_filesize')`,
`post_max_size` y `memory_limit`.

### Corrección: `.user.ini`/`.htaccess` sí funcionan en producción

La sección "Por qué costó encontrarlo" (más arriba) dice que `upload_max_filesize` es
`PHP_INI_SYSTEM` y que ni `.user.ini` ni `php_value` en `.htaccess` sirven. Eso es cierto
para **Apache + mod_php** (el entorno local), pero **no** para LiteSpeed en el hosting de
producción, donde PHP corre vía LSAPI (como PHP-FPM/CGI) y sí respeta `.htaccess` con
`php_value` para directivas `PHP_INI_PERDIR` — que es justo el caso real de
`upload_max_filesize` y `post_max_size` según el manual de PHP.

### Estado: ✅ RESUELTO en producción (2026-07-28)

- `public/.htaccess` → `upload_max_filesize = 20M`, `post_max_size = 25M`
- Aplicado tanto en el repo (deploy por FTP) como directo en `~/public_html/.htaccess` vía SSH con `sed`
- Verificado con `curl -s https://pindoor.cl/limits.php`

El problema de **Intervention Image no instalada** en el vendor del servidor compartido
sigue pendiente por separado — no está relacionado con el tamaño de subida.
