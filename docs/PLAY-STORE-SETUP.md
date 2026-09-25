# Publicación en Google Play — Prueba cerrada

## Objetivo

Preparar la ficha de Play Store y completar el formulario "Acceso a la app" /
"Seguridad de los datos" en Play Console para publicar Pindoor primero en
prueba cerrada y luego en producción.

## Datos clave del proyecto (para los formularios)

- App ID: `cl.pindoor.app`
- Sitio web: `https://pindoor.cl`
- Correo de contacto: `soporte@pindoor.cl`
- Política de privacidad: `https://pindoor.cl/privacidad`
- Categoría: Viajes y guías locales
- 124 lugares activos en 22 categorías

## Ficha de Play Store — completa y lista para enviar a revisión

- Nombre de la app: `Pindoor` (se dejó simple, sin el subtítulo sugerido).
- Descripción breve y completa: cargadas.
- Ícono: el favicon real de producción (`public/favicon.png`), reescalado a
  512×512 sin distorsión → `icon-512-favicon.png`.
- Gráfico de funciones: diseño propio 1024×500 (`feature-graphic-1024x500.png`)
  — **declarado como creado con IA** en la Declaración de recursos de IA.
- 5 capturas de teléfono reales tomadas del sitio en producción
  (home, ficha de lugar, La Brújula, info práctica, explorar), 1080×1919,
  sin declarar como IA (son capturas reales, no generadas).
- Todos los assets están en
  `/home/cesar/Descargas/pindoor-playstore-assets/` en este equipo.
- Tablet / Chromebook / Android XR / vídeo: dejados vacíos (opcionales).

## App access / App content — respuestas decididas

| Pregunta | Respuesta |
|---|---|
| ¿Alguna parte de la app está restringida? | Sí (el panel `/cliente` requiere login) |
| Comparte ubicación precisa con otros usuarios | No |
| Permite comprar contenido digital | No (compras son servicios reales vía Flow) |
| Recompensas en metálico / NFT / jugar-para-ganar | No |
| Es navegador o buscador | No |
| Es informativa o educativa | Sí |
| Grupos de edad objetivo | Solo "A partir de 18 años" |
| Restringir a menores detectados por Google | Sí, activada |
| Recoge o comparte datos de usuario | Sí |
| Datos cifrados en tránsito | Sí (`.htaccess` fuerza HTTPS) |
| Métodos de creación de cuenta | Nombre de usuario y contraseña + OAuth (Google) |
| Link de borrado de cuenta | `https://pindoor.cl/profile` |
| Borrado parcial de datos sin borrar la cuenta | No |
| Tipo de ubicación recogida | **Ubicación precisa** (usa `navigator.geolocation` con GPS, no solo IP) |
| Información personal recogida | Nombre, correo, IDs de usuario, teléfono |

## Bug encontrado y corregido en esta sesión

**Borrado de cuenta fallaba para usuarios de Google OAuth.**

`ProfileController::destroy` exigía `current_password`, pero los usuarios
creados solo por Google (`SocialiteController.php`) se guardan con
`password = null` — nunca pueden pasar esa validación y no podían borrar su
cuenta desde `/profile`. Esto rompía el link de borrado que se declaró en
Play Console para el método de cuenta "OAuth".

**Fix aplicado:**
- `app/Http/Controllers/ProfileController.php` — solo exige `current_password`
  si el usuario tiene contraseña definida.
- `resources/views/profile/partials/delete-user-form.blade.php` — oculta el
  campo de contraseña para usuarios sin contraseña (solo Google).

Sin commitear todavía (el usuario commitea manualmente).

## Seguridad de los datos — completado

Formulario "Seguridad de los datos" terminado: tipos de datos (Información
personal, Mensajes, Fotos, Archivos y documentos, Actividad en la app,
Ubicación), uso y gestión de cada uno (recogido/compartido, obligatorio u
opcional, finalidad), eliminación de datos (link `/profile`) y prácticas de
seguridad (cifrado en tránsito). Único dato compartido con terceros:
"Interacciones con la aplicación" → Microsoft Clarity / Google Tag Manager,
finalidad Análisis.

## Otras declaraciones de política completadas

| Declaración | Respuesta |
|---|---|
| Funciones financieras | Ninguna (los pagos de entradas/tours vía Flow son checkout de e-commerce, no un servicio financiero en sí) |
| ID de publicidad | No se usa (sin SDKs de ads ni permiso `AD_ID` en el manifiesto Android) |
| Aplicaciones de salud | No aplica |
| Aplicaciones gubernamentales | No aplica |

## minSdk bajado de 33 a 26 (sesión 2026-09-20)

El primer `.aab` subido quedó con `minSdk = 33` (Android 13+) porque
`config/nativephp.php` no tenía la clave `min_sdk` — el paquete `nativephp/mobile`
usaba su default (33). Eso dejaba fuera todos los dispositivos con Android 12
o anteriores sin ninguna razón técnica (no hay plugins nativos que lo exijan;
el piso que exige el propio paquete es 26).

**Fix aplicado:**
- `config/nativephp.php` — se agregó `'min_sdk' => env('NATIVEPHP_ANDROID_MIN_SDK', 26)`
  dentro del array `'android'`.
- `.env` — se agregó `NATIVEPHP_ANDROID_MIN_SDK=26`.
- Recompilado con `npm run build && php artisan native:package android` (el paso
  de Gradle interno falla por falta de TTY en sesiones no interactivas; en ese
  caso el bundle de Laravel ya quedó regenerado y basta correr
  `cd nativephp/android && JAVA_HOME=... ./gradlew clean bundleRelease` a mano).
- Confirmado en `nativephp/android/app/build.gradle.kts` → `minSdk = 26`.

## Conflicto de versionCode al subir el primer .aab

Al subir el `.aab` nuevo, Play Console mostró error: *"Ningún usuario podrá
ver este APK porque uno o varios APK con códigos de versión superiores lo han
sustituido"*, referido al `versionCode 27` que había quedado en la versión
desde una subida anterior. Causa: `NATIVEPHP_APP_VERSION_CODE` se autoincrementa
en cada `native:package`, así que el `.aab` en disco no siempre coincide con
el valor actual del `.env` (el `.env` va un paso adelante, guardando el
próximo código a usar).

**Fix:** en la pantalla de la versión en Play Console, quitar el artefacto
con `versionCode 27` de la lista de App Bundles/APKs de la versión, dejando
solo el más nuevo (28). Antes de cada subida, verificar el `versionCode` real
grabado en `nativephp/android/app/build.gradle.kts` (no el de `.env`).

## Estado en Play Console (sesión 2026-09-20)

Pista **Prueba cerrada – Alpha**, versión publicada actualmente `27 (1.0.1)`,
con cambios pendientes de enviar a revisión:

- Países/territorios: Chile agregado.
- Testers: gestionados por lista de correo electrónico ("Tester").
- Canal de sugerencias: `cesarandrade@pindoor.cl`.
- Ficha de Play Store: español (es-ES) completa y predeterminada.
- Clasificación de contenido (IARC): cuestionario enviado.
- Contenido y audiencia objetivo: actualizado, edad objetivo 18+.
- Política de privacidad: URL `https://pindoor.cl/privacidad` definida.
- Declaración de anuncios: actualizada (sin anuncios).
- Seguridad de los datos: cuestionario completo.
- Aplicaciones de salud: declaración completa (no aplica).
- Categoría de la app: Viajes y guías locales.
- Declaraciones adicionales registradas (no publicadas, se consideran en la
  revisión): instrucciones de datos de inicio de sesión (acceso restringido),
  ID de publicidad, aplicaciones gubernamentales, funciones financieras.

Pendiente para poder enviar a revisión: subir el `.aab` con `versionCode 28`
(minSdk 26) reemplazando el `27` en la lista de artefactos de la versión.
— **hecho**: el detalle de la versión confirma "App bundles nuevos → 28
(1.0.1), 26 y posterior"; el "27" que quedaba visible en el resumen era solo
el nombre/etiqueta de la versión (cosmético, no afecta el bundle publicado).

Notas de la versión (es-ES) cargadas:

> Primera versión de prueba de Pindoor, tu guía turística de Valparaíso.
>
> Explora miradores, museos, cafeterías, picadas y más de 120 lugares de la
> ciudad, con mapa, horarios y promociones actualizadas. Descubre eventos
> culturales en "La Brújula" y sigue a artistas locales.
>
> Esta es una versión de prueba cerrada: agradecemos que reportes cualquier
> error o comentario que tengas.

## Qué pasa después de enviar a revisión

- Con **"Publicación gestionada" desactivada**, en cuanto Google aprueba la
  versión se publica sola en la pista de prueba cerrada — no hay un segundo
  paso manual de "publicar".
- Revisión de primera versión de una app nueva: Google indica hasta 7 días,
  pero puede tardar más. Mientras tanto la versión queda en estado
  "En revisión" dentro de Play Console.
- Los testers de la lista de correo deben **aceptar la invitación** (link de
  "opt-in" de la pista, en la pestaña Testers) e instalar desde ese enlace o
  buscando la app en Play Store una vez inscritos — no reciben la app
  automáticamente solo por estar en la lista.
- **Requisito para llegar a producción** (cuentas de desarrollador nuevas/
  personales, política vigente desde 2023): Google exige un mínimo de
  **20 testers que acepten la invitación** y mantener la prueba cerrada
  activa **al menos 14 días continuos** antes de habilitar el botón para
  promover la app a producción. Conviene reclutar testers desde ya para no
  perder tiempo en esa ventana.

## Pendientes

- [x] Generar gráficos de la ficha (ícono, feature graphic, capturas de pantalla).
- [x] Corregir el texto de `/privacidad` sección 1 a "ubicación precisa".
- [x] Cuenta de prueba (`cliente`) para el revisor — resuelto vía instrucciones
      de acceso restringido declaradas en Play Console (ver arriba).
- [x] Completar el cuestionario de **Clasificación de contenido** (IARC).
- [x] Configurar **Precios y distribución** (país: Chile, gratis).
- [x] Confirmar código de versión — ver sección "Conflicto de versionCode" arriba.
- [x] **Compilar como App Bundle (.aab)** con `minSdk` corregido a 26.
- [x] Crear la pista de "Prueba cerrada" en Play Console y configurar testers
      (lista de correo).
- [x] Quitar el artefacto `versionCode 27` de la versión y subir el `.aab`
      con `versionCode 28`.
- [x] Redactar y cargar las notas de la versión (release notes) en la ficha
      de la versión.
- [ ] Enviar a revisión (confirmar los "14 cambios" en Resumen de publicación).
- [ ] Reclutar al menos 20 testers que acepten la invitación (opt-in) y
      mantener la pista activa 14 días — requisito de Google antes de poder
      promover a producción.
