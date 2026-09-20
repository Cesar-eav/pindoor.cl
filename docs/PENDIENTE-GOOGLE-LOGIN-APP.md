# Login con Google en la app Android

Estado (2026-09-20): **probado en teléfono real y funcionando.** Desplegado a producción por FTP. Código commiteado.

## Problema

La app NativePHP muestra `https://pindoor.cl` en un WebView (`routes/web.php` redirige el Laravel local `127.x` a producción). Al tocar "Continuar con Google", `SocialiteController::redirect` manda a `accounts.google.com`; `WebViewManager.shouldOverrideUrlLoading` abre todo host que no sea pindoor.cl en el navegador del sistema. La sesión termina en Chrome, no en la app. Además Google bloquea OAuth dentro de WebViews, así que no se puede "dejarlo dentro".

## Solución implementada

1. `WebViewManager` intercepta `https://pindoor.cl/auth/google` y abre un **Custom Tab** con `?app=1&challenge=<sha256(verifier)>` (PKCE; el verifier queda en `WebViewManager.googleVerifier`).
2. `SocialiteController::callback`, si la sesión trae `google_app_challenge`, no hace login: guarda en cache `google_app_login:{token}` (120 s, un solo uso) y muestra `auth/google-app-return.blade.php`, que abre `pindoor://auth/google/exchange?t={token}`.
3. `MainActivity.handleDeepLinkIntent` recibe el deeplink, agrega `&v={verifier}` y carga `http://127.0.0.1/auth/google/exchange?...` → redirige a producción.
4. `SocialiteController::exchange` valida token + `sha256(v) == challenge`, hace `Auth::login` en la sesión del WebView y redirige (`redirectAfterLogin`, mantiene el flujo de reclamo de perfil).

El login web normal no cambia (sin `app=1` nada se activa).

## Archivos tocados

Laravel:
- `app/Http/Controllers/Auth/SocialiteController.php` — `redirect`, `callback`, `exchange`, `redirectAfterLogin`
- `routes/auth.php` — `GET auth/google/exchange` (`throttle:10,1`)
- `resources/views/auth/google-app-return.blade.php` — página nueva

Android (`/nativephp` está en `.gitignore`: **estos cambios no están en git**, respaldarlos o reaplicar si se regenera el proyecto Android):
- `nativephp/android/app/src/main/java/com/nativephp/mobile/network/WebViewManager.kt`
  - imports `CustomTabsIntent`, `MessageDigest`, `SecureRandom`
  - `googleVerifier` en el `companion object` y función `startGoogleAuth(Uri)`
  - en `shouldOverrideUrlLoading`, justo después de `val urlHost`: si `urlHost == "pindoor.cl" && path == "/auth/google" && isForMainFrame` → `startGoogleAuth(request.url); return true`
- `nativephp/android/app/src/main/java/com/nativephp/mobile/ui/MainActivity.kt`
  - en `handleDeepLinkIntent`, antes de guardar `pendingDeepLink`: si `uri.scheme == "pindoor" && uri.host == "auth"` y hay verifier, `finalUrl = "$laravelUrl&v=$verifier"` (y se limpia el verifier); se usa `finalUrl` en vez de `laravelUrl`

## Verificado

- Prueba temporal (ya borrada) del `exchange`: verifier correcto → login; incorrecto → `/login`; token reutilizado → `/login`.
- `./gradlew :app:compileReleaseKotlin --offline` sin errores.
- **Probado en teléfono real (2026-09-20):** login funciona — Custom Tab se abre dentro de la app, retorno automático, sesión queda iniciada.

## Limitaciones conocidas

- El verifier vive en memoria: si Android mata la app mientras el usuario está en Chrome, el login falla y hay que reintentar.
- `MainActivity.initializeEnvironment()` llama `clearAllCookies()` al iniciar → la sesión se pierde en cada arranque en frío de la app (no relacionado con este cambio, pero afecta la experiencia de login).
