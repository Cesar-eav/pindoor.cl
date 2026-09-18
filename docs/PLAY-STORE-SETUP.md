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

## Pendientes

- [x] Generar gráficos de la ficha (ícono, feature graphic, capturas de pantalla).
- [x] Corregir el texto de `/privacidad` sección 1 a "ubicación precisa" —
      hecho.
- [ ] Decidir y crear la cuenta de prueba (`cliente`) para el revisor de
      Google si se declaró acceso restringido — no se creó ninguna cuenta
      todavía, ni local ni en producción.
- [ ] Completar el cuestionario de **Clasificación de contenido** (IARC) —
      no se ha tocado en esta sesión.
- [ ] Configurar **Precios y distribución** (países disponibles, gratis/pago).
- [ ] Confirmar código de versión / versionCode en `NATIVEPHP_APP_VERSION` y
      `NATIVEPHP_APP_VERSION_CODE` antes de compilar.
- [ ] **Compilar como App Bundle (.aab), no .apk**, para subir a la pista de
      prueba cerrada — Play Console exige AAB para apps nuevas. El comando
      `clean assembleRelease` de `CLAUDE.md` genera el `.apk` (sirve para
      sideload/firma manual), pero para Play Console es:
      ```bash
      cd nativephp/android && JAVA_HOME=/usr/lib/jvm/java-17-openjdk-amd64 ./gradlew clean bundleRelease
      ```
      Queda en `app/build/outputs/bundle/release/app-release.aab`.
- [ ] Crear la pista de "Prueba cerrada" en Play Console, agregar la lista
      de testers (correos o Google Group) y subir el `.aab`.
- [ ] Enviar a revisión.
