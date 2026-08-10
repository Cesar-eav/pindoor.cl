# WhatsApp Sharing en NativePHP Android

## Problema

El botón "Compartir por WhatsApp" en la página `/info` no funcionaba dentro de la app nativa compilada con NativePHP para Android. Se mostraba el error `ERR_UNKNOWN_URL_SCHEME` al intentar navegar a `whatsapp://`.

## Causa raíz

Dos bugs en `WebViewManager.kt`:

### Bug 1 — Esquemas no-HTTP no interceptados

El método `shouldOverrideUrlLoading` solo manejaba los esquemas `tel`, `mailto`, `sms` y `geo`. El esquema `whatsapp://` no estaba en esa lista, por lo que el WebView intentaba cargarlo como URL normal y fallaba.

```kotlin
// ANTES (solo 4 esquemas)
if (scheme in listOf("tel", "mailto", "sms", "geo")) { ... }

// DESPUÉS (todo lo que no sea http/https)
if (scheme != null && scheme !in listOf("http", "https")) {
    val intent = Intent(Intent.ACTION_VIEW, Uri.parse(url))
    intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK
    try {
        context.startActivity(intent)
    } catch (e: ActivityNotFoundException) {
        Toast.makeText(context, "No app can handle this link", Toast.LENGTH_SHORT).show()
    }
    return true
}
```

### Bug 2 — Check de `pindoor.cl` usando la URL completa

La condición para abrir URLs externas en Chrome usaba `!url.contains("pindoor.cl")` sobre la URL completa. Los links de `wa.me` con texto URL-encodeado que contenía la palabra "pindoor.cl" en el parámetro `text=` eran bloqueados incorrectamente.

```kotlin
// ANTES — comparaba el string completo de la URL
!url.contains("pindoor.cl")

// DESPUÉS — compara solo el host
val urlHost = request.url.host ?: ""
!urlHost.contains("pindoor.cl")
```

**Archivo:** `nativephp/android/app/src/main/java/com/nativephp/mobile/network/WebViewManager.kt`

## Solución JS (`info.blade.php`)

`navigator.share` **no está disponible** en el WebView de NativePHP (devuelve `false`/`undefined`). Se usa `whatsapp://send?text=` directamente en lugar de `https://wa.me/?text=`:

```js
function compartirInfo(e, el) {
    e.preventDefault();
    const text = el.dataset.text;
    if (navigator.share) {
        navigator.share({ text }).catch(() => {
            window.location.href = 'whatsapp://send?text=' + encodeURIComponent(text);
        });
    } else {
        window.location.href = 'whatsapp://send?text=' + encodeURIComponent(text);
    }
    return false;
}
```

La URL `whatsapp://` es interceptada por el WebViewManager fijo (Fix 1) y abierta con `Intent.ACTION_VIEW`, lo que lanza WhatsApp directamente.

## Flujo en la app nativa

```
Usuario toca "Compartir"
  → compartirInfo() ejecuta window.location.href = 'whatsapp://send?text=...'
  → WebView intenta navegar a whatsapp://
  → shouldOverrideUrlLoading: scheme = "whatsapp" → no es http/https
  → startActivity(Intent(ACTION_VIEW, "whatsapp://send?text=..."))
  → Android abre WhatsApp con el texto prellenado
```

## Contexto de la app NativePHP

La app NativePHP levanta un servidor PHP local en `127.x.x.x`. Al recibir cualquier request, `routes/web.php` redirige al sitio de producción `https://pindoor.cl` vía JavaScript:

```php
if (str_starts_with(request()->getHost(), '127.') && !in_array(request()->getPort(), $devPorts)) {
    Route::get('/{any?}', function () {
        $url = 'https://pindoor.cl' . request()->getRequestUri();
        return response("<script>window.location.replace(" . json_encode($url) . ");</script>");
    })->where('any', '.*');
}
```

Esto significa que **el WebView carga `pindoor.cl` en producción**, no el servidor local. Los cambios en archivos PHP/Blade requieren subida por FTP al servidor, no solo recompilar la app.

## Checklist para compilar nueva versión

1. Subir archivos Blade modificados por FTP a `pindoor.cl`
2. Actualizar `versionCode` en **ambos** lugares:
   - `.env` → `NATIVEPHP_APP_VERSION_CODE=X`
   - `nativephp/android/app/build.gradle.kts` → `versionCode = X`
3. Compilar AAB (requerido por Google Play):
   ```bash
   npm run build
   php artisan native:package android
   ```
4. Para sideload/debug, compilar APK:
   ```bash
   cd nativephp/android && JAVA_HOME=/usr/lib/jvm/java-17-openjdk-amd64 ./gradlew clean assembleRelease
   ```
