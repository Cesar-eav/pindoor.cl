# Migración de FTP a git en producción — Pindoor

**Estado: completada el 26 sep 2026.** Producción (`cpi116106@int15`, app en `/home2/cpi116106/pindoor`) corre desde un `.git` real en la rama `main`, sincronizada con `origin/main` (commit `9a5315a` al cierre de esta migración). El deploy pasa a ser `git pull` + los pasos de la sección "De ahí en adelante" al final de este documento. Esta guía queda como registro de cómo se hizo, para referencia futura.

Producción se desplegaba siempre por FTP y no tenía `.git`. Esta guía es el paso a paso real que se usó para convertirla a un deploy basado en `git pull`, verificando antes (por checksum) que el código desplegado coincidiera con `main`.

## Estado del hosting (cPanel, cuenta `cpi116106@int15`)

- El home de la cuenta es `/home2/cpi116106`. Existe también un alias/symlink heredado `/home4/cpi116106` que resuelve al mismo contenido (residuo de una migración de cuenta entre particiones "home" de cPanel) — los symlinks viejos de `public_html` que apuntan a rutas `/home4/...` siguen funcionando por esto, no están rotos.
- `public_html` (`/home2/cpi116106/public_html`) es un directorio real, **no** el `public/` de Laravel. Contiene un `index.php` customizado a mano que bootstrapea la app desde `../pindoor/`:
  ```php
  require __DIR__.'/../pindoor/vendor/autoload.php';
  $app = require_once __DIR__.'/../pindoor/bootstrap/app.php';
  ```
  y dos symlinks: `public_html/build -> .../pindoor/public/build` y `public_html/storage -> .../pindoor/storage/app/public`. Ninguno de los tres (`index.php`, `build`, `storage`) es parte del repo git — viven fuera de `~/pindoor` y `checkout -f` no los toca.
- PHP CLI y el sitio corren ambos en **8.3.28** (a diferencia de otros proyectos en este mismo hosting, acá no hubo que tocar MultiPHP Manager).
- No hay `composer` ni `npm` en el PATH del jailshell. Composer se corre con el `composer.phar` que ya estaba en la raíz del proyecto (`php composer.phar install ...`). Node/npm no están expuestos globalmente, pero **sí funcionan invocados con ruta completa** sin pasar por "Setup Node.js App" de cPanel:
  ```bash
  /opt/alt/alt-nodejs20/root/usr/bin/node --version   # v20.19.4
  /opt/alt/alt-nodejs20/root/usr/bin/npm --version    # 10.8.2
  ```
- `public/storage` (symlink de Laravel `storage:link`) ya existía antes de la migración, apuntando correctamente a `storage/app/public`.

## Paso 0 — Verificación pre-migración (manifest de checksums)

Antes de esta sesión ya existía en el servidor (`~/pindoor/pindoor_manifest.sha256` y `pindoor_tracked_files.txt`) un manifest de una sesión anterior. Se corrió `sha256sum -c` contra él:

- **543 de 600 archivos** coincidían exacto.
- **36 "faltantes"**: en su mayoría archivos de repo sin impacto en runtime (`.gitignore`, `README.md`, docs internos, scripts de dev, fuentes JS/Vue que solo importan al compilar). La única real: una migración nunca subida (ver Paso 3).
- **21 "distintos"**: al diffear cada uno contra `origin/main` (después del `git fetch`, sin necesidad de tocar disco):
  - **9 eran falsos positivos** — el manifest estaba generado contra un commit de `main` anterior a los últimos 5 commits de "estadísticas del cliente"; producción ya tenía ese código al día (subido por FTP en su momento). Afectaba a `AdminController.php`, `ClienteController.php`, `EventoFichaController.php`, `PublicitaController.php`, `PuntoInteres.php`, `admin/clientes.blade.php`, `cliente/estadisticas.blade.php`, `cliente/partials/_sidebar.blade.php`, `routes/web.php`.
  - **12 eran diferencias reales, todas en el sentido "`main` tiene algo que producción no tenía todavía"** (nunca subido por FTP), sin ningún caso de edición hecha a mano en el servidor que se fuera a perder:
    - Feature de mapa con búsqueda de dirección + rotación (`package.json`/`lock`, `vite.config.js`, `leaflet.js`, `SelectorMapa.vue`, nuevos paquetes `leaflet-rotate` y `@geoman-io/leaflet-geoman-free`).
    - Fix de un bug real: `resources/js/app.js` en producción todavía arrancaba Alpine.js manualmente además de Livewire, generando dos instancias compitiendo (`wire:click`/`wire:model` dejaban de registrarse) — `main` ya lo tenía resuelto.
    - `public/.htaccess`: `main` agrega `php_value upload_max_filesize 60M` / `post_max_size 120M`, relacionado con el problema de subida de imágenes documentado en memoria.
    - `legal/privacidad.blade.php` actualizado para reflejar el nuevo uso de ubicación GPS del mapa.
    - `Admin/CategoriaController.php`: producción tenía un bloque de debug (`\Log::info`, marcado `TODO-DEBUG`) para un problema de guardado de categorías ya resuelto en `main` con validación limpia — confirmado con el usuario que el problema ya estaba resuelto antes de sobreescribir.
    - `publicita/index.blade.php`: cambio de estilo (`text-lg bg-blue-900`) confirmado como intencional.
    - `favicon.png` actualizado, y `vite.config.js` con dos entrypoints nuevos (`distrito-editor.js`, `admin-clientes-dashboard.js`).

## Paso 1 — Backup de seguridad

```bash
cd ~
tar -czf pindoor-backup-$(date +%Y%m%d).tar.gz \
  --exclude='pindoor/vendor' \
  --exclude='pindoor/node_modules' \
  --exclude='pindoor/storage/app/public' \
  --exclude='pindoor/storage/logs' \
  --exclude='pindoor/.git' \
  pindoor/
```

## Paso 2 — Deploy key de GitHub

No existía ninguna llave SSH en el servidor. Se generó una nueva (no reusar la del entorno de desarrollo):

```bash
ssh-keygen -t ed25519 -C "pindoor-prod" -N "" -f ~/.ssh/id_ed25519
cat ~/.ssh/id_ed25519.pub
```

Agregada en GitHub → `Cesar-eav/pindoor.cl` → Settings → Deploy keys → Add deploy key (read-only), vía `https://github.com/Cesar-eav/pindoor.cl/settings/keys`. Verificado con `ssh -T git@github.com` (responde identificándose como el repo, no como el usuario — comportamiento normal de una deploy key).

## Paso 3 — Inicializar git

```bash
cd ~/pindoor
git init
git remote add origin git@github.com:Cesar-eav/pindoor.cl.git
git fetch origin
```

`fetch` no toca archivos en disco — dejó disponibles los objetos de `origin/main` para diffear contra el manifest (Paso 0) antes de arriesgar nada.

## Paso 4 — Checkout forzado

Una vez revisadas y confirmadas las 12 diferencias reales del Paso 0:

```bash
git checkout -f -b main origin/main
git status
git log -1 --oneline   # 9a5315a
```

`git status` quedó limpio salvo archivos gitignorados esperados y una lista larga de **archivos huérfanos sin versionar** (controllers y vistas de features viejas: "noticias", "aportes", "operador", vistas `___labrujula`/`__labrujula` ya reemplazadas por las nuevas `_labrujula`, el `welcome.blade.php` default de Laravel, `composer.phar`, un puñado de archivos sueltos con nombres raros de algún copy-paste fallido, y una migración huérfana `add_vigente_hasta_to_recomendaciones_table` que ya había corrido y sido revertida por una migración posterior). Ninguno de estos bloquea nada — no están en git, `checkout -f` no los toca, quedan como candidatos a limpieza manual futura, no urgente.

## Paso 5 — Reconstruir lo que no viene en git

```bash
cd ~/pindoor
php composer.phar install --no-dev --optimize-autoloader
```

Symlink `public/storage` ya existía (`php artisan storage:link` devolvió "The [public/storage] link already exists" — sin acción necesaria).

**Migración pendiente:** `2026_08_18_130000_add_video_local_to_recomendaciones_table` figuraba como `Pending` en `migrate:status` (nunca se había subido el archivo). Se corrió:

```bash
php artisan migrate --force
```

Cache limpiada:

```bash
php artisan optimize:clear
```

**Compilación de assets:** `npm`/`node` no están en el PATH del jailshell ni siquiera invocándolos con ruta completa alcanza — `npm` internamente ejecuta `node` (sin ruta completa) para scripts de instalación (ej. el `postinstall` de `esbuild`) y para correr `vite`, así que hace falta agregar el bin de Node al PATH de la sesión:

```bash
export PATH="/opt/alt/alt-nodejs20/root/usr/bin:$PATH"
cd ~/pindoor
npm ci
npm run build
```

Generó correctamente `public/build/manifest.json` con los assets nuevos (incluye los dos entrypoints que antes faltaban: `distrito-editor` y `admin-clientes-dashboard`).

**Verificación final:** `curl` a `https://pindoor.cl/` devuelve `200` y el HTML ya referencia el hash de CSS recién generado por el build (`app-DCbxP3LR.css`), confirmando que Blade está leyendo el manifest de Vite correcto. El usuario probó en el navegador home, ficha de lugar, login de cliente y `/admin` — todo funcionando sin errores.

## De ahí en adelante — flujo normal de deploy

```bash
export PATH="/opt/alt/alt-nodejs20/root/usr/bin:$PATH"   # necesario para npm/node en esta sesión
cd ~/pindoor
git pull
php composer.phar install --no-dev --optimize-autoloader   # si cambió composer.json/lock
npm ci                                                       # si cambió package.json/lock
npm run build                                                # si cambió Tailwind/Vite/Blade con assets
php artisan migrate --force                                  # si hay migraciones nuevas
php artisan optimize:clear
```

No se identificó necesidad de limpiar OPcache por separado (no está cargado en PHP CLI; el hosting parece usar LiteSpeed — hay una carpeta `lscache` en el home — no se detectó ningún problema de caché durante esta migración, pero si en el futuro un cambio de código no parece aplicar tras un deploy, revisar ahí primero).
